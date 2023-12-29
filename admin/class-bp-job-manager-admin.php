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
		 * Store plugin settings tabs.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      array     $plugin_settings_tabs    Array of plugin settings tabs.
		 */
		private $plugin_settings_tabs = array();

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

			if ( ! empty( $tab ) && ( 'bp-job-manager' === $tab ) ) {
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
			if ( ! empty( $tab ) && ( 'bp-job-manager' === $tab ) ) {
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
			if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
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
			$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bpjm-welcome';
			echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<li class="' . esc_attr( $tab_key ) . '"><a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=' . esc_attr( $this->plugin_name ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption, 'bp-job-manager' ) . '</a></li>';
			}
			echo '</div></ul></div>';
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
						$current_user_id = get_current_user_id();
						$job_listings = 0; // Initialize $job_listings with a default value
						$transient_key = 'user_' . $current_user_id . '_job_listings';
						$job_listings = get_transient($transient_key);
						if ($current_user_id > 0) {
							if ($job_listings === false) {
								$args = array(
									'post_type'      => 'job_listing',
									'post_status'    => 'publish',
									'author'         => $current_user_id,
									'posts_per_page' => -1,
									'orderby'        => 'post_date',
									'order'          => 'ASC',
									'fields'         => 'ids' // If you only need IDs.
								);
								$query = new WP_Query($args);
								$job_listings = $query->found_posts;
								// Cache the results for 1 hour.
								set_transient($transient_key, $job_listings, HOUR_IN_SECONDS);
								wp_reset_postdata();
							}
						}

						$base_url     = bp_loggedin_user_domain() . $profile_menu_slug;
						$post_job_url = $base_url . '/post-job';
						$my_jobs_url  = $base_url . '/my-jobs';

						$wp_admin_bar->add_menu(
							array(
								'parent' => 'my-account-buddypress',
								'id'     => 'my-account-' . $profile_menu_slug,
								'title'  => esc_html( $profile_menu_title ) . ' <span class="count">' . bp_core_number_format($job_listings) . '</span>',
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
								'title'  => esc_html__( 'Post a New Job', 'bp-job-manager' ),
								'href'   => trailingslashit( $post_job_url ),
							)
						);
					}

					/**
					 * Resumes menu - for the roles allowed for job posting
					 */
					$match_apply_job_roles = array_intersect( $bp_job_manager->apply_job_user_roles, $curr_user->roles );
					if ( ! empty( $match_apply_job_roles ) ) {
						$profile_menu_slug  = 'resumes';
						$profile_menu_title = esc_html__( 'Resumes', 'bp-job-manager' );
						$my_resumes_count = 0;
						$current_user_id = get_current_user_id();
						$transient_key = 'user_' . $current_user_id . '_my_resumes_count';
						$my_resumes_count = get_transient($transient_key);
						if ($my_resumes_count === false) {
							$args = [
								'post_type'      => 'resume',
								'post_status'    => 'publish',
								'author'         => $current_user_id,
								'posts_per_page' => -1,
								'fields'         => 'ids', // Only get post IDs to improve performance.
							];

							$query = new WP_Query($args);
							$my_resumes_count = $query->found_posts;
							// Cache the results for 1 hour.
							set_transient($transient_key, $my_resumes_count, HOUR_IN_SECONDS);
						}
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
					$check = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %s WHERE item_id = %d AND type IN ('bpjm_job_post')", $table_name, $post->ID ) );
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
					$check = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %s WHERE item_id = %d AND type IN ('bpjm_resume_publish')", $table_name, $post->ID ) );
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
