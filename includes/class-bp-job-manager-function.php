<?php
/**
 * Get job count of member, with WPML support if available.
 */
function bpjm_member_profile_jobs_count( $displayed_uid ) {
    global $wpdb;

    // Check if WPML is active and get the current language
    $current_language = defined( 'ICL_LANGUAGE_CODE' ) ? apply_filters( 'wpml_current_language', null ) : null;

    // Base query
    $query = "SELECT COUNT(*) FROM {$wpdb->posts} p";

    // If WPML is active, add a join to filter by language
    if ( $current_language ) {
        $query .= " JOIN {$wpdb->prefix}icl_translations t ON p.ID = t.element_id AND t.element_type = 'post_job_listing' AND t.language_code = %s";
    }
    
    $query .= " WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = 'publish'";

    // Prepare and execute the query, conditionally including the language
    $count = $current_language
        ? $wpdb->get_var( $wpdb->prepare( $query, $current_language, $displayed_uid, 'job_listing' ) )
        : $wpdb->get_var( $wpdb->prepare( $query, $displayed_uid, 'job_listing' ) );

    return intval( $count );
}

/**
 * Get resume count of member, with WPML support if available.
 */
function bpjm_member_profile_resumes_count( $displayed_uid ) {
    global $wpdb;

    // Check if WPML is active and get the current language
    $current_language = defined( 'ICL_LANGUAGE_CODE' ) ? apply_filters( 'wpml_current_language', null ) : null;

    // Base query
    $query = "SELECT COUNT(*) FROM {$wpdb->posts} p";

    // If WPML is active and there is a current language, add a join to filter by language
    if ( $current_language ) {
        $query .= " LEFT JOIN {$wpdb->prefix}icl_translations t 
                    ON p.ID = t.element_id 
                    AND t.element_type = 'post_resume' 
                    AND t.language_code = %s";
        $query .= " WHERE (t.language_code IS NULL OR t.language_code = %s) 
                    AND p.post_author = %d AND p.post_type = %s AND p.post_status = 'publish'";
    } else {
        // Query without WPML language filter
        $query .= " WHERE p.post_author = %d AND p.post_type = %s AND p.post_status = 'publish'";
    }

    // Prepare and execute the query, conditionally including the language if WPML is active
    if ( $current_language ) {
        $count = $wpdb->get_var( $wpdb->prepare( $query, $current_language, $current_language, $displayed_uid, 'resume' ) );
    } else {
        $count = $wpdb->get_var( $wpdb->prepare( $query, $displayed_uid, 'resume' ) );
    }

    return intval( $count );
}

