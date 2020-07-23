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
	 * @author   wbcomdesigns
	 * @access   public
	 * @param    string $plugin_name       The name of this plugin.
	 * @param    string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();
		if ( 'wb-plugins_page_bp-job-manager' === $screen->base ) {
			wp_enqueue_style( $this->plugin_name . '-selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.css' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-job-manager-admin.css' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'wb-plugins_page_bp-job-manager' === $screen->base ) {
			wp_enqueue_script( $this->plugin_name . '-selectize-js', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-job-manager-admin.js', array( 'jquery' ) );
		}

	}

	/**
	 * Register a settings page to handle groups export import settings.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function bpjm_add_options_page() {
		if ( empty ( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'bp-job-manager' ), esc_html__( 'WB Plugins', 'bp-job-manager' ), 'manage_options', 'wbcomplugins', array( $this, 'bpjm_admin_settings_page' ), 'dashicons-lightbulb', 59 );
		 	add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'bp-job-manager' ), esc_html__( 'General', 'bp-job-manager' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BP Job Manager', 'bp-job-manager' ), esc_html__( 'BP Job Manager', 'bp-job-manager' ), 'manage_options', $this->plugin_name, array( $this, 'bpjm_admin_settings_page' ) );
	}

	/**
	 * Actions performed to create a settings page content.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function bpjm_admin_settings_page() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : $this->plugin_name;
		?>
		<div class="wrap">
			<div class="bpjm-header">
				<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
					<?php esc_html_e( 'BuddyPress Job Manager Settings', 'bp-job-manager' ); ?>
				</h1>
			</div>
			<?php settings_errors(); ?>
			<div class="wbcom-admin-settings-page">
				<?php
				$this->bpjm_plugin_settings_tabs();
				do_settings_sections( $tab );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Actions performed to create tabs on the sub menu page.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function bpjm_plugin_settings_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : $this->plugin_name;
		echo '<div class="wbcom-tabs-section"><h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=' . esc_attr( $this->plugin_name ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption, 'bp-job-manager' ) . '</a>';
		}
		echo '</h2></div>';
	}

	/**
	 * Actions performed to create General Tab.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function bpjm_general_settings() {
		$this->plugin_settings_tabs[ $this->plugin_name ] = __( 'General', 'bp-job-manager' );
		register_setting( 'bpjm_general_settings', 'bpjm_general_settings' );
		add_settings_section( 'bp-job-manager-section', ' ', array( &$this, 'bpjm_general_settings_content' ), $this->plugin_name );
	}

	/**
	 * Actions performed to create General Tab Content.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function bpjm_general_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/includes/bp-job-manager-general-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/includes/bp-job-manager-general-settings.php';
		}
	}

	/**
	 * This function will list the jobs and resumes link in the dropdown list.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 * @param    array $wp_admin_nav contain wp nav items.
	 */
	public function bpjm_setup_admin_bar_links( $wp_admin_nav = array() ) {
		global $wp_admin_bar, $bp_job_manager;
		if ( is_user_logged_in() ) {
			$curr_user = wp_get_current_user();
			if ( ! empty( $curr_user->roles ) ) {
				/**
				 * Jobs menu - for the roles allowed for job posting.
				 */
				$match_post_job_roles = array_intersect( $bp_job_manager->post_job_user_roles, $curr_user->roles );
				if ( ! empty( $match_post_job_roles ) ) {
					$profile_menu_slug  = 'jobs';
					$profile_menu_title = __( 'Jobs', 'bp-job-manager' );

					$args          = array(
						'post_type'      => 'job_listing',
						'post_status'    => 'any',
						'author'         => get_current_user_id(),
						'posts_per_page' => -1,
						'orderby'        => 'post_date',
						'order'          => 'ASC',
					);
					$my_jobs_count = count( get_posts( $args ) );

					$base_url     = bp_loggedin_user_domain() . $profile_menu_slug;
					$post_job_url = $base_url . '/post-job';
					$my_jobs_url  = $base_url . '/my-jobs';

					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-buddypress',
							'id'     => 'my-account-' . $profile_menu_slug,
							'title'  => esc_html( $profile_menu_title ) . ' <span class="count">' . esc_html( $my_jobs_count ) . '</span>',
							'href'   => trailingslashit( $my_jobs_url ),
						)
					);

					// Add add-new submenu.
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-' . $profile_menu_slug,
							'id'     => 'my-account-' . $profile_menu_slug . '-my-jobs',
							'title'  => esc_html__( 'My Jobs', 'bp-job-manager' ),
							'href'   => trailingslashit( $my_jobs_url ),
						)
					);

					$wpjm_bookmarks_active = in_array( 'wp-job-manager-bookmarks/wp-job-manager-bookmarks.php', get_option( 'active_plugins' ) );
					if ( true === $wpjm_bookmarks_active ) {
						$bookmarked_jobs_url = $base_url . '/my-bookmarks';
						// Add add-new submenu.
						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-my-bookmarks',
								'title'  => esc_html__( 'My Bookmarks', 'bp-job-manager' ),
								'href'   => trailingslashit( $bookmarked_jobs_url ),
							)
						);
					}

					$wpjm_alerts_active = in_array( 'wp-job-manager-alerts/wp-job-manager-alerts.php', get_option( 'active_plugins' ) );
					if ( true === $wpjm_alerts_active ) {
						$job_alerts_url = $base_url . '/job-alerts';
						// Add add-new submenu.
						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-job-alerts',
								'title'  => esc_html__( 'Job Alerts', 'bp-job-manager' ),
								'href'   => trailingslashit( $job_alerts_url ),
							)
						);
					}

					// Add add-new submenu.
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-' . $profile_menu_slug,
							'id'     => 'my-account-' . $profile_menu_slug . '-post-job',
							'title'  => esc_html__( 'Post Job', 'bp-job-manager' ),
							'href'   => trailingslashit( $post_job_url ),
						)
					);
				}

				/**
				 * Resumes menu - for the roles allowed for job posting
				 */
				$wpjm_resumes_active      = in_array( 'wp-job-manager-resumes/wp-job-manager-resumes.php', get_option( 'active_plugins' ) );
				$match_apply_job_roles = array_intersect( $bp_job_manager->apply_job_user_roles, $curr_user->roles );
				if ( ! empty( $match_apply_job_roles ) && ($wpjm_resumes_active)) {
					$profile_menu_slug  = 'resumes';
					$profile_menu_title = esc_html__( 'Resumes', 'bp-job-manager' );

					// Count resumes.
					$args             = array(
						'post_type'      => 'resume',
						'post_status'    => 'any',
						'author'         => get_current_user_id(),
						'posts_per_page' => -1,
						'orderby'        => 'post_date',
						'order'          => 'ASC',
					);
					$my_resumes_count = count( get_posts( $args ) );

					$base_url         = bp_loggedin_user_domain() . $profile_menu_slug;
					$my_resumes_url   = $base_url . '/my-resumes';
					$applied_jobs_url = $base_url . '/applied-jobs';
					$add_resume_url   = $base_url . '/add-resume';

					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-buddypress',
							'id'     => 'my-account-' . $profile_menu_slug,
							'title'  => esc_html( $profile_menu_title ) . ' <span class="count">' . esc_html( $my_resumes_count ) . '</span>',
							'href'   => trailingslashit( $my_resumes_url ),
						)
					);

					// Add add-new submenu.
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-' . $profile_menu_slug,
							'id'     => 'my-account-' . $profile_menu_slug . '-my-resumes',
							'title'  => esc_html__( 'My Resumes', 'bp-job-manager' ),
							'href'   => trailingslashit( $my_resumes_url ),
						)
					);

					// Add add-new submenu.
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-' . $profile_menu_slug,
							'id'     => 'my-account-' . $profile_menu_slug . '-applied-jobs',
							'title'  => esc_html__( 'Applied Jobs', 'bp-job-manager' ),
							'href'   => trailingslashit( $applied_jobs_url ),
						)
					);

					// Add add-new submenu.
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-' . $profile_menu_slug,
							'id'     => 'my-account-' . $profile_menu_slug . '-add-resume',
							'title'  => esc_html__( 'Add Resume', 'bp-job-manager' ),
							'href'   => trailingslashit( $add_resume_url ),
						)
					);
				}
			}
		}
	}

	public function bpjm_publish_job_listing( $ID, $post ) {
		global $bp_job_manager;
		if( isset( $bp_job_manager->bpjm_job_post_activity ) && $bp_job_manager->bpjm_job_post_activity == 'yes' ){
			global $wpdb;
			$table_name = $wpdb->prefix . 'bp_activity';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$check = $wpdb->get_results("SELECT * FROM $table_name WHERE item_id = $post->ID AND type IN ('bpjm_job_post')");
				if( !$check ) {
					$args['type'] = 'bpjm_job_post';
					$job_permalink = '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
					$args['action'] = sprintf(__('%s posted a new job %s', 'bp-job-manager'), bp_core_get_userlink($post->post_author), $job_permalink );
					$args['component'] = 'activity';
					$args['user_id'] = $post->post_author;
					$args['item_id'] = $post->ID;
					$args['content'] = $post->post_content;

					bp_activity_add( $args );
				}
			}
		}
	}

	public function bpjm_publish_resume( $ID, $post ) {
		global $bp_job_manager;
		if( isset( $bp_job_manager->bpjm_resume_activity ) && $bp_job_manager->bpjm_resume_activity == 'yes' ){

			global $wpdb;
			$table_name = $wpdb->prefix . 'bp_activity';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$check = $wpdb->get_results("SELECT * FROM $table_name WHERE item_id = $post->ID AND type IN ('bpjm_resume_publish')");
				if( !$check ) {
					$args['type'] = 'bpjm_resume_publish';
					$job_permalink = '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
					$args['action'] = sprintf(__('%s posted resume %s', 'bp-job-manager'), bp_core_get_userlink($post->post_author), $job_permalink );
					$args['component'] = 'activity';
					$args['user_id'] = $post->post_author;
					$args['item_id'] = $post->ID;
					$args['content'] = $post->post_content;

					bp_activity_add( $args );
				}
			}
		}
	}
}
