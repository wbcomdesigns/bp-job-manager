<?php
/**
 * Plugin Name: BP Job Manager
 * Description: This plugin manages user's jobs.
 * Version: 1.0.0
 * Author: Wbcom Designs
 * Author URI: https://wbcomdesigns.com/
 * License: GPLv2+
 * Text Domain: wp-bp-job-manager
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Constants used in the plugin
 */
define( 'WPBPJM_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'WPBPJM_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'WPBPJM_TEXT_DOMAIN', 'wp-bp-job-manager' );

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
add_action( 'init', 'wpbpjm_load_textdomain' );
function wpbpjm_load_textdomain() {
  $domain = WPBPJM_TEXT_DOMAIN;
  $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
  load_textdomain( $domain, 'languages/'.$domain.'-' . $locale . '.mo' );
  $var = load_plugin_textdomain( $domain, false, plugin_basename( dirname(__FILE__) ) . '/languages' );
}

function run_wp_bp_job_manager() {
  /**
   * Include needed files
   */
  $include_files = array(
    'inc/wpbpjm-scripts.php',
    'inc/wpbpjm-profile-jobs.php',
    'inc/wpbpjm-profile-saved-jobs.php',
    'inc/wpbpjm-profile-resumes.php',
    'inc/wpbpjm-hooks.php',
    'inc/wpbpjm-ajax.php',
    'admin/widgets/wpbpjm-job-applications/wpbpjm-job-applications-widget.php'
  );
  foreach( $include_files as $include_file ) {
    include $include_file;
  }
}

function wpbpjm_settings_link( $links ) {
	$settings_link = array( '<a href="javascript:void(0);">Settings</a>' );
	return array_merge( $links, $settings_link );
}

/**
 * Check plugin requirement on plugins loaded
 * this plugin requires buddypress and woocommerce to be installed and active
 */
function wpbpjm_plugin_init() {
  // If BuddyPress && WP Job Manager is NOT active
  $wpjm_active = in_array('wp-job-manager/wp-job-manager.php', get_option('active_plugins'));
  $bp_active = in_array('buddypress/bp-loader.php', get_option('active_plugins'));

  if ( current_user_can('activate_plugins') && ( $wpjm_active !== true || $bp_active !== true ) ) {
    add_action('admin_notices', 'wpbpjm_plugin_admin_notice');
  } else {
    if (!defined('WPBPJM_PLUGIN_BASENAME')) {
        define('WPBPJM_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }
    run_wp_bp_job_manager();
    //Settings link for this plugin
    //add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpbpjm_settings_link' );
  }
}
add_action('plugins_loaded', 'wpbpjm_plugin_init');

// Throw an Alert to tell the Admin why it didn't activate
function wpbpjm_plugin_admin_notice() {
    $wpbpjm_plugin = __( 'BP Job Manager', WPBPJM_TEXT_DOMAIN );
    $bp_plugin = __( 'BuddyPress', WPBPJM_TEXT_DOMAIN );
    $wpjm_plugin = __( 'WP Job Manager', WPBPJM_TEXT_DOMAIN );
    $wpjm_applications_plugin = __( 'WP Job Manager - Applications', WPBPJM_TEXT_DOMAIN );
    $wpjm_resumes_plugin = __( 'WP Job Manager - Resume Manager', WPBPJM_TEXT_DOMAIN );
    echo '<div class="error"><p>'
    . sprintf(__('%1$s requires %2$s, %3$s, %4$s and %5$s to function correctly. Please activate %2$s and %3$s before activating %1$s.', WPBPJM_TEXT_DOMAIN), '<strong>' . esc_html($wpbpjm_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>', '<strong>' . esc_html($wpjm_plugin) . '</strong>', '<strong>' . esc_html($wpjm_applications_plugin) . '</strong>', '<strong>' . esc_html($wpjm_resumes_plugin) . '</strong>')
    . '</p></div>';
    if (isset($_GET['activate'])) unset($_GET['activate']);
}
