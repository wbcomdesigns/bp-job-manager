<?php
/**
 * Wbcom Designs - BuddyPress Job Manager.
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Bp_Job_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Designs - BuddyPress Job Manager
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       This plugin integrates WordPress Job Manager with BuddyPress. Allows the members to post jobs, and others to apply for those posted jobs.
 * Version:           3.0.1
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-job-manager
 * Domain Path:       /languages
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Requires Plugins:  wp-job-manager
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
	define( 'BPJM_PLUGIN_VERSION', '3.0.1' );
}


/**
 * Runs on activation.
 *
 * Sets one 30-second transient and nothing else. This plugin is a bridge: it
 * stores no jobs and no resumes, owns no tables and no post types, and since
 * 3.0.0 writes nothing but its settings option.
 *
 * Before 3.0.0 this delegated to Bpjm_Job_Manager_Activator, which created an
 * "Apply To Job" page in the customer's site and looped every user to stamp a
 * meta on each. Both are gone - the page because a bridge should not author
 * content, the loop because per-user state written at activation only ever
 * covers users who existed at that moment and silently misses everyone who
 * registers later. The activator and the empty deactivator class went with
 * them.
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 */
function bpjm_activate_bp_job_manager() {
	// Flag a fresh activation so the next admin page load can redirect to the
	// settings screen (handled by bpjm_do_activation_redirect on admin_init).
	set_transient( '_bpjm_is_new_install', true, 30 );
}

register_activation_hook( __FILE__, 'bpjm_activate_bp_job_manager' );

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
function bpjm_run_bp_job_manager() {

	$plugin = new Bp_Job_Manager();
	$plugin->run();
}



/**
 * Check plugin requirement on plugins loaded
 * this plugin requires the following plugins
 * BuddyPress, WP Job Manager, WP Job Manager Applications & WP Job Manager Resumes
 * to be installed and active.
 */
add_action( 'plugins_loaded', 'bpjm_plugin_init' );

/**
 * Check plugin requirement on plugins loaded.
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 */
function bpjm_plugin_init() {
	/*
	 * Whether the dependencies are present is a property of the SITE, never of
	 * the current user. This condition used to be ANDed with
	 * current_user_can( 'activate_plugins' ), so for a logged-out visitor the
	 * whole test collapsed to false and execution fell through to the "run the
	 * plugin" branch with BuddyPress missing. Every bp_*() call then fataled -
	 * the first fault being bpjm_allow_continue_submission() calling
	 * bp_is_my_profile() on WP Job Manager's
	 * job_manager_should_run_shortcode_action_handler filter, which runs on
	 * `wp`, i.e. before wp_enqueue_scripts. Result: HTTP 500 on the front end
	 * for everyone except administrators.
	 *
	 * BuddyPress is checked here, not in `Requires Plugins`: that header names a
	 * plugin folder, so `buddypress` blocked activation on BuddyBoss Platform,
	 * which also provides the BuddyPress class this check accepts.
	 *
	 * The `Requires Plugins` header (WP 6.5+) does NOT cover this. Core only
	 * consults it in validate_plugin_requirements() on the activation path, so
	 * it blocks activation but cannot stop an already-active plugin whose
	 * dependency was removed from disk - a failed update, an FTP delete or a
	 * renamed folder. That is precisely the case this guard has to survive.
	 */
	if ( ! class_exists( 'BuddyPress' ) || ! class_exists( 'WP_Job_Manager' ) ) {
		add_action( 'admin_notices', 'bpjm_required_plugin_admin_notice' );
		add_action( 'admin_init', 'bpjm_existing_checkin_plugin' );
		return;
	}

	if ( ! defined( 'BPJM_PLUGIN_BASENAME' ) ) {
		define( 'BPJM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	}
	bpjm_run_bp_job_manager();
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bpjm_plugin_links' );
}

/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author  wbcomdesigns
 * @since   1.0.0
 */
function bpjm_required_plugin_admin_notice() {
	// Only someone who can act on the notice is shown it. This check lives here
	// rather than at the plugins_loaded gate above so that the dependency test
	// itself stays user-independent.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$bpjm_plugin = esc_html__( 'BuddyPress Job Manager', 'bp-job-manager' );
	$bp_plugin   = 'BuddyPress';
	$wpjm_plugin = 'WP Job Manager';
	echo '<div class="error"><p>';
	printf(
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
 * Self-deactivate once the dependencies are gone.
 *
 * Deliberately stays on `admin_init`, and deliberately does NOT move to `init`
 * or `plugins_loaded`. Two reasons:
 *
 * 1. The front-end exposure this was meant to contain no longer exists. With
 *    the dependency gate in bpjm_plugin_init() fixed, a site missing
 *    BuddyPress or WP Job Manager simply does not boot this plugin, so there
 *    is nothing left to protect visitors from - deactivating sooner buys
 *    nothing.
 * 2. deactivate_plugins() and is_plugin_active() live in
 *    wp-admin/includes/plugin.php, which is not loaded on front-end requests,
 *    and mutating the active_plugins option from an anonymous request is a
 *    write that any number of concurrent visitors could race. Core itself only
 *    ever changes plugin state from the admin.
 *
 * @since 1.0.0
 */
function bpjm_existing_checkin_plugin() {
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
 * @param  array $links contains plugin's setting links.
 * @return array The action links with this plugin's entries appended.
 */
function bpjm_plugin_links( $links ) {
	$bpjm_links = array(
		'<a href="' . admin_url( 'admin.php?page=bp-job-manager' ) . '">' . esc_html__( 'Settings', 'bp-job-manager' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . esc_html__( 'Support', 'bp-job-manager' ) . '</a>',
	);
	return array_merge( $links, $bpjm_links );
}


/**
 * Redirect to the plugin settings page after a fresh single activation.
 *
 * The activation hook (bpjm_activate_bp_job_manager) sets the
 * _bpjm_is_new_install transient. This one-shot admin_init handler consumes it
 * on the next admin page load and redirects, skipping bulk activation and
 * running only while both dependencies are present.
 *
 * @return void
 */
function bpjm_do_activation_redirect() {
	if ( ! get_transient( '_bpjm_is_new_install' ) ) {
		return;
	}

	// One-shot: clear the flag before any early return so it never loops.
	delete_transient( '_bpjm_is_new_install' );

	// Skip bulk activation - WordPress adds ?activate-multi to that redirect.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only presence check; WordPress core already verified the activation nonce before firing the activation hook.
	if ( isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Dependencies are required for the settings screen to exist.
	if ( ! class_exists( 'BuddyPress' ) || ! class_exists( 'WP_Job_Manager' ) ) {
		return;
	}

	wp_safe_redirect( admin_url( 'admin.php?page=bp-job-manager' ) );
	exit;
}
add_action( 'admin_init', 'bpjm_do_activation_redirect' );
