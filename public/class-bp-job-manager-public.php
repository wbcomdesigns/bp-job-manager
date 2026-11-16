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
		 * Whether the list being built is past its last page (set by the dashboard arg filters).
		 *
		 * @since 3.0.1
		 * @var bool
		 */
		private $bpjm_past_last_page = false;

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
			// Reign overrides WP Job Manager's job card template but only loads its CSS on pages it
			// recognises as job pages; without this our profile tabs get the markup without the styles.
			add_filter( 'reign_load_wp_job_manager_assets', array( $this, 'bpjm_has_wpjm_shortcode' ) );
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
		 * Open the wrapper every Jobs / Resumes screen renders inside.
		 *
		 * The screens are WP Job Manager's and its add-ons' own markup, each with
		 * its own look. One wrapper lets the stylesheet give all of them the same
		 * buttons, tables and notices without re-rendering any of it.
		 *
		 * @since 3.0.1
		 */
		public function bpjm_screen_open() {
			if ( $this->bpjm_is_screen() ) {
				echo '<div class="bpjm-screen bpjm-screen--' . esc_attr( bp_current_action() ) . '">';
			}
		}

		/**
		 * Close the wrapper opened by bpjm_screen_open().
		 *
		 * @since 3.0.1
		 */
		public function bpjm_screen_close() {
			if ( $this->bpjm_is_screen() ) {
				echo '</div>';
			}
		}

		/**
		 * Whether the current page is one of our Jobs / Resumes screens.
		 *
		 * @since 3.0.1
		 * @return bool
		 */
		private function bpjm_is_screen() {
			return in_array( bp_current_component(), array( 'jobs', 'resumes' ), true ) && $this->bpjm_is_owned_action();
		}

		/**
		 * Register one Jobs / Resumes subnav item.
		 *
		 * @since 3.0.1
		 * @param string $parent_slug     'jobs' or 'resumes'.
		 * @param string $tab_link        Parent tab URL with trailing slash.
		 * @param string $slug            Subnav slug.
		 * @param string $name            Subnav label.
		 * @param string $screen_function Method of this class that loads the screen.
		 * @param int    $position        Subnav position.
		 */
		private function bpjm_add_subnav( $parent_slug, $tab_link, $slug, $name, $screen_function, $position ) {
			bp_core_new_subnav_item(
				array(
					'name'            => $name,
					'slug'            => $slug,
					'parent_url'      => $tab_link . $slug,
					'parent_slug'     => $parent_slug,
					'screen_function' => array( $this, $screen_function ),
					'position'        => $position,
					'link'            => $tab_link . $slug,
				)
			);
		}

		/**
		 * Load a profile screen whose title and content come from
		 * bpjm_{$screen}_tab_function_to_show_title() / _content().
		 *
		 * @since 3.0.1
		 * @param string $screen Screen key, e.g. 'my_jobs'.
		 */
		private function bpjm_load_screen( $screen ) {
			add_action( 'bp_template_title', array( $this, "bpjm_{$screen}_tab_function_to_show_title" ) );
			add_action( 'bp_template_content', array( $this, "bpjm_{$screen}_tab_function_to_show_content" ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * The owner's WP Job Manager / Resume Manager dashboard on My Jobs / My Resumes.
		 *
		 * @since 3.0.1
		 * @param string $tab       'jobs' or 'resumes'.
		 * @param string $shortcode Dashboard shortcode.
		 */
		private function bpjm_render_owner_dashboard( $tab, $shortcode ) {
			if ( $this->bpjm_owner_sub_screen( 'jobs' === $tab ? 'job_id' : 'resume_id' )['action'] ) {
				$this->bpjm_back_link( $tab );
			}
			// Rendered first: the dashboard args filter decides whether this page is past the end.
			$dashboard = do_shortcode( $shortcode );
			if ( $this->bpjm_past_last_page ) {
				$this->bpjm_first_page_notice();
			}
			echo $dashboard; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode output from WP Job Manager / Resume Manager, escaped by their own templates.
		}

		/**
		 * URL of a member's Jobs or Resumes tab, or one of its subnav screens.
		 *
		 * WP Job Manager's and Resume Manager's own dashboard and submission pages
		 * are usually not configured on a BuddyPress site - the profile tabs are
		 * those pages - so every link that means "your jobs", "post a job" or
		 * "add a resume" is built here.
		 *
		 * @since 3.0.1
		 * @param int    $user_id Member ID.
		 * @param string $tab     'jobs' or 'resumes'.
		 * @param string $screen  Optional subnav slug, e.g. 'post-job'.
		 * @return string
		 */
		public function bpjm_screen_url( $user_id, $tab, $screen = '' ) {
			return bp_core_get_userlink( $user_id, false, true ) . $tab . '/' . ( $screen ? $screen . '/' : '' );
		}

		/**
		 * Current page of a paginated list on a Jobs / Resumes screen.
		 *
		 * WP Job Manager paginates with /page/N/, which BuddyPress does not route
		 * under a member URL (it 404s), so our screens page with ?bpjm-page=N.
		 *
		 * @since 3.0.1
		 * @return int
		 */
		public function bpjm_current_page() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only page number.
			return isset( $_GET['bpjm-page'] ) ? max( 1, absint( $_GET['bpjm-page'] ) ) : 1;
		}

		/**
		 * Record whether dashboard query args land past the last page.
		 *
		 * Only runs on page 2+, as a one-row ID query with the same args.
		 *
		 * @since 3.0.1
		 * @param array $args WP_Query arguments with offset already set.
		 * @return void
		 */
		private function bpjm_check_past_last_page( $args ) {
			if ( $this->bpjm_current_page() < 2 ) {
				return;
			}
			$probe = new WP_Query(
				array_merge(
					$args,
					array(
						'fields'         => 'ids',
						'posts_per_page' => 1,
						'no_found_rows'  => true,
					)
				)
			);
			$this->bpjm_past_last_page = empty( $probe->posts );
		}

		/**
		 * On a page past the end of a list, link back to the first page.
		 *
		 * @since 3.0.1
		 * @return bool True when the notice was printed.
		 */
		private function bpjm_first_page_notice() {
			if ( $this->bpjm_current_page() < 2 ) {
				return false;
			}
			printf(
				'<p class="bpjm-back"><a href="%1$s">&larr; %2$s</a></p>',
				esc_url( remove_query_arg( 'bpjm-page' ) ),
				esc_html__( 'This page is empty. Back to the first page', 'bp-job-manager' )
			);
			return true;
		}

		/**
		 * Point WP Job Manager's pager at ?bpjm-page=N on our screens.
		 *
		 * @since 3.0.1
		 * @param array $args paginate_links() arguments.
		 * @return array
		 */
		public function bpjm_pagination_args( $args ) {
			if ( bp_is_user() && $this->bpjm_is_owned_action() ) {
				$args['base']    = add_query_arg( 'bpjm-page', '%#%' );
				$args['format']  = '';
				$args['current'] = $this->bpjm_current_page();
			}
			return $args;
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

			/*
			 * Not a standalone [job_dashboard] page: that is WP Job Manager's own
			 * screen, styled by WP Job Manager and the theme, and every rule here is
			 * scoped to .bpjm-screen or the apply page.
			 */
			return false;
		}

		/**
		 * Register the JavaScript for the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			// Same gate as the stylesheet: only the plugin's own profile screens.
			if ( ! $this->bpjm_is_owned_action() ) {
				return;
			}

			/*
			 * WP Job Manager's [job_dashboard] "Add Job" button links to its standalone
			 * submission page, which is empty when unconfigured and otherwise leaves the
			 * profile. Point it at the in-profile Post Job tab, on the member's own profile;
			 * same for Resume Manager's Add Resume link.
			 */
			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-job-manager-public.js', array( 'jquery' ), $this->version, true );
			wp_localize_script(
				$this->plugin_name,
				'bpjm_public',
				array(
					'post_job_url'   => bp_is_my_profile() ? $this->bpjm_screen_url( bp_loggedin_user_id(), 'jobs', 'post-job' ) : '',
					'add_resume_url' => bp_is_my_profile() ? $this->bpjm_screen_url( bp_loggedin_user_id(), 'resumes', 'add-resume' ) : '',
				)
			);
			wp_enqueue_script( $this->plugin_name );
		}

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
		 * LEGACY. Before 3.0.0 the plugin created an "Apply To Job" page at
		 * activation and stored its id in `bpjm_job_application_pgid`. It no
		 * longer creates one, and nothing in the plugin links to it - members
		 * apply on the job listing, where WP Job Manager and Applications put
		 * the form. The page is still rendered for sites that already have it,
		 * because the page it created has no post content of its own: dropping
		 * support would turn a live URL into a blank page. New installs never
		 * have the option, so this returns false and the whole path is inert.
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

			$my_job_tab = $this->bpjm_get_member_screen_label( 'jobs', $displayed_uid );
			/* translators: %s: Job count */
			$job_tab = trim( sprintf( __( 'Jobs %s', 'bp-job-manager' ), $job_count ? '<span class="count">' . bp_core_number_format( $job_count ) . '</span>' : '' ) ); // No "0" badge on an empty tab.

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
						'screen_function'         => array( $this, 'bpjm_my_jobs_show_screen' ),
						'position'                => 75,
						'default_subnav_slug'     => 'my-jobs',
						'show_for_displayed_user' => true,
					)
				);

				// Same shape as the Resumes tab: count badge on the parent, owner label here.
				$this->bpjm_add_subnav( $parent_slug, $jobs_tab_link, 'my-jobs', apply_filters( 'bpjm_jobs_tab_name', $my_job_tab ), 'bpjm_my_jobs_show_screen', 100 );

				// Add other submenus for current user.
				if ( $is_current_user ) {
					if ( bpjm_is_plugin_active( 'wp-job-manager-bookmarks/wp-job-manager-bookmarks.php' ) ) {
						$this->bpjm_add_subnav( $parent_slug, $jobs_tab_link, 'my-bookmarks', __( 'My Bookmarks', 'bp-job-manager' ), 'bpjm_bookmarked_jobs_show_screen', 200 );
					}

					if ( bpjm_is_plugin_active( 'wp-job-manager-alerts/wp-job-manager-alerts.php' ) ) {
						$this->bpjm_add_subnav( $parent_slug, $jobs_tab_link, 'job-alerts', __( 'Job Alerts', 'bp-job-manager' ), 'bpjm_job_alerts_show_screen', 200 );
					}

					/*
					 * Applied Jobs lives under Jobs: it is about jobs, a job seeker
					 * looks for it there, and it needs only Applications - under
					 * Resumes it vanished on sites without Resume Manager. The old
					 * /resumes/applied-jobs/ URL redirects (bpjm_redirect_old_applied_jobs()).
					 */
					if ( bpjm_is_plugin_active( 'wp-job-manager-applications/wp-job-manager-applications.php' ) ) {
						$this->bpjm_add_subnav( $parent_slug, $jobs_tab_link, 'applied-jobs', apply_filters( 'bpjm_resumes_subnav_item_applied_jobs', __( 'Applied Jobs', 'bp-job-manager' ) ), 'bpjm_applied_jobs_show_screen', 150 );
					}

					$this->bpjm_add_subnav( $parent_slug, $jobs_tab_link, 'post-job', __( 'Post Job', 'bp-job-manager' ), 'bpjm_post_job_show_screen', 200 );
				}
			}

			// No else: a visitor on a profile with no jobs gets no Jobs tab, not an empty one.
		}

		/**
		 * Screen function for post job menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_post_job_show_screen() {
			// An owner WP Job Manager will not let post never reaches [submit_job_form], even by direct URL.
			if ( bp_loggedin_user_id() === bp_displayed_user_id() && ! $this->bpjm_restrict_user_post_job( true ) ) {
				bp_core_add_message( $this->bpjm_restrict_user_post_job_message(), 'error' );
				bp_core_redirect( trailingslashit( bp_displayed_user_domain() . 'jobs' ) );
			}
			$this->bpjm_load_screen( 'post_job' );
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
			$this->bpjm_mark_application_notifications_read();
			$this->bpjm_load_screen( 'my_jobs' );
		}

		/**
		 * Tell the applicant when the employer changes their application's status.
		 *
		 * Applications stores the status as the post status (new, interviewed,
		 * offer, hired, rejected, archived). Creation goes new -> new and is
		 * skipped; archiving is the employer's housekeeping, not news for the
		 * candidate. Gated by the same "application notifications" setting as the
		 * employer's notice.
		 *
		 * @since 3.0.1
		 * @param string  $new_status New post status.
		 * @param string  $old_status Old post status.
		 * @param WP_Post $post       Application post.
		 */
		public function bpjm_notify_application_status( $new_status, $old_status, $post ) {
			global $bpjm_bp_job_manager;

			if ( 'job_application' !== $post->post_type || $new_status === $old_status || in_array( $new_status, array( 'archived', 'trash', 'auto-draft' ), true )
				|| ! bp_is_active( 'notifications' ) || ! isset( $bpjm_bp_job_manager->bpjm_app_notify ) || 'yes' !== $bpjm_bp_job_manager->bpjm_app_notify ) {
				return;
			}

			$candidate_id = (int) get_post_meta( $post->ID, '_candidate_user_id', true );
			if ( ! $candidate_id ) {
				return;
			}

			bp_notifications_add_notification(
				array(
					'user_id'          => $candidate_id,
					'item_id'          => $post->ID,
					'component_name'   => 'bpjm_notify_actions',
					'component_action' => 'application_status',
					'allow_duplicate'  => true,
				)
			);
		}

		/**
		 * Send the pre-3.0.1 /resumes/applied-jobs/ URL to /jobs/applied-jobs/.
		 *
		 * @since 3.0.1
		 */
		public function bpjm_redirect_old_applied_jobs() {
			if ( bp_is_user() && bp_is_current_component( 'resumes' ) && bp_is_current_action( 'applied-jobs' ) ) {
				bp_core_redirect( $this->bpjm_screen_url( bp_displayed_user_id(), 'jobs', 'applied-jobs' ) );
			}
		}

		/**
		 * Mark "X applied for Y" notices read once the owner opens Y's applications.
		 *
		 * The notice links here (bpjm_job_application_notifications()). Without
		 * this the unread count never clears, because no BuddyPress screen of
		 * ours marks them.
		 *
		 * @since 3.0.1
		 */
		private function bpjm_mark_application_notifications_read() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only view state; marks the viewer's own notices.
			$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
			if ( ! bp_is_my_profile() || ! bp_is_active( 'notifications' ) || empty( $_GET['job_id'] ) || 'show_applications' !== $action ) {
				return;
			}
			$job_id = absint( $_GET['job_id'] );
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( (int) get_post_field( 'post_author', $job_id ) !== bp_loggedin_user_id() ) {
				return;
			}

			$application_ids = get_posts(
				array(
					'post_type'      => 'job_application',
					'post_parent'    => $job_id,
					'post_status'    => 'any',
					'fields'         => 'ids',
					'posts_per_page' => -1, // ponytail: one job's applications; ids only, one UPDATE below.
					'no_found_rows'  => true,
				)
			);

			if ( $application_ids ) {
				bp_notifications_mark_notifications_by_item_ids( bp_loggedin_user_id(), $application_ids, 'bpjm_notify_actions', 'applied_job' );
			}
		}

		/**
		 * My Jobs - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_jobs_tab_function_to_show_title() {
			$sub = $this->bpjm_owner_sub_screen( 'job_id' );
			if ( 'show_applications' === $sub['action'] ) {
				/* translators: %s: job title. */
				echo esc_html( sprintf( __( 'Applications: %s', 'bp-job-manager' ), get_the_title( $sub['id'] ) ) );
			} elseif ( 'edit' === $sub['action'] ) {
				esc_html_e( 'Edit job', 'bp-job-manager' );
			} else {
				echo esc_html( $this->bpjm_get_member_screen_label( 'jobs', bp_displayed_user_id() ) );
			}
		}

		/**
		 * The owner's sub-screen on My Jobs / My Resumes, if any.
		 *
		 * WP Job Manager and Resume Manager open a job's applications, the job edit
		 * form and the resume edit form inside the same dashboard via ?action=,
		 * which left all of them headed "My Jobs" / "My Resumes" with no way back.
		 *
		 * @since 3.0.1
		 * @param string $id_key Query key holding the post ID: 'job_id' or 'resume_id'.
		 * @return array{action: string, id: int}
		 */
		private function bpjm_owner_sub_screen( $id_key ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only view state.
			$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
			$id     = isset( $_GET[ $id_key ] ) ? absint( $_GET[ $id_key ] ) : 0;
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( ! bp_is_my_profile() || ! $id || ! in_array( $action, array( 'show_applications', 'edit' ), true ) ) {
				return array(
					'action' => '',
					'id'     => 0,
				);
			}
			return array(
				'action' => $action,
				'id'     => $id,
			);
		}

		/**
		 * Keep "Save changes" on a resume inside the profile.
		 *
		 * When Resume Manager's own Candidate Dashboard page is set, saving a
		 * resume edited from My Resumes redirects there and the member leaves
		 * their profile. Resume Manager has no filter for that URL, so this
		 * rewrites only that one redirect, only on the member's own Resumes tab.
		 *
		 * @since 3.0.1
		 * @param string $location Redirect target.
		 * @return string
		 */
		public function bpjm_keep_resume_save_in_profile( $location ) {
			if ( ! bp_is_user() || ! bp_is_my_profile() || ! bp_is_current_component( 'resumes' ) ) {
				return $location;
			}
			$page_id = (int) get_option( 'resume_manager_candidate_dashboard_page_id' );
			if ( $page_id && untrailingslashit( (string) get_permalink( $page_id ) ) === untrailingslashit( $location ) ) {
				return $this->bpjm_screen_url( bp_loggedin_user_id(), 'resumes', 'my-resumes' );
			}
			return $location;
		}

		/**
		 * After posting a job on the profile, offer the way to My Jobs.
		 *
		 * WP Job Manager's confirmation only links to the listing itself, which
		 * left the member on an emptied Post Job screen.
		 *
		 * @since 3.0.1
		 */
		public function bpjm_job_submitted_next_step() {
			if ( ! bp_is_user() || ! bp_is_my_profile() || 'post-job' !== bp_current_action() ) {
				return;
			}
			printf(
				'<p class="bpjm-next"><a class="button" href="%1$s">%2$s</a></p>',
				esc_url( $this->bpjm_screen_url( bp_loggedin_user_id(), 'jobs', 'my-jobs' ) ),
				esc_html__( 'Go to My Jobs', 'bp-job-manager' )
			);
		}

		/**
		 * "Back to My Jobs / My Resumes" link above an owner sub-screen.
		 *
		 * @since 3.0.1
		 * @param string $tab 'jobs' or 'resumes'.
		 */
		private function bpjm_back_link( $tab ) {
			$is_jobs = ( 'jobs' === $tab );
			printf(
				'<p class="bpjm-back"><a href="%1$s">&larr; %2$s</a></p>',
				esc_url( $this->bpjm_screen_url( bp_loggedin_user_id(), $tab, $is_jobs ? 'my-jobs' : 'my-resumes' ) ),
				esc_html( $is_jobs ? __( 'Back to My Jobs', 'bp-job-manager' ) : __( 'Back to My Resumes', 'bp-job-manager' ) )
			);
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
			 * The owner gets WP Job Manager's [job_dashboard], a posting surface.
			 * Everyone else - and an owner WP Job Manager will not let post - gets
			 * the member's open jobs as WP Job Manager's own job cards, the same
			 * markup and translations as the site's Jobs page.
			 */
			$is_owner = ( bp_loggedin_user_id() === bp_displayed_user_id() );

			if ( $is_owner && $this->bpjm_restrict_user_post_job( true ) ) {
				$this->bpjm_render_owner_dashboard( 'jobs', '[job_dashboard]' );
				return;
			}

			$jobs = new WP_Query(
				array(
					'post_type'           => 'job_listing',
					'post_status'         => 'publish',
					'author'              => bp_displayed_user_id(),
					'posts_per_page'      => absint( get_option( 'job_manager_per_page', 10 ) ),
					'paged'               => $this->bpjm_current_page(),
					'orderby'             => 'date',
					'order'               => 'DESC',
					'ignore_sticky_posts' => true,
				)
			);

			if ( ! $jobs->have_posts() ) {
				if ( ! $this->bpjm_first_page_notice() ) {
					get_job_manager_template( 'content-no-jobs-found.php' );
				}
				return;
			}

			get_job_manager_template( 'job-listings-start.php' );
			while ( $jobs->have_posts() ) {
				$jobs->the_post();
				get_job_manager_template_part( 'content', 'job_listing' );
			}
			get_job_manager_template( 'job-listings-end.php' );
			wp_reset_postdata();

			// Links point at ?bpjm-page=N via bpjm_pagination_args().
			get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $jobs->max_num_pages ) );
		}

		/**
		 * Screen function for listing all my bookmarked jobs in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_bookmarked_jobs_show_screen() {
			$this->bpjm_load_screen( 'bookmarked_jobs' );
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
			$this->bpjm_load_screen( 'job_alerts' );
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
		 * Run Job Alerts' add/edit/delete handler on the member's own Job Alerts tab.
		 *
		 * Alerts only runs it on a page whose content holds [job_alerts]; ours is a
		 * profile tab. It used to run on every BuddyPress page, and with guest
		 * alerts enabled Alerts redirects any visitor without an action to
		 * ?action=add_alert - so every directory, stream and profile 302'd.
		 *
		 * @since 3.0.1 Scoped to the Job Alerts tab.
		 */
		public function bpjm_modify_job_alert_action_handler() {
			if ( class_exists( 'WP_Job_Manager_Alerts_Shortcodes' ) && bp_is_user() && bp_is_my_profile() && 'job-alerts' === bp_current_action() ) {
				$alert_handler = new WP_Job_Manager_Alerts_Shortcodes();
				$alert_handler->job_alerts_handler();
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
			if ( bp_is_user() && $this->bpjm_is_owned_action() ) {
				$per_page = absint( get_option( 'job_manager_per_page', 10 ) );

				$job_dashboard_job_listing_args['posts_per_page'] = $per_page;
				$job_dashboard_job_listing_args['offset']         = ( $this->bpjm_current_page() - 1 ) * $per_page;
				$this->bpjm_check_past_last_page( $job_dashboard_job_listing_args );
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
		 * Offer Relist on the member's own My Jobs tab.
		 *
		 * WP Job Manager only offers Relist when its submission page exists; on
		 * a BuddyPress site that page is the profile's Post Job tab, which
		 * bpjm_handle_submission_actions() sends the member to.
		 *
		 * @since    1.0.0
		 * @param    array   $actions Job actions keyed by slug.
		 * @param    WP_Post $job     Job listing.
		 * @return   array
		 */
		public function bpjm_job_dashboard_job_actions( $actions, $job ) {
			if ( 'expired' === $job->post_status && ! isset( $actions['relist'] )
				&& bp_is_user() && bp_is_my_profile() ) {
				$actions = array(
					'relist' => array(
						'label' => __( 'Relist', 'bp-job-manager' ),
						'nonce' => 'job_manager_my_job_actions',
					),
				) + $actions;
			}
			return $actions;
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
			// LEGACY - see bpjm_is_apply_page(). Only sites that had the page
			// created for them before 3.0.0 ever reach this.
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
			$resume_tab      = trim( sprintf( /* translators: %s: bp_core_number_format */ __( 'Resumes %s', 'bp-job-manager' ), $my_resumes_count ? '<span class="count">' . bp_core_number_format( $my_resumes_count ) . '</span>' : '' ) ); // No "0" badge on an empty tab.

			$resumes_tab_link = apply_filters( 'bpjm_resumes_tab_link', bp_core_get_userlink( $user_id, false, true ) . 'resumes/' );

			bp_core_new_nav_item(
				array(
					'name'                    => $resume_tab,
					'slug'                    => 'resumes',
					'screen_function'         => array( $this, 'bpjm_my_resumes_show_screen' ),
					'position'                => 75,
					'default_subnav_slug'     => 'my-resumes',
					'show_for_displayed_user' => true,
				)
			);

			// Subnav items.
			$this->bpjm_add_subnav( 'resumes', $resumes_tab_link, 'my-resumes', $resume_tab_name, 'bpjm_my_resumes_show_screen', 100 );

			if ( $is_logged_in_user ) {
				// Add Resume subnav.
				$this->bpjm_add_subnav( 'resumes', $resumes_tab_link, 'add-resume', apply_filters( 'bpjm_resumes_subnav_item_add_resume', __( 'Add Resume', 'bp-job-manager' ) ), 'bpjm_add_resume_show_screen', 200 );

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
			$this->bpjm_load_screen( 'add_resume' );
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
			$this->bpjm_load_screen( 'my_resumes' );
		}

		/**
		 * My Resumes - Title.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_my_resumes_tab_function_to_show_title() {
			$sub = $this->bpjm_owner_sub_screen( 'resume_id' );
			if ( 'edit' === $sub['action'] ) {
				esc_html_e( 'Edit resume', 'bp-job-manager' );
			} else {
				echo esc_html( $this->bpjm_get_member_screen_label( 'resumes', bp_displayed_user_id() ) );
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
			/*
			 * The owner gets Resume Manager's own [candidate_dashboard], scoped to
			 * them by bpjm_resume_dashboard_user_id(). It is an owner screen ("Your
			 * resume(s) can be edited...", Add Resume), so everyone else - other
			 * members and logged-out visitors - gets the member's published resumes
			 * as Resume Manager's own resume cards instead, behind Resume Manager's
			 * own "who can browse resumes" setting.
			 */
			if ( bp_is_my_profile() ) {
				$this->bpjm_render_owner_dashboard( 'resumes', '[candidate_dashboard]' );
				return;
			}

			$templates = RESUME_MANAGER_PLUGIN_DIR . '/templates/';

			if ( ! resume_manager_user_can_browse_resumes() ) {
				get_job_manager_template_part( 'access-denied', 'browse-resumes', 'wp-job-manager-resumes', $templates );
				return;
			}

			$resumes = new WP_Query(
				array(
					'post_type'      => 'resume',
					'post_status'    => 'publish',
					'author'         => bp_displayed_user_id(),
					'posts_per_page' => absint( get_option( 'resume_manager_per_page', 10 ) ),
					'paged'          => $this->bpjm_current_page(),
					'orderby'        => 'date',
					'order'          => 'DESC',
				)
			);

			if ( ! $resumes->have_posts() ) {
				if ( ! $this->bpjm_first_page_notice() ) {
					get_job_manager_template_part( 'content', 'no-resumes-found', 'wp-job-manager-resumes', $templates );
				}
				return;
			}

			get_job_manager_template( 'resumes-start.php', array(), 'wp-job-manager-resumes', $templates );
			while ( $resumes->have_posts() ) {
				$resumes->the_post();
				get_job_manager_template_part( 'content', 'resume', 'wp-job-manager-resumes', $templates );
			}
			get_job_manager_template( 'resumes-end.php', array(), 'wp-job-manager-resumes', $templates );
			wp_reset_postdata();

			// Links point at ?bpjm-page=N via bpjm_pagination_args().
			get_job_manager_template( 'pagination.php', array( 'max_num_pages' => $resumes->max_num_pages ) );
		}

		/**
		 * Screen function for listing all applied jobs in menu item.
		 *
		 * @since    1.0.0
		 * @author   wbcomdesigns
		 * @access   public
		 */
		public function bpjm_applied_jobs_show_screen() {
			if ( bp_is_my_profile() && bp_is_active( 'notifications' ) ) {
				bp_notifications_mark_notifications_by_type( bp_loggedin_user_id(), 'bpjm_notify_actions', 'application_status' );
			}
			$this->bpjm_load_screen( 'applied_jobs' );
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
					'offset'              => ( $this->bpjm_current_page() - 1 ) * 25,
					'orderby'             => 'date',
					'order'               => 'desc',
					'author'              => bp_displayed_user_id(),
				);
				$this->bpjm_check_past_last_page( $candidate_dashboard_resume_listing_args );
			}
			return $candidate_dashboard_resume_listing_args;
		}

		/**
		 * Resume actions on My Resumes: none for other viewers, Relist for the owner.
		 *
		 * @since    1.0.0
		 * @param    array   $actions Resume actions keyed by slug.
		 * @param    WP_Post $job     The resume.
		 * @return   array
		 */
		public function bpjm_candidate_dashboard_resume_actions( $actions, $job ) {
			if ( bp_is_user() ) {
				if ( bp_displayed_user_id() !== get_current_user_id() ) {
					$actions = array();
				}
			}

			// Resume Manager only offers Relist when its submission page exists; ours is the profile tab.
			if ( 'expired' === $job->post_status && ! isset( $actions['relist'] ) && bp_is_user() && bp_is_my_profile() ) {
				$actions = array(
					'relist' => array(
						'label' => __( 'Relist', 'bp-job-manager' ),
						'nonce' => true,
					),
				) + $actions;
			}
			return $actions;
		}

		/**
		 * Relist an expired resume from the profile, landing on the Add Resume tab.
		 *
		 * Resume Manager sends Relist to its standalone submission page, so the
		 * link does nothing when that page is not set (the normal BuddyPress
		 * setup) and leaves the profile when it is. Same nonce and edit check as
		 * Resume Manager; runs on `wp` before its own handler.
		 *
		 * @since 3.0.1
		 */
		public function bpjm_handle_resume_relist() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified below with Resume Manager's own nonce action.
			$action    = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
			$resume_id = isset( $_GET['resume_id'] ) ? absint( $_GET['resume_id'] ) : 0;
			$nonce     = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( 'relist' !== $action || ! $resume_id || ! function_exists( 'resume_manager_user_can_edit_resume' )
				|| ! bp_is_user() || ! bp_is_my_profile() || ! $this->bpjm_is_owned_action()
				|| ! wp_verify_nonce( $nonce, 'resume_manager_my_resume_actions' )
				|| 'resume' !== get_post_type( $resume_id ) || ! resume_manager_user_can_edit_resume( $resume_id ) ) {
				return;
			}

			wp_safe_redirect( add_query_arg( 'resume_id', $resume_id, $this->bpjm_screen_url( bp_loggedin_user_id(), 'resumes', 'add-resume' ) ) );
			exit;
		}

		/**
		 * Run Resume Manager's candidate-dashboard actions on the profile My Resumes tab.
		 *
		 * Job actions are not handled here: WP Job Manager runs its own handler on
		 * our job screens (bpjm_allow_continue_submission()), and the actions that
		 * need its submission page go through bpjm_handle_submission_actions().
		 *
		 * @since    1.0.6
		 */
		public function bpjm_shortcode_action_handler() {
			if ( ! bp_is_user() || ! class_exists( 'WP_Resume_Manager_Shortcodes' ) ) {
				return;
			}

			$action = filter_input( INPUT_GET, 'action' );
			if ( null !== $action && null !== filter_input( INPUT_GET, 'resume_id' ) && in_array( $action, array( 'delete', 'hide', 'publish' ), true ) ) {
				$wb_resume_shortcode_object = new WP_Resume_Manager_Shortcodes();
				$wb_resume_shortcode_object->candidate_dashboard_handler();
			}
		}

		/**
		 * Duplicate / Continue / Relist / Renew from the profile, landing on the Post Job tab.
		 *
		 * WP Job Manager sends these to its standalone submission page, and
		 * refuses with "Missing submission page." when that page is not set -
		 * which is the normal state on a BuddyPress site, where the profile's Post
		 * Job tab is the submission page. Runs on `wp` before WP Job Manager's own
		 * handler, with the same nonce and edit check.
		 *
		 * @since 3.0.1
		 */
		public function bpjm_handle_submission_actions() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified below with WP Job Manager's own nonce action.
			$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
			$job_id = isset( $_GET['job_id'] ) ? absint( $_GET['job_id'] ) : 0;
			$nonce  = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( ! $job_id || ! in_array( $action, array( 'duplicate', 'continue', 'relist', 'renew' ), true )
				|| ! bp_is_user() || ! bp_is_my_profile() || ! $this->bpjm_is_owned_action()
				|| ! wp_verify_nonce( $nonce, 'job_manager_my_job_actions' )
				|| 'job_listing' !== get_post_type( $job_id ) || ! job_manager_user_can_edit_job( $job_id ) ) {
				return;
			}

			$args = array(
				'job_id' => $job_id,
				'action' => $action,
			);

			if ( 'duplicate' === $action ) {
				$new_job_id = job_manager_duplicate_listing( $job_id );
				if ( ! $new_job_id ) {
					return; // Leave it to WP Job Manager, which reports the failure.
				}
				$args = array( 'job_id' => $new_job_id );
			} elseif ( 'renew' === $action ) {
				$args['nonce'] = wp_create_nonce( 'job_manager_renew_job_' . $job_id );
			}

			wp_safe_redirect( add_query_arg( $args, $this->bpjm_screen_url( bp_loggedin_user_id(), 'jobs', 'post-job' ) ) );
			exit;
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

				/*
				 * The applicant comes from the application, not from whoever
				 * happens to be logged in.
				 *
				 * This used to store get_current_user_id(). That is right for the
				 * common case - a member submitting the form is the applicant - but
				 * WP Job Manager Applications also creates applications from its
				 * LinkedIn, Xing, Facebook and resume-manager paths, where the
				 * current user is not necessarily the candidate and can be 0. A
				 * notification with secondary_item_id 0 renders with no name at
				 * all. `_candidate_user_id` is what the application itself records.
				 */
				$applicant_id = (int) get_post_meta( $application_id, '_candidate_user_id', true );

				if ( ! $applicant_id ) {
					$applicant_id = get_current_user_id();
				}

				if ( bp_is_active( 'notifications' ) ) {
					bp_notifications_add_notification(
						array(
							'user_id'           => $job_admin_id,
							'item_id'           => $application_id,
							'secondary_item_id' => $applicant_id,
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

				/*
				 * Display name, not user_login. The notice used to print the raw
				 * username, which is an internal identifier the member never chose
				 * to show anyone.
				 */
				$applicant_name = ( $applicant_info instanceof WP_User )
					? bp_core_get_user_displayname( $applicant_info->ID )
					: '';

				$application_data = get_post( $item_id );
				$job_id           = $application_data ? $application_data->post_parent : 0;
				$job_title        = $job_id ? get_the_title( $job_id ) : '';

				/*
				 * Name the job.
				 *
				 * "submitted an application for your job posting" does not say
				 * WHICH posting - an employer with several open roles has to click
				 * through to find out, which is the entire value of the notice. The
				 * job title is already resolved here.
				 */
				if ( '' !== $job_title ) {
					/* translators: 1: applicant display name, 2: job title. */
					$default_text = sprintf( esc_html__( '%1$s applied for %2$s.', 'bp-job-manager' ), $applicant_name, $job_title );
				} else {
					/* translators: %s: applicant display name. */
					$default_text = sprintf( esc_html__( '%s applied for one of your jobs.', 'bp-job-manager' ), $applicant_name );
				}

				/**
				 * Filters the application notification text.
				 *
				 * Hook name kept for backwards compatibility - it predates this
				 * plugin's naming convention and renaming it would silently drop
				 * every existing customisation.
				 *
				 * @since 4.3.0
				 *
				 * @param string $value Notification text.
				 */
				$text = apply_filters( '_bprwg_notification_text', $default_text ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Long-standing public contract.

				/*
				 * Point at the owner's own My Jobs tab, never WP Job Manager's
				 * dashboard page: that page is normally unconfigured on a BuddyPress
				 * site, which left this notice with an empty link. Viewing the list
				 * marks the notice read (bpjm_mark_application_notifications_read()).
				 */
				$application_link = '';
				$job_owner        = $job_id ? (int) get_post_field( 'post_author', $job_id ) : 0;
				if ( $job_owner ) {
					$application_link = add_query_arg(
						array(
							'action' => 'show_applications',
							'job_id' => $job_id,
						),
						$this->bpjm_screen_url( $job_owner, 'jobs', 'my-jobs' )
					);
				}

				return $this->bpjm_notification_output( $text, $application_link, $format );
			}

			if ( 'application_status' === $component_action_name ) {
				$application = get_post( $item_id );
				$job_title   = $application ? get_the_title( $application->post_parent ) : '';
				$status      = $application ? get_post_status_object( $application->post_status ) : null;

				/* translators: 1: job title, 2: application status label such as "Interviewed". */
				$text = sprintf( esc_html__( 'Your application for %1$s is now: %2$s.', 'bp-job-manager' ), esc_html( $job_title ), esc_html( $status ? $status->label : '' ) );
				return $this->bpjm_notification_output( $text, $this->bpjm_screen_url( bp_loggedin_user_id(), 'jobs', 'applied-jobs' ), $format );
			}

			return $action;
		}

		/**
		 * A notification as BuddyPress asks for it: a toolbar link, or text + link.
		 *
		 * @since 3.0.1
		 * @param string $text   Escaped notification text.
		 * @param string $link   Target URL.
		 * @param string $format 'string' or 'object'.
		 * @return string|array
		 */
		private function bpjm_notification_output( $text, $link, $format ) {
			if ( 'string' === $format ) {
				return sprintf( '<a class="ab-item" href="%s">%s</a>', esc_url( $link ), $text );
			}
			return array(
				'text' => $text,
				'link' => $link,
			);
		}

		/**
		 * Restrict to post job according to user role
		 *
		 * @param boolean $can_post user can post or not.
		 * @return boolean
		 */
		public function bpjm_restrict_user_post_job( $can_post ) {
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
		 * Let WP Job Manager's dashboard actions run on the member's own job screens.
		 *
		 * WP Job Manager only runs them on a page holding [job_dashboard]; here
		 * that dashboard lives in a profile tab. This used to answer true for
		 * every request on the site, so WP Job Manager's handler ran everywhere.
		 *
		 * @since 1.35.0
		 *
		 * @param bool $is_page Whether WP Job Manager thinks this is its dashboard page.
		 * @return bool
		 */
		public function bpjm_allow_continue_submission( $is_page ) {
			return $is_page || ( bp_is_user() && bp_is_my_profile() && $this->bpjm_is_owned_action() );
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
				delete_transient( "bpjm_user_{$post->post_author}_job_listings" );
				delete_transient( "bpjm_user_{$post->post_author}_my_resumes_count" );
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
