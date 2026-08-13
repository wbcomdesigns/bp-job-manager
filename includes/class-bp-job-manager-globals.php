<?php
/**
 * The file that defines the global variable of the plugin
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bp_Job_Manager_Globals' ) ) :

	/**
	 * The file that defines the global variable of the plugin
	 *
	 * @link       https://wbcomdesigns.com/
	 * @since      1.0.0
	 * @package    Bp_Job_Manager
	 * @subpackage Bp_Job_Manager/includes
	 * @author     Wbcom Designs <admin@wbcomdesigns.com>
	 */
	class Bp_Job_Manager_Globals {

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $version    The current version of the plugin.
		 */
		protected $version;

		/**
		 * This is the page id of the job application page
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string    $job_application_pgid
		 */
		public $job_application_pgid;

		/**
		 * Bpjm_resume_activity
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var mixed   $bpjm_resume_activity
		 */
		public $bpjm_resume_activity;

		/**
		 * Bspjm_job_post_activity
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var mixed  $bpjm_job_post_activity
		 */
		public $bpjm_job_post_activity;

		/**
		 * Bpjm_app_notify
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var mixed     $bpjm_app_notify
		 */
		public $bpjm_app_notify;

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->plugin_name = 'bp-job-manager';
			$this->version     = defined( 'BPJM_PLUGIN_VERSION' ) ? BPJM_PLUGIN_VERSION : '1.0.0';
			$this->setup_plugin_global();
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		public function setup_plugin_global() {
			$settings = get_option( 'bpjm_general_settings' );

			if ( ! empty( $settings ) ) {
				if ( array_key_exists( 'bpjm_resume_activity', $settings ) ) {
					$this->bpjm_resume_activity = 'yes';
				} else {
					$this->bpjm_resume_activity = 'no';
				}

				if ( array_key_exists( 'bpjm_job_post_activity', $settings ) ) {
					$this->bpjm_job_post_activity = 'yes';
				} else {
					$this->bpjm_job_post_activity = 'no';
				}

				if ( array_key_exists( 'bpjm_app_notify', $settings ) ) {
					$this->bpjm_app_notify = 'yes';
				} else {
					$this->bpjm_app_notify = 'no';
				}
			}

			$this->job_application_pgid = get_option( 'bpjm_job_application_pgid' );
		}
	}
endif;
