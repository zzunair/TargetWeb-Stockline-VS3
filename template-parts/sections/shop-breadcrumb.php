<?php
$home = sln_field( 'shop', 'breadcrumb', 'home_label' );
$here = sln_field( 'shop', 'breadcrumb', 'current_label' );
$items = array(
	array( 'label' => $home, 'url' => home_url( '/' ) ),
);
if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
	$items[] = array( 'label' => $here, 'url' => sln_shop_url() );
	$term    = get_queried_object();
	if ( $term && ! is_wp_error( $term ) ) {
		$items[] = array( 'label' => $term->name, 'url' => '' );
	}
} elseif ( is_search() ) {
	$items[] = array( 'label' => $here, 'url' => sln_shop_url() );
	$items[] = array( 'label' => get_search_query(), 'url' => '' );
} else {
	$items[] = array( 'label' => $here, 'url' => '' );
}
echo '<div class="sl-pad sl-crumb-wrap">';
sln_breadcrumb( $items );
echo '</div>';
