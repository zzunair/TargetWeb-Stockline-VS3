<?php
$cards = sln_repeater( 'about', 'features', 'cards' );
if ( ! $cards ) {
	return;
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap">
	<h2><?php echo esc_html( sln_field( 'about', 'features', 'heading' ) ); ?></h2>
	<div class="sl-callouts">
		<?php foreach ( $cards as $card ) : ?>
			<div class="sl-callout"><h3><?php echo esc_html( $card['title'] ); ?></h3><p><?php echo esc_html( $card['desc'] ); ?></p></div>
		<?php endforeach; ?>
	</div>
</div></section>
