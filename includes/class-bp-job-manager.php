<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
if ( ! class_exists( 'Bp_Job_Manager' ) ) :
	/**
	 * The core plugin class.
	 *
	 * @since 1.0.0
	 */
	class Bp_Job_Manager {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      Bp_Job_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

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

			$this->load_dependencies();
			$this->set_locale();
			$this->define_admin_hooks();
			$this->define_globals();
			$this->define_public_hooks();
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - Bp_Job_Manager_Loader. Orchestrates the hooks of the plugin.
		 * - Bp_Job_Manager_I18n. Defines internationalization functionality.
		 * - Bp_Job_Manager_Admin. Defines all hooks for the admin area.
		 * - Bp_Job_Manager_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies() {

			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'includes/class-bp-job-manager-loader.php';
				/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'includes/class-bp-job-manager-function.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'includes/class-bp-job-manager-i18n.php';

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 *
			 * The legacy Bp_Job_Manager_Admin class is retained for its non-UI
			 * concerns (settings sanitizer, admin-bar links, activity helpers).
			 * The card-panel admin UI lives in Bp_Job_Manager_Admin_Panel; the
			 * old admin/wbcom/ shared wrapper has been removed as of 2.8.5.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'admin/class-bp-job-manager-admin.php';
			require_once plugin_dir_path( __DIR__ ) . 'includes/admin/class-bp-job-manager-icons.php';
			require_once plugin_dir_path( __DIR__ ) . 'includes/admin/class-bp-job-manager-admin-panel.php';

			/**
			 * The class responsible for admin review.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'admin/class-bp-job-manager-admin-feedback.php';

			/**
			 * The class responsible for defining the global variable of the plugin.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'includes/class-bp-job-manager-globals.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'public/class-bp-job-manager-public.php';


			$this->loader = new Bp_Job_Manager_Loader();
		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the Bp_Job_Manager_I18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {

			$plugin_i18n = new Bp_Job_Manager_I18n();

			// init, not bp_loaded: bp_loaded fires during plugins_loaded, and loading a
			// text domain before init triggers _load_textdomain_just_in_time on WP 6.7+.
			$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );
		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_admin_hooks() {

			// Legacy admin service — retained ONLY for its non-UI helpers
			// (admin-bar links + activity-feed writers + the settings
			// sanitizer reused by the panel). Its UI methods
			// (enqueue_styles/scripts, bpjm_add_options_page,
			// bpjm_general_settings, wbcom_hide_all_admin_notices_*) are no
			// longer hooked — Bp_Job_Manager_Admin_Panel owns that surface
			// as of 2.8.5.
			$plugin_admin = new Bp_Job_Manager_Admin( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'bp_setup_admin_bar', $plugin_admin, 'bpjm_setup_admin_bar_links', 70 );
			$this->loader->add_action( 'transition_post_status', $plugin_admin, 'bpjm_publish_job_listing', 10, 3 );
			$this->loader->add_action( 'transition_post_status', $plugin_admin, 'bpjm_publish_resume', 10, 3 );
			$this->loader->add_action( 'bp_register_activity_actions', $plugin_admin, 'bpjm_register_activity_actions' );

			// Remove the matching BP activity when a job/resume is trashed or
			// permanently deleted so the stream never keeps a dead link.
			$this->loader->add_action( 'wp_trash_post', $plugin_admin, 'bpjm_delete_job_activity' );
			$this->loader->add_action( 'before_delete_post', $plugin_admin, 'bpjm_delete_job_activity' );

			// New card-panel admin (menu + settings + enqueue + notices).
			$admin_panel = new Bp_Job_Manager_Admin_Panel();
			$admin_panel->register();
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_public_hooks() {
			$plugin_public = new Bp_Job_Manager_Public( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bpjm_member_profile_jobs_tab' );

			/* Check if Resume Manager plugin active to add tab. */
			$wpjm_resumes_active = bpjm_is_plugin_active( 'wp-job-manager-resumes/wp-job-manager-resumes.php' );
			if ( $wpjm_resumes_active ) {
				/*
				 * The Resumes nav is the whole resume surface now.
				 *
				 * A second copy of the member's resume used to be printed onto the
				 * profile screen itself via bp_after_profile_loop_content, with its
				 * own admin setting, its own nine-checkbox per-member meta, its own
				 * settings form, a Youzify-only variant of that form, and a save
				 * handler on bp_init. All of it to show, on one screen, what the
				 * Resumes tab already shows on the next.
				 *
				 * It is gone in 3.0.0. The duplication was the source of its own
				 * bugs: the renderer and its settings form defaulted differently,
				 * so a member's resume could be visible while the form said it was
				 * hidden, and saving that form for an unrelated reason silently
				 * removed the resume. Deleting the second copy removes the whole
				 * class of bug rather than patching it.
				 */
				$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bpjm_member_profile_resumes_tab' );
			}

			$this->loader->add_filter( 'page_template', $plugin_public, 'bpjm_job_application_page' );

			// Before WP Job Manager's own `wp` handler, which needs a submission page we do not have.
			$this->loader->add_action( 'wp', $plugin_public, 'bpjm_handle_submission_actions', 9 );
			$this->loader->add_action( 'wp', $plugin_public, 'bpjm_handle_resume_relist', 9 );
			$this->loader->add_action( 'template_redirect', $plugin_public, 'bpjm_redirect_old_applied_jobs', 5 );
			$this->loader->add_action( 'transition_post_status', $plugin_public, 'bpjm_notify_application_status', 10, 3 );
			$this->loader->add_action( 'job_manager_job_submitted_content_after', $plugin_public, 'bpjm_job_submitted_next_step' );
			$this->loader->add_filter( 'wp_redirect', $plugin_public, 'bpjm_keep_resume_save_in_profile' );
			$this->loader->add_action( 'wp', $plugin_public, 'bpjm_shortcode_action_handler' );
			$this->loader->add_action( 'wp', $plugin_public, 'bpjm_modify_job_alert_action_handler' );
			$this->loader->add_filter( 'job_manager_pagination_args', $plugin_public, 'bpjm_pagination_args' );
			$this->loader->add_action( 'bp_template_content', $plugin_public, 'bpjm_screen_open', 1 );
			$this->loader->add_action( 'bp_template_content', $plugin_public, 'bpjm_screen_close', 999 );

			$this->loader->add_filter( 'job_manager_user_can_edit_published_submissions', $plugin_public, 'bpjm_filter_wpjm_user_can_edit_published_submissions', 10, 1 );

			$this->loader->add_filter( 'job_manager_user_can_edit_pending_submissions', $plugin_public, 'bpjm_allow_user_to_edit_pending_jobs', 10, 1 );
			$this->loader->add_filter( 'job_manager_my_job_actions', $plugin_public, 'bpjm_job_dashboard_job_actions', 10, 2 );
			$this->loader->add_filter( 'resume_manager_get_dashboard_resumes_args', $plugin_public, 'bpjm_resume_dashboard_user_id', 10, 1 );
			$this->loader->add_filter( 'resume_manager_my_resume_actions', $plugin_public, 'bpjm_candidate_dashboard_resume_actions', 10, 2 );

			/* Action to add private message link on candidate contact button */
			$this->loader->add_action( 'resume_manager_contact_details', $plugin_public, 'bpjm_add_private_message_link' );

			/*
			 * Always the canonical hook.
			 *
			 * This used to branch on has_action( 'new_job_application' ), which
			 * answers "has anything else hooked the legacy name?" - not "is the
			 * Applications add-on active?", the question actually being asked.
			 * At plugins_loaded nothing has hooked it yet, so the test happened
			 * to fall through to the right branch; the moment any third-party
			 * plugin registered on the legacy name, this bound to the
			 * deprecated hook instead.
			 *
			 * Applications fires BOTH names back to back with identical args
			 * (class-wp-job-manager-applications-apply.php), the legacy one via
			 * do_action_deprecated() since 2.5.3 - so the legacy branch still
			 * worked, but dragged a _deprecated_hook() notice along with it.
			 * Binding both would double-post the notification. One hook, always.
			 */
			$this->loader->add_action( 'job_manager_applications_new_job_application', $plugin_public, 'bpjm_add_bp_notification_for_job_post', 10, 2 );

			$this->loader->add_filter( 'job_manager_should_run_shortcode_action_handler', $plugin_public, 'bpjm_allow_continue_submission', 5, 1 );

			/* add component for notification. */
			$this->loader->add_filter( 'bp_notifications_get_registered_components', $plugin_public, 'bpjm_comment_get_registered_components' );

			$this->loader->add_filter( 'bp_notifications_get_notifications_for_user', $plugin_public, 'bpjm_job_application_notifications', 11, 7 );
			/*
			 * `job_manager_user_can_post_job` is deliberately NOT filtered.
			 *
			 * We used to override WP Job Manager's answer with our own role list,
			 * which is the second permission system removed in 3.0.0. Re-adding a
			 * filter here would also recurse: bpjm_user_can_post_job() asks WP Job
			 * Manager via this very hook, so a callback that calls it back would
			 * loop until the request exhausts memory.
			 */

			/*
			 * `submit_job_form_show_signin` is filtered for ONE case only: the
			 * profile owner, on their own profile, who is obviously already
			 * signed in. See bpjm_hide_signin_on_own_profile().
			 *
			 * It must stay for everyone else. This used to be filtered through
			 * bpjm_restrict_user_post_job(), which answered a different question
			 * ("may this role post?"), and the result was that a logged-out
			 * visitor who could not post as a guest had the form hidden AND the
			 * sign-in box hidden - nothing but a "not authorized" notice and no
			 * way to log in.
			 */
			$this->loader->add_filter( 'submit_job_form_show_signin', $plugin_public, 'bpjm_hide_signin_on_own_profile' );
			$this->loader->add_filter( 'submit_resume_form_show_signin', $plugin_public, 'bpjm_hide_signin_on_own_profile' );
			// `submit_job_form_disabled` expects the callback to echo. The
			// bare bpjm_restrict_user_post_job_message() only RETURNS the
			// string (bp_core_add_message() needs the value), so this action
			// goes through the printing wrapper.
			$this->loader->add_action( 'submit_job_form_disabled', $plugin_public, 'bpjm_print_restrict_user_post_job_message' );
			/*
			 * Standalone BP Rewrites plugin only - deliberately NOT extended to
			 * BuddyPress 12+, even though 12 merged rewrites into core.
			 *
			 * Both callbacks are written against the standalone plugin:
			 * bpjm_legacy_link_fix() removes a filter in the `BP\Rewrites\`
			 * namespace, which only that plugin declares, and
			 * bpjm_nouveau_link_fix() is built on bp_loggedin_user_domain() /
			 * bp_displayed_user_domain(), both deprecated in 12. Widening the
			 * gate to version_compare( BP_VERSION, '12.0.0', '>=' ) would run
			 * pre-12 compatibility shims against core's own rewrites and
			 * against a namespace that is not there.
			 *
			 * This is not dead code: the plugin still supports BP < 12 (profile
			 * URLs are built with bp_core_get_userlink(), which predates 12), and
			 * on those installs the standalone plugin is exactly what this gate
			 * is for.
			 */
			if ( bpjm_is_plugin_active( 'bp-rewrites/class-bp-rewrites.php' ) ) {
				$this->loader->add_filter( 'bp_nouveau_get_nav_link', $plugin_public, 'bpjm_nouveau_link_fix', 10, 2 );
				$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bpjm_legacy_link_fix', 9999 );
			}
		}


		/**
		 * Registers a global variable of the plugin - $bpjm_bp_job_manager
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		public function define_globals() {
			global $bpjm_bp_job_manager;
			// Bp_Job_Manager_Globals::__construct() takes no arguments — it derives
			// the plugin name and reads BPJM_PLUGIN_VERSION itself, so passing them
			// here was silently discarded.
			$bpjm_bp_job_manager = new Bp_Job_Manager_Globals();
		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->loader->run();
		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    Bp_Job_Manager_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since     1.0.0
		 * @return    string    The version number of the plugin.
		 */
		public function get_version() {
			return $this->version;
		}
	}
endif;
