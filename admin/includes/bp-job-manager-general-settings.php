<?php
/**
 * Provide a admin area view for Export X-Profile fields data.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    bp-job-manager
 * @subpackage bp-job-manager/admin/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $bp_job_manager, $wp_roles;
?>
<div class="wbcom-tab-content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bpjm_general_settings' );
		do_settings_sections( 'bpjm_general_settings' );
		?>
		<div class="wrap">
			<div class='wpbpjm-general-settings-container'>
				<table class="form-table">
					<tbody>
						<!-- ROLES ALLOWED FOR JOB POSTING -->
						<tr>
							<th scope="row"><label for="wpbpjm-job-member-types"><?php esc_attr_e( 'Post Job Roles', 'bp-job-manager' ); ?></label></th>
							<td>
								<?php if ( isset( $wp_roles->roles ) ) { ?>
									<select multiple required name="bpjm_general_settings[post_job_user_roles][]" class="bpjm-user-roles">
										<?php foreach ( $wp_roles->roles as $slug => $wp_role ) { ?>
											<option value="<?php echo esc_attr( $slug ); ?>" 
																	  <?php
																		if ( ! empty( $bp_job_manager->post_job_user_roles ) && in_array( $slug, $bp_job_manager->post_job_user_roles, true ) ) {
																			echo 'selected = selected'; }
																		?>
											><?php echo esc_html( $wp_role['name'] ); ?></option>
										<?php } ?>
									</select>
								<?php } ?>
								<p class="description"><?php esc_attr_e( 'Select the user roles that are allowed to post jobs on your site.', 'bp-job-manager' ); ?></p>
							</td>
						</tr>

						<!-- ROLES ALLOWED FOR JOB APPLY -->
						<tr>
							<th scope="row"><label for="wpbpjm-resume-member-types"><?php esc_attr_e( 'Apply Job Roles', 'bp-job-manager' ); ?></label></th>
							<td>
								<?php if ( isset( $wp_roles->roles ) ) { ?>
									<select multiple required name="bpjm_general_settings[apply_job_user_roles][]" class="bpjm-user-roles">
										<?php foreach ( $wp_roles->roles as $slug => $wp_role ) { ?>
											<option value="<?php echo esc_attr( $slug ); ?>" 
																	  <?php
																		if ( ! empty( $bp_job_manager->apply_job_user_roles ) && in_array( $slug, $bp_job_manager->apply_job_user_roles, true ) ) {
																			echo 'selected = selected'; }
																		?>
											><?php echo esc_html( $wp_role['name'] ); ?></option>
										<?php } ?>
									</select>
								<?php } ?>
								<p class="description"><?php esc_attr_e( 'Select the user roles that are allowed to apply for the jobs on your site.', 'bp-job-manager' ); ?></p>
							</td>
						</tr>
						<!-- ROLES ALLOWED FOR JOB APPLY -->
						<tr>
							<th scope="row"><label for="wpbpjm-resume-profile-view"><?php esc_attr_e( 'BuddyPress activity on posting job', 'bp-job-manager' ); ?></label></th>
							<td>
								<label class="blpro-switch">
									<input type="checkbox" name="bpjm_general_settings[bpjm_job_post_activity]" 
									<?php
									if ( isset( $bp_job_manager->bpjm_job_post_activity ) ) {
										checked( $bp_job_manager->bpjm_job_post_activity, 'yes' ); }
									?>
									>
									<div class="blpro-slider blpro-round"></div>
								</label>
								<p class="description"><?php esc_attr_e( 'Check this option if you want to create BuddyPress activity on posting job.', 'bp-job-manager' ); ?></p>
							</td>
						</tr>
						<?php
							$wpjm_resumes_active = in_array( 'wp-job-manager-resumes/wp-job-manager-resumes.php', get_option( 'active_plugins' ) );
						if ( $wpjm_resumes_active == true ) {
							?>
								<tr>
									<th scope="row"><label for="wpbpjm-resume-profile-view"><?php esc_attr_e( 'BuddyPress activity on posting resume', 'bp-job-manager' ); ?></label></th>
									<td>
										<label class="blpro-switch">
											<input type="checkbox" name="bpjm_general_settings[bpjm_resume_activity]" 
											<?php
											if ( isset( $bp_job_manager->bpjm_resume_activity ) ) {
												checked( $bp_job_manager->bpjm_resume_activity, 'yes' ); }
											?>
											>
											<div class="blpro-slider blpro-round"></div>
										</label>
										<p class="description"><?php esc_attr_e( 'Check this option if you want to create BuddyPress activity on posting resume.', 'bp-job-manager' ); ?></p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="wpbpjm-resume-profile-view"><?php esc_attr_e( 'Display resume at BuddyPress Profile', 'bp-job-manager' ); ?></label></th>
									<td>
										<label class="blpro-switch">
											<input type="checkbox" name="bpjm_general_settings[bpjm_resume_at_profile]" 
											<?php
											if ( isset( $bp_job_manager->bpjm_resume_at_profile ) ) {
												checked( $bp_job_manager->bpjm_resume_at_profile, 'yes' ); }
											?>
											>
											<div class="blpro-slider blpro-round"></div>
										</label>
										<p class="description"><?php esc_attr_e( 'Check this option if you want to list the last updated resume at BuddyPress profile.', 'bp-job-manager' ); ?></p>
									</td>
								</tr>
							<?php } ?>
							<?php
							$wpjm_applications_active = in_array( 'wp-job-manager-applications/wp-job-manager-applications.php', get_option( 'active_plugins' ) );
							if ( $wpjm_applications_active == true ) {
								?>
								<tr>
									<th scope="row"><label for="wpbpjm-resume-profile-view"><?php esc_attr_e( 'Send BuddyPress notification to job author on a job application', 'bp-job-manager' ); ?></label></th>
									<td>
										<label class="blpro-switch">
											<input type="checkbox" name="bpjm_general_settings[bpjm_app_notify]" 
											<?php
											if ( isset( $bp_job_manager->bpjm_app_notify ) ) {
												checked( $bp_job_manager->bpjm_app_notify, 'yes' ); }
											?>
											>
											<div class="blpro-slider blpro-round"></div>
										</label>
										<p class="description"><?php esc_attr_e( 'Check this option if you want to send bp notification to job author when someone applies to the job.', 'bp-job-manager' ); ?></p>
									</td>
								</tr>
							<?php } ?>
					</tbody>
				</table>
				<p class="submit">
					<?php wp_nonce_field( 'bpjm-general', 'bpjm-general-settings-nonce' ); ?>
					<input type="submit" name="bpjm-general-settings-submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'bp-job-manager' ); ?>">
				</p>
			</div>
		</div>
	</form>
</div>	
