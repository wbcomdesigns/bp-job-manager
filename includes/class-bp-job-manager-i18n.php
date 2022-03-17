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
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain(
				'bp-job-manager',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);

		}



	}
endif;
