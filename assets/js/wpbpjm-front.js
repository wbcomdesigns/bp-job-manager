jQuery(document).ready(function(){
  //Save A Job
  jQuery(document).on('click', '.wpbpjm-save-job', function(){
    var jobid = jQuery(this).data('jobid');
    jQuery(this).html('Saving...');
    jQuery.post(
      wpbpjm_front_js_object.ajaxurl,
      {
        'action' : 'wpbpjm_save_job',
        'jobid' : jobid
      },
      function( response ) {
        if( response == 'job-saved-successfully' ){
          jQuery('#wpbpjm-save-job-'+jobid).html('Unsave Job');
          jQuery('#wpbpjm-save-job-'+jobid).addClass('wpbpjm-unsave-job').removeClass('wpbpjm-save-job');
          jQuery('#wpbpjm-save-job-'+jobid).attr( 'id', 'wpbpjm-unsave-job-'+jobid );
        }
      }
    );
  });

  //Unsave A Job
  jQuery(document).on('click', '.wpbpjm-unsave-job', function(){
    var jobid = jQuery(this).data('jobid');
    jQuery(this).html('Unsaving...');
    jQuery.post(
      wpbpjm_front_js_object.ajaxurl,
      {
        'action' : 'wpbpjm_unsave_job',
        'jobid' : jobid
      },
      function( response ) {
        if( response == 'job-unsaved-successfully' ){
          jQuery('#wpbpjm-unsave-job-'+jobid).html('Save Job');
          jQuery('#wpbpjm-unsave-job-'+jobid).addClass('wpbpjm-save-job').removeClass('wpbpjm-unsave-job');
          jQuery('#wpbpjm-unsave-job-'+jobid).attr( 'id', 'wpbpjm-save-job-'+jobid );
        }
      }
    );
  });

  //List applicants based on selected job
  jQuery(document).on('change', '#wpbpjm-select-job-list-applicants', function(){
    var jobid = jQuery(this).val();
    if( jobid != 0 ) {
      jQuery('.wpbpjm-select-job-wait-text').show();
      jQuery.post(
        wpbpjm_front_js_object.ajaxurl,
        {
          'action' : 'wpbpjm_list_applicants_per_job',
          'jobid' : jobid
        },
        function( response ) {
          jQuery('.wpbpjm-select-job-wait-text').hide();
          jQuery('.wpbpjm-applicants-per-job').html(response);
        }
      );
    }
  });
});
