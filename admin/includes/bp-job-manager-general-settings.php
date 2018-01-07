<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $bp_job_manager, $wp_roles;
?>
<form action="" method="POST">
	<div class="wrap">
		<h3><?php _e( 'General Settings', BPJM_TEXT_DOMAIN ); ?></h3>
		<div class='wpbpjm-general-settings-container'>
			<table class="form-table">
				<tbody>
					<!-- ROLES ALLOWED FOR JOB POSTING -->
					<tr>
						<th scope="row"><label for="wpbpjm-job-member-types"><?php _e( 'Post Job Roles', BPJM_TEXT_DOMAIN ); ?></label></th>
						<td>
							<?php if ( isset( $wp_roles->roles ) ) { ?>
								<select multiple required name="bpjm-post-jobs-user-roles[]" class="bpjm-user-roles">
									<?php foreach ( $wp_roles->roles as $slug => $wp_role ) { ?>
										<option value="<?php echo $slug; ?>" <?php echo ( ! empty( $bp_job_manager->post_job_user_roles ) && in_array( $slug, $bp_job_manager->post_job_user_roles ) ) ? 'selected' : ''; ?>><?php echo $wp_role['name']; ?></option>
									<?php } ?>
								</select>
							<?php } ?>
							<p class="description"><?php _e( 'Select the user roles that are allowed to post jobs on your site.', BPJM_TEXT_DOMAIN ); ?></p>
						</td>
					</tr>

					<!-- ROLES ALLOWED FOR JOB APPLY -->
					<tr>
						<th scope="row"><label for="wpbpjm-resume-member-types"><?php _e( 'Apply Job Roles', BPJM_TEXT_DOMAIN ); ?></label></th>
						<td>
							<?php if ( isset( $wp_roles->roles ) ) { ?>
								<select multiple required name="bpjm-apply-jobs-user-roles[]" class="bpjm-user-roles">
									<?php foreach ( $wp_roles->roles as $slug => $wp_role ) { ?>
										<option value="<?php echo $slug; ?>" <?php echo ( ! empty( $bp_job_manager->apply_job_user_roles ) && in_array( $slug, $bp_job_manager->apply_job_user_roles ) ) ? 'selected' : ''; ?>><?php echo $wp_role['name']; ?></option>
									<?php } ?>
								</select>
							<?php } ?>
							<p class="description"><?php _e( 'Select the user roles that are allowed to apply for the jobs on your site.', BPJM_TEXT_DOMAIN ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<?php wp_nonce_field( 'bpjm-general', 'bpjm-general-settings-nonce' ); ?>
				<input type="submit" name="bpjm-general-settings-submit" class="button button-primary" value="<?php _e( 'Save Changes', BPJM_TEXT_DOMAIN ); ?>">
			</p>
		</div>
	</div>
</form>
