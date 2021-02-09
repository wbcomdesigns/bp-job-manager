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
					<td colspan="<?php echo esc_attr( sizeof( $candidate_dashboard_columns ) ); ?>"><?php esc_html_e( 'You do not have any active resume listings.', 'wp-job-manager-resumes' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $resumes as $resume ) : ?>
					<tr>
						<?php foreach ( $candidate_dashboard_columns as $key => $column ) : ?>
							<td class="<?php echo esc_attr( $key ); ?>">
								<?php if ( 'resume-title' === $key ) : ?>
									<?php if ( $resume->post_status == 'publish' ) : ?>
										<a href="<?php echo esc_url( get_permalink( $resume->ID ) ); ?>"><?php echo esc_html( $resume->post_title ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $resume->post_title ); ?> <small>(<?php the_resume_status( $resume ); ?>)</small>
									<?php endif; ?>
									<ul class="candidate-dashboard-actions">
										<?php
											$actions = array();

										switch ( $resume->post_status ) {
											case 'publish':
												if ( resume_manager_user_can_edit_published_submissions() ) {
													$actions['edit'] = array(
														'label' => __( 'Edit', 'bp-job-manager' ),
														'nonce' => false,
													);
												}
												$actions['hide'] = array(
													'label' => __( 'Hide', 'bp-job-manager' ),
													'nonce' => true,
												);
												break;
											case 'hidden':
												if ( resume_manager_user_can_edit_published_submissions() ) {
													$actions['edit'] = array(
														'label' => __( 'Edit', 'bp-job-manager' ),
														'nonce' => false,
													);
												}
												$actions['publish'] = array(
													'label' => __( 'Publish', 'bp-job-manager' ),
													'nonce' => true,
												);
												break;
											case 'pending_payment':
											case 'pending':
												if ( resume_manager_user_can_edit_pending_submissions() ) {
													$actions['edit'] = array(
														'label' => __( 'Edit', 'bp-job-manager' ),
														'nonce' => false,
													);
												}
												break;
											case 'expired':
												if ( get_option( 'resume_manager_submit_resume_form_page_id' ) ) {
													$actions['relist'] = array(
														'label' => __( 'Relist', 'bp-job-manager' ),
														'nonce' => true,
													);
												}
												break;
										}

											$actions['delete'] = array(
												'label' => __( 'Delete', 'bp-job-manager' ),
												'nonce' => true,
											);

											$actions = apply_filters( 'resume_manager_my_resume_actions', $actions, $resume );

											foreach ( $actions as $action => $value ) {
												if ( 'edit' === $action ) {
													$candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
													if ( $candidate_dashboard_page_id ) {
														$redirect_url = get_permalink( $candidate_dashboard_page_id );

													} else {
														$redirect_url = home_url( 'candidate-dashboard' );
													}
													$action_url = add_query_arg(
														array(
															'action' => $action,
															'resume_id' => $resume->ID,
														),
														$redirect_url
													);
												} else {
													$action_url = add_query_arg(
														array(
															'action' => $action,
															'resume_id' => $resume->ID,
														)
													);
												}


												if ( $value['nonce'] ) {
													$action_url = wp_nonce_url( $action_url, 'resume_manager_my_resume_actions' );
												}

												echo '<li><a href="' . $action_url . '" class="candidate-dashboard-action-' . $action . '">' . $value['label'] . '</a></li>';
											}
											?>
									</ul>
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
										printf( esc_html__( 'Expires %s', 'wp-job-manager-resumes' ), esc_html( date_i18n( get_option( 'date_format' ) ), strtotime( $resume->_resume_expires ) ) );
									} else {
										echo esc_html( date_i18n( get_option( 'date_format' ) ), strtotime( $resume->post_date ) );
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
					<td colspan="<?php echo esc_attr( sizeof( $candidate_dashboard_columns ) ); ?>">
						<a href="<?php echo esc_url( get_permalink( $submit_resume_form_page_id ) ); ?>"><?php esc_html_e( 'Add Resume', 'wp-job-manager-resumes' ); ?></a>
					</td>
				</tr>
			</tfoot>
		<?php endif; ?>
	<?php endif; ?>
	</table>
</div>
