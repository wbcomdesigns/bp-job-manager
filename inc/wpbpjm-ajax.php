<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Class to serve AJAX Calls
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 */
if( !class_exists( 'Wpbpjm_AJAX' ) ) {
	class Wpbpjm_AJAX {
		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {
			//Save a job
			add_action( 'wp_ajax_wpbpjm_save_job', array( $this, 'wpbpjm_save_job' ) );
			add_action( 'wp_ajax_nopriv_wpbpjm_save_job', array( $this, 'wpbpjm_save_job' ) );

			//Unsave a job
			add_action( 'wp_ajax_wpbpjm_unsave_job', array( $this, 'wpbpjm_unsave_job' ) );
			add_action( 'wp_ajax_nopriv_wpbpjm_unsave_job', array( $this, 'wpbpjm_unsave_job' ) );

			//List applicants per job
			add_action( 'wp_ajax_wpbpjm_list_applicants_per_job', array( $this, 'wpbpjm_list_applicants_per_job' ) );
			add_action( 'wp_ajax_nopriv_wpbpjm_list_applicants_per_job', array( $this, 'wpbpjm_list_applicants_per_job' ) );
		}

		/**
		 * Actions performed for saving a job
		 */
		public function wpbpjm_save_job() {
			if( isset( $_POST['action'] ) && $_POST['action'] === 'wpbpjm_save_job' ) {
        $job_id = sanitize_text_field( $_POST['jobid'] );

				$saved_jobs = get_user_meta( get_current_user_id(), 'my_saved_jobs', true );
				//echo '<pre>'; print_r( $saved_jobs );
				$saved_jobs[] = $job_id;
				//echo '<pre>'; print_r( $saved_jobs ); die;
				update_user_meta( get_current_user_id(), 'my_saved_jobs', $saved_jobs );
				echo 'job-saved-successfully';
        die;
			}
		}

		/**
		 * Actions performed for unsaving a job
		 */
		public function wpbpjm_unsave_job() {
			if( isset( $_POST['action'] ) && $_POST['action'] === 'wpbpjm_unsave_job' ) {
        $job_id = sanitize_text_field( $_POST['jobid'] );

				$saved_jobs = get_user_meta( get_current_user_id(), 'my_saved_jobs', true );
				$job_key = array_search( $job_id, $saved_jobs );
				unset( $saved_jobs[$job_key] );
				update_user_meta( get_current_user_id(), 'my_saved_jobs', $saved_jobs );
				if( empty( $saved_jobs ) ) {
					delete_user_meta( get_current_user_id(), 'my_saved_jobs' );
				}
				echo 'job-unsaved-successfully';
        die;
			}
		}

		/**
		 * Actions performed for listing the applicants per job
		 */
		public function wpbpjm_list_applicants_per_job() {
			if( isset( $_POST['action'] ) && $_POST['action'] === 'wpbpjm_list_applicants_per_job' ) {
        $job_id = sanitize_text_field( $_POST['jobid'] );
				$args = array(
		      'post_type'         => 'job_application',
		      'post_status'       => 'any',
		      'post_parent'       => $job_id,
		      'posts_per_page'    => -1,
		      'orderby'           => 'post_date',
		      'order'             => 'ASC',
		    );
		    $applicants = get_posts( $args );
				$htm = '';
				if( empty( $applicants ) ) {
					$htm .= '<h4>';
					$htm .= __( 'No applicants for this job', WPBPJM_TEXT_DOMAIN );
					$htm .= '</h4>';
				} else {
					foreach( $applicants as $applicant ) {
						$name = $applicant->post_title;
						$content = $applicant->post_content;
						$candidate_id = get_post_meta( $applicant->ID, '_candidate_user_id', true );
						$user_permalink = bp_core_get_user_domain( $candidate_id );

						$avatar_url = bp_core_fetch_avatar(array(
			          'item_id' => $candidate_id,
			          'object' => 'user',
			          'html' => false
			      ));

						$htm .= '<div class="bp-applicant-widget-user-avatar">';
						$htm .= '<a href="'.$user_permalink.'">';
						$htm .= '<img src="'.$avatar_url.'" class="avatar user-3-avatar avatar-50 photo" width="50" height="50" alt="Profile picture of '.$name.'">';
						$htm .= '</a>';
						$htm .= '</div>';
						$htm .= '<div class="bp-applicant-widget-user-links">';
						$htm .= '<div class="bp-applicant-widget-user-link">';
						$htm .= '<a href="'.$user_permalink.'">'.$name.'</a>';
						$htm .= '</div>';
						$htm .= '<div class="bp-applicant-widget-application-details">';
						$htm .= '<p class="applicant-detail"><i>'.$content.'</i></p>';
						$htm .= '</div>';
						$htm .= '</div>';
					}
				}
				echo $htm;
        die;
			}
		}
	}
	new Wpbpjm_AJAX();
}
