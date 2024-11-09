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
if ( ! class_exists( 'Bp_Job_Manager_Admin' ) ) :
	class Bp_Job_Manager_Admin {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		protected $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		protected $version;

		/**
		 * Store plugin settings tabs.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      array     $plugin_settings_tabs    Array of plugin settings tabs.
		 */
		protected $plugin_settings_tabs = array();

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
			$tab = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : 'bp-job-manager';
			if ( ! empty( $tab ) && strpos( $tab, 'bp-job-manager' ) !== false ) {
				wp_enqueue_style( $this->plugin_name . '-selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-job-manager-admin.css', array(), $this->version, 'all' );
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
			$tab = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : 'bp-job-manager';
			if ( ! empty( $tab ) && strpos( $tab, 'bp-job-manager' ) !== false ) {
				wp_enqueue_script( $this->plugin_name . '-selectize-js', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-job-manager-admin.js', array( 'jquery' ), $this->version, false );
			}

		}

		/**
		 * Hide all notices from the setting page.
		 *
		 * @return void
		 */
		public function wbcom_hide_all_admin_notices_from_setting_page() {
			$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'bp-job-manager' );
			$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

			if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
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
			
			$capability = 'manage_options';
			if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
				$capability = apply_filters( 'bpjm_menu_page_capability', 'manage_options' );
				add_menu_page( esc_html__( 'WB Plugins', 'bp-job-manager' ), esc_html__( 'WB Plugins', 'bp-job-manager' ), $capability, 'wbcomplugins', array( $this, 'bpjm_admin_settings_page' ), 'dashicons-lightbulb', 59 );
				add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'bp-job-manager' ), esc_html__( 'General', 'bp-job-manager' ), $capability, 'wbcomplugins' );
			}
			add_submenu_page( 'wbcomplugins', esc_html__( 'Job Manager', 'bp-job-manager' ), esc_html__( 'Job Manager', 'bp-job-manager' ), $capability, $this->plugin_name, array( $this, 'bpjm_admin_settings_page' ) );
		}

		/**
		 * Actions performed to create a settings page content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_admin_settings_page() {
			$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bpjm-welcome';
			?>
		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
					<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/?utm_source=pluginoffernotice&utm_medium=community_banner" target="_blank">
						<img src="<?php echo esc_url( BPJM_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
					</a>
				</div>
			</div>
			<div class="wbcom-wrap">
				<div class="bupr-header">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php esc_html_e( 'BuddyPress Job Manager', 'bp-job-manager' ); ?>
							<span>
								<?php
								/* translators: %s: */
								printf( esc_html__( 'Version %s', 'bp-job-manager' ), esc_attr( BPJM_PLUGIN_VERSION ) );
								?>
							</span>
						</div>
						<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					</div>
				</div>
			<?php settings_errors(); ?>
				<div class="wbcom-admin-settings-page">
					<?php
					$this->bpjm_plugin_settings_tabs();
					do_settings_sections( $tab );
					?>
				</div>
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
			if ( file_exists( dirname( __FILE__ ) . '/includes/bp-job-manager-settings-tabs.php' ) ) {
				require_once dirname( __FILE__ ) . '/includes/bp-job-manager-settings-tabs.php';
			}
		}

		/**
		 * Actions performed to create General Tab.
		 *
		 * @since    1.0.0
		 */
		public function bpjm_general_settings()
		{
			$this->plugin_settings_tabs['bpjm-welcome'] = __('Welcome', 'bp-job-manager');
			add_settings_section('bp-job-manager-welcome', ' ', array($this, 'bpjm_welcome_content'), 'bpjm-welcome');

			$this->plugin_settings_tabs[$this->plugin_name] = __('General', 'bp-job-manager');
			register_setting('bpjm_general_settings', 'bpjm_general_settings');
			add_settings_section('bp-job-manager-section', ' ', array($this, 'bpjm_general_settings_content'), $this->plugin_name);
		}

		/**
		 * Actions performed to create welcome Tab Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_welcome_content() {
			if ( file_exists( dirname( __FILE__ ) . '/includes/bp-job-manager-welcome-page.php' ) ) {
				require_once dirname( __FILE__ ) . '/includes/bp-job-manager-welcome-page.php';
			}
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
		 * @access   public
		 * @param    array $wp_admin_nav Contains wp nav items.
		 */
		public function bpjm_setup_admin_bar_links( $wp_admin_nav = array() ) {
			global $wp_admin_bar, $bp_job_manager;
			if ( is_user_logged_in() ) {
				$curr_user = wp_get_current_user();
				if ( ! empty( $curr_user->roles ) ) {
					
					/**
					 * Resumes menu - for the roles allowed for job posting.
					 */
					$apply_job_roles = isset( $bp_job_manager->apply_job_user_roles ) ? $bp_job_manager->apply_job_user_roles : [];
					$match_apply_job_roles = array_intersect( $apply_job_roles, $curr_user->roles );
					if ( ! empty( $match_apply_job_roles ) ) {
						// Resumes menu
						$profile_menu_slug  = 'resumes';
						$profile_menu_title = esc_html__( 'Resumes', 'bp-job-manager' );

						// Get resume count using the helper function
						$my_resumes_count = bpjm_member_profile_resumes_count( get_current_user_id() );

						$base_url         = bp_loggedin_user_domain() . $profile_menu_slug;
						$my_resumes_url   = $base_url . '/my-resumes';
						$applied_jobs_url = $base_url . '/applied-jobs';
						$add_resume_url   = $base_url . '/add-resume';

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-buddypress',
								'id'     => 'my-account-' . $profile_menu_slug,
								'title'  => esc_html( $profile_menu_title ) . ' <span class="count">' . bp_core_number_format($my_resumes_count) . '</span>',
								'href'   => trailingslashit( $my_resumes_url ),
							)
						);

						// Add resume submenu items
						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-my-resumes',
								'title'  => esc_html__( 'My Resumes', 'bp-job-manager' ),
								'href'   => trailingslashit( $my_resumes_url ),
							)
						);

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-applied-jobs',
								'title'  => esc_html__( 'Applied Jobs', 'bp-job-manager' ),
								'href'   => trailingslashit( $applied_jobs_url ),
							)
						);

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $profile_menu_slug,
								'id'     => 'my-account-' . $profile_menu_slug . '-add-resume',
								'title'  => esc_html__( 'Add Resume', 'bp-job-manager' ),
								'href'   => trailingslashit( $add_resume_url ),
							)
						);
					}

					/**
					 * Jobs menu - for the roles allowed for job posting.
					 */
					$job_posting_roles = isset( $bp_job_manager->post_job_user_roles ) ? $bp_job_manager->post_job_user_roles : [];
					$match_job_posting_roles = array_intersect( $job_posting_roles, $curr_user->roles );
					if ( ! empty( $match_job_posting_roles ) ) {
						// Jobs menu
						$jobs_menu_slug   = 'jobs';
						$jobs_menu_title  = esc_html__( 'Jobs', 'bp-job-manager' );

						// Get job count using the helper function
						$my_jobs_count = bpjm_member_profile_jobs_count( get_current_user_id() );

						$jobs_base_url    = bp_loggedin_user_domain() . $jobs_menu_slug;
						$my_jobs_url      = $jobs_base_url . '/my-jobs';
						$post_job_url     = $jobs_base_url . '/post-job';

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-buddypress',
								'id'     => 'my-account-' . $jobs_menu_slug,
								'title'  => esc_html( $jobs_menu_title ) . ' <span class="count">' . bp_core_number_format($my_jobs_count) . '</span>',
								'href'   => trailingslashit( $my_jobs_url ),
							)
						);

						// Add job submenu items
						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $jobs_menu_slug,
								'id'     => 'my-account-' . $jobs_menu_slug . '-my-jobs',
								'title'  => esc_html__( 'My Jobs', 'bp-job-manager' ),
								'href'   => trailingslashit( $my_jobs_url ),
							)
						);

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-' . $jobs_menu_slug,
								'id'     => 'my-account-' . $jobs_menu_slug . '-post-job',
								'title'  => esc_html__( 'Post Job', 'bp-job-manager' ),
								'href'   => trailingslashit( $post_job_url ),
							)
						);
					}
				}
			}
		}



		/**
		 * Display Job listings
		 *
		 * @param  int   $ID Job ID.
		 * @param  array $post Job post object.
		 * @return void
		 */
		public function bpjm_publish_job_listing( $ID, $post ) {
			global $bp_job_manager;
			if ( isset( $bp_job_manager->bpjm_job_post_activity ) && 'yes' === $bp_job_manager->bpjm_job_post_activity ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'bp_activity';
				$get_table  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

				if ( $get_table == $table_name ) {
					// Use prepare with placeholders to avoid direct interpolation
					$check = $wpdb->get_results( $wpdb->prepare( 
						"SELECT * FROM {$table_name} WHERE item_id = %d AND type = %s", 
						$post->ID, 
						'bpjm_job_post' 
					) );

					if ( ! $check ) {
						$args['type']  = 'bpjm_job_post';
						$job_permalink = '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
						/* translators: %1$s: BP user link ;  %2$s: Job Link*/
						$args['action']    = sprintf( __( '%1$s posted a new job %2$s', 'bp-job-manager' ), bp_core_get_userlink( $post->post_author ), $job_permalink );
						$args['component'] = 'activity';
						$args['user_id']   = $post->post_author;
						$args['item_id']   = $post->ID;
						$args['content']   = $post->post_content;

						bp_activity_add( $args );
					}
				}
			}
		}


		/**
		 * Publish resume.
		 *
		 * @param  int   $ID Job ID.
		 * @param  array $post Job post object.
		 * @return void
		 */
		public function bpjm_publish_resume( $ID, $post ) {
			global $bp_job_manager;
			if ( isset( $bp_job_manager->bpjm_resume_activity ) && 'yes' === $bp_job_manager->bpjm_resume_activity ) {

				global $wpdb;
				$table_name = $wpdb->prefix . 'bp_activity';
				$get_table  = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
				if ( $get_table == $table_name ) {
					$check = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE item_id = %d AND type IN (%s)", $post->ID, 'bpjm_resume_publish' ) );
					if ( ! $check ) {
						$args['type']  = 'bpjm_resume_publish';
						$job_permalink = '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
						/* translators: %1$s: Resume user link ;  %2$s: Resume Link*/
						$args['action']    = sprintf( __( '%1$s posted resume %2$s', 'bp-job-manager' ), bp_core_get_userlink( $post->post_author ), $job_permalink );
						$args['component'] = 'activity';
						$args['user_id']   = $post->post_author;
						$args['item_id']   = $post->ID;
						$args['content']   = $post->post_content;

						bp_activity_add( $args );
					}
				}
			}
		}
	}
endif;
