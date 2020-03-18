jQuery(document).ready(
  function($) {
    'use strict';

    // Open the job application url.
    $(document).on(
      'click', '#bpjm-job-application-btn a',
      function() {
        window.open($(this).data('url'), '_blank');
      }
    );

    $(document).on(
      'click', '.bpjm-display-resume-checkbox',
      function() {
        $(".resume-fields-row").animate({
          height: 'toggle'
        });
      });


    var count = 2;
    var total = bpjm_load_jobs_object.max_num_pages;
    $(window).scroll(function() {
      if ($(window).scrollTop() == $(document).height() - $(window).height()) {
        if (count > total) {
          return false;
        } else {
          loadJobs(count);
        }
        count++;
      }

    });

    function loadJobs(pageNumber) {
      $('#inifiniteLoader').show('fast');
      var data = {
        'action': 'bpjm_load_more_jobs',
        'page_no': pageNumber,
        'ajax_nonce': bpjm_load_jobs_object.ajax_nonce,
      };
      $.ajax({
        url: bpjm_load_jobs_object.ajaxurl,
        type: 'POST',
        data: data,
        success: function(response) {
          $('#inifiniteLoader').hide();
          $('.job-manager-jobs tbody:last-child').append(response)
        }
      });
      return false;
    }
  });