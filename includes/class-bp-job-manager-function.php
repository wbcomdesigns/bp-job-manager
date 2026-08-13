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
 * Whether a plugin is active on this site, network activation included.
 *
 * `get_option( 'active_plugins' )` only ever lists plugins activated on the
 * CURRENT site. On multisite, a plugin activated network-wide is recorded in
 * the network option `active_sitewide_plugins` - as an associative array keyed
 * by basename, not an indexed list - so a bare in_array() against
 * active_plugins reports every network-activated add-on as inactive on every
 * subsite. That is why Resume Manager / Applications / Bookmarks / Alerts
 * detection used to disagree with the activator, which tests class_exists()
 * and therefore always got it right.
 *
 * Core's own is_plugin_active() does exactly this, but it lives in
 * wp-admin/includes/plugin.php and is not loaded on front-end requests, where
 * most of these checks run. Hence a local helper.
 *
 * The option is read through a static cache because several of these checks
 * fire per profile-nav render.
 *
 * @since 3.0.0
 *
 * @param string $basename Plugin basename, e.g. 'wp-job-manager-resumes/wp-job-manager-resumes.php'.
 * @return bool True when the plugin is active on this site or network-wide.
 */
function bpjm_is_plugin_active( $basename ) {
	static $site_plugins    = null;
	static $network_plugins = null;

	if ( null === $site_plugins ) {
		$site_plugins = (array) get_option( 'active_plugins', array() );
	}

	if ( in_array( $basename, $site_plugins, true ) ) {
		return true;
	}

	if ( ! is_multisite() ) {
		return false;
	}

	if ( null === $network_plugins ) {
		$network_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
	}

	return isset( $network_plugins[ $basename ] );
}

/**
 * Whether a member may post jobs.
 *
 * THE accessor. Whether someone can post was previously decided by five separate
 * array_intersect() calls scattered across nav registration, the screen guard,
 * the admin-bar links and the WP Job Manager filter - each free to drift from
 * the others, which is how a member could be shown a Jobs tab whose screen then
 * refused them.
 *
 * Note on the underlying model: WP Job Manager itself has no role-based posting
 * capability - its `job_manager_user_can_post_job` defaults to true for anyone
 * logged in. The role list is therefore this plugin's own restriction layered on
 * top. Retiring it entirely means every member may post, which is WP Job
 * Manager's native behaviour but a change of product behaviour on every existing
 * site, so it is deliberately NOT done here. This accessor exists so that when
 * the decision is made it is one edit, not five.
 *
 * @since 3.0.0
 *
 * @param int|WP_User|null $user User, ID, or null for the current user.
 * @return bool
 */
function bpjm_user_can_post_job( $user = null ) {
	global $bpjm_bp_job_manager;

	$roles = isset( $bpjm_bp_job_manager->post_job_user_roles )
		? (array) $bpjm_bp_job_manager->post_job_user_roles
		: array();

	if ( null === $user ) {
		$user = wp_get_current_user();
	} elseif ( ! $user instanceof WP_User ) {
		$user = get_userdata( (int) $user );
	}

	if ( ! ( $user instanceof WP_User ) || ! $user->ID ) {
		// "guest" is the pseudo-role that permits logged-out submission.
		return in_array( 'guest', $roles, true );
	}

	return (bool) array_intersect( $roles, (array) $user->roles );
}

/**
 * Whether a member may apply for jobs.
 *
 * Companion to bpjm_user_can_post_job(); same reasoning, same single source.
 *
 * @since 3.0.0
 *
 * @param int|WP_User|null $user User, ID, or null for the current user.
 * @return bool
 */
function bpjm_user_can_apply_job( $user = null ) {
	global $bpjm_bp_job_manager;

	$roles = isset( $bpjm_bp_job_manager->apply_job_user_roles )
		? (array) $bpjm_bp_job_manager->apply_job_user_roles
		: array();

	if ( null === $user ) {
		$user = wp_get_current_user();
	} elseif ( ! $user instanceof WP_User ) {
		$user = get_userdata( (int) $user );
	}

	if ( ! ( $user instanceof WP_User ) || ! $user->ID ) {
		return false;
	}

	return (bool) array_intersect( $roles, (array) $user->roles );
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
