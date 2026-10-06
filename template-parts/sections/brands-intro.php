<?php
?>
<main class="sl-page sl-pad">
	<?php
	sln_breadcrumb( array(
		array( 'label' => sln_field( 'brands', 'intro', 'home_label' ), 'url' => home_url( '/' ) ),
		array( 'label' => sln_field( 'brands', 'intro', 'crumb' ), 'url' => '' ),
	) );
	?>
	<?php if ( sln_field( 'brands', 'intro', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'brands', 'intro', 'eyebrow' ) ); ?></div><?php endif; ?>
	<h1><?php echo esc_html( sln_field( 'brands', 'intro', 'heading' ) ); ?></h1>
	<?php if ( sln_field( 'brands', 'intro', 'body' ) ) : ?><p class="sl-lede"><?php echo esc_html( sln_field( 'brands', 'intro', 'body' ) ); ?></p><?php endif; ?>
</main>
