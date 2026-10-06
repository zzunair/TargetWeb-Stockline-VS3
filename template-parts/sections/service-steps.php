<?php
$steps = sln_repeater( 'service', 'steps', 'steps' );
if ( ! $steps ) {
	return;
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap">
	<h2><?php echo esc_html( sln_field( 'service', 'steps', 'heading' ) ); ?></h2>
	<div class="sl-steps">
		<?php foreach ( $steps as $i => $step ) : ?>
			<div class="<?php echo $i % 2 ? 'is-alt' : ''; ?>">
				<div class="sl-eyebrow"><?php echo esc_html( 'STEP ' . $step['number'] ); ?></div>
				<strong><?php echo esc_html( $step['title'] ); ?></strong>
				<?php if ( $step['desc'] ) : ?><p><?php echo esc_html( $step['desc'] ); ?></p><?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div></section>
