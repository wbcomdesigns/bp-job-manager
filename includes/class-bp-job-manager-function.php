<?php
/**
 * Get job count of member.
 */
function bpjm_member_profile_jobs_count( $displayed_uid ) {
	global $wpdb;
	$count = $wpdb->get_var( $wpdb->prepare( "SELECT count('*') FROM {$wpdb->posts} WHERE  post_author=%d AND post_type=%s AND post_status='publish'", bp_displayed_user_id(), 'job_listing' ) );
	return intval( $count );
}

/**
 * Get resume count of member.
 */
function bpjm_member_profile_resumes_count( $displayed_uid ) {
	global $wpdb;
	$count = $wpdb->get_var( $wpdb->prepare( "SELECT count('*') FROM {$wpdb->posts} WHERE  post_author=%d AND post_type=%s AND post_status='publish'", bp_displayed_user_id(), 'resume' ) );
	return intval( $count );
}
