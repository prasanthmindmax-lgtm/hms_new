<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bills & Payments</title>
  <style>
      .section {
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-bottom: 15px;
        overflow: hidden;
    }
    .transcation-header {
        background: #f5f7fb;
        padding: 10px 15px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.3s ease;
    }
    .transcation-header:hover {
        background: #e9ecf3;
    }
    .transcation-content {
        display: none;
        padding: 10px;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .transcation-content.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    table th, table td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    table th {
        background: #f0f0f0;
    }
    .status-overdue {
        color: red;
        font-weight: bold;
    }
    .pagination {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .pagination a {
        padding: 5px 10px;
        margin: 0 3px;
        border: 1px solid #ccc;
        background: #fff;
        cursor: pointer;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
    }
    .pagination .active span {
        background: #007bff;
        color: #fff;
        border-radius: 4px;
        padding: 5px 10px;
    }
    .new-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
    }
    .chevron-icon {
        transition: transform 0.3s ease;
    }
    .chevron-icon.rotated {
        transform: rotate(180deg);
    }
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }
    .table-container {
        position: relative;
        min-height: 100px;
    }
    .loader {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 2s linear infinite;
    }
    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    .trans{
        display: flex;
        flex-direction: column;
        gap: 25px;
    }
    .pagination-link {
        cursor: pointer;
    }
    .pagination-link.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
  </style>
</head>
<body>

<div class="container mt-4 trans">
    {{-- Quotations --}}
    @if(isset($TblQuotation) || (isset($type) && $type == 'quotation'))
    <div class="transcation" id="quotation-section">
        <div class="transcation-header">
            {{-- <span>Quotations</span> --}}
            <div>
                <span>Quotations</span>
                <div class="bill_show">
                    <div style="color: purple;">
                        <span style="">Count :</span><span>{{$quotationCount}}</span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('superadmin.getquotationcreate')}}"><button class="new-btn">+ New</button></a>
            </div>
        </div>
        <div class="transcation-content">
            <div class="table-container" id="quotation-table-container">
                <div class="loader" id="quotation-loader"></div>
                <div id="quotation-table">
                    @include('vendor.transactionvendor.partials.quotation_table',['TblQuotation' => $TblQuotation, 'perPage' => $perPage])
                </div>
                @if($TblQuotation->total() > $TblQuotation->perPage())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="ajax-pagination" data-type="quotation">
                            {{-- Laravel pagination still needed for page data --}}
                            {!! $TblQuotation->appends(['id' => $id ?? request('id'), 'type' => 'quotation'])->links('pagination::bootstrap-4') !!}
                        </div>

                        <div>
                            <select name="per_page" id="per_page" class="form-control form-control-sm per-page-select" style="width: 70px; display: inline-block;">
                                @foreach([10, 25, 50, 100,250,500] as $size)
                                    <option value="{{ $size }}" {{ (isset($perPage) && $perPage == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <span>entries</span>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Purchase Orders --}}
    @if(isset($TblPurchaseorder) || (isset($type) && $type == 'purchase'))
    <div class="transcation" id="purchase-section">
        <div class="transcation-header">
            {{-- <span>Purchase Orders</span> --}}
            <div>
                <span>Purchase Orders</span>
                <div class="bill_show">
                    <div style="color: purple;">
                        <span style="">Count :</span><span>{{$purchaseOrderCount}}</span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('superadmin.getpurchasecreate')}}"><button class="new-btn">+ New</button></a>
            </div>
        </div>
        <div class="transcation-content">
            <div class="table-container" id="purchase-table-container">
                <div class="loader" id="purchase-loader"></div>
                <div id="purchase-table">
                    @include('vendor.transactionvendor.partials.purchase_table',['TblPurchaseorder' => $TblPurchaseorder, 'perPage' => $perPage])
                </div>
                @if($TblPurchaseorder->total() > $TblPurchaseorder->perPage())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="ajax-pagination" data-type="purchase">
                            {{-- Laravel pagination still needed for page data --}}
                            {!! $TblPurchaseorder->appends(['id' => $id ?? request('id'), 'type' => 'purchase'])->links('pagination::bootstrap-4') !!}
                        </div>

                        <div>
                            <select name="per_page" id="per_page" class="form-control form-control-sm per-page-select" style="width: 70px; display: inline-block;">
                                @foreach([10, 25, 50, 100,250,500] as $size)
                                    <option value="{{ $size }}" {{ (isset($perPage) && $perPage == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <span>entries</span>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Bills --}}
    @if(isset($Tblbill) || (isset($type) && $type == 'bill'))
    <div class="transcation" id="bill-section">
        <div class="transcation-header">
            <div>
                <span>Bills</span>
                <div class="bill_show">
                    <div style="color: purple;">
                        <span style="">Count :</span><span>{{$billcount}}</span>
                    </div>
                    <div style="color: brown;">
                        <span>Total Bill Amount : </span><span> ₹{{ number_format($totalAmountSum, 2) }}</span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('superadmin.getbillcreate')}}"><button class="new-btn">+ New</button></a>
            </div>
        </div>
        <div class="transcation-content">
            <div class="table-container" id="bill-table-container">
                <div class="loader" id="bill-loader"></div>
                <div id="bill-table">
                    @include('vendor.transactionvendor.partials.bill_table',['Tblbill' => $Tblbill, 'perPage' => $perPage])
                </div>
                @if($Tblbill->total() > $Tblbill->perPage())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="ajax-pagination" data-type="bill">
                            {{-- Laravel pagination still needed for page data --}}
                            {!! $Tblbill->appends(['id' => $id ?? request('id'), 'type' => 'bill'])->links('pagination::bootstrap-4') !!}
                        </div>

                        <div>
                            <select name="per_page" id="per_page" class="form-control form-control-sm per-page-select" style="width: 70px; display: inline-block;">
                                @foreach([10, 25, 50, 100,250,500] as $size)
                                    <option value="{{ $size }}" {{ (isset($perPage) && $perPage == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <span>entries</span>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Bill Payments --}}
    @if(isset($Tblbillpay) || (isset($type) && $type == 'billpay'))
    <div class="transcation" id="billpay-section">
        <div class="transcation-header">
            {{-- <span>Bill Payments</span> --}}
            <div>
                <span>Bill Payments</span>
                <div class="bill_show">
                    <div style="color: purple;">
                        <span style="">Count :</span><span>{{$billPayCount}}</span>
                    </div>
                    <div style="color: brown;">
                        <span>Total Paid Amount : </span><span> ₹{{ number_format($billtotalAmountSum, 2) }}</span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('superadmin.getbillmadecreate')}}"><button class="new-btn">+ New</button></a>
            </div>
        </div>
        <div class="transcation-content">
            <div class="table-container" id="billpay-table-container">
                <div class="loader" id="billpay-loader"></div>
                <div id="billpay-table">
                    @include('vendor.transactionvendor.partials.billpay_table',['Tblbillpay' => $Tblbillpay, 'perPage' => $perPage])
                </div>
                @if($Tblbillpay->total() > $Tblbillpay->perPage())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="ajax-pagination" data-type="billpay">
                            {{-- Laravel pagination still needed for page data --}}
                            {!! $Tblbillpay->appends(['id' => $id ?? request('id'), 'type' => 'billpay'])->links('pagination::bootstrap-4') !!}
                        </div>

                        <div>
                            <select name="per_page" id="per_page" class="form-control form-control-sm per-page-select" style="width: 70px; display: inline-block;">
                                @foreach([10, 25, 50, 100,250,500] as $size)
                                    <option value="{{ $size }}" {{ (isset($perPage) && $perPage == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <span>entries</span>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    @endif

    {{-- GRN --}}
    @if(isset($Tblgrn) || (isset($type) && $type == 'grn'))
    <div class="transcation" id="grn-section">
        <div class="transcation-header">
            {{-- <span>Goods Receipt Notes (GRN)</span> --}}
            <div>
                <span>Goods Receipt Notes (GRN)</span>
                <div class="bill_show">
                    <div style="color: purple;">
                        <span style="">Count :</span><span>{{$grnCount}}</span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('superadmin.getgrncreate')}}"><button class="new-btn">+ New</button></a>
            </div>
        </div>
        <div class="transcation-content">
            <div class="table-container" id="grn-table-container">
                <div class="loader" id="grn-loader"></div>
                <div id="grn-table">
                    @include('vendor.transactionvendor.partials.grn_table',['Tblgrn' => $Tblgrn, 'perPage' => $perPage])
                </div>
                @if($Tblgrn->total() > $Tblgrn->perPage())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="ajax-pagination" data-type="grn">
                            {{-- Laravel pagination still needed for page data --}}
                            {!! $Tblgrn->appends(['id' => $id ?? request('id'), 'type' => 'grn'])->links('pagination::bootstrap-4') !!}
                        </div>

                        <div>
                            <select name="per_page" id="per_page" class="form-control form-control-sm per-page-select" style="width: 70px; display: inline-block;">
                                @foreach([10, 25, 50, 100,250,500] as $size)
                                    <option value="{{ $size }}" {{ (isset($perPage) && $perPage == $size) ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <span>entries</span>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    @endif

    <div class="transcation" id="billpay-section">
        <div class="transcation-header">
            <span>Outstanding Bill Payables</span>
        </div>

        <div class="transcation-content">
            {{-- 🧾 Summary Section --}}
            <div class="bill-summary">
                <div class="summary-card due">
                    <span class="label">Due Amount</span>
                    <span class="value">₹{{ number_format($dueAmountSum, 2) }}</span>
                </div>
                <div class="summary-card partial">
                    <span class="label">Partially Paid</span>
                    <span class="value">₹{{ number_format($partialPaidSum, 2) }}</span>
                </div>
                <div class="summary-card total">
                    <span class="label">Total Amount</span>
                    <span class="value">₹{{ number_format($totalAmountSum, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    const vendorId = {{ $id ?? 0 }};

    // Toggle expand/collapse
    $(".transcation-header").on("click", function () {
        const $content = $(this).next(".transcation-content");
        if ($content.hasClass("active")) {
            $content.removeClass("active").slideUp(300);
        } else {
            $(".transcation-content.active").removeClass("active").slideUp(300);
            $content.slideDown(300).addClass("active");
        }
    });

    // === AJAX Pagination Handler ===
    $('.ajax-pagination').on('click', '.pagination a', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const $link = $(this);
        const $paginationContainer = $link.closest(".ajax-pagination");
        const tableType = $paginationContainer.data("type");
        const href = $link.attr("href");
        const urlParams = new URL(href, window.location.origin).searchParams;
        const page = urlParams.get(`${tableType}_page`) || urlParams.get("page") || 1;

        const $tableContainer = $(`#${tableType}-table-container`);
        const $loader = $(`#${tableType}-loader`);

        $loader.show();
        $tableContainer.addClass("loading");

        $.ajax({
            url: "{{ route('superadmin.gettranscationvendorpagination') }}",
            type: "GET",
            data: {
                id: vendorId,
                type: tableType,
                page: page,
                per_page: $(`#${tableType}-table-container .per-page-select`).val() || 10
            },
            headers: { "X-Requested-With": "XMLHttpRequest" },
            success: function (response) {
                // Replace only table rows
                $(`#${tableType}-table`).html(response.html);

                // Update pagination UI correctly
                $paginationContainer.html(response.pagination);

                $loader.hide();
                $tableContainer.removeClass("loading");
            },
            error: function (xhr) {
                console.error("❌ Pagination AJAX error", xhr);
                $loader.hide();
                $tableContainer.removeClass("loading");
            }
        });
    });


    // === AJAX Per-page dropdown change ===
    $(document).on("change", ".per-page-select", function (e) {
        e.preventDefault();

        const $select = $(this);
        const perPage = $select.val();
        const $tableContainer = $select.closest(".table-container");
        const containerId = $tableContainer.attr("id");
        const tableType = containerId.replace("-table-container", "");
        const $loader = $(`#${tableType}-loader`);

        $loader.show();
        $tableContainer.addClass("loading");

        $.ajax({
            url: "{{ route('superadmin.gettranscationvendorpagination') }}",
            type: "GET",
            data: {
                id: vendorId,
                type: tableType,
                page: 1,
                per_page: perPage
            },
            headers: { "X-Requested-With": "XMLHttpRequest" },
            success: function (response) {
                $(`#${tableType}-table`).html(response.html);
                $loader.hide();
                $tableContainer.removeClass("loading");
            },
            error: function (xhr) {
                console.error("❌ PerPage AJAX error", xhr);
                $loader.hide();
                $tableContainer.removeClass("loading");
                toastr.error("Error loading data.");
            }
        });
    });
});

</script>

</body>
</html>