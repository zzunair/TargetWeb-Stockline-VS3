<?php
?>
<main class="sl-page sl-pad">
	<?php
	sln_breadcrumb( array(
		array( 'label' => sln_field( 'contact', 'intro', 'home_label' ), 'url' => home_url( '/' ) ),
		array( 'label' => sln_field( 'contact', 'intro', 'crumb' ), 'url' => '' ),
	) );
	?>
	<?php if ( sln_field( 'contact', 'intro', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'contact', 'intro', 'eyebrow' ) ); ?></div><?php endif; ?>
	<h1><?php echo esc_html( sln_field( 'contact', 'intro', 'heading' ) ); ?></h1>
</main>
