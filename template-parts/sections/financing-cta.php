<?php
if ( ! sln_field( 'financing', 'cta', 'heading' ) && ! sln_field( 'financing', 'cta', 'body' ) ) {
	return;
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap sl-panel">
	<?php if ( sln_field( 'financing', 'cta', 'heading' ) ) : ?><h2><?php echo esc_html( sln_field( 'financing', 'cta', 'heading' ) ); ?></h2><?php endif; ?>
	<?php if ( sln_field( 'financing', 'cta', 'body' ) ) : ?><p class="sl-lede"><?php echo esc_html( sln_field( 'financing', 'cta', 'body' ) ); ?></p><?php endif; ?>
	<?php if ( sln_field( 'financing', 'cta', 'button_text' ) ) : ?>
		<a class="sl-btn" href="<?php echo esc_url( sln_field( 'financing', 'cta', 'button_link' ) ); ?>"><?php echo esc_html( sln_field( 'financing', 'cta', 'button_text' ) ); ?></a>
	<?php endif; ?>
</div></section>
