<?php
/**
 * Product search uses the shop layout. Everything else uses the fallback.
 */
if ( isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] && function_exists( 'wc_get_products' ) ) {
	$stockline_template = locate_template( 'archive-product.php' );
	if ( $stockline_template ) {
		require $stockline_template;
		return;
	}
}
$stockline_template = locate_template( 'index.php' );
if ( $stockline_template ) {
	require $stockline_template;
}
