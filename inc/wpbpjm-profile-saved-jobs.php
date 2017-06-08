<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

add_action( 'bp_setup_nav', 'wpbpjm_saved_jobs_tab', 301 );
function wpbpjm_saved_jobs_tab(){
  global $bp;
  $displayed_user_id = bp_displayed_user_id();
  $name = bp_get_displayed_user_username();
  //If the current user is a candidate
  if( bp_get_member_type( get_current_user_id() ) === 'candidate' ) {
    $my_saved_jobs_count = count( get_user_meta( get_current_user_id(), 'my_saved_jobs', true ) );
  	$tab_args = array(
  		'name' => 'Saved Jobs <span class="no-count">'.$my_saved_jobs_count.'</span>',
  		'slug' => 'saved_jobs',
  		'screen_function' => 'wpbpjm_saved_jobs_tab_function_to_show_screen',
  		'position' => 75,
      'default_subnav_slug' => 'my_saved_jobs',
  		'show_for_displayed_user' => true,
  	);
  	bp_core_new_nav_item( $tab_args );
  }

  //If the current user is a employer
  if( bp_get_member_type( get_current_user_id() ) === 'employer' ) {
    if( bp_loggedin_user_id() === bp_displayed_user_id() ) {
      $parent_slug = 'jobs';
      bp_core_new_subnav_item(
    		array(
    			'name' => 'Saved Jobs',
    			'slug' => 'saved_jobs',
    			'parent_url' => $bp->loggedin_user->domain . $parent_slug.'/',
    			'parent_slug' => $parent_slug,
    			'screen_function' => 'wpbpjm_saved_jobs_tab_function_to_show_screen',
    			'position' => 110,
    			'link' => site_url()."/members/$name/$parent_slug/saved_jobs/",
    		)
    	);
    }
  }
}

//Screen functions for "my saved jobs tab"
function wpbpjm_saved_jobs_tab_function_to_show_screen(){
	add_action( 'bp_template_title', 'wpbpjm_saved_jobs_tab_function_to_show_title' );
	add_action( 'bp_template_content', 'wpbpjm_saved_jobs_tab_function_to_show_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

//Function to show title of saved jobs
function wpbpjm_saved_jobs_tab_function_to_show_title() {
  echo "My Saved Jobs";
}

//Function to show title of saved jobs
function wpbpjm_saved_jobs_tab_function_to_show_content() {
  include 'profile/jobs/saved-jobs.php';
}
