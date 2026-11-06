<?php
$link2 = sln_field( 'financing', 'hero', 'button2_link' );
if ( ! $link2 ) {
	$link2 = function_exists( 'sln_shop_url' ) ? sln_shop_url() : home_url( '/shop/' );
}
?>
<main class="sl-page sl-pad">
	<?php
	sln_breadcrumb(
		array(
			array( 'label' => sln_field( 'financing', 'hero', 'home_label' ), 'url' => home_url( '/' ) ),
			array( 'label' => sln_field( 'financing', 'hero', 'crumb' ), 'url' => '' ),
		)
	);
	?>
	<div class="sl-page-hero">
		<?php if ( sln_field( 'financing', 'hero', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'financing', 'hero', 'eyebrow' ) ); ?></div><?php endif; ?>
		<h1><?php echo esc_html( sln_field( 'financing', 'hero', 'heading' ) ); ?></h1>
		<?php if ( sln_field( 'financing', 'hero', 'body' ) ) : ?><p class="sl-lede"><?php echo esc_html( sln_field( 'financing', 'hero', 'body' ) ); ?></p><?php endif; ?>
		<div class="sl-actions">
			<?php if ( sln_field( 'financing', 'hero', 'button_text' ) ) : ?><a class="sl-btn" href="<?php echo esc_url( sln_field( 'financing', 'hero', 'button_link' ) ); ?>"><?php echo esc_html( sln_field( 'financing', 'hero', 'button_text' ) ); ?></a><?php endif; ?>
			<?php if ( sln_field( 'financing', 'hero', 'button2_text' ) && $link2 ) : ?><a class="sl-btn sl-btn-ghost" href="<?php echo esc_url( $link2 ); ?>"><?php echo esc_html( sln_field( 'financing', 'hero', 'button2_text' ) ); ?></a><?php endif; ?>
		</div>
	</div>
</main>
