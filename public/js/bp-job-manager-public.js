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


    //Assign class to each header
    $('#job-manager-job-dashboard table th').each(function() {
      $(this).addClass('header-' + $(this).index());
    });

    //Assign a data-header attribute with the text from the corresponding header
    $('#job-manager-job-dashboard table td').each(function() {
      $(this).attr('data-header', $('.header-' + $(this).index()).text());
    });

    //Assign class to each header
    $('#resume-manager-candidate-dashboard table th').each(function() {
      $(this).addClass('header-' + $(this).index());
    });

    //Assign a data-header attribute with the text from the corresponding header
    $('#resume-manager-candidate-dashboard table td').each(function() {
      $(this).attr('data-header', $('.header-' + $(this).index()).text());
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
        beforeSend: function() {
          $('.wb-grid-cell').find('.job-manager-jobs').after('<div class="inifinite-loader-wrapper"><a id="inifiniteLoader" href="javascript: void(0)" ><button class="loading">Loading...</button></a></div >');
        },
        success: function(response) {
          $('#inifiniteLoader').css('display', 'none');
          $('.job-manager-jobs tbody:last-child').append(response);

          //Assign class to each header
          $('#job-manager-job-dashboard table th').each(function() {
            $(this).addClass('header-' + $(this).index());
          });

          //Assign a data-header attribute with the text from the corresponding header
          $('#job-manager-job-dashboard table td').each(function() {
            $(this).attr('data-header', $('.header-' + $(this).index()).text());
          });

          //Assign class to each header
          $('#resume-manager-candidate-dashboard table th').each(function() {
            $(this).addClass('header-' + $(this).index());
          });

          //Assign a data-header attribute with the text from the corresponding header
          $('#resume-manager-candidate-dashboard table td').each(function() {
            $(this).attr('data-header', $('.header-' + $(this).index()).text());
          });
        }
      });
      return false;
    }

  });