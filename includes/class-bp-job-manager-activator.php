<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
if ( ! class_exists( 'Bp_Job_Manager_Activator' ) ) :
	class Bp_Job_Manager_Activator {

		/**
		 * Short Description. (use period)
		 *
		 * Long Description.
		 *
		 * @since    1.0.0
		 */
		public static function activate() {
			/**
			 * Create a page - Apply To Job
			 */
			if ( function_exists( 'get_page_by_title' ) ) {
				/******************** Job Application Page */
				$page_title = 'Apply To Job';
				$page       = get_page_by_title( $page_title );
				if ( empty( $page ) ) {
					$args  = array(
						'post_type'   => 'page',
						'post_title'  => $page_title,
						'post_status' => 'publish',
					);
					$pg_id = wp_insert_post( $args );
					update_option( 'bpjm_job_application_pgid', $pg_id );
				}
			}

			self::bpjm_resume_set_default_options();
		}

		/**
		 * Set default options for resume visibility.
		 */
		public static function bpjm_resume_set_default_options() {
			if ( class_exists( 'WP_Resume_Manager' ) ) {
				$users          = get_users( array( 'fields' => array( 'ID' ) ) );
				$resume_options = array(
					'display_resume' => 'yes',
					'email'          => 'yes',
					'prof_title'     => 'yes',
					'location'       => 'yes',
					'video'          => 'yes',
					'description'    => 'yes',
					'url'            => 'yes',
					'education'      => 'yes',
					'experience'     => 'yes',

				);
				foreach ( $users as $user ) {
					update_user_meta( $user->ID, 'bpjm_display_fields', $resume_options );
				}
			}

		}

	}
endif;
