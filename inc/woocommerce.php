<?php
/**
 * Catalog, optional cart, search suggestions, recently viewed.
 */

function sln_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'sln_woocommerce_setup' );

function sln_register_brand_taxonomy() {
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}
	register_taxonomy(
		'product_brand',
		'product',
		array(
			'label'             => 'Brands',
			'labels'            => array(
				'name'          => 'Brands',
				'singular_name' => 'Brand',
			),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'public'            => true,
			'rewrite'           => array( 'slug' => 'brand' ),
		)
	);
}
add_action( 'init', 'sln_register_brand_taxonomy' );

function sln_ecommerce_enabled() {
	return (bool) get_theme_mod( 'sln_enable_ecommerce', false );
}

function sln_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
}

function sln_cart_link() {
	if ( ! sln_ecommerce_enabled() || ! function_exists( 'wc_get_cart_url' ) ) {
		return;
	}
	$count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
	$label = $count ? sprintf( 'Cart, %d items', $count ) : 'Cart';
	?>
	<a class="sl-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
		<svg viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true"><path d="M6 6h15l-1.5 9h-12z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6 6L5 3H2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="9" cy="20" r="1.3" fill="currentColor"/><circle cx="18" cy="20" r="1.3" fill="currentColor"/></svg>
		<?php if ( $count ) : ?><span class="sl-cart-count"><?php echo (int) $count; ?></span><?php endif; ?>
	</a>
	<?php
}

function sln_cart_fragments( $fragments ) {
	if ( ! sln_ecommerce_enabled() ) {
		return $fragments;
	}
	ob_start();
	sln_cart_link();
	$fragments['a.sl-cart'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'sln_cart_fragments' );

function sln_commerce_scripts() {
	if ( sln_ecommerce_enabled() ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
		return;
	}
	wp_dequeue_script( 'wc-cart-fragments' );
	wp_dequeue_script( 'wc-add-to-cart' );
}
add_action( 'wp_enqueue_scripts', 'sln_commerce_scripts', 20 );

function sln_redirect_commerce_pages() {
	if ( is_admin() || ! function_exists( 'is_cart' ) ) {
		return;
	}
	$blocked = array();
	if ( ! sln_ecommerce_enabled() ) {
		$blocked[] = is_cart();
		$blocked[] = is_checkout();
	}
	if ( in_array( true, $blocked, true ) ) {
		wp_safe_redirect( sln_shop_url() );
		exit;
	}
}
add_action( 'template_redirect', 'sln_redirect_commerce_pages' );

/**
 * Product IDs matching normal product text plus SKU, variation SKU, brand,
 * category, tag, global attributes, and custom product attributes.
 */
function sln_product_search_ids( $term ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$term = trim( sanitize_text_field( $term ) );
	if ( '' === $term ) {
		return array();
	}

	$ids = wc_get_products(
		array(
			'status' => 'publish',
			's'      => $term,
			'limit'  => -1,
			'return' => 'ids',
		)
	);

	global $wpdb;
	$like = '%' . $wpdb->esc_like( $term ) . '%';
	$meta_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT CASE WHEN p.post_type = 'product_variation' THEN p.post_parent ELSE p.ID END
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key IN ('_sku', '_product_attributes')
			AND pm.meta_value LIKE %s
			AND p.post_type IN ('product', 'product_variation')",
			$like
		)
	);
	$ids = array_merge( $ids, $meta_ids );

	$taxonomies = array_filter(
		get_object_taxonomies( 'product', 'names' ),
		static function ( $taxonomy ) {
			return in_array( $taxonomy, array( 'product_cat', 'product_tag', 'product_brand' ), true )
				|| 0 === strpos( $taxonomy, 'pa_' );
		}
	);
	if ( $taxonomies ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomies,
				'hide_empty' => true,
				'name__like' => $term,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $matched_term ) {
				$ids = array_merge( $ids, get_objects_in_term( $matched_term->term_id, $matched_term->taxonomy ) );
			}
		}
	}

	return array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
}

function sln_ajax_search_suggest() {
	check_ajax_referer( 'sln_search', 'nonce' );
	if ( ! function_exists( 'wc_get_products' ) || ! sln_mod( 'sln_header_search_suggestions', true ) ) {
		wp_send_json( array() );
	}
	$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
	if ( strlen( trim( $term ) ) < 2 ) {
		wp_send_json( array() );
	}
	$limit       = 6;
	$matched_ids = sln_product_search_ids( $term );
	$products    = wc_get_products(
		array(
			'status'  => 'publish',
			'include' => $matched_ids ? $matched_ids : array( 0 ),
			'limit'   => $limit,
			'orderby' => 'include',
		)
	);
	$results = array();
	foreach ( $products as $product ) {
		$image_id  = $product->get_image_id();
		$stock     = sln_stock_view( $product );
		$price     = '';
		if ( sln_ecommerce_enabled() && '' !== $product->get_price() ) {
			$price = html_entity_decode( wp_strip_all_tags( wc_price( $product->get_price() ) ), ENT_QUOTES, 'UTF-8' );
		}
		$badge_labels = array(
			'ok'  => sln_field( 'product', 'product_main', 'instock_label' ),
			'low' => sln_field( 'product', 'product_main', 'low_label' ),
			'out' => sln_field( 'product', 'product_main', 'out_label' ),
		);
		$badge = isset( $badge_labels[ $stock['state'] ] ) ? $badge_labels[ $stock['state'] ] : '';
		if ( null !== $stock['qty'] ) {
			$badge = (int) $stock['qty'] . sln_field( 'product', 'product_main', 'stock_location_suffix' );
		}
		$results[] = array(
			'name'  => $product->get_name(),
			'price' => $price,
			'image' => $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '',
			'badge' => $badge,
			'url'   => get_permalink( $product->get_id() ),
		);
	}
	wp_send_json( $results );
}
add_action( 'wp_ajax_sln_search_suggest', 'sln_ajax_search_suggest' );
add_action( 'wp_ajax_nopriv_sln_search_suggest', 'sln_ajax_search_suggest' );

function sln_track_recently_viewed() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}
	$id  = get_the_ID();
	$ids = isset( $_COOKIE['sln_recently_viewed'] ) ? array_map( 'absint', explode( ',', wp_unslash( $_COOKIE['sln_recently_viewed'] ) ) ) : array();
	$ids = array_values( array_diff( $ids, array( $id ) ) );
	array_unshift( $ids, $id );
	$ids = array_slice( $ids, 0, 12 );
	setcookie( 'sln_recently_viewed', implode( ',', $ids ), time() + MONTH_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
}
add_action( 'template_redirect', 'sln_track_recently_viewed' );

function sln_get_recently_viewed_products( $exclude_id, $limit = 4 ) {
	if ( empty( $_COOKIE['sln_recently_viewed'] ) || ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$ids = array_map( 'absint', explode( ',', wp_unslash( $_COOKIE['sln_recently_viewed'] ) ) );
	$ids = array_values( array_diff( array_filter( $ids ), array( absint( $exclude_id ) ) ) );
	if ( ! $ids ) {
		return array();
	}
	$products = wc_get_products( array( 'status' => 'publish', 'include' => $ids, 'limit' => -1 ) );
	$by_id    = array();
	foreach ( $products as $product ) {
		$by_id[ $product->get_id() ] = $product;
	}
	$ordered = array();
	foreach ( $ids as $id ) {
		if ( isset( $by_id[ $id ] ) ) {
			$ordered[] = $by_id[ $id ];
		}
	}
	return array_slice( $ordered, 0, $limit );
}

function sln_catalog_query() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array( 'products' => array(), 'total' => 0, 'max_pages' => 1, 'paged' => 1 );
	}
	$per_page = max( 1, (int) sln_field( 'shop', 'products_grid', 'per_page' ) );
	$paged    = max( 1, (int) get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : ( isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ) );
	$orderby  = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'title';
	if ( ! in_array( $orderby, array( 'title', 'brand', 'stock' ), true ) ) {
		$orderby = 'title';
	}

	$args = array(
		'status'   => 'publish',
		'paginate' => true,
		'page'     => $paged,
		'limit'    => $per_page,
		'orderby'  => 'title',
		'order'    => 'ASC',
	);
	if ( 'stock' === $orderby ) {
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = '_stock';
		$args['order']    = 'DESC';
	}

	if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$key          = 'product_cat' === $term->taxonomy ? 'category' : $term->taxonomy;
			$args[ $key ] = array( $term->slug );
		}
	}

	if ( is_search() ) {
		$matched_ids     = function_exists( 'sln_product_search_ids' ) ? sln_product_search_ids( get_search_query() ) : array();
		$args['include'] = $matched_ids;
		if ( ! $args['include'] ) {
			$args['include'] = array( 0 );
		}
	}

	if ( ! empty( $_GET['product_cat'] ) ) {
		$args['category'] = array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_cat'] ) );
	}
	if ( ! empty( $_GET['product_brand'] ) ) {
		$args['product_brand'] = array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_brand'] ) );
	}

	$tax_query = array();
	if ( ! empty( $_GET['attr'] ) && is_array( $_GET['attr'] ) ) {
		foreach ( wp_unslash( $_GET['attr'] ) as $taxonomy => $slugs ) {
			$taxonomy = sanitize_key( $taxonomy );
			if ( 0 !== strpos( $taxonomy, 'pa_' ) || ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
			$slugs = array_filter( array_map( 'sanitize_title', (array) $slugs ) );
			if ( $slugs ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $slugs,
				);
			}
		}
	}
	if ( $tax_query ) {
		$args['tax_query'] = $tax_query;
	}

	$stock_filters    = ! empty( $_GET['stock'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['stock'] ) ) : array();
	$wants_instock    = in_array( 'instock', $stock_filters, true );
	$wants_low        = in_array( 'low', $stock_filters, true );
	$wants_outofstock = in_array( 'outofstock', $stock_filters, true );
	if ( $stock_filters ) {
		$statuses = array();
		if ( $wants_instock || $wants_low ) {
			$statuses[] = 'instock';
		}
		if ( $wants_outofstock ) {
			$statuses[] = 'outofstock';
		}
		$args['stock_status'] = array_unique( $statuses );
	}

	$needs_all = ( 'brand' === $orderby ) || ( $wants_low && ! $wants_instock );
	if ( $needs_all ) {
		$args['limit'] = -1;
		$args['page']  = 1;
		unset( $args['paginate'] );
	}

	$result   = wc_get_products( $args );
	$products = is_object( $result ) ? $result->products : $result;
	$total    = is_object( $result ) ? (int) $result->total : count( $products );
	$max      = is_object( $result ) && $result->max_num_pages ? (int) $result->max_num_pages : 1;

	if ( $wants_low && ! $wants_instock ) {
		$products = array_values(
			array_filter(
				$products,
				function ( $product ) {
					return 'low' === sln_stock_state( $product );
				}
			)
		);
	}

	if ( 'brand' === $orderby ) {
		usort(
			$products,
			function ( $a, $b ) {
				$ba = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $a->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
				$bb = taxonomy_exists( 'product_brand' ) ? wc_get_product_terms( $b->get_id(), 'product_brand', array( 'fields' => 'names' ) ) : array();
				$ba = is_wp_error( $ba ) || ! $ba ? '' : $ba[0];
				$bb = is_wp_error( $bb ) || ! $bb ? '' : $bb[0];
				$cmp = strcasecmp( $ba, $bb );
				return $cmp ? $cmp : strcasecmp( $a->get_name(), $b->get_name() );
			}
		);
	}

	if ( $needs_all ) {
		$total    = count( $products );
		$max      = max( 1, (int) ceil( $total / $per_page ) );
		$products = array_slice( $products, ( $paged - 1 ) * $per_page, $per_page );
	}

	return array(
		'products'  => $products,
		'total'     => $total,
		'max_pages' => $max,
		'paged'     => $paged,
		'orderby'   => $orderby,
	);
}
