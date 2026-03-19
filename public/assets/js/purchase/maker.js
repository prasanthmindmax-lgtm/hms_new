$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    // Clear error span text on input
    $('input, select, textarea').on('input change', function () {
        $(this).siblings('span').text('');
    });

    $('#close_button').click(function () {
        // Clear all input fields
        $('input, select, textarea').val('');
        // Clear error messages
        $('span').text('');
    });

    $('#submit-neft-datas').click(function (event) {
        event.preventDefault();
        let isValid = true;

        if ($('#serial_number').val() === "") {
            $('.error_serial').text('Serial Required');
            isValid = false;
        }
        if ($('#created_by').val() === "") {
            $('.error_created_by').text('Enter the Creator Name');
            isValid = false;
        }
        if ($('#vendor').val() === "") {
            $('.error_vendor').text('Please select the Employee Name or Vendor');
            isValid = false;
        }

        if ($('#nature_payment').val() === "") {
            $('.error_nature_payment').text('Enter the Nature Payment');
            isValid = false;
        }
        if ($('#invoice_amount').val() === "") {
            $('.error_invoice_amount').text('Enter the Invoice Amount');
            isValid = false;
        }

        if ($('#payment_status').val() === "") {
            $('.error_payment_status').text('Select Payment Status');
            isValid = false;
        }

        if ($('#utr_number').val() === "") {
            $('.error_utr_number').text('Enter UTR Number');
            isValid = false;
        }

        let paymentStatusChecked = false;
        $('input[name="payment_method[]"]:checked').each(function() {
            paymentStatusChecked = true;
        });
        if (!paymentStatusChecked) {
            $('.error_payment_method').text('Select at least one payment method');
            isValid = false;
        }

        // PAN number validation (e.g. ABCDE1234F)
        let panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        let panNumber = $('#pan_number').val().toUpperCase();
        if (!panPattern.test(panNumber)) {
            $('.error_pan_number').text('Invalid PAN Number');
            isValid = false;
        } else {
            $('.error_pan_number').text('');
        }

        // IFSC code validation (e.g. SBIN0001234)
        let ifscPattern = /^[A-Z]{4}0[A-Z0-9]{6}$/;
        let ifscCode = $('#ifsc_code').val().toUpperCase();
        if (!ifscPattern.test(ifscCode)) {
            $('.error_ifsc_code').text('Invalid IFSC Code');
            isValid = false;
        } else {
            $('.error_ifsc_code').text('');
        }

        function isFileTooLarge(inputId, errorClass) {
                const file = $(inputId)[0].files[0];
                if (file && file.size > 5242880) {
                    $(errorClass).text('File size must be less than 5MB');
                    return true;
                }
                $(errorClass).text('');
                return false;
            }

            // Apply file size check
            if (isFileTooLarge('#pan_upload', '.error_pan_upload')) isValid = false;
            if (isFileTooLarge('#bank_upload', '.error_bank_upload')) isValid = false;
            if (isFileTooLarge('#invoice_upload', '.error_invoice_upload')) isValid = false;
            if (isFileTooLarge('#po_upload', '.error_po_upload')) isValid = false;
            if (isFileTooLarge('#po_signed_upload', '.error_po_signed_upload')) isValid = false;
            if (isFileTooLarge('#po_delivery_upload', '.error_po_delivery_upload')) isValid = false;


        console.log("isValid",isValid);
        if (!isValid) {
            return;
        }


        // Create FormData object
        let formData = new FormData();
        formData.append('id', $('#id').val());
        formData.append('user_id', $('#users_id').val());
        formData.append('branch_id', $('#branch_id').val());
        formData.append('serial_number', $('#serial_number').val());
        formData.append('created_by', $('#created_by').val());
        formData.append('vendor', $('#vendor').val());
        formData.append('nature_payment', $('#nature_payment').val());
        formData.append('invoice_amount', $('#invoice_amount').val());
        formData.append('already_paid', $('#already_paid').val());
        formData.append('pan_number', $('#pan_number').val());
        formData.append('account_number', $('#account_number').val());
        formData.append('ifsc_code', $('#ifsc_code').val());
        formData.append('payment_status', $('#payment_status').val());
        formData.append('existing_pan_file', $('#existing_pan_file').val());
        formData.append('existing_bank_upload', $('#existing_bank_upload').val());
        formData.append('existing_invoice_upload', $('#existing_invoice_upload').val());
        formData.append('existing_po_upload', $('#existing_po_upload').val());
        formData.append('existing_po_signed_upload', $('#existing_po_signed_upload').val());
        formData.append('existing_po_delivery_upload', $('#existing_po_delivery_upload').val());
        let paymentMethods = [];
        $('input[name="payment_method[]"]:checked').each(function() {
            paymentMethods.push($(this).val());
        });
        paymentMethods.forEach((method, index) => {
            formData.append(`payment_method[${index}]`, method);
        });
        formData.append('utr_number', $('#utr_number').val());

        // Add payment_status
        $('input[name="payment_status[]"]:checked').each(function() {
            formData.append('payment_status[]', $(this).val());
        });

        // Add file uploads
       // Append all files from each input
        function appendMultipleFiles(formData, inputId, fieldName) {
            const files = $('#' + inputId)[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append(fieldName + '[]', files[i]); // Use array syntax
            }
        }
        appendMultipleFiles(formData, 'bank_upload', 'bank_upload');
        appendMultipleFiles(formData, 'invoice_upload', 'invoice_upload');
        appendMultipleFiles(formData, 'pan_upload', 'pan_upload');
        appendMultipleFiles(formData, 'po_upload', 'po_upload');
        appendMultipleFiles(formData, 'po_signed_upload', 'po_signed_upload');
        appendMultipleFiles(formData, 'po_delivery_upload', 'po_delivery_upload');


        // Include CSRF Token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // AJAX Request
        $.ajax({
            url: purchasesaveUrl, // Update with your actual endpoint
            type: "POST",
            data: formData,
            processData: false, // Prevent processing of the data
            contentType: false, // Prevent setting content-type header
            success: function (response) {
                if (response.success) {
                    location.reload(); // Optional: Refresh the page
                }
            },
            error: function (error) {
                if (error.responseJSON && error.responseJSON.errors) {
                    // Display validation errors
                    $.each(error.responseJSON.errors, function(key, value) {
                        $('.error_' + key).text(value[0]);
                    });
                } else {
                    console.error(error.responseJSON);
                }
            },
        });
    });
});





$(document).ready(function() {
    // Fetch data and initialize pagination
    overall_fetch();
    $(".search_view").hide();
    var fitterremovedata = []; // Keep this variable persistent
    var marketersearchvalue = [];



    $(document).on('click', '.value_views,.value_views_mainsearch', function() {

            //alert(marketersearchvalue);
           // alert(fitterremovedata);
        var morefillterremvedata = $(this).text();

        var datefilltervalue = $('#dateallviews').text();
        $(this).text("");
            $('input[type="checkbox"]').each(function() {
                if (morefillterremvedata.includes($(this).val())) {
                    $(this).prop('checked', false); // Uncheck the checkbox
                }
            });

        $('.morefittersclr').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");

        $('.marketervalues_search').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");

       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.endsWith(morefillterremvedata + "'");
    });
    const checkboxes = document.querySelectorAll('.multiselect-options input[type="checkbox"]');
          morefilltersremoveviews(fitterremovedata,datefilltervalue);

        //alert(fitterremovedata);
    });
    $(document).on("click", ".clear_all_views", function () {
        $('.morefittersclr').val("")
        $('.marketervalues_search').val("")
        $('.clear_all_views').hide();
        $(".search_view").hide();
        $(".value_views").text("");
        $(".value_views_mainsearch").text("");
        var datefilltervalue = $('#dateallviews').text();
        cleardatemore(datefilltervalue);
    });
    $(".value_edit").on("click",function(){
        $(".value_edit").hide();
        overall_fetch();
    });
    $(".mainclearall").on("click",function(){
        $('.morefittersclr').val("")
        $('.clear_all_views').hide();
        $(".search_view").hide();
        $(".value_views").text("");
        $('input[type="checkbox"]').prop('checked', false);
        $('#special_more').val('');
    });


    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var pageSize = parseInt($(this).val());
        renderPagination(dataSource, pageSize);
        renderTable(dataSource, pageSize, 1);  // Initially show the first page
    });
    $(document).on('click', '.ranges', function() {
        var datefilltervalue = $('#dateallviews').text(); // Get the current text value when '.ranges' is clicked
        var morefitterempty=$(".value_views").text();
        var morefitterempty_market=$(".value_views_mainsearch").text();

        if (morefitterempty !== '' || morefitterempty_market !== '') {

            fitterremovedatafitters = (morefitterempty === '' || morefitterempty_market === '') ? fitterremovedata: [...new Set(fitterremovedata.concat(marketersearchvalue))];

            dateandoveralldata(fitterremovedatafitters, datefilltervalue);

        } else {

            datefillterrange(datefilltervalue);
        }

    });

    $(document).on('click', '.options_marketers div', function (e) {
        // Get the selected value and text
        var selectedValue = $(this).val();
        var selectedText = $(this).text();
        var moredatefittervale = $('#dateallviews').text();

        $('.clear_all_views').show();
        $(".search_view").show();


        var resultsArray_marketer = [];
        $(".marketervalues_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return;
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#marketer_fetch").val(),
                $("#branchviews").val(),
                $("#zoneviews").val(),
                $("#special").val(),

            ];
            // alert(moreFilterValues_market);

            $(".value_views_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });

                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
                morefilterview(fitterremovedata,moredatefittervale);

        });

});
var dataSource = [];  // Data will be fetched here
// Fetch the data and initialize pagination
function overall_fetch() {
    $("#doctor_details1").show();

    $.ajax({
        url: purchasefetchUrl,
        type: "GET",
        success: function (responseData) {
            console.log(responseData);
            $("#doctor_details1").hide();
            $("#doctor_details").show();
            dataSource = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            renderPagination(dataSource, pageSize);
            renderTable(dataSource, pageSize, 1); // Show first page initially
            slct="";
            $.each(dataSource, function(index, user1) {

                slct += `<div data-value="${user1.doctor_name}">${user1.doctor_name}</div>`;


            });
            $(".meeting-doctorname").html(slct);
            $(".patient-doctorname").html(slct);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}


// date fitter function
function datefillterrange(datefiltervalue) {
    currentFilter = datefiltervalue;

    $("#doctor_details1").show();
    $.ajax({
        url: purchasefetchdatefilter,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
            type:1
        },
        success: function (responseData) {
            $("#doctor_details1").hide();
            $("#doctor_details").show();
            dataSource = responseData;
            totalItems = responseData.length; // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            renderPagination(dataSource, pageSize);
            renderTable(dataSource, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}
function morefilterview(fitterremovedata,moredatefittervale) {

   console.log("fitterremovedata",fitterremovedata);
   console.log("moredatefittervale",moredatefittervale);


    $.ajax({
        url: fetchUrlmorefitterpurchase,
        type: "GET",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        data: {
                fitterremovedata: fitterremovedata,
                moredatefittervale: moredatefittervale,
                type: 1
            },
        success: function (responseData) {
            $("#doctor_details1").hide();
            $("#doctor_details").show();
            dataSource = responseData;
            totalItems = responseData.length; // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            renderPagination(dataSource, pageSize);
            renderTable(dataSource, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}
function  morefilltersremoveviews(fitterremovedata,datefilltervalue)
{

    if(fitterremovedata!=''){

        $.ajax({
            url: fetchUrlmorefitterremove,
            type: "GET",
            data: {
                fitterremovedataall: fitterremovedata,
                datefilltervalue:datefilltervalue,
            },
            success: function (responseData) {
                $("#doctor_details1").hide();
                $("#doctor_details").show();
                dataSource = responseData;
                totalItems = responseData.length; // Set the data fetched from the server
                var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
                renderPagination(dataSource, pageSize);
                renderTable(dataSource, pageSize, 1); // Show first page initially
            },
            error: function (xhr, status, error) {
                $("#doctor_details1").hide();
                console.error("AJAX Error:", status, error);
            }
        });
      }
      else
      {
        $('.clear_all_views').hide();
        $(".search_view").hide();
        cleardatemore(datefilltervalue);
      }
}
function dateandoveralldata(fitterremovedatafitters, datefilltervalue)
{
   // alert(fitterremovedatafitters);
    //alert(datefilltervalue);

    fitterremovedata=fitterremovedatafitters;
   $.ajax({
    url: fetchUrlmorefitterdate,
    type: "GET",
    data: {
        fitterremovedata: fitterremovedata,
        datefilltervalue:datefilltervalue,
    },
    success: function (responseData) {
        $("#doctor_details1").hide();
        $("#doctor_details").show();
        dataSource = responseData;
        totalItems = responseData.length; // Set the data fetched from the server
        var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
        renderPagination(dataSource, pageSize);
        renderTable(dataSource, pageSize, 1); // Show first page initially
    },
    error: function (xhr, status, error) {
        $("#doctor_details1").hide();
        console.error("AJAX Error:", status, error);
    }
  });
}
function cleardatemore(datefilltervalue)
{
    // alert(fetchUrlmorefitterdateclear);
    $.ajax({
        url: fetchUrlmorefitterdateclearpurchase,
        type: "GET",
        data: {
            datefilltervalue: datefilltervalue,
            type:1
        },
        success: function (responseData) {
            $("#doctor_details1").hide();
            $("#doctor_details").show();
            dataSource = responseData;
            totalItems = responseData.length; // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            renderPagination(dataSource, pageSize);
            renderTable(dataSource, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
      });
}


// Render table rows based on the page and page size
function renderTable(data, pageSize, pageNum) {

    let count = data.length;
    var startIdx = (pageNum - 1) * pageSize;
    var endIdx = pageNum * pageSize;
    var pageData = data.slice(startIdx, endIdx);
    console.log(pageData);
    var body = "";
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY | HH:MM");

        body += '<tr class="tdview" onclick="rowClick(event)" style="cursor:pointer;" data-id="' + user.id + '">' +
                '<td class="tdview" style="display:none" id="id" data-id="' + (user.id ?? '-') +'">' + (user.id ?? '-') + '</td>' +
                '<td class="tdview" style="display:none" id="user_id" data-user_id="' + (user.user_id ?? '-') + '">' + (user.user_id ?? '-') + '</td>' +
                '<td class="tdview" style="display:none" id="utr_number1" data-utr_number="' + (user.utr_number ?? '-') +'">' + (user.utr_number ?? '-') + '</td>' +
                '<td class="tdview" style="display:none" id="pan_number1" data-pan_number="' + (user.pan_number ?? '-') +'">' + (user.pan_number ?? '-') + '</td>' +
                '<td class="tdview" id="serial_number1" data-serial_number="' + (user.serial_number ?? '-') +'">' + (user.serial_number ?? '-') + '</td>' +
                '<td class="tdview" id="created_by1" data-created_by="' + (user.created_by ?? '-') +'">' + (user.created_by ?? '-') + '</td>' +
                '<td class="tdview" id="vendor1" data-vendor="' + (user.vendor ?? '-') +'">' + (user.vendor ?? '-') + '</td>' +
                '<td class="tdview" id="nature_payment1" data-nature_payment="' + (user.nature_payment ?? '-') +'">' + (user.nature_payment ?? '-') + '</td>' +
                '<td class="tdview" id="payment_status1" data-payment_status="' + (user.payment_status ?? '-') +'">' + (user.payment_status ?? '-') + '</td>' +
                '<td class="tdview" id="account_number1" data-account_number="' + (user.account_number ?? '-') +'">' + (user.account_number ?? '-') + '</td>' +
                '<td class="tdview" id="ifsc_code1" data-ifsc_code="' + (user.ifsc_code ?? '-') +'">' + (user.ifsc_code ?? '-') + '</td>' +
                '<td class="tdview" id="invoice_amount1" data-invoice_amount="' + (user.invoice_amount ?? '-') +'">' + (user.invoice_amount ?? '-') + '</td>' +
                '<td class="tdview" id="already_paid1" data-already_paid="' + (user.already_paid ?? '-') +'">' + (user.already_paid ?? '-') + '</td>' +
                '<td class="tdview" id="checker_status1" data-checker_status="' + (user.checker_status ?? '-') + '">' +
                    (user.checker_status == 0
                        ? '<button class="btn btn-sm btn-danger">Pending</button>'
                        : '<button class="btn btn-sm btn-success">Success</button>') +
                '</td>' +
                '<td class="tdview" id="checker_status1" data-checker_status="' + (user.approval_status ?? '-') + '">' +
                    (user.approval_status == 0
                        ? '<button class="btn btn-sm btn-danger">Pending</button>'
                        : '<button class="btn btn-sm btn-success">Success</button>') +
                '</td>' +
                '<td class="tdview" id="payment_method1" style="display:none" data-payment_method="' + (user.payment_method ?? '-') +'">' + (user.payment_method ?? '-') + '</td>' +
                '<td style="display:none;" id="pan_upload1" class="tdview" data-filetype="pan" data-files=\'' + JSON.stringify(user.pan_upload) + '\'></td>' +
                '<td style="display:none;" class="tdview documentclk" data-filetype="pan"><img src="../assets/images/doc.png" style="width: 30px;" alt="Icon" class="icon"></td>' +

                '<td style="display:none;" id="invoice_upload1" class="tdview" data-filetype="invoice" data-files=\'' + JSON.stringify(user.invoice_upload) + '\'></td>' +
                '<td style="display:none;" class="tdview documentclk" data-filetype="invoice"><img src="../assets/images/doc.png" style="width: 30px;" alt="Icon" class="icon"></td>' +

                '<td style="display:none;" id="bank_upload1" class="tdview" data-filetype="invoice" data-files=\'' + JSON.stringify(user.bank_upload) + '\'></td>' +
                '<td style="display:none;" class="tdview documentclk" data-filetype="bank"><img src="../assets/images/doc.png" style="width: 30px;" alt="Icon" class="icon"></td>' +

                '<td style="display:none;" id="po_upload1" class="tdview" data-filetype="po" data-files=\'' + JSON.stringify(user.po_upload) + '\'></td>' +
                '<td style="display:none;" class="tdview documentclk" data-filetype="po"><img src="../assets/images/doc.png" style="width: 30px;" alt="Icon" class="icon"></td>' +

                '<td style="display:none;" id="po_signed_upload1" class="tdview" data-filetype="po_signed" data-files=\'' + JSON.stringify(user.po_signed_upload) + '\'></td>' +
                '<td style="display:none;" class="tdview documentclk" data-filetype="po_signed"><img src="../assets/images/doc.png" style="width: 30px;" alt="Icon" class="icon"></td>' +

                '<td style="display:none;" style="display:none;" id="po_delivery_upload1" class="tdview" data-filetype="po_delivery" data-files=\'' + JSON.stringify(user.po_delivery_upload) + '\'></td>' +
                '<td style="display:none;" class="tdview documentclk" data-filetype="po_delivery"><img src="../assets/images/doc.png" style="width: 30px;" alt="Icon" class="icon"></td>'+

                // '<td class="tdview" style="text-align: center; vertical-align: middle;">' +'<button class="btn btn-sm btn-success toggle-bank-btn" onclick="bankDocuments(event, ' + user.id + ')">' +'<i class="ti ti-plus f-18"></i>' +'</button>' +'</td>'  +
                // '<td class="tdview" style="text-align: center; vertical-align: middle;">' +'<button class="btn btn-sm btn-success toggle-docs-btn" onclick="toggleDocuments(event, ' + user.id + ')">' +'<i class="ti ti-plus f-18"></i>' +'</button>' +'</td>'  +
                '<td class="tdview" style="text-align: center; vertical-align: middle;">' +
                '<button class="btn btn-sm btn-success toggle-bank-btn" onclick="toggleBank(event, ' + user.id + ')">' +
                '<i class="ti ti-plus f-18"></i>' +
                '</button></td>' +
                '<td class="tdview" style="text-align: center; vertical-align: middle;">' +
                '<button class="btn btn-sm btn-success toggle-docs-btn" onclick="toggleDocs(event, ' + user.id + ')">' +
                '<i class="ti ti-plus f-18"></i>' +
                '</button></td>' +
                '<td class="tdview" style="text-align: center; vertical-align: middle;">' +'<button class="btn btn-sm btn-success edit-btn">' +' Edit' +'</button>' +'</td>';
            body += '</tr>';
            body += '<tr class="docs-row" id="docs-row-' + user.id + '" style="display: none;">' +
                        '<td colspan="25">' +
                        '<div class="docs-content" id="docs-content-' + user.id + '"><h2 class="title"></h2></div>' +
                        '</td></tr>';;

              });
    $("#doctor_details").html(body);
    $("#today_visits").text(count);
    $("#counts").text(count);
}
function renderPagination(data, pageSize) {
    var totalPages = Math.ceil(data.length / pageSize);
    var paginationHtml = '';
    var maxVisible = 5;

    for (var i = 1; i <= totalPages; i++) {
        var displayStyle = (i <= maxVisible) ? '' : 'display:none;';
        paginationHtml += '<button class="page-btn" data-page="' + i + '" style="' + displayStyle + '">' + i + '</button>';
    }

    // Optionally, add a "Next" button or dots
    if (totalPages > maxVisible) {
        paginationHtml += '<span class="dots"> ... </span>';
    }

    $('#pagination').html(paginationHtml);

    // Bind click event to each pagination button
    $('.page-btn').click(function () {
        var pageNum = $(this).data('page');
        $('.page-btn').removeClass('active').css('background-color', '#ffffff');
        $(this).addClass('active').css('background-color', '#080fd399');
        renderTable(data, pageSize, pageNum);
    });

    // Trigger click on first button by default
    $('.page-btn[data-page="1"]').click();
}
$(document).on('click', '.edit-btn', function () {
    $('#exampleModal').modal('show');

    const row = $(this).closest('tr');


    let id = row.data('id');
    let user_id = row.find('#user_id').data('user_id');
    let serial_number = row.find('#serial_number1').data('serial_number');
    let created_by = row.find('#created_by1').data('created_by');
    let vendor = row.find('#vendor1').data('vendor');
    let pan_number = row.find('#pan_number1').data('pan_number');
    let payment_status = row.find('#payment_status1').data('payment_status');
    let account_number = row.find('#account_number1').data('account_number');
    let invoice_amount = row.find('#invoice_amount1').data('invoice_amount');
    let already_paid = row.find('#already_paid1').data('already_paid');
    let nature_payment = row.find('#nature_payment1').data('nature_payment');
    let ifsc_code = row.find('#ifsc_code1').data('ifsc_code');
    let utr_number = row.find('#utr_number1').data('utr_number');


    let methodStr = row.find('#payment_method1').data('payment_method'); // "DD,IDhS"
    let paymentArray = [];

    if (typeof methodStr === 'string') {
        paymentArray = methodStr.split(',').map(e => e.trim()); // clean spaces
    }

    // File uploads (optional – you don't prefill file input types)
    let pan_upload = row.find('#pan_upload1').data('files') || [];
    let invoice_upload = row.find('#invoice_upload1').data('files') || [];
    let bank_upload = row.find('#bank_upload1').data('files') || [];
    let po_upload = row.find('#po_upload1').data('files') || [];
    let po_signed_upload = row.find('#po_signed_upload1').data('files') || [];
    let po_delivery_upload = row.find('#po_delivery_upload1').data('files') || [];


    showPreview('pan', pan_upload, '#pan_upload','#existing_pan_file');
    showPreview('invoice', invoice_upload, '#invoice_upload','#existing_invoice_upload');
    showPreview('bank', bank_upload, '#bank_upload','#existing_bank_upload');
    showPreview('po', po_upload, '#po_upload','#existing_po_upload');
    showPreview('po_signed', po_signed_upload, '#po_signed_upload','#existing_po_signed_upload');
    showPreview('po_delivery', po_delivery_upload, '#po_delivery_upload','#existing_po_delivery_upload');


    // Fill form fields
    $("#id").val(id);
    $("#user_id").val(user_id);
    $("#serial_number").val(serial_number);
    $("#created_by").val(created_by);
    $("#vendor").val(vendor);
    $("#nature_payment").val(nature_payment);
    $("#invoice_amount").val(invoice_amount);
    $("#already_paid").val(already_paid);
    $("#pan_number").val(pan_number);
    $("#account_number").val(account_number);
    $("#ifsc_code").val(ifsc_code);
    $("#payment_status").val(payment_status);
    $("#utr_number").val(utr_number);

    // Handle payment method checkboxes
    $('input[name="payment_method[]"]').prop('checked', false);
    if (Array.isArray(paymentArray)) {
        paymentArray.forEach(function (method) {
            $('input[name="payment_method[]"][value="' + method + '"]').prop('checked', true);
        });
    }

});

function showPreview(folder, fileList, inputSelector , setinput) {
    $(inputSelector).siblings('.existing-files').remove();


    let fileName = '';

    try {
        if (typeof fileList === 'string') {
            fileList = JSON.parse(fileList);
            fileName = fileList.replace(/[\[\]"]/g, '').trim();
        }

        if (Array.isArray(fileList)) {
            fileName = fileList[0];
        }
    } catch (e) {
        console.warn('Invalid JSON string, falling back to manual cleanup...');
        fileName = fileList.replace(/[\[\]"]/g, '').trim();
    }

    if (!fileName) return;

    const ext = fileName.split('.').pop().toLowerCase();
    console.log(setinput,"setinput");
    console.log(fileName,"fileName");

    $(setinput).val(fileName);
    // Build preview HTML
    let html = '';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
        html = `<a href="../uploads/${folder}/${fileName}" target="_blank">
                    <img src="../uploads/${folder}/${fileName}" style="height: 40px; margin-right: 5px;" />
                </a>`;
    } else if (ext === 'pdf') {
        html = `<a href="../uploads/${folder}/${fileName}" target="_blank" class="btn btn-sm btn-secondary" style="margin-right: 5px;">
                    View ${fileName}
                </a>`;
    }

    // Append the preview after input
    $(inputSelector).after(`<div class="existing-files mt-1">${html}</div>`);
}


$(document).on('click', '.documentclk', function () {
    $('#documentModal1').modal('show');

    const fileTd = $(this); // The clicked div with data-files
    const fileType = fileTd.data('filetype');
    const filesData = fileTd.attr('data-files');

    let fileArray = [];

    try {
        // Try single or double JSON parse depending on encoding
        fileArray = typeof filesData === 'string' ? JSON.parse(filesData) : filesData;

        // If still string (double encoded), parse again
        if (typeof fileArray === 'string') {
            fileArray = JSON.parse(fileArray);
        }

        console.log("fileArray:", fileArray);
    } catch (e) {
        console.error('Invalid JSON in data-files:', filesData);
        $('#image_pdfs').html('<p>Invalid file data</p>');
        return;
    }

    if (!Array.isArray(fileArray) || fileArray.length === 0) {
        $('#image_pdfs').html('<p>No files found</p>');
        return;
    }

    let basePath = "../uploads/" + fileType + "/";
    let firstFile = fileArray[0].split('/').pop(); // First file name
    let ext = firstFile.split('.').pop().toLowerCase();

    // Show preview of the first file
    $('#pdfmain').attr('src', basePath + firstFile);

    // Generate buttons for each file
    let views = '';
    fileArray.forEach((file, index) => {
        let fileName = file.split('/').pop().trim();
        views += `<button style="font-size: 11px;" type="button" class="btn btn-primary pdf-btn" data-filename="${fileName}">${fileName}</button>`;
    });

    $('#image_pdfs').html(views);

    // Handle individual PDF button click
    $(document).off('click', '.pdf-btn').on('click', '.pdf-btn', function () {
        $('.pdf-btn').removeClass('active');
        $(this).addClass('active');
        const fileName = $(this).data('filename');
        $('#pdfmain').attr('src', basePath + fileName);
    });
});

let currentlyOpenId = null;
let currentlyOpenType = null;

function toggleDocs(event, id) {
    event.stopPropagation();

    const clickedIcon = $(event.currentTarget).find('i');
    const isSame = (currentlyOpenId === id && currentlyOpenType === 'docs');

    if (isSame) {
        hideRow(id);
        resetIcon(clickedIcon);
        return;
    }

    if (currentlyOpenId !== null) {
        hideRow(currentlyOpenId);
        resetAllIcons(); // optional: ensures all icons are reset
    }

    const toggleRow = $('#docs-row-' + id);
    const contentDiv = $('#docs-content-' + id);
    const parentRow = $('tr[data-id="' + id + '"]');

    const docTypes = ['pan', 'invoice', 'bank', 'po', 'po_signed', 'po_delivery'];
    let html = `
        <div class="container-fluid p-2">
            <div class="row g-3">
            <h4>Documents</h4>
    `;

    docTypes.forEach(type => {
        const fileTd = parentRow.find('td#' + type + '_upload1');
        const fileData = fileTd.attr('data-files');

        let fileArray = [];
        try {
            fileArray = JSON.parse(JSON.parse(fileData));
        } catch (e) {
            html += `
                <div class="col-12">
                    <div class="alert alert-warning p-2">
                        <strong>${type.toUpperCase()}</strong>: Invalid or no file data found.
                    </div>
                </div>`;
            return;
        }

        html += `
            <div class="col-md-4 col-lg-3">
                <div class="card border shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold text-center">
                        ${type.replace(/_/g, ' ').toUpperCase()}
                    </div>
                    <div class="card-body p-2">`;

        fileArray.forEach((file) => {
            const fileName = file.split('/').pop();
            const ext = fileName.split('.').pop().toLowerCase();
            const fileIcon = ['jpg', 'jpeg', 'png', 'gif'].includes(ext)
                ? `../uploads/${type}/${fileName}`
                : `../assets/images/doc.png`;

            html += `
                <div class="documentclk d-flex align-items-center gap-3 p-2 mb-2 border rounded shadow-sm bg-white"
                    style="cursor: pointer;"
                    data-filetype="${type}"
                    data-files='["${file}"]'>
                    <img src="${fileIcon}" alt="doc" style="width: 36px; height:36px;">
                    <div class="d-flex flex-column">
                        <span>${fileName}</span>
                        <span class="badge bg-secondary text-light mt-1" style="font-size: 10px;">${ext.toUpperCase()}</span>
                    </div>
                </div>`;
        });

        html += `</div></div></div>`;
    });

    html += `</div></div>`;

    contentDiv.html(html);
    toggleRow.show();
    contentDiv.hide().slideDown(250);

    clickedIcon.removeClass('ti-plus').addClass('ti-minus');
    currentlyOpenId = id;
    currentlyOpenType = 'docs';
}



function toggleBank(event, id) {
    event.stopPropagation();

    const clickedIcon = $(event.currentTarget).find('i');
    const isSame = (currentlyOpenId === id && currentlyOpenType === 'bank');

    if (isSame) {
        hideRow(id);
        resetIcon(clickedIcon);
        return;
    }

    if (currentlyOpenId !== null) {
        hideRow(currentlyOpenId);
        resetAllIcons();
    }

    const toggleRow = $('#docs-row-' + id);
    const contentDiv = $('#docs-content-' + id);
    const row = document.querySelector(`tr[data-id='${id}']`);

    const details = {
        account_number: row.querySelector("#account_number1")?.dataset.account_number ?? '-',
        ifsc_code: row.querySelector("#ifsc_code1")?.dataset.ifsc_code ?? '-',
        payment_method: row.querySelector("#payment_method1")?.dataset.payment_method ?? '-',
        nature_payment: row.querySelector("#nature_payment1")?.dataset.nature_payment ?? '-',
        invoice_amount: row.querySelector("#invoice_amount1")?.dataset.invoice_amount ?? '-',
        pan_number: row.querySelector("#pan_number1")?.dataset.pan_number ?? '-',
        utr_number: row.querySelector("#utr_number1")?.dataset.utr_number ?? '-'
    };

    const detailsHtml = `
        <div class="row">
            <h4> Bank Details </h4>
            ${createDetailCol('Account Number', details.account_number, 'building-bank')}
            ${createDetailCol('IFSC Code', details.ifsc_code, 'code')}
            ${createDetailCol('Payment Method', details.payment_method, 'credit-card')}
            ${createDetailCol('Nature of Payment', details.nature_payment, 'file-text')}
            ${createDetailCol('Invoice Amount', details.invoice_amount, 'currency-rupee')}
            ${createDetailCol('PAN Number', details.pan_number, 'id')}
            ${createDetailCol('UTR Number', details.utr_number, 'hash')}
        </div>`;

    contentDiv.html(detailsHtml);
    toggleRow.show();
    contentDiv.hide().slideDown(250);

    clickedIcon.removeClass('ti-plus').addClass('ti-minus');
    currentlyOpenId = id;
    currentlyOpenType = 'bank';
}


function hideRow(id) {
    const toggleRow = $('#docs-row-' + id);
    const contentDiv = $('#docs-content-' + id);
    contentDiv.slideUp(200, function () {
        toggleRow.hide();
        contentDiv.html('');
    });

    resetAllIcons();

    currentlyOpenId = null;
    currentlyOpenType = null;
}

function resetIcon($icon) {
    $icon.removeClass('ti-minus').addClass('ti-plus');
}

function resetAllIcons() {
    $('.toggle-bank-btn i, .toggle-docs-btn i').removeClass('ti-minus').addClass('ti-plus');
}


// Reuse for both bank and doc
function createDetailCol(label, value, icon) {
    return `
        <div class="col-md-4 bank_docs mb-2 d-flex">
            <i class="ti ti-${icon} me-2 text-primary" style="font-size: 18px;"></i>
            <strong class="me-2">${label}:</strong>
            <span>${value}</span>
        </div>`;
}
