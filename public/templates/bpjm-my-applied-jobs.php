<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-job-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Read-only pagination parameter - no state change, nonce not required.
$bpjm_applied_paged    = isset( $_GET['bpjm-applied-page'] ) ? max( 1, absint( $_GET['bpjm-applied-page'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$bpjm_applied_per_page = (int) apply_filters( 'bpjm_applied_jobs_per_page', 20 );

$bpjm_applied_query = new WP_Query(
	array(
		'post_type'      => 'job_application',
		'post_status'    => 'any',
		'posts_per_page' => $bpjm_applied_per_page,
		'paged'          => $bpjm_applied_paged,
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => '_candidate_user_id',
				'value'   => get_current_user_id(),
				'compare' => '=',
				'type'    => 'NUMERIC',
			),
		),
	)
);

$bpjm_my_applied_jobs     = $bpjm_applied_query->posts;
$bpjm_applied_total       = (int) $bpjm_applied_query->found_posts;
$bpjm_applied_total_pages = (int) $bpjm_applied_query->max_num_pages;

// Batch-prime the parent job posts (post + meta caches) so get_permalink()
// inside the loop is served from cache instead of one query per row.
$bpjm_parent_job_ids = array_filter( array_map( 'absint', array_unique( wp_list_pluck( $bpjm_my_applied_jobs, 'post_parent' ) ) ) );
if ( ! empty( $bpjm_parent_job_ids ) ) {
	_prime_post_caches( $bpjm_parent_job_ids, false, true );
}
?>
<div id="job-manager-job-dashboard">
	<p>
	<?php
	printf(
		/* translators: %s: total number of job applications. */
		esc_html( _n( 'You have applied to %s job. Your applications are shown in the table below.', 'You have applied to %s jobs. Your applications are shown in the table below.', $bpjm_applied_total, 'bp-job-manager' ) ),
		esc_html( number_format_i18n( $bpjm_applied_total ) )
	);
	?>
	</p>
	<table class="job-manager-jobs">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Applied For', 'bp-job-manager' ); ?></th>
				<th><?php esc_html_e( 'Applied Date', 'bp-job-manager' ); ?></th>
				<th><?php esc_html_e( 'Attachment', 'bp-job-manager' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $bpjm_my_applied_jobs ) ) { ?>
				<tr>
					<td colspan="3"><?php esc_html_e( 'You have not applied to any jobs yet.', 'bp-job-manager' ); ?></td>
				</tr>
			<?php } else { ?>
				<?php foreach ( $bpjm_my_applied_jobs as $bpjm_applied_job ) { ?>
					<?php
					$bpjm_job_id           = $bpjm_applied_job->post_parent;
					$bpjm_job_permalink    = get_permalink( $bpjm_job_id );
					$bpjm_applied_job_meta = get_post_meta( $bpjm_applied_job->ID );
					$bpjm_attachment       = '';

					if ( ! empty( $bpjm_applied_job_meta['_attachment'][0] ) ) {
						$bpjm_attach_unserialized = maybe_unserialize( $bpjm_applied_job_meta['_attachment'][0] );

						if ( ! empty( $bpjm_attach_unserialized ) && is_array( $bpjm_attach_unserialized ) ) {
							$bpjm_attachment = $bpjm_attach_unserialized[0];
						}
					}
					?>
					<tr>
						<td class="job_title">
							<a href="<?php echo esc_url( $bpjm_job_permalink ); ?>">
								<?php echo isset( $bpjm_applied_job_meta['_job_applied_for'][0] ) ? esc_html( $bpjm_applied_job_meta['_job_applied_for'][0] ) : esc_html__( 'Unknown Job', 'bp-job-manager' ); ?>
							</a>
						</td>
						<td><?php echo esc_html( gmdate( 'F jS, Y', strtotime( $bpjm_applied_job->post_date ) ) ); ?></td>
						<td>
							<?php if ( ! empty( $bpjm_attachment ) ) { ?>
								<a href="<?php echo esc_url( $bpjm_attachment ); ?>" download><?php esc_html_e( 'File', 'bp-job-manager' ); ?></a>
								<?php
							} else {
								esc_html_e( 'No Attachment', 'bp-job-manager' );
							}
							?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	<?php if ( $bpjm_applied_total_pages > 1 ) { ?>
		<nav class="bpjm-applied-jobs-pagination job-manager-pagination" aria-label="<?php esc_attr_e( 'Applied jobs pagination', 'bp-job-manager' ); ?>">
			<?php
			echo wp_kses_post(
				paginate_links(
					array(
						'base'    => add_query_arg( 'bpjm-applied-page', '%#%' ),
						'format'  => '',
						'current' => $bpjm_applied_paged,
						'total'   => $bpjm_applied_total_pages,
					)
				)
			);
			?>
		</nav>
	<?php } ?>
</div>