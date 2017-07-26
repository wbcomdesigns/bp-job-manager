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
      'admin/wpbpjm-admin.php',
      'inc/wpbpjm-globals.php',
      'inc/wpbpjm-ajax.php',
      'admin/widgets/wpbpjm-job-applications/wpbpjm-job-applications-widget.php'
  );
  foreach( $include_files as $include_file ) include $include_file;

  //Initialize admin class
  new Wpbpjm_AdminPage();
  
  //Initialize globals class
  global $bp_job_manager;
  $bp_job_manager = new Wpbpjm_Globals();
}

function wpbpjm_admin_settings_link( $links ) {
    $settings_link = array( '<a href="'.admin_url('options-general.php?page=bp-job-manager-settings').'">'.__( 'Settings', WPBPJM_TEXT_DOMAIN ).'</a>' );
    return array_merge( $links, $settings_link );
}

/**
 * Check plugin requirement on plugins loaded
 * this plugin requires the following plugins
 * BuddyPress, WP Job Manager, WP Job Manager Applications & WP Job Manager Resumes
 * to be installed and active.
 */
function wpbpjm_plugin_init() {
  global $bp_job_manager;
  $wpjm_active = in_array('wp-job-manager/wp-job-manager.php', get_option('active_plugins'));
  $bp_active = in_array('buddypress/bp-loader.php', get_option('active_plugins'));
  $wpjm_applications_active = in_array('wp-job-manager-applications/wp-job-manager-applications.php', get_option('active_plugins'));
  $wpjm_resumes_active = in_array('wp-job-manager-resumes/wp-job-manager-resumes.php', get_option('active_plugins'));
  if ( current_user_can('activate_plugins') && ( $wpjm_active !== true || $bp_active !== true || $wpjm_applications_active !== true || $wpjm_resumes_active !== true ) ) {
    add_action('admin_notices', 'wpbpjm_plugin_admin_notice');
  } else {
    if (!defined('WPBPJM_PLUGIN_BASENAME')) {
        define('WPBPJM_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }
    run_wp_bp_job_manager();
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpbpjm_admin_settings_link' );

    if( empty( $bp_job_manager->job_user_roles ) || empty( $bp_job_manager->resume_user_roles ) ) {
      add_action( 'admin_notices', 'wpbpjm_empty_admin_settings_notice' );
    }
  }
}
add_action('plugins_loaded', 'wpbpjm_plugin_init');

// Throw an Alert to tell the Admin why it didn't activate
function wpbpjm_plugin_admin_notice() {
    $wpbpjm_plugin = 'BP Job Manager';
    $bp_plugin = 'BuddyPress';
    $wpjm_plugin = 'WP Job Manager';
    $wpjm_applications_plugin = 'WP Job Manager - Applications';
    $wpjm_resumes_plugin = 'WP Job Manager - Resume Manager';
    echo '<div class="error"><p>'
    . sprintf(__('%1$s is ineffective now as it requires %2$s, %3$s, %4$s and %5$s to function correctly.', WPBPJM_TEXT_DOMAIN), '<strong>' . esc_html($wpbpjm_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>', '<strong>' . esc_html($wpjm_plugin) . '</strong>', '<strong>' . esc_html($wpjm_applications_plugin) . '</strong>', '<strong>' . esc_html($wpjm_resumes_plugin) . '</strong>')
    . '</p></div>';
    if (isset($_GET['activate'])) unset($_GET['activate']);
}

// Throw an Alert to tell the Admin that the admin settings are blank, which needs to be saved
function wpbpjm_empty_admin_settings_notice() {
    echo '<div class="error"><p>Please set the user roles that can manage the jobs and resumes on your site. Make it <a href="'.admin_url('options-general.php?page=bp-job-manager-settings').'" title="BP Job Manager Settings">here</a>.</p></div>';
}