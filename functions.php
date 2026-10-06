<?php
/**
 * Theme setup.
 */

define( 'SLN_VERSION', '1.0.0-' . filemtime( get_template_directory() . '/style.css' ) );

require_once get_template_directory() . '/inc/section-defs.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/woocommerce.php';
require_once get_template_directory() . '/inc/seo.php';

function sln_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'woocommerce' );

	register_nav_menus(
		array(
			'primary'        => 'Primary Navigation',
			'footer_shop'    => 'Footer — Shop Column',
			'footer_company' => 'Footer — Company Column',
		)
	);
}
add_action( 'after_setup_theme', 'sln_setup' );

function sln_flush_permalinks_on_activation() {
	sln_ensure_pages();
	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'sln_flush_permalinks_on_activation' );

function sln_ensure_pages() {
	$pages = array(
		'service' => 'Service',
		'about'   => 'About',
		'contact' => 'Contact',
		'brands'  => 'Brands',
	);
	foreach ( $pages as $slug => $title ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			continue;
		}
		wp_insert_post(
			array(
				'post_title'  => $title,
				'post_name'   => $slug,
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);
	}
}

function sln_enqueue_assets() {
	$font = sln_mod( 'sln_font_family', 'Inter' );
	wp_enqueue_style( 'sln-fonts', sln_google_fonts_url( $font ), array(), null );
	wp_enqueue_style( 'sln-style', get_stylesheet_uri(), array( 'sln-fonts' ), SLN_VERSION );
	$deps = ( function_exists( 'sln_ecommerce_enabled' ) && sln_ecommerce_enabled() ) ? array( 'jquery' ) : array();
	wp_enqueue_script( 'sln-frontend', get_template_directory_uri() . '/assets/js/frontend.js', $deps, SLN_VERSION, true );
	wp_localize_script(
		'sln-frontend',
		'slnSearch',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'sln_search' ),
			'suggestions' => (bool) sln_mod( 'sln_header_search_suggestions', true ),
			'empty'       => 'No products match',
			'viewAll'     => 'View all results',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'sln_enqueue_assets' );

function sln_output_dynamic_styles() {
	$font  = sln_sanitize_font_family( sln_mod( 'sln_font_family', 'Inter' ) );
	$scale = sln_sanitize_font_scale( sln_mod( 'sln_font_scale', '100' ) );
	$vars  = array( '--sl-font' => "'" . str_replace( "'", '', $font ) . "', sans-serif" );
	foreach ( sln_color_vars() as $name => $value ) {
		if ( $value ) {
			$vars[ $name ] = $value;
		}
	}
	$top_bg   = sanitize_hex_color( sln_mod( 'sln_header_topbar_bg', '#080B12' ) );
	$top_text = sanitize_hex_color( sln_mod( 'sln_header_topbar_text', '#98A4B8' ) );
	if ( $top_bg ) {
		$vars['--sl-bar-bg'] = $top_bg;
	}
	if ( $top_text ) {
		$vars['--sl-bar-text'] = $top_text;
	}

	echo '<style id="sln-dynamic-styles">:root{';
	foreach ( $vars as $name => $value ) {
		echo $name . ':' . $value . ';';
	}
	echo '}html{font-size:calc(15px * ' . $scale . ' / 100);}</style>' . "\n";
}
add_action( 'wp_head', 'sln_output_dynamic_styles', 20 );

function sln_customize_preview_assets() {
	wp_enqueue_script(
		'sln-customizer-preview',
		get_template_directory_uri() . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		SLN_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'sln_customize_preview_assets' );

function sln_body_classes( $classes ) {
	$classes[] = 'sl-nav-' . sln_sanitize_nav_style( sln_mod( 'sln_nav_text_style', 'none' ) );
	if ( function_exists( 'sln_ecommerce_enabled' ) && sln_ecommerce_enabled() ) {
		$classes[] = 'sl-selling';
	}
	return $classes;
}
add_filter( 'body_class', 'sln_body_classes' );

function sln_woocommerce_missing_notice() {
	if ( class_exists( 'WooCommerce' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>' . esc_html( wp_get_theme()->get( 'Name' ) ) . '</strong> needs WooCommerce for the catalog. Product sections stay hidden until it is active. <a href="' . esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ) . '">Install WooCommerce</a>.</p></div>';
}
add_action( 'admin_notices', 'sln_woocommerce_missing_notice' );

function sln_output_gtm_head() {
	$gtm_id = sln_mod( 'sln_gtm_id', '' );
	if ( ! $gtm_id ) {
		return;
	}
	echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $gtm_id ) . "');</script>\n";
}
add_action( 'wp_head', 'sln_output_gtm_head', 1 );

function sln_output_gtm_body() {
	$gtm_id = sln_mod( 'sln_gtm_id', '' );
	if ( ! $gtm_id ) {
		return;
	}
	echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
}
add_action( 'wp_body_open', 'sln_output_gtm_body' );

function sln_output_ga4() {
	$ga4_id = sln_mod( 'sln_ga4_id', '' );
	if ( ! $ga4_id ) {
		return;
	}
	echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga4_id ) . '"></script>';
	echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js( $ga4_id ) . "');</script>\n";
}
add_action( 'wp_head', 'sln_output_ga4' );

function sln_output_targetcrm_widget() {
	$client_id = sln_mod( 'sln_targetcrm_client_id', '' );
	if ( ! $client_id ) {
		return;
	}
	echo '<script src="https://app.constellationdealer.com/widget.js" id="target-crm-widget" data-client-id="' . esc_attr( $client_id ) . '" defer></script>';
}
add_action( 'wp_footer', 'sln_output_targetcrm_widget' );
