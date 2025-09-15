<?php
/**
 * Get job count of member, with WPML support if available.
 */
function bpjm_member_profile_jobs_count( $displayed_uid ) {
    global $wpdb;

    // Check if WPML is active and get the current language
    $current_language = defined( 'ICL_LANGUAGE_CODE' ) ? apply_filters( 'wpml_current_language', null ) : null;

    // Check if the WPML translations table exists
    $translations_table = $wpdb->prefix . 'icl_translations';
    $table_exists = $wpdb->get_var( $wpdb->prepare(
        "SHOW TABLES LIKE %s", $translations_table
    ) );

    // Execute query with proper escaping
    if ( $current_language && $table_exists ) {
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p JOIN {$translations_table} t ON p.ID = t.element_id AND t.element_type = %s AND t.language_code = %s WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s",
            'post_job_listing',
            $current_language,
            $displayed_uid,
            'job_listing',
            'publish'
        ) );
    } else {
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s",
            $displayed_uid,
            'job_listing',
            'publish'
        ) );
    }

    return intval( $count );
}

/**
 * Get resume count of member, with WPML support if available.
 */
function bpjm_member_profile_resumes_count( $displayed_uid ) {
    global $wpdb;

    // Check if WPML is active and get the current language
    $current_language = defined( 'ICL_LANGUAGE_CODE' ) ? apply_filters( 'wpml_current_language', null ) : null;

    // Check if the WPML translations table exists
    $translations_table = $wpdb->prefix . 'icl_translations';
    $table_exists = $wpdb->get_var( $wpdb->prepare(
        "SHOW TABLES LIKE %s", $translations_table
    ) );

    // Execute query with proper escaping
    if ( $current_language && $table_exists ) {
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p LEFT JOIN {$translations_table} t ON p.ID = t.element_id AND t.element_type = %s AND (t.language_code IS NULL OR t.language_code = %s) WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s",
            'post_resume',
            $current_language,
            $displayed_uid,
            'resume',
            'publish'
        ) );
    } else {
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = %s",
            $displayed_uid,
            'resume',
            'publish'
        ) );
    }

    return intval( $count );
}
