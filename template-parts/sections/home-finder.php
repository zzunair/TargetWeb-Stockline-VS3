<?php
if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}
$cats   = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
$brands = taxonomy_exists( 'product_brand' ) ? get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) ) : array();
if ( is_wp_error( $cats ) ) {
	$cats = array();
}
if ( is_wp_error( $brands ) ) {
	$brands = array();
}
$action = sln_field( 'home', 'finder', 'button_link' );
if ( ! $action ) {
	$action = sln_shop_url();
}
?>
<section class="sl-finder sl-pad">
	<form class="sl-finder-bar" method="get" action="<?php echo esc_url( $action ); ?>">
		<select name="product_cat">
			<option value=""><?php echo esc_html( sln_field( 'home', 'finder', 'category_label' ) ); ?></option>
			<?php foreach ( $cats as $term ) : ?>
				<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<select name="product_brand">
			<option value=""><?php echo esc_html( sln_field( 'home', 'finder', 'brand_label' ) ); ?></option>
			<?php foreach ( $brands as $term ) : ?>
				<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
			<?php endforeach; ?>
		</select>
		<label class="sl-switch">
			<input type="checkbox" name="stock[]" value="instock" />
			<span></span>
			<?php echo esc_html( sln_field( 'home', 'finder', 'stock_label' ) ); ?>
		</label>
		<button type="submit" class="sl-btn"><?php echo esc_html( sln_field( 'home', 'finder', 'button_text' ) ); ?></button>
	</form>
</section>
