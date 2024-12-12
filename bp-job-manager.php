<?php
/**
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Bp_Job_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Job Manager
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       This plugin integrates WordPress Job Manager with BuddyPress. Allows the members to post jobs, and others to apply for those posted jobs.
 * Version:           2.8.2
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

if ( ! defined( 'BPJM_PLUGIN_PATH' ) ) {
	define( 'BPJM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BPJM_PLUGIN_URL' ) ) {
	define( 'BPJM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BPJM_PLUGIN_VERSION' ) ) {
	define( 'BPJM_PLUGIN_VERSION', '2.8.2' );
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
	if ( current_user_can( 'activate_plugins' ) && ( ! class_exists( 'BuddyPress' ) || ! class_exists( 'WP_Job_Manager' ) ) ) {
		add_action( 'admin_notices', 'bpjm_required_plugin_admin_notice' );
		add_action( 'admin_init', 'wpbpjm_existing_checkin_plugin' );
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
	$bpjm_plugin = esc_html__( 'BuddyPress Job Manager', 'bp-job-manager' );
	$bp_plugin   = esc_html__( 'BuddyPress', 'bp-job-manager' );
	$wpjm_plugin = esc_html__( 'WP Job Manager', 'bp-job-manager' );
	echo '<div class="error"><p>';
	echo sprintf(
		/* translators: %1$s: BuddyPress Job Manager ;  %2$s: BuddyPress ; %3$s: WP Job Manager*/
		esc_html__( '%1$s is ineffective now as it requires %2$s and %3$s to be installed and active.', 'bp-job-manager' ),
		'<strong>' . esc_html( $bpjm_plugin ) . '</strong>',
		'<strong>' . esc_html( $bp_plugin ) . '</strong>',
		'<strong>' . esc_html( $wpjm_plugin ) . '</strong>'
	);
	echo '</p></div>';
	if ( filter_input( INPUT_GET, 'activate' ) ) {
		unset( $_GET['activate'] );
	}
}

/**
 * Function to remove BP Job Manager plugin if already exist.
 *
 * @since 1.0.0
 */
function wpbpjm_existing_checkin_plugin() {
	// Check to see if plugin is already active.
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
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
		'<a href="' . admin_url( 'admin.php?page=bp-job-manager' ) . '">' . esc_html__( 'Settings', 'bp-job-manager' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . esc_html__( 'Support', 'bp-job-manager' ) . '</a>',
	);
	return array_merge( $links, $bpjm_links );
}


/**
 * Redirect to plugin settings page after activation
 */
function bpjm_activation_redirect_settings( $plugin ) {

    // Verify nonce for security
    if ( isset( $_REQUEST['_wpnonce'] ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bpjm_activation_nonce' ) ) {
        return; // Exit if nonce is invalid
    }

    if ( isset( $_REQUEST['action'], $_REQUEST['plugin'] ) && $_REQUEST['plugin'] === $plugin ) {
        if ( class_exists( 'BuddyPress' ) && class_exists( 'WP_Job_Manager' ) && plugin_basename( __FILE__ ) === $plugin ) {
            if ( $_REQUEST['action'] === 'activate' ) {
                wp_safe_redirect( admin_url( 'admin.php?page=bp-job-manager' ) );
                exit;
            }
        }
    
        if ( $_REQUEST['action'] === 'activate-plugin' && class_exists( 'BuddyPress' ) ) {
            set_transient( '_bpjm_is_new_install', true, 30 );
        }
    }
}
add_action( 'activated_plugin', 'bpjm_activation_redirect_settings' );

// Enqueue nonce field for the plugin activation form (if needed elsewhere)
function bpjm_add_activation_nonce() {
    wp_nonce_field( 'bpjm_activation_nonce', '_wpnonce' );
}

/**
 * bpjm_do_activation_redirect
 *
 * @return void
 */
function bpjm_do_activation_redirect() {
	if ( get_transient( '_bpjm_is_new_install' ) ) {
		delete_transient( '_bpjm_is_new_install' );
		wp_safe_redirect( admin_url( 'admin.php?page=bp-job-manager' ) );

	}
}
add_action( 'admin_init', 'bpjm_do_activation_redirect' );
