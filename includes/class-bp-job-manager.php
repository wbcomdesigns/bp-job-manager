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
		$this->version     = '1.0.0';

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-job-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-job-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-job-manager-admin.php';

		/**
		 * The class responsible for defining the global variable of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-job-manager-globals.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-job-manager-public.php';

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

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bp_Job_Manager_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpjm_add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpjm_general_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpjm_support' );
		$this->loader->add_action( 'bp_setup_admin_bar', $plugin_admin, 'bpjm_setup_admin_bar_links', 70 );

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
		$this->loader->add_action( 'bp_setup_nav', $plugin_public, 'bpjm_member_profile_resumes_tab' );
		$this->loader->add_filter( 'page_template', $plugin_public, 'bpjm_job_application_page' );
		$this->loader->add_filter( 'job_manager_get_dashboard_jobs_args', $plugin_public, 'bpjm_job_dashboard_user_id', 10, 1 );
		$this->loader->add_filter( 'job_manager_user_can_edit_pending_submissions', $plugin_public, 'bpjm_allow_user_to_edit_pending_jobs', 10, 1 );
		$this->loader->add_filter( 'job_manager_my_job_actions', $plugin_public, 'bpjm_job_dashboard_job_actions', 10, 2 );
		$this->loader->add_filter( 'job_manager_job_dashboard_columns', $plugin_public, 'bpjm_job_dashboard_cols', 10, 1 );
		$this->loader->add_action( 'job_manager_job_dashboard_column_actions', $plugin_public, 'bpjm_job_dashboard_actions_col_content', 10, 1 );
		$this->loader->add_filter( 'resume_manager_get_dashboard_resumes_args', $plugin_public, 'bpjm_resume_dashboard_user_id', 10, 1 );
		$this->loader->add_filter( 'resume_manager_my_resume_actions', $plugin_public, 'bpjm_candidate_dashboard_resume_actions', 10, 2 );

	}

	/**
	 * Registers a global variable of the plugin - $bp_job_manager
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function define_globals() {
		global $bp_job_manager;
		$bp_job_manager = new Bp_Job_Manager_Globals( $this->get_plugin_name(), $this->get_version() );
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
