<?php
/**
 * Settings tab: General (roles + activity + notification toggles).
 *
 * Posts the bpjm_general_settings option. Field names are byte-identical
 * to the legacy form so the saved option shape and the runtime config
 * read by Bp_Job_Manager_Globals are unchanged:
 *
 *  - post_job_user_roles[]   (multi-select role list)
 *  - apply_job_user_roles[]  (multi-select role list)
 *  - bpjm_job_post_activity  (checkbox; present => yes)
 *  - bpjm_resume_activity    (checkbox; present => yes)
 *  - bpjm_resume_at_profile  (checkbox; present => yes)
 *  - bpjm_app_notify         (checkbox; present => yes)
 *
 * Sentinel hidden inputs (bpjm_tab_rendered_keys / bpjm_array_rendered_keys)
 * tell the sanitizer which fields this tab owns, so unchecked boxes and
 * cleared role lists persist as "off"/empty. See playbook Part 7.1.
 *
 * @package Bp_Job_Manager
 * @subpackage Bp_Job_Manager/includes/admin/views
 * @since   2.8.5
 */

defined( 'ABSPATH' ) || exit;

global $wp_roles, $bpjm_bp_job_manager;

/*
 * $settings is the bpjm_general_settings option, injected by
 * Bp_Job_Manager_Admin_Panel::render_page() through include scope. Normalise it
 * here so this view is safe if it is ever included directly, and so the
 * array_key_exists() calls below cannot receive null (a TypeError on PHP 8).
 */
$settings = ( isset( $settings ) && is_array( $settings ) ) ? $settings : array(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Injected by Bp_Job_Manager_Admin_Panel::render_page() through include scope, not a global.

$all_roles = isset( $wp_roles ) ? $wp_roles->get_names() : array();

// Selected role lists — prefer the stored option, fall back to the
// same defaults Bp_Job_Manager_Globals seeds for a fresh install.
$post_selected  = ! empty( $settings['post_job_user_roles'] ) && is_array( $settings['post_job_user_roles'] )
	? $settings['post_job_user_roles']
	: ( isset( $bpjm_bp_job_manager->post_job_user_roles ) ? (array) $bpjm_bp_job_manager->post_job_user_roles : array() );
$apply_selected = ! empty( $settings['apply_job_user_roles'] ) && is_array( $settings['apply_job_user_roles'] )
	? $settings['apply_job_user_roles']
	: ( isset( $bpjm_bp_job_manager->apply_job_user_roles ) ? (array) $bpjm_bp_job_manager->apply_job_user_roles : array() );

// Checkbox states (presence => on, mirroring Globals).
$job_activity_on    = array_key_exists( 'bpjm_job_post_activity', $settings );
$resume_activity_on = array_key_exists( 'bpjm_resume_activity', $settings );
$resume_profile_on  = empty( $settings ) ? true : array_key_exists( 'bpjm_resume_at_profile', $settings );
$app_notify_on      = array_key_exists( 'bpjm_app_notify', $settings );

// Add-on availability gates the matching fields, same as the legacy form.
// bpjm_is_plugin_active() also covers network activation - a bare
// active_plugins lookup reports every network-activated add-on as off.
$bpjm_resumes_on = bpjm_is_plugin_active( 'wp-job-manager-resumes/wp-job-manager-resumes.php' );
$bpjm_apps_on    = bpjm_is_plugin_active( 'wp-job-manager-applications/wp-job-manager-applications.php' );

// Sentinels: which checkbox keys this tab renders. The sanitizer treats
// any listed key absent from $_POST as an explicit "off".
$tab_rendered_keys = array( 'bpjm_job_post_activity' );
if ( $bpjm_resumes_on ) {
	$tab_rendered_keys[] = 'bpjm_resume_activity';
	$tab_rendered_keys[] = 'bpjm_resume_at_profile';
}
if ( $bpjm_apps_on ) {
	$tab_rendered_keys[] = 'bpjm_app_notify';
}
foreach ( $tab_rendered_keys as $tab_rendered_key ) :
	?>
	<input type="hidden" name="bpjm_general_settings[bpjm_tab_rendered_keys][]" value="<?php echo esc_attr( $tab_rendered_key ); ?>">
	<?php
endforeach;

// Sentinels: which multi-select role arrays this tab renders. Lets the
// sanitizer treat a cleared list (absent from $_POST) as an empty list.
$array_rendered_keys = array( 'post_job_user_roles', 'apply_job_user_roles' );
foreach ( $array_rendered_keys as $array_rendered_key ) :
	?>
	<input type="hidden" name="bpjm_general_settings[bpjm_array_rendered_keys][]" value="<?php echo esc_attr( $array_rendered_key ); ?>">
<?php endforeach; ?>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'Who Can Post & Apply', 'bp-job-manager' ); ?></p>
		<p class="bpjm-card__desc"><?php esc_html_e( 'Pick the member roles that get each profile tab. Search for a role and select as many as you need. Only job posting can be opened to logged-out visitors, with the Guest option.', 'bp-job-manager' ); ?></p>
	</div>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="bpjm-post-roles"><?php esc_html_e( 'Roles allowed to post jobs', 'bp-job-manager' ); ?></label>
			</th>
			<td>
				<?php if ( ! empty( $all_roles ) ) : ?>
					<select multiple required id="bpjm-post-roles" name="bpjm_general_settings[post_job_user_roles][]" class="bpjm-role-select" size="6" data-bpjm-role-picker="1">
						<?php foreach ( $all_roles as $role_slug => $role_label ) : ?>
							<option value="<?php echo esc_attr( $role_slug ); ?>" <?php selected( in_array( $role_slug, $post_selected, true ) ); ?>>
								<?php echo esc_html( translate_user_role( $role_label ) ); ?>
							</option>
						<?php endforeach; ?>
						<option value="guest" <?php selected( in_array( 'guest', $post_selected, true ) ); ?>>
							<?php esc_html_e( 'Guest (not logged in)', 'bp-job-manager' ); ?>
						</option>
					</select>
				<?php endif; ?>
				<p class="description"><?php esc_html_e( 'Members in these roles get the Jobs tab on their profile, where they can post and manage job listings.', 'bp-job-manager' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="bpjm-apply-roles"><?php esc_html_e( 'Roles allowed to apply for jobs', 'bp-job-manager' ); ?></label>
			</th>
			<td>
				<?php if ( ! empty( $all_roles ) ) : ?>
					<?php
					/*
					 * No "Guest" option here. Applying is gated by
					 * Bp_Job_Manager_Public at :902-906, which only ever
					 * intersects the saved list against a logged-in
					 * $user->roles — there is no logged-out branch, so a
					 * saved "guest" value was silently inert. Offering it
					 * told the site owner they had enabled something the
					 * plugin never implemented.
					 */
					?>
					<select multiple required id="bpjm-apply-roles" name="bpjm_general_settings[apply_job_user_roles][]" class="bpjm-role-select" size="6" data-bpjm-role-picker="1">
						<?php foreach ( $all_roles as $role_slug => $role_label ) : ?>
							<option value="<?php echo esc_attr( $role_slug ); ?>" <?php selected( in_array( $role_slug, $apply_selected, true ) ); ?>>
								<?php echo esc_html( translate_user_role( $role_label ) ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
				<p class="description"><?php esc_html_e( 'Members in these roles get the Resumes tab on their profile, where they can manage resumes and apply for jobs.', 'bp-job-manager' ); ?></p>
			</td>
		</tr>
	</table>
</div>

<div class="bpjm-card">
	<div class="bpjm-card__head">
		<p class="bpjm-card__title"><?php esc_html_e( 'Activity & Notifications', 'bp-job-manager' ); ?></p>
		<p class="bpjm-card__desc"><?php esc_html_e( 'Decide what shows up in the BuddyPress activity feed and which notifications members receive.', 'bp-job-manager' ); ?></p>
	</div>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'Post job activity', 'bp-job-manager' ); ?></th>
			<td>
				<label class="bpjm-toggle">
					<input type="checkbox" name="bpjm_general_settings[bpjm_job_post_activity]" value="yes" <?php checked( $job_activity_on ); ?>>
					<?php esc_html_e( 'Add a BuddyPress activity update when a member posts a job', 'bp-job-manager' ); ?>
				</label>
			</td>
		</tr>
		<?php if ( $bpjm_resumes_on ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Post resume activity', 'bp-job-manager' ); ?></th>
				<td>
					<label class="bpjm-toggle">
						<input type="checkbox" name="bpjm_general_settings[bpjm_resume_activity]" value="yes" <?php checked( $resume_activity_on ); ?>>
						<?php esc_html_e( 'Add a BuddyPress activity update when a member publishes a resume', 'bp-job-manager' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Resume on profile', 'bp-job-manager' ); ?></th>
				<td>
					<label class="bpjm-toggle">
						<input type="checkbox" name="bpjm_general_settings[bpjm_resume_at_profile]" value="yes" <?php checked( $resume_profile_on ); ?>>
						<?php esc_html_e( 'Show the latest resume on the member\'s BuddyPress profile', 'bp-job-manager' ); ?>
					</label>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( $bpjm_apps_on ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Application notifications', 'bp-job-manager' ); ?></th>
				<td>
					<label class="bpjm-toggle">
						<input type="checkbox" name="bpjm_general_settings[bpjm_app_notify]" value="yes" <?php checked( $app_notify_on ); ?>>
						<?php esc_html_e( 'Send the job author a BuddyPress notification when someone applies', 'bp-job-manager' ); ?>
					</label>
				</td>
			</tr>
		<?php endif; ?>
	</table>
</div>

<?php if ( ! $bpjm_resumes_on || ! $bpjm_apps_on ) : ?>
	<div class="bpjm-notice bpjm-notice--info">
		<span class="dashicons dashicons-info" aria-hidden="true"></span>
		<div>
			<strong><?php esc_html_e( 'More options unlock with the WP Job Manager add-ons.', 'bp-job-manager' ); ?></strong>
			<?php
			/* translators: 1: "Resume Manager" add-on name, 2: "Applications" add-on name. Both are WP Job Manager product names — do not translate the placeholder values. */
			printf( esc_html__( 'Install the %1$s and %2$s add-ons to manage resumes on profiles and notify job authors about new applications.', 'bp-job-manager' ), 'Resume Manager', 'Applications' );
			?>
		</div>
	</div>
<?php endif; ?>
