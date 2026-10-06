<?php
global $product;
if ( ! is_a( $product, 'WC_Product' ) ) {
	$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;
}
if ( ! $product ) {
	return;
}
$groups = array();
foreach ( $product->get_attributes() as $attribute ) {
	if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
		continue;
	}
	$label = wc_attribute_label( $attribute->get_name() );
	if ( $attribute->is_taxonomy() ) {
		$names = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
		$value = is_wp_error( $names ) ? '' : implode( ', ', $names );
	} else {
		$value = implode( ', ', $attribute->get_options() );
	}
	if ( '' === $value ) {
		continue;
	}
	$group = sln_field( 'product', 'details', 'specs_heading' );
	if ( false !== strpos( $label, ':' ) ) {
		$parts = array_map( 'trim', explode( ':', $label, 2 ) );
		$group = $parts[0];
		$label = $parts[1];
	}
	$groups[ $group ][] = array( 'label' => $label, 'value' => $value );
}
if ( $product->get_weight() ) {
	$groups[ sln_field( 'product', 'details', 'specs_heading' ) ][] = array(
		'label' => sln_field( 'product', 'details', 'weight_label' ),
		'value' => $product->get_weight() . ' ' . get_option( 'woocommerce_weight_unit' ),
	);
}
$raw      = $product->get_description();
$features = array();
if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $raw, $matches ) ) {
	foreach ( $matches[1] as $item ) {
		$text = trim( wp_strip_all_tags( $item ) );
		if ( ! $text ) {
			continue;
		}
		$bits = preg_split( '/\s+[—–-]\s+/', $text, 2 );
		$features[] = array( 'title' => $bits[0], 'desc' => isset( $bits[1] ) ? $bits[1] : '' );
	}
}
$overview = $raw;
if ( $features ) {
	$overview = preg_replace( '/<(ul|ol)\b[^>]*>.*?<\/\1>/is', '', $overview );
}
$overview_text = trim( wp_strip_all_tags( (string) $overview ) );
$downloads = $product->get_downloads();
$sheet     = '';
foreach ( $downloads as $download ) {
	$sheet = $download->get_file();
	break;
}
$tabs = array();
if ( $overview_text ) {
	$tabs[] = array( 'id' => 'overview', 'label' => sln_field( 'product', 'details', 'overview_label' ) );
}
if ( $groups ) {
	$tabs[] = array( 'id' => 'specs', 'label' => sln_field( 'product', 'details', 'specs_label' ) );
}
if ( $features ) {
	$tabs[] = array( 'id' => 'features', 'label' => sln_field( 'product', 'details', 'features_label' ) );
}
if ( $downloads ) {
	$tabs[] = array( 'id' => 'docs', 'label' => sln_field( 'product', 'details', 'docs_label' ) );
}
if ( ! $tabs ) {
	return;
}
?>
<section class="sl-details sl-pad">
	<div class="sl-tabs" data-sl-tabs>
		<?php foreach ( $tabs as $i => $tab ) : ?>
			<a href="#<?php echo esc_attr( $tab['id'] ); ?>" class="<?php echo 0 === $i ? 'is-on' : ''; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php if ( $overview_text ) : ?>
		<section id="overview" class="sl-detail-block">
			<h2><?php echo esc_html( sln_field( 'product', 'details', 'description_heading' ) ); ?></h2>
			<div class="sl-prose"><?php echo wp_kses_post( apply_filters( 'the_content', $overview ) ); ?></div>
		</section>
	<?php endif; ?>
	<?php if ( $groups ) : ?>
		<section id="specs" class="sl-detail-block">
			<div class="sl-head">
				<h2><?php echo esc_html( sln_field( 'product', 'details', 'specs_heading' ) ); ?></h2>
				<?php if ( $sheet ) : ?><a class="sl-btn sl-btn-ghost" href="<?php echo esc_url( $sheet ); ?>"><?php echo esc_html( sln_field( 'product', 'details', 'download_text' ) ); ?></a><?php endif; ?>
			</div>
			<div class="sl-spec-grid">
				<?php foreach ( $groups as $name => $rows ) : ?>
					<div class="sl-spec-card">
						<div class="sl-spec-title"><?php echo esc_html( $name ); ?></div>
						<?php foreach ( $rows as $i => $row ) : ?>
							<div class="sl-spec-row<?php echo $i % 2 ? ' is-alt' : ''; ?>"><span><?php echo esc_html( $row['label'] ); ?></span><span><?php echo esc_html( $row['value'] ); ?></span></div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
	<?php if ( $features ) : ?>
		<section id="features" class="sl-detail-block">
			<h2><?php echo esc_html( sln_field( 'product', 'details', 'features_heading' ) ); ?></h2>
			<div class="sl-feature-grid">
				<?php foreach ( $features as $feature ) : ?>
					<div><strong><?php echo esc_html( $feature['title'] ); ?></strong><?php if ( $feature['desc'] ) : ?><p><?php echo esc_html( $feature['desc'] ); ?></p><?php endif; ?></div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
	<?php if ( $downloads ) : ?>
		<section id="docs" class="sl-detail-block">
			<h2><?php echo esc_html( sln_field( 'product', 'details', 'docs_heading' ) ); ?></h2>
			<div class="sl-docs">
				<?php foreach ( $downloads as $download ) : ?>
					<a href="<?php echo esc_url( $download->get_file() ); ?>"><span><?php echo esc_html( $download->get_name() ); ?></span><span class="sl-mono">↓</span></a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>
</section>
