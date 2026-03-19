<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<style>
    #naturePie{
        height: 300px !important;
        width: 300px !important;
    }
</style>

<body style="overflow-x: hidden;">

    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    <!-- [ Header ] end -->
    <div class="pc-container">
        <div class="pc-content">

            <div class="container">
                <h3>Payment Initiation Dashboard</h3>

                {{-- Filter --}}
                <div class="row">
                    <div class="col-xl-3 col-md-3">
                        <div class="tax-dropdown-wrapper account-section">
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 11px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span id="data_values"></span> <i class="fa fa-caret-down"></i>
                                <input type="hidden" class="data_values">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        {{-- Nature Filter --}}
                        <select id="natureFilter" class="form-select">
                            <option value="">All Natures</option>
                            @foreach($natures as $nature)
                                <option value="{{ $nature }}">{{ ucfirst($nature) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                {{-- Charts --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <canvas id="naturePie"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="creditDebitBar"></canvas>
                    </div>
                </div>

                {{-- Transactions Table --}}
                <div id="transactions-container">
                    @include('vendor.partials.transactions',['transactions'=>$transactions])
                </div>

                <a href="{{ route('dashboard.export') }}" class="btn btn-primary mt-3">Export NEFT/RTGS</a>
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
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>
<script>
    // Declare globally
    let naturePieChart;
    let creditDebitChart;

    // Pie Chart: Nature of Payments
    const ctx1 = document.getElementById('naturePie');
    naturePieChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: {!! json_encode($natureSummary->keys()) !!},
            datasets: [{
                data: {!! json_encode($natureSummary->values()) !!},
                backgroundColor: ['#FF6384','#36A2EB','#FFCE56']
            }]
        }
    });

    // Bar Chart: Credit vs Debit
    const ctx2 = document.getElementById('creditDebitBar');
    creditDebitChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Credit','Debit'],
            datasets: [{
                label: 'Amount',
                data: [{{ $creditTotal }}, {{ $debitTotal }}],
                backgroundColor: ['#4CAF50','#F44336']
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    $(document).ready(function () {
    function fetchData() {
        let nature = $('#natureFilter').val();
        let dateRange = $('.data_values').val();

        $.ajax({
            url: "{{ route('superadmin.dashboard') }}",
            method: "GET",
            data: {
                nature: nature,
                date: dateRange
            },
            success: function (response) {
                console.log("response",response);

                // Insert only HTML table content
                $('#transactions-container').html(response.html);

                // Update charts with chartData
                if (response.chartData) {
                    updateCharts(response.chartData);
                }
            }
        });
    }

    // Trigger on change
    $('#natureFilter').on('change', fetchData);

    // Trigger on date change (daterangepicker)
    $(document).on('change', '.data_values', fetchData);
});
function updateCharts(chartData) {
    // Pie chart
    naturePieChart.data.labels = Object.keys(chartData.natureSummary);
    naturePieChart.data.datasets[0].data = Object.values(chartData.natureSummary);
    naturePieChart.update();

    // Bar chart
    creditDebitChart.data.datasets[0].data = [
        chartData.creditTotal,
        chartData.debitTotal
    ];
    creditDebitChart.update();
}

</script>

    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>