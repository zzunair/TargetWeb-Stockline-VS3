<?php
global $product;
if ( ! is_a( $product, 'WC_Product' ) ) {
	$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;
}
if ( ! $product ) {
	return;
}
$brands = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
if ( is_wp_error( $brands ) ) {
	$brands = array();
}
$cats    = sln_product_categories( $product->get_id() );
$stock   = sln_stock_view( $product );
$selling = sln_ecommerce_enabled();
$phone   = sln_phone();
$gallery = array_values( array_filter( array_unique( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) ) );
$labels  = array(
	'ok'  => sln_field( 'product', 'product_main', 'instock_label' ),
	'low' => sln_field( 'product', 'product_main', 'low_label' ),
	'out' => sln_field( 'product', 'product_main', 'out_label' ),
);
$supply = array(
	'ok'  => sln_field( 'product', 'product_main', 'healthy_label' ),
	'low' => sln_field( 'product', 'product_main', 'limited_label' ),
	'out' => sln_field( 'product', 'product_main', 'outofstock_heading' ),
);
$qty_line = null !== $stock['qty'] ? (int) $stock['qty'] . sln_field( 'product', 'product_main', 'stock_location_suffix' ) : $labels[ $stock['state'] ];
$eyebrow  = trim( implode( ' · ', array_filter( array( $brands ? $brands[0] : '', $cats ? $cats[0] : '' ) ) ) );
?>
<div class="sl-sticky-buy" data-sl-sticky hidden>
	<div class="sl-wrap sl-sticky-row">
		<div><strong><?php echo esc_html( $product->get_name() ); ?></strong> <span class="is-<?php echo esc_attr( $stock['state'] ); ?>">● <?php echo esc_html( $qty_line ); ?></span></div>
		<?php if ( $phone ) : ?><a class="sl-btn" href="<?php echo esc_url( sln_tel_href( $phone ) ); ?>"><?php echo esc_html( sln_field( 'product', 'product_main', 'call_text' ) ); ?></a><?php endif; ?>
	</div>
</div>
<section class="sl-product sl-pad">
	<div class="sl-product-grid">
		<div>
			<div class="sl-gallery-main" id="sl-gallery-main">
				<?php if ( $gallery ) : ?>
					<?php echo wp_get_attachment_image( $gallery[0], 'large', false, sln_image_attrs( $gallery[0], $product->get_name(), array( 'id' => 'sl-gallery-img' ) ) ); ?>
				<?php else : ?>
					<?php sln_placeholder( sln_field( 'product', 'product_main', 'main_photo_placeholder' ) ); ?>
				<?php endif; ?>
				<?php if ( $gallery ) : ?><button type="button" class="sl-zoom" data-sl-zoom><?php echo esc_html( sln_field( 'product', 'product_main', 'zoom_label' ) ); ?></button><?php endif; ?>
			</div>
			<?php if ( count( $gallery ) > 1 ) : ?>
				<div class="sl-thumbs" data-sl-gallery>
					<?php foreach ( array_slice( $gallery, 0, 5 ) as $i => $id ) : ?>
						<button type="button" class="<?php echo 0 === $i ? 'is-on' : ''; ?>" data-full="<?php echo esc_url( wp_get_attachment_image_url( $id, 'large' ) ); ?>">
							<?php echo wp_get_attachment_image( $id, 'thumbnail', false, sln_image_attrs( $id, $product->get_name() ) ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<div>
			<?php if ( $eyebrow ) : ?><div class="sl-eyebrow"><?php echo esc_html( $eyebrow ); ?></div><?php endif; ?>
			<h1><?php echo esc_html( $product->get_name() ); ?></h1>
			<?php
			$model = sln_product_model( $product );
			$sku   = $product->get_sku();
			?>
			<?php if ( ( $model && $model !== $sku ) || $sku ) : ?>
			<div class="sl-meta-line">
				<?php if ( $model && $model !== $sku ) : ?><span><?php echo esc_html( sln_field( 'product', 'product_main', 'model_label' ) ); ?> <b><?php echo esc_html( $model ); ?></b></span><?php endif; ?>
				<?php if ( $sku ) : ?><span><?php echo esc_html( sln_field( 'product', 'product_main', 'sku_label' ) ); ?> <b><?php echo esc_html( $sku ); ?></b></span><?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ( $product->get_short_description() ) : ?><div class="sl-summary"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div><?php endif; ?>

			<div class="sl-inventory">
				<div class="sl-inventory-top">
					<span class="sl-eyebrow"><?php echo esc_html( sln_field( 'product', 'product_main', 'inventory_label' ) ); ?></span>
					<span class="sl-pill is-<?php echo esc_attr( $stock['state'] ); ?>">● <?php echo esc_html( $labels[ $stock['state'] ] ); ?></span>
				</div>
				<?php if ( null !== $stock['qty'] ) : ?>
					<div class="sl-qty"><span><?php echo (int) $stock['qty']; ?></span><span><?php echo esc_html( sln_field( 'product', 'product_main', 'stock_location_suffix' ) ); ?></span></div>
				<?php endif; ?>
				<div class="sl-bar sl-bar-lg"><span class="is-<?php echo esc_attr( $stock['state'] ); ?>" style="width:<?php echo (int) $stock['percent']; ?>%"></span></div>
				<div class="sl-inventory-foot"><span><?php echo esc_html( sln_field( 'product', 'product_main', 'updated_label' ) ); ?>: <?php echo esc_html( get_the_modified_date( 'M j, Y · g:i A' ) ); ?></span><span><?php echo esc_html( $supply[ $stock['state'] ] ); ?></span></div>
				<div class="sl-product-actions">
					<?php if ( $phone ) : ?><a class="sl-btn" href="<?php echo esc_url( sln_tel_href( $phone ) ); ?>"><?php echo esc_html( sln_field( 'product', 'product_main', 'call_text' ) ); ?></a><?php endif; ?>
					<?php if ( $selling ) : ?>
						<button type="button" class="sl-btn sl-btn-ghost sl-request-open" aria-expanded="false" aria-controls="sl-request"><?php echo esc_html( sln_field( 'product', 'product_main', 'request_text' ) ); ?></button>
					<?php else : ?>
						<a class="sl-btn sl-btn-ghost" href="#sl-reserve"><?php echo esc_html( sln_field( 'product', 'product_main', 'request_text' ) ); ?></a>
					<?php endif; ?>
					<a href="<?php echo esc_url( sln_field( 'product', 'product_main', 'directions_link' ) ); ?>"><?php echo esc_html( sln_field( 'product', 'product_main', 'directions_text' ) ); ?></a>
				</div>
				<?php if ( $selling && $product->is_in_stock() ) : ?>
					<div class="sl-purchase"><?php woocommerce_template_single_add_to_cart(); ?></div>
				<?php endif; ?>
				<?php if ( sln_mod( 'sln_compare_enabled', true ) ) : ?>
					<label class="sl-compare sl-compare-inline"><input type="checkbox" class="sl-compare-box" <?php echo sln_compare_checkbox_attrs( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> /><span><?php echo esc_html( sln_mod( 'sln_compare_checkbox_label', 'Compare' ) ); ?></span></label>
				<?php endif; ?>
			</div>
			<?php if ( ! $selling ) : ?><p class="sl-note"><?php echo esc_html( sln_field( 'product', 'product_main', 'price_note' ) ); ?></p><?php endif; ?>

			<?php if ( $selling ) : ?><div class="sl-modal" id="sl-request" hidden><div class="sl-modal-backdrop" data-close-request></div><div class="sl-modal-dialog" role="dialog" aria-modal="true"><?php endif; ?>
			<div class="sl-reserve" id="sl-reserve">
				<?php if ( $selling ) : ?><button type="button" class="sl-modal-x" data-close-request aria-label="<?php echo esc_attr( sln_field( 'product', 'product_main', 'close_label' ) ); ?>">×</button><?php endif; ?>
				<?php if ( $product->is_in_stock() ) : ?>
					<h2><?php echo esc_html( sln_field( 'product', 'product_main', 'reserve_heading' ) ); ?></h2>
					<p><?php echo esc_html( sln_field( 'product', 'product_main', 'reserve_intro' ) ); ?></p>
					<div id="tw-crm-popup" data-tw-crm-inline data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
						<div id="leads-form-div"></div>
					</div>
				<?php else : ?>
					<h2><?php echo esc_html( sln_field( 'product', 'product_main', 'outofstock_heading' ) ); ?></h2>
					<p><?php echo esc_html( sln_field( 'product', 'product_main', 'outofstock_text' ) ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $selling ) : ?></div></div><?php endif; ?>
		</div>
	</div>
</section>
<?php if ( $gallery ) : ?>
<div class="sl-zoom-view" id="sl-zoom-view" hidden>
	<button type="button" data-close-zoom aria-label="<?php echo esc_attr( sln_field( 'product', 'product_main', 'close_label' ) ); ?>">×</button>
	<img alt="<?php echo esc_attr( $product->get_name() ); ?>" src="<?php echo esc_url( wp_get_attachment_image_url( $gallery[0], 'full' ) ); ?>" />
</div>
<?php endif; ?>
