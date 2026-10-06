<?php
if ( ! taxonomy_exists( 'product_brand' ) ) {
	sln_render_empty_state( sln_field( 'brands', 'intro', 'heading' ), 'WooCommerce is required before brands can show.' );
	return;
}
$overrides = array();
foreach ( sln_repeater( 'brands', 'brand_cards', 'cards' ) as $card ) {
	if ( ! empty( $card['brand'] ) ) {
		$overrides[ $card['brand'] ] = $card;
	}
}
$terms = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
if ( is_wp_error( $terms ) || ! $terms ) {
	sln_render_empty_state( sln_field( 'brands', 'intro', 'heading' ), 'Add brands and they will show here.' );
	return;
}
if ( $overrides ) {
	$ordered = array();
	foreach ( $overrides as $slug => $card ) {
		$term = get_term_by( 'slug', $slug, 'product_brand' );
		if ( $term && ! is_wp_error( $term ) ) {
			$ordered[] = $term;
		}
	}
	if ( $ordered ) {
		$terms = $ordered;
	}
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap sl-brand-cards">
	<?php foreach ( $terms as $term ) :
		$card  = isset( $overrides[ $term->slug ] ) ? $overrides[ $term->slug ] : array( 'body' => $term->description, 'image' => '' );
		$count = sln_term_live_count( 'product_brand', $term->slug );
		$stock = 0;
		if ( function_exists( 'wc_get_products' ) ) {
			foreach ( wc_get_products( array( 'status' => 'publish', 'product_brand' => array( $term->slug ), 'limit' => -1 ) ) as $product ) {
				if ( $product->managing_stock() ) {
					$stock += max( 0, (int) $product->get_stock_quantity() );
				} elseif ( $product->is_in_stock() ) {
					$stock++;
				}
			}
		}
		$body = isset( $card['body'] ) && $card['body'] ? $card['body'] : $term->description;
		?>
		<article>
			<div class="sl-brand-logo">
				<?php if ( ! empty( $card['image'] ) ) : ?><img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" /><?php else : ?><?php echo esc_html( $term->name ); ?><?php endif; ?>
			</div>
			<?php if ( $body ) : ?><p><?php echo esc_html( wp_strip_all_tags( $body ) ); ?></p><?php endif; ?>
			<div class="sl-brand-counts">
				<span><b><?php echo (int) $count; ?></b><?php echo esc_html( sln_field( 'brands', 'brand_cards', 'products_label' ) ); ?></span>
				<span><b class="is-ok"><?php echo (int) $stock; ?></b><?php echo esc_html( sln_field( 'brands', 'brand_cards', 'stock_label' ) ); ?></span>
			</div>
			<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( str_replace( '{name}', $term->name, sln_field( 'brands', 'brand_cards', 'link_text' ) ) ); ?> →</a>
		</article>
	<?php endforeach; ?>
</div></section>
