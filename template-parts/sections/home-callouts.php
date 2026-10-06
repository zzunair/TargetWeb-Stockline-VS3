<?php
$items = sln_repeater( 'home', 'callouts', 'items' );
if ( ! $items ) {
	return;
}
?>
<section class="sl-section sl-pad">
	<div class="sl-wrap">
		<?php if ( sln_field( 'home', 'callouts', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'home', 'callouts', 'eyebrow' ) ); ?></div><?php endif; ?>
		<h2><?php echo esc_html( sln_field( 'home', 'callouts', 'heading' ) ); ?></h2>
		<div class="sl-callouts">
			<?php foreach ( $items as $item ) : ?>
				<a class="sl-callout" href="<?php echo esc_url( $item['link'] ? $item['link'] : home_url( '/contact/' ) ); ?>">
					<div class="sl-mono sl-link"><?php echo esc_html( $item['number'] ); ?></div>
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<?php if ( $item['desc'] ) : ?><p><?php echo esc_html( $item['desc'] ); ?></p><?php endif; ?>
					<?php if ( $item['cta'] ) : ?><span><?php echo esc_html( $item['cta'] ); ?> →</span><?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
