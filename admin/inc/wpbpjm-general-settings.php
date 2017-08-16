<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bp_job_manager, $wp_roles;
//echo '<pre>'; print_r( $bp_job_manager ); die;
?>
<div class="wrap">
	<h3><?php _e( 'General Settings', WPBPJM_TEXT_DOMAIN ); ?></h3>
	<div class='wpbpjm-general-settings-container'>
		<table class="form-table">
			<tbody>
				<!-- ROLES ALLOWED FOR JOB MGMT -->
				<tr>
					<th scope="row"><label for="wpbpjm-job-member-types"><?php _e( 'Roles for Job Management', WPBPJM_TEXT_DOMAIN );?></label></th>
					<td>
						<?php foreach( $wp_roles->roles as $slug => $wp_role ) {?>
							<input id="job-role-<?php echo $slug;?>" type="checkbox" name="wpbpjm-job-user-roles[]" value="<?php echo $slug;?>" <?php echo ( !empty( $bp_job_manager->job_user_roles ) && in_array( $slug, $bp_job_manager->job_user_roles ) ) ? 'checked' : '';?>/>
							<label for="job-role-<?php echo $slug;?>"><?php echo $wp_role['name'];?></label><br />
						<?php }?>
						<p class="description"><?php _e( 'Here you can select the roles that can manage the <strong>Job</strong> section.', WPBPJM_TEXT_DOMAIN );?></p>
					</td>
				</tr>

				<!-- ROLES ALLOWED FOR RESUME MGMT -->
				<tr>
					<th scope="row"><label for="wpbpjm-resume-member-types"><?php _e( 'Roles for Resume Management', WPBPJM_TEXT_DOMAIN );?></label></th>
					<td>
						<?php foreach( $wp_roles->roles as $slug => $wp_role ) {?>
							<input id="resume-role-<?php echo $slug;?>" type="checkbox" name="wpbpjm-resume-user-roles[]" value="<?php echo $slug;?>" <?php echo ( !empty( $bp_job_manager->resume_user_roles ) && in_array( $slug, $bp_job_manager->resume_user_roles ) ) ? 'checked' : '';?>/>
							<label for="resume-role-<?php echo $slug;?>"><?php echo $wp_role['name'];?></label><br />
						<?php }?>
						<p class="description"><?php _e( 'Here you can select the roles that can manage the <strong>Resume</strong> section.', WPBPJM_TEXT_DOMAIN );?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="wpbpjm-general-settings-submit" class="button button-primary" value="<?php _e('Save Changes', WPBPJM_TEXT_DOMAIN); ?>"></p>
	</div>
</div>