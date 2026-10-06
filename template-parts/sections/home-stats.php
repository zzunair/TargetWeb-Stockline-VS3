<?php
$items = sln_repeater( 'home', 'stats', 'items' );
if ( ! $items ) {
	return;
}
?>
<section class="sl-section sl-pad">
	<div class="sl-wrap sl-stats">
		<?php foreach ( $items as $item ) : ?>
			<div><div class="sl-stat"><?php echo esc_html( $item['value'] ); ?></div><div class="sl-eyebrow"><?php echo esc_html( $item['label'] ); ?></div></div>
		<?php endforeach; ?>
	</div>
</section>
