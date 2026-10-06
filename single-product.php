<?php
/**
 * Single product.
 */
get_header();
if ( function_exists( 'wc_get_product' ) ) {
	while ( have_posts() ) {
		the_post();
		$GLOBALS['product'] = wc_get_product( get_the_ID() );
		sln_render_sections( 'product' );
	}
}
get_footer();
