<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $bp_job_manager, $wp_roles;
?>
<form action="" method="POST">
	<div class="wrap">
		<h3><?php esc_attr_e( 'General Settings', 'bp-job-manager' ); ?></h3>
		<div class='wpbpjm-general-settings-container'>
			<table class="form-table">
				<tbody>
					<!-- ROLES ALLOWED FOR JOB POSTING -->
					<tr>
						<th scope="row"><label for="wpbpjm-job-member-types"><?php esc_attr_e( 'Post Job Roles', 'bp-job-manager' ); ?></label></th>
						<td>
							<?php if ( isset( $wp_roles->roles ) ) { ?>
								<select multiple required name="bpjm-post-jobs-user-roles[]" class="bpjm-user-roles">
									<?php foreach ( $wp_roles->roles as $slug => $wp_role ) { ?>
										<option value="<?php echo $slug; ?>" <?php echo ( ! empty( $bp_job_manager->post_job_user_roles ) && in_array( $slug, $bp_job_manager->post_job_user_roles, TRUE ) ) ? 'selected' : ''; ?>><?php echo $wp_role['name']; ?></option>
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
								<select multiple required name="bpjm-apply-jobs-user-roles[]" class="bpjm-user-roles">
									<?php foreach ( $wp_roles->roles as $slug => $wp_role ) { ?>
										<option value="<?php echo $slug; ?>" <?php echo ( ! empty( $bp_job_manager->apply_job_user_roles ) && in_array( $slug, $bp_job_manager->apply_job_user_roles, TRUE ) ) ? 'selected' : ''; ?>><?php echo $wp_role['name']; ?></option>
									<?php } ?>
								</select>
							<?php } ?>
							<p class="description"><?php esc_attr_e( 'Select the user roles that are allowed to apply for the jobs on your site.', 'bp-job-manager' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<?php wp_nonce_field( 'bpjm-general', 'bpjm-general-settings-nonce' ); ?>
				<input type="submit" name="bpjm-general-settings-submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'bp-job-manager' ); ?>">
			</p>
		</div>
	</div>
</form>
