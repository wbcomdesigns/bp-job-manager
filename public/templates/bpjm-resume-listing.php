<?php
/**
 * Template for the candidate resume listing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bpjm_submission_limit           = get_option( 'resume_manager_submission_limit' );
$bpjm_submit_resume_form_page_id = get_option( 'resume_manager_submit_resume_form_page_id' );
?>
<?php if ( is_user_logged_in() && ( bp_loggedin_user_id() == bp_displayed_user_id() ) ) : ?>
	<?php if ( $bpjm_submit_resume_form_page_id && ( resume_manager_count_user_resumes() < $bpjm_submission_limit || ! $bpjm_submission_limit ) ) : ?>
		<div class="resume-dashboard__actions">
			<a class="button" href="<?php echo esc_url( get_permalink( $bpjm_submit_resume_form_page_id ) ); ?>"><?php esc_html_e( 'Add Resume', 'bp-job-manager' ); ?></a>
		</div>
	<?php endif; ?>
<?php endif; ?>

<div id="resume-manager-candidate-dashboard">
	<table class="resume-manager-resumes resume-dashboard-table">
		<thead class="resume-dashboard-header">
			<tr>
				<?php if ( ! empty( $candidate_dashboard_columns ) ) : ?>
					<?php foreach ( $candidate_dashboard_columns as $bpjm_key => $bpjm_column ) : ?>
						<th class="resume-dashboard-resume-column-label <?php echo esc_attr( $bpjm_key ); ?>"><?php echo esc_html( $bpjm_column ); ?></th>
					<?php endforeach; ?>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! $resumes ) : ?>
				<tr>
					<td colspan="<?php echo esc_attr( sizeof( $candidate_dashboard_columns ) ); ?>"><?php esc_html_e( 'You do not have any active resume listings.', 'bp-job-manager' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $resumes as $bpjm_resume ) : ?>
					<tr>
						<?php if ( ! empty( $candidate_dashboard_columns ) ) : ?>
							<?php foreach ( $candidate_dashboard_columns as $bpjm_key => $bpjm_column ) : ?>
								<td class="<?php echo esc_attr( $bpjm_key ); ?>">
									<?php if ( 'resume-title' === $bpjm_key ) : ?>
										<?php if ( 'publish' === $bpjm_resume->post_status ) : ?>
											<a href="<?php echo esc_url( get_permalink( $bpjm_resume->ID ) ); ?>"><?php echo esc_html( $bpjm_resume->post_title ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $bpjm_resume->post_title ); ?> <small>(<?php the_resume_status( $bpjm_resume ); ?>)</small>
										<?php endif; ?>
										<ul class="candidate-dashboard-actions">
											<?php
												$bpjm_actions = array();

											switch ( $bpjm_resume->post_status ) {
												case 'publish':
													if ( resume_manager_user_can_edit_published_submissions() ) {
														$bpjm_actions['edit'] = array(
															'label' => __( 'Edit', 'bp-job-manager' ),
															'nonce' => false,
														);
													}
													$bpjm_actions['hide'] = array(
														'label' => __( 'Hide', 'bp-job-manager' ),
														'nonce' => true,
													);
													break;
												case 'hidden':
													if ( resume_manager_user_can_edit_published_submissions() ) {
														$bpjm_actions['edit'] = array(
															'label' => __( 'Edit', 'bp-job-manager' ),
															'nonce' => false,
														);
													}
													$bpjm_actions['publish'] = array(
														'label' => __( 'Publish', 'bp-job-manager' ),
														'nonce' => true,
													);
													break;
												case 'pending_payment':
												case 'pending':
													if ( resume_manager_user_can_edit_pending_submissions() ) {
														$bpjm_actions['edit'] = array(
															'label' => __( 'Edit', 'bp-job-manager' ),
															'nonce' => false,
														);
													}
													break;
												case 'expired':
													if ( get_option( 'resume_manager_submit_resume_form_page_id' ) ) {
														$bpjm_actions['relist'] = array(
															'label' => __( 'Relist', 'bp-job-manager' ),
															'nonce' => true,
														);
													}
													break;
											}

												$bpjm_actions['delete'] = array(
													'label' => __( 'Delete', 'bp-job-manager' ),
													'nonce' => true,
												);

												$bpjm_actions = apply_filters( 'resume_manager_my_resume_actions', $bpjm_actions, $bpjm_resume ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook from Resume Manager

												foreach ( $bpjm_actions as $bpjm_action => $bpjm_value ) {
													if ( 'edit' === $bpjm_action ) {
														$bpjm_candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
														if ( $bpjm_candidate_dashboard_page_id ) {
															$bpjm_redirect_url = get_permalink( $bpjm_candidate_dashboard_page_id );

														} else {
															$bpjm_redirect_url = home_url( 'candidate-dashboard' );
														}
														$bpjm_action_url = add_query_arg(
															array(
																'action' => $bpjm_action,
																'resume_id' => $bpjm_resume->ID,
															),
															$bpjm_redirect_url
														);
													} elseif ( 'relist' === $bpjm_action ) {
														$bpjm_candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' );
														if ( $bpjm_candidate_dashboard_page_id ) {
															$bpjm_redirect_url = get_permalink( $bpjm_candidate_dashboard_page_id );

														} else {
															$bpjm_redirect_url = home_url( 'candidate-dashboard' );
														}
														$bpjm_action_url = add_query_arg(
															array(
																'action' => $bpjm_action,
																'resume_id' => $bpjm_resume->ID,
															),
															$bpjm_redirect_url
														);
													} else {

														$bpjm_action_url = add_query_arg(
															array(
																'action' => $bpjm_action,
																'resume_id' => $bpjm_resume->ID,
															)
														);

													}


													if ( $bpjm_value['nonce'] ) {
														$bpjm_action_url = wp_nonce_url( $bpjm_action_url, 'resume_manager_my_resume_actions' );
													}

													echo '<li><a href="' . esc_url( $bpjm_action_url ) . '" class="candidate-dashboard-action-' . esc_attr( $bpjm_action ) . '">' . esc_html( $bpjm_value['label'] ) . '</a></li>';
												}
												?>
										</ul>
									<?php elseif ( 'candidate-title' === $bpjm_key ) : ?>
										<?php the_candidate_title( '', '', true, $bpjm_resume ); ?>
									<?php elseif ( 'candidate-location' === $bpjm_key ) : ?>
										<?php the_candidate_location( false, $bpjm_resume ); ?></td>
									<?php elseif ( 'resume-category' === $bpjm_key ) : ?>
										<?php the_resume_category( $bpjm_resume ); ?>
									<?php elseif ( 'status' === $bpjm_key ) : ?>
										<?php the_resume_status( $bpjm_resume ); ?>
									<?php elseif ( 'date' === $bpjm_key ) : ?>
										<?php
										if ( ! empty( $bpjm_resume->_resume_expires ) && strtotime( $bpjm_resume->_resume_expires ) > current_time( 'timestamp' ) ) {
											/* translators: %s: Expire Date*/
											printf( esc_html__( 'Expires %s', 'bp-job-manager' ), esc_html( wp_date( get_option( 'date_format' ), strtotime( $bpjm_resume->_resume_expires ) ) ) );
										} else {
											echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $bpjm_resume->post_date ) ) );
										}
										?>
									<?php else : ?>
										<?php do_action( 'resume_manager_candidate_dashboard_column_' . $bpjm_key, $bpjm_resume ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook from Resume Manager ?>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php do_action( 'resume_manager_candidate_dashboard_actions', $resumes ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook from Resume Manager ?>
</div>