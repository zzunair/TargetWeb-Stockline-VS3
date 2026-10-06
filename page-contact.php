<?php
get_header();
$order = sln_get_section_order( 'contact' );
$pair  = array();
$flush = function () use ( &$pair ) {
	if ( ! $pair ) {
		return;
	}
	echo '<div class="sl-contact-grid sl-pad">';
	foreach ( $pair as $section_id ) {
		get_template_part( 'template-parts/sections/contact-' . $section_id );
	}
	echo '</div>';
	$pair = array();
};
foreach ( $order as $section_id ) {
	if ( 'intro' === $section_id ) {
		$flush();
		get_template_part( 'template-parts/sections/contact-intro' );
	} else {
		$pair[] = $section_id;
	}
}
$flush();
get_footer();
