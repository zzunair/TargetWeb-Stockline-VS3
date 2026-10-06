<?php
$stats = sln_repeater( 'about', 'story', 'stats' );
?>
<section class="sl-section sl-pad"><div class="sl-wrap sl-split">
	<div>
		<h2><?php echo esc_html( sln_field( 'about', 'story', 'heading' ) ); ?></h2>
		<?php if ( sln_field( 'about', 'story', 'body1' ) ) : ?><p><?php echo esc_html( sln_field( 'about', 'story', 'body1' ) ); ?></p><?php endif; ?>
		<?php if ( sln_field( 'about', 'story', 'body2' ) ) : ?><p><?php echo esc_html( sln_field( 'about', 'story', 'body2' ) ); ?></p><?php endif; ?>
	</div>
	<?php if ( $stats ) : ?>
		<div class="sl-stat-grid">
			<?php foreach ( $stats as $stat ) : ?>
				<div><div class="sl-stat"><?php echo esc_html( $stat['value'] ); ?></div><div class="sl-eyebrow"><?php echo esc_html( $stat['label'] ); ?></div></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div></section>
