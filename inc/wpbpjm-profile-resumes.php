<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
add_action( 'bp_setup_nav', 'wpbpjm_resumes_tab', 301 );
function wpbpjm_resumes_tab() {
  if( bp_loggedin_user_id() === bp_displayed_user_id() ) {
    if( bp_get_member_type( bp_displayed_user_id() ) === 'candidate' ) {
      //Count resumes
      $displayed_user_id = bp_displayed_user_id();
      $args = array(
        'post_type'         => 'resume',
        'post_status'       => 'any',
        'author'            => $displayed_user_id,
        'posts_per_page'    => -1,
        'orderby'           => 'post_date',
        'order'             => 'ASC',
      );
      $my_resumes_count = count( get_posts( $args ) );

      global $bp;
    	$name = bp_get_displayed_user_username();
    	$tab_args = array(
    		'name' => 'Resumes <span class="no-count">'.$my_resumes_count.'</span>',
    		'slug' => 'resumes',
    		'screen_function' => 'resumes_tab_function_to_show_screen',
    		'position' => 75,
    		'default_subnav_slug' => 'my_resumes',
    		'show_for_displayed_user' => true,
    	);
    	bp_core_new_nav_item( $tab_args );

    	$parent_slug = 'resumes';

      //Add subnav my jobs - list all my jobs
    	bp_core_new_subnav_item(
    		array(
    			'name' => 'My Resumes',
    			'slug' => 'my_resumes',
    			'parent_url' => $bp->loggedin_user->domain . $parent_slug.'/',
    			'parent_slug' => $parent_slug,
    			'screen_function' => 'wpbpjm_my_resumes_show_screen',
    			'position' => 100,
    			'link' => site_url()."/members/$name/$parent_slug/my_resumes/",
    		)
    	);
    }
  }
}

//Screen functions for "my resumes tab"
function wpbpjm_my_resumes_show_screen(){
	add_action( 'bp_template_title', 'wpbpjm_my_resumes_tab_function_to_show_title' );
	add_action( 'bp_template_content', 'wpbpjm_my_resumes_tab_function_to_show_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function wpbpjm_my_resumes_tab_function_to_show_title(){
  $title = 'My Resumes';
  echo $title;
}

function wpbpjm_my_resumes_tab_function_to_show_content() {
  include 'profile/resumes/my-resumes.php';
}
