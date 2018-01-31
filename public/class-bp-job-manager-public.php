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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-job-manager-public.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-job-manager-public.js', array( 'jquery' ), $this->version, false );

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

				$parent_slug   = 'jobs';
				$jobs_tab_link = bp_core_get_userlink( $displayed_uid, false, true ) . $parent_slug . '/';

				bp_core_new_nav_item(
					array(
						'name'                    => __( 'Jobs <span class="no-count">' . $my_jobs_count . '</span>', 'bp-job-manager' ),
						'slug'                    => $parent_slug,
						'screen_function'         => array( $this, 'bpjm_jobs_tab_function_to_show_screen' ),
						'position'                => 75,
						'default_subnav_slug'     => 'my-jobs',
						'show_for_displayed_user' => true,
					)
				);
				bp_core_new_subnav_item(
					array(
						'name'            => __( 'My Jobs', 'bp-job-manager' ),
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
							'name'            => __( 'Post a New Job', 'bp-job-manager' ),
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
		}
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
		esc_html_e( 'Post a New Job', 'bp-job-manager' );
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
		esc_html_e( 'My Jobs', 'bp-job-manager' );
	}

	/**
	 * My Jobs - Content.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function bpjm_my_jobs_tab_function_to_show_content() {
		echo do_shortcode( '[job_dashboard]' );
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
		$job_dashboard_job_listing_args = array(
			'post_type'           => 'job_listing',
			'post_status'         => 'any',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => 25,
			'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * 25,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => bp_displayed_user_id(),
		);
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
		if ( bp_displayed_user_id() != get_current_user_id() ) {
			$actions = array();
		}
		return $actions;
	}

	/**
	 * Action performed to add a column in job dashboard table.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 * @param    string $job_dashboard_cols contain dashboard column data.
	 */
	public function bpjm_job_dashboard_cols( $job_dashboard_cols ) {
		$column             = array(
			'actions' => __( 'Actions', 'bp-job-manager' ),
		);
		$job_dashboard_cols = array_merge( $job_dashboard_cols, $column );
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
		$job_application_page  = get_permalink( $bp_job_manager->job_application_pgid );
		$job_application_page .= '?args=' . $job->ID;
		?>
		<div class="generic-button" id="bpjm-job-application-btn">
			<a href="javascript:void(0);" data-url="<?php echo esc_attr( $job_application_page ); ?>"><?php esc_html_e( 'Apply', 'bp-job-manager' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Action performed to override the template of the page - Apply To Job.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 * @param    string $template contain page template data.
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
		$displayed_uid  = bp_displayed_user_id();
		$displayed_user = get_userdata( $displayed_uid );
		$curr_user      = wp_get_current_user();

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

				$parent_slug      = 'resumes';
				$resumes_tab_link = bp_core_get_userlink( $displayed_uid, false, true ) . $parent_slug . '/';

				bp_core_new_nav_item(
					array(
						'name'                    => __( 'Resumes <span class="no-count">' . $my_resumes_count . '</span>', 'bp-job-manager' ),
						'slug'                    => $parent_slug,
						'screen_function'         => array( $this, 'bpjm_resumes_tab_function_to_show_screen' ),
						'position'                => 75,
						'default_subnav_slug'     => 'my-resumes',
						'show_for_displayed_user' => true,
					)
				);
				// My Resumes.
				bp_core_new_subnav_item(
					array(
						'name'            => __( 'My Resumes', 'bp-job-manager' ),
						'slug'            => 'my-resumes',
						'parent_url'      => $resumes_tab_link . 'my-resumes',
						'parent_slug'     => $parent_slug,
						'screen_function' => array( $this, 'bpjm_my_resumes_show_screen' ),
						'position'        => 100,
						'link'            => $resumes_tab_link . 'my-resumes',
					)
				);

				if ( get_current_user_id() == $displayed_uid ) {
					// Applied Jobs.
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
				}
			}
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
		esc_html_e( 'My Resumes', 'bp-job-manager' );
	}

	/**
	 * My Resumes - Content.
	 *
	 * @since    1.0.0
	 * @author   wbcomdesigns
	 * @access   public
	 */
	public function bpjm_my_resumes_tab_function_to_show_content() {
		echo do_shortcode( '[candidate_dashboard]' );
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
		$candidate_dashboard_resume_listing_args = array(
			'post_type'           => 'resume',
			'post_status'         => 'any',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => 25,
			'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * 25,
			'orderby'             => 'date',
			'order'               => 'desc',
			'author'              => bp_displayed_user_id(),
		);
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
		if ( bp_displayed_user_id() != get_current_user_id() ) {
			$actions = array();
		}
		return $actions;
	}
}
