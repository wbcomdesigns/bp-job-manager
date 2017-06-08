<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<section id="wpbpjm-job-applicants-content" class="widget">
  <h2 class="widget-title"><?php echo $show_title;?></h2>
  <?php if ( bp_get_member_type( get_current_user_id() ) != 'employer' ) {?>
    <p><?php _e( "This section is available only for employers.", WPBPJM_TEXT_DOMAIN );?></p>
  <?php } else {?>
    <?php
    $loggedin_user_id = bp_loggedin_user_id();
    $args = array(
      'post_type'         => 'job_listing',
      'post_status'       => 'any',
      'author'            => $loggedin_user_id,
      'posts_per_page'    => -1,
      'orderby'           => 'post_date',
      'order'             => 'ASC',
    );
    $jobs = get_posts( $args );
    if( empty( $jobs ) ) {
    ?>
      <p><?php _e( "No job found.", WPBPJM_TEXT_DOMAIN );?></p>
    <?php } else {?>
      <select id="wpbpjm-select-job-list-applicants">
        <option value="0">--Select--</option>
        <?php foreach( $jobs as $job ) {?>
          <option value="<?php echo $job->ID;?>"><?php echo $job->post_title;?></option>
        <?php }?>
      </select>
      <p class="wpbpjm-select-job-wait-text"><i><?php _e( "Listing applicants, please wait...", WPBPJM_TEXT_DOMAIN );?></i></p>
      <div class="wpbpjm-applicants-per-job"></div>
    <?php }?>
  <?php }?>
</section>
