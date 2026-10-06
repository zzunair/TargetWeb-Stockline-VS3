<?php
if ( ! function_exists( 'wc_get_products' ) ) {
	sln_render_empty_state( sln_field( 'home', 'featured_products', 'heading' ), 'WooCommerce is required before products can show.' );
	return;
}
$count    = max( 1, (int) sln_field( 'home', 'featured_products', 'count' ) );
$products = wc_get_products( array( 'status' => 'publish', 'limit' => $count, 'featured' => true, 'orderby' => 'date', 'order' => 'DESC' ) );
if ( ! $products ) {
	$products = wc_get_products( array( 'status' => 'publish', 'limit' => $count, 'orderby' => 'date', 'order' => 'DESC' ) );
}
if ( ! $products ) {
	sln_render_empty_state( sln_field( 'home', 'featured_products', 'heading' ), 'Add products and they will show here.' );
	return;
}
$sln_card = array(
	'link_text'  => sln_field( 'home', 'featured_products', 'link_text' ),
	'price_note' => sln_field( 'home', 'featured_products', 'price_note' ),
);
?>
<section class="sl-section sl-pad">
	<div class="sl-wrap">
		<div class="sl-head">
			<div>
				<?php if ( sln_field( 'home', 'featured_products', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'home', 'featured_products', 'eyebrow' ) ); ?></div><?php endif; ?>
				<h2><?php echo esc_html( sln_field( 'home', 'featured_products', 'heading' ) ); ?></h2>
			</div>
			<?php if ( sln_field( 'home', 'featured_products', 'view_all_text' ) ) : ?>
				<a href="<?php echo esc_url( sln_field( 'home', 'featured_products', 'view_all_link' ) ); ?>"><?php echo esc_html( sln_field( 'home', 'featured_products', 'view_all_text' ) ); ?> →</a>
			<?php endif; ?>
		</div>
		<div class="sl-cards">
			<?php foreach ( $products as $product ) : ?>
				<?php include get_theme_file_path( 'template-parts/product-card.php' ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
