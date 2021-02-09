jQuery( document ).ready(
	function( $ ) {
		'use strict';

		// Support Tab.
		var acc = document.getElementsByClassName( "bpjm-accordion" );
		var i;
		for (i = 0; i < acc.length; i++) {
			acc[i].onclick = function() {
				this.classList.toggle( "active" );
				var panel = this.nextElementSibling;
				if (panel.style.maxHeight) {
					panel.style.maxHeight = null;
				} else {
					panel.style.maxHeight = panel.scrollHeight + "px";
				}
			}
		}
		$( document ).on(
			'click',
			'.bpjm-accordion',
			function(){
				return false;
			}
		);

		$( '.bpjm-user-roles' ).selectize(
			{
				placeholder     : "Select user roles",
				plugins         : ['remove_button'],
			}
		);

	}
);
