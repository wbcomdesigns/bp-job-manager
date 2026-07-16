<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
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

if ( ! class_exists( 'Bp_Job_Manager_I18n' ) ) :
	/**
	 * Define the internationalization functionality.
	 *
	 * Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * @since      1.0.0
	 * @package    Bp_Job_Manager
	 * @subpackage Bp_Job_Manager/includes
	 * @author     Wbcom Designs <admin@wbcomdesigns.com>
	 */
	class Bp_Job_Manager_I18n {

		/**
		 * Load the plugin text domain for translation.
		 *
		 * WordPress only auto-loads language packs that exist on
		 * translate.wordpress.org. This plugin ships locales that are not
		 * available there, so the bundled files in languages/ must still be
		 * registered. load_plugin_textdomain() checks the WordPress.org pack
		 * first and falls back to the bundled file, so both paths work.
		 *
		 * Must run on init (not earlier): loading a text domain before init
		 * triggers _load_textdomain_just_in_time on WordPress 6.7+.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain(
				'bp-job-manager',
				false,
				basename( BPJM_PLUGIN_PATH ) . '/languages/'
			);
		}
	}
endif;
