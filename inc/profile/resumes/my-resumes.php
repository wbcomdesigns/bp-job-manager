<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
$loggedin_user_id = bp_loggedin_user_id();
$displayed_user_id = bp_displayed_user_id();

$args = array(
  'post_type'         => 'resume',
  'post_status'       => 'any',
  'author'            => $displayed_user_id,
  'posts_per_page'    => -1,
  'orderby'           => 'post_date',
  'order'             => 'ASC',
);
$my_resumes = get_posts( $args );
if( !$my_resumes ) {
  ?>
  <div id="message" class="info">
      <p><?php _e( "Sorry, no resumes were found.", WPBPJM_TEXT_DOMAIN );?></p>
  </div>
  <?php
} else {
  ?>
  <ul id="members-list" class="item-list" aria-live="assertive" aria-relevant="all">
    <?php foreach( $my_resumes as $resume ) {?>
      <?php
      $resume_author_id = $resume->post_author;
      $avatar_url = bp_core_fetch_avatar(array(
          'item_id' => $resume_author_id,
          'object' => 'user',
          'html' => false
      ));
      $resume_author_link = bp_core_get_user_domain( $resume_author_id );
      $resume_author_details = get_userdata( $resume_author_id );
      //echo '<pre>'; print_r( $resume_author_details ); die;
      $resume_author_name = $resume_author_details->data->display_name;

      $resume_title = $resume->post_title;
      $resume_permalink = get_permalink( $resume->ID );

      $resume_status = $resume->post_status;
      if( $resume_status === 'pending' ) {
        $status = '-yet pending';
      } elseif ( $resume_status === 'publish' ) {
        $status = '-published';
      }

      ?>
    <li class="odd is-online is-current-user">
      <div class="item-avatar">
        <a href="<?php echo $resume_author_link;?>">
          <img src="<?php echo $avatar_url;?>" class="avatar user-<?php echo $resume_author_id;?>-avatar avatar-50 photo" width="50" height="50" alt="Profile picture of <?php echo $resume_author_name;?>">
        </a>
			</div>
			<div class="item">
				<div class="item-title">
					<a href="<?php echo $resume_permalink;?>"><?php echo $resume_title;?></a> <i class="wpbpjm-resume-status"><?php echo $status;?></i>
				</div>
				<div class="item-meta">
          <span class="activity"><?php echo $resume->post_content;?></span>
        </div>
      </div>
      <div class="action">
			</div>
			<div class="clear"></div>
		</li>
    <?php }?>
	</ul>
  <?php
}
