<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly
$args            = array(
	'post_type'      => 'job_application',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'orderby'        => 'post_date',
	'order'          => 'ASC',
	'meta_query'     => array(
		array(
			'key'     => '_candidate_user_id',
			'value'   => get_current_user_id(),
			'compare' => '=',
		),
	),
);
$my_applied_jobs = get_posts( $args );
?>
<div id="job-manager-job-dashboard">
	<p><?php _e( 'Your applied jobs are shown in the table below.', BPJM_TEXT_DOMAIN ); ?></p>
	<table class="job-manager-jobs">
		<thead>
			<tr>
				<th><?php _e( 'Applied For', BPJM_TEXT_DOMAIN ); ?></th>
				<th><?php _e( 'Date Applied', BPJM_TEXT_DOMAIN ); ?></th>
				<th><?php _e( 'Attachment', BPJM_TEXT_DOMAIN ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $my_applied_jobs ) ) { ?>
				<tr><td colspan="3"><?php _e( 'You have not applied to any jobs yet.', BPJM_TEXT_DOMAIN ); ?></td></tr>
			<?php } else { ?>
				<?php foreach ( $my_applied_jobs as $applied_job ) { ?>
					<?php
					$job_id           = $applied_job->post_parent;
					$job              = get_post( $job_id );
					$job_permalink    = get_permalink( $job_id );
					$applied_job_meta = get_post_meta( $applied_job->ID );

					if ( ! empty( $applied_job_meta['_attachment'] ) ) {
						$attachment = unserialize( $applied_job_meta['_attachment'][0] )[0];
					}
					?>
					<tr>
						<td class="job_title"><a href="<?php echo $job_permalink; ?>"><?php echo $applied_job_meta['_job_applied_for'][0]; ?></a></td>
						<td><?php echo date( 'F jS, Y', strtotime( $applied_job->post_date ) ); ?></td>
						<td>
							<?php if ( ! empty( $applied_job_meta['_attachment'] ) ) { ?>
								<a href="<?php echo $attachment; ?>" download><?php _e( 'File', BPJM_TEXT_DOMAIN ); ?></a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>
