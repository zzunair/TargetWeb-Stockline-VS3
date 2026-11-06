<?php
$topics = sln_lines( sln_field( 'contact', 'form_info', 'topics' ) );
?>
<div class="sl-panel sl-form-card">
	<h2><?php echo esc_html( sln_field( 'contact', 'form_info', 'heading' ) ); ?></h2>
	<form class="sl-form" data-sl-form data-sl-contact data-success="<?php echo esc_attr( sln_field( 'contact', 'form_info', 'success' ) ); ?>">
		<input type="text" name="name" placeholder="<?php echo esc_attr( sln_field( 'contact', 'form_info', 'name_ph' ) ); ?>" />
		<input type="email" name="email" placeholder="<?php echo esc_attr( sln_field( 'contact', 'form_info', 'email_ph' ) ); ?>" />
		<?php if ( $topics ) : ?>
			<select name="topic">
				<option value=""><?php echo esc_html( sln_field( 'contact', 'form_info', 'topic_label' ) ); ?></option>
				<?php foreach ( $topics as $topic ) : ?><option><?php echo esc_html( $topic ); ?></option><?php endforeach; ?>
			</select>
		<?php endif; ?>
		<input class="sl-mono-input" type="text" name="model" placeholder="<?php echo esc_attr( sln_field( 'contact', 'form_info', 'model_ph' ) ); ?>" />
		<textarea name="message" rows="4" placeholder="<?php echo esc_attr( sln_field( 'contact', 'form_info', 'message_ph' ) ); ?>"></textarea>
		<button type="submit" class="sl-btn"><?php echo esc_html( sln_field( 'contact', 'form_info', 'button_text' ) ); ?></button>
		<p class="sl-form-error" data-contact-error hidden><?php echo esc_html( sln_field( 'contact', 'form_info', 'error_text' ) ); ?></p>
		<p class="sl-form-success" hidden></p>
	</form>
</div>
