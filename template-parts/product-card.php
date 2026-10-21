<?php
/**
 * Product card. Expects $product (WC_Product) plus optional $sln_card.
 */
if ( empty( $product ) || ! is_a( $product, 'WC_Product' ) ) {
	return;
}
$card      = isset( $sln_card ) && is_array( $sln_card ) ? $sln_card : array();
$link_text = isset( $card['link_text'] ) ? $card['link_text'] : 'View Specs';
$price_note = isset( $card['price_note'] ) ? $card['price_note'] : 'Price on request';
$layout    = isset( $card['layout'] ) ? $card['layout'] : 'grid';
$compare   = ! isset( $card['compare'] ) || $card['compare'];
$selling   = function_exists( 'sln_ecommerce_enabled' ) && sln_ecommerce_enabled();
$brands    = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
if ( is_wp_error( $brands ) ) {
	$brands = array();
}
$stock           = sln_stock_view( $product );
$image_id        = $product->get_image_id();
$second_image    = ! array_key_exists( 'second_image', $card ) || $card['second_image'];
$exact_inventory = ! array_key_exists( 'exact_inventory', $card ) || $card['exact_inventory'];
$gallery         = $second_image ? $product->get_gallery_image_ids() : array();
$hover_id        = $gallery ? (int) $gallery[0] : 0;
$show_hover      = $hover_id && $hover_id !== (int) $image_id;
$chips    = sln_product_chips( $product, 3 );
$specs    = sln_product_specs( $product, 3 );
$labels   = array(
	'ok'  => sln_field( 'product', 'product_main', 'instock_label' ),
	'low' => sln_field( 'product', 'product_main', 'low_label' ),
	'out' => sln_field( 'product', 'product_main', 'out_label' ),
);
$qty_text = null !== $stock['qty'] ? (int) $stock['qty'] . sln_field( 'product', 'product_main', 'stock_location_suffix' ) : $labels[ $stock['state'] ];
?>
<article class="sl-card<?php echo 'list' === $layout ? ' is-list' : ''; ?>">
	<a class="sl-card-media<?php echo $show_hover ? ' has-swap' : ''; ?>" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
		<?php if ( $image_id ) : ?>
			<?php echo wp_get_attachment_image( $image_id, 'medium', false, sln_image_attrs( $image_id, $product->get_name(), array( 'class' => 'sl-card-photo' ) ) ); ?>
			<?php if ( $show_hover ) : ?>
				<?php echo wp_get_attachment_image( $hover_id, 'medium', false, sln_image_attrs( $hover_id, '', array( 'class' => 'sl-card-hover', 'loading' => 'eager' ) ) ); ?>
			<?php endif; ?>
		<?php else : ?>
			<?php sln_placeholder( $product->get_name() ); ?>
		<?php endif; ?>
	</a>
	<?php if ( $compare && sln_mod( 'sln_compare_enabled', true ) ) : ?>
		<label class="sl-compare">
			<input type="checkbox" class="sl-compare-box" <?php echo sln_compare_checkbox_attrs( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
			<span><?php echo esc_html( sln_mod( 'sln_compare_checkbox_label', 'Compare' ) ); ?></span>
		</label>
	<?php endif; ?>
	<div class="sl-card-body">
		<?php if ( $brands ) : ?><div class="sl-eyebrow"><?php echo esc_html( $brands[0] ); ?></div><?php endif; ?>
		<h3><a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<?php if ( $product->get_sku() ) : ?><div class="sl-mono"><?php echo esc_html( $product->get_sku() ); ?></div><?php endif; ?>
		<?php if ( $chips ) : ?>
			<div class="sl-chips"><?php foreach ( $chips as $chip ) : ?><span><?php echo esc_html( $chip ); ?></span><?php endforeach; ?></div>
		<?php endif; ?>
		<?php if ( $specs ) : ?>
			<table class="sl-mini"><tbody>
				<?php foreach ( $specs as $row ) : ?>
					<tr><td><?php echo esc_html( $row['label'] ); ?></td><td><?php echo esc_html( $row['value'] ); ?></td></tr>
				<?php endforeach; ?>
			</tbody></table>
		<?php endif; ?>
		<div class="sl-meter">
			<div class="sl-meter-top"><span class="is-<?php echo esc_attr( $stock['state'] ); ?>">● <?php echo esc_html( $labels[ $stock['state'] ] ); ?></span><?php if ( $exact_inventory ) : ?><span class="sl-mono"><?php echo esc_html( $qty_text ); ?></span><?php endif; ?></div>
			<div class="sl-bar"><span class="is-<?php echo esc_attr( $stock['state'] ); ?>" style="width:<?php echo (int) $stock['percent']; ?>%"></span></div>
		</div>
		<div class="sl-card-foot">
			<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_html( $link_text ); ?> →</a>
			<?php if ( $selling && '' !== (string) $product->get_price() ) : ?>
				<span class="sl-price"><?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?></span>
			<?php elseif ( $price_note ) : ?>
				<span class="sl-note"><?php echo esc_html( $price_note ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( $selling && $product->is_purchasable() && $product->is_in_stock() ) : ?>
			<a class="sl-btn sl-card-cart add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" rel="nofollow"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
		<?php endif; ?>
	</div>
</article>
