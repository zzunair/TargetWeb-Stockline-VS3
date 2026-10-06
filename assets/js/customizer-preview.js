( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.customize ) {
		return;
	}
	var map = {
		sln_color_accent: '--sl-accent',
		sln_color_link: '--sl-link',
		sln_color_text: '--sl-text',
		sln_color_muted: '--sl-muted',
		sln_color_bg: '--sl-bg',
		sln_color_soft: '--sl-soft',
		sln_color_border: '--sl-border',
		sln_header_topbar_bg: '--sl-bar-bg',
		sln_header_topbar_text: '--sl-bar-text'
	};
	Object.keys( map ).forEach( function ( id ) {
		wp.customize( id, function ( value ) {
			value.bind( function ( next ) {
				document.documentElement.style.setProperty( map[ id ], next );
			} );
		} );
	} );
	wp.customize( 'sln_nav_text_style', function ( value ) {
		value.bind( function ( next ) {
			document.body.className = document.body.className.replace( /\bsl-nav-(?:none|uppercase|lowercase|capitalize)\b/g, '' );
			document.body.classList.add( 'sl-nav-' + next );
		} );
	} );
} )( window.wp );
