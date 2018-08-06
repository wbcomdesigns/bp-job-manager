<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-job-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	<p><?php esc_html_e( 'Your applied jobs are shown in the table below.', 'bp-job-manager' ); ?></p>
	<table class="job-manager-jobs">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Applied For', 'bp-job-manager' ); ?></th>
				<th><?php esc_html_e( 'Applied Date', 'bp-job-manager' ); ?></th>
				<th><?php esc_html_e( 'Attachment', 'bp-job-manager' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $my_applied_jobs ) ) { ?>
				<tr><td colspan="3"><?php esc_html_e( 'You have not applied to any jobs yet.', 'bp-job-manager' ); ?></td></tr>
			<?php } else { ?>
				<?php foreach ( $my_applied_jobs as $applied_job ) { ?>
					<?php
					$job_id           = $applied_job->post_parent;
					$job              = get_post( $job_id );
					$job_permalink    = get_permalink( $job_id );
					$applied_job_meta = get_post_meta( $applied_job->ID );

					if ( ! empty( $applied_job_meta['_attachment'] ) ) {
						$attach_unserialized = unserialize( $applied_job_meta['_attachment'][0] );						
						if( $attach_unserialized ) {
							$attachment = unserialize( $applied_job_meta['_attachment'][0] )[0];
						}
					}
					?>
					<tr>
						<td class="job_title"><a href="<?php echo esc_attr( $job_permalink ); ?>"><?php echo esc_html( $applied_job_meta['_job_applied_for'][0], 'bp-job-manager' ); ?></a></td>
						<td><?php echo esc_html( date( 'F jS, Y', strtotime( $applied_job->post_date ) ), 'bp-job-manager' ); ?></td>
						<td>
							<?php if ( ! empty( $applied_job_meta['_attachment'] ) ) { ?>
								<a href="<?php echo esc_attr( $attachment ); ?>" download><?php esc_html_e( 'File', 'bp-job-manager' ); ?></a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>
