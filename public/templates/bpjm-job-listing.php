<?php
/**
 * Job dashboard shortcode content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$bpjm_job_dashboard_columns = array(
	'job_title' => __( 'Title', 'bp-job-manager' ),
	'filled'    => __( 'Filled?', 'bp-job-manager' ),
	'date'      => __( 'Date Posted', 'bp-job-manager' ),
	'expires'   => __( 'Listing Expires', 'bp-job-manager' ),
	'actions'   => __( 'Actions', 'bp-job-manager' ),
);

?>
<div id="job-manager-job-dashboard">
	<table class="job-manager-jobs job-dashboard-table">
		<thead class="job-dashboard-header">
			<tr>
				<?php foreach ( $bpjm_job_dashboard_columns as $bpjm_key => $bpjm_column ) : ?>
					<th class="job-dashboard-job-column-label <?php echo esc_attr( $bpjm_key ); ?>"><?php echo esc_html( $bpjm_column ); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $jobs ) ) : ?>
				<tr>
					<td colspan="<?php echo intval( count( $bpjm_job_dashboard_columns ) ); ?>"><?php esc_html_e( 'No jobs found.', 'bp-job-manager' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $jobs as $bpjm_job ) : ?>
					<tr>
						<?php foreach ( $bpjm_job_dashboard_columns as $bpjm_key => $bpjm_column ) : ?>
							<td class="<?php echo esc_attr( $bpjm_key ); ?>">
								<?php if ( 'job_title' === $bpjm_key ) : ?>
									<?php if ( $bpjm_job->post_status == 'publish' ) : ?>
										<a href="<?php echo esc_url( get_permalink( $bpjm_job->ID ) ); ?>"><?php wpjm_the_job_title( $bpjm_job ); ?></a>
									<?php else : ?>
										<?php wpjm_the_job_title( $bpjm_job ); ?> <small>(<?php the_job_status( $bpjm_job ); ?>)</small>
									<?php endif; ?>
									<?php echo is_position_featured( $bpjm_job ) ? '<span class="featured-job-icon" title="' . esc_attr__( 'Featured Job', 'bp-job-manager' ) . '"></span>' : ''; ?>

								<?php elseif ( 'date' === $bpjm_key ) : ?>
									<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $bpjm_job->post_date ) ) ); ?>
								<?php elseif ( 'expires' === $bpjm_key ) : ?>
									<?php echo esc_html( $bpjm_job->_job_expires ? date_i18n( get_option( 'date_format' ), strtotime( $bpjm_job->_job_expires ) ) : '&ndash;' ); ?>
								<?php elseif ( 'filled' === $bpjm_key ) : ?>
									<?php echo is_position_filled( $bpjm_job ) ? '&#10004;' : '&ndash;'; ?>
								<?php else : ?>
									<?php do_action( 'bpjm_job_manager_job_dashboard_column_' . $bpjm_key, $bpjm_job, '' ); ?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>