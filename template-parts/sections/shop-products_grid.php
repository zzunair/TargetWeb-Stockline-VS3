<?php
$shop     = isset( $GLOBALS['sln_shop'] ) ? $GLOBALS['sln_shop'] : array( 'products' => array(), 'total' => 0, 'max_pages' => 1, 'paged' => 1, 'orderby' => 'title' );
$products = $shop['products'];
$orderby  = isset( $shop['orderby'] ) ? $shop['orderby'] : 'title';
$sln_card = array(
	'link_text'  => sln_field( 'shop', 'products_grid', 'link_text' ),
	'price_note' => sln_field( 'shop', 'products_grid', 'price_note' ),
);
$chips = array();
if ( ! empty( $_GET['product_cat'] ) ) {
	foreach ( (array) wp_unslash( $_GET['product_cat'] ) as $slug ) {
		$term = get_term_by( 'slug', sanitize_title( $slug ), 'product_cat' );
		if ( $term ) {
			$chips[] = array( 'label' => $term->name, 'key' => 'product_cat', 'value' => $term->slug );
		}
	}
}
if ( ! empty( $_GET['product_brand'] ) ) {
	foreach ( (array) wp_unslash( $_GET['product_brand'] ) as $slug ) {
		$term = get_term_by( 'slug', sanitize_title( $slug ), 'product_brand' );
		if ( $term ) {
			$chips[] = array( 'label' => $term->name, 'key' => 'product_brand', 'value' => $term->slug );
		}
	}
}
?>
<div class="sl-catalog">
	<?php if ( $chips ) : ?>
		<div class="sl-active-chips">
			<?php foreach ( $chips as $chip ) : ?>
				<a href="<?php echo esc_url( sln_remove_query_value( $chip['key'], $chip['value'] ) ); ?>"><?php echo esc_html( $chip['label'] ); ?> <span>×</span></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<div class="sl-toolbar">
		<div>
			<button type="button" class="sl-btn sl-btn-ghost sl-filters-open"><?php echo esc_html( sln_field( 'shop', 'filters', 'mobile_open' ) ); ?></button>
			<span class="sl-mono sl-count"><?php echo esc_html( number_format_i18n( $shop['total'] ) . sln_field( 'shop', 'products_grid', 'count_suffix' ) ); ?></span>
		</div>
		<div class="sl-toolbar-tools">
			<form method="get">
				<?php
				foreach ( $_GET as $key => $value ) {
					if ( 'orderby' === $key ) {
						continue;
					}
					if ( is_array( $value ) ) {
						continue;
					}
					echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
				}
				?>
				<select name="orderby" onchange="this.form.submit()">
					<option value="title" <?php selected( $orderby, 'title' ); ?>><?php echo esc_html( sln_field( 'shop', 'products_grid', 'sort_name' ) ); ?></option>
					<option value="brand" <?php selected( $orderby, 'brand' ); ?>><?php echo esc_html( sln_field( 'shop', 'products_grid', 'sort_brand' ) ); ?></option>
					<option value="stock" <?php selected( $orderby, 'stock' ); ?>><?php echo esc_html( sln_field( 'shop', 'products_grid', 'sort_stock' ) ); ?></option>
				</select>
			</form>
			<div class="sl-view" data-sl-view>
				<button type="button" data-view="grid" class="is-on"><?php echo esc_html( sln_field( 'shop', 'products_grid', 'grid_label' ) ); ?></button>
				<button type="button" data-view="list"><?php echo esc_html( sln_field( 'shop', 'products_grid', 'list_label' ) ); ?></button>
			</div>
		</div>
	</div>

	<?php if ( $products ) : ?>
		<div class="sl-cards" data-sl-catalog>
			<?php foreach ( $products as $product ) : ?>
				<?php include get_theme_file_path( 'template-parts/product-card.php' ); ?>
			<?php endforeach; ?>
		</div>
	<?php elseif ( is_search() ) : ?>
		<p><?php echo esc_html( str_replace( '{term}', get_search_query( false ), sln_field( 'shop', 'products_grid', 'empty_search_text' ) ) ); ?></p>
	<?php else : ?>
		<p><?php echo esc_html( sln_field( 'shop', 'products_grid', 'empty_filter_text' ) ); ?></p>
	<?php endif; ?>

	<?php if ( $shop['max_pages'] > 1 ) : ?>
		<nav class="sl-pager" aria-label="Pages">
			<?php
			$paged = (int) $shop['paged'];
			$prev  = $paged > 1 ? add_query_arg( 'paged', $paged - 1 ) : '';
			$next  = $paged < (int) $shop['max_pages'] ? add_query_arg( 'paged', $paged + 1 ) : '';
			if ( $prev ) {
				echo '<a href="' . esc_url( $prev ) . '">‹</a>';
			}
			for ( $i = 1; $i <= (int) $shop['max_pages']; $i++ ) {
				$url = 1 === $i ? remove_query_arg( 'paged' ) : add_query_arg( 'paged', $i );
				echo '<a class="' . ( $i === $paged ? 'is-on' : '' ) . '" href="' . esc_url( $url ) . '">' . (int) $i . '</a>';
			}
			if ( $next ) {
				echo '<a href="' . esc_url( $next ) . '">›</a>';
				echo '<a class="sl-load" href="' . esc_url( $next ) . '">' . esc_html( sln_field( 'shop', 'products_grid', 'load_more' ) ) . '</a>';
			}
			?>
		</nav>
	<?php endif; ?>
</div>
