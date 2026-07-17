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
 *  - bpjm_publish_job_listing()    (transition_post_status)
 *  - bpjm_publish_resume()         (transition_post_status)
 *  - bpjm_delete_job_activity()    (wp_trash_post, before_delete_post)
 *  - bpjm_sanitize_settings()      (delegated to by the panel sanitizer)
 *
 * NOTE: the two activity writers hook WP core's `transition_post_status`,
 * NOT WP Job Manager's `publish_job_listing` / `publish_resume`. This plugin
 * consumes neither of those WPJM hooks — see includes/class-bp-job-manager.php
 * (define_admin_hooks) for the real wiring.
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
		 * Post a BuddyPress activity when a job is published for the first time.
		 *
		 * Publish and update are different: this fires only on the transition to
		 * publish (e.g. pending/draft -> publish). Editing or re-saving an
		 * already published job (publish -> publish) adds nothing.
		 *
		 * @param string $new_status New post status.
		 * @param string $old_status Previous post status.
		 * @param object $post       Post object.
		 * @return void
		 */
		public function bpjm_publish_job_listing( $new_status, $old_status, $post ) {
			if ( 'publish' !== $new_status || 'publish' === $old_status || 'job_listing' !== $post->post_type ) {
				return;
			}

			global $bpjm_bp_job_manager;
			if ( ! isset( $bpjm_bp_job_manager->bpjm_job_post_activity ) || 'yes' !== $bpjm_bp_job_manager->bpjm_job_post_activity ) {
				return;
			}

			/* translators: %1$s: BP user link ; %2$s: Job link */
			$this->bpjm_add_publish_activity( $post, 'bpjm_job_post', __( '%1$s posted a new job %2$s', 'bp-job-manager' ) );
		}

		/**
		 * Post a BuddyPress activity when a resume is published for the first time.
		 *
		 * Same publish-only rule as bpjm_publish_job_listing(): fires on the
		 * transition to publish, never on later updates.
		 *
		 * @param string $new_status New post status.
		 * @param string $old_status Previous post status.
		 * @param object $post       Post object.
		 * @return void
		 */
		public function bpjm_publish_resume( $new_status, $old_status, $post ) {
			if ( 'publish' !== $new_status || 'publish' === $old_status || 'resume' !== $post->post_type ) {
				return;
			}

			global $bpjm_bp_job_manager;
			if ( ! isset( $bpjm_bp_job_manager->bpjm_resume_activity ) || 'yes' !== $bpjm_bp_job_manager->bpjm_resume_activity ) {
				return;
			}

			/* translators: %1$s: Resume user link ; %2$s: Resume link */
			$this->bpjm_add_publish_activity( $post, 'bpjm_resume_publish', __( '%1$s posted resume %2$s', 'bp-job-manager' ) );
		}

		/**
		 * Add the "posted a new job/resume" BuddyPress activity for a post.
		 *
		 * @param object $post          Post object.
		 * @param string $activity_type Activity type (bpjm_job_post|bpjm_resume_publish).
		 * @param string $action_format Translated sprintf format for the activity action.
		 * @return void
		 */
		private function bpjm_add_publish_activity( $post, $activity_type, $action_format ) {
			if ( ! function_exists( 'bp_activity_add' ) ) {
				return;
			}

			$permalink = '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a>';

			bp_activity_add(
				array(
					'type'      => $activity_type,
					'action'    => sprintf( $action_format, bp_core_get_userlink( $post->post_author ), $permalink ),
					'component' => 'activity',
					'user_id'   => $post->post_author,
					'item_id'   => $post->ID,
					'content'   => $post->post_content,
				)
			);
		}

		/**
		 * Remove the BuddyPress activity tied to a job/resume when the post is
		 * trashed or permanently deleted, so the stream never keeps a dead link.
		 *
		 * Mirrors the activity written on publish in bpjm_publish_job_listing()
		 * / bpjm_publish_resume(): item_id = post ID, component 'activity', type
		 * 'bpjm_job_post' for jobs and 'bpjm_resume_publish' for resumes. Wired
		 * to both wp_trash_post (trash) and before_delete_post (permanent/force
		 * delete) so every removal path is covered. Re-firing on an already
		 * cleaned item is a harmless no-op.
		 *
		 * @since 2.8.5
		 * @param int $post_id Post ID being trashed or deleted.
		 * @return void
		 */
		public function bpjm_delete_job_activity( $post_id ) {
			if ( ! function_exists( 'bp_activity_delete' ) ) {
				return;
			}

			$post_type = get_post_type( $post_id );

			if ( 'job_listing' === $post_type ) {
				$activity_type = 'bpjm_job_post';
			} elseif ( 'resume' === $post_type ) {
				$activity_type = 'bpjm_resume_publish';
			} else {
				return;
			}

			bp_activity_delete(
				array(
					'item_id'   => $post_id,
					'type'      => $activity_type,
					'component' => 'activity',
				)
			);
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
