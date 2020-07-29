<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Job_Manager
 * @subpackage Bp_Job_Manager/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
if ( ! class_exists( 'Bp_Job_Manager_Public' ) ) :
	class Bp_Job_Manager_Public {

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

		public $max_num_pages = '';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string $plugin_name       The name of the plugin.
		 * @param      string $version    The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {
			$this->plugin_name = $plugin_name;
			$this->version     = $version;

			add_filter( 'job_manager_get_dashboard_jobs_args', array( $this, 'bpjm_job_dashboard_user_id' ) );
			add_filter( 'has_wpjm_shortcode', array( $this, 'bpjm_has_wpjm_shortcode' ) );
			add_action( 'wp_ajax_bpjm_load_more_jobs', array( $this, 'bpjm_load_more_jobs' ) );
			add_action( 'wp_ajax_nopriv_bpjm_load_more_jobs', array( $this, 'bpjm_load_more_jobs' ) );

		}

		public function get_jobs_max_num_pages() {
			if ( 'my-jobs' == bp_current_action() || 'jobs' == bp_current_action() ) {
				$args = array(
					'post_type'           => 'job_listing',
					'post_status'         => array( 'publish', 'expired', 'pending', 'draft', 'preview' ),
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => 10,
					'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * 25,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => bp_displayed_user_id(),
				);

				$jobs                = new WP_Query( $args );
				$this->max_num_pages = $jobs->max_num_pages;
				wp_reset_postdata();
			}
		}


		/**
		 * Register the stylesheets for the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Bp_Job_Manager_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Bp_Job_Manager_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */
			if ( 'my-jobs' == bp_current_action() || 'applied-jobs' == bp_current_action() || 'post-job' == bp_current_action() || 'my-resumes' == bp_current_action() || 'add-resume' == bp_current_action() || 'job-alerts' == bp_current_action() ) {
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-job-manager-public.css', array(), $this->version, 'all' );

			}

		}

		/**
		 * Register the JavaScript for the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Bp_Job_Manager_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Bp_Job_Manager_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */
			if ( 'my-jobs' == bp_current_action() || 'applied-jobs' == bp_current_action() || 'post-job' == bp_current_action() || 'my-resumes' == bp_current_action() || 'add-resume' == bp_current_action() ) {
				$this->get_jobs_max_num_pages();
				wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-job-manager-public.js', array( 'jquery' ), $this->version, false );
				wp_localize_script(
					$this->plugin_name,
					'bpjm_load_jobs_object',
					array(
						'ajaxurl'       => admin_url( 'admin-ajax.php' ),
						'max_num_pages' => $this->max_num_pages,
						'ajax_nonce'    => wp_create_nonce( 'bpjm_load_jobs_nonce' ),
					)
				);
				wp_enqueue_script( $this->plugin_name );
			}
		}

		/**
		 * Load default js and css for submit job shortcode.
		 *
		 * @return boolean
		 */
		public function bpjm_has_wpjm_shortcode() {
			$action = bp_current_action();
			
			if ( $action === 'my-jobs' ) {
				return true;
			}
			if ( $action === 'applied-jobs' ) {
				return true;
			}
			if ( $action === 'post-job' ) {
				return true;
			}
			if ( $action === 'my-resumes' ) {
				return true;
			}
			if ( $action === 'add-resume' ) {
				return true;
			}
			if ( $action === 'job-alerts' ) {
				return true;
			}
			if ( $action === 'my-bookmarks' ) {
				return true;
			}
		}

		/**
		 *  Load jobs on scroll.
		 */
		public function bpjm_load_more_jobs() {
			if ( isset( $_POST ) ) {
				check_ajax_referer( 'bpjm_load_jobs_nonce', 'ajax_nonce' );
				$paged = $_POST['page_no'];

				$job_dashboard_columns = array(
					'job_title' => __( 'Title', 'bp-job-manager' ),
					'filled'    => __( 'Filled?', 'bp-job-manager' ),
					'date'      => __( 'Date Posted', 'bp-job-manager' ),
					'expires'   => __( 'Listing Expires', 'bp-job-manager' ),
					'actions'   => __( 'Actions', 'bp-job-manager' ),
				);

				$args = array(
					'post_type'           => 'job_listing',
					'post_status'         => array( 'publish', 'expired', 'pending', 'draft', 'preview' ),
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => 10,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => bp_displayed_user_id(),
					'paged'               => $paged,
				);
				$jobs = get_posts( $args );

				foreach ( $jobs as $job ) : ?>
				<tr>
						<?php foreach ( $job_dashboard_columns as $key => $column ) : ?>
						<td class="<?php echo esc_attr( $key ); ?>">
							<?php if ( 'job_title' === $key ) : ?>
								<?php if ( $job->post_status == 'publish' ) : ?>
									<a href="<?php echo esc_url( get_permalink( $job->ID ) ); ?>"><?php wpjm_the_job_title( $job ); ?></a>
								<?php else : ?>
									<?php wpjm_the_job_title( $job ); ?> <small>(<?php the_job_status( $job ); ?>)</small>
								<?php endif; ?>
								<?php echo is_position_featured( $job ) ? '<span class="featured-job-icon" title="' . esc_attr__( 'Featured Job', 'bp-job-manager' ) . '"></span>' : ''; ?>
								<?php if ( bp_loggedin_user_id() == bp_displayed_user_id() ) { ?>
								<ul class="job-dashboard-actions">
									<?php
										$actions = array();

									switch ( $job->post_status ) {
										case 'publish':
											if ( WP_Job_Manager_Post_Types::job_is_editable( $job->ID ) ) {
												$actions['edit'] = array(
													'label' => __( 'Edit', 'bp-job-manager' ),
													'nonce' => false,
												);
											}
											if ( is_position_filled( $job ) ) {
												$actions['mark_not_filled'] = array(
													'label' => __( 'Mark not filled', 'bp-job-manager' ),
													'nonce' => true,
												);
											} else {
												$actions['mark_filled'] = array(
													'label' => __( 'Mark filled', 'bp-job-manager' ),
													'nonce' => true,
												);
											}

											$actions['duplicate'] = array(
												'label' => __( 'Duplicate', 'bp-job-manager' ),
												'nonce' => true,
											);
											break;
										case 'expired':
											if ( job_manager_get_permalink( 'submit_job_form' ) ) {
												$actions['relist'] = array(
													'label' => __( 'Relist', 'bp-job-manager' ),
													'nonce' => true,
												);
											}
											break;
										case 'pending_payment':
										case 'pending':
											if ( WP_Job_Manager_Post_Types::job_is_editable( $job->ID ) ) {
												$actions['edit'] = array(
													'label' => __( 'Edit', 'bp-job-manager' ),
													'nonce' => false,
												);
											}
											break;
										case 'draft':
										case 'preview':
											$actions['continue'] = array(
												'label' => __( 'Continue Submission', 'bp-job-manager' ),
												'nonce' => true,
											);
											break;
									}

										$actions['delete'] = array(
											'label' => __( 'Delete', 'bp-job-manager' ),
											'nonce' => true,
										);
										foreach ( $actions as $action => $value ) {

											$action_url = add_query_arg(
												array(
													'action' => $action,
													'job_id' => $job->ID,
												)
											);
											if ( $value['nonce'] ) {
												$action_url = wp_nonce_url( $action_url, 'job_manager_my_job_actions' );
											}
												echo '<li><a href="' . esc_url( $action_url ) . '" class="job-dashboard-action-' . esc_attr( $action ) . '">' . esc_html( $value['label'] ) . '</a></li>';

										}
										?>
								</ul>
							<?php } ?>

							<?php elseif ( 'date' === $key ) : ?>
								<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $job->post_date ) ) ); ?>
							<?php elseif ( 'expires' === $key ) : ?>
								<?php echo esc_html( $job->_job_expires ? date_i18n( get_option( 'date_format' ), strtotime( $job->_job_expires ) ) : '&ndash;' ); ?>
							<?php elseif ( 'filled' === $key ) : ?>
								<?php echo is_position_filled( $job ) ? '&#10004;' : '&ndash;'; ?>
							<?php else : ?>
								<?php do_action( 'job_manager_job_dashboard_column_' . $key, $job ); ?>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php endforeach; ?>
				<?php
				wp_reset_postdata();
			}
			wp_die();
		}

		/**
		 * Register a new tab in member's profile - Jobs.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_member_profile_jobs_tab() {
			global $bp_job_manager;
			$displayed_uid  = bp_displayed_user_id();
			$displayed_user = get_userdata( $displayed_uid );
			$curr_user      = wp_get_current_user();
			$parent_slug    = 'jobs';
			$jobs_tab_link  = bp_core_get_userlink( $displayed_uid, false, true ) . $parent_slug . '/';

			if ( ! empty( $curr_user->roles ) && ! empty( $displayed_user->roles ) ) {
				/**
				 * Jobs tab - for the roles allowed for job posting.
				 */
				$match_post_job_roles_curr_usr = array_intersect( $bp_job_manager->post_job_user_roles, $curr_user->roles );
				$match_post_job_roles_disp_usr = array_intersect( $bp_job_manager->post_job_user_roles, $displayed_user->roles );
				if ( ! empty( $match_post_job_roles_curr_usr ) || ! empty( $match_post_job_roles_disp_usr ) ) {

					// Count jobs.
					$args          = array(
						'post_type'      => 'job_listing',
						'post_status'    => 'any',
						'author'         => $displayed_uid,
						'posts_per_page' => -1,
						'orderby'        => 'post_date',
						'order'          => 'ASC',
					);
					$my_jobs_count = count( get_posts( $args ) );

					bp_core_new_nav_item(
						array(
							'name'                    => sprintf( __( 'Jobs <span>%d</span>', 'bp-job-manager' ), $my_jobs_count ),
							'slug'                    => $parent_slug,
							'screen_function'         => array( $this, 'bpjm_jobs_tab_function_to_show_screen' ),
							'position'                => 75,
							'default_subnav_slug'     => 'my-jobs',
							'show_for_displayed_user' => true,
						)
					);

					bp_core_new_subnav_item(
						array(
							'name'            => __( 'Jobs', 'bp-job-manager' ),
							'slug'            => 'my-jobs',
							'parent_url'      => $jobs_tab_link . 'my-jobs',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bpjm_my_jobs_show_screen' ),
							'position'        => 100,
							'link'            => $jobs_tab_link . 'my-jobs',
						)
					);
					if ( get_current_user_id() == $displayed_uid ) {						

						$wpjm_bookmarks_active = $wpjm_active = in_array( 'wp-job-manager-bookmarks/wp-job-manager-bookmarks.php', get_option( 'active_plugins' ) );
						if ( true === $wpjm_bookmarks_active ) {
							bp_core_new_subnav_item(
								array(
									'name'            => __( 'My Bookmarks', 'bp-job-manager' ),
									'slug'            => 'my-bookmarks',
									'parent_url'      => $jobs_tab_link . 'my-bookmarks',
									'parent_slug'     => $parent_slug,
									'screen_function' => array( $this, 'bpjm_bookmarked_jobs_show_screen' ),
									'position'        => 200,
									'link'            => $jobs_tab_link . 'my-bookmarks',
								)
							);
						}

						$wpjm_alerts_active = $wpjm_active = in_array( 'wp-job-manager-alerts/wp-job-manager-alerts.php', get_option( 'active_plugins' ) );
						if ( true === $wpjm_alerts_active ) {
							bp_core_new_subnav_item(
								array(
									'name'            => __( 'Job Alerts', 'bp-job-manager' ),
									'slug'            => 'job-alerts',
									'parent_url'      => $jobs_tab_link . 'job-alerts',
									'parent_slug'     => $parent_slug,
									'screen_function' => array( $this, 'bpjm_job_alerts_show_screen' ),
									'position'        => 200,
									'link'            => $jobs_tab_link . 'job-alerts',
								)
							);
						}

						bp_core_new_subnav_item(
							array(
								'name'            => __( 'Post Job', 'bp-job-manager' ),
								'slug'            => 'post-job',
								'parent_url'      => $jobs_tab_link . 'post-job',
								'parent_slug'     => $parent_slug,
								'screen_function' => array( $this, 'bpjm_post_job_show_screen' ),
								'position'        => 200,
								'link'            => $jobs_tab_link . 'post-job',
							)
						);
					}
				}
			} else {
				bp_core_new_nav_item(
					array(
						'name'                    => __( 'Jobs <span>%d</span>', 'bp-job-manager' ),
						'slug'                    => 'jobs',
						'screen_function'         => array( $this, 'bpjm_jobs_tab_show_screen' ),
						'position'                => 75,
						'default_subnav_slug'     => 'jobs',
						'show_for_displayed_user' => true,
					)
				);
			}
		}

		/**
		 * Screen function for job tab.
		 *
		 * @since    2.3.1
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_jobs_tab_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_my_jobs_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_my_jobs_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

		}

		/**
		 * Screen function for post job menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_post_job_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_post_job_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_post_job_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Post Job - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_post_job_tab_function_to_show_title() {
			esc_html_e( 'Post Job', 'bp-job-manager' );
		}

		/**
		 * Post Job - Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_post_job_tab_function_to_show_content() {
			echo do_shortcode( '[submit_job_form]' );
		}

		/**
		 * Screen function for listing all my jobs in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_jobs_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_my_jobs_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_my_jobs_tab_function_to_show_content' ) );
			add_filter( 'job_manager_my_job_actions', array( $this, 'bpjm_hide_job_actions' ), 999, 2 );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * My Jobs - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_jobs_tab_function_to_show_title() {
			if ( bp_loggedin_user_id() == bp_displayed_user_id() ) {
				esc_html_e( 'My Jobs', 'bp-job-manager' );
			} elseif ( is_user_logged_in() && bp_displayed_user_id() ) {
				$author_name = bp_core_get_user_displayname( bp_displayed_user_id() );
				esc_html_e( $author_name . '\'s' . ' ' . 'Jobs', 'bp-job-manager' );
			} else {
				esc_html_e( 'Jobs', 'bp-job-manager' );
			}
		}

		/**
		 * My Jobs - Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_jobs_tab_function_to_show_content() {
			if ( bp_loggedin_user_id() == bp_displayed_user_id() ) {
				echo do_shortcode( '[job_dashboard]' );
			} else {
				$job_args = array(
					'post_type'           => 'job_listing',
					'post_status'         => 'any',
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => 10,
					'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * 25,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => bp_displayed_user_id(),
				);
				$jobs     = get_posts( $job_args );
				include BPJM_PLUGIN_PATH . '/public/templates/bpjm-job-listing.php';
			}

		}

		/** Hide job actions from display member profile.
		 *
		 * @param $job_actions
		 * @param $job
		 * @return mixed
		 */
		public function bpjm_hide_job_actions( $job_actions, $job ) {
			if ( ( bp_loggedin_user_id() != bp_displayed_user_id() ) && ! bp_is_user_profile() ) {
				if ( ! empty( $job ) && $job->post_status == 'publish' ) {
					unset( $job_actions['edit'] );
					unset( $job_actions['delete'] );
					unset( $job_actions['mark_filled'] );
					unset( $job_actions['duplicate'] );
					unset( $job_actions['mark_not_filled'] );
				}
			}
			return $job_actions;
		}

		/**
		 * Screen function for listing all my jobs in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_jobs_listing_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_my_jobs_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_jobs_listing_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}


		/**
		 * Screen function for listing all my bookmarked jobs in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_bookmarked_jobs_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_bookmarked_jobs_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_bookmarked_jobs_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * My Bookmarked Jobs - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_bookmarked_jobs_tab_function_to_show_title() {
			esc_html_e( 'My Bookmarks', 'bp-job-manager' );
		}

		/**
		 * My Bookmarked Jobs - Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_bookmarked_jobs_tab_function_to_show_content() {
			echo do_shortcode( '[my_bookmarks]' );
		}

		/**
		 * Screen function for listing all my bookmarked jobs in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_job_alerts_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_job_alerts_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_job_alerts_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * My Bookmarked Jobs - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_job_alerts_tab_function_to_show_title() {
			esc_html_e( 'Job Alerts', 'bp-job-manager' );
		}

		/**
		 * My Bookmarked Jobs - Content.
		 * the job listing by [job_dashboard] by wp-job-manager plugin.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_job_alerts_tab_function_to_show_content() {
			echo do_shortcode( '[job_alerts]' );
		}

		/**
		 * Modify alert short_code handler for buddypress
		 */
		public function bpjm_modify_job_alert_action_handler() {
			global $post;
			if ( is_buddypress() ) {
				if( class_exists( 'WP_Job_Manager_Alerts_Shortcodes' ) ){
					remove_action( 'wp', array( 'WP_Job_Manager_Alerts_Shortcodes', 'shortcode_action_handler' ) );
					$alert_handler = new WP_Job_Manager_Alerts_Shortcodes();
					$alert_handler->job_alerts_handler();
				}
			}
		}

		/**
		 * Action performed to override the arguments passed in job listing process.
		 * the job listing by [job_dashboard] by wp-job-manager plugin.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    string $job_dashboard_job_listing_args job listing arguments.
		 * @return   string $job_dashboard_job_listing_args return job listing arguments.
		 */
		public function bpjm_job_dashboard_user_id( $job_dashboard_job_listing_args ) {
			$displayed_uid  = bp_displayed_user_id();
			$displayed_user = get_userdata( $displayed_uid );
			$curr_user      = wp_get_current_user();

			if ( ! empty( $curr_user->roles ) && ! empty( $displayed_user->roles ) ) {
				$job_dashboard_job_listing_args = array(
					'post_type'           => 'job_listing',
					'post_status'         => 'any',
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => 10,
					'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * 25,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => bp_displayed_user_id(),
				);
			}
			return $job_dashboard_job_listing_args;
		}

		/**
		 * Action performed to override whether the user is allowed to edit the pending jobs.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    string $allowed members pending job post.
		 * @return   string $allowed return true or false for members pending job post.
		 */
		public function bpjm_allow_user_to_edit_pending_jobs( $allowed ) {
			$allowed = true;
			return $allowed;
		}

		public function bpjm_filter_wpjm_user_can_edit_published_submissions( $allowed ) {
			$allowed = true;
			return $allowed;
		}

		/**
		 * Action performed to hide the actions on job dashboard.
		 * when the loggedin user id != displayed user id.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    string $actions contain job action.
		 * @param    string $job contain job data.
		 * @return   string $actions contain job action.
		 */
		public function bpjm_job_dashboard_job_actions( $actions, $job ) {
			if ( bp_is_user_profile() ) {
				if ( bp_displayed_user_id() != get_current_user_id() ) {
					$actions = array();
				}
			}
			return $actions;
		}
		/**
		 * Action performed to add a column in job dashboard table.
		 *
		 * @param string $job_dashboard_cols contain dashboard column data.
		 * @return array|string
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_job_dashboard_cols( $job_dashboard_cols ) {
			if ( 'my-jobs' == bp_current_action() ) {
				if ( ! bp_is_user_profile() && ( bp_loggedin_user_id() != bp_displayed_user_id() ) ) {
					$column             = array(
						'actions' => __( 'Actions', 'bp-job-manager' ),
					);
					$job_dashboard_cols = array_merge( $job_dashboard_cols, $column );
				}
				// if ( array_key_exists( 'applications', $job_dashboard_cols ) ) {
				// unset( $job_dashboard_cols['applications'] );
				// }
			}
			return $job_dashboard_cols;
		}

		/**
		 * Action performed to add a column content in job dashboard.
		 * Column added above - Actions.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    string $job contain job data.
		 */
		public function bpjm_job_dashboard_actions_col_content( $job ) {
			global $bp_job_manager;
			if ( ! bp_is_user_profile() && ( bp_loggedin_user_id() != bp_displayed_user_id() ) ) {
				$user_id               = get_current_user_id();
				$job_application_page  = get_permalink( $bp_job_manager->job_application_pgid );
				$job_application_page .= '?args=' . $job->ID;

				if ( ! is_position_filled( $job ) ) {
					// $wpjm_applications_active = in_array( 'wp-job-manager-applications/wp-job-manager-applications.php', get_option( 'active_plugins' ) );

					if ( is_user_logged_in() && class_exists( 'WP_Job_Manager_Applications' ) ) {

						if ( user_has_applied_for_job( $user_id, $job->ID ) ) {
							get_job_manager_template( 'applied-notice.php', array(), 'wp-job-manager-applications', JOB_MANAGER_APPLICATIONS_PLUGIN_DIR . '/templates/' );
						} else {
							?>
							<div class="generic-button" id="bpjm-job-application-btn">
								<a href="javascript:void(0);" data-url="<?php echo esc_attr( $job_application_page ); ?>"><?php esc_html_e( 'Apply', 'bp-job-manager' ); ?></a>
							</div>
							<?php
						}
					}
				} else {
					echo '<li class="position-filled">This position has been filled</li>';
				}
			}
		}

		/**
		 * Action performed to override the template of the page - Apply To Job.
		 *
		 * @param string $template contain page template data.
		 * @return string
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_job_application_page( $template ) {
			global $bp_job_manager, $post;
			if ( ! empty( $post ) && $bp_job_manager->job_application_pgid == $post->ID ) {
				$file = BPJM_PLUGIN_PATH . 'public/templates/bpjm-job-application.php';
				if ( file_exists( $file ) ) {
					$template = $file;
				}
			}
			return $template;
		}

		/**
		 * Register a new tab in member's profile - Resumes.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_member_profile_resumes_tab() {
			global $bp_job_manager;
			$displayed_uid    = bp_displayed_user_id();
			$displayed_user   = get_userdata( $displayed_uid );
			$curr_user        = wp_get_current_user();
			$parent_slug      = 'resumes';
			$resumes_tab_link = bp_core_get_userlink( $displayed_uid, false, true ) . $parent_slug . '/';

			if ( ! empty( $curr_user->roles ) && ! empty( $displayed_user->roles ) ) {
				/**
				 * Resumes tab - for the roles allowed for job posting.
				 */
				$match_apply_job_roles_curr_usr = array_intersect( $bp_job_manager->apply_job_user_roles, $curr_user->roles );
				$match_apply_job_roles_disp_usr = array_intersect( $bp_job_manager->apply_job_user_roles, $displayed_user->roles );
				if ( ! empty( $match_apply_job_roles_curr_usr ) || ! empty( $match_apply_job_roles_disp_usr ) ) {
					// Count resumes.
					$args             = array(
						'post_type'      => 'resume',
						'post_status'    => 'any',
						'author'         => $displayed_uid,
						'posts_per_page' => -1,
						'orderby'        => 'post_date',
						'order'          => 'ASC',
					);
					$my_resumes_count = count( get_posts( $args ) );

					bp_core_new_nav_item(
						array(
							'name'                    => sprintf( __( 'Resumes <span>%d</span>', 'bp-job-manager' ), $my_resumes_count ),
							'slug'                    => $parent_slug,
							'screen_function'         => array( $this, 'bpjm_resumes_tab_function_to_show_screen' ),
							'position'                => 75,
							'default_subnav_slug'     => 'my-resumes',
							'show_for_displayed_user' => true,
						)
					);
					// My Resumes.
					if ( bp_loggedin_user_id() == bp_displayed_user_id() ) {
						$resume = __( 'My Resumes', 'bp-job-manager' );
					} else {
						$author_name = bp_core_get_user_displayname( bp_displayed_user_id() );
						$resume      = __( ucfirst( $author_name ) . '\'s' . ' ' . 'Resumes', 'bp-job-manager' );
					}
					bp_core_new_subnav_item(
						array(
							'name'            => $resume,
							'slug'            => 'my-resumes',
							'parent_url'      => $resumes_tab_link . 'my-resumes',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bpjm_my_resumes_show_screen' ),
							'position'        => 100,
							'link'            => $resumes_tab_link . 'my-resumes',
						)
					);

					if ( get_current_user_id() == $displayed_uid ) {
						// Add Resume.
						bp_core_new_subnav_item(
							array(
								'name'            => __( 'Add Resume', 'bp-job-manager' ),
								'slug'            => 'add-resume',
								'parent_url'      => $resumes_tab_link . 'add-resume',
								'parent_slug'     => $parent_slug,
								'screen_function' => array( $this, 'bpjm_add_resume_show_screen' ),
								'position'        => 200,
								'link'            => $resumes_tab_link . 'add-resume',
							)
						);
						// check if BuddyPress Job manager - Application manager is active.
						$wpjm_applications_active = in_array( 'wp-job-manager-applications/wp-job-manager-applications.php', get_option( 'active_plugins' ) );
						if ( $wpjm_applications_active == true ) {
							// Applied Jobs
							bp_core_new_subnav_item(
								array(
									'name'            => __( 'Applied Jobs', 'bp-job-manager' ),
									'slug'            => 'applied-jobs',
									'parent_url'      => $resumes_tab_link . 'applied-jobs',
									'parent_slug'     => $parent_slug,
									'screen_function' => array( $this, 'bpjm_applied_jobs_show_screen' ),
									'position'        => 200,
									'link'            => $resumes_tab_link . 'applied-jobs',
								)
							);
						}
					}
				}
			} else {
				bp_core_new_nav_item(
					array(
						'name'                    => __( 'Resumes <span>%d</span>', 'bp-job-manager' ),
						'slug'                    => $parent_slug,
						'screen_function'         => array( $this, 'bpjm_my_resumes_show_screen' ),
						'position'                => 75,
						'default_subnav_slug'     => 'my-resumes',
						'show_for_displayed_user' => true,
					)
				);
			}
		}

		/**
		 * Screen function for add resume menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_add_resume_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_add_resume_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_add_resume_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Add Resume - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_add_resume_tab_function_to_show_title() {
			esc_html_e( 'Add Resume', 'bp-job-manager' );
		}

		/**
		 * Add Resume - Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_add_resume_tab_function_to_show_content() {
			echo do_shortcode( '[submit_resume_form]' );
		}

		/**
		 * Screen function for listing all my resumes in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_resumes_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_my_resumes_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_my_resumes_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * My Resumes - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_resumes_tab_function_to_show_title() {
			if ( bp_loggedin_user_id() == bp_displayed_user_id() ) {
				esc_html_e( 'My Resumes', 'bp-job-manager' );
			} else {
				$author_name = bp_core_get_user_displayname( bp_displayed_user_id() );
				esc_html_e( ucfirst( $author_name ) . '\'s' . ' ' . 'Resumes', 'bp-job-manager' );
			}

		}

		/**
		 * My Resumes - Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_resumes_tab_function_to_show_content() {
			if ( ! is_user_logged_in() ) {
				?>
			<div id="resume-manager-candidate-dashboard">
				<p class="account-sign-in">
					<?php _e( 'You need to be signed in to manage your resumes.', 'wp-job-manager-resumes' ); ?>
					 <a class="button" href="<?php echo apply_filters( 'resume_manager_candidate_dashboard_login_url', wp_login_url( get_permalink() ) ); ?>"><?php _e( 'Sign in', 'bp-job-manager' ); ?></a></p>
			</div>
				<?php
			}
			$args    = apply_filters(
				'bp_job_manager_get_dashboard_resumes_args',
				array(
					'post_type'           => 'resume',
					'post_status'         => array( 'publish', 'expired', 'pending', 'hidden' ),
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => 10,
					'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * 10,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => bp_displayed_user_id(),
				)
			);
			$resumes = new WP_Query();

			$candidate_dashboard_columns = array(
				'resume-title'       => __( 'Name', 'wp-job-manager-resumes' ),
				'candidate-title'    => __( 'Title', 'wp-job-manager-resumes' ),
				'candidate-location' => __( 'Location', 'wp-job-manager-resumes' ),
				'resume-category'    => __( 'Category', 'wp-job-manager-resumes' ),
				'date'               => __( 'Date Posted', 'wp-job-manager-resumes' ),
			);

			if ( ! get_option( 'resume_manager_enable_categories' ) ) {
					unset( $candidate_dashboard_columns['resume-category'] );
			}

			get_job_manager_template(
				'bpjm-resume-listing.php',
				array(
					'resumes'                     => $resumes->query( $args ),
					'max_num_pages'               => $resumes->max_num_pages,
					'candidate_dashboard_columns' => $candidate_dashboard_columns,
				),
				'bp-job-manager',
				BPJM_PLUGIN_PATH . '/public/templates/'
			);
		}

		/**
		 * Screen function for listing all applied jobs in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_applied_jobs_show_screen() {
			add_action( 'bp_template_title', array( $this, 'bpjm_applied_jobs_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'bpjm_applied_jobs_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Applied Jobs - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_applied_jobs_tab_function_to_show_title() {
			esc_html_e( 'Applied Jobs', 'bp-job-manager' );
		}

		/**
		 * Applied Jobs - Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_applied_jobs_tab_function_to_show_content() {
			$file = BPJM_PLUGIN_PATH . 'public/templates/bpjm-my-applied-jobs.php';
			if ( file_exists( $file ) ) {
				include $file;
			}
		}

		/**
		 * Action performed to override the arguments passed in resume listing process.
		 * the job listing by [candidate_dashboard] by wp-job-manager-resumes plugin.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    string $candidate_dashboard_resume_listing_args resume listing arguments.
		 * @return   string $candidate_dashboard_resume_listing_args resume listing arguments.
		 */
		public function bpjm_resume_dashboard_user_id( $candidate_dashboard_resume_listing_args ) {
			if ( bp_is_user() ) {
				$candidate_dashboard_resume_listing_args = array(
					'post_type'           => 'resume',
					'post_status'         => array( 'publish', 'expired', 'pending', 'hidden' ),
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => 25,
					'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * 25,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => bp_displayed_user_id(),
				);
			}
			return $candidate_dashboard_resume_listing_args;
		}

		/**
		 * Action performed to hide the actions on candidate dashboard.
		 * when the loggedin user id != displayed user id.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    string $actions contain job action.
		 * @param    string $job contain job data.
		 */
		public function bpjm_candidate_dashboard_resume_actions( $actions, $job ) {
			if ( bp_is_user() ) {
				if ( bp_displayed_user_id() != get_current_user_id() ) {
					$actions = array();
				}
			}
			return $actions;
		}

		/**
		 * Function to show resume fields at buddypress profile page.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_bp_profile_field_item() {
			global $bp_job_manager;
			if ( get_option( 'bpjm_profile_resume_show_id' ) ) {
				$selected_post = get_option( 'bpjm_profile_resume_show_id' );
				if ( isset( $bp_job_manager->bpjm_resume_at_profile ) && $bp_job_manager->bpjm_resume_at_profile == 'yes' ) {
					bpjm_show_resume_at_profile( $selected_post );
				}
			} else {
				$user_id = bp_displayed_user_id();
				$where   = get_posts_by_author_sql( 'resume', true, $user_id, '' );
				global $wpdb;
				$query = "SELECT ID FROM $wpdb->posts $where ORDER BY post_modified DESC";
				$post  = $wpdb->get_row( $query );
				if ( $post && isset( $bp_job_manager->bpjm_resume_at_profile ) && $bp_job_manager->bpjm_resume_at_profile == 'yes' ) {
					bpjm_show_resume_at_profile( $post->ID );
				}
			}
		}

		/**
		 * Function to add settings under profile visibility-buddypress.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_resume_settings_before_submit() {
			$user_id = bp_displayed_user_id();
			$where   = get_posts_by_author_sql( 'resume', true, $user_id, '' );
			global $wpdb, $bp_job_manager;
			$query       = "SELECT ID, post_title FROM $wpdb->posts $where ORDER BY post_modified DESC";
			$post        = $wpdb->get_results( $query );
			$single_post = $wpdb->get_row( $query );
			if ( ! empty( $single_post ) ) {
				$selected_post = $single_post->ID;
			}

			if ( get_option( 'bpjm_profile_resume_show_id' ) ) {
				$selected_post = get_option( 'bpjm_profile_resume_show_id' );
			}

			if ( ! empty( $selected_post ) ) {
				$fields_display = get_option( 'bpjm_display_fields' );
				$display_resume = ( isset( $fields_display['display_resume'] ) ) ? $fields_display['display_resume'] : 'no';
				if ( $display_resume == 'yes' ) {
					$display_class = 'display-true';
				} else {
					$display_class = 'display-false';
				}
				if ( $post && isset( $bp_job_manager->bpjm_resume_at_profile ) && $bp_job_manager->bpjm_resume_at_profile == 'yes' ) {
					?>
			<table class="profile-settings">
				<thead>
					<tr>
						<th class="title field-group-name"><?php _e( 'BuddyPress Resumes', 'buddypress' ); ?></th>
						<th class="title"><?php _e( 'Available Resumes', 'buddypress' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="field_name field_type_textbox">
						<td class="field-name"><?php _e( 'Display resume at profile page', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input class="bpjm-display-resume-checkbox" type="checkbox" value="yes" name="bpjm_display[display_resume]"
					<?php
					if ( ! empty( $fields_display['display_resume'] ) ) {
						checked( $fields_display['display_resume'], 'yes' );}
					?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Select resume to display at profile', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<select name="bpjm_prof_resume_show_postid">
					<?php
					foreach ( $post as $key => $value ) {
						echo "<option value='" . $value->ID . "' " . selected( $selected_post, $value->ID, false ) . '>' . get_the_candidate_title( $value->ID ) . '</option>';
					}
					?>
							</select>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display e-mail field', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[email]"
						<?php
						if ( ! empty( $fields_display['email'] ) ) {
							checked( $fields_display['email'], 'yes' );}
						?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display professional title', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[prof_title]"
							<?php
							if ( ! empty( $fields_display['prof_title'] ) ) {
								checked( $fields_display['prof_title'], 'yes' );}
							?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display location', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[location]"
							<?php
							if ( ! empty( $fields_display['location'] ) ) {
								checked( $fields_display['location'], 'yes' );}
							?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display video link', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[video]"
							<?php
							if ( ! empty( $fields_display['video'] ) ) {
								checked( $fields_display['video'], 'yes' );}
							?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display resume contents(description)', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[description]"
							<?php
							if ( ! empty( $fields_display['description'] ) ) {
								checked( $fields_display['description'], 'yes' );}
							?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display URL(s)', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[url]"
							<?php
							if ( ! empty( $fields_display['url'] ) ) {
								checked( $fields_display['url'], 'yes' );}
							?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display education', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[education]"
							<?php
							if ( ! empty( $fields_display['education'] ) ) {
								checked( $fields_display['education'], 'yes' );}
							?>
								>
						</td>
					</tr>
					<tr class="field_name field_type_textbox resume-fields-row <?php echo $display_class; ?>">
						<td class="field-name"><?php _e( 'Display experience', 'buddypress' ); ?></td>
						<td class="field-visibility">
							<input type="checkbox" value="yes" name="bpjm_display[experience]"
							<?php
							if ( ! empty( $fields_display['experience'] ) ) {
								checked( $fields_display['experience'], 'yes' );}
							?>
								>
						</td>
					</tr>
				</tbody>
			</table>
							<?php
				}
			}
		}

		/**
		 * Function to update bp job manager resume fields at profile page options.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function custom_bp_init() {
			if ( isset( $_POST['xprofile-settings-submit'] ) && isset( $_POST['bpjm_prof_resume_show_postid'] ) ) {
				$resume_id = $_POST['bpjm_prof_resume_show_postid'];
				update_option( 'bpjm_profile_resume_show_id', $resume_id );
			}
			if ( isset( $_POST['xprofile-settings-submit'] ) ) {
				$display_fields = $_POST['bpjm_display'];
				update_option( 'bpjm_display_fields', $display_fields );
			}
		}

		/**
		 * Function to call wp job manager plugin's WP_Job_Manager_Shortcodes class job_dashboard_handler function for mark filled functionality on buddypress job tab.
		 *
		 * @since    1.0.6
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_shortcode_action_handler() {
			$action          = filter_input( INPUT_GET, 'action' );
			$job_id_param    = filter_input( INPUT_GET, 'job_id' );
			$resume_id_param = filter_input( INPUT_GET, 'resume_id' );

			if ( bp_is_user() && ( null !== $action ) ) {
				if ( ( 'mark_filled' == $action || 'mark_not_filled' == $action || 'duplicate' == $action || 'delete' == $action ) && ( null !== $job_id_param ) ) {
					$wbjob_shortcode_object = new WP_Job_Manager_Shortcodes();
					$wbjob_shortcode_object->job_dashboard_handler();
				}
				if ( ( 'delete' == $action || 'hide' == $action || 'publish' == $action ) && ( null !== $resume_id_param ) ) {
					$wb_resume_shortcode_object = new WP_Resume_Manager_Shortcodes();
					$wb_resume_shortcode_object->candidate_dashboard_handler();
				}
			}
		}

		/**
		 * Function for filter redirect url when duplicate any job on buddypress job tab.
		 *
		 * @since    1.0.6
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_filter_redirect_duplicate_post_url( $location, $status ) {
			if ( bp_is_user() && bp_is_my_profile() ) {
				$query_str = parse_url( $location, PHP_URL_QUERY );
				$job_arr   = explode( '=', $query_str );
				if ( ( 'job_id' == $job_arr[0] ) && ( '0' != $job_arr[1] ) ) {
					$location = bp_core_get_user_domain( bp_displayed_user_id() ) . 'jobs/post-job/?job_id=' . $job_arr[1];
				}
			}
			return $location;
		}

		/**
		 * Function for add private message link on contact candidate button.
		 *
		 * @since    1.0.6
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_add_private_message_link() {
			$url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( get_the_author_meta( 'ID' ) ) );

			echo '<a href="' . $url . '" title="Private Message">' . __( 'Private Message', 'bp-job-manager' ) . '</a>';
		}

		/**
		 * Filters the default activity types to add job post type activity.
		 *
		 * @since 2.1.0
		 *
		 * @param array $types Default activity types to moderate.
		 */
		public function bpolls_add_polls_type_activity( $types ) {
			$types[] = 'bpjm_job_post';
			return $types;
		}

		/**
		 * Register the activity stream actions for job post updates.
		 *
		 * @since 2.1.0
		 */
		public function bpjm_register_job_post_activity_actions() {
			 $bp = buddypress();

			bp_activity_set_action(
				$bp->activity->id,
				'bpjm_job_post',
				__( 'Jobs Update', 'bp-job-manager' ),
				array( $this, 'bpjm_job_post_activity_action_format' ),
				__( 'Jobs', 'bp-job-manager' ),
				array( 'activity', 'group', 'member', 'member_groups' )
			);
		}

		/**
		 * Format 'bpjm_job_post' activity actions.
		 *
		 * @since 2.1.0
		 *
		 * @param string $action   Static activity action.
		 * @param object $activity Activity data object.
		 * @return string $action
		 */
		function bpjm_job_post_activity_action_format( $action, $activity ) {
			$action = sprintf( __( '%s posted a new job update', 'bp-job-manager' ), bp_core_get_userlink( $activity->user_id ) );

			/**
			 * Filters the formatted activity action update string.
			 *
			 * @since 1.0.0
			 *
			 * @param string               $action   Activity action string value.
			 * @param BP_Activity_Activity $activity Activity item object.
			 */
			return apply_filters( 'bp_activity_new_job_post_action', $action, $activity );
		}

		/**
		 * Activity action for job post.
		 *
		 * @since 2.1.0
		 */
		public function bpjm_activity_action_wall_posts( $retval, $activity ) {
			if ( 'bpjm_job_post' !== $activity->type ) {
				return $retval;
			}
			$retval = sprintf( __( '%s posted a job update', 'bp-job-manager' ), bp_core_get_userlink( $activity->user_id ) );
			return $retval;
		}

		/**
		 * Add bp notification on sending job application.
		 *
		 * @since 2.1.0
		 */
		public function bpjm_add_bp_notification_for_job_post( $application_id, $job_id ) {
			global $bp_job_manager;

			if ( isset( $bp_job_manager->bpjm_app_notify ) && $bp_job_manager->bpjm_app_notify == 'yes' ) {
				$job          = get_post( $job_id );
				$job_admin_id = $job->post_author;
				if ( bp_is_active( 'notifications' ) ) {
					bp_notifications_add_notification(
						array(
							'user_id'           => $job_admin_id,
							'item_id'           => $application_id,
							'secondary_item_id' => get_current_user_id(),
							'component_name'    => 'bpjm_notify_actions',
							'component_action'  => 'applied_job',
							'allow_duplicate'   => false,
						// 'is_new'         => 1
						)
					);
				}
			}
		}

		/**
		 * Register component for job application notification.
		 *
		 * @since 2.1.0
		 */
		public function bpjm_comment_get_registered_components( $component_names = array() ) {
			// Force $component_names to be an array.
			if ( ! is_array( $component_names ) ) {
				$component_names = array();
			}
			array_push( $component_names, 'bpjm_notify_actions' );
			return $component_names;
		}

		/**
		 * Job application notification content.
		 *
		 * @since 2.1.0
		 */
		public function bpjm_job_application_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string', $component_action_name, $component_name ) {

			if ( 'applied_job' === $component_action_name ) {

				$applicant_info = get_userdata( $secondary_item_id );
				$applicant_name = $applicant_info->user_login;

				/**
				 * Filters the text used for the notification generated by BuddyPress Registration Options.
				 *
				 * @since 4.3.0
				 *
				 * @param string $value Notification text.
				 */
				$text             = apply_filters( '_bprwg_notification_text', sprintf( esc_html__( '%s has applied to the job post.', 'bp-job-manager' ), $applicant_name ) );
				$application_data = get_post( $item_id );
				$job_id           = $application_data->post_parent;

				$job_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );
				$application_link      = '';
				if ( $job_dashboard_page_id ) {
					$application_link = add_query_arg(
						array(
							'action' => 'show_applications',
							'job_id' => $job_id,
						),
						get_permalink( $job_dashboard_page_id )
					);
				}

				$result = array(
					'text' => $text,
					'link' => $application_link,
				);

				// WordPress Toolbar
				if ( 'string' === $format ) {
					$result = sprintf( '<a class="ab-item" href="%s">%s</a>', $application_link, $text );
				}

				return $result;
			}

			return $action;
		}
	}
endif;
