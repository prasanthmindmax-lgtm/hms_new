$(document).ready(function() {
    var start = moment().startOf('month');
    var end   = moment().endOf('month');

    function cb(start, end) {
        let formattedRange = start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY');

        $('#data_values').text(formattedRange);
        $('.data_values').val(formattedRange).trigger('change');
        $("#dateviewsall").text(formattedRange);
        $("#dateallviews").text(formattedRange);
        start=start;
        end=end;
        console.log("start",start);
        console.log("end",end);

    }

    // Destroy old picker if already initialized
    $('#reportrange').data('daterangepicker')?.remove();

    // Re-init with default "This Month"
    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);


    cb(start, end);

});
