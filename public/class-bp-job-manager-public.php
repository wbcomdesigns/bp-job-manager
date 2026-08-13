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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bp_Job_Manager_Public' ) ) :

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
		 * Total pages in the profile job listing, set by get_jobs_max_num_pages().
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      int    $max_num_pages    Maximum pages.
		 */
		public $max_num_pages = 0;

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
			add_action( 'transition_post_status', array( $this, 'flush_job_listings_transient_on_update' ), 10, 3 );
			add_action( 'deleted_post', array( $this, 'flush_job_listings_transient_on_delete' ), 10, 2 );
		}

		/**
		 * The BuddyPress profile actions this plugin owns.
		 *
		 * Every screen listed here renders a WP Job Manager (or Resume Manager)
		 * surface inside a member profile, so all of them need this plugin's
		 * CSS/JS *and* need to answer true to WPJM core's `has_wpjm_shortcode`
		 * gate. This list is the single source of truth for that set — it used
		 * to be duplicated in four places and had already drifted apart, which
		 * silently dropped the stylesheet on `jobs` (fixed 2026-07-10) and both
		 * assets on `job-alerts` / `my-bookmarks`.
		 *
		 * The bare `jobs` action is the no-post-role fallback screen registered
		 * by bpjm_member_profile_jobs_tab(); it has no `my-jobs` subnav, so
		 * bp_current_action() resolves to `jobs` there and it must be included.
		 *
		 * @since 3.0.0
		 * @return string[] BuddyPress action slugs owned by this plugin.
		 */
		public function bpjm_get_owned_actions() {
			/**
			 * Filters the profile actions treated as job/resume screens.
			 *
			 * @since 3.0.0
			 * @param string[] $actions BuddyPress action slugs.
			 */
			return apply_filters(
				'bpjm_owned_profile_actions',
				array( 'jobs', 'my-jobs', 'applied-jobs', 'post-job', 'my-resumes', 'add-resume', 'job-alerts', 'my-bookmarks' )
			);
		}

		/**
		 * Whether the current BuddyPress action is a screen this plugin owns.
		 *
		 * @since 3.0.0
		 * @return bool True when the current action renders a job/resume screen.
		 */
		public function bpjm_is_owned_action() {
			return in_array( bp_current_action(), $this->bpjm_get_owned_actions(), true );
		}

		/**
		 * The label for a member's Jobs or Resumes screen.
		 *
		 * THE single source for these four strings. The profile nav item and the
		 * screen heading for the same screen are produced from one call, so the
		 * tab a member clicks and the heading they land on can never disagree.
		 *
		 * They used to be four independent literals and had drifted every way
		 * they could: the Jobs nav said "%s's Jobs" while the Jobs heading said
		 * "%s Jobs"; the Resumes pair had the same split; the nav labels ran the
		 * display name through ucfirst() while the headings did not; and the
		 * headings hid the name from logged-out visitors while the nav showed it.
		 *
		 * The name is no longer passed through ucfirst(). A display name is
		 * whatever the member chose to call themselves - re-capitalising it is
		 * the plugin overriding the member, and it is meaningless for scripts
		 * with no letter case.
		 *
		 * @since 3.0.0
		 *
		 * @param string $screen  Either 'jobs' or 'resumes'.
		 * @param int    $user_id Displayed member ID.
		 * @return string Untranslated-safe, ready to escape at the point of output.
		 */
		public function bpjm_get_member_screen_label( $screen, $user_id ) {
			$user_id  = (int) $user_id;
			$is_owner = ( bp_loggedin_user_id() === $user_id && $user_id );

			if ( 'resumes' === $screen ) {
				if ( $is_owner ) {
					return __( 'My Resumes', 'bp-job-manager' );
				}

				/* translators: %s: display name of the member whose profile is being viewed. */
				return sprintf( __( 'Resumes by %s', 'bp-job-manager' ), bp_core_get_user_displayname( $user_id ) );
			}

			if ( $is_owner ) {
				return __( 'My Jobs', 'bp-job-manager' );
			}

			/*
			 * "Jobs by %s", not "%s Jobs" or "%s's Jobs".
			 *
			 * Both old forms hard-coded English word order into the string.
			 * "%s Jobs" rendered "Varun Jobs", which is not English and gives a
			 * translator nowhere to put their language's preposition - es_ES gave
			 * up and reused "Empleos %s" for BOTH this and the "Jobs %s" count
			 * badge, making the two indistinguishable in the catalogue. "%s's
			 * Jobs" baked in the English possessive, which no other language
			 * forms that way. "Jobs by %s" carries the relationship in the source
			 * so each locale supplies its own connector ("de", "von", "di").
			 */
			/* translators: %s: display name of the member whose profile is being viewed. */
			return sprintf( __( 'Jobs by %s', 'bp-job-manager' ), bp_core_get_user_displayname( $user_id ) );
		}

		/**
		 * Job Listing Maximum pages.
		 *
		 * @return void
		 */
		public function get_jobs_max_num_pages() {
			$per_page = get_option( 'job_manager_per_page' );

			// Defining allowed actions once.
			$allowed_actions = array( 'my-jobs', 'jobs' );

			if ( in_array( bp_current_action(), $allowed_actions, true ) ) {
				$args = array(
					'post_type'           => 'job_listing',
					'post_status'         => array( 'publish', 'expired', 'pending', 'draft', 'preview' ),
					'ignore_sticky_posts' => true,
					'posts_per_page'      => $per_page,
					'offset'              => ( max( 1, get_query_var( 'paged' ) ) - 1 ) * $per_page,
					'orderby'             => 'date',
					'order'               => 'DESC',
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

			if ( $this->bpjm_needs_public_assets() ) {
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-job-manager-public.css', array(), $this->version, 'all' );
			}
		}

		/**
		 * Whether the current request renders anything this plugin styles.
		 *
		 * One predicate rather than a chain of separate enqueue calls, which is
		 * how the stylesheet previously came to be registered twice for the same
		 * handle on dashboard pages.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		public function bpjm_needs_public_assets() {
			if ( $this->bpjm_is_owned_action() ) {
				return true;
			}

			/*
			 * The standalone "Apply To Job" page carries our replacement spacing
			 * for the parts of the form WP Job Manager Applications scopes to
			 * `.single_job_listing`, so our stylesheet has to load there. This
			 * page was in no asset condition at all before.
			 */
			if ( $this->bpjm_is_apply_page() ) {
				return true;
			}

			global $post;

			return is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'job_dashboard' );
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
			// Global $wp_query.
			$get_dashboard_page_id = (int) get_option( 'job_manager_job_dashboard_page_id' );
			if ( $this->bpjm_is_owned_action() || get_the_ID() === $get_dashboard_page_id ) {
				$this->get_jobs_max_num_pages();

				/*
				 * WP Job Manager's [job_dashboard] "Add Job" button links to the standalone
				 * submit_job_form page (job_manager_submit_job_form_page_id). Inside a BuddyPress
				 * profile that either renders empty (when the WPJM page is unconfigured) or navigates
				 * the member out of their profile. Point it at the in-profile "Post Job" tab instead,
				 * for the member viewing their own dashboard.
				 */
				$bpjm_post_job_url = '';
				if ( bp_loggedin_user_id() === bp_displayed_user_id() ) {
					$bpjm_post_job_url = bp_core_get_userlink( bp_displayed_user_id(), false, true ) . 'jobs/post-job/';
				}

				wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-job-manager-public.js', array( 'jquery' ), $this->version, false );
				wp_localize_script(
					$this->plugin_name,
					'bpjm_load_jobs_object',
					array(
						'ajaxurl'           => admin_url( 'admin-ajax.php' ),
						'max_num_pages'     => $this->max_num_pages,
						'ajax_nonce'        => wp_create_nonce( 'bpjm_load_jobs_nonce' ),
						'displayed_user_id' => bp_displayed_user_id(),
						'loading_text'      => __( 'Loading...', 'bp-job-manager' ),
						'post_job_url'      => $bpjm_post_job_url,
					)
				);
				wp_enqueue_script( $this->plugin_name );
			}
		}

		/**
		 * Tell WP Job Manager core that our profile screens are WPJM screens.
		 *
		 * Filters `has_wpjm_shortcode`, which drives WPJM's own
		 * `job_manager_enqueue_frontend_style` gate. Returning the incoming
		 * value untouched off our screens keeps other filters intact.
		 *
		 * @since 1.0.0
		 * @param bool $has_shortcode Whether WPJM already detected a shortcode.
		 * @return bool True on a screen this plugin owns, otherwise unchanged.
		 */
		/**
		 * Hide WP Job Manager's account block on a member's own profile.
		 *
		 * WP Job Manager prints "You are currently signed in as {username}" with a
		 * Sign out button above its submit form. On a standalone submit page that
		 * is useful. Inside the member's OWN BuddyPress profile it is three
		 * problems at once: they plainly are signed in, it prints the raw
		 * user_login rather than their display name, and it puts a destructive
		 * control in the middle of a form they are filling in - one mis-click and
		 * the draft is gone.
		 *
		 * Scoped as narrowly as possible. A logged-out visitor still gets the
		 * sign-in box: suppressing it for everyone is a bug this plugin already
		 * shipped once, where a visitor who could not post as a guest got the form
		 * hidden AND the sign-in box hidden, leaving no way to continue.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $show_signin Whether WP Job Manager should render the block.
		 * @return bool
		 */
		public function bpjm_hide_signin_on_own_profile( $show_signin = true ) {
			if ( ! is_user_logged_in() || ! bp_is_user() ) {
				return $show_signin;
			}

			return ( bp_loggedin_user_id() === bp_displayed_user_id() ) ? false : $show_signin;
		}

		public function bpjm_has_wpjm_shortcode( $has_shortcode = false ) {
			/*
			 * The standalone "Apply To Job" page must count too. It renders
			 * [job_apply] through our own template, but WP Job Manager decides
			 * whether to enqueue its frontend stylesheet from is_wpjm(), which
			 * this filter feeds. Without it the page loaded job-listings.css but
			 * NOT WP Job Manager's frontend.css - the stylesheet that spaces
			 * .job-manager-form fieldsets, labels and fields - so the apply form
			 * rendered with no vertical rhythm at all. Applications' own spacing
			 * could not fill the gap either: every rule it ships is scoped under
			 * `.single_job_listing`, which this page has no ancestor for.
			 */
			if ( $this->bpjm_is_owned_action() || $this->bpjm_is_apply_page() ) {
				return true;
			}

			return (bool) $has_shortcode;
		}

		/**
		 * Whether the current request is the standalone Apply To Job page.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		public function bpjm_is_apply_page() {
			global $post, $bpjm_bp_job_manager;

			if ( ! is_a( $post, 'WP_Post' ) || ! isset( $bpjm_bp_job_manager->job_application_pgid ) ) {
				return false;
			}

			$bpjm_apply_page_id = (int) $bpjm_bp_job_manager->job_application_pgid;

			return ( $bpjm_apply_page_id && (int) $post->ID === $bpjm_apply_page_id );
		}

		/**
		 *  Load jobs on scroll.
		 */
		public function bpjm_load_more_jobs() {
				check_ajax_referer( 'bpjm_load_jobs_nonce', 'ajax_nonce' );
				$paged = ! empty( $_POST['page_no'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['page_no'] ) ) ) : 1;

				/*
				 * Resolve the profile owner whose jobs are being listed. BuddyPress cannot
				 * parse a member URL out of an admin-ajax.php request, so bp_displayed_user_id()
				 * is 0 here; the profile page passes its displayed user ID in the payload.
				 * Never fall through to an unfiltered query - WP_Query drops the author
				 * clause entirely when 'author' is 0, which would leak every user's jobs.
				 */
				$author = ! empty( $_POST['user_id'] ) ? absint( wp_unslash( $_POST['user_id'] ) ) : bp_displayed_user_id();
			if ( empty( $author ) ) {
				wp_send_json_error( array( 'message' => __( 'Invalid member profile.', 'bp-job-manager' ) ), 400 );
			}

				// Only the profile owner may see their non-public (draft/pending/expired/preview) listings.
				$is_profile_owner = ( get_current_user_id() === $author );
				$post_status      = $is_profile_owner ? array( 'publish', 'expired', 'pending', 'draft', 'preview' ) : array( 'publish' );

				$per_page              = get_option( 'job_manager_per_page' );
				$job_dashboard_columns = array(
					'job_title' => __( 'Title', 'bp-job-manager' ),
					'filled'    => __( 'Filled?', 'bp-job-manager' ),
					'date'      => __( 'Date Posted', 'bp-job-manager' ),
					'expires'   => __( 'Listing Expires', 'bp-job-manager' ),
					'actions'   => __( 'Actions', 'bp-job-manager' ),
				);

				$args = array(
					'post_type'           => 'job_listing',
					'post_status'         => $post_status,
					'ignore_sticky_posts' => true,
					'posts_per_page'      => $per_page,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => $author,
					'paged'               => $paged,
				);
				$jobs = get_posts( $args );

				foreach ( $jobs as $job ) : ?>
					<tr>
						<?php foreach ( $job_dashboard_columns as $key => $column ) : ?>
							<td class="<?php echo esc_attr( $key ); ?>">
								<?php if ( 'job_title' === $key ) : ?>
									<?php if ( 'publish' === $job->post_status ) : ?>
										<a href="<?php echo esc_url( get_permalink( $job->ID ) ); ?>"><?php wpjm_the_job_title( $job ); ?></a>
									<?php else : ?>
										<?php wpjm_the_job_title( $job ); ?> <small>(<?php the_job_status( $job ); ?>)</small>
									<?php endif; ?>
									<?php echo is_position_featured( $job ) ? '<span class="featured-job-icon" title="' . esc_attr__( 'Featured Job', 'bp-job-manager' ) . '"></span>' : ''; ?>
									<?php if ( $is_profile_owner ) { ?>
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
									<?php
									/*
									 * WPJM 2.3+ hooks the actions column with 2 required args
									 * ($job, $actions) - see WPJM templates/job-dashboard.php.
									 * Pass an empty actions array to satisfy that contract; this
									 * handler renders its own owner actions in the title column.
									 */
									do_action( 'job_manager_job_dashboard_column_' . $key, $job, array() );
									?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			<?php
				wp_reset_postdata();
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
			if ( ! bp_is_user() ) {
				return;
			}
			global $bpjm_bp_job_manager;

			$displayed_uid  = bp_displayed_user_id();
			$displayed_user = get_userdata( $displayed_uid );
			$curr_user      = wp_get_current_user();

			// get_userdata() returns false for an unknown ID; wp_get_current_user()
			// always returns a WP_User (ID 0 when logged out), so only the displayed
			// user needs validating here.
			if ( ! ( $displayed_user instanceof WP_User ) ) {
				return;
			}

			$parent_slug     = 'jobs';
			$jobs_tab_link   = bp_core_get_userlink( $displayed_uid, false, true ) . $parent_slug . '/';
			$is_current_user = ( get_current_user_id() === $displayed_uid );
			$job_count       = function_exists( 'bpjm_member_profile_jobs_count' ) ? bpjm_member_profile_jobs_count( $displayed_uid ) : 0;

			/* translators: %s: User's display name */
			$my_job_tab = $this->bpjm_get_member_screen_label( 'jobs', $displayed_uid );
			/* translators: %s: Job count */
			$job_tab = sprintf( __( 'Jobs %s', 'bp-job-manager' ), '<span class="count">' . bp_core_number_format( $job_count ) . '</span>' );

			/*
			 * A tab exists because the member HAS something, or because it is
			 * their own profile and they can start.
			 *
			 * Until 3.0.0 this asked whether either party's role appeared in a
			 * configured `post_job_user_roles` list. That is why a member whose
			 * role was not listed got an employer dashboard they could never use,
			 * and why a site owner had to configure two role arrays before the
			 * plugin was useful. We do not define roles and we do not gate on
			 * them - we surface what someone has done.
			 *
			 * Own profile: show the tab, because they can post (WP Job Manager
			 * decides that, and its answer is yes for any logged-in member).
			 * Someone else's profile: show it only if they actually have jobs -
			 * an empty employer dashboard on a stranger's profile tells a visitor
			 * nothing.
			 */
			if ( $is_current_user || $job_count > 0 ) {
				bp_core_new_nav_item(
					array(
						'name'                    => $job_tab,
						'slug'                    => $parent_slug,
						'screen_function'         => array( $this, 'bpjm_jobs_tab_function_to_show_screen' ),
						'position'                => 75,
						'default_subnav_slug'     => 'my-jobs',
						'show_for_displayed_user' => true,
					)
				);

				bp_core_new_subnav_item(
					array(

						/*
						 * Mirrors the Resumes tab exactly: the parent nav carries the
						 * count badge ("Jobs 6"), the subnav carries the ownership
						 * label ("My Jobs" / "Jobs by Rita"). $my_job_tab was built
						 * for this slot years ago and then never passed to it, so the
						 * variable sat dead, this subnav read a flat "Jobs", and the
						 * "%s's Jobs" string it produced could never render - while
						 * the Resumes subnav did show its owner label. Same shape on
						 * both tabs now.
						 */
						'name'            => apply_filters( 'bpjm_jobs_tab_name', $my_job_tab ),
						'slug'            => 'my-jobs',
						'parent_url'      => $jobs_tab_link . 'my-jobs',
						'parent_slug'     => $parent_slug,
						'screen_function' => array( $this, 'bpjm_my_jobs_show_screen' ),
						'position'        => 100,
						'link'            => $jobs_tab_link . 'my-jobs',
					)
				);

				// Add other submenus for current user.
				if ( $is_current_user ) {
					if ( bpjm_is_plugin_active( 'wp-job-manager-bookmarks/wp-job-manager-bookmarks.php' ) ) {
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

					if ( bpjm_is_plugin_active( 'wp-job-manager-alerts/wp-job-manager-alerts.php' ) ) {
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

			/*
			 * There is deliberately no `else`.
			 *
			 * A bare "jobs" fallback tab used to be registered here for members
			 * whose role was not in the configured posting list: an employer's
			 * empty dashboard - Title / Filled? / Date posted / Listing expires /
			 * Actions and "No jobs found." - with no subnav, shown to the majority
			 * role on a job site. It existed only to serve the role gating, and it
			 * took the post-job subnav with `user_has_access => false` along with
			 * it to stop direct URLs 404ing.
			 *
			 * With the gating gone the whole branch has nothing to do: a member on
			 * their own profile gets the real Jobs tab and can post, and a visitor
			 * looking at someone with no jobs simply sees no Jobs tab rather than
			 * an empty one.
			 */
		}

		/**
		 * Screen function for post job menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_post_job_show_screen() {
			/*
			 * Defense in depth: a profile owner without posting permission must
			 * never reach [submit_job_form] here. The post-job subnav is already
			 * registered `user_has_access => false` in the no-post-role branch
			 * (which hides the tab and hands the URL to BP's no-access handling),
			 * but BP grants owners access to their own tabs in some versions, so
			 * we also gate here: redirect the non-poster to their jobs listing
			 * with the standard "not authorized" message. Prevents both the Add
			 * Job button AND a direct jobs/post-job/ hit from exposing the form.
			 */
			if ( bp_loggedin_user_id() === bp_displayed_user_id() && ! $this->bpjm_restrict_user_post_job( true ) ) {
				bp_core_add_message( $this->bpjm_restrict_user_post_job_message(), 'error' );
				bp_core_redirect( trailingslashit( bp_displayed_user_domain() . 'jobs' ) );
			}
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
			echo esc_html( $this->bpjm_get_member_screen_label( 'jobs', bp_displayed_user_id() ) );
		}



		/**
		 * My Jobs - Content.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_jobs_tab_function_to_show_content() {
			/*
			 * The WP Job Manager [job_dashboard] (and its "Add Job" button) is a
			 * posting surface, so it is only shown to the profile owner when that
			 * owner's role is allowed to post jobs (post_job_user_roles). Owners
			 * without posting permission - and every other viewer - get the
			 * read-only listing below instead of the Add Job dashboard.
			 */
			$is_owner   = ( bp_loggedin_user_id() === bp_displayed_user_id() );
			$can_manage = $is_owner && $this->bpjm_restrict_user_post_job( true );

			if ( $can_manage ) {
				echo do_shortcode( '[job_dashboard]' );
				?>
				<div class="bpjm-loader" style="display:none;">
					<div class="bpjm-loader-img">
						<img src="<?php echo esc_url( BPJM_PLUGIN_URL . '/public/images/spinner.gif' ); ?>" />
					</div>
				</div>
				<?php
			} else {
				$job_args = array(
					'post_type'           => 'job_listing',
					'post_status'         => 'publish',
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

		/**
		 * Hide job actions from display member profile.
		 *
		 * @param  array        $job_actions Job actions keyed by action slug.
		 * @param  WP_Post|null $job         Job listing post.
		 * @return array Filtered job actions.
		 */
		public function bpjm_hide_job_actions( $job_actions, $job ) {
			if ( ( bp_loggedin_user_id() !== bp_displayed_user_id() ) && ! bp_is_user_profile() ) {
				if ( ! empty( $job ) && 'publish' === $job->post_status ) {
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
				if ( class_exists( 'WP_Job_Manager_Alerts_Shortcodes' ) ) {
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
		 * @param    array $job_dashboard_job_listing_args job listing arguments.
		 * @return   array $job_dashboard_job_listing_args return job listing arguments.
		 */
		public function bpjm_job_dashboard_user_id( $job_dashboard_job_listing_args ) {
			$displayed_uid  = bp_displayed_user_id();
			$displayed_user = get_userdata( $displayed_uid );
			$curr_user      = wp_get_current_user();
			$per_page       = get_option( 'job_manager_per_page' );
			if ( ! empty( $curr_user->roles ) && ! empty( $displayed_user->roles ) ) {
				$job_dashboard_job_listing_args['posts_per_page'] = $per_page;
				$job_dashboard_job_listing_args['offset']         = ( max( 1, get_query_var( 'paged' ) ) - 1 ) * $per_page;
			}
			return $job_dashboard_job_listing_args;
		}

		/**
		 * Action performed to override whether the user is allowed to edit the pending jobs.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    bool $allowed members pending job post.
		 * @return   bool Always true — profile owners may edit their pending jobs.
		 */
		public function bpjm_allow_user_to_edit_pending_jobs( $allowed ) {
			$allowed = true;
			return $allowed;
		}

		/**
		 * Action performed to override whether the user is allowed to publish the pending jobs.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 * @param    bool $allowed members pending job post.
		 * @return   bool Always true — profile owners may edit published submissions.
		 */
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
		 * @param    array   $actions contain job action.
		 * @param    WP_Post $job contain job data.
		 * @return   array   $actions contain job action.
		 */
		public function bpjm_job_dashboard_job_actions( $actions, $job ) {
			if ( bp_is_user_profile() ) {
				if ( bp_displayed_user_id() !== get_current_user_id() ) {
					$actions = array();
				}
			}
			return $actions;
		}
		/**
		 * Action performed to add a column in job dashboard table.
		 *
		 * @param array $job_dashboard_cols contain dashboard column data.
		 * @return array
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_job_dashboard_cols( $job_dashboard_cols ) {
			if ( 'my-jobs' === bp_current_action() ) {
				if ( ! bp_is_user_profile() && ( bp_loggedin_user_id() !== bp_displayed_user_id() ) ) {
					$column             = array(
						'actions' => __( 'Actions', 'bp-job-manager' ),
					);
					$job_dashboard_cols = array_merge( $job_dashboard_cols, $column );
				}
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
		 * @param    WP_Post $job contain job data.
		 */
		public function bpjm_job_dashboard_actions_col_content( $job ) {
			global $bpjm_bp_job_manager;
			if ( is_user_logged_in() ) {
				if ( bp_is_user() && ( bp_loggedin_user_id() !== bp_displayed_user_id() ) ) {
					$user_id               = bp_loggedin_user_id();
					$job_application_page  = get_permalink( $bpjm_bp_job_manager->job_application_pgid );
					$job_application_page .= '?args=' . $job->ID;

					if ( bpjm_user_can_apply_job( $user_id ) ) {
						if ( ! is_position_filled( $job ) ) {
							// The enclosing branch already established is_user_logged_in().
							if ( class_exists( 'WP_Job_Manager_Applications' ) ) {

								if ( user_has_applied_for_job( $user_id, $job->ID ) ) {
									get_job_manager_template( 'applied-notice.php', array(), 'wp-job-manager-applications', JOB_MANAGER_APPLICATIONS_PLUGIN_DIR . '/templates/' );
								} else {
									?>
									<div class="generic-button bpjm-job-application-btn-action" id="bpjm-job-application-btn">
										<a href="javascript:void(0);" data-url="<?php echo esc_attr( $job_application_page ); ?>"><?php esc_html_e( 'Submit Application', 'bp-job-manager' ); ?></a>
									</div>
									<?php
								}
							}
						} else {
							echo '<li class="position-filled">' . esc_html__( 'This job position is no longer available.', 'bp-job-manager' ) . '</li>';
						}
					} else {
						echo '<div class="position-filled">' . esc_html__( 'You do not meet the requirements to apply for this job.', 'bp-job-manager' ) . '</div>';
					}
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
			global $bpjm_bp_job_manager, $post;
			if ( ! empty( $post ) && (int) $bpjm_bp_job_manager->job_application_pgid === $post->ID ) {
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
			global $bpjm_bp_job_manager;
			$user_id           = bp_displayed_user_id();
			$is_logged_in_user = ( bp_loggedin_user_id() === $user_id );
			$my_resumes_count  = bpjm_member_profile_resumes_count( $user_id );

			/*
			 * Same rule as the Jobs tab: it exists because the member HAS
			 * something, or because it is their own profile and they can start.
			 * A "Resumes 0" tab on a stranger's profile tells a visitor nothing,
			 * and the two tabs disagreeing about when they appear is exactly the
			 * inconsistency 3.0.0 is removing.
			 */
			if ( ! $is_logged_in_user && $my_resumes_count < 1 ) {
				return;
			}
			$resume_tab_name = apply_filters(
				'bpjm_resume_tab_name',
				$this->bpjm_get_member_screen_label( 'resumes', $user_id )
			);
			$resume_tab      = sprintf( /* translators: %s: bp_core_number_format */ __( 'Resumes %s', 'bp-job-manager' ), '<span class="count">' . bp_core_number_format( $my_resumes_count ) . '</span>' );

			$resumes_tab_link = apply_filters( 'bpjm_resumes_tab_link', bp_core_get_userlink( $user_id, false, true ) . 'resumes/' );

			bp_core_new_nav_item(
				array(
					'name'                    => $resume_tab,
					'slug'                    => 'resumes',
					'screen_function'         => array( $this, 'bpjm_resumes_tab_function_to_show_screen' ),
					'position'                => 75,
					'default_subnav_slug'     => 'my-resumes',
					'show_for_displayed_user' => true,
				)
			);

			// Subnav items.
			bp_core_new_subnav_item(
				array(
					'name'            => $resume_tab_name,
					'slug'            => 'my-resumes',
					'parent_url'      => $resumes_tab_link . 'my-resumes',
					'parent_slug'     => 'resumes',
					'screen_function' => array( $this, 'bpjm_my_resumes_show_screen' ),
					'position'        => 100,
					'link'            => $resumes_tab_link . 'my-resumes',
				)
			);

			if ( $is_logged_in_user ) {
				// Add Resume subnav.
				bp_core_new_subnav_item(
					array(
						'name'            => apply_filters( 'bpjm_resumes_subnav_item_add_resume', __( 'Add Resume', 'bp-job-manager' ) ),
						'slug'            => 'add-resume',
						'parent_url'      => $resumes_tab_link . 'add-resume',
						'parent_slug'     => 'resumes',
						'screen_function' => array( $this, 'bpjm_add_resume_show_screen' ),
						'position'        => 200,
						'link'            => $resumes_tab_link . 'add-resume',
					)
				);

				// Applied Jobs subnav, if applicable.
				$wpjm_applications_active = bpjm_is_plugin_active( 'wp-job-manager-applications/wp-job-manager-applications.php' );
				if ( $wpjm_applications_active ) {
					bp_core_new_subnav_item(
						array(
							'name'            => apply_filters( 'bpjm_resumes_subnav_item_applied_jobs', __( 'Applied Jobs', 'bp-job-manager' ) ),
							'slug'            => 'applied-jobs',
							'parent_url'      => $resumes_tab_link . 'applied-jobs',
							'parent_slug'     => 'resumes',
							'screen_function' => array( $this, 'bpjm_applied_jobs_show_screen' ),
							'position'        => 200,
							'link'            => $resumes_tab_link . 'applied-jobs',
						)
					);
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
			echo esc_html( $this->bpjm_get_member_screen_label( 'resumes', bp_displayed_user_id() ) );
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
						<?php esc_html_e( 'You need to be signed in to manage your resumes.', 'bp-job-manager' ); ?>
						<a class="button" href="<?php echo esc_url( apply_filters( 'resume_manager_candidate_dashboard_login_url', wp_login_url( get_permalink() ) ) ); ?>"><?php esc_html_e( 'Sign in', 'bp-job-manager' ); ?></a>
					</p>
				</div>
				<?php
				return;
			}

			/*
			 * Resume Manager renders this screen - we do not.
			 *
			 * This used to be our own 193-line template plus a hand-built query and
			 * column list. It drifted from the dashboard it was copied from - it
			 * never gained the status column Resume Manager shows - and it
			 * re-declared labels (Name, Title, Location, Category, Date Posted,
			 * Edit, Delete) that already exist in the Resume Manager catalogue with
			 * community translations from wordpress.org. So a translated site got
			 * Resume Manager's own screens in its language and this one in English.
			 *
			 * [candidate_dashboard] is scoped to the displayed member by our
			 * existing `resume_manager_get_dashboard_resumes_args` filter
			 * (bpjm_resume_dashboard_user_id), so it shows the right person's
			 * resumes inside a BuddyPress profile.
			 */
			echo do_shortcode( '[candidate_dashboard]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode output from Resume Manager, escaped by that plugin's own templates.
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
			/*
			 * WP Job Manager Applications renders this screen - we do not.
			 *
			 * This used to be our own 121-line template running its own query. It
			 * could not show an application's status (the single thing a candidate
			 * opens this screen to find out) even though Applications tracks it,
			 * and it re-declared labels - Applied For, Attachment - that already
			 * exist in the Applications catalogue with community translations from
			 * wordpress.org. So a translated site got Applications' own screens in
			 * its language and this one in English.
			 *
			 * [past_applications] queries every application status and is scoped to
			 * the current user, which is correct here because this screen is
			 * owner-only. Filter `job_manager_job_applications_past_args` to adjust.
			 */
			echo do_shortcode( '[past_applications]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode output from WP Job Manager Applications, escaped by that plugin's own templates.
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
				if ( bp_displayed_user_id() !== get_current_user_id() ) {
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
			global $bpjm_bp_job_manager;

			if ( ! isset( $bpjm_bp_job_manager->bpjm_resume_at_profile ) || 'yes' !== $bpjm_bp_job_manager->bpjm_resume_at_profile ) {
				return;
			}

			/*
			 * Which resume to show is derived, never stored.
			 *
			 * There used to be a `bpjm_profile_resume_show_id` meta pointing at a
			 * chosen resume, with a raw $wpdb fallback query when it was empty -
			 * two code paths answering one question, and a stored pointer that
			 * could outlive the resume it named. The member's most recent
			 * published resume is the answer in both cases, and get_posts() gets
			 * it with the caching and filtering a hand-built query skips.
			 */
			$bpjm_resumes = get_posts(
				array(
					'post_type'        => 'resume',
					'post_status'      => 'publish',
					'author'           => bp_displayed_user_id(),
					'numberposts'      => 1,
					'orderby'          => 'modified',
					'order'            => 'DESC',
					'fields'           => 'ids',
					'suppress_filters' => false,
				)
			);

			if ( empty( $bpjm_resumes ) ) {
				return;
			}

			bpjm_show_resume_at_profile( (int) $bpjm_resumes[0] );
		}

		/**
		 * Add resume fields visibility menu to BuddyPress profile.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		public function bpjm_add_resume_fields_visibility_menu() {

			global $bp;
			if ( bp_is_my_profile() || current_user_can( 'manage_options' ) ) {
				$tab_name = apply_filters( 'bpjm_resume_fields_custom_text', __( 'Resume Fields', 'bp-job-manager' ) );
				$tab_slug = apply_filters( 'bpjm_resume_fields_custom_slug', 'resume-fields' );
				bp_core_new_subnav_item(
					array(
						'name'            => esc_html( $tab_name ),
						'slug'            => $tab_slug,
						'parent_url'      => trailingslashit( bp_displayed_user_domain() . 'profile' ),
						'parent_slug'     => 'profile',
						'screen_function' => array( $this, 'bpjm_resume_fields_template' ),
						'position'        => 40,
					)
				);
			}
		}

		/**
		 * Load the resume fields template for BuddyPress profile.
		 *
		 * @since    1.0.0
		 * @access   public
		 */
		public function bpjm_resume_fields_template() {
			add_action( 'bp_template_content', array( $this, 'bpjm_resume_settings_before_submit' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}
		/**
		 * Function to add settings under profile visibility-buddypress.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_resume_settings_before_submit() {
			global $wpdb;
			$user_id   = bp_loggedin_user_id();
			$resume_id = get_user_meta( $user_id, 'bpjm_profile_resume_show_id', true );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Small prepared lookup of the logged-in member's own published resumes on the BP settings screen; per-user caching would add invalidation complexity for no measurable gain.
			$user_posts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, post_title FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s AND post_status = %s ORDER BY post_modified DESC",
					$user_id,
					'resume',
					'publish'
				),
				OBJECT
			);

			$selected_post = ! empty( $resume_id ) ? $resume_id : ( ! empty( $user_posts ) ? $user_posts[0]->ID : null );

			if ( ! empty( $selected_post ) && ! empty( $user_posts ) ) {
				/*
				 * This form MUST default the same way the renderer does.
				 *
				 * Since 3.0.0 an absent `bpjm_display_fields` means SHOW - the
				 * activation loop that used to seed it is gone, so most members
				 * legitimately have no meta at all. This form defaulted the other
				 * way, to 'no', which produced a screen that contradicted the
				 * profile beside it: the resume was visible while every box here
				 * read unchecked.
				 *
				 * Worse than cosmetic. Saving this screen for any unrelated reason
				 * posted every box unchecked, wrote the meta, and silently removed
				 * the member's resume from their own profile.
				 */
				$fields_display = get_user_meta( bp_loggedin_user_id(), 'bpjm_display_fields', true );

				if ( ! is_array( $fields_display ) ) {
					$fields_display = bpjm_resume_profile_default_fields();
				}

				$display_resume = isset( $fields_display['display_resume'] ) ? $fields_display['display_resume'] : 'yes';
				$display_class  = ( 'yes' === $display_resume ) ? 'display-true' : 'display-false';

				global $bpjm_bp_job_manager;
				// $user_posts is already known non-empty from the enclosing branch.
				if ( isset( $bpjm_bp_job_manager->bpjm_resume_at_profile ) && 'yes' === $bpjm_bp_job_manager->bpjm_resume_at_profile ) {
					if ( class_exists( 'Youzify' ) ) {
						?>
						<form method="post">
					<?php } ?>
					<table class="profile-settings bp-tables-user">
						<thead>
							<tr>
								<th class="title field-group-name"><?php esc_html_e( 'Resume', 'bp-job-manager' ); ?></th>
								<th class="title"><?php esc_html_e( 'Visibility', 'bp-job-manager' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr class="field_name field_type_textbox">
								<td class="field-name"><?php esc_html_e( 'Show resume on the profile page', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input class="bpjm-display-resume-checkbox" type="checkbox" value="yes" name="bpjm_display[display_resume]" 
									<?php
									if ( ! empty( $fields_display['display_resume'] ) ) {
										checked( $fields_display['display_resume'], 'yes' );
									}
									?>
																																				>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Select resume to display at profile', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<select name="bpjm_prof_resume_show_postid">
										<?php
										foreach ( $user_posts as $key => $value ) {
											echo "<option value='" . esc_attr( $value->ID ) . "' " . esc_attr( selected( $selected_post, $value->ID, false ) ) . '>' . esc_attr( get_the_candidate_title( $value->ID ) ) . '</option>';
										}
										?>
									</select>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Show email field', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[email]" 
									<?php
									if ( ! empty( $fields_display['email'] ) ) {
										checked( $fields_display['email'], 'yes' );
									}
									?>
																									>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Display professional title', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[prof_title]" 
									<?php
									if ( ! empty( $fields_display['prof_title'] ) ) {
										checked( $fields_display['prof_title'], 'yes' );
									}
									?>
																										>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Display location', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[location]" 
									<?php
									if ( ! empty( $fields_display['location'] ) ) {
										checked( $fields_display['location'], 'yes' );
									}
									?>
																										>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Display video link', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[video]" 
									<?php
									if ( ! empty( $fields_display['video'] ) ) {
										checked( $fields_display['video'], 'yes' );
									}
									?>
																									>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Display resume contents(description)', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[description]" 
									<?php
									if ( ! empty( $fields_display['description'] ) ) {
										checked( $fields_display['description'], 'yes' );
									}
									?>
																										>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Display URL(s)', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[url]" 
									<?php
									if ( ! empty( $fields_display['url'] ) ) {
										checked( $fields_display['url'], 'yes' );
									}
									?>
																								>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Display education', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[education]" 
									<?php
									if ( ! empty( $fields_display['education'] ) ) {
										checked( $fields_display['education'], 'yes' );
									}
									?>
																										>
								</td>
							</tr>
							<tr class="field_name field_type_textbox resume-fields-row <?php echo esc_attr( $display_class ); ?>">
								<td class="field-name"><?php esc_html_e( 'Display experience', 'bp-job-manager' ); ?></td>
								<td class="field-visibility">
									<input type="checkbox" value="yes" name="bpjm_display[experience]" 
									<?php
									if ( ! empty( $fields_display['experience'] ) ) {
										checked( $fields_display['experience'], 'yes' );
									}
									?>
																										>
								</td>
							</tr>
						</tbody>
					</table>
					<?php
					if ( class_exists( 'Youzify' ) ) {
						?>
							<button name="bpjm-resume-fields-settings-submit" class="youzify-save-options" type="submit">
								<?php esc_html_e( 'Save Changes', 'bp-job-manager' ); ?>
							</button>
							<?php wp_nonce_field( 'bpjm_resume_fields_settings_nonce' ); ?>
						</form>
						<?php
					}
				}
			} elseif ( ( empty( $user_posts ) || empty( $selected_post ) ) && class_exists( 'Youzify' ) ) {

					wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-job-manager-public.css', array(), $this->version, 'all' );
				?>
					<div class="bpjm-flex-container">
						<div><i class="fa fa-exclamation-circle bpjm-fa-icon"></i></div>
						<p><?php echo esc_html__( 'No resume fields available. Please create a resume to enable display options.', 'bp-job-manager' ); ?></p>
					</div>
					<?php

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

			if ( isset( $_POST['xprofile-settings-submit'] ) || isset( $_POST['bpjm-resume-fields-settings-submit'] ) ) {

				// Nonce check.
				if ( class_exists( 'Youzify' ) ) {
					check_admin_referer( 'bpjm_resume_fields_settings_nonce' );
				} else {
					check_admin_referer( 'bp_xprofile_settings' );
				}

				/*
				 * `bpjm_profile_resume_show_id` is no longer written.
				 *
				 * It stored which resume to show on the profile. Since 3.0.0 that
				 * is derived - the member's most recent published resume - so the
				 * meta had no reader left and writing it only created a pointer
				 * that could outlive the resume it named.
				 */

				// Handle display fields update.
				if ( isset( $_POST['bpjm_display'] ) ) {
					$display_fields = array(
						'display_resume',
						'email',
						'prof_title',
						'location',
						'video',
						'description',
						'url',
						'education',
						'experience',
					);

					$saved_display_fields = array();
					foreach ( $display_fields as $field ) {
						$saved_display_fields[ $field ] = isset( $_POST['bpjm_display'][ $field ] ) ? sanitize_text_field( wp_unslash( $_POST['bpjm_display'][ $field ] ) ) : '';
					}

					update_user_meta( bp_loggedin_user_id(), 'bpjm_display_fields', $saved_display_fields );
				}
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
			if ( ! bp_is_user() ) {
				return;
			}

			$action = filter_input( INPUT_GET, 'action' );
			if ( null === $action ) {
				return;
			}

			$job_id_param    = filter_input( INPUT_GET, 'job_id' );
			$resume_id_param = filter_input( INPUT_GET, 'resume_id' );

			$job_actions    = array( 'mark_filled', 'mark_not_filled', 'duplicate', 'delete', 'relist' );
			$resume_actions = array( 'delete', 'hide', 'publish' );

			if ( in_array( $action, $job_actions, true ) && null !== $job_id_param ) {
				$wbjob_shortcode_object = new WP_Job_Manager_Shortcodes();
				$wbjob_shortcode_object->job_dashboard_handler();
			} elseif ( in_array( $action, $resume_actions, true ) && null !== $resume_id_param ) {
				$wb_resume_shortcode_object = new WP_Resume_Manager_Shortcodes();
				$wb_resume_shortcode_object->candidate_dashboard_handler();
			}
		}


		/**
		 * Function for filter redirect url when duplicate any job on buddypress job tab.
		 *
		 * @since    1.0.6
		 * @author   wbcomdesigns
		 * @access   public
		 * @param string $location The path or URL to redirect to.
		 * @param int    $status HTTP response status code to use. Default '302' (Moved Temporarily).
		 */
		public function bpjm_filter_redirect_duplicate_post_url( $location, $status ) {
			// Check if the current page is a user's profile page.
			if ( ! bp_is_user() || ! bp_is_my_profile() ) {
				return $location;
			}

			// Parse the query string from the location URL.
			$query_str = wp_parse_url( $location, PHP_URL_QUERY );

			// Ensure query_str is not null before parsing.
			if ( null !== $query_str ) {
				parse_str( $query_str, $parsed_query );

				// Redirect if 'job_id' is set and not '0'.
				if ( isset( $parsed_query['job_id'] ) && '0' !== $parsed_query['job_id'] ) {
					// bp_members_get_user_url() only exists on BuddyPress 12.0+.
					// Mirror the guard used by bpjm_add_private_message_link()
					// and fall back to bp_core_get_user_domain() on older BP.
					if ( function_exists( 'bp_members_get_user_url' ) && version_compare( BP_VERSION, '12.0.0', '>=' ) ) {
						$user_url = bp_members_get_user_url( bp_displayed_user_id() );
					} else {
						$user_url = bp_core_get_user_domain( bp_displayed_user_id() );
					}

					$location = trailingslashit( $user_url ) . 'jobs/post-job/?job_id=' . $parsed_query['job_id'];
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
			if ( ! bp_is_active( 'messages' ) ) {
				return;
			}
			// Check if the BuddyPress version is 12.0.0 or higher.
			if ( function_exists( 'bp_members_get_user_slug' ) && version_compare( BP_VERSION, '12.0.0', '>=' ) ) {
				// Use bp_members_get_user_slug for BuddyPress v12.0.0 and above.
				$url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_members_get_user_slug( get_the_author_meta( 'ID' ) ) );
			} else {
				// Use bp_core_get_username for older versions.
				$url = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( get_the_author_meta( 'ID' ) ) );
			}

			echo '<a href="' . esc_url( $url ) . '" title="' . esc_attr__( 'Private Message', 'bp-job-manager' ) . '">' . esc_html__( 'Send Private Message', 'bp-job-manager' ) . '</a>';
		}

		/**
		 * Add bp notification on sending job application.
		 *
		 * @since 2.1.0
		 *
		 * @param int $application_id Application ID.
		 * @param int $job_id Job ID.
		 */
		public function bpjm_add_bp_notification_for_job_post( $application_id, $job_id ) {
			global $bpjm_bp_job_manager;

			if ( isset( $bpjm_bp_job_manager->bpjm_app_notify ) && 'yes' === $bpjm_bp_job_manager->bpjm_app_notify ) {
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
						)
					);
				}
			}
		}

		/**
		 * Register component for job application notification.
		 *
		 * @since 2.1.0
		 *
		 * BuddyPress passes whatever earlier filters returned, so the parameter is
		 * typed loose and normalised below.
		 *
		 * @param mixed $component_names WP Job Manager Component.
		 * @return array Component names with this plugin's component appended.
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
		 * @param string $action               Component action. Deprecated. Do not do checks against this! Use.
		 * @param int    $item_id               Notification item ID.
		 * @param int    $secondary_item_id     Notification secondary item ID.
		 * @param int    $total_items     Number of notifications with the same action.
		 * @param string $format                Format of return. Either 'string' or 'object'.
		 * @param string $component_action_name Canonical notification action.
		 * @param string $component_name        Notification component ID.
		 */
		public function bpjm_job_application_notifications( $action, $item_id, $secondary_item_id, $total_items, $format, $component_action_name, $component_name ) {
			if ( empty( $format ) ) {
				$format = 'string';
			}
			if ( 'applied_job' === $component_action_name ) {

				$applicant_info = get_userdata( $secondary_item_id );
				// get_userdata() returns false when the user no longer exists.
				if ( $applicant_info ) {
					$applicant_name = property_exists( $applicant_info->data, 'user_login' ) ? $applicant_info->user_login : '';
				} else {
					// Handle the case where the user data could not be retrieved.
					$applicant_name = ''; // Or some default value or error handling.
				}

				/**
				 * Filters the text used for the notification generated by BuddyPress Registration Options.
				 *
				 * @since 4.3.0
				 *
				 * @param string $value Notification text.
				 */
				/* translators: %1$s: Applicant Name*/
				$text             = apply_filters( '_bprwg_notification_text', sprintf( esc_html__( '%s submitted an application for your job posting.', 'bp-job-manager' ), $applicant_name ) );
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

				// WordPress Toolbar.
				if ( 'string' === $format ) {
					$result = sprintf( '<a class="ab-item" href="%s">%s</a>', $application_link, $text );
				}

				return $result;
			}

			return $action;
		}

		/**
		 * Restrict to post job according to user role
		 *
		 * @param boolean $can_post user can post or not.
		 * @return boolean
		 */
		public function bpjm_restrict_user_post_job( $can_post ) {
			global $bpjm_bp_job_manager;

			return bpjm_user_can_post_job( is_user_logged_in() ? bp_loggedin_user_id() : null );
		}

		/**
		 * Message shown when a user's role is not allowed to post a job.
		 *
		 * RETURNS the string — it does not print. Callers that need it on the
		 * page (the `submit_job_form_disabled` action) must go through
		 * bpjm_print_restrict_user_post_job_message(); callers that need the
		 * value (bp_core_add_message()) use this directly.
		 *
		 * @return string Translated restriction message.
		 */
		public function bpjm_restrict_user_post_job_message() {
			return __( 'You are not authorized to post a job. Please reach out to the administrator for further assistance.', 'bp-job-manager' );
		}

		/**
		 * Print the "not authorized to post a job" message.
		 *
		 * Hooked to WP Job Manager's `submit_job_form_disabled` action, which
		 * expects the callback to echo its own output.
		 *
		 * @return void
		 */
		public function bpjm_print_restrict_user_post_job_message() {
			echo esc_html( $this->bpjm_restrict_user_post_job_message() );
		}


		/**
		 * Nav item links.
		 *
		 * @param string $link     The URL for the nav item.
		 * @param object $nav_item The nav item object.
		 * @return string The URL for the nav item.
		 */
		public function bpjm_nouveau_link_fix( $link, $nav_item ) {
			$bp_nouveau = bp_nouveau();
			$nav_item   = $bp_nouveau->current_nav_item;

			$link = '#';
			if ( ! empty( $nav_item->link ) ) {
				$link = $nav_item->link;
			}

			if ( 'personal' === $bp_nouveau->displayed_nav && ! empty( $nav_item->primary ) ) {
				if ( bp_loggedin_user_domain() ) {
					$link = str_replace( bp_loggedin_user_domain(), bp_displayed_user_domain(), $link );
				} else {
					$link = trailingslashit( bp_displayed_user_domain() . $link );
				}
			}
			return $link;
		}

		/**
		 * Legacy Link for BP Rewrites.
		 *
		 * @return void
		 */
		public function bpjm_legacy_link_fix() {
			$user_nav_items = buddypress()->members->nav->get_primary();
			if ( $user_nav_items ) {
				foreach ( $user_nav_items as $user_nav_item ) {
					if ( ! isset( $user_nav_item->css_id ) || ! $user_nav_item->css_id ) {
						continue;
					}

					// remove_filter() takes ( $hook, $callback, $priority ) only —
					// the accepted-args count is not part of its signature.
					remove_filter( 'bp_get_displayed_user_nav_' . $user_nav_item->css_id, 'BP\Rewrites\bp_get_displayed_user_nav', 1 );
				}
			}
		}

		/**
		 * Determine if the shortcode action handler should run.
		 *
		 * @since 1.35.0
		 *
		 * @param bool $is_page Should the handler run.
		 */
		public function bpjm_allow_continue_submission( $is_page ) {

			if ( bp_is_my_profile() && 'post-job' === bp_current_action() ) {
				return true;
			}
			return true;
		}

		/**
		 * Flush job listings transient when a job or resume post status changes.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @param string  $new_status New post status.
		 * @param string  $old_status Old post status.
		 * @param WP_Post $post       Post object.
		 */
		public function flush_job_listings_transient_on_update( $new_status, $old_status, $post ) {
			if ( in_array( $post->post_type, array( 'job_listing', 'resume' ), true ) ) {
				delete_transient( "user_{$post->post_author}_job_listings" );
				delete_transient( "user_{$post->post_author}_my_resumes_count" );
			}
		}

		/**
		 * Flush the cached profile-nav counts when a job or resume is force-deleted
		 * (bypasses trash, so transition_post_status never fires).
		 *
		 * @since    2.8.5
		 * @access   public
		 * @param int          $post_id Deleted post ID.
		 * @param WP_Post|null $post    Deleted post object, null if a caller fired
		 *                              the action without it.
		 */
		public function flush_job_listings_transient_on_delete( $post_id, $post ) {
			if ( $post instanceof WP_Post ) {
				$this->flush_job_listings_transient_on_update( '', '', $post );
			}
		}
	}
endif;
