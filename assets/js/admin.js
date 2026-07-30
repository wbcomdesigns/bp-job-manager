/**
 * BuddyPress Job Manager: Admin JS (toast + confirm helpers).
 *
 * Skill Part 6 rules 10 + 12: no browser alert()/confirm() anywhere.
 * Expose window.bpjmToast() and window.bpjmConfirm() so both this
 * file and future Pro extensions can use the same feedback surface.
 *
 * @since 2.8.5
 */
( function ( $ ) {
	'use strict';

	var i18n = ( window.bpjmAdmin && window.bpjmAdmin.i18n ) || {};

	/* ─── Toast ─────────────────────────────────────────────── */

	function getToastHost() {
		var host = document.querySelector( '.bpjm-toast-host' );
		if ( ! host ) {
			host = document.createElement( 'div' );
			host.className = 'bpjm-toast-host';
			document.body.appendChild( host );
		}
		return host;
	}

	function toast( message, tone ) {
		tone = tone || 'info';
		var host = getToastHost();
		var el   = document.createElement( 'div' );
		el.className    = 'bpjm-toast bpjm-toast--' + tone;
		el.setAttribute( 'role', 'status' );
		el.textContent  = String( message );
		host.appendChild( el );

		// Trigger transition.
		requestAnimationFrame( function () {
			el.classList.add( 'bpjm-toast--visible' );
		} );

		window.setTimeout( function () {
			el.classList.remove( 'bpjm-toast--visible' );
			window.setTimeout( function () {
				if ( el.parentNode ) {
					el.parentNode.removeChild( el );
				}
			}, 250 );
		}, 3600 );
	}

	window.bpjmToast = toast;

	/* ─── Confirm modal (returns a Promise) ──────────────────── */

	function confirmModal( opts ) {
		opts = opts || {};
		return new Promise( function ( resolve ) {
			var backdrop = document.createElement( 'div' );
			backdrop.className = 'bpjm-confirm-backdrop';

			var card = document.createElement( 'div' );
			card.className = 'bpjm-confirm';
			card.setAttribute( 'role', 'dialog' );
			card.setAttribute( 'aria-modal', 'true' );

			var title = document.createElement( 'h2' );
			title.className = 'bpjm-confirm__title';
			title.textContent = opts.title || '';
			if ( opts.title ) { card.appendChild( title ); }

			var desc = document.createElement( 'p' );
			desc.className = 'bpjm-confirm__desc';
			desc.textContent = opts.message || i18n.confirmDanger || '';
			if ( opts.message || i18n.confirmDanger ) { card.appendChild( desc ); }

			var actions = document.createElement( 'div' );
			actions.className = 'bpjm-confirm__actions';

			var cancelBtn = document.createElement( 'button' );
			cancelBtn.type = 'button';
			cancelBtn.className = 'bpjm-btn bpjm-btn-secondary';
			cancelBtn.textContent = opts.cancelLabel || i18n.confirmCancel || 'Cancel';

			var confirmBtn = document.createElement( 'button' );
			confirmBtn.type = 'button';
			confirmBtn.className = 'bpjm-btn ' + ( 'danger' === opts.tone ? 'bpjm-btn-danger': 'bpjm-btn-primary' );
			confirmBtn.textContent = opts.confirmLabel || i18n.confirmContinue || 'Continue';

			actions.appendChild( cancelBtn );
			actions.appendChild( confirmBtn );
			card.appendChild( actions );
			backdrop.appendChild( card );
			document.body.appendChild( backdrop );

			function cleanup( result ) {
				document.removeEventListener( 'keydown', onKey );
				if ( backdrop.parentNode ) {
					backdrop.parentNode.removeChild( backdrop );
				}
				resolve( result );
			}

			function onKey( e ) {
				if ( 'Escape' === e.key ) { cleanup( false ); }
				if ( 'Enter' === e.key ) { cleanup( true ); }
			}

			cancelBtn.addEventListener( 'click', function () { cleanup( false ); } );
			confirmBtn.addEventListener( 'click', function () { cleanup( true ); } );
			backdrop.addEventListener( 'click', function ( e ) {
				if ( e.target === backdrop ) { cleanup( false ); }
			} );
			document.addEventListener( 'keydown', onKey );
			confirmBtn.focus();
		} );
	}

	window.bpjmConfirm = confirmModal;

	/* ─── Role picker (searchable chips) ─────────────────────── */

	/**
	 * Progressive enhancement over a native <select multiple>.
	 *
	 * The <select> stays in the DOM and remains the only thing that is
	 * POSTed, so the field name, the option values and the sanitizer's
	 * bpjm_array_rendered_keys sentinel contract are all untouched — with
	 * JS off the plain multi-select still works exactly as before.
	 *
	 * `required` is stripped on enhance: a hidden required control makes
	 * Chrome abort submit with "not focusable" and blocks the whole form.
	 * The same "pick at least one" guard is re-applied below on submit.
	 */
	function enhanceRolePicker( select ) {
		var options = Array.prototype.slice.call( select.options );
		var wrap    = document.createElement( 'div' );
		var control = document.createElement( 'div' );
		var chips   = document.createElement( 'div' );
		var search  = document.createElement( 'input' );
		var clear   = document.createElement( 'button' );
		var list    = document.createElement( 'div' );
		var empty   = document.createElement( 'p' );

		wrap.className    = 'bpjm-rolepick';
		control.className = 'bpjm-rolepick__control';
		chips.className   = 'bpjm-rolepick__chips';
		list.className    = 'bpjm-rolepick__list';
		empty.className   = 'bpjm-rolepick__empty';
		empty.hidden      = true;
		empty.textContent = i18n.roleNoMatches || 'No roles match your search.';

		search.type        = 'search';
		search.className   = 'bpjm-rolepick__search';
		search.placeholder = i18n.roleSearch || 'Search roles…';
		search.setAttribute( 'aria-label', search.placeholder );

		clear.type      = 'button';
		clear.className = 'bpjm-rolepick__clear';
		clear.innerHTML = '&times;';
		clear.setAttribute( 'aria-label', i18n.roleClearAll || 'Clear all' );
		clear.title     = i18n.roleClearAll || 'Clear all';

		// One checkbox per option. Real inputs, so they are focusable,
		// labelled and keyboard-reachable without any ARIA gymnastics.
		var rows = options.map( function ( opt, index ) {
			var id    = select.id + '-opt-' + index;
			var row   = document.createElement( 'label' );
			var box   = document.createElement( 'input' );
			var text  = document.createElement( 'span' );

			row.className   = 'bpjm-rolepick__option';
			row.htmlFor     = id;
			box.type        = 'checkbox';
			box.id          = id;
			box.checked     = opt.selected;
			box.value       = opt.value;
			text.textContent = ( opt.textContent || '' ).trim();

			row.appendChild( box );
			row.appendChild( text );
			list.appendChild( row );

			box.addEventListener( 'change', function () {
				opt.selected = box.checked;
				render();
			} );

			return { row: row, box: box, label: text.textContent, option: opt };
		} );

		function render() {
			chips.textContent = '';
			var any = false;

			rows.forEach( function ( item ) {
				if ( ! item.option.selected ) {
					return;
				}
				any = true;

				var chip = document.createElement( 'span' );
				chip.className = 'bpjm-rolepick__chip';

				var chipLabel = document.createElement( 'span' );
				chipLabel.textContent = item.label;

				var remove = document.createElement( 'button' );
				remove.type      = 'button';
				remove.className = 'bpjm-rolepick__chip-remove';
				remove.innerHTML = '&times;';
				// translators: %s is a user role name, e.g. "Administrator".
				remove.setAttribute( 'aria-label', ( i18n.roleRemove || 'Remove %s' ).replace( '%s', item.label ) );

				remove.addEventListener( 'click', function () {
					item.option.selected = false;
					item.box.checked     = false;
					render();
					search.focus();
				} );

				chip.appendChild( chipLabel );
				chip.appendChild( remove );
				chips.appendChild( chip );
			} );

			clear.hidden = ! any;
		}

		function filter() {
			var term  = search.value.trim().toLowerCase();
			var shown = 0;

			rows.forEach( function ( item ) {
				var hit = ! term || item.label.toLowerCase().indexOf( term ) !== -1;
				item.row.hidden = ! hit;
				if ( hit ) {
					shown++;
				}
			} );

			empty.hidden = shown > 0;
		}

		search.addEventListener( 'input', filter );
		// A bare Enter in the search box must not submit the settings form.
		search.addEventListener( 'keydown', function ( e ) {
			if ( 'Enter' === e.key ) {
				e.preventDefault();
			}
		} );

		clear.addEventListener( 'click', function () {
			rows.forEach( function ( item ) {
				item.option.selected = false;
				item.box.checked     = false;
			} );
			render();
			search.focus();
		} );

		control.appendChild( chips );
		control.appendChild( search );
		control.appendChild( clear );
		wrap.appendChild( control );
		wrap.appendChild( list );
		wrap.appendChild( empty );

		select.parentNode.insertBefore( wrap, select );

		// Keep the select as the value carrier, out of the a11y tree so
		// screen readers do not meet every role twice.
		select.removeAttribute( 'required' );
		select.hidden = true;
		select.setAttribute( 'aria-hidden', 'true' );
		select.tabIndex = -1;
		select.classList.add( 'bpjm-role-select--enhanced' );

		render();
		filter();

		return {
			select: select,
			search: search,
			hasSelection: function () {
				return options.some( function ( opt ) {
					return opt.selected;
				} );
			},
		};
	}

	/* ─── Color picker ──────────────────────────────────────── */

	$( function () {
		if ( $.fn.wpColorPicker ) {
			$( '.bpjm-color-picker' ).wpColorPicker();
		}

		var pickers = Array.prototype.slice
			.call( document.querySelectorAll( 'select[data-bpjm-role-picker]' ) )
			.map( enhanceRolePicker );

		// Re-apply the guard that the stripped `required` attribute used to
		// provide, so an empty role list still cannot be saved by accident.
		if ( pickers.length ) {
			var form = pickers[ 0 ].select.closest( 'form' );
			if ( form ) {
				form.addEventListener( 'submit', function ( e ) {
					for ( var i = 0; i < pickers.length; i++ ) {
						if ( pickers[ i ].hasSelection() ) {
							continue;
						}
						e.preventDefault();
						toast( i18n.rolePickOne || 'Select at least one role.', 'error' );
						pickers[ i ].search.focus();
						return;
					}
				} );
			}
		}

		// Intercept destructive links that opt in via data-bpjm-confirm.
		$( document ).on( 'click', '[data-bpjm-confirm]', function ( e ) {
			var $el = $( this );
			if ( $el.data( 'bpjm-confirm-ok' ) ) { return; }
			e.preventDefault();
			var message = $el.data( 'bpjm-confirm' ) || i18n.confirmDanger;
			var tone    = $el.data( 'bpjm-confirm-tone' ) || 'danger';
			confirmModal( { message: message, tone: tone } ).then( function ( ok ) {
				if ( ! ok ) { return; }
				$el.data( 'bpjm-confirm-ok', true );
				if ( $el.is( 'a' ) ) {
					window.location.href = $el.attr( 'href' );
				} else if ( $el.is( 'button' ) || $el.is( 'input' ) ) {
					var form = $el.closest( 'form' ).get( 0 );
					if ( ! form ) { return; }
					// Preserve which submit button was clicked so the form's
					// named buttons (e.g. bpjm named submit buttons) still
					// POST their value. Native form.submit() drops the
					// submitter, so the PHP handler that keys off the button
					// name would never fire and the action silently no-ops.
					// Reference: Basecamp card 9828852542.
					var btnName  = $el.attr( 'name' ) || '';
					var btnValue = $el.attr( 'value' ) || '1';
					if ( btnName ) {
						var hidden    = document.createElement( 'input' );
						hidden.type   = 'hidden';
						hidden.name   = btnName;
						hidden.value  = btnValue;
						form.appendChild( hidden );
					}
					form.submit();
				}
			} );
		} );
	} );

} )( jQuery );
