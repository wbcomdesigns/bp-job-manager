<?php
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
			if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
			}
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
				wp_enqueue_style( 'wbcom-admin-setting-css', BPJM_PLUGIN_URL . 'admin/wbcom/assets/css/wbcom-admin-setting.css' );
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
			$page          = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : 'wbcom-themes-page';
			$plugin_active = $theme_active = $support_active = $settings_active = '';
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
								<i class="fa fa-sliders" aria-hidden="true"></i>
								<h4><?php esc_html_e( 'Settings', 'bp-job-manager' ); ?></h4>
							</a>
						</li>
						<li class="wb_admin_nav_item <?php echo esc_attr( $plugin_active ); ?>">
							<a href="<?php echo esc_url( get_admin_url() ) . 'admin.php?page=wbcom-plugins-page'; ?>" id="wb_admin_nav_trigger_extensions">
								<i class="fa fa-th" aria-hidden="true"></i>
								<h4><?php esc_html_e( 'Themes & Extension', 'bp-job-manager' ); ?></h4>
							</a>
						</li>
						<li class="wb_admin_nav_item <?php echo esc_attr( $support_active ); ?>">
							<a href="<?php echo esc_url( get_admin_url() ) . 'admin.php?page=wbcom-support-page'; ?>" id="wb_admin_nav_trigger_support">
								<i class="fa fa-question-circle" aria-hidden="true"></i>
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
	function instantiate_wbcom_plugin_manager() {
		new Wbcom_Admin_Settings();
	}

	instantiate_wbcom_plugin_manager();
}
