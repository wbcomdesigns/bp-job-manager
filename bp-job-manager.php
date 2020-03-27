<?php
/**
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Bp_Job_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Job Manager
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       This plugin integrates WordPress Job Manager with BuddyPress. Allows the members to post jobs, and others to apply for those posted jobs.
 * Version:           2.3.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-job-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation. This action is documented in includes/class-bp-job-manager-activator.php
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 */
function activate_bp_job_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-job-manager-activator.php';
	Bp_Job_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bp-job-manager-deactivator.php
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 */
function deactivate_bp_job_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-job-manager-deactivator.php';
	Bp_Job_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bp_job_manager' );
register_deactivation_hook( __FILE__, 'deactivate_bp_job_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bp-job-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_job_manager() {

	if ( ! defined( 'BPJM_PLUGIN_PATH' ) ) {
		define( 'BPJM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	}

	if ( ! defined( 'BPJM_PLUGIN_URL' ) ) {
		define( 'BPJM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	$plugin = new Bp_Job_Manager();
	$plugin->run();

}



/**
 * Check plugin requirement on plugins loaded
 * this plugin requires the following plugins
 * BuddyPress, WP Job Manager, WP Job Manager Applications & WP Job Manager Resumes
 * to be installed and active.
 */
add_action( 'plugins_loaded', 'wpbpjm_plugin_init' );

/**
 * Check plugin requirement on plugins loaded.
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 */
function wpbpjm_plugin_init() {
	$wpjm_active              = in_array( 'wp-job-manager/wp-job-manager.php', get_option( 'active_plugins' ) );
	$bp_active                = in_array( 'buddypress/bp-loader.php', get_option( 'active_plugins' ) );
	// $wpjm_applications_active = in_array( 'wp-job-manager-applications/wp-job-manager-applications.php', get_option( 'active_plugins' ) );
	// $wpjm_resumes_active      = in_array( 'wp-job-manager-resumes/wp-job-manager-resumes.php', get_option( 'active_plugins' ) );

	if ( current_user_can( 'activate_plugins' ) && ( true !== $wpjm_active || true !== $bp_active ) ) {
		add_action( 'admin_notices', 'bpjm_required_plugin_admin_notice' );
	} else {
		if ( ! defined( 'BPJM_PLUGIN_BASENAME' ) ) {
			define( 'BPJM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}
		run_bp_job_manager();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpjm_plugin_links' );
	}
}

/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 */
function bpjm_required_plugin_admin_notice() {
	$bpjm_plugin              = esc_html__( 'BuddyPress Job Manager', 'bp-job-manager' );
	$bp_plugin                = esc_html__( 'BuddyPress', 'bp-job-manager' );
	$wpjm_plugin              = esc_html__( 'WP Job Manager', 'bp-job-manager' );
	$wpjm_applications_plugin = esc_html__( 'WP Job Manager - Applications', 'bp-job-manager' );
	$wpjm_resumes_plugin      = esc_html__( 'WP Job Manager - Resume Manager', 'bp-job-manager' );
	echo '<div class="error"><p>';
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s, %3$s, %4$s and %5$s to be installed and active.', 'bp-job-manager' ), '<strong>' . esc_html( $bpjm_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>', '<strong>' . esc_html( $wpjm_plugin ) . '</strong>', '<strong>' . esc_html( $wpjm_applications_plugin ) . '</strong>', '<strong>' . esc_html( $wpjm_resumes_plugin ) . '</strong>' );
	echo '</p></div>';
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}

/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 * @param  string $links contains plugin's setting links.
 */
function bpjm_plugin_links( $links ) {
	$bpjm_links = array(
		'<a href="' . admin_url("admin.php?page=bp-job-manager") . '">' . esc_html__( 'Settings', 'bp-job-manager' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . esc_html__( 'Support', 'bp-job-manager' ) . '</a>',
	);
	return array_merge( $links, $bpjm_links );
}
