<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Bp_Job_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if( isset( $_POST['bpjm-general-settings-submit'] ) && wp_verify_nonce( $_POST['bpjm-general-settings-nonce'], 'bpjm-general' ) ) {
			$this->bpjm_save_general_settings();
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if( stripos( $_SERVER['REQUEST_URI'], $this->plugin_name ) !== false ) {
			wp_enqueue_style( $this->plugin_name.'-font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css' );
			wp_enqueue_style( $this->plugin_name.'-selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.css' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-job-manager-admin.css' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if( stripos( $_SERVER['REQUEST_URI'], $this->plugin_name ) !== false ) {
			wp_enqueue_script( $this->plugin_name.'-selectize-js', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-job-manager-admin.js', array( 'jquery' ) );
		}

	}

	/**
	 * Register a settings page to handle groups export import settings
	 *
	 * @since    1.0.0
	 */
	public function bpjm_add_options_page() {
		add_options_page( __( 'BuddyPress Job Manager Settings', BPJM_TEXT_DOMAIN ), __( 'BP Job Manager', BPJM_TEXT_DOMAIN ), 'manage_options', $this->plugin_name, array( $this, 'bpjm_admin_settings_page' ) );
	}

	/**
	 * Actions performed to create a settings page content
	 */
	public function bpjm_admin_settings_page() {
		$tab = isset($_GET['tab']) ? $_GET['tab'] : $this->plugin_name;
		?>
		<div class="wrap">
			<div class="bpjm-header">
				<h2 class="bpjm-plugin-heading"><?php _e( 'BuddyPress Job Manager', BPJM_TEXT_DOMAIN );?></h2>
				<div class="bpjm-extra-actions">
					<button type="button" class="button button-secondary" onclick="window.open('https://wbcomdesigns.com/contact/', '_blank');"><i class="fa fa-envelope" aria-hidden="true"></i> <?php _e( 'Email Support', BPJM_TEXT_DOMAIN )?></button>
					<button type="button" class="button button-secondary" onclick="window.open('https://wbcomdesigns.com/helpdesk/article-categories/buddypress-job-manager/', '_blank');"><i class="fa fa-file" aria-hidden="true"></i> <?php _e( 'User Manual', BPJM_TEXT_DOMAIN )?></button>
					<button type="button" class="button button-secondary" onclick="window.open('https://wordpress.org/support/plugin/bp-job-manager/reviews/', '_blank');"><i class="fa fa-star" aria-hidden="true"></i> <?php _e( 'Rate Us on WordPress.org', BPJM_TEXT_DOMAIN )?></button>
				</div>
			</div>
			<?php $this->bpjm_plugin_settings_tabs();?>
			<?php do_settings_sections( $tab );?>
		</div>
		<?php
	}

	/**
	 * Actions performed to create tabs on the sub menu page
	 */
	public function bpjm_plugin_settings_tabs() {
		$current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->plugin_name;
		echo '<h2 class="nav-tab-wrapper">';
		foreach ($this->plugin_settings_tabs as $tab_key => $tab_caption) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" id="' . $tab_key . '-tab" href="?page=' . $this->plugin_name . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * General Tab
	 */
	public function bpjm_general_settings() {
		$this->plugin_settings_tabs[$this->plugin_name] = __( 'General', BPJM_TEXT_DOMAIN );
		register_setting($this->plugin_name, $this->plugin_name);
		add_settings_section('bp-job-manager-section', ' ', array(&$this, 'bpjm_general_settings_content'), $this->plugin_name );
	}

	/**
	 * General Tab Content
	 */
	public function bpjm_general_settings_content() {
		if (file_exists(dirname(__FILE__) . '/includes/bp-job-manager-general-settings.php')) {
			require_once( dirname(__FILE__) . '/includes/bp-job-manager-general-settings.php' );
		}
	}

	/**
	 * Support Tab
	 */
	public function bpjm_support() {
		$this->plugin_settings_tabs[$this->plugin_name.'-support'] = __( 'Support', BPJM_TEXT_DOMAIN );
		register_setting($this->plugin_name.'-support', $this->plugin_name.'-support');
		add_settings_section('bp-job-manager-support-section', ' ', array(&$this, 'bpjm_support_content'), $this->plugin_name.'-support' );
	}

	/**
	 * Support Tab Content
	 */
	public function bpjm_support_content() {
		if (file_exists(dirname(__FILE__) . '/includes/bp-job-manager-support.php')) {
			require_once( dirname(__FILE__) . '/includes/bp-job-manager-support.php' );
		}
	}

	/**
	 * Actions performed to save general settings of the plugin
	 */
	public function bpjm_save_general_settings() {
		$bpjm_general_settings 		= array();
		$post_job_user_roles 		= wp_unslash( $_POST['bpjm-post-jobs-user-roles'] );
		$apply_job_user_roles 		= wp_unslash( $_POST['bpjm-apply-jobs-user-roles'] );
		$matching_user_roles 		= array_intersect( $post_job_user_roles, $apply_job_user_roles );
		$matching_roles_count 		= count( $matching_user_roles );
		$bpjm_general_settings 		= array(
			'post_job_user_roles'	=> $post_job_user_roles,
			'apply_job_user_roles'	=> $apply_job_user_roles,
		);

		if( empty( $matching_user_roles ) ) {
			update_option( 'bpjm_general_settings', $bpjm_general_settings );
			$success_msg = "<div class='notice updated' id='message'>";
			$success_msg .= "<p>".__( 'Settings Saved.', BPJM_TEXT_DOMAIN )."</p>";
			$success_msg .= "</div>";
			echo $success_msg;
		} else {
			if( $matching_roles_count == 1 && $matching_user_roles[0] == 'administrator' ) {
				update_option( 'bpjm_general_settings', $bpjm_general_settings );
				$success_msg = "<div class='notice updated' id='message'>";
				$success_msg .= "<p>".__( 'Settings Saved.', BPJM_TEXT_DOMAIN )."</p>";
				$success_msg .= "</div>";
				echo $success_msg;
			} else {
				$err_msg = "<div class='error' id='message'>";
				$err_msg .= "<p>".__( 'User roles cannot be same for posting the jobs and the ones allowed for job applications.', BPJM_TEXT_DOMAIN )."</p>";
				$err_msg .= "</div>";
				echo $err_msg;
			}
		}
	}

}
