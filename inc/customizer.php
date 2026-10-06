<?php
/**
 * Customizer: one Site-Wide Settings panel, one panel per page,
 * section order, and repeaters. Driven by sln_section_defs().
 */

function sln_sanitize_section_order( $value ) {
	$decoded = json_decode( wp_unslash( $value ), true );
	if ( ! is_array( $decoded ) ) {
		return '';
	}
	$clean = array();
	foreach ( $decoded as $row ) {
		if ( ! isset( $row['id'] ) || ! preg_match( '/^[a-z0-9_]+$/', $row['id'] ) ) {
			continue;
		}
		$clean[] = array(
			'id'      => sanitize_key( $row['id'] ),
			'enabled' => ! empty( $row['enabled'] ),
		);
	}
	return wp_json_encode( $clean );
}

function sln_sanitize_checkbox( $value ) {
	return (bool) $value;
}

function sln_sanitize_nav_menu_choice( $value ) {
	if ( '' === (string) $value || '0' === (string) $value ) {
		return '';
	}
	return wp_get_nav_menu_object( (int) $value ) ? (string) (int) $value : '';
}

function sln_nav_menu_choices() {
	$choices = array( '' => 'Automatic' );
	$menus   = wp_get_nav_menus();
	if ( $menus ) {
		foreach ( $menus as $menu ) {
			$choices[ (string) $menu->term_id ] = $menu->name;
		}
	}
	return $choices;
}

function sln_sanitize_font_family( $value ) {
	$choices = array_keys( sln_font_choices() );
	return in_array( $value, $choices, true ) ? $value : 'Inter';
}

function sln_sanitize_font_scale( $value ) {
	$choices = array_keys( sln_font_scale_choices() );
	return in_array( (string) $value, $choices, true ) ? (string) $value : '100';
}

function sln_sanitize_nav_style( $value ) {
	$allowed = array( 'none', 'uppercase', 'lowercase', 'capitalize' );
	return in_array( $value, $allowed, true ) ? $value : 'none';
}

function sln_sanitize_ga4_id( $value ) {
	$value = strtoupper( trim( (string) $value ) );
	return preg_match( '/^G-[A-Z0-9]+$/', $value ) ? $value : '';
}

function sln_sanitize_gtm_id( $value ) {
	$value = strtoupper( trim( (string) $value ) );
	return preg_match( '/^GTM-[A-Z0-9]+$/', $value ) ? $value : '';
}

function sln_sanitize_business_type( $value ) {
	$value = preg_replace( '/[^A-Za-z]/', '', (string) $value );
	return $value ? $value : 'Store';
}

function sln_field_sanitize_callback( $type ) {
	switch ( $type ) {
		case 'textarea':
			return 'sanitize_textarea_field';
		case 'checkbox':
			return 'sln_sanitize_checkbox';
		case 'number':
		case 'range':
			return 'absint';
		case 'color':
			return 'sanitize_hex_color';
		case 'image':
		case 'url':
			return 'esc_url_raw';
		default:
			return 'sanitize_text_field';
	}
}

function sln_make_repeater_sanitizer( $block_fields ) {
	return function ( $value ) use ( $block_fields ) {
		$decoded = json_decode( wp_unslash( $value ), true );
		if ( ! is_array( $decoded ) ) {
			return '[]';
		}
		$clean = array();
		foreach ( $decoded as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}
			$clean_block = array();
			foreach ( $block_fields as $key => $field ) {
				$raw = array_key_exists( $key, $block ) ? $block[ $key ] : ( isset( $field['default'] ) ? $field['default'] : '' );
				$clean_block[ $key ] = call_user_func( sln_field_sanitize_callback( $field['type'] ), $raw );
			}
			$clean[] = $clean_block;
		}
		return wp_json_encode( $clean );
	};
}

function sln_add_field_control( $wp_customize, $setting_id, $customizer_section, $field ) {
	if ( 'repeater' === $field['type'] ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => wp_json_encode( isset( $field['default_blocks'] ) ? $field['default_blocks'] : array() ),
				'sanitize_callback' => sln_make_repeater_sanitizer( $field['block_fields'] ),
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new SLN_Repeater_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'           => $field['label'],
					'section'         => $customizer_section,
					'block_fields'    => $field['block_fields'],
					'title_field'     => isset( $field['title_field'] ) ? $field['title_field'] : '',
					'add_button_text' => isset( $field['add_button_text'] ) ? $field['add_button_text'] : 'Add block',
					'min_blocks'      => isset( $field['min'] ) ? (int) $field['min'] : 0,
				)
			)
		);
		return;
	}

	$transport = ( 'color' === $field['type'] ) ? 'postMessage' : 'refresh';
	$wp_customize->add_setting(
		$setting_id,
		array(
			'default'           => $field['default'],
			'sanitize_callback' => sln_field_sanitize_callback( $field['type'] ),
			'transport'         => $transport,
		)
	);

	if ( 'color' === $field['type'] ) {
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array( 'label' => $field['label'], 'section' => $customizer_section )
			)
		);
		return;
	}

	if ( 'image' === $field['type'] ) {
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$setting_id,
				array( 'label' => $field['label'], 'section' => $customizer_section )
			)
		);
		return;
	}

	$control_type = in_array( $field['type'], array( 'text', 'textarea', 'checkbox', 'number', 'url', 'range' ), true ) ? $field['type'] : 'text';
	$control_args = array(
		'label'   => $field['label'],
		'section' => $customizer_section,
		'type'    => $control_type,
	);
	if ( in_array( $control_type, array( 'range', 'number' ), true ) && ! empty( $field['input_attrs'] ) ) {
		$control_args['input_attrs'] = $field['input_attrs'];
	}
	if ( ! empty( $field['description'] ) ) {
		$control_args['description'] = $field['description'];
	}
	$wp_customize->add_control( $setting_id, $control_args );
}

function sln_panel_active_callback( $page ) {
	switch ( $page ) {
		case 'home':
			return 'is_front_page';
		case 'shop':
			return function () {
				return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_search() );
			};
		case 'product':
			return function () {
				return is_singular( 'product' );
			};
		default:
			return function () use ( $page ) {
				return is_page( $page );
			};
	}
}

function sln_customize_register( $wp_customize ) {
	require_once get_template_directory() . '/inc/class-section-order-control.php';
	require_once get_template_directory() . '/inc/class-repeater-control.php';

	$wp_customize->add_panel( 'sln_global', array( 'title' => 'Site-Wide Settings', 'priority' => 5 ) );

	$wp_customize->add_section( 'sln_colors', array( 'title' => 'Colors', 'panel' => 'sln_global', 'priority' => 10 ) );
	$color_fields = array(
		'accent' => array( 'label' => 'Buttons and accent', 'default' => '#4F8CC9' ),
		'link'   => array( 'label' => 'Links and eyebrow text', 'default' => '#4F8CC9' ),
		'text'   => array( 'label' => 'Headings and body text', 'default' => '#E8EDF5' ),
		'muted'  => array( 'label' => 'Muted / secondary text', 'default' => '#98A4B8' ),
		'bg'     => array( 'label' => 'Page background', 'default' => '#0B0F17' ),
		'soft'   => array( 'label' => 'Soft section background', 'default' => '#121926' ),
		'border' => array( 'label' => 'Card and input borders', 'default' => '#243049' ),
	);
	foreach ( $color_fields as $key => $field ) {
		$setting_id = 'sln_color_' . $key;
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $field['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array( 'label' => $field['label'], 'section' => 'sln_colors' ) ) );
	}

	$wp_customize->add_section( 'sln_typography', array( 'title' => 'Typography', 'panel' => 'sln_global', 'priority' => 11 ) );
	$wp_customize->add_setting( 'sln_font_family', array( 'default' => 'Inter', 'sanitize_callback' => 'sln_sanitize_font_family', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'sln_font_family', array( 'label' => 'Site font', 'section' => 'sln_typography', 'type' => 'select', 'choices' => sln_font_choices() ) );
	$wp_customize->add_setting( 'sln_font_scale', array( 'default' => '100', 'sanitize_callback' => 'sln_sanitize_font_scale', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		'sln_font_scale',
		array(
			'label'       => 'Text size',
			'description' => 'Scales the whole site together.',
			'section'     => 'sln_typography',
			'type'        => 'select',
			'choices'     => sln_font_scale_choices(),
		)
	);
	$wp_customize->add_setting( 'sln_nav_text_style', array( 'default' => 'none', 'sanitize_callback' => 'sln_sanitize_nav_style', 'transport' => 'postMessage' ) );
	$wp_customize->add_control(
		'sln_nav_text_style',
		array(
			'label'   => 'Navigation text style',
			'section' => 'sln_typography',
			'type'    => 'select',
			'choices' => array(
				'none'       => 'As typed',
				'uppercase'  => 'UPPERCASE',
				'lowercase'  => 'lowercase',
				'capitalize' => 'Capitalize Each Word',
			),
		)
	);

	$wp_customize->add_section(
		'sln_commerce',
		array(
			'title'       => 'Commerce',
			'description' => 'Off by default. The site stays a showroom until this is turned on.',
			'panel'       => 'sln_global',
			'priority'    => 12,
		)
	);
	sln_add_field_control(
		$wp_customize,
		'sln_enable_ecommerce',
		'sln_commerce',
		array(
			'label'       => 'Make the site ecommerce.',
			'description' => 'Shows the header cart, add to cart on cards and the product page, quantity, and styled cart and checkout. The reserve form moves behind a Request information button.',
			'default'     => false,
			'type'        => 'checkbox',
		)
	);
	foreach (
		array(
			'cart_label'      => array( 'label' => 'Cart breadcrumb', 'default' => 'Cart' ),
			'checkout_label'  => array( 'label' => 'Checkout breadcrumb', 'default' => 'Checkout' ),
			'received_label'  => array( 'label' => 'Order received breadcrumb', 'default' => 'Order received' ),
			'empty_label'     => array( 'label' => 'Empty page text', 'default' => 'Nothing found.' ),
		) as $key => $field
	) {
		sln_add_field_control(
			$wp_customize,
			'sln_' . $key,
			'sln_commerce',
			array(
				'label'   => $field['label'],
				'default' => $field['default'],
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_section(
		'sln_seo',
		array(
			'title'       => 'SEO',
			'description' => 'Used only when Yoast, Rank Math, All in One SEO, SEOPress, Slim SEO, or The SEO Framework is not active.',
			'panel'       => 'sln_global',
			'priority'    => 13,
		)
	);
	foreach (
		array(
			'home_description' => array( 'label' => 'Homepage meta description', 'description' => 'Leave empty to use the first hero text.', 'default' => '', 'type' => 'textarea' ),
			'shop_description' => array( 'label' => 'Shop meta description', 'description' => 'Leave empty for an automatic description.', 'default' => '', 'type' => 'textarea' ),
			'share_image'      => array( 'label' => 'Default share image (1200×630)', 'default' => '', 'type' => 'image' ),
			'business_type'    => array( 'label' => 'Business type', 'description' => 'Default Store. Name, phone, email, and address come from Footer settings.', 'default' => 'Store', 'type' => 'text' ),
			'social_profiles'  => array( 'label' => 'Social profile URLs', 'description' => 'One per line.', 'default' => '', 'type' => 'textarea' ),
		) as $key => $field
	) {
		if ( 'business_type' === $key ) {
			$wp_customize->add_setting( 'sln_seo_business_type', array( 'default' => 'Store', 'sanitize_callback' => 'sln_sanitize_business_type', 'transport' => 'refresh' ) );
			$wp_customize->add_control( 'sln_seo_business_type', array( 'label' => $field['label'], 'description' => $field['description'], 'section' => 'sln_seo', 'type' => 'text' ) );
			continue;
		}
		sln_add_field_control( $wp_customize, 'sln_seo_' . $key, 'sln_seo', $field );
	}

	$wp_customize->add_section( 'sln_announcement', array( 'title' => 'Announcement Bar', 'panel' => 'sln_global', 'priority' => 29 ) );
	foreach (
		array(
			'top_bar_left'  => array( 'label' => 'Left text', 'default' => 'Mon–Fri 8–6 · Sat 8–4 · Sun closed', 'type' => 'text' ),
			'top_bar_right' => array( 'label' => 'Right text', 'default' => '(123) 456-7890 · hello@yourstore.com', 'type' => 'text' ),
			'topbar_bg'     => array( 'label' => 'Background color', 'default' => '#080B12', 'type' => 'color' ),
			'topbar_text'   => array( 'label' => 'Text color', 'default' => '#98A4B8', 'type' => 'color' ),
		) as $key => $field
	) {
		sln_add_field_control( $wp_customize, 'sln_header_' . $key, 'sln_announcement', $field );
	}

	$wp_customize->add_section( 'sln_header', array( 'title' => 'Header', 'panel' => 'sln_global', 'priority' => 30 ) );
	foreach (
		array(
			'logo_image'          => array( 'label' => 'Logo image (empty keeps the text logo)', 'default' => '', 'type' => 'image' ),
			'logo_max_width'      => array( 'label' => 'Logo max width (px)', 'default' => 180, 'type' => 'range', 'input_attrs' => array( 'min' => 40, 'max' => 400, 'step' => 5 ) ),
			'logo_max_height'     => array( 'label' => 'Logo max height (px)', 'default' => 36, 'type' => 'range', 'input_attrs' => array( 'min' => 20, 'max' => 150, 'step' => 2 ) ),
			'logo_text'           => array( 'label' => 'Logo text', 'default' => get_bloginfo( 'name' ), 'type' => 'text' ),
			'logo_tagline'        => array( 'label' => 'Tagline (empty hides it)', 'default' => '', 'type' => 'text' ),
			'search_placeholder'  => array( 'label' => 'Search placeholder', 'default' => 'Search model, brand or category', 'type' => 'text' ),
			'search_button_text'  => array( 'label' => 'Search button text', 'default' => 'Search', 'type' => 'text' ),
			'search_suggestions'  => array( 'label' => 'Live suggestions', 'default' => true, 'type' => 'checkbox' ),
			'reserve_button_text' => array( 'label' => 'Reserve button text (empty hides it)', 'default' => 'Contact Store', 'type' => 'text' ),
			'reserve_button_link' => array( 'label' => 'Reserve button link', 'default' => '/contact/', 'type' => 'url' ),
			'menu_label'          => array( 'label' => 'Mobile menu button', 'default' => 'Menu', 'type' => 'text' ),
			'call_label'          => array( 'label' => 'Mobile call button', 'default' => 'Call the Store', 'type' => 'text' ),
			'email_label'         => array( 'label' => 'Mobile email button', 'default' => 'Email', 'type' => 'text' ),
		) as $key => $field
	) {
		sln_add_field_control( $wp_customize, 'sln_header_' . $key, 'sln_header', $field );
	}

	$wp_customize->add_section( 'sln_footer', array( 'title' => 'Footer', 'panel' => 'sln_global', 'priority' => 31 ) );
	$menu_choices = sln_nav_menu_choices();
	foreach ( array( 'footer_shop_menu' => 'Shop column menu', 'footer_company_menu' => 'Company column menu' ) as $key => $label ) {
		$wp_customize->add_setting( 'sln_' . $key, array( 'default' => '', 'sanitize_callback' => 'sln_sanitize_nav_menu_choice', 'transport' => 'refresh' ) );
		$wp_customize->add_control( 'sln_' . $key, array( 'label' => $label, 'section' => 'sln_footer', 'type' => 'select', 'choices' => $menu_choices ) );
	}
	foreach (
		array(
			'company_name'    => array( 'label' => 'Company name', 'default' => get_bloginfo( 'name' ), 'type' => 'text' ),
			'address'         => array( 'label' => 'Address', 'default' => "123 Main Street\nYour City, ST 00000", 'type' => 'textarea' ),
			'phone_email'     => array( 'label' => 'Phone and email', 'default' => "(123) 456-7890\nhello@yourstore.com", 'type' => 'textarea' ),
			'hours_heading'   => array( 'label' => 'Hours heading', 'default' => 'Hours', 'type' => 'text' ),
			'hours'           => array( 'label' => 'Hours', 'default' => "Mon–Fri 8–6\nSat 8–4\nSun closed", 'type' => 'textarea' ),
			'shop_heading'    => array( 'label' => 'Shop column heading', 'default' => 'Categories', 'type' => 'text' ),
			'company_heading' => array( 'label' => 'Company column heading', 'default' => 'Brands', 'type' => 'text' ),
			'note'            => array( 'label' => 'Footer note', 'default' => 'Catalog only — no online sales.', 'type' => 'textarea' ),
			'legal_right'     => array( 'label' => 'Legal line', 'default' => 'Specs and stock subject to change. Pricing on request.', 'type' => 'text' ),
		) as $key => $field
	) {
		sln_add_field_control( $wp_customize, 'sln_footer_' . $key, 'sln_footer', $field );
	}

	$wp_customize->add_section( 'sln_newsletter', array( 'title' => 'Footer Newsletter', 'panel' => 'sln_global', 'priority' => 32 ) );
	foreach (
		array(
			'enabled'     => array( 'label' => 'Show newsletter signup', 'default' => true, 'type' => 'checkbox' ),
			'heading'     => array( 'label' => 'Heading', 'default' => 'Stock alerts', 'type' => 'text' ),
			'body'        => array( 'label' => 'Body', 'default' => '', 'type' => 'textarea' ),
			'placeholder' => array( 'label' => 'Placeholder', 'default' => 'Email address', 'type' => 'text' ),
			'button_text' => array( 'label' => 'Button text', 'default' => 'Join', 'type' => 'text' ),
		) as $key => $field
	) {
		sln_add_field_control( $wp_customize, 'sln_newsletter_' . $key, 'sln_newsletter', $field );
	}

	$wp_customize->add_section( 'sln_compare', array( 'title' => 'Compare Products', 'panel' => 'sln_global', 'priority' => 33 ) );
	foreach (
		array(
			'enabled'        => array( 'label' => 'Enable compare', 'default' => true, 'type' => 'checkbox' ),
			'checkbox_label' => array( 'label' => 'Checkbox label', 'default' => 'Compare', 'type' => 'text' ),
			'max_items'      => array( 'label' => 'Max items (2–4)', 'default' => 4, 'type' => 'number', 'input_attrs' => array( 'min' => 2, 'max' => 4 ) ),
			'bar_text'       => array( 'label' => 'Bar text (use {count})', 'default' => '{count} selected', 'type' => 'text' ),
			'clear_text'     => array( 'label' => 'Clear text', 'default' => 'Clear', 'type' => 'text' ),
			'button_text'    => array( 'label' => 'Button text', 'default' => 'Compare Now', 'type' => 'text' ),
			'modal_heading'  => array( 'label' => 'Modal heading', 'default' => 'Compare specs', 'type' => 'text' ),
			'max_text'       => array( 'label' => 'Max-reached message (use {max})', 'default' => 'You can compare up to {max} items at a time.', 'type' => 'text' ),
			'view_text'      => array( 'label' => 'View product text', 'default' => 'View Specs', 'type' => 'text' ),
		) as $key => $field
	) {
		sln_add_field_control( $wp_customize, 'sln_compare_' . $key, 'sln_compare', $field );
	}

	$wp_customize->add_panel( 'sln_integrations', array( 'title' => 'Integrations', 'priority' => 6 ) );
	$wp_customize->add_section( 'sln_analytics', array( 'title' => 'Analytics', 'panel' => 'sln_integrations', 'priority' => 10 ) );
	$wp_customize->add_setting( 'sln_ga4_id', array( 'default' => '', 'sanitize_callback' => 'sln_sanitize_ga4_id' ) );
	$wp_customize->add_control( 'sln_ga4_id', array( 'label' => 'GA4 Measurement ID', 'description' => 'Example: G-XXXXXXXX. Empty disables it.', 'section' => 'sln_analytics', 'type' => 'text' ) );
	$wp_customize->add_setting( 'sln_gtm_id', array( 'default' => '', 'sanitize_callback' => 'sln_sanitize_gtm_id' ) );
	$wp_customize->add_control( 'sln_gtm_id', array( 'label' => 'Google Tag Manager container ID', 'description' => 'Example: GTM-XXXXXXX. Prints the head script and the noscript body snippet.', 'section' => 'sln_analytics', 'type' => 'text' ) );
	$wp_customize->add_section( 'sln_targetcrm', array( 'title' => 'TargetCRM', 'panel' => 'sln_integrations', 'priority' => 20 ) );
	$wp_customize->add_setting( 'sln_targetcrm_client_id', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'sln_targetcrm_client_id', array( 'label' => 'TargetCRM client ID', 'description' => 'Empty means the widget does not load.', 'section' => 'sln_targetcrm', 'type' => 'text' ) );

	$defs     = sln_section_defs();
	$priority = 40;
	foreach ( $defs as $page => $page_def ) {
		$panel_id = 'sln_page_' . $page;
		$wp_customize->add_panel(
			$panel_id,
			array(
				'title'           => $page_def['label'],
				'priority'        => $priority++,
				'active_callback' => sln_panel_active_callback( $page ),
			)
		);

		$order_section_id = $panel_id . '_order';
		$wp_customize->add_section( $order_section_id, array( 'title' => 'Section Order', 'panel' => $panel_id, 'priority' => 0 ) );
		$default_order = array();
		foreach ( $page_def['default_order'] as $id ) {
			$default_order[] = array( 'id' => $id, 'enabled' => true );
		}
		$wp_customize->add_setting(
			'sln_' . $page . '_section_order',
			array(
				'default'           => wp_json_encode( $default_order ),
				'sanitize_callback' => 'sln_sanitize_section_order',
				'transport'         => 'refresh',
			)
		);
		$sections_config = array();
		foreach ( $page_def['sections'] as $section_id => $section_def ) {
			$sections_config[ $section_id ] = $section_def['label'];
		}
		$wp_customize->add_control(
			new SLN_Section_Order_Control(
				$wp_customize,
				'sln_' . $page . '_section_order',
				array(
					'label'           => 'Sections',
					'section'         => $order_section_id,
					'sections_config' => $sections_config,
				)
			)
		);

		$sub = 10;
		foreach ( $page_def['sections'] as $section_id => $section_def ) {
			if ( empty( $section_def['fields'] ) ) {
				continue;
			}
			$customizer_section_id = $panel_id . '_' . $section_id;
			$wp_customize->add_section( $customizer_section_id, array( 'title' => $section_def['label'], 'panel' => $panel_id, 'priority' => $sub++ ) );
			foreach ( $section_def['fields'] as $field_id => $field ) {
				sln_add_field_control( $wp_customize, 'sln_' . $page . '_' . $section_id . '_' . $field_id, $customizer_section_id, $field );
			}
		}
	}
}
add_action( 'customize_register', 'sln_customize_register' );
