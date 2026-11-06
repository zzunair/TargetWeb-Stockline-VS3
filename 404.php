<?php
/**
 * Branded 404. Catalog link falls back to /shop/ when WooCommerce is off.
 */
get_header();
$shop = function_exists( 'sln_shop_url' ) ? sln_shop_url() : home_url( '/shop/' );
?>
<main class="sl-page sl-pad">
	<div class="sl-eyebrow">404</div>
	<h1>That page is not on the floor.</h1>
	<p class="sl-lede">The link may be old, or the machine may have moved. Try the catalog, or head back to the showroom.</p>
	<div class="sl-actions">
		<a class="sl-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
		<a class="sl-btn sl-btn-ghost" href="<?php echo esc_url( $shop ); ?>">Catalog</a>
	</div>
</main>
<?php
get_footer();
