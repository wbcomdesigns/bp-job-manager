<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Class to add custom scripts and styles
 */
if( !class_exists( 'Wpbpjm_ScriptsStyles' ) ) {
	class Wpbpjm_ScriptsStyles{

		/**
		 * Constructor
		 */
		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'wpbpjm_front_scripts_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wpbpjm_admin_scripts_styles' ) );
		}

		/**
		 * Actions performed for enqueuing scripts and styles for site front
		 */
		function wpbpjm_front_scripts_styles() {
			wp_enqueue_style( 'wpbpjm-front-css', WPBPJM_PLUGIN_URL.'assets/css/wpbpjm-front.css' );
			wp_enqueue_script( 'wpbpjm-front-js', WPBPJM_PLUGIN_URL.'assets/js/wpbpjm-front.js', array( 'jquery' ) );

			wp_localize_script(
				'wpbpjm-front-js',
				'wpbpjm_front_js_object',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'ajax_loader' => WPBPJM_PLUGIN_URL.'assets/images/Maelstorm.gif'
				)
			);

			$handle = 'wpbpjm-fa';
			$list = 'enqueued';
			if (!wp_script_is( $handle, $list )) {
				wp_enqueue_style( 'wpbpjm-fa',WPBPJM_PLUGIN_URL.'assets/css/font-awesome.min.css' );
			}
		}

		/**
		 * Actions performed for enqueuing scripts and styles for site admin
		 */
		function wpbpjm_admin_scripts_styles() {
			wp_enqueue_style( 'wpbpjm-admin-css', WPBPJM_PLUGIN_URL.'admin/assets/css/wpbpjm-admin.css' );
			wp_enqueue_script( 'wpbpjm-admin-js', WPBPJM_PLUGIN_URL.'admin/assets/js/wpbpjm-admin.js', array( 'jquery' ) );
		}
	}
	new Wpbpjm_ScriptsStyles();
}
