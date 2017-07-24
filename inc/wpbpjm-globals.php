<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
* Class to define global variable for this plugin
*
* @since    1.0.0
* @author   Wbcom Designs
*/
if( !class_exists( 'Wpbpjm_Globals' ) ) {
	class Wpbpjm_Globals{

		public  $job_user_roles,
				$resume_user_roles;
		/**
		* Constructor.
		*
		* @since    1.0.0
		* @access   public
		* @author   Wbcom Designs
		*/
		public function __construct() {
			$this->setup_globals();
		}

		/**
		 *
		 */
		public function setup_globals() {
			global $bp_job_manager;
			$settings = get_option( 'wpbpjm_general_settings' );

			//User roles for job management
			$this->job_user_roles = '';
			if( isset( $settings['job_user_roles'] ) ) {
				$this->job_user_roles = $settings['job_user_roles'];
			}

			//User roles for resume management
			$this->resume_user_roles = '';
			if( isset( $settings['resume_user_roles'] ) ) {
				$this->resume_user_roles = $settings['resume_user_roles'];
			}
		}

		public static function pluralize($singular, $plural=null) {
			if($plural!==null) return $plural;

			$last_letter = strtolower($singular[strlen($singular)-1]);
			switch($last_letter) {
				case 'y':
					return substr($singular,0,-1).'ies';
				case 's':
					return $singular.'es';
				default:
					return $singular.'s';
			}
		}
	}
}