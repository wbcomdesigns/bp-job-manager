jQuery( function( $ ) {
	'use strict';

	// Keep WP Job Manager's [job_dashboard] "Add Job" button inside the BuddyPress
	// profile. WPJM hardcodes that button to its standalone submit_job_form page,
	// which renders an empty href when unconfigured and otherwise leaves the profile.
	if ( window.bpjm_public && bpjm_public.post_job_url ) {
		$( '.jm-dashboard__actions a.jm-ui-button' ).attr( 'href', bpjm_public.post_job_url );
	}
	if ( window.bpjm_public && bpjm_public.add_resume_url ) {
		$( '#resume-manager-candidate-dashboard tfoot a' ).attr( 'href', bpjm_public.add_resume_url );
	}

	// Resume Manager's and Applications' dashboards are tables; give each cell its
	// column name so the stylesheet can stack rows into labelled cards on small screens.
	// Applications has no <thead>, so read whichever row holds the <th> cells.
	$( '.bpjm-screen table' ).each( function() {
		var labels = $( this ).find( 'tr' ).has( 'th' ).first().children( 'th' ).map( function() {
			return $( this ).text().trim();
		} ).get();

		$( this ).find( 'tr' ).has( 'td' ).each( function() {
			$( this ).children( 'td' ).each( function( i ) {
				$( this ).attr( 'data-header', labels[ i ] || '' );
			} );
		} );
	} );
} );
