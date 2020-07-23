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
if ( ! class_exists( 'Bp_Job_Manager_Globals' ) ) :
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
		 * The user roles allowed to post jobs.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string    $post_job_user_roles
		 */
		public $post_job_user_roles;

		/**
		 * The user roles allowed to apply for jobs.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string    $apply_job_user_roles
		 */
		public $apply_job_user_roles;

		/**
		 * This is the page id of the job application page
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string    $job_application_pgid
		 */
		public $job_application_pgid;

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
			$this->version     = '1.0.1';
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

			if ( empty( $settings ) ) {
				$this->apply_job_user_roles   = array( 'administrator', 'candidate', 'subscriber' );
				$this->post_job_user_roles    = array( 'administrator', 'employer' );
				$this->bpjm_resume_at_profile = 'yes';
			} else {
				if ( ! empty( $settings['apply_job_user_roles'] ) ) {
					$this->apply_job_user_roles = $settings['apply_job_user_roles'];
				}
				if ( ! empty( $settings['post_job_user_roles'] ) ) {
					$this->post_job_user_roles = $settings['post_job_user_roles'];
				}

				if ( array_key_exists( 'bpjm_resume_at_profile', $settings ) ) {
					$this->bpjm_resume_at_profile = 'yes';
				} else {
					$this->bpjm_resume_at_profile = 'no';
				}

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
