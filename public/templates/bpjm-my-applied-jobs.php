<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-job-manager
 */

if (!defined('ABSPATH')) {
	exit;
}

$bpjm_args            = array(
	'post_type'      => 'job_application',
	'post_status'    => 'any',
	'posts_per_page' => 50, // Limit to 50 posts per page for better performance
	'orderby'        => 'post_date',
	'order'          => 'ASC',
	'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		array(
			'key'     => '_candidate_user_id',
			'value'   => get_current_user_id(),
			'compare' => '=',
			'type'    => 'NUMERIC',
		),
	),
);
$bpjm_my_applied_jobs = get_posts($bpjm_args);
?>
<div id="job-manager-job-dashboard">
	<p><?php esc_html_e('Your applied jobs are shown in the table below.', 'bp-job-manager'); ?></p>
	<table class="job-manager-jobs">
		<thead>
			<tr>
				<th><?php esc_html_e('Applied For', 'bp-job-manager'); ?></th>
				<th><?php esc_html_e('Applied Date', 'bp-job-manager'); ?></th>
				<th><?php esc_html_e('Attachment', 'bp-job-manager'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($bpjm_my_applied_jobs)) { ?>
				<tr>
					<td colspan="3"><?php esc_html_e('You have not applied to any jobs yet.', 'bp-job-manager'); ?></td>
				</tr>
			<?php } else { ?>
				<?php foreach ($bpjm_my_applied_jobs as $bpjm_applied_job) { ?>
					<?php
					$bpjm_job_id           = $bpjm_applied_job->post_parent;
					$bpjm_job              = get_post($bpjm_job_id);
					$bpjm_job_permalink    = get_permalink($bpjm_job_id);
					$bpjm_applied_job_meta = get_post_meta($bpjm_applied_job->ID);
					$bpjm_attachment       = '';

					if (!empty($bpjm_applied_job_meta['_attachment'][0])) {
						$bpjm_attach_unserialized = maybe_unserialize($bpjm_applied_job_meta['_attachment'][0]);

						if (!empty($bpjm_attach_unserialized) && is_array($bpjm_attach_unserialized)) {
							$bpjm_attachment = $bpjm_attach_unserialized[0];
						}
					}
					?>
					<tr>
						<td class="job_title">
							<a href="<?php echo esc_url($bpjm_job_permalink); ?>">
								<?php echo isset($bpjm_applied_job_meta['_job_applied_for'][0]) ? esc_html($bpjm_applied_job_meta['_job_applied_for'][0]) : esc_html__('Unknown Job', 'bp-job-manager'); ?>
							</a>
						</td>
						<td><?php echo esc_html(gmdate('F jS, Y', strtotime($bpjm_applied_job->post_date)), 'bp-job-manager'); ?></td>
						<td>
							<?php if (!empty($bpjm_attachment)) { ?>
								<a href="<?php echo esc_url($bpjm_attachment); ?>" download><?php esc_html_e('File', 'bp-job-manager'); ?></a>
							<?php
							} else {
								esc_html_e('No Attachment', 'bp-job-manager');
							}
							?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>