<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
$loggedin_user_id = bp_loggedin_user_id();
$displayed_user_id = bp_displayed_user_id();

$my_saved_jobs = get_user_meta( get_current_user_id(), 'my_saved_jobs', true );

if( !$my_saved_jobs ) {
  ?>
  <div id="message" class="info">
      <p><?php _e( "Sorry, no saved jobs were found.", WPBPJM_TEXT_DOMAIN );?></p>
  </div>
  <?php
} else {
  ?>
  <ul id="members-list" class="item-list" aria-live="assertive" aria-relevant="all">
    <?php foreach( $my_saved_jobs as $job_id ) {?>
      <?php
      $job = get_post( $job_id );
      $job_author_id = $job->post_author;
      $avatar_url = bp_core_fetch_avatar(array(
          'item_id' => $job_author_id,
          'object' => 'user',
          'html' => false
      ));
      $job_author_link = bp_core_get_user_domain( $job_author_id );
      $job_author_details = get_userdata( $job_author_id );
      //echo '<pre>'; print_r( $job_author_details ); die;
      $job_author_name = $job_author_details->data->display_name;

      $job_title = $job->post_title;
      $job_permalink = get_permalink( $job_id );

      $job_status = $job->post_status;
      if( $job_status === 'pending' ) {
        $status = '-yet pending';
      } elseif ( $job_status === 'publish' ) {
        $status = '-published';
      }

      ?>
    <li class="odd is-online is-current-user">
      <div class="item-avatar">
        <a href="<?php echo $job_author_link;?>">
          <img src="<?php echo $avatar_url;?>" class="avatar user-<?php echo $job_author_id;?>-avatar avatar-50 photo" width="50" height="50" alt="Profile picture of <?php echo $job_author_name;?>">
        </a>
			</div>
			<div class="item">
				<div class="item-title">
					<a href="<?php echo $job_permalink;?>"><?php echo $job_title;?></a> <i class="wpbpjm-job-status"><?php echo $status;?></i>
				</div>
				<div class="item-meta">
          <span class="activity"><?php echo $job->post_content;?></span>
        </div>
      </div>
      <div class="action">
        <?php if( is_user_logged_in() ) {?>
          <!-- APPLY FOR A JOB -->
          <?php if ( bp_get_member_type( get_current_user_id() ) === 'candidate' && $job_status === 'publish' ) {?>
            <div class="generic-button">
              <?php $job_apply_url = $job_author_link.'jobs?apply='.$job_id;?>
              <a href="<?php echo $job_apply_url;?>" class="wpbpjm-apply-for-job">
                <?php _e( 'Apply For Job', WPBPJM_TEXT_DOMAIN );?>
              </a>
            </div>
          <?php }?>

          <!-- SAVE A JOB -->
          <?php if ( $job_author_id != get_current_user_id() ) {?>
            <div class="generic-button">
              <?php $my_saved_jobs = get_user_meta( get_current_user_id(), 'my_saved_jobs', true );?>
              <?php if( !empty( $my_saved_jobs ) && in_array( $job_id, $my_saved_jobs ) ) {?>
                <a href="javascript:void(0);" class="wpbpjm-unsave-job" id="wpbpjm-unsave-job-<?php echo $job_id;?>" data-jobid="<?php echo $job_id;?>">
                  <?php _e( 'Unsave Job', WPBPJM_TEXT_DOMAIN );?>
                </a>
              <?php } else {?>
                <a href="javascript:void(0);" class="wpbpjm-save-job" id="wpbpjm-save-job-<?php echo $job_id;?>" data-jobid="<?php echo $job_id;?>">
                  <?php _e( 'Save Job', WPBPJM_TEXT_DOMAIN );?>
                </a>
              <?php }?>
            </div>
          <?php }?>
        <?php }?>
			</div>
			<div class="clear"></div>
		</li>
    <?php }?>
	</ul>
  <?php
}
