<?php
/**
 * Site header. Utility bar, wordmark, nav, search, contact button.
 */
$current = '';
if ( is_front_page() ) {
	$current = 'home';
} elseif ( is_page( 'brands' ) ) {
	$current = 'brands';
} elseif ( is_page( 'service' ) ) {
	$current = 'service';
} elseif ( is_page( 'financing' ) ) {
	$current = 'financing';
} elseif ( is_page( 'about' ) ) {
	$current = 'about';
} elseif ( is_page( 'contact' ) ) {
	$current = 'contact';
} elseif ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_search() || is_singular( 'product' ) ) ) {
	$current = 'shop';
}

$logo_text  = sln_mod( 'sln_header_logo_text', get_bloginfo( 'name' ) );
$logo_image = sln_mod( 'sln_header_logo_image', '' );
$tagline    = sln_mod( 'sln_header_logo_tagline', '' );
$reserve    = sln_mod( 'sln_header_reserve_button_text', 'Contact Store' );
$reserve_url = sln_mod( 'sln_header_reserve_button_link', '/contact/' );
$left       = sln_mod( 'sln_header_top_bar_left', 'Mon–Fri 8–6 · Sat 8–4 · Sun closed' );
$right      = sln_mod( 'sln_header_top_bar_right', '(123) 456-7890 · hello@yourstore.com' );
$phone      = sln_phone();
$shop       = function_exists( 'sln_shop_url' ) ? sln_shop_url() : home_url( '/shop/' );

$fallback = array(
	'home'    => array( sln_field( 'shop', 'breadcrumb', 'home_label' ), home_url( '/' ) ),
	'shop'    => array( sln_field( 'shop', 'breadcrumb', 'current_label' ), $shop ),
	'brands'  => array( sln_field( 'brands', 'intro', 'crumb' ), home_url( '/brands/' ) ),
	'service'   => array( sln_field( 'service', 'hero', 'crumb' ), home_url( '/service/' ) ),
	'financing' => array( sln_field( 'financing', 'hero', 'crumb' ), home_url( '/financing/' ) ),
	'about'     => array( sln_field( 'about', 'intro', 'crumb' ), home_url( '/about/' ) ),
	'contact' => array( sln_field( 'contact', 'intro', 'crumb' ), home_url( '/contact/' ) ),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( $left || $right ) : ?>
<div class="sl-util">
	<?php if ( $left ) : ?><span><?php echo esc_html( $left ); ?></span><?php endif; ?>
	<?php if ( $right ) : ?><span class="sl-util-right"><?php echo esc_html( $right ); ?></span><?php endif; ?>
</div>
<?php endif; ?>

<header class="sl-header">
	<a class="sl-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php if ( $logo_image ) : ?>
			<img src="<?php echo esc_url( $logo_image ); ?>" alt="<?php echo esc_attr( $logo_text ); ?>" style="max-width:<?php echo (int) sln_mod( 'sln_header_logo_max_width', 180 ); ?>px;max-height:<?php echo (int) sln_mod( 'sln_header_logo_max_height', 36 ); ?>px;" />
		<?php else : ?>
			<span class="sl-mark" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $logo_text ? $logo_text : 'S', 0, 1 ) ) ); ?></span>
			<span class="sl-logo-lockup">
				<span class="sl-logo-text"><?php echo esc_html( $logo_text ); ?></span>
				<?php if ( $tagline ) : ?><span class="sl-tagline"><?php echo esc_html( $tagline ); ?></span><?php endif; ?>
			</span>
		<?php endif; ?>
	</a>

	<nav class="sl-nav" aria-label="Primary">
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'depth' => 1 ) ); ?>
		<?php else : ?>
			<ul>
				<?php foreach ( $fallback as $key => $item ) : ?>
					<li><a href="<?php echo esc_url( $item[1] ); ?>" class="<?php echo $current === $key ? 'is-active' : ''; ?>"><?php echo esc_html( $item[0] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</nav>

	<button type="button" class="sl-search-open" aria-expanded="false" aria-controls="sl-search">
		<span class="sl-search-icon" aria-hidden="true">⌕</span>
		<span class="sl-search-ph"><?php echo esc_html( sln_mod( 'sln_header_search_placeholder', 'Search model, brand or category' ) ); ?></span>
		<kbd class="sl-kbd">/</kbd>
	</button>

	<?php if ( function_exists( 'sln_cart_link' ) ) { sln_cart_link(); } ?>

	<?php if ( $reserve ) : ?>
		<a class="sl-header-cta" href="<?php echo esc_url( $reserve_url ); ?>"><?php echo esc_html( $reserve ); ?></a>
	<?php endif; ?>

	<button type="button" class="sl-menu-open" aria-expanded="false" aria-controls="sl-nav-sheet"><?php echo esc_html( sln_mod( 'sln_header_menu_label', 'Menu' ) ); ?></button>
</header>

<div class="sl-sheet" id="sl-nav-sheet" hidden>
	<div class="sl-sheet-backdrop" data-close-sheet></div>
	<div class="sl-sheet-panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( sln_mod( 'sln_header_menu_label', 'Menu' ) ); ?>">
		<div class="sl-sheet-head">
			<strong><?php echo esc_html( sln_mod( 'sln_header_menu_label', 'Menu' ) ); ?></strong>
			<button type="button" class="sl-sheet-close" data-close-sheet aria-label="Close">×</button>
		</div>
		<nav class="sl-sheet-nav">
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'depth' => 1 ) ); ?>
			<?php else : ?>
				<ul>
					<?php foreach ( $fallback as $item ) : ?>
						<li><a href="<?php echo esc_url( $item[1] ); ?>"><?php echo esc_html( $item[0] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>
	</div>
</div>

<div class="sl-search" id="sl-search" hidden>
	<div class="sl-search-backdrop" data-close-search></div>
	<div class="sl-search-dialog" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( sln_mod( 'sln_header_search_button_text', 'Search' ) ); ?>">
		<form class="sl-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="hidden" name="post_type" value="product" />
			<span class="sl-search-icon" aria-hidden="true">⌕</span>
			<input type="search" name="s" data-sl-live-search placeholder="<?php echo esc_attr( sln_mod( 'sln_header_search_placeholder', 'Search model, brand or category' ) ); ?>" autocomplete="off" />
			<button type="submit" class="sl-btn"><?php echo esc_html( sln_mod( 'sln_header_search_button_text', 'Search' ) ); ?></button>
			<button type="button" class="sl-search-close" data-close-search aria-label="Close">×</button>
		</form>
		<div class="sl-suggest" hidden></div>
	</div>
</div>
