<?php
global $product;
$id    = is_a( $product, 'WC_Product' ) ? $product->get_id() : get_the_ID();
$items = sln_get_recently_viewed_products( $id, max( 1, (int) sln_field( 'product', 'recently_viewed', 'count' ) ) );
if ( ! $items ) {
	return;
}
?>
<section class="sl-section sl-pad">
	<div class="sl-wrap">
		<h2><?php echo esc_html( sln_field( 'product', 'recently_viewed', 'heading' ) ); ?></h2>
		<div class="sl-related">
			<?php foreach ( $items as $item ) :
				$brands = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $item->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
				$stock  = sln_stock_view( $item );
				$label  = array( 'ok' => 'In stock', 'low' => 'Low stock', 'out' => 'Out of stock' );
				?>
				<article class="sl-related-card">
					<?php if ( $brands && ! is_wp_error( $brands ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( $brands[0] ); ?></div><?php endif; ?>
					<h3><?php echo esc_html( $item->get_name() ); ?></h3>
					<?php if ( $item->get_sku() ) : ?><div class="sl-mono"><?php echo esc_html( $item->get_sku() ); ?></div><?php endif; ?>
					<div class="sl-card-foot"><span class="is-<?php echo esc_attr( $stock['state'] ); ?>">● <?php echo esc_html( $label[ $stock['state'] ] ); ?></span><a href="<?php echo esc_url( get_permalink( $item->get_id() ) ); ?>"><?php echo esc_html( sln_field( 'product', 'recently_viewed', 'link_text' ) ); ?> →</a></div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
