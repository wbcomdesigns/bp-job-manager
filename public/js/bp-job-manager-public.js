jQuery( document ).ready(
	function( $ ) {
		'use strict';

		// Open the job application url.
		$( document ).on(
			'click', '#bpjm-job-application-btn a', function(){
				window.open( $( this ).data( 'url' ), '_blank' );
			}
		);

		$( document ).on(
			'click', '.bpjm-display-resume-checkbox', function(){
			$(".resume-fields-row").animate({
				height: 'toggle'
			});
		});

	}
);
