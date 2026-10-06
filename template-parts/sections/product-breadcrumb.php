<?php
global $product;
if ( ! is_a( $product, 'WC_Product' ) ) {
	$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;
}
if ( ! $product ) {
	return;
}
$cats  = sln_product_categories( $product->get_id(), 'all' );
$first = $cats ? $cats[0] : null;
$items = array(
	array( 'label' => sln_field( 'product', 'breadcrumb', 'home_label' ), 'url' => home_url( '/' ) ),
	array( 'label' => sln_field( 'product', 'breadcrumb', 'shop_label' ), 'url' => sln_shop_url() ),
);
if ( $first ) {
	$link = get_term_link( $first );
	$items[] = array( 'label' => $first->name, 'url' => is_wp_error( $link ) ? '' : $link );
}
$items[] = array( 'label' => $product->get_name(), 'url' => '' );
echo '<div class="sl-pad sl-crumb-wrap">';
sln_breadcrumb( $items );
echo '</div>';
