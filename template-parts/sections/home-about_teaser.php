<?php
$body = sln_paragraphs( sln_field( 'home', 'about_teaser', 'body' ) );
?>
<section class="sl-section sl-pad">
	<div class="sl-wrap sl-split">
		<div class="sl-frame">
			<?php if ( sln_field( 'home', 'about_teaser', 'image' ) ) : ?>
				<img src="<?php echo esc_url( sln_field( 'home', 'about_teaser', 'image' ) ); ?>" alt="<?php echo esc_attr( sln_field( 'home', 'about_teaser', 'heading' ) ); ?>" />
			<?php else : ?>
				<?php sln_placeholder( sln_field( 'home', 'about_teaser', 'image_label' ) ); ?>
			<?php endif; ?>
		</div>
		<div>
			<?php if ( sln_field( 'home', 'about_teaser', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'home', 'about_teaser', 'eyebrow' ) ); ?></div><?php endif; ?>
			<h2><?php echo esc_html( sln_field( 'home', 'about_teaser', 'heading' ) ); ?></h2>
			<?php foreach ( $body as $paragraph ) : ?><p><?php echo esc_html( $paragraph ); ?></p><?php endforeach; ?>
			<?php if ( sln_field( 'home', 'about_teaser', 'link_text' ) ) : ?>
				<a class="sl-btn sl-btn-ghost" href="<?php echo esc_url( sln_field( 'home', 'about_teaser', 'link_href' ) ); ?>"><?php echo esc_html( sln_field( 'home', 'about_teaser', 'link_text' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
