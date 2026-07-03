<?php
/**
 * Admin class for BuddyPress Job Manager plugin.
 *
 * @package Bp_Job_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/admin
 */

/**
 * Legacy admin service class for BuddyPress Job Manager.
 *
 * As of 2.8.5 the admin UI (menu, settings page, enqueues, notice
 * suppression) is owned by Bp_Job_Manager_Admin_Panel. This class is
 * retained ONLY for its non-UI responsibilities that remain wired via
 * the loader / reused by the panel:
 *
 *  - bpjm_setup_admin_bar_links()  (bp_setup_admin_bar)
 *  - bpjm_publish_job_listing()    (publish_job_listing)
 *  - bpjm_publish_resume()         (publish_resume)
 *  - bpjm_sanitize_settings()      (delegated to by the panel sanitizer)
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
if ( ! class_exists( 'Bp_Job_Manager_Admin' ) ) :

	/**
	 * Class Bp_Job_Manager_Admin
	 *
	 * Retained non-UI admin helpers (admin-bar links, activity writers,
	 * settings sanitizer). UI moved to Bp_Job_Manager_Admin_Panel in 2.8.5.
	 *
	 * @since 1.0.0
	 */
	class Bp_Job_Manager_Admin {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		protected $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		protected $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    string $plugin_name       The name of this plugin.
		 * @param    string $version    The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version     = $version;
		}

		/**
		 * This function will list the jobs and resumes link in the dropdown list.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param    array $wp_admin_nav Contains wp nav items.
		 */
		public function bpjm_setup_admin_bar_links( $wp_admin_nav = array() ) {
			global $wp_admin_bar, $bpjm_bp_job_manager;
			if ( is_user_logged_in() ) {
				$curr_user = wp_get_current_user();
				if ( ! empty( $curr_user->roles ) ) {

					/**
					 * Resumes menu - for the roles allowed for job posting.
					 */
					$apply_job_roles       = isset( $bpjm_bp_job_manager->apply_job_user_roles ) ? $bpjm_bp_job_manager->apply_job_user_roles : array();
					$match_apply_job_roles = array_intersect( $apply_job_roles, $curr_user->roles );
					if ( ! empty( $match_apply_job_roles ) ) {
						// Resumes menu.
						$profile_menu_slug  = 'resumes';
						$profile_menu_title = esc_html__( 'Resumes', 'bp-job-manager' );

						// Get resume count using the helper function.
						$my_resumes_count = bpjm_member_profile_resumes_count( get_current_user_id() );

						$base_url         = bp_loggedin_user_domain() . $profile_menu_slug;
						$my_resumes_url   = $base_url . '/my-resumes';
						$applied_jobs_url = $base_url . '/applied-jobs';
						$add_resume_url   = $base_url . '/add-resume';

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-buddypress',
								'id'     => 'my-account-' . $profile_menu_slug,
								'title'  => esc_html( $profile_menu_title ) . ' <span class="count">' . bp_core_number_format( $my_resumes_count ) . '</span>',
								'href'   => trailingslashit( $my_resumes_url ),
							)
						);

						// Add resume submenu items.
						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-my-resumes',
								'title'  => esc_html__( 'My Resumes', 'bp-job-manager' ),
								'href'   => trailingslashit( $my_resumes_url ),
							)
						);

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-applied-jobs',
								'title'  => esc_html__( 'Applied Jobs', 'bp-job-manager' ),
								'href'   => trailingslashit( $applied_jobs_url ),
							)
						);

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-add-resume',
								'title'  => esc_html__( 'Add Resume', 'bp-job-manager' ),
								'href'   => trailingslashit( $add_resume_url ),
							)
						);
					}

					/**
					 * Jobs menu - for the roles allowed for job posting.
					 */
					$job_posting_roles       = isset( $bpjm_bp_job_manager->post_job_user_roles ) ? $bpjm_bp_job_manager->post_job_user_roles : array();
					$match_job_posting_roles = array_intersect( $job_posting_roles, $curr_user->roles );
					if ( ! empty( $match_job_posting_roles ) ) {
						// Jobs menu.
						$jobs_menu_slug  = 'jobs';
						$jobs_menu_title = esc_html__( 'Jobs', 'bp-job-manager' );

						// Get job count using the helper function.
						$my_jobs_count = bpjm_member_profile_jobs_count( get_current_user_id() );

						$jobs_base_url = bp_loggedin_user_domain() . $jobs_menu_slug;
						$my_jobs_url   = $jobs_base_url . '/my-jobs';
						$post_job_url  = $jobs_base_url . '/post-job';

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-buddypress',
								'id'     => 'my-account-' . $jobs_menu_slug,
								'title'  => esc_html( $jobs_menu_title ) . ' <span class="count">' . bp_core_number_format( $my_jobs_count ) . '</span>',
								'href'   => trailingslashit( $my_jobs_url ),
							)
						);

						// Add job submenu items.
						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $jobs_menu_slug,
								'id'     => 'my-account-' . $jobs_menu_slug . '-my-jobs',
								'title'  => esc_html__( 'My Jobs', 'bp-job-manager' ),
								'href'   => trailingslashit( $my_jobs_url ),
							)
						);

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $jobs_menu_slug,
								'id'     => 'my-account-' . $jobs_menu_slug . '-post-job',
								'title'  => esc_html__( 'Post Job', 'bp-job-manager' ),
								'href'   => trailingslashit( $post_job_url ),
							)
						);
					}
				}
			}
		}



		/**
		 * Display Job listings
		 *
		 * @param  int   $post_id Job ID.
		 * @param  array $post Job post object.
		 * @return void
		 */
		public function bpjm_publish_job_listing( $post_id, $post ) {
			global $bpjm_bp_job_manager;
			if ( isset( $bpjm_bp_job_manager->bpjm_job_post_activity ) && 'yes' === $bpjm_bp_job_manager->bpjm_job_post_activity ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'bp_activity';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Existence probe for the BuddyPress activity table on the publish write path; no WP API exposes SHOW TABLES and caching a schema probe on a write path is not useful.
				$get_table  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

				if ( $table_name === $get_table ) {
					$activity_table = $wpdb->prefix . 'bp_activity';
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Duplicate-activity guard against the BuddyPress custom activity table (no core CRUD API for this item_id+type lookup); runs once on the publish write path so caching adds no value. Fully prepared.
					$check          = $wpdb->get_results(
						$wpdb->prepare(
							'SELECT * FROM `' . esc_sql( $activity_table ) . '` WHERE item_id = %d AND type = %s',
							$post->ID,
							'bpjm_job_post'
						)
					);

					if ( ! $check ) {
						$args['type']  = 'bpjm_job_post';
						$job_permalink = '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a>';
						/* translators: %1$s: BP user link ;  %2$s: Job Link*/
						$args['action']    = sprintf( __( '%1$s posted a new job %2$s', 'bp-job-manager' ), bp_core_get_userlink( $post->post_author ), $job_permalink );
						$args['component'] = 'activity';
						$args['user_id']   = $post->post_author;
						$args['item_id']   = $post->ID;
						$args['content']   = $post->post_content;

						bp_activity_add( $args );
					}
				}
			}
		}


		/**
		 * Publish resume.
		 *
		 * @param  int   $post_id Job ID.
		 * @param  array $post Job post object.
		 * @return void
		 */
		public function bpjm_publish_resume( $post_id, $post ) {
			global $bpjm_bp_job_manager;
			if ( isset( $bpjm_bp_job_manager->bpjm_resume_activity ) && 'yes' === $bpjm_bp_job_manager->bpjm_resume_activity ) {

				global $wpdb;
				$table_name = $wpdb->prefix . 'bp_activity';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Existence probe for the BuddyPress activity table on the publish write path; no WP API exposes SHOW TABLES and caching a schema probe on a write path is not useful.
				$get_table  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
				if ( $table_name === $get_table ) {
					$activity_table = $wpdb->prefix . 'bp_activity';
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Duplicate-activity guard against the BuddyPress custom activity table (no core CRUD API for this item_id+type lookup); runs once on the publish write path so caching adds no value. Fully prepared.
					$check          = $wpdb->get_results(
						$wpdb->prepare(
							'SELECT * FROM `' . esc_sql( $activity_table ) . '` WHERE item_id = %d AND type = %s',
							$post->ID,
							'bpjm_resume_publish'
						)
					);
					if ( ! $check ) {
						$args['type']  = 'bpjm_resume_publish';
						$job_permalink = '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a>';
						/* translators: %1$s: Resume user link ;  %2$s: Resume Link*/
						$args['action']    = sprintf( __( '%1$s posted resume %2$s', 'bp-job-manager' ), bp_core_get_userlink( $post->post_author ), $job_permalink );
						$args['component'] = 'activity';
						$args['user_id']   = $post->post_author;
						$args['item_id']   = $post->ID;
						$args['content']   = $post->post_content;

						bp_activity_add( $args );
					}
				}
			}
		}

		/**
		 * Sanitize settings for register_setting()
		 *
		 * @param array $input Input settings array.
		 * @return array Sanitized settings array.
		 */
		public function bpjm_sanitize_settings( $input ) {
			$sanitized = array();

			if ( is_array( $input ) ) {
				foreach ( $input as $key => $value ) {
					if ( is_array( $value ) ) {
						$sanitized[ sanitize_key( $key ) ] = array_map( 'sanitize_text_field', $value );
					} else {
						$sanitized[ sanitize_key( $key ) ] = sanitize_text_field( $value );
					}
				}
			}

			return $sanitized;
		}
	}
endif;
