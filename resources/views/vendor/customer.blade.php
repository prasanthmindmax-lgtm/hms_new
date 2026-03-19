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

            <div class="container-box">
                <div class="header-bar">
                    <h5>Active Customers <span class="dropdown-toggle"></span></h5>
                    <div>
                    <a href="{{ route('superadmin.getcustomercreate') }}" class="btn btn-primary btn-sm">+ New</a>
                    <span class="ellipsis">⋮</span>
                    </div>
                </div>

                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th style="width: 30px;"><span class="filter-icon">⚙️</span></th>
                        <th>NAME</th>
                        <th>COMPANY NAME</th>
                        <th>EMAIL</th>
                        <th>WORK PHONE</th>
                        <th>RECEIVABLES (BCY)</th>
                        <th>UNUSED CREDITS (BCY)</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr class="customer-row" data-id="{{ $customer->id }}"
                                data-name="{{ $customer->display_name }}"
                                data-email="{{ $customer->email }}"
                                data-phone="{{ $customer->work_phone }}"
                                data-company="{{ $customer->company_name }}"
                                data-balance="{{ $customer->opening_balance }}"
                                data-currency="INR"
                                data-pan="{{ $customer->pan_number ?? 'FUFP2541T' }}"
                                data-payment_terms="{{ $customer->payment_terms }}"
                                data-portal_language="{{ $customer->portal_language }}"
                                data-type="{{ $customer->customer_type }}"
                                data-billingAddress="{{ $customer->billingAddress }}"
                                data-shippingAddress="{{ $customer->shippingAddress }}"
                                data-portal_language="{{ $customer->portal_language}}"
                                data-contacts="{{ $customer->contacts}}"
                                data-created_at="{{ $customer->created_at}}"
                                data-remarks="{{ $customer->remarks}}">
                                <td><input type="checkbox" /></td>
                                <td><a href="#" class="customer-link">{{ $customer->display_name }}</a></td>
                                <td>{{ $customer->company_name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->work_phone }}</td>
                                <td>₹{{ number_format($customer->opening_balance, 2) }}</td>
                                <td>₹0.00</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div class="modal-backdrop" id="customerModalBackdrop"></div>
    <div class="customer-modal" id="customerModal">
        <div class="customer-header d-flex align-items-center justify-content-between">
            <h4 class="mb-0 contact-name">Mr. vasanth s</h4>
            <div class="d-flex align-items-center gap-3">

                <button class="btn btn-outline-secondary edit_btn btn-sm" style="border-radius: 8px;">Edit</button>

                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        New Transaction
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Transaction 1</a></li>
                        <li><a class="dropdown-item" href="#">Transaction 2</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" style="border-radius: 8px;" data-bs-toggle="dropdown">
                        More
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Option 1</a></li>
                        <li><a class="dropdown-item" href="#">Option 2</a></li>
                    </ul>
                </div>
                <span class="close-modal fs-4" id="closeCustomerModal" role="button">&times;</span>
            </div>

    </div>

        <div class="customer-tabs">
            <div class="customer-tab active" data-tab="overview">Overview</div>
            <div class="customer-tab" data-tab="comments">Comments</div>
            <div class="customer-tab" data-tab="transactions">Transactions</div>
            <div class="customer-tab" data-tab="mails">Mails</div>
            <div class="customer-tab" data-tab="statement">Statement</div>
        </div>

        <div class="customer-content">
            <!-- Overview Tab Content -->
            <div id="overview-tab" class="tab-content active">
                <table class="info-table">
                    <tr>
                        <td style="width: 40%;">
                            <div class="contact-card">
                                <div class="contact-header">ifive</div>
                                    <div class="contact-info">
                                        <div class="contact-image"></div>
                                        <div class="contact-details">
                                        <div class="contact-name">Mr. vasanth s <span class="gear-icon">⚙</span></div>
                                        <div class="contact-email">vasanthlinga@gmail.com</div>
                                        <div class="contact-phone">📱 9500366117</div>
                                        <div class="contact-status">Portal invitation not accepted</div>
                                        <div class="contact-actions">
                                            <a href="#">Re-invite</a>
                                            <a href="#">Send Email</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <div class="section-header" onclick="toggleSection(this)">
                                    <span>ADDRESS</span>
                                    <span class="toggle-arrow">▼</span>
                                </div>
                                <div class="section-content">
                                    <div class="address-section1">
                                        <div class="address-title">Billing Address</div>
                                        <div class="billing-address">No Billing Address - New Address</div>
                                    </div>
                                    <div class="address-section1">
                                        <div class="address-title">Shipping Address</div>
                                        <div class="shipping-address">No Shipping Address - New Address</div>
                                    </div>
                                </div>
                            </div>

                            <div class="details-section">
                                <div class="details-header" onclick="toggleDetails(this)">
                                    <span>OTHER DETAILS</span>
                                    <span class="toggle-arrow">▼</span>
                                </div>
                                <div class="details-content">
                                    <table class="details-table">
                                        <tr>
                                            <td>Customer Type</td>
                                            <td id="customertype">Business</td>
                                        </tr>
                                        <tr>
                                            <td>Default Currency</td>
                                            <td id="currency">INR</td>
                                        </tr>
                                        <tr>
                                            <td>PAN</td>
                                            <td id="pannumber">FUFP2521T</td>
                                        </tr>
                                        <tr>
                                            <td>Portal Status</td>
                                            <td>
                                                <span class="status-indicator">Enabled</span>
                                                <span class="contact-count">(1 of 1 Contacts)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Portal Language</td>
                                            <td id="customer_language">English</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                              <div class="contact-persons-container">
                                    <div class="contact-persons-title" onclick="toggleContactSection(this)">
                                        <span>CONTACT PERSONS (1)</span>
                                        <span class="contact-toggle-icon">▼</span>
                                    </div>
                                    <div class="contact-persons-body">
                                        <div class="contact-person">
                                            <span class="contact-name">santh k</span>
                                            <span class="contact-phone">9500970811</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="portal-info-container">
                                    <div class="portal-description">
                                        Customer<br>
                                        Portal allows<br>
                                        your customers<br>
                                        to keep track<br>
                                        of all the<br>
                                        transactions<br>
                                        between them<br>
                                        and your<br>
                                        business.
                                    </div>
                                    <div>
                                        <a href="#" class="portal-learn-more">Learn More</a>
                                        <button class="portal-activate-btn">Enable Portal</button>
                                    </div>
                                </div>

                             <div class="record-info-wrapper">
                                <div class="record-info-heading" onclick="toggleRecordInfo(this)">
                                    <span>RECORD INFO</span>
                                    <span class="record-toggle-arrow">▼</span>
                                </div>
                                <div class="record-info-content">
                                    <table class="record-details">
                                        <tr>
                                            <td>Customer ID</td>
                                            <td class="customerid">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Created On</td>
                                            <td class="created_date">19/07/2025</td>
                                        </tr>
                                        <tr>
                                            <td>Created By</td>
                                            <td>
                                                <span>santhk070</span>
                                                <span class="record-id-line">8</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                        <td style="width: 60%;">
                            <div class="mb-3">
                                <div class="section-title">Payment due period</div>
                                <div class="cust_payment_term">Net 15</div>
                            </div>

                            <div class="mb-3">
                                <div class="section-title">Receivables</div>
                            </div>

                            <table class="table table-bordered mb-3">
                                <thead>
                                    <tr>
                                        <th>CURRENCY</th>
                                        <th>OUTSTANDING RECEIVABLES</th>
                                        <th>UNUSED CREDITS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="customer_currency">INR- Indian Rupee</td>
                                        <td id="customer-balance">₹10,000.00</td>
                                        <td>₹0.00</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="mb-3">
                                <a href="#" class="btn btn-link p-0">View Opening Balance</a>
                            </div>

                            <div class="mb-3">
                                <div class="section-title">Income</div>
                                <p class="small text-muted mb-1">This chart is displayed in the organization's base currency.</p>
                                <p class="small text-muted mb-2">Last 6 Months - Accrual -</p>

                                <!-- Chart Container -->
                                <div class="chart-container mb-3" style="height: 200px; position: relative;">
                                    <canvas id="incomeChart"></canvas>
                                </div>

                                <div id="total-income">Total Income (Last 6 Months) - ₹10,000.00</div>
                            </div>

                            <div class="timeline-container">
                                <div class="timeline-item">
                                    <div class="timeline-date">21/07/2025<br>09:58 AM</div>
                                    <div class="timeline-content">
                                    <div class="timeline-event">Contact updated</div>
                                    <div class="timeline-meta">Contact updated by santhk0708</div>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-date">19/07/2025<br>09:50 AM</div>
                                    <div class="timeline-content">
                                    <div class="timeline-event">added</div>
                                    <div class="timeline-meta">Opening Balance of amount ₹1,000.00 created. by santhk0708</div>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-date">19/07/2025<br>09:50 AM</div>
                                    <div class="timeline-content">
                                    <div class="timeline-event">Contact added</div>
                                    <div class="timeline-meta">Contact created by santhk0708</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Other tabs (initially hidden) -->
            <div id="comments-tab" class="tab-content" style="display: none;">
                Comments content goes here
            </div>
            <div id="transactions-tab" class="tab-content" style="display: none;">
                Transactions content goes here
            </div>
            <div id="mails-tab" class="tab-content" style="display: none;">
                Mails content goes here
            </div>
            <div id="statement-tab" class="tab-content" style="display: none;">
                Statement content goes here
            </div>
        </div>


    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('/assets/js/purchase/vendor.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#exampleModal').modal('show');
    });
</script>
@endif

<script>
    $(document).ready(function() {
        var customerId="";
        // Handle row click
        $('.customer-row').click(function() {
            // Highlight selected row
            $('.customer-row').removeClass('selected');
            $(this).addClass('selected');

            // Get customer data from data attributes
             customerId = $(this).data('id');
            const customerName = $(this).data('name');
            const customerEmail = $(this).data('email');
            const customerPhone = $(this).data('phone');
            const customerCompany = $(this).data('company');
            const customerBalance = $(this).data('balance');
            const customerCurrency = $(this).data('currency');
            const customerPan = $(this).data('pan');
            const customerType = $(this).data('type');
            const customerpayment_terms= $(this).data('payment_terms');
            const customerLanguage = $(this).data('portal_language');
            const billing_address = JSON.parse($(this).attr('data-billingaddress'));
            const shippingAddress = JSON.parse($(this).attr('data-shippingaddress'));
            const contacts = JSON.parse($(this).attr('data-contacts'));
            const created_at = $(this).data('created_at');
            const created_date = created_at.split(" ")[0];
            const custype=(customerType==0)?'Business':'Individual';
            const billingaddress = `
                ${billing_address.address}</br>
                ${billing_address.city}</br>
                ${billing_address.state}</br>
                ${billing_address.country}</br>
                ${billing_address.zip_code}</br>
                ${billing_address.phone}
            `;
            const shipping_address = `
                ${shippingAddress.address}</br>
                ${shippingAddress.city}</br>
                ${shippingAddress.state}</br>
                ${shippingAddress.country}</br>
                ${shippingAddress.zip_code}</br>
                ${shippingAddress.phone}
            `;
            let contact = ""; // Use let instead of const

            contacts.forEach(element => {
                contact += `
                    <div class="contact-person">
                        <span class="contact-name">${element.first_name} ${element.last_name}</span>
                        <span class="contact-phone">${element.mobile}</span>
                    </div>
                `;
            });

            // Update modal content
            $('.contact-header').text(customerCompany);
            $('.contact-name').text(customerName);
            $('.contact-email').text(customerEmail);
            $('.contact-phone').text(customerPhone);
            $('.created_date').text(created_date);
            $('.customerid').text(customerId);
            $('.cust_payment_term').text(customerpayment_terms);
            $('.billing-address').html(billingaddress);
            $('.shipping-address').html(shipping_address);
            $('.contact-persons-body').html(contact);
            $('#customertype').text(custype);
            $('#customer-balance').text('₹' + customerBalance.toFixed(2));
            $('#customer-currency-display').text(customerCurrency + ' - ' + (customerCurrency === 'INR' ? 'Indian Rupee' : ''));
            $('#pannumber').text(customerPan);
            $('#currency').text(customerCurrency);
            $('.customer_currency').text(customerCurrency);
            $('#customer_language').text(customerLanguage);
            $('#customer-type').text(customerType);
            $('#total-income').text('Total Income (Last 6 Months) - ₹' + customerBalance.toFixed(2));
            updateIncomeChart(customerBalance);
            // Show modal
            $('#customerModal').show();
            $('#customerModalBackdrop').show();
            $('body').css('overflow', 'hidden');
        });
       $('.edit_btn').on('click', function() {
            window.location.href = "{{ route('superadmin.getcustomercreate') }}" + "?id=" + customerId;
        });


        // Close modal
        $('#closeCustomerModal, #customerModalBackdrop').click(function() {
            $('#customerModal').hide();
            $('#customerModalBackdrop').hide();
            $('body').css('overflow', 'auto');
        });

        // Tab switching
        $('.customer-tab').click(function() {
            const tabId = $(this).data('tab');

            // Update active tab
            $('.customer-tab').removeClass('active');
            $(this).addClass('active');

            // Show corresponding content
            $('.tab-content').hide();
            $('#' + tabId + '-tab').show();
        });

        // Prevent modal close when clicking inside
        $('#customerModal').click(function(e) {
            e.stopPropagation();
        });
    });
        let incomeChart = null;

        function updateIncomeChart(balance) {
            const ctx = document.getElementById('incomeChart');

            // Sample data - replace with your actual data
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            const amounts = [0, 0, 0, 0, 0, balance/1000]; // Assuming last month has the balance

            // If chart exists, update its data
            if (incomeChart) {
                incomeChart.data.datasets[0].data = amounts;
                incomeChart.update();
                return;
            }

            // Otherwise, create new chart
            incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Income',
                        data: amounts,
                        backgroundColor: '#4a6cf7',
                        borderColor: '#4a6cf7',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value + 'k';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₹' + (context.raw * 1000).toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }
        $('#closeCustomerModal, #customerModalBackdrop').click(function() {
            // Clean up chart if it exists
            if (incomeChart) {
                incomeChart.destroy();
                incomeChart = null;
            }

            $('#customerModal').hide();
            $('#customerModalBackdrop').hide();
            $('body').css('overflow', 'auto');
        });
        function toggleSection(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.toggle-arrow');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Rotate arrow
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }
         function toggleDetails(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.toggle-arrow');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Rotate arrow
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }
        function toggleContactSection(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.contact-toggle-icon');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Update arrow icon
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }
          function toggleRecordInfo(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.record-toggle-arrow');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Update arrow icon
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }
    const vendorsaveUrl = "{{ route('superadmin.vendorsave') }}";
    const vendorfetch = "{{ route('superadmin.vendorfetch') }}";
</script>

    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>