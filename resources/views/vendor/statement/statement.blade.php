<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Statement - Filter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
          body { font-family: Arial, sans-serif; font-size: 13px; color: #000; }
          .container { width: 100%; }
          .left { float: left; width: 50%; }
          .right { float: right; width: 40%; text-align: right; }
          .statement { margin-top: 20px; text-align: right; }
          .statement h2 { margin: 0; font-size: 16px; font-weight: bold; }
          .statement small { display: block; margin-top: 5px; }
          .summary { width: 40%; float: right; margin-top: 10px; border-collapse: collapse; font-size: 13px; }
          .summary th { background: #e0e0e0; padding: 6px; text-align: left; }
          .summary td { padding: 6px; border-bottom: 1px solid #ccc; }
          .summary td:last-child { text-align: right; }
          .summary tr:last-child td { border-bottom: none; }
          table.statement-table { width: 100%; border-collapse: collapse; margin-top: 50px; }
          table.statement-table th { background: #333; color: #fff; text-align: left; padding: 8px; }
          table.statement-table td { padding: 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
          table.statement-table tr:nth-child(even) { background: #f9f9f9; }
          table.statement-table td:nth-child(4),
          table.statement-table td:nth-child(5),
          table.statement-table td:nth-child(6) { text-align: right; }
          .balance-row td { font-weight: bold; border-top: 2px solid #000; background: #fff !important; }
          .drp-calendar{width:250px;}
      </style>
</head>
<body>

<div class="container mt-3">
    <div class="row mb-3" style="display: flex; justify-content: space-between; align-items: center;">
        <!-- Date Picker -->
        <div class="col-xl-3 col-md-4">
            <div class="tax-dropdown-wrapper account-section">
                <div id="reportrange"
                     style="background: #fff; cursor: pointer; padding: 11px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span id="data_values"></span> <i class="fa fa-caret-down"></i>
                    <input type="hidden" class="data_values" name="data_values">
                    <input type="hidden" id="vendor_id" name="vendor_id" value="{{ $id }}">
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="col-xl-3 col-md-4 d-flex gap-2">
            <button class="btn btn-outline-secondary" id="printBtn">
                <i class="bi bi-printer"></i>
            </button>
            <button class="btn btn-outline-secondary" id="pdfBtn">
                <i class="bi bi-file-earmark-pdf"></i>
            </button>
        </div>
    </div>
    <div id="statement-body"></div>
</div>

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width:90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Statement Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="printFrame" src="" width="100%" height="600px" style="border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {

    var start = moment().startOf('month');
    var end   = moment().endOf('month');

    function cb(start, end) {
        let formattedRange = start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY');

        $('#data_values').text(formattedRange);
        $('.data_values').val(formattedRange).trigger('change');
        $("#dateviewsall").text(formattedRange);
        $("#dateallviews").text(formattedRange);
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


    var filters = { date_from: '', date_to: '', vendor_id: $('#vendor_id').val() };
    function loadQuotations() {
       $.ajax({ url: '{{ route("vendor.showStatement") }}',
       type: "GET",
       data: { date_from: filters.date_from, date_to: filters.date_to, id: filters.vendor_id, },
        success: function (data)
        {
          $("#statement-body").html(data);
        }
      });
     }
     loadQuotations();
     $('.data_values').on('change', function () {
              let dateRange = $(this).val();
              if (dateRange.includes('to')) {
                  let parts = dateRange.split(' to ');
                  filters.date_from = parts[0].trim();
                  filters.date_to = parts[1].trim();
              }
              loadQuotations();
          });
    const vendorStatementUrl = "{{ route('superadmin.statementprint', ['id' => '__id__']) }}";

    $('#printBtn').on('click', function() {
        const vendor_id = $('#vendor_id').val();
        let dateRange = $('.data_values').val();
              if (dateRange.includes('to')) {
                  let parts = dateRange.split(' to ');
                  filters.date_from = parts[0].trim();
                  filters.date_to = parts[1].trim();
              }
        let url = vendorStatementUrl.replace('__id__', vendor_id);
        url += `?date_from=${filters.date_from}&date_to=${filters.date_to}`;
        $('#printFrame').attr('src', url);
        var modal = new bootstrap.Modal(document.getElementById('printModal'));
        modal.show();
    });

    $('#pdfBtn').on('click', function() {
        const vendor_id = $('#vendor_id').val();
        let url = vendorStatementUrl.replace('__id__', vendor_id);
        url += `?date_from=${filters.date_from}&date_to=${filters.date_to}&download=pdf`;

        // Create a temporary link and click it
        const link = document.createElement('a');
        link.href = url;
        link.download = "statement.pdf";  // filename for download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

});
</script>
</body>
</html>
