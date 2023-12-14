<?php


function bp_job_manager_get_total_published_posts( $user_id = 0 ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	// Needs revisit.
	global $wpdb;

	$count = $wpdb->get_var( $wpdb->prepare( "SELECT count('*') FROM {$wpdb->posts} WHERE  post_author=%d AND post_type=%s AND post_status='publish'", $user_id, 'job_listing' ) );

	return intval( $count );
}
