<?php
$blocks = sln_repeater( 'home', 'brands', 'logos' );
$items  = array();
if ( $blocks ) {
	foreach ( $blocks as $block ) {
		$term = ( ! empty( $block['brand'] ) && taxonomy_exists( 'product_brand' ) ) ? get_term_by( 'slug', $block['brand'], 'product_brand' ) : null;
		$name = $block['label'] ? $block['label'] : ( $term && ! is_wp_error( $term ) ? $term->name : '' );
		if ( ! $name ) {
			continue;
		}
		$link = $block['link'];
		if ( ! $link && $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
		}
		$items[] = array( 'name' => $name, 'image' => $block['image'], 'link' => $link ? $link : sln_shop_url() );
	}
} elseif ( taxonomy_exists( 'product_brand' ) ) {
	$terms = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$items[] = array( 'name' => $term->name, 'image' => '', 'link' => get_term_link( $term ) );
		}
	}
}
if ( ! $items ) {
	sln_render_empty_state( 'Brands', 'Add brands and they will show in this strip.' );
	return;
}
?>
<section class="sl-brands sl-pad">
	<div class="sl-wrap sl-brand-row">
		<?php foreach ( $items as $item ) : ?>
			<a href="<?php echo esc_url( $item['link'] ); ?>">
				<?php if ( $item['image'] ) : ?><img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" /><?php else : ?><?php echo esc_html( $item['name'] ); ?><?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
</section>
