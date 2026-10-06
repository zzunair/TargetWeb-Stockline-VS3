<?php
if ( ! sln_field( 'brands', 'note', 'text' ) && ! sln_field( 'brands', 'note', 'aside' ) ) {
	return;
}
?>
<section class="sl-section sl-pad"><div class="sl-wrap sl-note">
	<span><?php echo esc_html( sln_field( 'brands', 'note', 'text' ) ); ?></span>
	<span class="sl-mono"><?php echo esc_html( sln_field( 'brands', 'note', 'aside' ) ); ?></span>
</div></section>
