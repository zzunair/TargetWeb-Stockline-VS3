/* global jQuery, wp */
( function ( $, api ) {
	'use strict';
	if ( ! api || ! api.controlConstructor ) {
		return;
	}

	api.controlConstructor['sln-section-order'] = api.Control.extend( {
		ready: function () {
			var control = this;
			var $list = control.container.find( '.sln-section-order' );
			function sync() {
				var rows = [];
				$list.find( '.sln-section-order-item' ).each( function () {
					var $item = $( this );
					rows.push( { id: $item.data( 'id' ), enabled: $item.find( '.sln-section-enabled' ).is( ':checked' ) } );
				} );
				var value = JSON.stringify( rows );
				$list.siblings( '.sln-section-order-value' ).val( value );
				control.setting.set( value );
			}
			$list.sortable( { handle: '.sln-drag-handle', axis: 'y', update: sync } );
			$list.on( 'change', '.sln-section-enabled', sync );
		}
	} );

	api.controlConstructor['sln-repeater'] = api.Control.extend( {
		ready: function () {
			var control = this;
			var $root = control.container.find( '.sln-repeater' );
			var $hidden = control.container.find( '.sln-repeater-value' );
			var $template = control.container.find( '.sln-repeater-template' );
			var titleField = $root.data( 'title-field' );
			var minBlocks = parseInt( $root.data( 'min' ), 10 ) || 0;

			function fieldValue( $field ) {
				return $field.is( ':checkbox' ) ? $field.is( ':checked' ) : $field.val();
			}
			function updateTitle( $block ) {
				if ( ! titleField ) { return; }
				var $field = $block.find( '[data-field="' + titleField + '"]' );
				if ( ! $field.length ) { return; }
				var text = $field.is( 'select' ) ? $field.find( 'option:selected' ).text() : $field.val();
				$block.find( '.sln-repeater-block-title' ).first().text( text || 'Block' );
			}
			function updateRemove() {
				var count = $root.find( '> .sln-repeater-block' ).length;
				$root.find( '.sln-repeater-remove' ).prop( 'disabled', count <= minBlocks );
			}
			function sync() {
				var blocks = [];
				$root.find( '> .sln-repeater-block' ).each( function () {
					var block = {};
					$( this ).find( '.sln-repeater-block-body [data-field]' ).each( function () {
						block[ $( this ).data( 'field' ) ] = fieldValue( $( this ) );
					} );
					blocks.push( block );
				} );
				var value = JSON.stringify( blocks );
				$hidden.val( value );
				control.setting.set( value );
				updateRemove();
			}
			$root.sortable( { handle: '.sln-repeater-drag-handle', items: '> .sln-repeater-block', axis: 'y', update: sync } );
			control.container.on( 'click', '.sln-repeater-block-toggle', function () {
				$( this ).closest( '.sln-repeater-block' ).toggleClass( 'is-collapsed' );
			} );
			control.container.on( 'click', '.sln-repeater-remove', function ( e ) {
				e.preventDefault();
				if ( $root.find( '> .sln-repeater-block' ).length <= minBlocks ) { return; }
				$( this ).closest( '.sln-repeater-block' ).remove();
				sync();
			} );
			control.container.on( 'click', '.sln-repeater-add', function ( e ) {
				e.preventDefault();
				var $clone = $( document.importNode( $template[ 0 ].content, true ) ).find( '.sln-repeater-block' );
				$root.append( $clone );
				updateTitle( $clone );
				sync();
			} );
			control.container.on( 'input change', '.sln-repeater-block-body [data-field]', function () {
				updateTitle( $( this ).closest( '.sln-repeater-block' ) );
				sync();
			} );
			control.container.on( 'click', '.sln-repeater-image-select', function ( e ) {
				e.preventDefault();
				var $wrap = $( this ).closest( '.sln-repeater-image' );
				var frame = wp.media( { title: 'Select image', multiple: false, library: { type: 'image' } } );
				frame.on( 'select', function () {
					var url = frame.state().get( 'selection' ).first().toJSON().url;
					$wrap.find( '[data-field]' ).val( url );
					$wrap.find( '.sln-repeater-image-preview' ).attr( 'src', url ).removeClass( 'sln-hidden' );
					$wrap.find( '.sln-repeater-image-remove' ).removeClass( 'sln-hidden' );
					$wrap.find( '.sln-repeater-image-select' ).text( 'Change image' );
					sync();
				} );
				frame.open();
			} );
			control.container.on( 'click', '.sln-repeater-image-remove', function ( e ) {
				e.preventDefault();
				var $wrap = $( this ).closest( '.sln-repeater-image' );
				$wrap.find( '[data-field]' ).val( '' );
				$wrap.find( '.sln-repeater-image-preview' ).addClass( 'sln-hidden' ).attr( 'src', '' );
				$wrap.find( '.sln-repeater-image-select' ).text( 'Select image' );
				$( this ).addClass( 'sln-hidden' );
				sync();
			} );
			updateRemove();
		}
	} );
} )( jQuery, wp.customize );
