<?php


function bp_job_manager_get_total_published_posts( $user_id ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	// Needs revisit.
	$transient_key = 'user_' . $user_id . '_job_listings';
	$job_listings = get_transient($transient_key);

	if ($job_listings === 0) {
		$args = [
			'post_type'      => 'job_listing',
			'post_status'    => 'publish',
			'author'         => $user_id,
			'posts_per_page' => -1,
			'orderby'        => 'post_date',
			'order'          => 'ASC',
			'fields'         => 'ids'
		];

		$query = new WP_Query($args);
		$job_listings = $query->found_posts;
		set_transient($transient_key, $job_listings, HOUR_IN_SECONDS);
	}

	return $job_listings;
}
