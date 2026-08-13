<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * This plugin is a bridge: the jobs, resumes and applications on the site belong
 * to WP Job Manager and its add-ons, and BuddyPress owns the activity and the
 * notifications. None of that is touched here. What this removes is the handful
 * of rows the plugin created for itself, so uninstalling leaves nothing behind.
 *
 * @link       https://wbcomdesigns.com/
 * @since      3.0.0
 *
 * @package    Bp_Job_Manager
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Remove this plugin's own rows from the current site.
 *
 * @return void
 */
function bpjm_uninstall_current_site() {
	global $wpdb;

	// The settings, plus the review notice's two flags.
	delete_option( 'bpjm_general_settings' );
	delete_option( 'bp-job-manager_activation_date' );
	delete_option( 'bp-job-manager_no_bug' );

	/*
	 * The id of the "Apply To Job" page that versions before 3.0.0 created.
	 * Only the pointer goes; the page itself is the site owner's content now
	 * and deleting it here would remove something they may have edited or
	 * linked. It is theirs to keep or bin.
	 */
	delete_option( 'bpjm_job_application_pgid' );

	// The per-member profile-nav count caches.
	$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall cleanup; no cache to prime and no API for a LIKE delete.
		"DELETE FROM {$wpdb->options}
		 WHERE option_name LIKE '\_transient\_bpjm\_user\_%'
		    OR option_name LIKE '\_transient\_timeout\_bpjm\_user\_%'"
	);
}

if ( is_multisite() ) {
	// Network activation puts a copy of every option on every site.
	$bpjm_site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $bpjm_site_ids as $bpjm_site_id ) {
		switch_to_blog( $bpjm_site_id );
		bpjm_uninstall_current_site();
		restore_current_blog();
	}

	delete_site_option( 'bp-job-manager_activation_date' );
	delete_site_option( 'bp-job-manager_no_bug' );
} else {
	bpjm_uninstall_current_site();
}
