<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

add_action( 'bp_setup_nav', 'wpbpjm_jobs_tab', 301 );
function wpbpjm_jobs_tab(){
  $displayed_user_id = bp_displayed_user_id();
  if( bp_get_member_type( bp_displayed_user_id() ) === 'employer' ) {
    //Count jobs
    $args = array(
      'post_type'         => 'job_listing',
      'post_status'       => 'any',
      'author'            => $displayed_user_id,
      'posts_per_page'    => -1,
      'orderby'           => 'post_date',
      'order'             => 'ASC',
    );
    $my_jobs_count = count( get_posts( $args ) );

  	global $bp;
  	$name = bp_get_displayed_user_username();
  	$tab_args = array(
  		'name' => 'Jobs <span class="no-count">'.$my_jobs_count.'</span>',
  		'slug' => 'jobs',
  		'screen_function' => 'jobs_tab_function_to_show_screen',
  		'position' => 75,
  		'default_subnav_slug' => 'my_jobs',
  		'show_for_displayed_user' => true,
  	);
  	bp_core_new_nav_item( $tab_args );

  	$parent_slug = 'jobs';

  	//Add subnav my jobs - list all my jobs
  	bp_core_new_subnav_item(
  		array(
  			'name' => 'My Jobs',
  			'slug' => 'my_jobs',
  			'parent_url' => $bp->loggedin_user->domain . $parent_slug.'/',
  			'parent_slug' => $parent_slug,
  			'screen_function' => 'wpbpjm_my_jobs_show_screen',
  			'position' => 100,
  			'link' => site_url()."/members/$name/$parent_slug/my_jobs/",
  		)
  	);

    if( bp_loggedin_user_id() === bp_displayed_user_id() ) {
      //Add subnav post a job
    	bp_core_new_subnav_item(
    		array(
    			'name' => 'Post A Job',
    			'slug' => 'post_a_job',
    			'parent_url' => $bp->loggedin_user->domain . $parent_slug.'/',
    			'parent_slug' => $parent_slug,
    			'screen_function' => 'wpbpjm_post_a_job_show_screen',
    			'position' => 100,
    			'link' => site_url()."/members/$name/$parent_slug/post_a_job/",
    		)
    	);
    }
  }
}

//Screen functions for "my jobs tab"
function wpbpjm_my_jobs_show_screen(){
	add_action( 'bp_template_title', 'wpbpjm_my_jobs_tab_function_to_show_title' );
	add_action( 'bp_template_content', 'wpbpjm_my_jobs_tab_function_to_show_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function wpbpjm_my_jobs_tab_function_to_show_title(){
  if( isset( $_GET['apply'] ) ) {
    $job = get_post( sanitize_text_field( $_GET['apply'] ) );
    $title = 'Apply to the job : '.$job->post_title;
  } else {
    $title = 'My Jobs';
    if( bp_loggedin_user_id() === bp_displayed_user_id() ) {
      $name = bp_get_displayed_user_username();
      $parent_slug = 'jobs';
      $post_job_url = site_url()."/members/$name/$parent_slug/post_a_job/";
      $title .= '<a class="wpbpjm-post-job-link" href="'.$post_job_url.'" title="Post A New Job To Your Profile!">';
      $title .= ' +Post A Job';
      $title .= '</a>';
    }
  }
  echo $title;
}

function wpbpjm_my_jobs_tab_function_to_show_content() {
  if( isset( $_GET['apply'] ) ) {
    include 'profile/jobs/apply-jobs.php';
  } else {
    include 'profile/jobs/my-jobs.php';
  }
}

//Screen functions for "post a job tab"
function wpbpjm_post_a_job_show_screen(){
	add_action( 'bp_template_title', 'wpbpjm_post_a_job_tab_function_to_show_title' );
	add_action( 'bp_template_content', 'wpbpjm_post_a_job_tab_function_to_show_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function wpbpjm_post_a_job_tab_function_to_show_title(){
  echo 'Post A New Job Here';
}

function wpbpjm_post_a_job_tab_function_to_show_content(){
	echo do_shortcode( '[submit_job_form]' );
}
