/**
 * Petty Cash Dashboard — date range picker for #reportrange / .data_values
 */
$(function () {
    var $rr = $('#reportrange');
    if (!$rr.length || typeof $.fn.daterangepicker !== 'function') {
        return;
    }

    function applyRange(start, end, label) {
        if (label === 'All Dates') {
            $('#data_values').text('All Dates');
            $('.data_values').val('').trigger('change');
            return;
        }
        var from = start.format('DD/MM/YYYY');
        var to = end.format('DD/MM/YYYY');
        $('#data_values').text(from + ' - ' + to);
        $('.data_values').val(from + ' to ' + to).trigger('change');
    }

    var start = moment().subtract(29, 'days');
    var end = moment();

    $rr.daterangepicker({
        startDate: start,
        endDate: end,
        opens: 'left',
        autoUpdateInput: false,
        alwaysShowCalendars: true,
        ranges: {
            'All Dates': [moment().subtract(50, 'years'), moment().add(50, 'years')],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, applyRange);

    $('#data_values').text('All Dates');
    $('.data_values').val('');
});
