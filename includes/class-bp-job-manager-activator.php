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
	}

}
