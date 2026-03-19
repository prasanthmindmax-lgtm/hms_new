
    Dropzone.options.myDropzone = {
        acceptedFiles: "image/*", // Only accept image files (any image type)
        addRemoveLinks: true, // Optionally, show remove links for the file
        dictDefaultMessage: "Drag an image here or click to select one image"
    };
    // Set the initial start and end dates
    var start = moment();
    var end = moment();
    // Callback function to update the span text with the selected date range
    function cb(start, end) {
      let formattedRange = start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY');

      // Store the range for AJAX
      $('#data_values').text(formattedRange);
      $('.data_values').val(formattedRange).trigger('change');

      // Keep your existing UI updates
      $("#dateviewsall").text(formattedRange);
      $("#dateallviews").text(formattedRange);

      if (start.isSame(end, 'day')) {
          if (start.isSame(moment(), 'day')) {
              $('#reportrange span').html('Today');
          } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
              $('#reportrange span').html('Yesterday');
          } else {
              $('#reportrange span').html(start.format('DD/MM/YYYY'));
          }
      } else {
          $('#reportrange span').html(formattedRange);
      }
  }
//   console.log("213123123");
  
    // Initialize the date range picker
    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        opens: 'right',
        alwaysShowCalendars: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);


    // Set initial date range text
    cb(start, end);
