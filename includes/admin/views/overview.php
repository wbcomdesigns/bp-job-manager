<?php
/**
 * Overview dashboard partial: rendered inside shell.php.
 *
 * Read-only snapshot — dependency status, who-can-do-what role counts,
 * and the current activity/notification feature configuration. No DB
 * writes; all data comes from the already-loaded $settings option plus
 * active-plugin detection.
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin/views
 * @since   2.8.5
 */

defined( 'ABSPATH' ) || exit;

/**
 * Parent view provides $settings + $page_url.
 *
 * @var array  $settings bpjm_general_settings already loaded by render_page().
 * @var string $page_url Base admin URL for this plugin page.
 */

$general_url = isset( $page_url ) ? $page_url . '&tab=general' : admin_url( 'admin.php?page=bp-job-manager&tab=general' );

// Dependency detection.
$bpjm_bp_active   = class_exists( 'BuddyPress' );
$bpjm_wpjm_active = class_exists( 'WP_Job_Manager' );
$active_plugins   = (array) get_option( 'active_plugins', array() );
$bpjm_resumes_on  = in_array( 'wp-job-manager-resumes/wp-job-manager-resumes.php', $active_plugins, true );
$bpjm_apps_on     = in_array( 'wp-job-manager-applications/wp-job-manager-applications.php', $active_plugins, true );

// Role configuration counts (fall back to the same defaults as Globals).
$post_roles  = ! empty( $settings['post_job_user_roles'] ) && is_array( $settings['post_job_user_roles'] )
	? $settings['post_job_user_roles']
	: array( 'administrator', 'employer', 'guest' );
$apply_roles = ! empty( $settings['apply_job_user_roles'] ) && is_array( $settings['apply_job_user_roles'] )
	? $settings['apply_job_user_roles']
	: array( 'administrator', 'candidate', 'subscriber' );

// Feature toggles — read the same way Bp_Job_Manager_Globals does
// (presence of the key means "on").
$job_activity_on    = array_key_exists( 'bpjm_job_post_activity', $settings );
$resume_activity_on = array_key_exists( 'bpjm_resume_activity', $settings );
$resume_profile_on  = empty( $settings ) ? true : array_key_exists( 'bpjm_resume_at_profile', $settings );
$app_notify_on      = array_key_exists( 'bpjm_app_notify', $settings );
?>

<div class="bpjm-stats-grid">
	<div class="bpjm-stat">
		<p class="bpjm-stat__label"><?php esc_html_e( 'Roles That Can Post Jobs', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__value"><?php echo esc_html( number_format_i18n( count( $post_roles ) ) ); ?></p>
		<p class="bpjm-stat__trend"><?php esc_html_e( 'Members in these roles get the Jobs profile tab', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-stat">
		<p class="bpjm-stat__label"><?php esc_html_e( 'Roles That Can Apply', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__value"><?php echo esc_html( number_format_i18n( count( $apply_roles ) ) ); ?></p>
		<p class="bpjm-stat__trend"><?php esc_html_e( 'Members in these roles get the Resumes profile tab', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-stat">
		<p class="bpjm-stat__label"><?php esc_html_e( 'Resume Manager', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__value"><?php echo $bpjm_resumes_on ? esc_html__( 'On', 'bp-job-manager' ) : esc_html__( 'Off', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__trend"><?php esc_html_e( 'Adds resume management inside profiles', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-stat">
		<p class="bpjm-stat__label"><?php esc_html_e( 'Applications', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__value"><?php echo $bpjm_apps_on ? esc_html__( 'On', 'bp-job-manager' ) : esc_html__( 'Off', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__trend"><?php esc_html_e( 'Lets members apply to jobs from their profile', 'bp-job-manager' ); ?></p>
	</div>
</div>

<?php if ( ! $bpjm_bp_active || ! $bpjm_wpjm_active ) : ?>
	<div class="bpjm-notice bpjm-notice--warn">
		<span class="dashicons dashicons-warning" aria-hidden="true"></span>
		<div>
			<strong><?php esc_html_e( 'Required plugins missing.', 'bp-job-manager' ); ?></strong>
			<?php esc_html_e( 'BuddyPress Job Manager needs both BuddyPress and WP Job Manager to be active. Activate them to use the profile job and resume features.', 'bp-job-manager' ); ?>
		</div>
	</div>
<?php endif; ?>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'Required Integrations', 'bp-job-manager' ); ?></p>
		<p class="bpjm-card__desc"><?php esc_html_e( 'What this plugin connects to. Resume and application features need the matching WP Job Manager add-ons.', 'bp-job-manager' ); ?></p>
	</div>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'BuddyPress', 'bp-job-manager' ); ?></th>
			<td>
				<?php if ( $bpjm_bp_active ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--off"><?php esc_html_e( 'Not active', 'bp-job-manager' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'WP Job Manager', 'bp-job-manager' ); ?></th>
			<td>
				<?php if ( $bpjm_wpjm_active ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--off"><?php esc_html_e( 'Not active', 'bp-job-manager' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Resume Manager add-on', 'bp-job-manager' ); ?></th>
			<td>
				<?php if ( $bpjm_resumes_on ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Optional — not active', 'bp-job-manager' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Applications add-on', 'bp-job-manager' ); ?></th>
			<td>
				<?php if ( $bpjm_apps_on ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Optional — not active', 'bp-job-manager' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</div>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'Current Configuration', 'bp-job-manager' ); ?></p>
		<p class="bpjm-card__desc"><?php esc_html_e( 'What happens right now when members post jobs, publish resumes, or apply. Change any of these on the General tab.', 'bp-job-manager' ); ?></p>
	</div>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'Activity for job posts', 'bp-job-manager' ); ?></th>
			<td>
				<?php if ( $job_activity_on ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'On: a new job creates a BuddyPress activity update', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Off', 'bp-job-manager' ); ?></span>
					<a href="<?php echo esc_url( $general_url ); ?>" class="bpjm-inline-hint"><?php esc_html_e( 'Turn on', 'bp-job-manager' ); ?></a>
				<?php endif; ?>
			</td>
		</tr>
		<?php if ( $bpjm_resumes_on ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Activity for resumes', 'bp-job-manager' ); ?></th>
				<td>
					<?php if ( $resume_activity_on ) : ?>
						<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'On: a new resume creates a BuddyPress activity update', 'bp-job-manager' ); ?></span>
					<?php else : ?>
						<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Off', 'bp-job-manager' ); ?></span>
						<a href="<?php echo esc_url( $general_url ); ?>" class="bpjm-inline-hint"><?php esc_html_e( 'Turn on', 'bp-job-manager' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Resume on profile', 'bp-job-manager' ); ?></th>
				<td>
					<?php if ( $resume_profile_on ) : ?>
						<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'On: members see the latest resume on profiles', 'bp-job-manager' ); ?></span>
					<?php else : ?>
						<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Off', 'bp-job-manager' ); ?></span>
						<a href="<?php echo esc_url( $general_url ); ?>" class="bpjm-inline-hint"><?php esc_html_e( 'Turn on', 'bp-job-manager' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( $bpjm_apps_on ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Application notifications', 'bp-job-manager' ); ?></th>
				<td>
					<?php if ( $app_notify_on ) : ?>
						<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'On: job authors get a BuddyPress notification for each application', 'bp-job-manager' ); ?></span>
					<?php else : ?>
						<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Off', 'bp-job-manager' ); ?></span>
						<a href="<?php echo esc_url( $general_url ); ?>" class="bpjm-inline-hint"><?php esc_html_e( 'Turn on', 'bp-job-manager' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endif; ?>
	</table>
</div>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'Quick Actions', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-card__body">
		<div class="bpjm-save-bar bpjm-save-bar--wrap">
			<a href="<?php echo esc_url( $general_url ); ?>" class="bpjm-btn bpjm-btn-secondary">
				<span class="dashicons dashicons-admin-settings" aria-hidden="true"></span>
				<?php esc_html_e( 'Configure Settings', 'bp-job-manager' ); ?>
			</a>
			<a href="https://docs.wbcomdesigns.com/doc_category/buddypress-job-manager/" target="_blank" rel="noopener noreferrer" class="bpjm-btn bpjm-btn-secondary">
				<span class="dashicons dashicons-book" aria-hidden="true"></span>
				<?php esc_html_e( 'Read Documentation', 'bp-job-manager' ); ?>
			</a>
		</div>
	</div>
</div>
