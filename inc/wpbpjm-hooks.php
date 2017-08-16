<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Class to add custom hooks
 */
if( !class_exists( 'Wpbpjm_Hooks' ) ) {
	class Wpbpjm_Hooks{
		/**
		 * Constructor
		 */
		function __construct() {
			add_action( 'widgets_init', array( $this, 'wpbpjm_register_widgets' ) );
		}

		/**
 		 * Actions performed for registering widgets
 		 */
		function wpbpjm_register_widgets() {
			if( class_exists( 'JobApplicants' ) ) {
				register_widget ( 'JobApplicants' );
			}
		}
	}
	new Wpbpjm_Hooks();
}
