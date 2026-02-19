<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Shared Wbcom admin file.
/**
 * Class to add top header pages of wbcom plugin and additional features.
 *
 * @author   Wbcom Designs
 * @package  Bp_Add_Group_Types
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wbcom_Admin_Settings' ) ) {

	/**
	 * Class to add wbcom plugin's admin settings.
	 *
	 * @author   Wbcom Designs
	 * @since    2.0.0
	 */
	class Wbcom_Admin_Settings {

		/**
		 * Wbcom_Admin_Settings Constructor.
		 *
		 * @since 2.0.0
		 * @access public
		 */
		public function __construct() {
			add_shortcode( 'wbcom_admin_setting_header', array( $this, 'wbcom_admin_setting_header_html' ) );
			add_action( 'admin_menu', array( $this, 'wbcom_admin_additional_pages' ), 999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'wbcom_enqueue_admin_scripts' ) );
			add_action( 'wp_ajax_wbcom_addons_cards', array( $this, 'wbcom_addons_cards_links' ) );
		}

		/**
		 * Extensions cards callback function.
		 *
		 * @return void
		 */
		public function wbcom_addons_cards_links() {
			$wbcom_setting_nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			$action              = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
			if ( ! empty( $wbcom_setting_nonce ) && wp_verify_nonce( $wbcom_setting_nonce, 'wbcom_admin_setting_nonce' ) && 'wbcom_addons_cards' === $action ) {
				$display_extention = isset( $_POST['display_extension'] ) ? sanitize_text_field( wp_unslash( $_POST['display_extension'] ) ) : '';
				echo esc_html( $display_extention );
				die;
			}
		}

		/**
		 * Function for upgrade plugin.
		 *
		 * @since 2.0.0
		 * @access public
		 * @param string $plugin_slug Plugin's slug.
		 */
		public function upgrade_plugin( $plugin_slug ) {
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			wp_cache_flush();

			$upgrader = new Plugin_Upgrader();
			$upgraded = $upgrader->upgrade( $plugin_slug );

			return $upgraded;
		}

		/**
		 * Function for return plugin's WordPress repo download url.
		 *
		 * @since 2.0.0
		 * @access public
		 * @param string $slug Plugin's slug.
		 */
		public function get_download_url( $slug ) {
			return $this->get_wp_repo_download_url( $slug );
		}

		/**
		 * Function for get plugin's WordPress repo download url.
		 *
		 * @since 2.0.0
		 * @access public
		 * @param string $slug Plugin's slug.
		 */
		public function get_wp_repo_download_url( $slug ) {
			$status = array();
			include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // for plugins_api..
			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array( 'sections' => false ),
				)
			); // Save on a bit of bandwidth.

			if ( is_wp_error( $api ) ) {
				$status['error'] = $api->get_error_message();
				wp_send_json_error( $status );
			}

			return $api->download_link;
		}

		/**
		 * Function for check plugin is installed or not.
		 *
		 * @since 2.0.0
		 * @access public
		 * @param string $slug Plugin's slug.
		 */
		public function wbcom_is_plugin_installed( $slug ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$all_plugins = get_plugins();
			$keys        = array_keys( $all_plugins );
			foreach ( $keys as $key ) {
				if ( preg_match( '|^' . $slug . '/|', $key ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Function for check plugin's status.
		 *
		 * @since 2.0.0
		 * @access public
		 * @param string $slug Plugin's slug.
		 */
		public function wbcom_plugin_status( $slug ) {
			if ( $this->wbcom_is_plugin_installed( $slug ) ) {
				if ( $this->wbcom_is_plugin_active( $slug ) ) {
					return 'activated';
				} else {
					return 'installed';
				}
			} else {
				return 'not_installed';
			}
		}

		/**
		 * Function for check plugin is activated or not.
		 *
		 * @since 2.0.0
		 * @access public
		 * @param string $slug Plugin's slug.
		 */
		public function wbcom_is_plugin_active( $slug ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$all_plugins = get_plugins();
			$keys        = array_keys( $all_plugins );
			$response    = false;
			foreach ( $keys as $key ) {
				if ( preg_match( '|^' . $slug . '/|', $key ) ) {
					if ( is_plugin_active( $key ) ) {
						$response = true;
					}
				}
			}
			return $response;
		}

		/**
		 * Enqueue js & css related to wbcom plugin.
		 *
		 * @since 2.0.0
		 * @access public
		 */
		public function wbcom_enqueue_admin_scripts() {
			// Font Awesome removed to comply with WordPress.org guidelines (no external CDN resources).
			// If FontAwesome is needed, it should be included locally in the plugin.
			if ( ! wp_script_is( 'wbcom_admin_setting_js', 'enqueued' ) ) {

				wp_register_script(
					'wbcom_admin_setting_js',
					BPJM_PLUGIN_URL . 'admin/wbcom/assets/js/wbcom-admin-setting.js',
					array( 'jquery' ),
					time(),
					false
				);
				wp_localize_script(
					'wbcom_admin_setting_js',
					'wbcom_plugin_installer_params',
					array(
						'ajax_url'        => admin_url( 'admin-ajax.php' ),
						'activate_text'   => esc_html__( 'Activate', 'bp-job-manager' ),
						'deactivate_text' => esc_html__( 'Deactivate', 'bp-job-manager' ),
						'nonce'           => wp_create_nonce( 'wbcom_admin_setting_nonce' ),
					)
				);
				wp_enqueue_script( 'wbcom_admin_setting_js' );

			}

			if ( ! wp_style_is( 'wbcom-admin-setting-css', 'enqueued' ) ) {
				wp_enqueue_style( 'wbcom-admin-setting-css', BPJM_PLUGIN_URL . 'admin/wbcom/assets/css/wbcom-admin-setting.css', array(), BPJM_PLUGIN_VERSION, 'all' );
			}
		}

		/**
		 * Function for add plugin's admin panel header pages.
		 *
		 * @since 2.0.0
		 * @access public
		 */
		public function wbcom_admin_additional_pages() {
			add_submenu_page(
				'wbcomplugins',
				esc_html__( 'Our Plugins', 'bp-job-manager' ),
				esc_html__( 'Our Plugins', 'bp-job-manager' ),
				'manage_options',
				'wbcom-plugins-page',
				array( $this, 'wbcom_plugins_submenu_page_callback' )
			);
			add_submenu_page(
				'wbcomplugins',
				esc_html__( 'Our Themes', 'bp-job-manager' ),
				esc_html__( 'Our Themes', 'bp-job-manager' ),
				'manage_options',
				'wbcom-themes-page',
				array( $this, 'wbcom_themes_submenu_page_callback' )
			);
			add_submenu_page(
				'wbcomplugins',
				esc_html__( 'Support', 'bp-job-manager' ),
				esc_html__( 'Support', 'bp-job-manager' ),
				'manage_options',
				'wbcom-support-page',
				array( $this, 'wbcom_support_submenu_page_callback' )
			);
		}

		/**
		 * Function for include wbcom plugins list page.
		 *
		 * @since 2.0.0
		 * @access public
		 */
		public function wbcom_plugins_submenu_page_callback() {
			include 'templates/wbcom-plugins-page.php';
		}

		/**
		 * Function for include themes list page.
		 *
		 * @since 2.0.0
		 * @access public
		 */
		public function wbcom_themes_submenu_page_callback() {
			include 'templates/wbcom-themes-page.php';
		}

		/**
		 * Function for include support page.
		 *
		 * @since 2.0.0
		 * @access public
		 */
		public function wbcom_support_submenu_page_callback() {
			include 'templates/wbcom-support-page.php';
		}

		/**
		 * Shortcode for display top menu header.
		 *
		 * @since 2.0.0
		 * @access public
		 */
		public function wbcom_admin_setting_header_html() {
			$page            = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : 'wbcom-themes-page';
			$plugin_active   = '';
			$theme_active    = '';
			$support_active  = '';
			$settings_active = '';
			switch ( $page ) {
				case 'wbcom-plugins-page':
					$plugin_active = 'is_active';
					break;
				case 'wbcom-support-page':
					$support_active = 'is_active';
					break;
				case 'wbcom-license-page':
					$license_active = 'is_active';
					break;
				default:
					$settings_active = 'is_active';
			}
			?>
			<div id="wb_admin_header" class="wp-clearfix">
				<nav id="wb_admin_nav">
					<ul>
						<li class="wb_admin_nav_item <?php echo esc_attr( $settings_active ); ?>">
							<a href="<?php echo esc_url( get_admin_url() ) . 'admin.php?page=wbcomplugins'; ?>" id="wb_admin_nav_trigger_settings">
								<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M6.17071 18C6.58254 16.8348 7.69378 16 9 16C10.3062 16 11.4175 16.8348 11.8293 18H22V20H11.8293C11.4175 21.1652 10.3062 22 9 22C7.69378 22 6.58254 21.1652 6.17071 20H2V18H6.17071ZM12.1707 11C12.5825 9.83481 13.6938 9 15 9C16.3062 9 17.4175 9.83481 17.8293 11H22V13H17.8293C17.4175 14.1652 16.3062 15 15 15C13.6938 15 12.5825 14.1652 12.1707 13H2V11H12.1707ZM6.17071 4C6.58254 2.83481 7.69378 2 9 2C10.3062 2 11.4175 2.83481 11.8293 4H22V6H11.8293C11.4175 7.16519 10.3062 8 9 8C7.69378 8 6.58254 7.16519 6.17071 6H2V4H6.17071ZM9 6C9.55228 6 10 5.55228 10 5C10 4.44772 9.55228 4 9 4C8.44772 4 8 4.44772 8 5C8 5.55228 8.44772 6 9 6ZM15 13C15.5523 13 16 12.5523 16 12C16 11.4477 15.5523 11 15 11C14.4477 11 14 11.4477 14 12C14 12.5523 14.4477 13 15 13ZM9 20C9.55228 20 10 19.5523 10 19C10 18.4477 9.55228 18 9 18C8.44772 18 8 18.4477 8 19C8 19.5523 8.44772 20 9 20Z"></path></svg>
								<h4><?php esc_html_e( 'Settings', 'bp-job-manager' ); ?></h4>
							</a>
						</li>
						<li class="wb_admin_nav_item <?php echo esc_attr( $plugin_active ); ?>">
							<a href="<?php echo esc_url( get_admin_url() ) . 'admin.php?page=wbcom-plugins-page'; ?>" id="wb_admin_nav_trigger_extensions">
								<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3C2.44772 3 2 3.44772 2 4V8L7.5 8V3H3ZM9.5 3V8H14.5V3H9.5ZM16.5 3V8H22V4C22 3.44772 21.5523 3 21 3H16.5ZM22 10H16.5V14H22V10ZM22 16H16.5V21H21C21.5523 21 22 20.5523 22 20V16ZM14.5 21V16H9.5V21H14.5ZM7.5 21V16H2V20C2 20.5523 2.44772 21 3 21H7.5ZM2 14H7.5V10L2 10V14ZM9.5 10H14.5V14H9.5V10Z"></path></svg>
								<h4><?php esc_html_e( 'Themes & Extension', 'bp-job-manager' ); ?></h4>
							</a>
						</li>
						<li class="wb_admin_nav_item <?php echo esc_attr( $support_active ); ?>">
							<a href="<?php echo esc_url( get_admin_url() ) . 'admin.php?page=wbcom-support-page'; ?>" id="wb_admin_nav_trigger_support">
								<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11 15V17H13V15H11ZM13 13.3551C14.4457 12.9248 15.5 11.5855 15.5 10C15.5 8.067 13.933 6.5 12 6.5C10.302 6.5 8.88637 7.70919 8.56731 9.31346L10.5288 9.70577C10.6656 9.01823 11.2723 8.5 12 8.5C12.8284 8.5 13.5 9.17157 13.5 10C13.5 10.8284 12.8284 11.5 12 11.5C11.4477 11.5 11 11.9477 11 12.5V14H13V13.3551Z"></path></svg>
								<h4><?php esc_html_e( 'Help & Support', 'bp-job-manager' ); ?></h4>
							</a>
						</li>
						<?php do_action( 'wbcom_add_header_menu' ); ?>
					</ul>
				</nav>
			</div>
			<?php
		}
	}

	/**
	 * Initialte Wbcom plugin manager.
	 *
	 * @return void
	 */
	function instantiate_wbcom_plugin_manager() { // phpcs:ignore Universal.Files.SeparateFunctionsFromOO.Mixed -- Shared Wbcom admin bootstrap.
		new Wbcom_Admin_Settings();
	}

	instantiate_wbcom_plugin_manager();
}
