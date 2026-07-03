<?php
/**
 * BuddyPress Job Manager helper functions.
 *
 * @package Bp_Job_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the WPML translations table exists.
 *
 * The result cannot change mid-request, so it is memoized in a static var
 * to avoid re-running SHOW TABLES on every profile-nav render.
 *
 * @return bool True when the icl_translations table exists.
 */
function bpjm_wpml_translations_table_exists() {
	global $wpdb;
	static $table_exists = null;

	if ( null === $table_exists ) {
		$translations_table = $wpdb->prefix . 'icl_translations';
		$table_exists       = (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- schema probe, memoized per request.
			$wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$translations_table
			)
		);
	}

	return $table_exists;
}

/**
 * Get job count of member, with WPML support if available.
 *
 * Cached in the `user_{ID}_job_listings` transient (keyed per WPML language),
 * invalidated on job/resume status changes via
 * Bp_Job_Manager_Public::flush_job_listings_transient_on_update().
 *
 * @param int $displayed_uid User ID.
 */
function bpjm_member_profile_jobs_count( $displayed_uid ) {
	global $wpdb;

	$displayed_uid = absint( $displayed_uid );

	// Check if WPML is active and get the current language.
	$current_language = defined( 'ICL_LANGUAGE_CODE' ) ? apply_filters( 'wpml_current_language', null ) : null;
	$use_wpml         = $current_language && bpjm_wpml_translations_table_exists();
	$language_key     = $use_wpml ? $current_language : 'default';

	$transient_key = "user_{$displayed_uid}_job_listings";
	$cached_counts = get_transient( $transient_key );

	if ( is_array( $cached_counts ) && isset( $cached_counts[ $language_key ] ) ) {
		return intval( $cached_counts[ $language_key ] );
	}

	// Execute query with proper escaping.
	if ( $use_wpml ) {
		$translations_table = $wpdb->prefix . 'icl_translations';

		$count = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- result cached in transient below.
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} p JOIN " . esc_sql( $translations_table ) . ' t ON p.ID = t.element_id AND t.element_type = %s AND t.language_code = %s WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s',
				'post_job_listing',
				$current_language,
				$displayed_uid,
				'job_listing',
				'publish'
			)
		);
	} else {
		$count = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- result cached in transient below.
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} p WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s",
				$displayed_uid,
				'job_listing',
				'publish'
			)
		);
	}

	$cached_counts                  = is_array( $cached_counts ) ? $cached_counts : array();
	$cached_counts[ $language_key ] = intval( $count );
	set_transient( $transient_key, $cached_counts, HOUR_IN_SECONDS );

	return intval( $count );
}

/**
 * Get resume count of member, with WPML support if available.
 *
 * Cached in the `user_{ID}_my_resumes_count` transient (keyed per WPML
 * language), invalidated on job/resume status changes via
 * Bp_Job_Manager_Public::flush_job_listings_transient_on_update().
 *
 * @param int $displayed_uid User ID.
 */
function bpjm_member_profile_resumes_count( $displayed_uid ) {
	global $wpdb;

	$displayed_uid = absint( $displayed_uid );

	// Check if WPML is active and get the current language.
	$current_language = defined( 'ICL_LANGUAGE_CODE' ) ? apply_filters( 'wpml_current_language', null ) : null;
	$use_wpml         = $current_language && bpjm_wpml_translations_table_exists();
	$language_key     = $use_wpml ? $current_language : 'default';

	$transient_key = "user_{$displayed_uid}_my_resumes_count";
	$cached_counts = get_transient( $transient_key );

	if ( is_array( $cached_counts ) && isset( $cached_counts[ $language_key ] ) ) {
		return intval( $cached_counts[ $language_key ] );
	}

	// Execute query with proper escaping.
	if ( $use_wpml ) {
		$translations_table = $wpdb->prefix . 'icl_translations';

		$count = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- result cached in transient below.
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} p LEFT JOIN " . esc_sql( $translations_table ) . ' t ON p.ID = t.element_id AND t.element_type = %s AND (t.language_code IS NULL OR t.language_code = %s) WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s',
				'post_resume',
				$current_language,
				$displayed_uid,
				'resume',
				'publish'
			)
		);
	} else {
		$count = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- result cached in transient below.
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} p WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s",
				$displayed_uid,
				'resume',
				'publish'
			)
		);
	}

	$cached_counts                  = is_array( $cached_counts ) ? $cached_counts : array();
	$cached_counts[ $language_key ] = intval( $count );
	set_transient( $transient_key, $cached_counts, HOUR_IN_SECONDS );

	return intval( $count );
}
