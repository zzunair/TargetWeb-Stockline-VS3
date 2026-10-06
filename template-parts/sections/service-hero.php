<?php
$phone = sln_phone();
$link2 = sln_field( 'service', 'hero', 'button2_link' );
if ( ! $link2 && $phone ) {
	$link2 = sln_tel_href( $phone );
}
?>
<main class="sl-page sl-pad">
	<?php
	sln_breadcrumb(
		array(
			array( 'label' => sln_field( 'service', 'hero', 'home_label' ), 'url' => home_url( '/' ) ),
			array( 'label' => sln_field( 'service', 'hero', 'crumb' ), 'url' => '' ),
		)
	);
	?>
	<div class="sl-split sl-page-hero">
		<div>
			<?php if ( sln_field( 'service', 'hero', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'service', 'hero', 'eyebrow' ) ); ?></div><?php endif; ?>
			<h1><?php echo esc_html( sln_field( 'service', 'hero', 'heading' ) ); ?></h1>
			<?php if ( sln_field( 'service', 'hero', 'body' ) ) : ?><p class="sl-lede"><?php echo esc_html( sln_field( 'service', 'hero', 'body' ) ); ?></p><?php endif; ?>
			<div class="sl-actions">
				<?php if ( sln_field( 'service', 'hero', 'button_text' ) ) : ?><a class="sl-btn" href="<?php echo esc_url( sln_field( 'service', 'hero', 'button_link' ) ); ?>"><?php echo esc_html( sln_field( 'service', 'hero', 'button_text' ) ); ?></a><?php endif; ?>
				<?php if ( sln_field( 'service', 'hero', 'button2_text' ) && $link2 ) : ?><a class="sl-btn sl-btn-ghost" href="<?php echo esc_url( $link2 ); ?>"><?php echo esc_html( sln_field( 'service', 'hero', 'button2_text' ) ); ?></a><?php endif; ?>
			</div>
		</div>
		<div class="sl-frame">
			<?php if ( sln_field( 'service', 'hero', 'image' ) ) : ?>
				<img src="<?php echo esc_url( sln_field( 'service', 'hero', 'image' ) ); ?>" alt="<?php echo esc_attr( sln_field( 'service', 'hero', 'heading' ) ); ?>" />
			<?php else : ?>
				<?php sln_placeholder( sln_field( 'service', 'hero', 'image_label' ) ); ?>
			<?php endif; ?>
		</div>
	</div>
</main>
