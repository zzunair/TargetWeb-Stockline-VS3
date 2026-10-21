( function () {
	'use strict';

	function on( selector, event, handler ) {
		document.addEventListener( event, function ( e ) {
			var node = e.target.closest ? e.target.closest( selector ) : null;
			if ( node ) {
				handler( e, node );
			}
		} );
	}

	var sheet = document.getElementById( 'sl-nav-sheet' );
	var menuBtn = document.querySelector( '.sl-menu-open' );
	function setSheet( open ) {
		if ( ! sheet ) { return; }
		sheet.hidden = ! open;
		if ( menuBtn ) { menuBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' ); }
	}
	if ( menuBtn ) {
		menuBtn.addEventListener( 'click', function () { setSheet( sheet.hidden ); } );
	}
	on( '[data-close-sheet]', 'click', function () { setSheet( false ); } );
	if ( sheet ) {
		sheet.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) { setSheet( false ); }
		} );
	}

	var search = document.getElementById( 'sl-search' );
	var searchOpen = document.querySelector( '.sl-search-open' );
	var searchInput = search ? search.querySelector( 'input[name="s"]' ) : null;
	var suggest = search ? search.querySelector( '.sl-suggest' ) : null;
	function setSearch( open ) {
		if ( ! search ) { return; }
		search.hidden = ! open;
		if ( searchOpen ) { searchOpen.setAttribute( 'aria-expanded', open ? 'true' : 'false' ); }
		if ( open && searchInput ) { window.setTimeout( function () { searchInput.focus(); }, 30 ); }
		if ( ! open && suggest ) { suggest.hidden = true; }
	}
	if ( searchOpen ) { searchOpen.addEventListener( 'click', function () { setSearch( true ); } ); }
	on( '[data-close-search]', 'click', function () { setSearch( false ); } );
	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === '/' && document.activeElement && ! /input|textarea|select/i.test( document.activeElement.tagName ) ) {
			e.preventDefault();
			setSearch( true );
		}
		if ( e.key === 'Escape' ) {
			setSearch( false );
			setSheet( false );
			document.querySelectorAll( '.sl-modal' ).forEach( function ( modal ) { modal.hidden = true; } );
			var zoom = document.getElementById( 'sl-zoom-view' );
			if ( zoom ) { zoom.hidden = true; }
			var filters = document.getElementById( 'sl-filters' );
			if ( filters ) { filters.classList.remove( 'is-open' ); }
		}
	} );

	function escapeHtml( str ) {
		var div = document.createElement( 'div' );
		div.textContent = str == null ? '' : String( str );
		return div.innerHTML;
	}
	if ( searchInput && suggest && window.slnSearch && window.slnSearch.suggestions ) {
		var timer = null;
		var current = '';
		searchInput.addEventListener( 'input', function () {
			var term = searchInput.value.trim();
			current = term;
			clearTimeout( timer );
			if ( term.length < 2 ) { suggest.hidden = true; return; }
			timer = setTimeout( function () {
				var url = window.slnSearch.ajaxUrl + '?action=sln_search_suggest&nonce=' + encodeURIComponent( window.slnSearch.nonce ) + '&term=' + encodeURIComponent( term );
				fetch( url ).then( function ( r ) { return r.json(); } ).then( function ( items ) {
					if ( term !== current ) { return; }
					if ( ! items.length ) {
						suggest.innerHTML = '<p>' + escapeHtml( window.slnSearch.empty ) + '</p>';
					} else {
						suggest.innerHTML = items.map( function ( item ) {
							var img = item.image ? '<img src="' + escapeHtml( item.image ) + '" alt="" />' : '<span class="sl-suggest-ph"></span>';
							var meta = [ item.price, item.badge ].filter( Boolean ).join( ' · ' );
							return '<a href="' + escapeHtml( item.url ) + '">' + img + '<span><strong>' + escapeHtml( item.name ) + '</strong><br><span class="sl-mono">' + escapeHtml( meta ) + '</span></span></a>';
						} ).join( '' ) + '<button type="button" class="sl-suggest-all">' + escapeHtml( window.slnSearch.viewAll ) + '</button>';
					}
					suggest.hidden = false;
					var all = suggest.querySelector( '.sl-suggest-all' );
					if ( all ) { all.addEventListener( 'click', function () { searchInput.form.submit(); } ); }
				} ).catch( function () {} );
			}, 250 );
		} );
	}

	document.querySelectorAll( '[data-sl-slideshow]' ).forEach( function ( root ) {
		var slides = root.querySelectorAll( '.sl-slide' );
		var dots = root.querySelectorAll( '.sl-dot' );
		if ( slides.length < 2 ) { return; }
		var index = 0;
		var timer = null;
		function show( i ) {
			index = ( i + slides.length ) % slides.length;
			slides.forEach( function ( slide, n ) { slide.classList.toggle( 'is-active', n === index ); } );
			dots.forEach( function ( dot, n ) { dot.classList.toggle( 'is-active', n === index ); } );
		}
		function start() { timer = setInterval( function () { show( index + 1 ); }, 6000 ); }
		var prev = root.querySelector( '.sl-prev' );
		var next = root.querySelector( '.sl-next' );
		if ( prev ) { prev.addEventListener( 'click', function () { clearInterval( timer ); show( index - 1 ); } ); }
		if ( next ) { next.addEventListener( 'click', function () { clearInterval( timer ); show( index + 1 ); } ); }
		dots.forEach( function ( dot, n ) { dot.addEventListener( 'click', function () { clearInterval( timer ); show( n ); } ); } );
		start();
	} );

	document.querySelectorAll( '[data-sl-form]' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			if ( form.hasAttribute( 'data-sl-contact' ) ) {
				var error = form.querySelector( '[data-contact-error]' );
				var success = form.querySelector( '.sl-form-success' );
				var submit = form.querySelector( '[type="submit"]' );
				if ( error ) { error.hidden = true; }
				if ( ! window.slnContactForm ) {
					if ( error ) { error.hidden = false; }
					return;
				}
				submit.disabled = true;
				var formData = new FormData( form );
				formData.append( 'action', 'sln_contact_message' );
				formData.append( 'nonce', window.slnContactForm.nonce );
				window.fetch( window.slnContactForm.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					body: formData
				} )
					.then( function ( response ) {
						return response.json().then( function ( payload ) {
							if ( ! response.ok || ! payload.success ) {
								throw new Error( 'Request failed' );
							}
						} );
					} )
					.then( function () {
						form.querySelectorAll( 'input, textarea, select, button, h2, [data-contact-error]' ).forEach( function ( el ) {
							if ( ! el.classList.contains( 'sl-form-success' ) ) { el.hidden = true; }
						} );
						if ( success ) {
							success.hidden = false;
							success.textContent = form.getAttribute( 'data-success' ) || '';
						}
					} )
					.catch( function () {
						if ( error ) { error.hidden = false; }
						submit.disabled = false;
					} );
				return;
			}
			var success = form.querySelector( '.sl-form-success' );
			var template = form.getAttribute( 'data-success' ) || '';
			var nameInput = form.querySelector( '[name="name"], .sl-reserve-name' );
			var name = nameInput && nameInput.value.trim() ? ', ' + nameInput.value.trim() : '';
			if ( success ) {
				var heading = success.getAttribute( 'data-heading' );
				success.hidden = false;
				success.textContent = ( heading ? heading + ' ' : '' ) + template.replace( '{name}', name );
			}
			Array.prototype.forEach.call( form.elements, function ( el ) {
				if ( el !== success && el.tagName !== 'P' ) { el.hidden = el.type !== 'submit' ? false : el.hidden; }
			} );
			form.querySelectorAll( 'input, textarea, select, button, h2, label' ).forEach( function ( el ) {
				if ( ! el.classList.contains( 'sl-form-success' ) ) { el.hidden = true; }
			} );
			var resetLabel = success ? success.getAttribute( 'data-reset' ) : '';
			if ( resetLabel ) {
				var reset = document.createElement( 'button' );
				reset.type = 'button';
				reset.className = 'sl-btn sl-btn-ghost';
				reset.textContent = resetLabel;
				reset.addEventListener( 'click', function () { window.location.reload(); } );
				form.appendChild( reset );
			}
		} );
	} );
	document.querySelectorAll( '.sl-news' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function ( e ) { e.preventDefault(); } );
	} );

	var request = document.getElementById( 'sl-request' );
	on( '.sl-request-open', 'click', function ( e, btn ) {
		e.preventDefault();
		if ( request ) { request.hidden = false; btn.setAttribute( 'aria-expanded', 'true' ); }
	} );
	on( '[data-close-request]', 'click', function () {
		if ( request ) { request.hidden = true; }
		document.querySelectorAll( '.sl-request-open' ).forEach( function ( btn ) { btn.setAttribute( 'aria-expanded', 'false' ); } );
	} );

	var filters = document.getElementById( 'sl-filters' );
	on( '.sl-filters-open', 'click', function () { if ( filters ) { filters.classList.add( 'is-open' ); } } );
	document.querySelectorAll( '.sl-filters-form' ).forEach( function ( form ) {
		form.addEventListener( 'change', function () {
			if ( window.matchMedia( '(max-width: 860px)' ).matches ) { return; }
			form.submit();
		} );
	} );

	document.querySelectorAll( '[data-sl-gallery]' ).forEach( function ( gallery ) {
		gallery.addEventListener( 'click', function ( e ) {
			var thumb = e.target.closest( 'button' );
			if ( ! thumb || ! gallery.contains( thumb ) ) { return; }
			var full = thumb.getAttribute( 'data-full' );
			var img = document.getElementById( 'sl-gallery-img' ) || document.querySelector( '.sl-gallery-main img' );
			if ( img && full ) {
				img.removeAttribute( 'srcset' );
				img.removeAttribute( 'sizes' );
				img.src = full;
			}
			gallery.querySelectorAll( 'button' ).forEach( function ( btn ) {
				btn.classList.toggle( 'is-on', btn === thumb );
			} );
		} );
	} );
	on( '[data-sl-zoom]', 'click', function () {
		var view = document.getElementById( 'sl-zoom-view' );
		var img = document.getElementById( 'sl-gallery-img' );
		if ( view && img ) { view.querySelector( 'img' ).src = img.src; view.hidden = false; }
	} );
	on( '[data-close-zoom]', 'click', function () {
		var view = document.getElementById( 'sl-zoom-view' );
		if ( view ) { view.hidden = true; }
	} );

	var sticky = document.querySelector( '[data-sl-sticky]' );
	if ( sticky ) {
		window.addEventListener( 'scroll', function () { sticky.hidden = window.scrollY < 560; }, { passive: true } );
	}

	document.querySelectorAll( '[data-sl-tabs] a' ).forEach( function ( link ) {
		link.addEventListener( 'click', function () {
			link.parentNode.querySelectorAll( 'a' ).forEach( function ( item ) { item.classList.toggle( 'is-on', item === link ); } );
		} );
	} );

	var view = document.querySelector( '[data-sl-view]' );
	var catalog = document.querySelector( '[data-sl-catalog]' );
	if ( view && catalog ) {
		var saved = window.sessionStorage.getItem( 'sln_view' ) || 'grid';
		function applyView( mode ) {
			catalog.classList.toggle( 'is-list', mode === 'list' );
			catalog.querySelectorAll( '.sl-card' ).forEach( function ( card ) { card.classList.toggle( 'is-list', mode === 'list' ); } );
			view.querySelectorAll( 'button' ).forEach( function ( btn ) { btn.classList.toggle( 'is-on', btn.getAttribute( 'data-view' ) === mode ); } );
			window.sessionStorage.setItem( 'sln_view', mode );
		}
		applyView( saved );
		view.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( 'button' );
			if ( btn ) { applyView( btn.getAttribute( 'data-view' ) ); }
		} );
	}

	var bar = document.getElementById( 'sl-compare-bar' );
	if ( bar ) {
		var key = 'sln_compare_items';
		var max = parseInt( bar.getAttribute( 'data-max' ), 10 ) || 4;
		var barText = bar.getAttribute( 'data-bar-text' ) || '{count} selected';
		var maxText = bar.getAttribute( 'data-max-text' ) || '';
		var viewText = bar.getAttribute( 'data-view-text' ) || 'View Specs';
		var textEl = document.getElementById( 'sl-compare-bar-text' );
		var modal = document.getElementById( 'sl-compare-modal' );
		var body = document.getElementById( 'sl-compare-modal-body' );
		function read() {
			try { return JSON.parse( window.localStorage.getItem( key ) ) || {}; } catch ( err ) { return {}; }
		}
		function write( store ) { window.localStorage.setItem( key, JSON.stringify( store ) ); }
		function snap( box ) {
			var specs = [];
			try { specs = JSON.parse( box.getAttribute( 'data-specs' ) || '[]' ); } catch ( err ) { specs = []; }
			return {
				name: box.getAttribute( 'data-name' ) || '',
				brand: box.getAttribute( 'data-brand' ) || '',
				model: box.getAttribute( 'data-model' ) || '',
				price: box.getAttribute( 'data-price' ) || '',
				image: box.getAttribute( 'data-image' ) || '',
				url: box.getAttribute( 'data-url' ) || '',
				badge: box.getAttribute( 'data-badge' ) || '',
				qty: box.getAttribute( 'data-qty' ) || '',
				description: box.getAttribute( 'data-description' ) || '',
				specs: Array.isArray( specs ) ? specs : []
			};
		}
		function refresh() {
			var store = read();
			document.querySelectorAll( '.sl-compare-box' ).forEach( function ( box ) {
				var id = box.getAttribute( 'data-id' );
				box.checked = !! store[ id ];
				box.disabled = Object.keys( store ).length >= max && ! box.checked;
				if ( store[ id ] ) { store[ id ] = snap( box ); }
			} );
			write( store );
			var count = Object.keys( store ).length;
			bar.hidden = count < 1;
			if ( textEl ) { textEl.textContent = barText.replace( '{count}', String( count ) ); }
			return store;
		}
		function render( store ) {
			var ids = Object.keys( store );
			var labels = [];
			ids.forEach( function ( id ) {
				( store[ id ].specs || [] ).forEach( function ( spec ) {
					if ( spec && spec.label && labels.indexOf( spec.label ) === -1 ) { labels.push( spec.label ); }
				} );
			} );
			var html = '<table class="sl-compare-table"><thead><tr><th>Spec</th>';
			ids.forEach( function ( id ) {
				html += '<th>' + escapeHtml( store[ id ].name ) + '<br><button type="button" data-remove="' + escapeHtml( id ) + '">×</button></th>';
			} );
			html += '</tr></thead><tbody>';
			function row( label, values, bestIndex ) {
				html += '<tr><td>' + escapeHtml( label ) + '</td>';
				values.forEach( function ( value, index ) {
					html += '<td class="' + ( index === bestIndex ? 'is-best' : '' ) + '">' + escapeHtml( value || '—' ) + '</td>';
				} );
				html += '</tr>';
			}
			row( 'Brand', ids.map( function ( id ) { return store[ id ].brand; } ), -1 );
			if ( ids.some( function ( id ) { return store[ id ].price; } ) ) {
				row( 'Price', ids.map( function ( id ) { return store[ id ].price; } ), -1 );
			}
			labels.forEach( function ( label ) {
				var values = ids.map( function ( id ) {
					var found = ( store[ id ].specs || [] ).filter( function ( spec ) { return spec.label === label; } )[ 0 ];
					return found ? found.value : '';
				} );
				row( label, values, -1 );
			} );
			html += '<tr><td></td>';
			ids.forEach( function ( id ) {
				html += '<td><a class="sl-btn sl-btn-ghost" href="' + escapeHtml( store[ id ].url ) + '">' + escapeHtml( viewText ) + '</a></td>';
			} );
			html += '</tr></tbody></table>';
			body.innerHTML = html;
			body.querySelectorAll( '[data-remove]' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					var storeNow = read();
					delete storeNow[ btn.getAttribute( 'data-remove' ) ];
					write( storeNow );
					var next = refresh();
					if ( Object.keys( next ).length ) { render( next ); } else { modal.hidden = true; }
				} );
			} );
		}
		document.addEventListener( 'change', function ( e ) {
			if ( ! e.target.classList || ! e.target.classList.contains( 'sl-compare-box' ) ) { return; }
			var box = e.target;
			var store = read();
			var id = box.getAttribute( 'data-id' );
			if ( box.checked ) {
				if ( Object.keys( store ).length >= max ) {
					box.checked = false;
					window.alert( maxText.replace( '{max}', String( max ) ) );
					return;
				}
				store[ id ] = snap( box );
			} else {
				delete store[ id ];
			}
			write( store );
			refresh();
		} );
		document.getElementById( 'sl-compare-clear' ).addEventListener( 'click', function () { write( {} ); refresh(); } );
		document.getElementById( 'sl-compare-open' ).addEventListener( 'click', function () {
			var store = refresh();
			if ( ! Object.keys( store ).length ) { return; }
			render( store );
			modal.hidden = false;
		} );
		document.getElementById( 'sl-compare-modal-close' ).addEventListener( 'click', function () { modal.hidden = true; } );
		document.getElementById( 'sl-compare-modal-overlay' ).addEventListener( 'click', function () { modal.hidden = true; } );
		refresh();
	}

	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'added_to_cart', function ( e, fragments, hash, $button ) {
			if ( $button && $button.length ) {
				$button[ 0 ].classList.add( 'is-added' );
			}
		} );
	}
} )();
