<?php
?>
<section class="sl-section sl-pad" id="contact">
	<div class="sl-wrap sl-inquiry">
		<div>
			<?php if ( sln_field( 'home', 'inquiry', 'eyebrow' ) ) : ?><div class="sl-eyebrow"><?php echo esc_html( sln_field( 'home', 'inquiry', 'eyebrow' ) ); ?></div><?php endif; ?>
			<h2><?php echo esc_html( sln_field( 'home', 'inquiry', 'heading' ) ); ?></h2>
			<?php if ( sln_field( 'home', 'inquiry', 'body' ) ) : ?><p><?php echo esc_html( sln_field( 'home', 'inquiry', 'body' ) ); ?></p><?php endif; ?>
		</div>
		<form class="sl-form" data-sl-form data-success="<?php echo esc_attr( sln_field( 'home', 'inquiry', 'success' ) ); ?>">
			<input type="text" name="name" placeholder="<?php echo esc_attr( sln_field( 'home', 'inquiry', 'name_ph' ) ); ?>" />
			<input type="text" name="contact" placeholder="<?php echo esc_attr( sln_field( 'home', 'inquiry', 'contact_ph' ) ); ?>" />
			<textarea name="message" rows="3" placeholder="<?php echo esc_attr( sln_field( 'home', 'inquiry', 'message_ph' ) ); ?>"></textarea>
			<button type="submit" class="sl-btn"><?php echo esc_html( sln_field( 'home', 'inquiry', 'button_text' ) ); ?></button>
			<p class="sl-form-success" hidden></p>
		</form>
	</div>
</section>
