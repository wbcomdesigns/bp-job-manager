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

	/* ─── Color picker ──────────────────────────────────────── */

	$( function () {
		if ( $.fn.wpColorPicker ) {
			$( '.bpjm-color-picker' ).wpColorPicker();
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
