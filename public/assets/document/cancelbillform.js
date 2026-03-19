$(document).ready(function () {
//    if(fitterremovedata.length ==  0){
//             var defaultLocation = "Chennai - Sholinganallur";
//             var defaultZone = "TN CHENNAI";
// 			$('#can_loc_views').val(defaultLocation);
// 		    $('#can_zone_views').val(defaultZone);
// 			$('.dropdown-options div').each(function() {

//                 if ($(this).text().trim() === defaultZone){
//                 $(this).addClass('selected');
//                 }
// 			});
//             $('#getlocation > div').each(function(){
//                 if ($(this).text().trim() === defaultLocation) {
//                     $(this).addClass('selected');
//                 }
//             });
// 		}

    $(".my_search_view").hide();
    $(".my_search_saveview").hide();
    cancelformdata();
    cancelsaveformdata();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });
    // Clear error span text on input
    $('input, select, textarea').on('input change', function () {
        $(this).siblings('span').text('');
    });
    $('#close-button').click(function () {
        // Clear all input fields
        $('input, select, textarea').val('');
        // Clear error messages
        $('span').text('');
    });
    $(".documentscls div").click(function () {
        var selectedText = $(this).text(); // Get selected city name
        var selectedValue = $(this).attr('data-value'); // Get data-value attribute

        // alert(selectedValue);

        $('#zone_id').val(selectedText); // Set input value (visible text)
        $('#zone_id').attr('data-selected-value', selectedValue); // Store data-value in a custom attribute
    });

    $('.dropdown-item-loc').on('click', function () {
        console.log("123123");
        
        var selectedBranchName = $(this).text();
        var selectedBranchId = $(this).data('value');
        $('#zone_id').val(selectedBranchName);
        $('#zone_id').attr('data-value', selectedBranchId);
        // $('.dropdown-options').hide();
    });
    $('.dropdown-item-loc_edit').on('click', function () {
        var selectedBranchName = $(this).text();
        var selectedBranchId = $(this).data('value');
        $('#edit_zone_id').val(selectedBranchName);
        $('#edit_zone_id').attr('data-value', selectedBranchId);
    });

  $('#submit_cancelform').click(function (event) {
    event.preventDefault();
    var fullMobile = $('#pct_mobile').val();
    var maskedMobile = fullMobile.replace(/^(\d{2})\d{5}(\d{3})$/, '$1*****$2');
    let formData = new FormData();
    let zoneId = $('#zone_id').attr('data-value');
    formData.append('can_zone_id', zoneId);
    formData.append('can_op_no', $('#opno').val());
    formData.append('can_token_no', $('#tokenno').val());
    formData.append('can_bill_no', $('#billno').val());
    formData.append('can_consultant', $('#consultant').val());
    formData.append('can_date', $('#bill_date').val());
    formData.append('can_name', $('#pat_name').val());
    formData.append('can_mrdno', $('#pat_mrdno').val());
    formData.append('can_age', $('#pat_age').val());
    formData.append('can_gender', $('input[name="pat_gender_edit"]:checked').val() || '');
    formData.append('can_mobile', maskedMobile);
    formData.append('can_payment_type', $('#payment_type').val());
    formData.append('can_payment_details', $('#payment_details').val());
    formData.append('can_form_status', $('input[name="request"]:checked').val() || '');
    formData.append('can_total', $('#totalamt').val());
    formData.append('can_previous_alance', $('#prebalanceamt').val());
    formData.append('can_amount_receivable', $('#receivableamt').val());
    formData.append('can_amount_received', $('#receivedamt').val());
    formData.append('can_advance', $('#advancedamt').val());
    formData.append('can_amount_word', $('#receivedamtword').val());
    formData.append('can_advance_word', $('#advancedamtword').val());
    formData.append('can_prepared_by', $('#prepared').val());
    formData.append('can_reason', $('#cancelreason').val());

    // Append dynamic table rows
    $('#product_detials tbody tr').each(function () {
        const sno = $(this).find('td:eq(0)').text();
        const particulars = $(this).find('td:eq(1) input').val();
        const qty = $(this).find('td:eq(2) input').val();
        const rate = $(this).find('td:eq(3) input').val();
        const tax = $(this).find('td:eq(4) input').val();
        const amount = $(this).find('td:eq(5) input').val();

        formData.append('can_sno[]', sno);
        formData.append('can_particulars[]', particulars);
        formData.append('can_qty[]', qty);
        formData.append('can_rate[]', rate);
        formData.append('can_tax[]', tax);
        formData.append('can_amount[]', amount);
    });

    const signers = [
        { field: 'can_zonal_sign', radioName: 'cc-signature', canvasId: 'ccCanvas', fileInputId: 'ccsignimg' },
        { field: 'can_admin_sign', radioName: 'admin-signature', canvasId: 'adminCanvas', fileInputId: 'adminsignimg' },
    ];

    let pendingBlobs = 0;

    const finishSubmission = () => {
        if (pendingBlobs === 0) {
            $.ajax({
                url: cancelbillformadded,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        window.dispatchEvent(new CustomEvent('swal:toast', {
                            detail: {
                                title: 'Info!',
                                text: response.message,
                                icon: 'success',
                                background: 'success',
                            }
                        }));
                        clearForm();
                        $("#exampleModaluser").modal('hide');
                        cancelformdata();
                    } else {
                        showError(response.message);
                    }
                },
                error: function (error) {
                    showError(error.responseJSON?.message || 'An error occurred');
                },
            });
        }
    };

    function showError(message) {
        window.dispatchEvent(new CustomEvent('swal:toast', {
            detail: {
                title: 'Error!',
                text: message,
                icon: 'error',
                background: '#f8d7da',
            }
        }));
    }

    signers.forEach(signer => {
        const isUpload = $(`input[name="${signer.radioName}"]:checked`).val() === 'upload';
        if (isUpload) {
            const file = document.getElementById(signer.fileInputId)?.files[0];
            if (file) formData.append(signer.field, file);
        } else {
            const canvas = document.getElementById(signer.canvasId);
            if (canvas) {
                canvas.toBlob(blob => {
                    if (blob) {
                        formData.append(signer.field, blob, `${signer.field}.png`);
                    }
                    pendingBlobs--;
                    finishSubmission();
                });
                pendingBlobs++;
            }
        }
    });

    // If no signature canvas was used
    if (pendingBlobs === 0) finishSubmission();
});

    function clearForm(){
                $('.dropdown-item-loc.selected').removeClass('selected');
                $('#zone_id').attr('data-value', '');
                $('#zone_id').val("");
                $('#opno').val("");
                $('#tokenno').val("");
                $('#billno').val("");
                $('#consultant').val("");
                $('#bill_date').val("");
                $('#pat_name').val("");
                $('#pat_mrdno').val("");
                $('#pct_mobile').val("");
                $('#pat_age').val("");
                $('#payment_type').text("");
                $('input[name="pat_gender"]').prop("checked", false);
                $('input[name="request"]').prop("checked", false);
                $('#payment_details').val("");
                $('#totalamt').val("");
                $('#prebalanceamt').val("");
                $('#receivableamt').val("");
                $('#receivedamt').val("");
                $('#advancedamt').val("");
                 $('#receivedamtword').val("");
                $('#advancedamtword').val("");
                $('#prepared').val("");
                $('cancelreason').val("");
         $('.signature-option-group').each(function () {
        const group = $(this);
        const target = group.data('target');
        const canvas = document.getElementById(`${target}Canvas`);

        if (canvas) {
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        $(`#${target}-upload input[type="file"]`).val('');
        $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');
    });
}

    //  $('#itemsPerPageSelectdocument').change(function() {
    //     var pageSizedocuments = parseInt($(this).val());
    //     renderPaginationdocument(dataSourcedocument, pageSizedocuments);
    //     renderTabledocument(dataSourcedocument, pageSizedocuments, 1);  // Initially show the first page
    //       renderBillPagination(matchedPatients, currentPage, selectedPatient, newPageSize);
    // });
  $('#itemsPerPageSelectdocument').change(function () {
    const pageSizedocuments = parseInt($(this).val());
    const deduplicated = getDeduplicatedPatients(dataSourcedocument);
    renderPaginationdocument(deduplicated, pageSizedocuments);
    renderTabledocument(deduplicated, pageSizedocuments, 1);
});

    //    $('#itemsPerPageSelectsave').change(function() {
    //   var pageSizedocuments = parseInt($(this).val());
    //    renderPaginationsaved(dataSaved, pageSizedocuments, 1);
    // });

    $('#itemsPerPageSelectsave').off('change').on('change', function () {
        const pageSizedocuments = parseInt($(this).val(), 10) || 10;
        // Always reset to page 1 when page size changes
        renderPaginationsaved(tableData, pageSizedocuments, 1);
    });

    $("#editcancelform").on("click",function(event){

        // if($('#token_no_edit').val() ==="" || "-"){
        //     $('.token_no').text('Enter the Token No');
        //     isValid= false;
        //   } if($('#payment_details_edit').val() ==="" || "-"){
        //     $('.payment').text('Enter the Payment Details');
        //     isValid= false;
        //   } if($('#receivedamt_edit').val() ==="" || "-"){
        //     $('.received').text('Enter the Received Amount');
        //     isValid= false;
        //   } if($('#prepared_edit').val() ==="" || "-"){
        //     $('.prepared').text('Enter the Prepared By');
        //     isValid= false;
        //   }if($('#cancelreason_edit').val() ==="" || "-"){
        //     $('.reason').text('Enter the Reason');
        //     isValid= false;
        //   } if($('#adminsignimge').val() ==="" || "-"){
        //     $('.received').text('Enter the Token No');
        //     isValid= false;
        //   }if (!isValid) {
        //       return;
        //   }
        event.preventDefault();
        const formData = new FormData();
    formData.append('can_zone_id', $('#locationid').val());
    formData.append('can_op_no', $('#opno_edit').val());
    formData.append('can_token_no', $('#token_no_edit').val());
    formData.append('can_bill_no', $('#billno_edit').val());
    formData.append('can_consultant', $('#consultant_edit').val());
    formData.append('can_date', $('#bill_date_edit').val());
    formData.append('can_name', $('#pat_name_edit').val());
    formData.append('can_mrdno', $('#pat_mrdno_edit').val());
    formData.append('can_age', $('#pat_age_edit').val());
    formData.append('can_gender', $('input[name="pat_gender_edit"]:checked').val() || '');
    formData.append('can_mobile', $('#pct_mobile_edit').val());
    formData.append('can_payment_type', $('#payment_type_edit').val());
    formData.append('can_payment_details', $('#payment_details_edit').val());
    formData.append('can_form_status', $('input[name="request_edit"]:checked').val() || '');
    formData.append('can_total', $('#totalamt_edit').val());
    formData.append('can_previous_alance', $('#prebalanceamt_edit').val());
    formData.append('can_amount_receivable', $('#receivableamt_edit').val());
    formData.append('can_amount_received', $('#receivedamt_edit').val());
    formData.append('can_advance', $('#advancedamt_edit').val());
    formData.append('can_amount_word', $('#receivedamtword_edit').val());
    formData.append('can_advance_word', $('#advancedamtword_edit').val());
    formData.append('can_prepared_by', $('#prepared_edit').val());
    formData.append('can_reason', $('#cancelreason_edit').val());

    // Append dynamic table rows
    $('#product_detials_edit tbody tr').each(function () {
        const sno = $(this).find('td:eq(0)').text();
        const particulars = $(this).find('td:eq(1) input').val();
        const qty = $(this).find('td:eq(2) input').val();
        const rate = $(this).find('td:eq(3) input').val();
        const tax = $(this).find('td:eq(4) input').val();
        const amount = $(this).find('td:eq(5) input').val();

        formData.append('can_sno[]', sno);
        formData.append('can_particulars[]', particulars);
        formData.append('can_qty[]', qty);
        formData.append('can_rate[]', rate);
        formData.append('can_tax[]', tax);
        formData.append('can_amount[]', amount);
    });

  const signers = [
   { field: 'can_zonal_sign', radio: 'editcc-signature', canvasId: 'editccCanvas', fileInput: 'ccsignimge' },
    { field: 'can_admin_sign', radio: 'editadmin-signature', canvasId: 'editadminCanvas', fileInput: 'adminsignimge' },
];
    console.log("signers",signers);

    let pendingBlobs = 0;
     const finishEditSubmission = () => {
    if (pendingBlobs === 0) {
           $.ajax({
               url: cancelbillformadded,
               type: "POST",
               data: formData,
               processData: false, // Prevent processing of the data
               contentType: false, // Prevent setting content-type header
               success: function (response) {
                if (response.success) {
                    window.dispatchEvent(new CustomEvent('swal:toast', {
                        detail: {
                          title:'Info!',
                          text: response.message,
                          icon: 'success',
                          background: 'success',
                        }
                    }));
                   document.getElementById('analytics-tab-2').click();
                   }else{
                    window.dispatchEvent(new CustomEvent('swal:toast', {
                        detail: {
                          title:'Error!',
                          text: response.message,
                          icon: 'error',
                          background: '#f8d7da',
                        }
                    }));
                   }
                   $("#exampleModal2").modal('hide');
                   cancelformdata();
                   cancelsaveformdata();
               },
               error: function (error) {
                   console.error(error.responseJSON);
               },
           });
        }
    };
    signers.forEach(signer => {
        const isUpload = $(`input[name="${signer.radio}"]:checked`).val() === 'upload';
        const fileInput = document.getElementById(signer.fileInput);
        const canvas = document.getElementById(signer.canvasId);

        if (isUpload && fileInput && fileInput.files.length > 0) {
            formData.append(signer.field, fileInput.files[0]);
        } else if (!isUpload && canvas) {
            pendingBlobs++;
            canvas.toBlob(blob => {
                if (blob) {
                    formData.append(signer.field, blob, signer.field + '.png');
                }
                pendingBlobs--;
                finishEditSubmission();
            });
        }
    });

        if (pendingBlobs === 0) finishEditSubmission();
});
});

$('.dropdown-item-loc').on('click', function () {
    var selectedBranchName = $(this).text();
    var selectedBranchId = $(this).data('value');
    $('#zone_id').val(selectedBranchName);
    $('#zone_id').attr('data-value', selectedBranchId);
});
$('.dropdown-item-loc_edit').on('click', function () {
    var selectedBranchName = $(this).text();
    var selectedBranchId = $(this).data('value');
    $('#edit_zone_id').val(selectedBranchName);
    $('#edit_zone_id').attr('data-value', selectedBranchId);
});
    $(document).on("click", '.edit_can_details', function () {
        var row = $(this).closest('tr');
        var wifemrdno =row.find(".wifemrdno").text();
        var husmrdno =row.find('.husmrdno').text();
       $('#exampleModal2').modal('show');
        var opno=row.find(".opnoedit").text();
        var mobileno=row.find(".p_mobile").text();
        var gender=row.find(".p_gender").text();
        var age=row.find(".p_age").text();
        var consultby=row.find(".consultby").text();
        var mrdno=row.find(".mrdno").text();
        var p_name=row.find(".nameedit").text();
        var billno=row.find(".bill_no").text();
        var token_no=row.find('.token_no').text();
        var dateedit = row.find(".billdate").text().trim();
        var gender = row.find(".p_gender").text().trim();
        var payment =row.find('.p_payment').text().trim();
        var location =row.find('.location').text().trim();
        var locationid=row.find('.locationid').text().trim();
        var prev_balance=row.find('.prev_balance').text().trim();
        var payment_details=row.find('.payment_details').text().trim();
        var amountpayable=row.find('.amountpayable').text().trim();
        var amount_word=row.find('.amount_word').text().trim();
        var advance_word=row.find('.advance_word').text().trim();
        var can_reason=row.find('.can_reason').text().trim();
        var can_prepared_by=row.find('.can_prepared_by').text().trim();
        var amount_received=row.find('.amount_received').text().trim();
        var advancedamt=row.find('.advancedamt').text().trim();
        var imagezonal = row.find('.zonal_sign').text().trim();
        var imageadmin = row.find('.admin_sign').text().trim();
        imageviewscc = imagezonal.replace(/[\[\]\"]/g, '');
        imageviewsadmin = imageadmin.replace(/[\[\]\"]/g, '');
        $('#ccimgPreviewe').css('background-image', 'url(../public/' + imageviewscc + ')');
            $('#adminimgPreviewe').css('background-image', 'url(../public/' + imageviewsadmin + ')');

        // var amountreceivedRaw = row.find('.amountreceived').text().trim();
        // let amountreceived = 0;
        // try {
        //     let parsed = JSON.parse(amountreceivedRaw);
        //     if (Array.isArray(parsed)) {
        //         amountreceived = parsed.reduce((sum, val) => sum + parseFloat(val || 0), 0).toFixed(2);
        //     } else {
        //         amountreceived = parseFloat(parsed || 0).toFixed(2);
        //     }
        // } catch (e) {
        //     amountreceived = parseFloat(amountreceivedRaw || 0).toFixed(2);
        // }
        // $('#receivedamt_edit').val(amountreceived);

              $('#product_detials_edit tbody').empty();

let billItems = JSON.parse(row.attr('data-billitems') || '{}');
let index = 1;
let totalAmount = 0;

// Check if structured billitems exist
if (Object.keys(billItems).length > 0) {
    Object.values(billItems).forEach(item => {
        const particulars = item.name || '-';
        const qty = parseFloat(item.qty || 0);
        const rate = parseFloat(item.mrp || item.amt || 0);
        const taxPercentage = parseFloat(item.taxpercentage || 0);

        const lineAmount = qty * rate;
        const taxAmount = lineAmount * taxPercentage / 100;
        const totalLineAmount = lineAmount + taxAmount;

        totalAmount += totalLineAmount;
 if (totalAmount >= 5000) {
                    $('#zonal_sign_edit').removeClass('hide');
                } else {
                    $('#zonal_sign_edit').addClass('hide');
                }
        const itemRow = `
            <tr>
                <td>${index++}</td>
                <td><input type="text" class="tdinput" name="particulars" readonly value="${particulars}"></td>
                <td><input type="text" class="tdinput" name="qty" readonly value="${qty}"></td>
                <td><input type="text" class="tdinput" name="rate" readonly value="${rate}"></td>
                <td><input type="text" class="tdinput" name="taxPercentage" readonly value="${taxPercentage}"></td>
                <td class="amount"><input type="text" class="tdinput" name="totalamt" readonly value="${totalLineAmount.toFixed(2)}"></td>
            </tr>`;
        $('#product_detials_edit tbody').append(itemRow);
    });

} else {
    // Fallback to can_* string fields
  const particularsArr = JSON.parse(row.attr('data-can_particulars') || '[]');
const qtyArr = JSON.parse(row.attr('data-can_qty') || '[]');
const rateArr = JSON.parse(row.attr('data-can_rate') || '[]');
const taxArr = JSON.parse(row.attr('data-can_tax') || '[]');

    for (let i = 0; i < particularsArr.length; i++) {
        const particulars = particularsArr[i] || '-';
        const qty = parseFloat(qtyArr[i] || 0);
        const rate = parseFloat(rateArr[i] || 0);
        const taxPercentage = parseFloat(taxArr[i] || 0);

        const lineAmount = qty * rate;
        const taxAmount = lineAmount * taxPercentage / 100;
        const totalLineAmount = lineAmount + taxAmount;

        totalAmount += totalLineAmount;

        const itemRow = `
            <tr>
                <td>${index++}</td>
                <td><input type="text" class="tdinput" name="particulars" readonly value="${particulars}"></td>
                <td><input type="text" class="tdinput" name="qty" readonly value="${qty}"></td>
                <td><input type="text" class="tdinput" name="rate" readonly value="${rate}"></td>
                <td><input type="text" class="tdinput" name="taxPercentage" readonly value="${taxPercentage}"></td>
                <td class="amount"><input type="text" class="tdinput" name="totalamt" readonly value="${totalLineAmount.toFixed(2)}"></td>
            </tr>`;
        $('#product_detials_edit tbody').append(itemRow);
        if (totalAmount >= 5000) {
                    $('#zonal_sign_edit').removeClass('hide');
                } else {
                    $('#zonal_sign_edit').addClass('hide');
                }
    }

}

$('#totalamt_edit').val(totalAmount.toFixed(2));

        if (gender === 'F' || gender === 'M') {
            $('input[name="pat_gender_edit"][value="' + gender + '"]').prop('checked', true);
        }
       var billtype = row.find('.p_billtype').text().trim().replace('/', '');
        if (billtype === 'IP' || billtype === 'I/P') {
            billtype = 'IP';
        } else if (billtype === 'OP' || billtype === 'O/P') {
            billtype = 'OP';
        } else if (billtype === 'Pharmacy') {
            billtype = 'Pharmacy';
        }
        $('input[name="request_edit"][value="'+ billtype +'"]').prop('checked', true);

        function parseToInputDate(dateStr) {
            if (!dateStr) return '';
            const match = dateStr.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
            if (match) {
                const [_, day, month, year] = match;
                return `${year}-${month}-${day}`; // format accepted by <input type="date">
            }
            return '';
        }

        const formattedDate = parseToInputDate(dateedit);
        $('#bill_date_edit').val(formattedDate);
        $('#opno_edit').val(opno);
        $('#billno_edit').val(billno);
        $('#payment_details_edit').val(payment_details);
        $('#token_no_edit').val(token_no);
        $('#pat_name_edit').val(p_name);
        $('#pat_mrdno_edit').val(mrdno);
        $('#consultant_edit').val(consultby);
        $('#hidden_wifemrd').val(wifemrdno);
        $('#hidden_husmrd').val(husmrdno);
        $('#pat_age_edit').val(age);
        $('#pct_mobile_edit').val(mobileno);
        $('#payment_type_edit').val(payment);
        $('#zone_id_edit').val(location);
        $('#prebalanceamt_edit').val(prev_balance);
        $('#receivableamt_edit').val(amountpayable);
        $('#locationid').val(locationid);
        $('#hidden_billno').val(billno);
        $('#cancelreason_edit').val(can_reason);
        $('#prepared_edit').val(can_prepared_by);
        $('#receivedamt_edit').val(amount_received);
        $('#advancedamt_edit').val(advancedamt);
        $('#receivedamtword_edit').val(amount_word);
        $('#advancedamtword_edit').val(advance_word);
      });

function formatBillDate(rawDate) {
    if (!rawDate) return '-';

    // Try to parse YYYY-MM-DD HH:MM:SS or similar
    const dateObj = new Date(rawDate);
    if (!isNaN(dateObj)) {
        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const year = dateObj.getFullYear();
        const hours = String(dateObj.getHours()).padStart(2, '0');
        const minutes = String(dateObj.getMinutes()).padStart(2, '0');
        const seconds = String(dateObj.getSeconds()).padStart(2, '0');

        // If time is not all zero, show full datetime
        const isTimeNonZero = hours !== '00' || minutes !== '00' || seconds !== '00';

        return isTimeNonZero
            ? `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`
            : `${day}/${month}/${year}`;
    }

    // Try custom compact format: YYYYMMDDHH:MM:SS
    const compactDateTimeMatch = rawDate.match(/^(\d{4})(\d{2})(\d{2})(\d{2}):(\d{2}):(\d{2})$/);
    if (compactDateTimeMatch) {
        const [_, year, month, day, hour, minute, second] = compactDateTimeMatch;
        const isTimeNonZero = hour !== '00' || minute !== '00' || second !== '00';
        return isTimeNonZero
            ? `${day}/${month}/${year} ${hour}:${minute}:${second}`
            : `${day}/${month}/${year}`;
    }

    // Fallback: YYYYMMDD
    const compactDateOnlyMatch = rawDate.match(/^(\d{4})(\d{2})(\d{2})$/);
    if (compactDateOnlyMatch) {
        const [_, year, month, day] = compactDateOnlyMatch;
        return `${day}/${month}/${year}`;
    }

    return rawDate;
}


let selectedPatientBills = [];
let selectedPatient = null;
let matchedPatients = [];

$(document).on('click', '.showbill', function (e) {
    e.preventDefault();

    let row = $(this).closest('tr');
    let wmrd = row.find('.wifemrdno').data('ph_id') || '';
    let hmrd = row.find('.husmrdno').data('ph_id') || '';

    // Match duplicates based on phid or can_mrdno
    matchedPatients = dataSourcedocument.filter(user =>
        user.can_mrdno === wmrd || user.can_mrdno === hmrd || user.phid === wmrd || user.phid === hmrd
    );

    if (matchedPatients.length > 0) {
        selectedPatient = matchedPatients[0]; // Use the first matched patient as base

        // Flatten and deduplicate all bill entries
        const allBills = matchedPatients.flatMap(user =>
            Array.isArray(user.main_bills) ? user.main_bills : []
        );

        const seenBillNos = new Set();
        selectedPatientBills = allBills.filter(bill => {
            const id = bill.bill_no || JSON.stringify(bill);
            if (seenBillNos.has(id)) return false;
            seenBillNos.add(id);
            return true;
        });
    } else {
        selectedPatient = null;
        selectedPatientBills = [];
    }
    renderBillPagination(matchedPatients, 1, selectedPatient);

    // UI updates
    $(".maintable").addClass("hide");
    $(".subtable").removeClass("hide");
    $(".filterrow").addClass("hide");
    $("#billcounts").text(matchedPatients.length);
    $('#mycounts').addClass('hide');
    $('#billcounts').removeClass('hide');
    $("#paginationdocument").addClass('hide');
    $('#paginationbill').removeClass('hide');
    $("#itemsPerPageSelectdocument").addClass('hide');
    $('#itemsPerPageSelectbill').removeClass('hide');
    $('.search_this').addClass('hide');
});

$('#itemsPerPageSelectbill').change(function () {
    const newPageSize = parseInt($(this).val());
    renderBillPagination(matchedPatients, 1, selectedPatient, newPageSize);
});

$(document).on('click', '#backToMain', function () {
    $(".filterrow").removeClass("hide");
    $(".subtable").addClass("hide");
    $(".maintable").removeClass("hide");
    $('#billcounts').addClass('hide');
    $('#mycounts').removeClass('hide');
     $("#paginationdocument").removeClass('hide');
     $('#paginationbill').addClass('hide');
     $("#itemsPerPageSelectdocument").removeClass('hide');
     $('#itemsPerPageSelectbill').addClass('hide');
     $('.search_this').removeClass('hide');
});

var dataSourcedocument = [];
function renderTabledocument(data, pageSizedocuments, pageNum) {
    // Deduplicate using Map based on can_mrdno or phid
    const uniquePatientsMap = new Map();
    data.forEach(user => {
        let uniqueKey = user.can_mrdno || user.phid || '';
        if (!uniquePatientsMap.has(uniqueKey)) {
            uniquePatientsMap.set(uniqueKey, user);
        }
    });
    const uniquePatients = Array.from(uniquePatientsMap.values()); // Now deduplicated
    const totalItems = uniquePatients.length;

    const startIdx = (pageNum - 1) * pageSizedocuments;
    const endIdx = startIdx + pageSizedocuments;
    const pageData = uniquePatients.slice(startIdx, endIdx);

    let body = '';

    $.each(pageData, function(index, user) {
      let gender = user.gender || user.can_gender;
        let wifemrdno = 'N/A';
        let wifename = 'N/A';
        let husmrdno = 'N/A';
        let husname = 'N/A';
        if (gender === 'F') {
            wifemrdno = user.can_mrdno || user.phid || 'N/A';
            wifename = user.can_name || user.name || 'N/A';
        } else if (gender === 'M') {
            husmrdno = user.can_mrdno || user.phid || 'N/A';
            husname = user.can_name || user.name || 'N/A';
        }
        let wmrd = wifemrdno;
        let wname = wifename;
        let hmrd = husmrdno;
        let hname = husname;

        let zone = user.zone || user.zone_name || 'N/A';
        let location = user.branch || user.location_name || 'N/A';
        let opno = user.can_op_no || user.opno || '-';
        let age = user.can_age || user.age || '-';
        let patient_ph = user.can_mobile || user.mobile || '-';
        let counselled_by = user.can_consultant || user.consultingdr_name || '-';

        body += '<tr onclick="rowClick(event)">' +
            '<td class="tdview"><strong>#' + (startIdx + index + 1) + '</strong></td>' +
            '<td class="tdview locationname" data-id="' + (user.can_zone_id || user.locationid) + '">' + zone + '</td>' +
            '<td class="tdview branchname">' + location + '</td>' +
            '<td class="tdview wifemrdno" data-ph_id="' + wmrd + '"><a href="#">' + wmrd + '<br>' + wname + '</a></td>' +
            '<td class="tdview husmrdno" data-ph_id="' + hmrd + '"><a href="#">' + hmrd + '<br>' + hname + '</a></td>' +
            '<td class="tdview opno">' + opno + '</td>' +
            '<td class="tdview age">' + age + '</td>' +
            '<td class="tdview gender">' + gender + '</td>' +
            '<td class="tdview mobileno">' + patient_ph + '</td>' +
            '<td class="tdview consultby">' + counselled_by + '</td>' +
            '<td class="tdview"><a href="#" class="showbill" data-index="' + (startIdx + index) + '">View Bills</a></td>' +
            '<td class="tdview wifename" style="display:none;"><a href="#">' + wifename + '</a></td>' +
            '<td class="tdview husname" style="display:none;"><a href="#">' + husname + '</a></td>' +
        '</tr>';
    });

    if (pageData.length === 0) {
        body = '<tr><td colspan="15" class="tdview" style="text-align: center;">No data available</td></tr>';
    }

    $("#document_tbl").html(body);
    $("#mycounts").text(totalItems);
}

function renderBillPagination(bills, currentPage = 1, patient = {}, pageSize = 3) {
    const totalPages = Math.ceil(bills.length / pageSize);
    const paginatedBills = bills.slice((currentPage - 1) * pageSize, currentPage * pageSize);
    let billbody = '';

    if (paginatedBills.length === 0) {
        billbody = '<tr><td colspan="10" class="tdview" style="text-align: center;">No bills available</td></tr>';
    } else {
        $.each(paginatedBills, function (i, bill) {
            let index = (i + 1) + ((currentPage - 1) * pageSize);
 let gender = patient.can_gender || patient.gender;
        let wifemrdno = 'N/A';
        let wifename = 'N/A';
        let husmrdno = 'N/A';
        let husname = 'N/A';
        if (gender === 'F') {
            wifemrdno = patient.can_mrdno || patient.phid || 'N/A';
            wifename = patient.can_name || patient.name || 'N/A';
        } else if (gender === 'M') {
            husmrdno = patient.can_mrdno || patient.phid || 'N/A';
            husname = patient.can_name || patient.name || 'N/A';
        }

        let wmrd = wifemrdno;
        let wname = wifename;
        let hmrd = husmrdno;
        let hname = husname;
            billbody += `
          <tr
                data-billitems='${JSON.stringify(bill.billitems || {})}'
                data-can_particulars='${bill.can_particulars ? bill.can_particulars : "[]"}'
                data-can_qty='${bill.can_qty ? bill.can_qty : "[]"}'
                data-can_rate='${bill.can_rate ? bill.can_rate : "[]"}'
                data-can_tax='${bill.can_tax ? bill.can_tax : "[]"}'>

                <td class="tdview"><strong>#${index}</strong></td>
                <td class="tdview opnoedit" style="display:none;">${patient.opno || patient.can_op_no || '-'}</td>
                <td class="tdview nameedit" style="display:none;">${patient.name || patient.can_name || '-'}</td>
                <td class="tdview mrdno" style="display:none;">${patient.phid || patient.can_mrdno || '-'}</td>
                <td class="tdview p_age" style="display:none;">${patient.age || patient.can_age || '-'}</td>
                <td class="tdview p_gender" style="display:none;">${patient.gender || patient.can_gender || '-'}</td>
                <td class="tdview p_mobile" style="display:none;">${patient.mobile || patient.can_mobile || '-'}</td>
                <td class="tdview p_payment" style="display:none;">${bill.paymenttype || bill.can_payment_type || '-'}</td>
                <td class="tdview p_billtype" style="display:none;">${bill.billtype || bill.can_form_status || '-'}</td>
                <td class="tdview prev_balance" style="display:none;">${bill.prev_balance || bill.can_previous_alance || '-'}</td>
                <td class="tdview amountpayable" style="display:none;">${bill.amountpayable || bill.can_amount_receivable || '-'}</td>
                <td class="tdview amountreceived" style="display:none;">${bill.amountreceived || bill.can_amount_received || '-'}</td>
                <td class="tdview can_reason" style="display:none;">${bill.can_reason || '-'}</td>
                <td class="tdview payment_details" style="display:none;">${bill.can_payment_details || '-'}</td>
                <td class="tdview amount_received" style="display:none;">${bill.can_amount_received || '-'}</td>
                <td class="tdview advancedamt" style="display:none;">${bill.can_advance || '-'}</td>
                <td class="tdview token_no" style="display:none;">${bill.can_token_no || '-'}</td>
                <td class="tdview can_prepared_by" style="display:none;">${bill.can_prepared_by || '-'}</td>
                <td class="tdview advance_word" style="display:none;">${bill.can_advance_word || '-'}</td>
                <td class="tdview amount_word" style="display:none;">${bill.can_amount_word || '-'}</td>
                <td class="tdview admin_sign" style="display:none;">${bill.can_admin_sign || '-'}</td>
                <td class="tdview zonal_sign" style="display:none;">${bill.can_zonal_sign || '-'}</td>
                <td class="tdview" data-id="${patient.locationid || bill.can_zone_id}">${patient.zone || patient.zone_name || '-'}</td>
                <td class="tdview location">${patient.branch || patient.location_name || '-'}</td>
                <td class="tdview locationid" style="display:none;">${patient.locationid || bill.can_zone_id}</td>
                <td class="tdview"><a href="#">${wmrd}<br>${wname}</a></td>
                <td class="tdview"><a href="#">${hmrd}<br>${hname}</a></td>
                <td class="tdview bill_no">${bill.bill_no || bill.can_bill_no || '-'}</td>
                <td class="tdview">${bill.billtype || bill.can_form_status || '-'}</td>
                <td class="tdview billdate">${formatBillDate(bill.billdate || bill.can_date || '-')}</td>
                <td class="tdview consultby">${bill.consultant || bill.can_consultant || '-'}</td>
                <td class="tdview"><a href="#"><img src="../assets/images/edit.png" style="width: 23px;" class="edit_can_details" alt="Icon"></a></td>
            </tr>`;
        });
    }

    $("#bill_tbl").html(billbody);
    renderBillPageButtons(totalPages, currentPage, patient, pageSize);
}

function renderBillPageButtons(totalPages, currentPage, patient = {}, pageSize = 3) {
    let paginationHtml = '';

    if (currentPage > 1) {
        paginationHtml += `<button class="bill-page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
    }

    const maxVisible = 3;
    const pageRange = [];

    pageRange.push(1); // Always show first page

    if (currentPage > maxVisible) {
        pageRange.push('...');
    }

    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
        pageRange.push(i);
    }

    if (currentPage < totalPages - maxVisible + 1) {
        pageRange.push('...');
    }

    if (totalPages > 1) {
        pageRange.push(totalPages); // Always show last page if more than one
    }

    for (let i = 0; i < pageRange.length; i++) {
        if (pageRange[i] === '...') {
            paginationHtml += `<span class="dots">...</span>`;
        } else {
            const page = pageRange[i];
            const activeClass = page === currentPage ? 'active' : '';
            const bgColor = page === currentPage ? 'style="background-color: #080fd399;"' : '';
            paginationHtml += `<button class="bill-page-btnviews ${activeClass}" data-page="${page}" ${bgColor}>${page}</button>`;
        }
    }

    if (currentPage < totalPages) {
        paginationHtml += `<button class="bill-page-btnviews" data-page="${currentPage + 1}">Next</button>`;
    }

    $('#paginationbill').html(paginationHtml);

    $('.bill-page-btnviews').click(function () {
        const page = $(this).data('page');
        renderBillPagination(matchedPatients, page, patient, pageSize);
    });
}

function printRowData(trElement) {
  const row = $(trElement).closest('tr');
  const billno = row.find(".bill_no").text().trim();
  $.ajax({
    url: cancelbill_data,
    type: "GET",
    data: {
      bill_no: billno,
    },
    success: function (response) {
      if (!response || !Array.isArray(response) || !response[0]) {
        // Show alert if response is empty or invalid
        // alert("No data found for this patient.");
         window.dispatchEvent(new CustomEvent('swal:toast', {
              detail: {
                title: 'Error!',
                text: 'No data found for this patient, You have to save the data.',
                icon: 'error',
                background: '#f8d7da',
              }
            }));
        return;
      }
        const data = response[0];
        const cleanPath = (path) => {
                if (!path) return '';

                try {
                    const parsed = JSON.parse(path); // convert string → array
                    return Array.isArray(parsed) ? parsed[0] : parsed;
                } catch (e) {
                    return path;
                }
            };
        const imageviewscc = cleanPath(data.can_zonal_sign);
        const imageviewsadmin = cleanPath(data.can_admin_sign);
        console.log("imageviewscc",imageviewscc);
        console.log("imageviewsadmin",imageviewsadmin);

        const branchname =row.find('.branchname').text().trim();
        const sno = JSON.parse(data.can_sno || "[]");
        const particulars = JSON.parse(data.can_particulars || "[]");
        const qty = JSON.parse(data.can_qty || "[]");
        const rate = JSON.parse(data.can_rate || "[]");
        const tax = JSON.parse(data.can_tax || "[]");
        const amount = JSON.parse(data.can_amount || "[]");

    const printContent = `
                <html>
                <head>
                <title>Cancel Bill Print</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .form-container { border: 1px solid #000; padding: 20px; }
                    .header { display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 18px; border-bottom: 1px solid #000; padding-bottom: 10px; }
                    .form-title { font-size: 22px; font-weight: bold; }
                    .form-subtitle { font-size: 12px; }
                    .discount-box { border: 2px solid #000; padding: 5px 10px; display: inline-block; margin-left: 10px; font-weight: bold; }
                    .form-row-line { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
                    .form-label-col1, .form-colon1, .form-input1 { margin-right: 5px; }
                    .form-label-col4 { width: 180px; font-weight: bold; }
                    .form-input4 { width: 150px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
                    th, td { border: 1px solid #000; padding: 5px; text-align: left; }
                    .signatures { margin-top: 40px; display: flex; justify-content: space-between; }
                    .signature-box { text-align: center; }
                    .signature-box img { max-height: 50px; margin-top: 10px; }
                    .form-section-title { font-weight: bold; border-bottom: 1px solid #000; margin-bottom: 10px; padding-bottom: 5px; }
                </style>
                </head>
                <body>
                <div class="form-container">
                    <div class="header">
                    <div>
                        <div class="form-title">Dr. ARAVIND’s IVF</div>
                        <div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div>
                    </div>
                    <div class="discount-box">CANCEL BILL FORM</div>
                    <div>S. No: ${data.can_id || ''}</div>
                    </div>

                    <div class="form-row-line">
                    <div>OP No: ${data.can_op_no || ''}</div>
                    <div>Token No: ${data.can_token_no || ''}</div>
                    </div>
                    <div class="form-row-line">
                    <div>Consultant: ${data.can_consultant || ''}</div>
                    <div>Bill No: ${data.can_bill_no || ''}</div>
                    </div>
                    <div class="form-row-line">
                    <div>Date: ${formatBillDate(data.can_date || '')}</div>
                    <div>Branch: ${branchname || ''}</div>
                    </div>
                    <hr>

                    <div class="form-row-line">
                    <div>Name: <br> ${data.can_name || ''}</div>
                    <div>MRD No: <br> ${data.can_mrdno || ''}</div>
                    <div>Age: <br> ${data.can_age || ''}</div>
                    <div>Gender: <br> ${data.can_gender || ''}</div>
                    <div>Mobile: <br> ${data.can_mobile || ''}</div>
                    </div>
                    <hr>

                    <div class="form-row-line">
                    <div>Payment Type: ${data.can_payment_type || ''}</div>
                    <div>Payment Details: ${data.can_payment_details || ''}</div>
                    <div>Request Type: ${data.can_form_status || ''}</div>
                    </div>

                    <table>
                    <thead>
                        <tr>
                        <th>S.No</th>
                        <th>Particulars</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Tax (%)</th>
                        <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                ${sno.map((_, i) => `
                    <tr>
                        <td>${sno[i]}</td>
                        <td>${particulars[i]}</td>
                        <td>${qty[i]}</td>
                        <td>${rate[i]}</td>
                        <td>${tax[i]}</td>
                        <td>${amount[i]}</td>
                    </tr>
                    `).join('')
                }
                </tbody>
                    </table>

                    <div class="form-row-line">
                    <div style="width: 70%;"></div>
                    <div>Total: ${data.can_total}</div>
                    </div>
                    <div class="form-row-line">
                    <div style="width: 70%;"></div>
                    <div>Previous Balance: ${data.can_previous_alance}</div>
                    </div>
                    <div class="form-row-line">
                    <div style="width: 70%;"></div>
                    <div>Amount Receivable: ${data.can_amount_receivable}</div>
                    </div>
                    <div class="form-row-line">
                    <div>Amount in Words: ${data.can_amount_word}</div>
                    <div>Amount Received: ${data.can_amount_received}</div>
                    </div>
                    <div class="form-row-line">
                    <div>Advance (in Words): ${data.can_advance_word}</div>
                    <div>Advance: ${data.can_advance}</div>
                    </div>
                    <div class="form-row-line">
                    <div style="width: 70%;"></div>
                    <div>Prepared By: ${data.can_prepared_by}</div>
                    </div>
                    <div class="form-row-line">
                    <div>Cancel Reason: ${data.can_reason}</div>
                    </div>

                    <div class="signatures">

                    <div class="signature-box">
                        <div>Admin Sign</div>
                        ${imageviewsadmin ? `<img src="${PUBLIC_BASE_URL}/public/${imageviewsadmin}" alt="Admin Sign">` : '-'}
                    </div>
                    <div class="signature-box">
                        <div>Zonal Head Sign</div>
                        ${imageviewscc ? `<img src="${PUBLIC_BASE_URL}/public/${imageviewscc}" alt="Zonal Sign">` : '-'}
                    </div>
                    </div>
                </div>

                <script>
                    window.onload = function() {
                    window.print();
                    };
                </script>
                </body>
                </html>
                `;


      const printWindow = window.open('', '', 'width=1200,height=600');
      printWindow.document.write(printContent);
      printWindow.document.close();
    },
    error: function (xhr) {
      console.error("Error loading print data:", xhr.responseText);
      alert("Failed to load data for printing.");

    }
  });
}

document.addEventListener('click', function (event) {
    const iconCell = event.target.closest('.print_can_details');
    if (iconCell) {
        event.stopPropagation(); // Prevent row click
        const row = iconCell.closest('tr');
        printRowData(row);
    }
});

function renderPaginationdocument(data, pageSizedocuments, currentPage = 1) {
    const totalPages = Math.ceil(data.length / pageSizedocuments);
    let paginationHtml = '';

    if (currentPage > 1) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
    }

    const maxVisible = 3;
    const pageRange = [];

    pageRange.push(1);

    if (currentPage > maxVisible) {
        pageRange.push('...');
    }

    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
        pageRange.push(i);
    }

    if (currentPage < totalPages - maxVisible + 1) {
        pageRange.push('...');
    }

    if (totalPages > 1) {
        pageRange.push(totalPages);
    }

    for (let i = 0; i < pageRange.length; i++) {
        if (pageRange[i] === '...') {
            paginationHtml += `<span class="dots">...</span>`;
        } else {
            const page = pageRange[i];
            const activeClass = page === currentPage ? 'active' : '';
            const bgColor = page === currentPage ? 'style="background-color: #080fd399;"' : '';
            paginationHtml += `<button class="page-btnviews ${activeClass}" data-page="${page}" ${bgColor}>${page}</button>`;
        }
    }

    if (currentPage < totalPages) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage + 1}">Next</button>`;
    }

    $('#paginationdocument').html(paginationHtml);

    $('.page-btnviews').click(function () {
        const pageNum = $(this).data('page');
        renderPaginationdocument(data, pageSizedocuments, pageNum);
        renderTabledocument(data, pageSizedocuments, pageNum);
    });

    renderTabledocument(data, pageSizedocuments, currentPage);
}


$('.ranges, .applyBtn').on('click', function() {
    const activeTabId = $('.tab-pane.active').attr('id');
    if ($(this).hasClass('ranges')) {
    var moredatefittervale = $('#mydateviewsall').text().trim();
    var datefiltersave = $('#mydateviewsallsave').text().trim();
  }
   else if ($(this).hasClass('applyBtn')) {
        var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
        var dateRange = datefilltervaluenew.split(' - ');
        function convertDateFormat(dateStr) {
            let parts = dateStr.split('/');
            return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
        }
        var startDate = convertDateFormat(dateRange[0]);
        var endDate = convertDateFormat(dateRange[1]);
        var moredatefittervale = `${startDate} - ${endDate}`;
        var datefiltersave =`${startDate} - ${endDate}`;
    }
     if (activeTabId === 'analytics-tab-1-pane') {
        if (moredatefittervale) ticketdatefillterrange(moredatefittervale, fitterremovedata);
    } else if (activeTabId === 'analytics-tab-2-pane') {
        if (datefiltersave) datefillterrange(datefiltersave, fitterremovedata);
    }
});

function cancelformdata() {
      startLoader();
        $("#document_tbl").hide();
		var moredatefittervale = $('#mydateallviews').text();
        // var billnovale = $('#hidden_billno').val();
		$(".value_views_mysearch").text("");
		$.ajax({
			url: cancelform_data,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
                // billnoval:billnovale,
			},
			success: function (responseData) {
                stopLoader(true);
				handleSuccess(responseData);
			},
			error: function (xhr, status, error) {
                 stopLoader(false, error);
				console.error("AJAX Error:", status, error);
			}
		});
	}


    function dismorefilterview(uniqueResults,url,moredatefittervale, ph_id = '') {
          startLoader();
            $("#document_tbl").hide();
        if(uniqueResults!="")
           {
           var morefilltersall=uniqueResults.join(" AND ");
           $.ajax({
               url: url,
               type: "GET",
               data: {
                   morefilltersall: morefilltersall,
                   moredatefittervale:moredatefittervale,
                    mrodnofilter: ph_id
               },
               success: function (responseData) {
                stopLoader(true);
                   handleSuccess(responseData);
               },
               error: function (xhr, status, error) {
                 stopLoader(false, error);
                   console.error("AJAX Error:", status, error);
               }
           });
           }else
          {
           $('.clear_my_views').hide();
            $(".my_search_view").hide();
            cancelformdata();

          }
       }
  function cancelsavefilterview(uniqueResults,url,moredatefittervale, ph_id = '') {
          startLoader2();
            $('sveddata_tbl').hide();
        if(uniqueResults!="")
           {
           var morefilltersall=uniqueResults.join(" AND ");
           $.ajax({
               url: url,
               type: "GET",
               data: {
                   morefilltersall: morefilltersall,
                   moredatefittervale:moredatefittervale,
                    mrodnofilter: ph_id
               },
               success: function (responseData) {
                stopLoader2(true);
                   handlesavedSuccess(responseData);
               },
               error: function (xhr, status, error) {
                 stopLoader2(false, error);
                   console.error("AJAX Error:", status, error);
               }
           });
           }else
          {
           $('.clear_my_saveviews').hide();
            $(".my_search_saveview").hide();
            cancelsaveformdata();

          }
       }
function getDeduplicatedPatients(data) {
    const uniquePatientsMap = new Map();
    data.forEach(user => {
        const key = user.can_mrdno || user.phid || '';
        if (key && !uniquePatientsMap.has(key)) {
            uniquePatientsMap.set(key, user);
        }
    });
    return Array.from(uniquePatientsMap.values());
}

function handleSuccess(responseData) {
    $("#document_tbl").show();

    dataSourcedocument = responseData.checkinData || [];
    const deduplicated = getDeduplicatedPatients(dataSourcedocument);
    const pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val());

    renderPaginationdocument(deduplicated, pageSizedocuments);
    renderTabledocument(deduplicated, pageSizedocuments, 1);
}
// function handlesavedSuccess(responseData) {
//     console.log("responseData",responseData);

//     $("#sveddata_tbl").show();
//     dataSaved = responseData || [];
//     const pageSizedocuments = parseInt($('#itemsPerPageSelectsave').val());
//     renderPaginationsaved(dataSaved, pageSizedocuments, 1);
// }

// function renderPaginationsaved(data, pageSizedocuments, currentPage = 1) {
//     console.log("data",data);

//     const totalPages = Math.ceil(data.length / pageSizedocuments);
//     let paginationHtml = '';

//     if (currentPage > 1) {
//         paginationHtml += `<button class="page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
//     }

//     const maxVisible = 3;
//     const pageRange = [];

//     pageRange.push(1);

//     if (currentPage > maxVisible) {
//         pageRange.push('...');
//     }

//     const start = Math.max(2, currentPage - 1);
//     const end = Math.min(totalPages - 1, currentPage + 1);

//     for (let i = start; i <= end; i++) {
//         pageRange.push(i);
//     }

//     if (currentPage < totalPages - maxVisible + 1) {
//         pageRange.push('...');
//     }

//     if (totalPages > 1) {
//         pageRange.push(totalPages);
//     }

//     for (let i = 0; i < pageRange.length; i++) {
//         if (pageRange[i] === '...') {
//             paginationHtml += `<span class="dots">...</span>`;
//         } else {
//             const page = pageRange[i];
//             const activeClass = page === currentPage ? 'active' : '';
//             const bgColor = page === currentPage ? 'style="background-color: #080fd399;"' : '';
//             paginationHtml += `<button class="page-btnviews ${activeClass}" data-page="${page}" ${bgColor}>${page}</button>`;
//         }
//     }

//     if (currentPage < totalPages) {
//         paginationHtml += `<button class="page-btnviews" data-page="${currentPage + 1}">Next</button>`;
//     }

//     $('#paginationsavedata').html(paginationHtml);

//     $('.page-btnviews').click(function () {
//         const pageNum = $(this).data('page');
//         renderPaginationsaved(data, pageSizedocuments, pageNum);
//         renderTablesaved(data.isApprover ? data.data: data, pageSizedocuments, pageNum,data.isApprover ?data.isApprover:'');
//     });
//     renderTablesaved(data.isApprover ? data.data: data, pageSizedocuments, currentPage,data.isApprover ?data.isApprover:'');
// }
let tableData = [];
let isApprover = false;

function handlesavedSuccess(responseData) {
    console.log("responseData", responseData);

    $("#sveddata_tbl").show();

    // ✅ normalize
    tableData = Array.isArray(responseData?.data)
        ? responseData.data
        : [];

    isApprover = !!responseData?.isApprover;

    const pageSizedocuments = parseInt($('#itemsPerPageSelectsave').val()) || 10;

    renderPaginationsaved(tableData, pageSizedocuments, 1);
    renderStatisticsCards(responseData.statistics);
}

function renderPaginationsaved(dataArray, pageSizedocuments, currentPage = 1) {
console.log("dataArray",dataArray );
console.log("pageSizedocuments",pageSizedocuments );
console.log("currentPage",currentPage );

    if (!Array.isArray(dataArray)) return;

    const totalPages = Math.ceil(dataArray.length / pageSizedocuments);
    let paginationHtml = '';

    if (currentPage > 1) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
    }

    const maxVisible = 3;
    const pageRange = [1];

    if (currentPage > maxVisible) pageRange.push('...');

    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
        pageRange.push(i);
    }

    if (currentPage < totalPages - maxVisible + 1) pageRange.push('...');

    if (totalPages > 1) pageRange.push(totalPages);

    pageRange.forEach(page => {
        if (page === '...') {
            paginationHtml += `<span class="dots">...</span>`;
        } else {
            const active = page === currentPage ? 'active' : '';
            paginationHtml += `<button class="page-btnviews ${active}" data-page="${page}">${page}</button>`;
        }
    });

    if (currentPage < totalPages) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage + 1}">Next</button>`;
    }

    $('#paginationsavedata').html(paginationHtml);

    // click handler
    $('.page-btnviews').off('click').on('click', function () {
        const pageNum = $(this).data('page');
        renderPaginationsaved(dataArray, pageSizedocuments, pageNum);
        renderTablesaved(dataArray, pageSizedocuments, pageNum, isApprover);
    });

    // initial render
    renderTablesaved(dataArray, pageSizedocuments, currentPage, isApprover);
}

function renderStatisticsCards(statistics) {
    console.log('Rendering statistics:', statistics);
    
    const $container = $('#statisticsCards');
    
    const colorClasses = ['red', 'teal', 'blue', 'green', 'light-green', 'orange', 'purple', 'pink', 'grey'];
    const icons = {
        'file-text': 'fa-file-text',
        'check-circle': 'fa-check-circle',
        'shield-check': 'fa-shield-alt',
        'check-double': 'fa-check-double',
        'clock': 'fa-clock'
    };
    
    // Build HTML string
    let html = '';
    $.each(statistics, function(index, stat) {
        const colorClass = colorClasses[index % colorClasses.length];
        const iconClass = icons[stat.icon] || 'fa-chart-bar';
        const dataFilter = stat.label.toLowerCase().replace(/\s+/g, '-');
        
        html += `
            <div class="stat-card ${colorClass}" data-filter="${dataFilter}">
                <div class="stat-card-content">
                    <div class="stat-icon">
                        <i class="fas ${iconClass}"></i>
                    </div>
                    <div class="stat-count">${stat.count}</div>
                    <div class="stat-label">${stat.label}</div>
                </div>
            </div>
        `;
    });
    
    // Set the HTML
    $container.html(html);
    
    // Add click handlers for filtering (optional)
    $container.find('.stat-card').on('click', function() {
        const filter = $(this).data('filter');
        applyStatFilter(filter);
        
        // Visual feedback - remove active class from all cards
        $container.find('.stat-card').removeClass('active');
        // Add active class to clicked card
        $(this).addClass('active');
    });
}


function cancelsaveformdata() {
      startLoader2();
        $("#sveddata_tbl").hide();
		var moredatefittervale = $('#mydateallviewssave').text();
		$(".value_save_mysearch").text("");
		$.ajax({
			url: cancelformsave_data,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
			},
			success: function (responseData) {
                stopLoader2(true);
				handlesavedSuccess(responseData);
			},
			error: function (xhr, status, error) {
                 stopLoader2(false, error);
				console.error("AJAX Error:", status, error);
			}
		});
	}
// function renderTablesaved(data, pageSizedocuments, pageNum,isApprover) {
//     console.log("data",data);
//     console.log("pageSizedocuments",pageSizedocuments);
//     console.log("pageNum",pageNum);
//     console.log("isApprover",isApprover);
//     // var isApprover = data.isApprover;
//     // var data = data.data;
//     // var startIdx = (pageNum - 1) * pageSizedocuments;
//     // var endIdx = pageNum * pageSizedocuments;
//     // var pageData = data.slice(startIdx, endIdx);
//     // var body = "";
//     if (!Array.isArray(data)) return;

//     const startIdx = (pageNum - 1) * pageSizedocuments;
//     const endIdx = pageNum * pageSizedocuments;
//     const pageData = data.slice(startIdx, endIdx);
//     var totalItems = data.length;

//     let body = '';

//      $.each(pageData, function(index, user) {

//     // Determine gender and assign values accordingly
//     let gender = user.can_gender||'';
//    // Pull all possible values
//     let wifemrdno = (gender === 'F' ? user.can_mrdno : '') || 'N/A';
//     let wifename = (gender === 'F' ? user.can_name : '') || 'N/A';
//     let husmrdno = (gender === 'M' ? user.can_mrdno : '') || 'N/A';
//     let husname = (gender === 'M' ? user.can_name : '') || 'N/A';

//     // These fallback values are used in visible MRD column
//     let wmrd = wifemrdno;
//     let wname = wifename;
//     let hmrd = husmrdno;
//     let hname = husname;
//     function getStatusInfo(status) {
//         switch (parseInt(status)) {
//             case 1:
//                 return { text: '✔', color: 'green', size: '20px' };
//             case 2:
//                 return { text: '✖', color: 'red', size: '20px' };
//             default:
//                 return { text: 'Pending', color: '#666', size: '13px' };
//         }
//     }

//     let statusInfo = getStatusInfo(user.approve_status);

//     let statusTd = `
//     <td class="tdview approver-status"
//         style="font-weight:bold;
//             color:${statusInfo.color};
//             font-size:${statusInfo.size};">
//         ${statusInfo.text}
//     </td>`;

//     let actionTd = '';
//     console.log("isApprover",isApprover);

//     if (isApprover && user.approve_status == 0) {
//         actionTd = `
//         <td class="tdview ">
//             <div class="approver-action" data-id="${user.can_id}">
//                 <button class="btn btn-approve" data-status="1">✔</button>
//                 <button class="btn btn-reject" data-status="2">✖</button>
//             </div>
//         </td>`;
//     }
//     else{
//         actionTd = `<td class="tdview">
//             <div class="approver-action" style="display:flex;justify-content: center;" data-id="${user.can_id}"> - </div>
//         </td>`;
//     }



//     // Additional fields with fallbacks
//     let zone = user.zone_name || 'N/A';
//     let location = user.location_name || 'N/A';
//     let opno = user.can_op_no ||"-";
//     let form_status = user.can_form_status || 'test';
//     let age=user.can_age || '-';
//     let patient_ph = user.can_mobile || '-';
//     let counselled_by = user.can_consultant || '-';
//     let bill_no = user.can_bill_no || '-';
//     let billdate = formatBillDate(user.can_date || '-');
//     let billtype = user.can_form_status || '-';
//     // Build table row
//     body += `
//         <tr onclick="rowClick(event)">
        
//             <td class="tdview"><strong>#${index + 1}</strong></td>
//             <td class="tdview" data-id="${user.locationid}">${zone}</td>
//             <td class="tdview branchname">${location}</td>
//             <td class="tdview" data-ph_id="${wmrd}"><a href="#">${wmrd}<br>${wname}</a></td>
//             <td class="tdview" data-ph_id="${hmrd}"><a href="#">${hmrd}<br>${hname}</a></td>
//             <td class="tdview">${opno}</td>
//             <td class="tdview" style="display:none;">${form_status}</td>
//             <td class="tdview">${age}</td>
//             <td class="tdview">${gender}</td>
//             <td class="tdview">${patient_ph}</td>
//             <td class="tdview">${counselled_by}</td>
//             <td class="tdview bill_no">${bill_no}</td>
//             <td class="tdview">${billtype}</td>
//             <td class="tdview">${billdate}</td>
//             <td class="tdview">${user.can_total}</td>

//             <td class="tdview wifemrdno" style="display:none;" data-ph_id="${wmrd}"><a href="#">${wmrd}</a></td>
//             <td class="tdview husmrdno" style="display:none;" data-ph_id="${hmrd}"><a href="#">${hmrd}</a></td>
//             <td class="tdview wifename" style="display:none;"><a href="#">${wname}</a></td>
//             <td class="tdview husname" style="display:none;"><a href="#">${hname}</a></td>

//             ${statusTd}
//             ${isApprover ? actionTd : ''}

//             <td class="tdview">
//                 <a href="#"><img src="../assets/images/edit.png" style="width: 23px;" class="edit_can_details_new" alt="Icon"></a>
//             </td>
//             <td class="tdview" style="padding:3px;" id="savebtn">
//                 <a href="#" style="margin-left: 5px;">
//                     <img src="../assets/images/print.png" style="width: 23px;margin-right:5px;" alt="Icon" class="icon print_can_details">
//                 </a>
//             </td>
//         </tr>`;
//       });

//     if (pageData.length === 0) {
// 			body += '<tr><td colspan="15" class="tdview" style="text-align: center;">No data available</td></tr>';
// 		}

//     $("#sveddata_tbl").html(body);
//     $("#billsavecounts").text(totalItems);
// }

function renderTablesaved(data, pageSizedocuments, pageNum, isApprover) {

    if (!Array.isArray(data)) return;

    const startIdx = (pageNum - 1) * pageSizedocuments;
    const endIdx   = pageNum * pageSizedocuments;
    const pageData = data.slice(startIdx, endIdx);
    const totalItems = data.length;

    let body = '';
    let access_limits = admin_user.access_limits;

    // ===============================
    // STATUS ICON HELPER (same as discount)
    // ===============================
    function renderStatus(status) {
        status = parseInt(status);
        if (status === 1) {
            return `<span style="color:green;font-weight:bold;font-size:20px;">✔</span>`;
        } else if (status === 2) {
            return `<span style="color:red;font-weight:bold;font-size:20px;">✖</span>`;
        } else {
            return `<span style="color:#f0ad4e;font-weight:bold;font-size:14px;">⏳ Pending</span>`;
        }
    }

    $.each(pageData, function (index, user) {

        // ===============================
        // GENDER BASED MRD
        // ===============================
        let id   = user.can_id || '';
        let gender   = user.can_gender || '';
        let wmrd     = (gender === 'F' ? user.can_mrdno : '') || 'N/A';
        let wname    = (gender === 'F' ? user.can_name  : '') || 'N/A';
        let hmrd     = (gender === 'M' ? user.can_mrdno : '') || 'N/A';
        let hname    = (gender === 'M' ? user.can_name  : '') || 'N/A';

        // ===============================
        // BASIC FIELDS
        // ===============================
        let form_status = user.can_form_status || '-';
        let zone        = user.zone_name || 'N/A';
        let location    = user.location_name || 'N/A';
        let opno        = user.can_op_no || '-';
        let age         = user.can_age || '-';
        let phone       = user.can_mobile || '-';
        let consultant  = user.can_consultant || '-';
        let billno      = user.can_bill_no || '-';
        let billdate    = formatBillDate(user.can_date);
        let billtype    = user.can_form_status || '-';
        let total       = user.can_total || '-';
        let patient_ph = user.can_mobile || '-';
        // ===============================
        // ACTION BUTTONS (ROLE BASED)
        // ===============================
        let actionHtml = '-';

        if (access_limits == 1 && user.final_approver == 0) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }
        else if (access_limits == 2 && user.zonal_approver == 0) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }
        else if (access_limits == 3 && user.admin_approver == 0) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }
        else if (access_limits == 4 && user.audit_approver == 0) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }

        // ===============================
        // ROW START
        // ===============================
        body += `
        <tr onclick="rowClick(event)" data-id="${user.can_id}">
            
            <td class="tdview"><strong>#${index + 1}</strong></td>
            <td class="tdview" data-id="${user.locationid}">${zone}</td>
            <td class="tdview branchname">${location}</td>
            <td class="tdview" data-ph_id="${wmrd}"><a href="#">${wmrd}<br>${wname}</a></td>
            <td class="tdview" data-ph_id="${hmrd}"><a href="#">${hmrd}<br>${hname}</a></td>
            <td class="tdview">${opno}</td>
            <td class="tdview" style="display:none;">${form_status}</td>
            <td class="tdview">${age}</td>
            <td class="tdview">${gender}</td>
            <td class="tdview">${patient_ph}</td>
            <td class="tdview">${consultant}</td>
            <td class="tdview bill_no">${billno}</td>
            <td class="tdview">${billtype}</td>
            <td class="tdview">${billdate}</td>
            <td class="tdview">${total}</td>

            <td class="tdview wifemrdno" style="display:none;" data-ph_id="${wmrd}"><a href="#">${wmrd}</a></td>
            <td class="tdview husmrdno" style="display:none;" data-ph_id="${hmrd}"><a href="#">${hmrd}</a></td>
            <td class="tdview wifename" style="display:none;"><a href="#">${wname}</a></td>
            <td class="tdview husname" style="display:none;"><a href="#">${hname}</a></td>

            <td class="tdview">${user.username+'</br>'+ user.userid}</td>
        `;

        // ===============================
        // APPROVAL COLUMNS (MATCH DISCOUNT)
        // ===============================
        if (access_limits == 1) {
            body += `
                <td class="tdview" data-column="admin-approver">${renderStatus(user.admin_approver)}</td>
                <td class="tdview" data-column="zonal-approver">${renderStatus(user.zonal_approver)}</td>
                <td class="tdview" data-column="dit-approver">${renderStatus(user.audit_approver)}</td>
                <td class="tdview" data-column="final-approver">${renderStatus(user.final_approver)}</td>
                <td class="tdview approver-action">${actionHtml}</td>`;
        }
        else if (access_limits == 2) {
            body += `
                <td class="tdview" data-column="admin-approver">${renderStatus(user.admin_approver)}</td>
                <td class="tdview" data-column="dit-approver">${renderStatus(user.audit_approver)}</td>
                <td class="tdview" data-column="final-approver">${renderStatus(user.final_approver)}</td>
                <td class="tdview" data-column="zonal-approver">${renderStatus(user.zonal_approver)}</td>
                <td class="tdview approver-action">${actionHtml}</td>`;
        }
        else if (access_limits == 3) {
            body += `
                <td class="tdview" data-column="zonal-approver">${renderStatus(user.zonal_approver)}</td>
                <td class="tdview" data-column="dit-approver">${renderStatus(user.audit_approver)}</td>
                <td class="tdview" data-column="final-approver">${renderStatus(user.final_approver)}</td>
                <td class="tdview" data-column="admin-approver">${renderStatus(user.admin_approver)}</td>
                <td class="tdview approver-action">${actionHtml}</td>`;
        }
        else if (access_limits == 4) {
            body += `
                <td class="tdview" data-column="admin-approver">${renderStatus(user.admin_approver)}</td>
                <td class="tdview" data-column="zonal-approver">${renderStatus(user.zonal_approver)}</td>
                <td class="tdview" data-column="final-approver">${renderStatus(user.final_approver)}</td>
                <td class="tdview" data-column="dit-approver">${renderStatus(user.audit_approver)}</td>
                <td class="tdview approver-action">${actionHtml}</td>`;
        }

        // ===============================
        // EDIT + PRINT
        // ===============================
        body += `
            <td class="tdview">
                <img src="../assets/images/edit.png" class="edit_can_details_new" style="width:23px;">
            </td>
            <td class="tdview">
                <img src="../assets/images/print.png" class="print_can_details" style="width:23px;">
            </td>
        </tr>`;
    });

    if (pageData.length === 0) {
        body = `<tr><td colspan="20" class="tdview text-center">No data available</td></tr>`;
    }

    $("#sveddata_tbl").html(body);
    $("#billsavecounts").text(totalItems);
}

    var fitterremovedata = [];
    function ticketdatefillterrange(moredatefittervale,fitterremovedata) {

    currentFilter = moredatefittervale;
	var morefilltersall=fitterremovedata.join(" AND ");
   startLoader();
    $("#document_tbl").hide();
    $.ajax({
        url: cancelform_data,
        type: "GET",
        data: {
      moredatefittervale: currentFilter,
	  morefilltersall: morefilltersall,
        },
        success: function (responseData) {
            stopLoader(true);
            handleSuccess(responseData);
        },
        error: function (xhr, status, error) {
            stopLoader(false, error);
            console.error("AJAX Error:", status, error);
        }
    });
}
 var fitterremovedata = [];
    function datefillterrange(datefiltersave,fitterremovedata) {
    currentFilter = datefiltersave;
	var morefilltersall=fitterremovedata.join(" AND ");
   startLoader2();
        $("#sveddata_tbl").hide();
    $.ajax({
        url: cancelsave_data,
        type: "GET",
        data: {
      moredatefittervale: currentFilter,
	  morefilltersall: morefilltersall,
        },
        success: function (responseData) {
            stopLoader2(true);
            handlesavedSuccess(responseData);
        },
        error: function (xhr, status, error) {
             stopLoader2(false, error);
            console.error("AJAX Error:", status, error);
        }
    });
}


   $(document).on('click', '.sec_options_marketers div', function () {
    $(".my_value_views").text("");
    $('.clear_my_views').show();
    $(".my_search_view").show();
    var moredatefittervale = $('#mydateallviews').text();
    let resultsArray_marketer = [];
    $(".documentdatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const result =  $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });

    const moreFilterValues_market = [
        $("#can_zone_views").val(),
        $('#can_loc_views').val(),
       $('#can_mrd_views').val()
    ];
    // alert(moreFilterValues_market);
    // Update the UI with the selected filter values
    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
        $(this).text(filterValue);
    });

     fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    // Call function with the processed data
    dismorefilterview(fitterremovedata,cancelform_data,moredatefittervale);
});

   $(document).on('click', '.savedata_options div', function () {
    $(".my_savevalue_views").text("");
    $('.clear_my_saveviews').show();
    $(".my_search_saveview").show();
    var moredatefittervale = $('#mydateallviewssave').text();
    let resultsArray_marketer = [];
    $(".savedatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const result =  $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });
    const moreFilterValues_market = [
        $("#save_zone_views").val(),
        $('#save_loc_views').val(),
       $('#save_mrd_views').val()
    ];
    // alert(moreFilterValues_market);
    // Update the UI with the selected filter values
    $(".value_save_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
        $(this).text(filterValue);
    });

     fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    // Call function with the processed data
    cancelsavefilterview(fitterremovedata,cancelsave_data,moredatefittervale);
});


$(document).on("click", ".value_save_mysearch", function () {

    var moredatefittervale = $('#mydateallviewssave').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");
    if(clear_filtr == 'savebranch_search'){
        $('#save_loc_views').val('');
    }
    if(clear_filtr == 'savezone_search'){
        $('#save_zone_views').val('');
    }
     if(clear_filtr == 'savemrdno_search'){
      $('#save_mrd_views').val('');
    }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      cancelsavefilterview(fitterremovedata,cancelsave_data,moredatefittervale);
});
$(document).on("click", ".clear_my_saveviews", function () {

    fitterremovedata.length = 0;

    $('.savedatasearch').val("");
    $(".my_savevalue_views").text("");
    $(".value_save_mysearch").text("");

    $('.clear_my_saveviews').hide();
    $(".my_search_saveview").hide();

    $(".dropdown-options div").removeClass("selected");
    $('#dissave_zone_id').val('');        // clear hidden zone ids
    $('#save_zone_views').val('');        // clear zone input
    $('#save_loc_views').val('');        
    $('#getlocationsave > div')
        .removeClass('filtered-by-zone')
        .show();

    $('#getlocationsave .no-results').remove();
    cancelsaveformdata();
});

// $(document).on("click", ".clear_my_saveviews", function () {
//     fitterremovedata.length = 0;
//     $('.savedatasearch').val("");
//     $(".my_savevalue_views").text("");
//     $('.clear_my_saveviews').hide();
//     $(".my_search_saveview").hide();
//     $(".value_save_mysearch").text("");
//     cancelsaveformdata();
// });

$('#save_mrd_views').on('input', function () {
    const ph_id = $(this).val().trim();
     $(".my_savevalue_views").text("");
    $('.clear_my_saveviews').show();
    $(".my_search_saveview").show();
    const moredatefittervale = $('#mydateallviewssave').text();
    let resultsArray_marketer = [];
    $(".savedatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const filterStr = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(filterStr);
        }
    });

    const moreFilterValues_market = [
        $("#save_zone_views").val(),
        $('#save_loc_views').val(),
        $('#save_mrd_views').val()
    ];

    $(".value_save_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    cancelsavefilterview(fitterremovedata,cancelform_data,moredatefittervale, ph_id);
});

$(document).on("click", ".clear_my_views", function () {

    fitterremovedata.length = 0;
    $('.documentdatasearch').val("");
    $(".my_value_views").text("");
    $('.clear_my_views').hide();
    $(".my_search_view").hide();
    $(".value_views_mysearch").text("");
    cancelformdata();
});
$(document).on("click", ".value_views_mysearch", function () {
    var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");
    if(clear_filtr == 'refbranch_search'){
        $('#can_loc_views').val('');
    }
    if(clear_filtr == 'refzone_search'){
        $('#can_zone_views').val('');
    }
     if(clear_filtr == 'refmrdno_search'){
      $('#can_mrd_views').val('');
    }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      dismorefilterview(fitterremovedata,cancelform_data,moredatefittervale);
});

$(document).on("click", ".my_value_views", function () {
    var moredatefittervale = $('#mydateallviews').text();

    var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();

    // Clear the text of the clicked element
    $(this).text("");

    // Find the index of the filter to remove
    let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

    let removedElement = "";
    if (indexToRemove !== -1) {
        removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // Get removed item
    }
    let key = removedElement.split('=')[0];
   if (key === 'phid') {
        $('#can_mrd_views').val('');
    }

    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim());
    });

    // Call function to refresh view based on updated filters
    dismorefilterview(fitterremovedata, cancelform_data, moredatefittervale);
});

// var fitterremovedata = [];
//     function ticketFilterAjax(sec_date_type,url){
//         $.ajax({
//                 url: url,
//                 type: "GET",
//                 data: {
//                     sec_date_type: sec_date_type,
//                     // moredatefittervale:moredatefittervale,
//                 },
//                 success: function (responseData) {
//                     handleSuccess(responseData);
//                     handlesavedSuccess(responseData)
//                 },
//                 error: function (xhr, status, error) {
//                     console.error("AJAX Error:", status, error);
//                 }
//           });
//     }



$('#can_mrd_views').on('input', function () {
    const ph_id = $(this).val().trim();
    $(".my_value_views").text("");
    $('.clear_my_views').show();
    $(".my_search_view").show();

    const moredatefittervale = $('#mydateallviews').text();
    let resultsArray_marketer = [];

    // Collect all filter input values
    $(".documentdatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const filterStr = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(filterStr);
        }
    });

    // Update the view text (for UI display)
    const moreFilterValues_market = [
        $("#can_zone_views").val(),
        $('#can_loc_views').val(),
        $('#can_mrd_views').val()
    ];

    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    // Finalize filter string array
    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));

    // Send both filters and individual MRD value (if needed separately)
    dismorefilterview(fitterremovedata,cancelform_data,moredatefittervale, ph_id);
});


var loaderInterval = null;
var loaderProgress = 0;

function startLoader() {
    const progressBar = $('.progress-bar');
    const progressText = $('.progress-bar');
    const errorMessage = $('#error-message');

    loaderProgress = 0;

    progressText.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    errorMessage.hide().text('');

    loaderInterval = setInterval(() => {
        if (loaderProgress < 90) {
            loaderProgress += 10;
            progressText.text(`Loading: ${loaderProgress}%`);
            progressBar.css('width', `${loaderProgress}%`);
        }
    }, 950);
}

function stopLoader(success = true, error = '') {
    const progressBar = $('.progress-bar');
    const progressText = $('.progress-bar');
    const errorMessage = $('#error-message');

    clearInterval(loaderInterval);

    if (success) {
        loaderProgress = 100;
        progressText.text(`Loading: 100% - Done`);
        progressBar.css({
            'width': '100%',
            'background-color': '#007bff'
        });

        setTimeout(() => {
            progressBar.hide();
            $("#document_tbl").show();
        }, 500);
    } else {
        progressText.text('Error loading data');
        progressBar.css('background-color', 'red');
        errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            progressBar.hide();
            $("#document_tbl").hide();
        }, 1000);
    }
}
function startLoader2() {
    const progressBar = $('.progress-bar2');
    const progressText = $('.progress-bar2');
    const errorMessage = $('#error-message');

    loaderProgress = 0;

    progressText.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    errorMessage.hide().text('');

    loaderInterval = setInterval(() => {
        if (loaderProgress < 90) {
            loaderProgress += 10;
            progressText.text(`Loading: ${loaderProgress}%`);
            progressBar.css('width', `${loaderProgress}%`);
        }
    }, 950);
}

function stopLoader2(success = true, error = '') {
    const progressBar = $('.progress-bar2');
    const progressText = $('.progress-bar2');
    const errorMessage = $('#error-message');

    clearInterval(loaderInterval);

    if (success) {
        loaderProgress = 100;
        progressText.text(`Loading: 100% - Done`);
        progressBar.css({
            'width': '100%',
            'background-color': '#007bff'
        });

        setTimeout(() => {
            progressBar.hide();
            $("#sveddata_tbl").show();
        }, 500);
    } else {
        progressText.text('Error loading data');
        progressBar.css('background-color', 'red');
        errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            progressBar.hide();
            $("sveddata_tbl").hide();
        }, 1000);
    }
    $(document).on("click", ".edit_can_details_new", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const row = $(this).closest("tr");
        const billNo = row.find(".bill_no").text().trim();
        console.log("billNo",billNo);
        
        loadCancelBillForEdit(billNo);
    });

    function loadCancelBillForEdit(billNo) {
        $.ajax({
            url: cancelbill_data,
            type: "GET",
            data: { bill_no: billNo },

            success: function (response) {
                if (!response || !response[0]) {
                    alert("No data found");
                    return;
                }

                fillCancelBillForm(response[0]);
                $('#exampleModal2').modal('show'); // 🔥 open AFTER fill
            },

            error: function () {
                alert("Failed to load cancel bill data");
            }
        });
    }
    function fillCancelBillForm(data) {

        /* ===============================
        BASIC HEADER DETAILS
        =============================== */

        $('#opno_edit').val(data.can_op_no || '');
        $('#token_no_edit').val(data.can_token_no || '');
        $('#billno_edit').val(data.can_bill_no || '');
        $('#bill_date_edit').val(toInputDate(data.can_date || ''));
        $('#consultant_edit').val(data.can_consultant || '');
        $('#zone_id_edit').val(data.location_name || '');
        $('#locationid').val(data.can_zone_id || '');

        $('#pat_name_edit').val(data.can_name || '');
        $('#pat_mrdno_edit').val(data.can_mrdno || '');
        $('#pat_age_edit').val(data.can_age || '');
        $('#pct_mobile_edit').val(data.can_mobile || '');

        $('#payment_type_edit').val(data.can_payment_type || '');
        $('#payment_details_edit').val(data.can_payment_details || '');

        $('#prebalanceamt_edit').val(data.can_previous_alance || '0.00');
        $('#receivableamt_edit').val(data.can_amount_receivable || '0.00');
        $('#receivedamt_edit').val(data.can_amount_received || '0.00');
        $('#advancedamt_edit').val(data.can_advance || '0.00');

        $('#receivedamtword_edit').val(data.can_amount_word || '');
        $('#advancedamtword_edit').val(data.can_advance_word || '');

        $('#cancelreason_edit').val(data.can_reason || '');
        $('#prepared_edit').val(data.can_prepared_by || '');

        /* ===============================
        GENDER RADIO
        =============================== */

        $('input[name="pat_gender_edit"]').prop('checked', false);
        if (data.can_gender === 'F' || data.can_gender === 'M') {
            $('input[name="pat_gender_edit"][value="' + data.can_gender + '"]')
                .prop('checked', true);
        }

        /* ===============================
        REQUEST TYPE (OP / IP / Pharmacy)
        =============================== */

        $('input[name="request_edit"]').prop('checked', false);
        if (data.can_form_status) {
            $('input[name="request_edit"][value="' + data.can_form_status + '"]')
                .prop('checked', true);
        }

        /* ===============================
        TABLE ITEMS
        =============================== */

        $('#product_detials_edit tbody').empty();

        const sno  = JSON.parse(data.can_sno || '[]');
        const part = JSON.parse(data.can_particulars || '[]');
        const qty  = JSON.parse(data.can_qty || '[]');
        const rate = JSON.parse(data.can_rate || '[]');
        const tax  = JSON.parse(data.can_tax || '[]');
        const amt  = JSON.parse(data.can_amount || '[]');

        let totalAmount = 0;

        for (let i = 0; i < part.length; i++) {

            const amount = parseFloat(amt[i] || 0);
            totalAmount += amount;

            const rowHtml = `
            <tr>
                <td>${i + 1}</td>
                <td><input type="text" class="tdinput" readonly value="${part[i] || ''}"></td>
                <td><input type="text" class="tdinput" readonly value="${qty[i] || ''}"></td>
                <td><input type="text" class="tdinput" readonly value="${rate[i] || ''}"></td>
                <td><input type="text" class="tdinput" readonly value="${tax[i] || ''}"></td>
                <td><input type="text" class="tdinput" readonly value="${amount.toFixed(2)}"></td>
                <td></td>
            </tr>`;

            $('#product_detials_edit tbody').append(rowHtml);
        }

        $('#totalamt_edit').val(totalAmount.toFixed(2));

        /* ===============================
        ZONAL SIGN CONDITION
        =============================== */

        if (totalAmount >= 5000) {
            $('#zonal_sign_edit').removeClass('hide');
        } else {
            $('#zonal_sign_edit').addClass('hide');
        }

        /* ===============================
        SIGNATURE PREVIEWS
        =============================== */

        setSignPreview('#adminimgPreviewe', data.can_admin_sign);
        setSignPreview('#ccimgPreviewe', data.can_zonal_sign);
    }
   function toInputDate(dateStr) {
        if (!dateStr) return '';

        // Case 1: yyyy-mm-dd or yyyy-mm-dd HH:mm:ss
        const isoMatch = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (isoMatch) {
            return `${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]}`;
        }

        // Case 2: dd/mm/yyyy
        const slashMatch = dateStr.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
        if (slashMatch) {
            return `${slashMatch[3]}-${slashMatch[2]}-${slashMatch[1]}`;
        }

        return '';
    }

    function setSignPreview(selector, value) {
        if (!value) return;

        try {
            const parsed = JSON.parse(value);
            value = Array.isArray(parsed) ? parsed[0] : parsed;
        } catch {}

        $(selector).css('background-image', `url(../public/${value})`);
    }

}


function updateTableRow($row, record, status) {
    let access_limits = admin_user.access_limits;
    
    // Helper function to render status (same as renderTablesaved)
    function renderStatus(status) {
        status = parseInt(status);
        
        if (status === 1) {
            return `<span style="color:green;font-weight:bold;font-size: 20px;" title="Approved">✔</span>`;
        } else if (status === 2) {
            return `<span style="color:red;font-weight:bold;font-size: 20px;" title="Rejected">✖</span>`;
        } else {
            return `<span style="color:#f0ad4e;font-weight:bold;font-size: 14px;" title="Pending">⏳ Pending</span>`;
        }
    }
    
    // Find approval cells using data-column attribute
    let $adminCell = $row.find('[data-column="admin-approver"]');
    let $zonalCell = $row.find('[data-column="zonal-approver"]');
    let $auditCell = $row.find('[data-column="audit-approver"]');
    let $finalCell = $row.find('[data-column="final-approver"]');
    
    // Update cells with new status
    $adminCell.html(renderStatus(record.admin_approver));
    $zonalCell.html(renderStatus(record.zonal_approver));
    $auditCell.html(renderStatus(record.audit_approver));
    $finalCell.html(renderStatus(record.final_approver));
    
    // Hide action buttons after approval/rejection
    let $actionCell = $row.find('.approver-action');
    $actionCell.html('-');
    
    // Add success animation to the row
    $row.addClass('row-updated');
    setTimeout(() => {
        $row.removeClass('row-updated');
    }, 2000);
}

/**
 * Update count badges throughout the page
 */
function updateCountBadges(counts) {
    // Update any count badges you have in your UI
    $('.total-raised-count').text(counts.total_raised || 0);
    $('.admin-approved-count').text(counts.admin_approved || 0);
    $('.zonal-approved-count').text(counts.zonal_approved || 0);
    $('.audit-approved-count').text(counts.audit_approved || 0);
    $('.final-approved-count').text(counts.final_approved || 0);
    $('.pending-count').text(counts.pending || 0);
}
