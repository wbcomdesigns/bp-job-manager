<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Add admin page for displaying buddypress fitness settings
if( !class_exists( 'Wpbpjm_AdminPage' ) ) {
	class Wpbpjm_AdminPage{

		public  $plugin_slug = 'bp-job-manager-settings',
				$plugin_settings_tabs = array();

		//constructor
		function __construct() {
			add_action( 'admin_menu', array( $this, 'wpbpjm_add_options_page' ) );

			add_action('admin_init', array($this, 'wpbpjm_register_general_settings'));
			add_action('admin_init', array($this, 'wpbpjm_register_support_settings'));

			$this->wpbpjm_save_general_settings();
		}

		//Actions performed to create a custom menu on loading admin_menu
		function wpbpjm_add_options_page() {
			add_options_page( __( 'BuddyPress Job Manager Settings', WPBPJM_TEXT_DOMAIN ), __( 'BP Job Manager', WPBPJM_TEXT_DOMAIN ), 'manage_options', $this->plugin_slug, array( $this, 'wpbpjm_admin_settings_page' ) );
		}

		function wpbpjm_admin_settings_page() {
			$tab = isset($_GET['tab']) ? $_GET['tab'] : $this->plugin_slug;
			?>
			<div class='wrap'>
				<h2><?php _e( 'BuddyPress Job Manager', WPBPJM_TEXT_DOMAIN ); ?></h2>
				<p><?php _e(' This plugin allows <strong>BuddyPress Members</strong> to set <strong>Jobs & Resumes</strong> for their profiles.', WPBPJM_TEXT_DOMAIN ); ?></p>
				<?php $this->wpbpjm_plugin_settings_tabs(); ?>
				<form action="" method="POST" id="<?php echo $tab;?>-settings-form">
				<?php do_settings_sections( $tab );?>
				</form>
			</div>
			<?php
		}

		function wpbpjm_plugin_settings_tabs() {
			$current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->plugin_slug;
			
			echo '<h2 class="nav-tab-wrapper">';
			foreach ($this->plugin_settings_tabs as $tab_key => $tab_caption) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}

		function wpbpjm_register_general_settings() {
			$this->plugin_settings_tabs[$this->plugin_slug] = __( 'General', WPBPJM_TEXT_DOMAIN );
			register_setting( $this->plugin_slug, $this->plugin_slug );
			add_settings_section('wpbpjm-general-section', ' ', array(&$this, 'wpbpjm_general_settings_template'), $this->plugin_slug );
		}

		function wpbpjm_register_support_settings() {
			$this->plugin_settings_tabs['wpbpjm-support'] = __( 'Support', WPBPJM_TEXT_DOMAIN );
			register_setting('wpbpjm-support', 'wpbpjm-support');
			add_settings_section('wpbpjm-support-section', ' ', array(&$this, 'wpbpjm_support_template'), 'wpbpjm-support');
		}

		function wpbpjm_support_template() {
			if (file_exists(dirname(__FILE__) . '/inc/wpbpjm-support.php')) {
				require_once( dirname(__FILE__) . '/inc/wpbpjm-support.php' );
			}
		}

		 function wpbpjm_general_settings_template() {
		 	dirname(__FILE__) . '/inc/wpbpjm-general-settings.php';
			if (file_exists(dirname(__FILE__) . '/inc/wpbpjm-general-settings.php')) {
				require_once( dirname(__FILE__) . '/inc/wpbpjm-general-settings.php' );
			}
		}

		/**
		 * Save the admin settings here
		 */
		function wpbpjm_save_general_settings(){
			if( isset( $_POST['wpbpjm-general-settings-submit'] ) ) {
				$wpbpjm_general_settings = array();
				
				$wpbpjm_general_settings['job_user_roles'] = $_POST['wpbpjm-job-user-roles'];
				$wpbpjm_general_settings['resume_user_roles'] = $_POST['wpbpjm-resume-user-roles'];
				
				// echo '<pre>'; print_r( $wpbpjm_general_settings ); die;
				update_option( 'wpbpjm_general_settings', $wpbpjm_general_settings );
				$success_msg = "<div class='notice updated is-dismissible' id='message'>";
				$success_msg .= "<p>".__( 'BuddyPress Job Manager Settings Saved.', WPBPJM_TEXT_DOMAIN )."</p>";
				$success_msg .= "</div>";
				echo $success_msg;
			}
		}
	}
}