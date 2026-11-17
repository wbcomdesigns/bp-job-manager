<?php
/**
 * Overview dashboard partial: rendered inside layout.php.
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

/*
 * $settings (the bpjm_general_settings option) and $page_url (this plugin's
 * base admin URL) are injected by Bp_Job_Manager_Admin_Panel::render_page()
 * through include scope. Normalise both so this view is safe if it is ever
 * included directly.
 */
$settings = ( isset( $settings ) && is_array( $settings ) ) ? $settings : array(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Injected by Bp_Job_Manager_Admin_Panel::render_page() through include scope, not a global.

$general_url = isset( $page_url ) ? $page_url . '&tab=general' : admin_url( 'admin.php?page=bp-job-manager&tab=general' );

// Dependency detection.
$bpjm_bp_active   = class_exists( 'BuddyPress' );
$bpjm_wpjm_active = class_exists( 'WP_Job_Manager' );
$bpjm_resumes_on  = bpjm_is_plugin_active( 'wp-job-manager-resumes/wp-job-manager-resumes.php' );
$bpjm_apps_on     = bpjm_is_plugin_active( 'wp-job-manager-applications/wp-job-manager-applications.php' );

// Feature toggles — read the same way Bp_Job_Manager_Globals does
// (presence of the key means "on").
$job_activity_on    = array_key_exists( 'bpjm_job_post_activity', $settings );
$resume_activity_on = array_key_exists( 'bpjm_resume_activity', $settings );
$app_notify_on      = array_key_exists( 'bpjm_app_notify', $settings );
?>

<div class="bpjm-stats-grid">
	<div class="bpjm-stat">
		<p class="bpjm-stat__label"><?php echo 'Resume Manager'; ?></p>
		<p class="bpjm-stat__value"><?php echo $bpjm_resumes_on ? esc_html__( 'On', 'bp-job-manager' ) : esc_html__( 'Off', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__trend"><?php esc_html_e( 'Adds resume management inside profiles', 'bp-job-manager' ); ?></p>
	</div>
	<div class="bpjm-stat">
		<p class="bpjm-stat__label"><?php echo 'Applications'; ?></p>
		<p class="bpjm-stat__value"><?php echo $bpjm_apps_on ? esc_html__( 'On', 'bp-job-manager' ) : esc_html__( 'Off', 'bp-job-manager' ); ?></p>
		<p class="bpjm-stat__trend"><?php esc_html_e( 'Lets members apply to jobs and follow each application from their profile', 'bp-job-manager' ); ?></p>
	</div>
</div>

<?php if ( ! $bpjm_bp_active || ! $bpjm_wpjm_active ) : ?>
	<div class="bpjm-notice bpjm-notice--warn">
		<?php echo bpjm_icon( 'alert' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in bpjm_icon(). ?>
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
			<th scope="row"><?php echo 'BuddyPress'; ?></th>
			<td>
				<?php if ( $bpjm_bp_active ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--off"><?php esc_html_e( 'Not active', 'bp-job-manager' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo 'WP Job Manager'; ?></th>
			<td>
				<?php if ( $bpjm_wpjm_active ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--off"><?php esc_html_e( 'Not active', 'bp-job-manager' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php
				/* translators: %s: name of a WP Job Manager add-on, e.g. "Resume Manager" or "Applications". Product name - do not translate the placeholder value. */
				printf( esc_html__( '%s add-on', 'bp-job-manager' ), 'Resume Manager' );
				?>
			</th>
			<td>
				<?php if ( $bpjm_resumes_on ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Optional, not active', 'bp-job-manager' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php
				/* translators: %s: name of a WP Job Manager add-on, e.g. "Resume Manager" or "Applications". Product name - do not translate the placeholder value. */
				printf( esc_html__( '%s add-on', 'bp-job-manager' ), 'Applications' );
				?>
			</th>
			<td>
				<?php if ( $bpjm_apps_on ) : ?>
					<span class="bpjm-status bpjm-status--ok"><?php esc_html_e( 'Active', 'bp-job-manager' ); ?></span>
				<?php else : ?>
					<span class="bpjm-status bpjm-status--muted"><?php esc_html_e( 'Optional, not active', 'bp-job-manager' ); ?></span>
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
				<?php echo bpjm_icon( 'settings' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in bpjm_icon(). ?>
				<?php esc_html_e( 'Configure Settings', 'bp-job-manager' ); ?>
			</a>
			<a href="https://docs.wbcomdesigns.com/doc_category/buddypress-job-manager/" target="_blank" rel="noopener noreferrer" class="bpjm-btn bpjm-btn-secondary">
				<?php echo bpjm_icon( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in bpjm_icon(). ?>
				<?php esc_html_e( 'Read Documentation', 'bp-job-manager' ); ?>
			</a>
		</div>
	</div>
</div>
