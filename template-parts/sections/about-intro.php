<?php
?>
<main class="sl-page sl-pad">
	<?php
	sln_breadcrumb( array(
		array( 'label' => sln_field( 'about', 'intro', 'home_label' ), 'url' => home_url( '/' ) ),
		array( 'label' => sln_field( 'about', 'intro', 'crumb' ), 'url' => '' ),
	) );
	?>
	<?php if ( sln_field( 'about', 'intro', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'about', 'intro', 'eyebrow' ) ); ?></div><?php endif; ?>
	<h1 class="sl-balance"><?php echo esc_html( sln_field( 'about', 'intro', 'heading' ) ); ?></h1>
	<div class="sl-frame sl-panorama">
		<?php if ( sln_field( 'about', 'intro', 'image' ) ) : ?>
			<img src="<?php echo esc_url( sln_field( 'about', 'intro', 'image' ) ); ?>" alt="<?php echo esc_attr( sln_field( 'about', 'intro', 'heading' ) ); ?>" />
		<?php else : ?>
			<?php sln_placeholder( sln_field( 'about', 'intro', 'image_label' ) ); ?>
		<?php endif; ?>
	</div>
</main>
