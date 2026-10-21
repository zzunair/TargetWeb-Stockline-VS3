<?php
/** Shop filters. Real GET filtering. */
if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}
$selected_cats   = ! empty( $_GET['product_cat'] ) ? array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_cat'] ) ) : array();
$selected_brands = ! empty( $_GET['product_brand'] ) ? array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_brand'] ) ) : array();
$selected_stock  = ! empty( $_GET['stock'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['stock'] ) ) : array();
$selected_attr   = array();
if ( ! empty( $_GET['attr'] ) && is_array( $_GET['attr'] ) ) {
	foreach ( wp_unslash( $_GET['attr'] ) as $tax => $slugs ) {
		$selected_attr[ sanitize_key( $tax ) ] = array_map( 'sanitize_title', (array) $slugs );
	}
}

$load = function ( $taxonomy ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}
	$args = array( 'taxonomy' => $taxonomy, 'hide_empty' => false );
	if ( 'product_cat' === $taxonomy ) {
		$args['exclude'] = array( (int) get_option( 'default_product_cat' ) );
	}
	$terms = get_terms( $args );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	$out = array();
	foreach ( $terms as $term ) {
		$count = sln_term_live_count( $taxonomy, $term->slug );
		if ( $count ) {
			$term->sln_count = $count;
			$out[]           = $term;
		}
	}
	return $out;
};
$cats   = $load( 'product_cat' );
$brands = $load( 'product_brand' );

$count_stock = function ( $status ) {
	$result = wc_get_products( array( 'status' => 'publish', 'stock_status' => $status, 'paginate' => true, 'limit' => 1 ) );
	return is_object( $result ) ? (int) $result->total : 0;
};
$low_count = 0;
foreach ( wc_get_products( array( 'status' => 'publish', 'stock_status' => 'instock', 'limit' => -1 ) ) as $product ) {
	if ( 'low' === sln_stock_state( $product ) ) {
		$low_count++;
	}
}
$shop    = isset( $GLOBALS['sln_shop'] ) ? $GLOBALS['sln_shop'] : array( 'total' => 0 );
$total   = isset( $shop['total'] ) ? (int) $shop['total'] : 0;
$action  = sln_shop_url();
?>
<aside class="sl-filters" id="sl-filters">
	<div class="sl-filters-head">
		<strong><?php echo esc_html( sln_field( 'shop', 'filters', 'heading' ) ); ?></strong>
		<a href="<?php echo esc_url( $action ); ?>"><?php echo esc_html( sln_field( 'shop', 'filters', 'clear_text' ) ); ?></a>
	</div>
	<form method="get" class="sl-filters-form" action="<?php echo esc_url( $action ); ?>">
		<?php if ( ! empty( $_GET['s'] ) ) : ?><input type="hidden" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" /><?php endif; ?>
		<?php if ( ! empty( $_GET['post_type'] ) ) : ?><input type="hidden" name="post_type" value="product" /><?php endif; ?>

		<?php if ( sln_field( 'shop', 'filters', 'show_availability' ) ) : ?>
			<details class="sl-filter" open>
				<summary><?php echo esc_html( sln_field( 'shop', 'filters', 'availability_label' ) ); ?><span>−</span></summary>
				<div>
					<?php
					$stock_opts = array(
						'instock'    => array( sln_field( 'shop', 'filters', 'instock_label' ), $count_stock( 'instock' ) ),
						'low'        => array( sln_field( 'shop', 'filters', 'lowstock_label' ), $low_count ),
						'outofstock' => array( sln_field( 'shop', 'filters', 'outofstock_label' ), $count_stock( 'outofstock' ) ),
					);
					foreach ( $stock_opts as $value => $opt ) :
						$on = in_array( $value, $selected_stock, true );
						?>
						<label class="<?php echo $on ? 'is-on' : ''; ?>">
							<input type="checkbox" name="stock[]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $on ); ?> />
							<span class="sl-tick" aria-hidden="true"><?php echo $on ? '✓' : ''; ?></span>
							<span><?php echo esc_html( $opt[0] ); ?></span>
							<span class="sl-mono"><?php echo (int) $opt[1]; ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</details>
		<?php endif; ?>

		<?php
		$groups = array();
		if ( sln_field( 'shop', 'filters', 'show_brand' ) && $brands ) {
			$groups[] = array( 'name' => sln_field( 'shop', 'filters', 'brand_label' ), 'field' => 'product_brand', 'terms' => $brands, 'selected' => $selected_brands );
		}
		if ( sln_field( 'shop', 'filters', 'show_category' ) && $cats ) {
			$groups[] = array( 'name' => sln_field( 'shop', 'filters', 'category_label' ), 'field' => 'product_cat', 'terms' => $cats, 'selected' => $selected_cats );
		}
		foreach ( $groups as $group ) :
			?>
			<details class="sl-filter" open>
				<summary><?php echo esc_html( $group['name'] ); ?><span>−</span></summary>
				<div>
					<?php foreach ( $group['terms'] as $term ) : $on = in_array( $term->slug, $group['selected'], true ); ?>
						<label class="<?php echo $on ? 'is-on' : ''; ?>">
							<input type="checkbox" name="<?php echo esc_attr( $group['field'] ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( $on ); ?> />
							<span class="sl-tick" aria-hidden="true"><?php echo $on ? '✓' : ''; ?></span>
							<span><?php echo esc_html( $term->name ); ?></span>
							<span class="sl-mono"><?php echo (int) $term->sln_count; ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</details>
		<?php endforeach; ?>

		<?php
		if ( sln_field( 'shop', 'filters', 'show_attributes' ) && function_exists( 'wc_get_attribute_taxonomies' ) ) :
			foreach ( wc_get_attribute_taxonomies() as $attribute ) :
				$tax   = wc_attribute_taxonomy_name( $attribute->attribute_name );
				$terms = $load( $tax );
				if ( ! $terms ) {
					continue;
				}
				$picked = isset( $selected_attr[ $tax ] ) ? $selected_attr[ $tax ] : array();
				?>
				<details class="sl-filter" open>
					<summary><?php echo esc_html( $attribute->attribute_label ); ?><span>−</span></summary>
					<div>
						<?php foreach ( $terms as $term ) : $on = in_array( $term->slug, $picked, true ); ?>
							<label class="<?php echo $on ? 'is-on' : ''; ?>">
								<input type="checkbox" name="attr[<?php echo esc_attr( $tax ); ?>][]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( $on ); ?> />
								<span class="sl-tick" aria-hidden="true"><?php echo $on ? '✓' : ''; ?></span>
								<span><?php echo esc_html( $term->name ); ?></span>
								<span class="sl-mono"><?php echo (int) $term->sln_count; ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</details>
			<?php endforeach; ?>
		<?php endif; ?>

		<button type="submit" class="sl-btn sl-filters-apply"><?php echo esc_html( str_replace( '{count}', number_format_i18n( $total ), sln_field( 'shop', 'filters', 'apply_text' ) ) ); ?></button>
	</form>
</aside>
