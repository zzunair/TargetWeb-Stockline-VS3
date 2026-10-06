<?php
global $product;
if ( ! is_a( $product, 'WC_Product' ) || ! function_exists( 'wc_get_related_products' ) ) {
	return;
}
$ids = wc_get_related_products( $product->get_id(), max( 1, (int) sln_field( 'product', 'related', 'count' ) ) );
if ( ! $ids ) {
	return;
}
$products = array_filter( array_map( 'wc_get_product', $ids ) );
if ( ! $products ) {
	return;
}
?>
<section class="sl-section sl-pad" id="related">
	<div class="sl-wrap">
		<h2><?php echo esc_html( sln_field( 'product', 'related', 'heading' ) ); ?></h2>
		<div class="sl-related">
			<?php foreach ( $products as $item ) :
				$brands = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $item->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
				$stock  = sln_stock_view( $item );
				$label  = array( 'ok' => 'In stock', 'low' => 'Low stock', 'out' => 'Out of stock' );
				?>
				<article class="sl-related-card">
					<?php if ( $brands && ! is_wp_error( $brands ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( $brands[0] ); ?></div><?php endif; ?>
					<h3><?php echo esc_html( $item->get_name() ); ?></h3>
					<?php if ( $item->get_sku() ) : ?><div class="sl-mono"><?php echo esc_html( $item->get_sku() ); ?></div><?php endif; ?>
					<div class="sl-card-foot"><span class="is-<?php echo esc_attr( $stock['state'] ); ?>">● <?php echo esc_html( $label[ $stock['state'] ] ); ?></span><a href="<?php echo esc_url( get_permalink( $item->get_id() ) ); ?>"><?php echo esc_html( sln_field( 'product', 'related', 'link_text' ) ); ?> →</a></div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
