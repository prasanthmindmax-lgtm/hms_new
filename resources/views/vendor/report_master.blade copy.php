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
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

  <script>
      function rowClick(event) {
          // Remove the 'selected' class from any currently selected row
          const selectedRows = document.querySelectorAll('.selected');
          selectedRows.forEach(row => row.classList.remove('selected'));

          // Add the 'selected' class to the clicked row
          const clickedRow = event.currentTarget;
          clickedRow.classList.add('selected');
      }
  </script>
  <body style="overflow-x: hidden;">
{{--
    @php
        dd($vendor);
    @endphp --}}
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    {{-- @php
        dd($bill[0]->BillLines);
    @endphp --}}
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    <!-- [ Header ] end -->
    <div class="pc-container">
      <div class="card p-4">
        <div class="row p-2">
          <div class="row mb-4">
            <div class="col-xl-2 col-md-2 tax-dropdown-wrapper">
              <label for="vendor">Date</label>
                <div class="col-12 tax-dropdown-wrapper account-section">
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 11px 10px; border: 1px solid #ccc; width: 185px">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span id="data_values"></span> <i class="fa fa-caret-down"></i>
                        <input type="hidden" class="data_values">
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-2 tax-dropdown-wrapper company-section">
              <label for="vendor">Company</label>
              <input type="text" class="form-control company-search-input dropdown-search-input" placeholder="Select Company" readonly>
              <input type="hidden" name="company_id" class="company_id">
              <div class="dropdown-menu tax-dropdown">
                <div class="inner-search-container">
                  <input type="text" class="inner-search" placeholder="Search Company...">
                </div>
                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                  </div>
                <div class="dropdown-list multiselect company-list">
                </div>
              </div>
            </div>

            <div class="col-xl-2 col-md-2 tax-dropdown-wrapper zone-section">
              <label for="vendor">Zone</label>
              <input type="text" class="form-control zone-search-input dropdown-search-input" placeholder="Select Zone" readonly>
              <input type="hidden" name="zone_id" class="zone_id">
              <div class="dropdown-menu tax-dropdown">
                <div class="inner-search-container">
                  <input type="text" class="inner-search" placeholder="Search Zone...">
                </div>
                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                  </div>
                <div class="dropdown-list multiselect zone-list">
                </div>
              </div>
            </div>


            <div class="col-xl-2 col-md-2 tax-dropdown-wrapper branch-section">
              <label for="vendor">Branch</label>
                <input type="text" class="form-control branch-search-input dropdown-search-input" placeholder="Select Branch" readonly>
                <input type="hidden" name="branch_id" class="branch_id">
                <div class="dropdown-menu tax-dropdown">
                  <div class="inner-search-container">
                    <input type="text" class="inner-search" placeholder="Search Branch...">
                  </div>
                  <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                  </div>
                  <div class="dropdown-list multiselect branch-list">
                  </div>
                </div>
              </div>

            <div class="col-xl-2 col-md-2 tax-dropdown-wrapper vendor-section">
              <label for="vendor">Vendor</label>
              <input type="text" class="form-control vendor-search-input dropdown-search-input" placeholder="Select Vendor" readonly>
              <input type="hidden" name="vendor_id" class="vendor_id">
              <div class="dropdown-menu tax-dropdown">
                <div class="inner-search-container">
                  <input type="text" class="inner-search" placeholder="Search Vendor...">
                </div>
                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                  </div>
                <div class="dropdown-list multiselect vendor-list">
                </div>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-xl-2 col-md-2 tax-dropdown-wrapper vendor-section">
              <label for="vendor">Nature of Payment</label>
              <input type="text" class="form-control nature-search-input dropdown-search-input" placeholder="Search Nature of payment..." readonly>
              <input type="hidden" name="nature_id" class="nature_id">
              <div class="dropdown-menu tax-dropdown">
                <div class="inner-search-container">
                  <input type="text" class="inner-search" placeholder="Search Nature of payment...">
                </div>
                <div class="d-flex justify-content-between g-3 p-2 border-bottom" style="gap: 10px;">
                    <button type="button" class="btn btn-sm btn-outline-primary select-all">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all">Deselect All</button>
                  </div>
                <div class="dropdown-list multiselect account-list">
                </div>
              </div>
            </div>

            <div class="col-xl-2 col-md-2 tax-dropdown-wrapper company-section">
              <label for="">Search</label>
              <input type="text" class="form-control universal_search" placeholder="Search">
            </div>
          </div>
            <div class="mb-2 p-2 d-flex align-items-center">
              <strong>Applied Filters:</strong>
              <div id="filter-summary" class=""></div>
          </div>
        </div>
          <h4 class="mb-3">Top Expenses</h4>
          <div class="row">
              <!-- Chart Section -->
              <div class="col-md-6">
                  <canvas id="expenseChart" height="300"></canvas>
              </div>

              <!-- Expense List Section -->
              <div class="col-md-6">
                  <div class="row">
                      <!-- Split into two halves -->
                      @php
                          $half = ceil($topExpenses->count() / 2);
                          $firstHalf = $topExpenses->slice(0, $half);
                          $secondHalf = $topExpenses->slice($half);
                      @endphp

                      <div class="col-md-6">
                          <ul class="list-group">
                              @foreach ($firstHalf as $expense)
                                  <li class="list-group-item d-flex justify-content-between align-items-center expense-item"
                                      data-type="{{ $expense->account }}"
                                      style="cursor:pointer;">
                                      <span>{{ $expense->account }}</span>
                                      <strong>₹{{ number_format($expense->total_amount, 2) }}</strong>
                                  </li>
                              @endforeach
                          </ul>
                      </div>

                      <div class="col-md-6">
                          <ul class="list-group">
                              @foreach ($secondHalf as $expense)
                                  <li class="list-group-item d-flex justify-content-between align-items-center expense-item"
                                      data-type="{{ $expense->account }}"
                                      style="cursor:pointer;">
                                      <span>{{ $expense->account }}</span>
                                      <strong>₹{{ number_format($expense->total_amount, 2) }}</strong>
                                  </li>
                              @endforeach
                          </ul>
                      </div>
                  </div>
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
<script src="{{ asset('/assets/js/vendor/quotation_search.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const reportDetailsRoute = "{{ url('/superadmin/reports/details') }}";
const TblZonesModel = @json($TblZonesModel);
    const Tblcompany = @json($Tblcompany);
    const Tblvendor = @json($Tblvendor);
    const Tblaccount = @json($Tblaccount);
     (Array.isArray(TblZonesModel) ? TblZonesModel : (TblZonesModel.data || [])).forEach(locations => {
                        const item = $(`
                          <div data-id="${locations.id}">${locations.name} </div>
                        `);
                        $('.zone-list').append(item);
                    });
      Tblcompany.data.forEach(Tblcompany => {
                        const item = $(`
                            <div data-value="${Tblcompany.company_name}" data-id="${Tblcompany.id}">${Tblcompany.company_name}</div>
                        `);
                        $('.company-list').append(item);
                    });
      Tblvendor.forEach(Tblvendor => {
                        const item = $(`
                            <div data-value="${Tblvendor.display_name}" data-id="${Tblvendor.id}">${Tblvendor.display_name}</div>
                        `);
                        $('.vendor-list').append(item);
                    });
                    Tblaccount.forEach(Tblaccount => {
                        const item = $(`
                            <div data-value="${Tblaccount.name}" data-id="${Tblaccount.id}">${Tblaccount.name}</div>
                        `);
                        $('.account-list').append(item);
                    });


   $(document).ready(function () {

    // =================== OPEN DROPDOWN ===================
    $(document).on('click', '.dropdown-search-input', function (e) {
      e.stopPropagation();
      $('.dropdown-menu.tax-dropdown').hide(); // close others

      const $input = $(this);
      let $dropdown = $input.data('dropdown');

      // Clone dropdown only once per input
      if (!$dropdown) {
        $dropdown = $input.siblings('.dropdown-menu').clone(true);
        $('body').append($dropdown);
        $input.data('dropdown', $dropdown);
      }

      $dropdown.data('wrapper', $input.closest('.tax-dropdown-wrapper'));

      const offset = $input.offset();
      $dropdown.css({
        position: 'absolute',
        top: offset.top + $input.outerHeight(),
        left: offset.left,
        width: $input.outerWidth(),
        zIndex: 999
      }).show();

      $dropdown.find('.inner-search').focus();
    });

    // =================== FILTER SEARCH ===================
    $(document).on('keyup', '.inner-search', function () {
      const searchVal = $(this).val().toLowerCase();
      $(this).closest('.dropdown-menu').find('.dropdown-list div').each(function () {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(searchVal) > -1);
      });
    });

    // =================== MULTISELECT (Individual Item) ===================
    $(document).on('click', '.dropdown-list.multiselect div', function (e) {
      e.stopPropagation();
      $(this).toggleClass('selected');
      const $dropdown = $(this).closest('.tax-dropdown');
      updateMultiSelection($dropdown);
    });

    // =================== SELECT ALL ===================
    $(document).on('click', '.select-all', function (e) {
      e.stopPropagation();
      const $dropdown = $(this).closest('.tax-dropdown');
      $dropdown.find('.dropdown-list.multiselect div').addClass('selected');
      updateMultiSelection($dropdown);
    });

    // =================== DESELECT ALL ===================
    $(document).on('click', '.deselect-all', function (e) {
      e.stopPropagation();
      const $dropdown = $(this).closest('.tax-dropdown');
      $dropdown.find('.dropdown-list.multiselect div').removeClass('selected');
      updateMultiSelection($dropdown);
    });

    // =================== CLOSE ON OUTSIDE CLICK ===================
    $(document).on('click', function (e) {
      if (!$(e.target).closest('.tax-dropdown-wrapper').length && !$(e.target).closest('.tax-dropdown').length) {
        $('.dropdown-menu.tax-dropdown').hide();
      }
    });

    // =================== UPDATE MULTISELECT VALUES ===================
    function updateMultiSelection($dropdown) {
      const wrapper = $dropdown.data('wrapper');
      if (!wrapper) return;

      const selectedItems = [];
      const selectedIds = [];

      $dropdown.find('.dropdown-list.multiselect div.selected').each(function () {
        selectedItems.push($(this).text().trim());
        selectedIds.push($(this).data('id'));
      });

      const $visibleInput = wrapper.find('.dropdown-search-input');
      const $hiddenInput = wrapper.find('input[type="hidden"]');

      // Update visible & hidden inputs
      $visibleInput.val(selectedItems.join(', '));
      $hiddenInput.val(selectedIds.join(','));

      // ✅ Trigger change event (important)
      $hiddenInput.trigger('click');
    }
  });

      $('.zone_id').on('click', function () {
        var id=$('.zone_id').val();
        let formData = new FormData();
        formData.append('id',id);
        $.ajax({
            url: '{{ route("superadmin.getbranchfetch") }}',
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
              // toastr.success(response.message);
              if(response.branch !==""){
                $('.branch-list div').remove();
                response.branch.forEach(branch => {
                    const item = $(`
                      <div data-id="${branch.id}">${branch.name} </div>
                    `);
                    $('.branch-list').append(item);
                });
              }
            },
            error: function (xhr) {
                console.error("Error saving form:", xhr);
                toastr.error("Something went wrong.");
            }
        });
      });
      $('.zone-list div').on('click',function(){
        $('.branch-search-input').val('');
        $('.branch_id').val('');
      })
       $('.zone-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.zone-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
        $('.branch-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.branch-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
        $('.company-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.company-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
        $('.vendor-inner-search').on('keyup', function () {
          const searchText = $(this).val().toLowerCase();
          const list = $(this).closest('.dropdown-menu').find('.vendor-list div');

          list.each(function () {
            const itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.includes(searchText));
          });
        });
         $(document).on('click', function (e) {
            if (!$(e.target).closest('.tax-dropdown-wrapper').length) {
              $('.dropdown-menu.tax-dropdown').hide();
            }
          });
          $(document).on('click', '.dropdown-menu.tax-dropdown', function (e) {
                e.stopPropagation();
            });

const ctx = document.getElementById('expenseChart').getContext('2d');

// 🎨 Beautiful gradient colors
const colors = [
  '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
  '#858796', '#fd7e14', '#20c997', '#6610f2', '#17a2b8',
  '#ff6384', '#6f42c1', '#ffc107', '#198754', '#0dcaf0',
  '#8e44ad', '#ff9f40', '#00b894', '#d63031', '#0984e3',
  '#ff7675', '#00cec9', '#fab1a0', '#e17055', '#a29bfe'
];

const labels = @json($topExpenses->pluck('account'));
const dataValues = @json($topExpenses->pluck('total_amount'));

// 🥧 Donut chart setup
const expenseChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: labels,
    datasets: [{
      label: 'Total Expenses (₹)',
      data: dataValues,
      backgroundColor: colors,
      borderColor: '#fff',
      borderWidth: 2,
      hoverOffset: 12
    }]
  },
  options: {
    responsive: true,
    cutout: '70%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          usePointStyle: true,
          pointStyle: 'circle',
          color: '#333',
          font: {
            size: 13,
            weight: '500'
          },
          padding: 20
        }
      },
      title: {
        display: true,
        text: 'Top Expenses Breakdown',
        color: '#333',
        font: {
          size: 18,
          weight: 'bold'
        },
        padding: { bottom: 25 }
      },
      tooltip: {
        backgroundColor: 'rgba(0,0,0,0.8)',
        titleColor: '#fff',
        bodyColor: '#fff',
        padding: 10,
        cornerRadius: 8,
        callbacks: {
          label: (context) => {
            let label = context.label || '';
            let value = context.raw || 0;
            return `${label}: ₹${value.toLocaleString()}`;
          }
        }
      }
    },
    animation: {
      animateScale: true,
      animateRotate: true,
      duration: 2000,
      easing: 'easeOutQuart'
    }
  }

});



var filters='';
$(document).ready(function () {
         filters = {
            date_from: '',
            date_to: '',
            zone_name: '',
            zone_id: '',
            branch_name: '',
            branch_id: '',
            company_id: '',
            company_name: '',
            vendor_name: '',
            vendor_id: '',
            nature_name: '',
            nature_id: '',
            universal_search: '',
        };

        function renderSummary() {
            let summaryHtml = '';

            if (filters.date_from && filters.date_to) {
                summaryHtml += `<span class="filter-badge remove-icon" data-type="date">
                    ${filters.date_from} → ${filters.date_to}
                </span>`;
            }

            if (filters.zone_id) {
                summaryHtml += `<span class="filter-badge remove-icon" data-type="zone">
                    ${filters.zone_name}
                </span>`;
            }
            if (filters.branch_id) {
                summaryHtml += `<span class="filter-badge remove-icon" data-type="branch">
                    ${filters.branch_name}
                </span>`;
            }
            if (filters.company_id) {
                summaryHtml += `<span class="filter-badge remove-icon" data-type="company">
                    ${filters.company_name}
                </span>`;
            }
            if (filters.vendor_id) {
                summaryHtml += `<span class="filter-badge remove-icon" data-type="vendor">
                    ${filters.vendor_name}
                </span>`;
            }
            if (filters.nature_id) {
                summaryHtml += `<span class="filter-badge remove-icon" data-type="nature">
                    ${filters.nature_name}
                </span>`;
            }
            if (filters.status_id) {
                summaryHtml += `<span class="filter-badge remove-icon" data-type="status">
                    ${filters.status_name}
                </span>`;
            }

            if (summaryHtml) {
                summaryHtml += `<span class="filter-badge filter-clear" id="clear-all">
                    Clear all
                </span>`;
            }

            $("#filter-summary").html(summaryHtml || "");
        }

        function loadReport() {
          $.ajax({
            url: '{{ route("superadmin.reportindex") }}',
            type: "GET",
            data: {
              date_from: filters.date_from,
              date_to: filters.date_to,
              zone_id: filters.zone_id,
              branch_id: filters.branch_id,
              company_id: filters.company_id,
              vendor_id: filters.vendor_id,
              nature_id: filters.nature_id,
              universal_search: filters.universal_search,
            },
           success: function (response) {
                // ✅ Update chart data dynamically
                expenseChart.data.labels = response.labels;
                expenseChart.data.datasets[0].data = response.values;
                expenseChart.update();

                // ✅ Rebuild list-group section
                const $listContainer = $(".list-group").closest(".col-md-6").parent(); // The div containing both halves
                $listContainer.empty();

                const half = Math.ceil(response.list.length / 2);
                const firstHalf = response.list.slice(0, half);
                const secondHalf = response.list.slice(half);

                const buildListHtml = (data) => {
                    return `<ul class="list-group">
                        ${data.map(item => `
                            <li class="list-group-item d-flex justify-content-between align-items-center expense-item"
                                data-type="${item.account}" style="cursor:pointer;">
                                <span>${item.account}</span>
                                <strong>₹${item.total_amount}</strong>
                            </li>
                        `).join('')}
                    </ul>`;
                };

                const newHtml = `
                    <div class="col-md-6">${buildListHtml(firstHalf)}</div>
                    <div class="col-md-6">${buildListHtml(secondHalf)}</div>
                `;

                $listContainer.append(newHtml);
                renderSummary();
            },
            error: function (xhr) {
              console.error("Failed to load chart data", xhr);
            },
          });
        }


        // =================== MULTI-SELECT CHANGE LISTENER ===================
        function setupMultiSelect(selectorInput, selectorHidden) {
          $(document).on('click', selectorHidden, function () {
            const selectedIds = $(this).val();
            const selectedText = $(selectorInput).val();

            if (selectorHidden === '.zone_id') {
              filters.zone_id = selectedIds;
              filters.zone_name = selectedText;
            } else if (selectorHidden === '.branch_id') {
              filters.branch_id = selectedIds;
              filters.branch_name = selectedText;
            } else if (selectorHidden === '.company_id') {
              filters.company_id = selectedIds;
              filters.company_name = selectedText;
            } else if (selectorHidden === '.vendor_id') {
              filters.vendor_id = selectedIds;
              filters.vendor_name = selectedText;
            } else if (selectorHidden === '.nature_id') {
              filters.nature_id = selectedIds;
              filters.nature_name = selectedText;
            }

            loadReport();
          });
        }

       $('.zone_id').on('click', function () {
          setupMultiSelect('.zone-search-input', '.zone_id');
        });
        $('.branch_id').on('click', function () {
          setupMultiSelect('.branch-search-input', '.branch_id');
        });
        $('.company_id').on('click', function () {
          setupMultiSelect('.company-search-input', '.company_id');
        });
        $('.vendor_id').on('click', function () {
          setupMultiSelect('.vendor-search-input', '.vendor_id');
        });
        $('.nature_id').on('click', function () {
          setupMultiSelect('.nature-search-input', '.nature_id');
        });
        $('.universal_search').on('keyup', function () {
          filters.universal_search=  $('.universal_search').val();
          loadReport();
        });


        // Date change
        $('.data_values').on('change', function () {
            let dateRange = $(this).val();
            if (dateRange.includes('to')) {
                let parts = dateRange.split(' to ');
                filters.date_from = parts[0].trim();
                filters.date_to = parts[1].trim();
            }
            loadReport();
        });

        // Remove single filter
        $("#filter-summary").on('click', '.remove-icon', function () {
            let type = $(this).data('type');

            if (type === 'date') {
                filters.date_from = '';
                filters.date_to = '';
                $('.data_values').val('');
            } else if (type === 'zone') {
                filters.zone_id = '';
                filters.zone_name = '';
                $('.zone_id').val('');
                $('.zone-search-input').val('');
                $('.zone-list div').removeClass('selected');
            } else if (type === 'branch') {
                filters.branch_id = '';
                filters.branch_name = '';
                $('.branch_id').val('');
                $('.branch-search-input').val('');
                $('.branch-list div').removeClass('selected');
            } else if (type === 'company') {
                filters.company_id = '';
                filters.company_name = '';
                $('.company_id').val('');
                $('.company-search-input').val('');
                $('.company-list div').removeClass('selected');
            } else if (type === 'vendor') {
                filters.vendor_id = '';
                filters.vendor_name = '';
                $('.vendor_id').val('');
                $('.vendor-search-input').val('');
                $('.vendor-list div').removeClass('selected');
            } else if (type === 'nature') {
                filters.nature_id = '';
                filters.nature_name = '';
                $('.nature_id').val('');
                $('.nature-search-input').val('');
                $('.account-list div').removeClass('selected');
            }
            loadReport();
        });

        // Clear all filters
        $("#filter-summary").on('click', '#clear-all', function () {
            filters = {
                date_from: '',
                date_to: '',
                zone_name: '',
                zone_id: '',
                branch_name: '',
                branch_id: '',
                company_id: '',
                company_name: '',
                vendor_name: '',
                vendor_id: '',
                nature_name: '',
                nature_id: '',

                universal_search: '',
            };
            $('.zone-search-input, .branch-search-input, .company-search-input, .vendor-search-input,.nature-search-input,.status-search-input').val('');
            $('.zone_id, .branch_id, .company_id, .vendor_id,.nature_id,.status_id').val('');
            $('.data_values').val('');
            $('.dropdown-list div').removeClass('selected');
            loadReport();
        });

        // Pagination
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            let url = $(this).attr('href');
            let params = new URLSearchParams(url.split('?')[1]);
            let page = params.get('page') || 1;
            let perPage = $('#per_page').val();
            loadReport(page, perPage);
        });

        // Change per_page
        $(document).on('change', '#per_page', function () {
            loadReport(1, $(this).val());
        });
    });
    $(document).on('click', '.expense-item', function () {
      // alert(12);
      const type = $(this).data('type');

      const params = new URLSearchParams({
          date_from: filters.date_from,
          date_to: filters.date_to,
          zone_id: filters.zone_id,
          branch_id: filters.branch_id,
          company_id: filters.company_id,
          vendor_id: filters.vendor_id,
          nature_id: filters.nature_id,
          universal_search: filters.universal_search,
      });

      // 👇 Redirect to the details page with filters
      // window.location.href = `/superadmin/reports/details/${encodeURIComponent(type)}?${params.toString()}`;
      window.location.href = `${reportDetailsRoute}/${encodeURIComponent(type)}?${params.toString()}`;
    });


</script>

    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>