<?php
/**
 * Template for the candidate resume listing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$submission_limit           = get_option( 'resume_manager_submission_limit' );
$submit_resume_form_page_id = get_option( 'resume_manager_submit_resume_form_page_id' );
?>
<div id="resume-manager-candidate-dashboard">
	<table class="resume-manager-resumes">
		<thead>
			<tr>
				<?php foreach ( $candidate_dashboard_columns as $key => $column ) : ?>
					<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $column ); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! $resumes ) : ?>
				<tr>
					<td colspan="<?php echo sizeof( $candidate_dashboard_columns ); ?>"><?php _e( 'You do not have any active resume listings.', 'wp-job-manager-resumes' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $resumes as $resume ) : ?>
					<tr>
						<?php foreach ( $candidate_dashboard_columns as $key => $column ) : ?>
							<td class="<?php echo esc_attr( $key ); ?>">
								<?php if ( 'resume-title' === $key ) : ?>
									<?php if ( $resume->post_status == 'publish' ) : ?>
										<a href="<?php echo get_permalink( $resume->ID ); ?>"><?php echo esc_html( $resume->post_title ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $resume->post_title ); ?> <small>(<?php the_resume_status( $resume ); ?>)</small>
									<?php endif; ?>
								<?php elseif ( 'candidate-title' === $key ) : ?>
									<?php the_candidate_title( '', '', true, $resume ); ?>
								<?php elseif ( 'candidate-location' === $key ) : ?>
									<?php the_candidate_location( false, $resume ); ?></td>
								<?php elseif ( 'resume-category' === $key ) : ?>
									<?php the_resume_category( $resume ); ?>
								<?php elseif ( 'status' === $key ) : ?>
									<?php the_resume_status( $resume ); ?>
								<?php elseif ( 'date' === $key ) : ?>
									<?php
									if ( ! empty( $resume->_resume_expires ) && strtotime( $resume->_resume_expires ) > current_time( 'timestamp' ) ) {
										printf( __( 'Expires %s', 'wp-job-manager-resumes' ), date_i18n( get_option( 'date_format' ), strtotime( $resume->_resume_expires ) ) );
									} else {
										echo date_i18n( get_option( 'date_format' ), strtotime( $resume->post_date ) );
									}
									?>
								<?php else : ?>
									<?php do_action( 'bpjm_candidate_dashboard_column_' . $key, $resume ); ?>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	<?php if ( is_user_logged_in() && ( bp_loggedin_user_id() == bp_displayed_user_id() ) ) : ?>
		<?php if ( $submit_resume_form_page_id && ( resume_manager_count_user_resumes() < $submission_limit || ! $submission_limit ) ) : ?>
			<tfoot>
				<tr>
					<td colspan="<?php echo sizeof( $candidate_dashboard_columns ); ?>">
						<a href="<?php echo esc_url( get_permalink( $submit_resume_form_page_id ) ); ?>"><?php _e( 'Add Resume', 'wp-job-manager-resumes' ); ?></a>
					</td>
				</tr>
			</tfoot>
		<?php endif; ?>
	<?php endif; ?>
	</table>
</div>
