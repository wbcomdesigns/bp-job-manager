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
			add_action( 'bp_register_member_types', array( $this, 'wpbpjm_register_bp_member_types' ) );
			add_action( 'widgets_init', array( $this, 'wpbpjm_register_widgets' ) );
		}

		 /**
 		 * Actions performed for registering bp member types
 		 */
		 function wpbpjm_register_bp_member_types() {
		   bp_register_member_type(
		     'employer',
		     array(
		       'labels' => array(
		         'name' => 'Employers',
		         'singular_name' => 'Employer'
		       ),
		       'has_directory' => 'employer'
		     )
		   );
		   bp_register_member_type(
		     'candidate',
		     array(
		       'labels' => array(
		         'name' => 'Candidates',
		         'singular_name' => 'Candidate'
		       ),
		       'has_directory' => 'candidate'
		     )
		   );
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
