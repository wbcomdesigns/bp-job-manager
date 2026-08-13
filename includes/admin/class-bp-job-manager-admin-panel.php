<?php
/**
 * BP Job Manager Admin: menu, enqueue, page rendering (card-panel UI).
 *
 * Replaces the legacy Wbcom wrapper chrome (admin/wbcom/). Follows the
 * wp-plugin-development skill Part 6 card-panel pattern and the
 * references/wbcom-wrapper-migration.md playbook (Parts 5, 6, 7.1, 15).
 *
 * The legacy Bp_Job_Manager_Admin class is retained for its non-UI
 * concerns (settings registration is moved here; activity helpers,
 * admin-bar links, and the sanitizer stay there). This class owns the
 * menu, enqueue, notice-suppression, and page render surface.
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin
 * @since 2.8.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Bp_Job_Manager_Admin_Panel' ) ) :

	/**
	 * Class Bp_Job_Manager_Admin_Panel
	 *
	 * @since 2.8.5
	 */
	class Bp_Job_Manager_Admin_Panel {

		/**
		 * Menu slug (kept identical to legacy so bookmarks + the
		 * activation redirect + plugin-action Settings link still work).
		 */
		const MENU_SLUG = 'bp-job-manager';

		/**
		 * Option group + option name used by register_setting.
		 * Kept byte-identical to the legacy registration so the saved
		 * option row shape is unchanged.
		 */
		const OPTION_GROUP = 'bpjm_general_settings';
		const OPTION_NAME  = 'bpjm_general_settings';

		/**
		 * All sidebar tabs rendered inside the one admin page.
		 *
		 * @return array<string, array{label:string, icon:string, group:string}>
		 */
		public static function get_tabs() {
			$tabs = array(
				'overview' => array(
					'label' => __( 'Overview', 'bp-job-manager' ),
					'icon'  => 'dashicons-chart-bar',
					'group' => 'main',
				),
				'general'  => array(
					'label' => __( 'General', 'bp-job-manager' ),
					'icon'  => 'dashicons-admin-settings',
					'group' => 'settings',
				),
				'welcome'  => array(
					'label' => __( 'Help & Support', 'bp-job-manager' ),
					'icon'  => 'dashicons-sos',
					'group' => 'account',
				),
				// Discover — ecosystem cross-promotion (read-only display view).
				'discover' => array(
					'label' => __( 'Discover', 'bp-job-manager' ),
					'icon'  => 'dashicons-products',
					'group' => 'discover',
				),
			);
			return apply_filters( 'bpjm_admin_tabs', $tabs );
		}

		/**
		 * Bootstrap hooks. Wired from the plugin orchestrator.
		 */
		public function register() {
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			// Priority 999 runs after every other plugin has registered its
			// admin_menu entries. Used to reclaim the hub's landing render
			// when a legacy wbcom-wrapper plugin was the first to register
			// the wbcomplugins top-level menu. See playbook Part 15.
			add_action( 'admin_menu', array( $this, 'takeover_hub_landing' ), 999 );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'in_admin_header', array( $this, 'suppress_foreign_notices' ), 1 );
		}

		/**
		 * Attach as a single submenu under the shared WB Plugins hub.
		 */
		public function add_menu() {
			$cap = apply_filters( 'bpjm_menu_page_capability', 'manage_options' );

			// First Wbcom plugin to load creates the shared WB Plugins hub.
			if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
				add_menu_page(
					esc_html__( 'WB Plugins', 'bp-job-manager' ),
					esc_html__( 'WB Plugins', 'bp-job-manager' ),
					$cap,
					'wbcomplugins',
					array( $this, 'render_hub' ),
					'dashicons-lightbulb',
					59
				);
			}

			add_submenu_page(
				'wbcomplugins',
				esc_html__( 'BuddyPress Job Manager', 'bp-job-manager' ),
				esc_html__( 'Job Manager', 'bp-job-manager' ),
				$cap,
				self::MENU_SLUG,
				array( $this, 'render_page' )
			);
		}

		/**
		 * Force the shared hub's landing page to render our clean
		 * card-panel dashboard, overriding whatever a legacy
		 * wbcom-wrapper plugin may have registered. Playbook Part 15.
		 */
		public function takeover_hub_landing() {
			global $admin_page_hooks;
			if ( empty( $admin_page_hooks['wbcomplugins'] ) ) {
				return;
			}
			remove_all_actions( 'toplevel_page_wbcomplugins' );
			add_action( 'toplevel_page_wbcomplugins', array( $this, 'render_hub' ) );
		}

		/**
		 * Register the settings option used by the General tab form.
		 * Sanitization delegates to the legacy admin class so the
		 * save contract stays byte-identical.
		 */
		public function register_settings() {
			register_setting(
				self::OPTION_GROUP,
				self::OPTION_NAME,
				array(
					'sanitize_callback' => array( $this, 'sanitize_settings' ),
				)
			);
		}

		/**
		 * Sanitizer with a sentinel-guarded merge (playbook Part 7.1).
		 *
		 * The General tab is the sole submitter of this option, but the
		 * merge-on-top-of-stored shape is kept so that:
		 *  - a future second settings tab can't wipe this tab's keys, and
		 *  - the legacy "key present => yes, absent => off" checkbox
		 *    contract that Bp_Job_Manager_Globals reads via
		 *    array_key_exists() is preserved exactly.
		 *
		 * The view emits hidden `bpjm_tab_rendered_keys[]` /
		 * `bpjm_array_rendered_keys[]` sentinels listing every bool/array
		 * key it renders, so an unchecked checkbox (absent from $_POST)
		 * is treated as an explicit "off" instead of "untouched".
		 *
		 * Text-field sanitization still runs through the legacy
		 * Bp_Job_Manager_Admin::bpjm_sanitize_settings() helper so its
		 * contract has a single home.
		 *
		 * @param mixed $input Raw form input.
		 * @return array Sanitized settings.
		 */
		public function sanitize_settings( $input ) {
			if ( ! is_array( $input ) ) {
				$input = array();
			}

			// Start from the currently stored option so untouched keys
			// (and any future tab's keys) survive this save.
			$output = get_option( self::OPTION_NAME, array() );
			if ( ! is_array( $output ) ) {
				$output = array();
			}

			// Boolean (checkbox) keys: present => 'yes', rendered-but-absent => removed.
			$bool_keys = array(
				'bpjm_resume_activity',
				'bpjm_job_post_activity',
				'bpjm_app_notify',
			);

			$posted_bool_keys = array();
			foreach ( $bool_keys as $key ) {
				if ( array_key_exists( $key, $input ) ) {
					$posted_bool_keys[] = $key;
					$output[ $key ]     = 'yes';
				}
			}

			if ( isset( $input['bpjm_tab_rendered_keys'] ) && is_array( $input['bpjm_tab_rendered_keys'] ) ) {
				foreach ( $input['bpjm_tab_rendered_keys'] as $rendered ) {
					$rendered = sanitize_key( $rendered );
					if ( in_array( $rendered, $bool_keys, true ) && ! in_array( $rendered, $posted_bool_keys, true ) ) {
						// Rendered on this tab but unchecked: explicit off.
						unset( $output[ $rendered ] );
					}
				}
			}
			unset( $input['bpjm_tab_rendered_keys'] );

			/*
			 * No array settings remain. The multi-select role lists this branch
			 * existed for were removed in 3.0.0 - the plugin does not define or
			 * gate on roles. The bpjm_array_rendered_keys sentinel is still
			 * discarded below in case an old form is posted from a cached page.
			 */
			unset( $input['bpjm_array_rendered_keys'] );

			// Any other scalar keys (defensive): route through the legacy
			// sanitizer so its text-field contract has one home.
			if ( class_exists( 'Bp_Job_Manager_Admin' ) ) {
				$legacy    = new Bp_Job_Manager_Admin( 'bp-job-manager', defined( 'BPJM_PLUGIN_VERSION' ) ? BPJM_PLUGIN_VERSION : '1.0.0' );
				$leftovers = array();
				foreach ( $input as $key => $value ) {
					if ( in_array( $key, $bool_keys, true ) ) {
						continue;
					}
					$leftovers[ $key ] = $value;
				}
				if ( ! empty( $leftovers ) ) {
					$clean = $legacy->bpjm_sanitize_settings( $leftovers );
					foreach ( $clean as $key => $value ) {
						$output[ $key ] = $value;
					}
				}
			}

			return $output;
		}

		/**
		 * Enqueue admin assets only on our screen + the shared hub landing.
		 *
		 * @param string $hook_suffix Current admin page hook.
		 */
		public function enqueue_assets( $hook_suffix ) {
			unset( $hook_suffix ); // We inspect the screen object instead.
			$screen = get_current_screen();
			if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
				return;
			}

			$version = defined( 'BPJM_PLUGIN_VERSION' ) ? BPJM_PLUGIN_VERSION : '1.0.0';

			wp_enqueue_style(
				'bpjm-admin',
				BPJM_PLUGIN_URL . 'assets/css/admin.css',
				array(),
				$version
			);

			wp_enqueue_script(
				'bpjm-admin',
				BPJM_PLUGIN_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				$version,
				true
			);

			wp_localize_script(
				'bpjm-admin',
				'bpjmAdmin',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'i18n'    => array(
						'confirmDanger'   => __( 'Are you sure? This cannot be undone.', 'bp-job-manager' ),
						'confirmContinue' => __( 'Continue', 'bp-job-manager' ),
						'confirmCancel'   => __( 'Cancel', 'bp-job-manager' ),
					),
				)
			);
		}

		/**
		 * Suppress 3rd-party admin notices on our screen + the hub.
		 */
		public function suppress_foreign_notices() {
			$screen = get_current_screen();
			if ( ! $screen || ! $this->is_our_screen( $screen ) ) {
				return;
			}
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}

		/**
		 * True if the current screen is the plugin's admin page OR the
		 * shared hub landing (our callback owns the hub render too, so
		 * our CSS/JS must load there).
		 *
		 * @param WP_Screen $screen Current screen.
		 */
		private function is_our_screen( $screen ) {
			if ( empty( $screen->id ) ) {
				return false;
			}
			return (bool) preg_match( '/_page_' . preg_quote( self::MENU_SLUG, '/' ) . '$/', $screen->id )
				|| 'toplevel_page_wbcomplugins' === $screen->id;
		}

		/**
		 * Render the shared WB Plugins hub landing page.
		 */
		public function render_hub() {
			$view = BPJM_PLUGIN_PATH . 'includes/admin/views/hub.php';
			if ( file_exists( $view ) ) {
				include $view;
			}
		}

		/**
		 * Render the single admin page. Routes to the active tab.
		 */
		public function render_page() {
			$bpjm_tabs = self::get_tabs();
			$tab_slugs = array_keys( $bpjm_tabs );
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tab routing.
			$active = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $tab_slugs[0];
			if ( ! isset( $bpjm_tabs[ $active ] ) ) {
				$active = $tab_slugs[0];
			}

			$page_url = admin_url( 'admin.php?page=' . self::MENU_SLUG );
			$settings = get_option( self::OPTION_NAME, array() );
			if ( ! is_array( $settings ) ) {
				$settings = array();
			}

			$view_map = array(
				'overview' => 'overview',
				'general'  => 'settings-general',
				'welcome'  => 'welcome',
				'discover' => 'discover',
			);

			$view                = isset( $view_map[ $active ] ) ? $view_map[ $active ] : 'overview';
			$in_settings_group   = isset( $bpjm_tabs[ $active ]['group'] ) && 'settings' === $bpjm_tabs[ $active ]['group'];
			$settings_form_group = self::OPTION_GROUP;

			$view_path = BPJM_PLUGIN_PATH . 'includes/admin/views/' . $view . '.php';
			$shell     = BPJM_PLUGIN_PATH . 'includes/admin/views/shell.php';

			include $shell;
		}
	}
endif;
