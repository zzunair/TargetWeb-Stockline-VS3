<?php
?>
<section class="sl-section sl-pad" id="sl-service-form"><div class="sl-wrap sl-inquiry">
	<div>
		<h2><?php echo esc_html( sln_field( 'service', 'request', 'heading' ) ); ?></h2>
		<?php if ( sln_field( 'service', 'request', 'body' ) ) : ?><p><?php echo esc_html( sln_field( 'service', 'request', 'body' ) ); ?></p><?php endif; ?>
	</div>
	<form class="sl-form" data-sl-form data-success="<?php echo esc_attr( sln_field( 'service', 'request', 'success' ) ); ?>">
		<input type="text" name="name" placeholder="<?php echo esc_attr( sln_field( 'service', 'request', 'name_ph' ) ); ?>" />
		<input type="text" name="contact" placeholder="<?php echo esc_attr( sln_field( 'service', 'request', 'contact_ph' ) ); ?>" />
		<input type="text" name="model" placeholder="<?php echo esc_attr( sln_field( 'service', 'request', 'model_ph' ) ); ?>" />
		<textarea name="message" rows="3" placeholder="<?php echo esc_attr( sln_field( 'service', 'request', 'message_ph' ) ); ?>"></textarea>
		<button type="submit" class="sl-btn"><?php echo esc_html( sln_field( 'service', 'request', 'button_text' ) ); ?></button>
		<p class="sl-form-success" hidden></p>
	</form>
</div></section>
