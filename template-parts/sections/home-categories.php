<?php
if ( ! taxonomy_exists( 'product_cat' ) ) {
	sln_render_empty_state( sln_field( 'home', 'categories', 'heading' ), 'WooCommerce is required before categories can show.' );
	return;
}
$blocks = sln_repeater( 'home', 'categories', 'collections' );
$items  = array();
if ( $blocks ) {
	foreach ( $blocks as $block ) {
		if ( empty( $block['category'] ) ) {
			continue;
		}
		$term = get_term_by( 'slug', $block['category'], 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}
		$items[] = array(
			'name'  => $block['label'] ? $block['label'] : $term->name,
			'code'  => $block['code'] ? $block['code'] : sln_category_code( $term->name ),
			'count' => sln_term_live_count( 'product_cat', $term->slug ),
			'link'  => $block['link'] ? $block['link'] : get_term_link( $term ),
			'image' => $block['image'],
		);
	}
} else {
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$count = sln_term_live_count( 'product_cat', $term->slug );
			if ( ! $count && ! current_user_can( 'edit_theme_options' ) ) {
				continue;
			}
			$thumb = get_term_meta( $term->term_id, 'thumbnail_id', true );
			$items[] = array(
				'name'  => $term->name,
				'code'  => sln_category_code( $term->name ),
				'count' => $count,
				'link'  => get_term_link( $term ),
				'image' => $thumb ? wp_get_attachment_image_url( $thumb, 'thumbnail' ) : '',
			);
		}
	}
}
$limit = (int) sln_field( 'home', 'categories', 'limit' );
if ( $limit > 0 ) {
	$items = array_slice( $items, 0, $limit );
}
if ( ! $items ) {
	sln_render_empty_state( sln_field( 'home', 'categories', 'heading' ), 'Add product categories and they will show here.' );
	return;
}
?>
<section class="sl-section sl-pad">
	<div class="sl-wrap">
		<?php if ( sln_field( 'home', 'categories', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'home', 'categories', 'eyebrow' ) ); ?></div><?php endif; ?>
		<h2><?php echo esc_html( sln_field( 'home', 'categories', 'heading' ) ); ?></h2>
		<div class="sl-cat-grid">
			<?php foreach ( $items as $item ) : ?>
				<a class="sl-cat" href="<?php echo esc_url( $item['link'] ); ?>">
					<span class="sl-cat-ic">
						<?php if ( $item['image'] ) : ?><img src="<?php echo esc_url( $item['image'] ); ?>" alt="" /><?php else : ?><?php echo esc_html( $item['code'] ); ?><?php endif; ?>
					</span>
					<span><strong><?php echo esc_html( $item['name'] ); ?></strong><span class="sl-mono"><?php echo esc_html( number_format_i18n( $item['count'] ) . sln_field( 'home', 'categories', 'count_suffix' ) ); ?></span></span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php if ( sln_field( 'home', 'categories', 'view_all_text' ) ) : ?>
			<p class="sl-more"><a href="<?php echo esc_url( sln_field( 'home', 'categories', 'view_all_link' ) ); ?>"><?php echo esc_html( sln_field( 'home', 'categories', 'view_all_text' ) ); ?> →</a></p>
		<?php endif; ?>
	</div>
</section>
