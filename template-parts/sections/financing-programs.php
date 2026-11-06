<?php
$cards = sln_repeater( 'financing', 'programs', 'cards' );
if ( ! $cards && ! sln_field( 'financing', 'programs', 'heading' ) ) {
	return;
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap">
	<?php if ( sln_field( 'financing', 'programs', 'heading' ) ) : ?><h2><?php echo esc_html( sln_field( 'financing', 'programs', 'heading' ) ); ?></h2><?php endif; ?>
	<?php if ( $cards ) : ?>
	<div class="sl-callouts">
		<?php foreach ( $cards as $card ) : ?>
			<div class="sl-callout">
				<div class="sl-mono sl-link"><?php echo esc_html( $card['number'] ); ?></div>
				<h3><?php echo esc_html( $card['title'] ); ?></h3>
				<?php if ( $card['desc'] ) : ?><p><?php echo esc_html( $card['desc'] ); ?></p><?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ( sln_field( 'financing', 'programs', 'disclaimer' ) ) : ?><p class="sl-note"><?php echo esc_html( sln_field( 'financing', 'programs', 'disclaimer' ) ); ?></p><?php endif; ?>
</div></section>
