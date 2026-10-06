<?php
$cards = sln_repeater( 'service', 'service_cards', 'cards' );
if ( ! $cards ) {
	return;
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap sl-callouts">
	<?php foreach ( $cards as $card ) : ?>
		<div class="sl-callout">
			<div class="sl-mono sl-link"><?php echo esc_html( $card['number'] ); ?></div>
			<h3><?php echo esc_html( $card['title'] ); ?></h3>
			<?php if ( $card['desc'] ) : ?><p><?php echo esc_html( $card['desc'] ); ?></p><?php endif; ?>
		</div>
	<?php endforeach; ?>
</div></section>
