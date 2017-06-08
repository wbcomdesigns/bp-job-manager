<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Class to add a widget for showing applicants per job
if( !class_exists( 'JobApplicants' ) ) {
  class JobApplicants extends WP_Widget {
    function __construct() {
      parent::__construct('JobApplicants',
       __('Employer\'s Profile - Job Applicants'),
        array('description' => __('This widget shows the list of applicants per job on single employers profile page.') )
      );
    }

    public function widget( $args, $instance ) {
      global $wp_widget_factory;
      $title = $instance['title'];
      if( !empty( $title ) ) {
        $show_title = strip_tags( $title );
      }
      include 'wpbpjm-job-applicants-template.php';
    }

    public function update( $new_instance, $old_instance ) {
      $instance = array();
      $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
      return $instance;
    }

    public function form( $instance ) {
      $title = __('New Title');
      if( isset( $instance['title'] ) ) {
        $title = $instance['title'];
      }
      //Creating the form at the admin side for setting the title
      ?>
        <p>
          <label for="<?php echo $this->get_field_id('title')?>"><?php _e('Title')?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title')?>" name="<?php echo $this->get_field_name('title')?>" type="text" value="<?php echo esc_attr($title);?>">
        </p>
      <?php
    }
  }
}
