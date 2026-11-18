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
					if ( bpjm_user_can_apply_job( $curr_user ) && bpjm_is_plugin_active( 'wp-job-manager-resumes/wp-job-manager-resumes.php' ) ) {
						// Resumes menu.
						$profile_menu_slug  = 'resumes';
						$profile_menu_title = esc_html__( 'Resumes', 'bp-job-manager' );

						// Get resume count using the helper function.
						$my_resumes_count = bpjm_member_profile_resumes_count( get_current_user_id() );

						$base_url       = bp_loggedin_user_domain() . $profile_menu_slug;
						$my_resumes_url = $base_url . '/my-resumes';
						$add_resume_url = $base_url . '/add-resume';

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-buddypress',
								'id'     => 'my-account-' . $profile_menu_slug,
								'title'  => esc_html( $profile_menu_title ) . ( $my_resumes_count ? ' <span class="count">' . bp_core_number_format( $my_resumes_count ) . '</span>' : '' ),
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
								'id'     => 'my-account-' . $profile_menu_slug . '-add-resume',
								'title'  => esc_html__( 'Add Resume', 'bp-job-manager' ),
								'href'   => trailingslashit( $add_resume_url ),
							)
						);
					}

					/**
					 * Jobs menu - for the roles allowed for job posting.
					 */
					if ( bpjm_user_can_post_job( $curr_user ) ) {
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
								'title'  => esc_html( $jobs_menu_title ) . ( $my_jobs_count ? ' <span class="count">' . bp_core_number_format( $my_jobs_count ) . '</span>' : '' ),
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

						if ( bpjm_is_plugin_active( 'wp-job-manager-applications/wp-job-manager-applications.php' ) ) {
							$wp_admin_bar->add_menu(
								array(
									'parent' => 'my-account-' . $jobs_menu_slug,
									'id'     => 'my-account-' . $jobs_menu_slug . '-applied-jobs',
									'title'  => esc_html__( 'Applied Jobs', 'bp-job-manager' ),
									'href'   => trailingslashit( $jobs_base_url . '/applied-jobs' ),
								)
							);
						}

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
		 * One BuddyPress activity per job: added when it is first published, kept
		 * current when it is edited.
		 *
		 * Fires on every status transition. A job going back to publish (relist,
		 * re-publish after hide) reuses the entry it already has instead of adding
		 * a second one, and an edit to a published job (publish -> publish)
		 * refreshes the entry's title and text without moving it in the stream.
		 *
		 * @param string $new_status New post status.
		 * @param string $old_status Previous post status.
		 * @param object $post       Post object.
		 * @return void
		 */
		public function bpjm_publish_job_listing( $new_status, $old_status, $post ) {
			if ( 'publish' !== $new_status || 'job_listing' !== $post->post_type ) {
				return;
			}

			global $bpjm_bp_job_manager;
			if ( ! isset( $bpjm_bp_job_manager->bpjm_job_post_activity ) || 'yes' !== $bpjm_bp_job_manager->bpjm_job_post_activity ) {
				return;
			}

			$this->bpjm_save_publish_activity( $post, 'bpjm_job_post', 'publish' === $old_status );
		}

		/**
		 * One BuddyPress activity per resume, same rules as bpjm_publish_job_listing().
		 *
		 * @param string $new_status New post status.
		 * @param string $old_status Previous post status.
		 * @param object $post       Post object.
		 * @return void
		 */
		public function bpjm_publish_resume( $new_status, $old_status, $post ) {
			if ( 'publish' !== $new_status || 'resume' !== $post->post_type ) {
				return;
			}

			global $bpjm_bp_job_manager;
			if ( ! isset( $bpjm_bp_job_manager->bpjm_resume_activity ) || 'yes' !== $bpjm_bp_job_manager->bpjm_resume_activity ) {
				return;
			}

			$this->bpjm_save_publish_activity( $post, 'bpjm_resume_publish', 'publish' === $old_status );
		}

		/**
		 * Register the job and resume activity types with BuddyPress.
		 *
		 * The format callbacks rebuild each entry's text when it is displayed,
		 * so every reader sees it in their own language and with the job's
		 * current title. Stored text alone is frozen in the poster's language.
		 * Also lists both types in the activity filter.
		 *
		 * @since 3.0.1
		 */
		public function bpjm_register_activity_actions() {
			if ( ! function_exists( 'bp_activity_set_action' ) ) {
				return;
			}
			bp_activity_set_action( 'activity', 'bpjm_job_post', __( 'Posted a job', 'bp-job-manager' ), array( $this, 'bpjm_format_activity' ), __( 'Jobs', 'bp-job-manager' ), array( 'activity', 'member' ) );
			bp_activity_set_action( 'activity', 'bpjm_resume_publish', __( 'Published a resume', 'bp-job-manager' ), array( $this, 'bpjm_format_activity' ), __( 'Resumes', 'bp-job-manager' ), array( 'activity', 'member' ) );
		}

		/**
		 * Job / resume activity text, in the reader's language.
		 *
		 * @since 3.0.1
		 * @param string $action   Stored action.
		 * @param object $activity Activity item.
		 * @return string
		 */
		public function bpjm_format_activity( $action, $activity ) {
			$post = get_post( $activity->item_id );
			return $post ? $this->bpjm_activity_text( $activity->type, $post, $activity->user_id ) : $action;
		}

		/**
		 * The one place the job and resume activity text is built.
		 *
		 * A resume's title is the candidate's own name, so its entry reads
		 * "Fiona published a resume" rather than repeating the name as a link.
		 *
		 * @param string  $activity_type bpjm_job_post|bpjm_resume_publish.
		 * @param WP_Post $post          Job or resume.
		 * @param int     $user_id       Member who posted it.
		 * @return string
		 */
		private function bpjm_activity_text( $activity_type, $post, $user_id ) {
			if ( 'bpjm_resume_publish' === $activity_type ) {
				/* translators: 1: member profile link, 2: URL of the resume. */
				return sprintf( __( '%1$s published a <a href="%2$s">resume</a>', 'bp-job-manager' ), bp_core_get_userlink( $user_id ), esc_url( get_permalink( $post ) ) );
			}
			/* translators: 1: member profile link, 2: job title link. */
			return sprintf( __( '%1$s posted a job: %2$s', 'bp-job-manager' ), bp_core_get_userlink( $user_id ), '<a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a>' );
		}

		/**
		 * Add the job/resume activity, or update the one it already has.
		 *
		 * @param object $post          Post object.
		 * @param string $activity_type Activity type (bpjm_job_post|bpjm_resume_publish).
		 * @param bool   $is_edit       True for an edit to an already published post: update only, never add.
		 * @return void
		 */
		private function bpjm_save_publish_activity( $post, $activity_type, $is_edit ) {
			if ( ! function_exists( 'bp_activity_add' ) ) {
				return;
			}

			$args = array(
				'type'      => $activity_type,
				'action'    => $this->bpjm_activity_text( $activity_type, $post, $post->post_author ),
				'component' => 'activity',
				'user_id'   => $post->post_author,
				'item_id'   => $post->ID,
				'content'   => $post->post_content,
			);

			$existing_id = bp_activity_get_activity_id(
				array(
					'component' => 'activity',
					'type'      => $activity_type,
					'item_id'   => $post->ID,
				)
			);

			// An edit refreshes an existing entry; it never announces an old post as new.
			if ( $is_edit && ! $existing_id ) {
				return;
			}

			if ( $existing_id ) {
				$existing              = new BP_Activity_Activity( $existing_id );
				$args['id']            = $existing_id;
				$args['recorded_time'] = $existing->date_recorded; // Keep its place in the stream.
			}

			bp_activity_add( $args );
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
		 * WordPress calls this with whatever was submitted, which is not
		 * guaranteed to be an array (an emptied option arrives as null), so the
		 * parameter is deliberately typed loose and guarded below.
		 *
		 * @param mixed $input Input settings, normally an array.
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
