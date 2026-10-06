<?php
$slides = sln_repeater( 'home', 'hero', 'slides' );
$rows   = sln_repeater( 'home', 'hero', 'highlights' );
if ( ! $slides ) {
	return;
}
?>
<section class="sl-hero sl-pad" data-sl-slideshow>
	<div class="sl-hero-grid">
		<?php foreach ( $slides as $i => $slide ) : ?>
			<div class="sl-slide<?php echo 0 === $i ? ' is-active' : ''; ?> is-<?php echo esc_attr( $slide['layout'] ); ?> is-align-<?php echo esc_attr( $slide['text_align'] ); ?>">
				<div class="sl-hero-copy">
					<?php if ( $slide['eyebrow'] ) : ?><div class="sl-eyebrow"><?php echo esc_html( $slide['eyebrow'] ); ?></div><?php endif; ?>
					<?php if ( 0 === $i ) : ?>
						<h1><?php echo esc_html( $slide['heading'] ); ?></h1>
					<?php else : ?>
						<h2><?php echo esc_html( $slide['heading'] ); ?></h2>
					<?php endif; ?>
					<?php if ( $slide['body'] ) : ?><p><?php echo esc_html( $slide['body'] ); ?></p><?php endif; ?>
					<div class="sl-actions">
						<?php if ( $slide['button_text'] ) : ?><a class="sl-btn" href="<?php echo esc_url( $slide['button_link'] ); ?>"><?php echo esc_html( $slide['button_text'] ); ?></a><?php endif; ?>
						<?php if ( $slide['button2_text'] ) : ?><a class="sl-btn sl-btn-ghost" href="<?php echo esc_url( $slide['button2_link'] ); ?>"><?php echo esc_html( $slide['button2_text'] ); ?></a><?php endif; ?>
					</div>
				</div>
				<div class="sl-hero-visual">
					<div class="sl-hero-photo">
						<?php if ( $slide['image'] ) : ?>
							<img src="<?php echo esc_url( $slide['image'] ); ?>" alt="<?php echo esc_attr( $slide['heading'] ); ?>" />
						<?php else : ?>
							<?php sln_placeholder( $slide['image_label'] ); ?>
						<?php endif; ?>
					</div>
					<?php if ( 'split' === $slide['layout'] && ( $slide['highlight_label'] || $rows ) ) : ?>
						<div class="sl-live">
							<div class="sl-live-head"><span><?php echo esc_html( $slide['highlight_label'] ); ?></span><?php if ( $slide['highlight_badge'] ) : ?><span class="is-ok">● <?php echo esc_html( $slide['highlight_badge'] ); ?></span><?php endif; ?></div>
							<?php foreach ( $rows as $row ) : ?>
								<div class="sl-live-row">
									<div><span><?php echo esc_html( $row['name'] ); ?></span><span class="sl-mono"><?php echo esc_html( $row['qty'] ); ?></span></div>
									<div class="sl-bar"><span class="is-<?php echo esc_attr( $row['tone'] ); ?>" style="width:<?php echo max( 0, min( 100, (int) $row['percent'] ) ); ?>%"></span></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ( count( $slides ) > 1 ) : ?>
		<div class="sl-dots">
			<button type="button" class="sl-prev" aria-label="Previous">‹</button>
			<?php foreach ( $slides as $i => $slide ) : ?>
				<button type="button" class="sl-dot<?php echo 0 === $i ? ' is-active' : ''; ?>" aria-label="<?php echo esc_attr( sprintf( 'Slide %d', $i + 1 ) ); ?>"></button>
			<?php endforeach; ?>
			<button type="button" class="sl-next" aria-label="Next">›</button>
		</div>
	<?php endif; ?>
</section>
