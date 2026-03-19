<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Statement - Filter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>
<body>

<div class="container mt-3">
    <div class="row" style="display: flex; justify-content: space-between; align-items: center;">
        <!-- Date Picker -->
        <div class="col-xl-3 col-md-4">
            <div class="tax-dropdown-wrapper account-section">
                <div id="reportrange"
                     style="background: #fff; cursor: pointer; padding: 11px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span id="data_values"></span> <i class="fa fa-caret-down"></i>
                    <input type="hidden" class="data_values" name="data_values">

                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="col-xl-3 col-md-4 d-flex gap-2">
            <button class="btn btn-outline-secondary" id="printBtn">
                <i class="bi bi-printer"></i> Print
            </button>
            <button class="btn btn-outline-secondary" id="pdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> PDF
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
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
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
    //  loadQuotations();
    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        let dateRange = picker.startDate.format('DD/MM/YYYY') + ' to ' + picker.endDate.format('DD/MM/YYYY');
        $('.data_values').val(dateRange);
        $('#data_values').text(dateRange);
        filters.date_from = picker.startDate.format('DD/MM/YYYY');
        filters.date_to   = picker.endDate.format('DD/MM/YYYY');
        loadQuotations();
    });

    const vendorStatementUrl = "{{ route('superadmin.statementprint', ['id' => '__id__']) }}";

    $('#printBtn').on('click', function() {
        const vendor_id = $('#vendor_id').val();
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
        window.open(url, "_blank");
    });
});
</script>
</body>
</html>
