<?php
/**
 * Shop, product taxonomies, and product search.
 */
get_header();

if ( ! function_exists( 'wc_get_products' ) ) {
	echo '<main class="sl-page sl-pad"><p>The catalog needs WooCommerce.</p></main>';
	get_footer();
	return;
}

$GLOBALS['sln_shop'] = sln_catalog_query();
$order             = sln_get_section_order( 'shop' );

if ( in_array( 'breadcrumb', $order, true ) ) {
	get_template_part( 'template-parts/sections/shop-breadcrumb' );
}

$row = array_values( array_intersect( $order, array( 'filters', 'products_grid' ) ) );
if ( $row ) {
	echo '<div class="sl-shop sl-pad">';
	if ( in_array( 'products_grid', $row, true ) ) {
		if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
			$heading = get_queried_object()->name;
		} elseif ( is_search() ) {
			$heading = get_search_query();
		} else {
			$heading = sln_field( 'shop', 'products_grid', 'heading' );
		}
		echo '<h1 class="sl-shop-title">' . esc_html( $heading ) . '</h1>';
	}
	echo '<div class="sl-shop-layout">';
	foreach ( $row as $section_id ) {
		get_template_part( 'template-parts/sections/shop-' . $section_id );
	}
	echo '</div></div>';
}

get_footer();
