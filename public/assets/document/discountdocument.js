$(document).ready(function () {
    //   if(fitterremovedata.length ==  0){
	// 		 var defaultLocation = "Chennai - Sholinganallur";
    //    var defaultZone = "TN CHENNAI";
	// 		$('#dis_loc_views').val(defaultLocation);
	// 	  $('#dis_zone_views').val(defaultZone);
	// 		$('.dropdown-options div').each(function() {

    //     if ($(this).text().trim() === defaultZone){
    //       $(this).addClass('selected');
    //     }
	// 		});
    //         $('#getlocation > div').each(function(){
    //     if ($(this).text().trim() === defaultLocation) {
    //     $(this).addClass('selected');
	// 			}
    //         });
	// 	}

    $(".my_search_view").hide();
    $(".my_search_saveview").hide();
     discountformdata();
     discountsaveformdata();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });
    // Clear error span text on input
    $('input, select, textarea').on('input change', function () {
        $(this).siblings('.errorss').text('');
    });
    $('#close-button').click(function () {
        // Clear all input fields
        $('input, select, textarea').val('');
        // Clear error messages
        $('.errorss').text('');
    });
    $(".documentscls div").click(function () {
        var selectedText = $(this).text(); // Get selected city name
        var selectedValue = $(this).attr('data-value'); // Get data-value attribute

        // alert(selectedValue);

        $('#zone_id').val(selectedText); // Set input value (visible text)
        $('#zone_id').attr('data-selected-value', selectedValue); // Store data-value in a custom attribute
    });

 $('#submit_discountform').click(function (event) {
    event.preventDefault();
        let isValid = true;

        if ($('#zone_id').val() === "") {
            $('.error_location').text('Please select the location Name');
            isValid = false;
        }
        if ($('#wife_name').val() === "") {
            $('.error_wifename').text('Enter the Wife Name');
            isValid = false;
        }
        if ($('#wifemrdno').val() === ""){
          $(".error_wifemrd").text('Enter the Wife MRD No');
        }
         if ($('#husband_name').val() === ""){
          $(".error_husname").text('Enter the Husband Name');
        }
         if ($('#husbandmrdno').val() === ""){
          $(".error_husmrd").text('Enter the Husband MRD No');
        }
         if ($('#service_name').val() === ""){
          $(".error_treatment").text('Enter the Service Name');
        }
         if ($('#totalbill').val() === ""){
          $(".error_total").text('Enter the Total Bill Value');
        }
         if ($('#ex_discount_display').val() === ""){
          $(".error_exp").text('Enter the Expected Discount');
        }
         if ($('#post_discount_display').val() === ""){
          $(".error_post").text('Enter the Post Discount');
        }
         if ($('#patientph').val() === ""){
          $(".error_patient").text('Enter the Patient Ph. No');
        }
         if ($('#counselled_by').val() === ""){
          $(".error_counsel").text('Enter the Counselled By');
        }
        if(admin_user.access_limits == 2){
            if ($('#branch_no').val() === ""){
              $(".error_brno").text('Enter the B.R. No.');
            }
             if ($('#authourised_by').val() === ""){
              $(".error_authouris").text('Enter the Authorised By');
            }
        }
        if(admin_user.access_limits == 1){
             if ($('#approveded_by').val() === ""){
              $(".error_approve").text('Enter the Final Approved By');
            }
        }
        if (!isValid) {
            return;
        }

    const formData = new FormData();
    // Append text fields
    const fields = {
        'dis_zone_id': $('#zone_id').attr('data-value'),
        'dis_wife_name': $('#wife_name').val(),
        'dis_wife_mrd_no': $('#wifemrdno').val(),
        'dis_husband_name': $('#husband_name').val(),
        'dis_husband_mrd_no': $('#husbandmrdno').val(),
        'dis_service_name': $('#service_name').val(),
        'dis_total_bill': $('#totalbill').val(),
        'dis_expected_request': parseFloat($('#ex_discount_value').text().replace('₹', '').trim()) || 0 ,
        'dis_form_status': $('input[name="request"]:checked').val() || '',
        'dis_post_discount': parseFloat($('#post_discount_value').text().replace('₹', '').trim()) || 0 ,
        'dis_patient_ph': $('#patientph').val(),
        'dis_counselled_by': $('#counselled_by').val(),
        'dis_final_auth': $('#final_amount').val(),
        'dis_branch_no': $('#branch_no').val(),
        'dis_auth_by': $('#authourised_by').val(),
        'dis_approved_by' : $('#approveded_by').val(),
        '_token': $('meta[name="csrf-token"]').attr('content')
    };

    for (const key in fields) formData.append(key, fields[key]);
    const signers = [
        { field: 'dis_wife_sign', radioName: 'wife-signature', canvasId: 'wifeCanvas', fileInputId: 'imageUpload', uploadDiv: '#wife-upload' },
        { field: 'dis_husband_sign', radioName: 'husband-signature', canvasId: 'husbandCanvas', fileInputId: 'husbandsignimg', uploadDiv: '#husband-upload' },
        { field: 'dis_drsign', radioName: 'dr-signature', canvasId: 'drCanvas', fileInputId: 'drsignimg', uploadDiv: '#dr-upload' },
        { field: 'dis_cc_sign', radioName: 'cc-signature', canvasId: 'ccCanvas', fileInputId: 'ccsignimg', uploadDiv: '#cc-upload' },
        { field: 'dis_admin_sign', radioName: 'admin-signature', canvasId: 'adminCanvas', fileInputId: 'adminsignimg', uploadDiv: '#admin-upload' },
    ];

    let pendingBlobs = 0;

    const finishSubmission = () => {
        if (pendingBlobs === 0) {
            $.ajax({
                url: discountform_documentaddedUrl,
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
                } else {
                    window.dispatchEvent(new CustomEvent('swal:toast', {
                        detail: {
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            background: '#f8d7da',
                        }
                    }));
                }

                    $("#exampleModaluser").modal('hide');
                    discountformdata();
                    clearForm();
                },
                error: function (error) {
                    console.error(error.responseJSON);
                },
            });
        }
    };

    signers.forEach(signer => {
        const isUpload = $(`input[name="${signer.radioName}"]:checked`).val() === 'upload';
        if (isUpload) {
            const file = document.getElementById(signer.fileInputId)?.files[0];
            if (file) formData.append(signer.field, file);
        } else {
            const canvas = document.getElementById(signer.canvasId);
            if (canvas) {
                pendingBlobs++;
                canvas.toBlob(blob => {
                    if (blob) formData.append(signer.field, blob, signer.field + '.png');
                    pendingBlobs--;
                    finishSubmission();
                });
            }
        }
    });
// Validate signatures before proceeding
let allSignaturesValid = true;

signers.forEach(signer => {
    const isUpload = $(`input[name="${signer.radioName}"]:checked`).val() === 'upload';
    const isCanvas = $(`input[name="${signer.radioName}"]:checked`).val() === 'canvas';

    // Check if neither upload nor canvas is selected
    if (!isUpload && !isCanvas) {
        allSignaturesValid = false;
        $(`${signer.uploadDiv} .sign-error`).remove(); // remove old errors if any
        $(`${signer.uploadDiv}`).append('<span class="text-danger sign-error">Please select and provide a signature.</span>');
    } else {
        // Further check: if upload selected, ensure file is present
        if (isUpload) {
            const file = document.getElementById(signer.fileInputId)?.files[0];
            if (!file) {
                allSignaturesValid = false;
                $(`${signer.uploadDiv} .sign-error`).remove();
                $(`${signer.uploadDiv}`).append('<span class="text-danger sign-error">Please upload a signature image.</span>');
            }
        }

        // If canvas selected, check if signature is actually drawn
        if (isCanvas) {
            const canvas = document.getElementById(signer.canvasId);
            if (canvas && isCanvasBlank(canvas)) {
                allSignaturesValid = false;
                $(`${signer.uploadDiv} .sign-error`).remove();
                $(`${signer.uploadDiv}`).append('<span class="text-danger sign-error">Please sign on the canvas.</span>');
            }
        }
    }
});

// Stop submission if any signature is invalid
if (!allSignaturesValid) {
    return;
}

    // If no digital signatures to wait for
    if (pendingBlobs === 0) finishSubmission();
});
function isCanvasBlank(canvas) {
    const context = canvas.getContext('2d');
    const pixelBuffer = new Uint32Array(
        context.getImageData(0, 0, canvas.width, canvas.height).data.buffer
    );
    return !pixelBuffer.some(color => color !== 0);
}

function clearForm(){
   $('.dropdown-item-loc.selected').removeClass('selected');
 $('#zone_id').attr('data-value', '');
 $('#zone_id').val("");
                $('#sno').val("");
                $('#dissno').val("");
                $('#wife_name').val("");
                $('#wifemrdno').val("");
                $('#husband_name').val("");
                $('#husbandmrdno').val("");
                $('#service_name').val("");
                $('#totalbill').val("");
                $('#ex_discount_display').val("");
                $('#ex_discount_value').text("");
                $('input[name="expectdis"]').prop("checked", false);
                $('input[name="request"]').prop("checked", false);
                $('input[name="postdis"]').prop("checked", false);
                $('#post_discount_display').val("");
                $('#post_discount_value').text("");
                $('#patientph').val("");
                $('#counselled_by').val("");
                $('#final_amount').val("");
                $('#branch_no').val("");
                $('#authourised_by').val("");
                $('#approveded_by').val("");
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

  $(document).on("click", '.edittddata', function (e) {
      e.preventDefault();

      var id = $(this).closest('tr').find('#idfetch').data('id');
        var row = $(this).closest('tr');
        var wifemrdno = row.find(".wifemrdno").text();
        var husmrdno =row.find('.husmrdno').text();
        $.ajax({
          url: discountform_data,
          type: "GET",
          data: {
              dis_wife_mrd_no: wifemrdno,
              dis_husband_mrd_no: husmrdno
             },
          success: function (response) {

      if (!response || !Array.isArray(response) || !response[0]) {
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
         $('#exampleModal3').modal('show');
        var imageviewsignw = data.dis_wife_sign;
        var imagehusband = data.dis_husband_sign;
        var imagedr = data.dis_drsign;
        var imagecc= data.dis_cc_sign;
        var imageadmin = data.dis_admin_sign;
        imageviews = imageviewsignw.replace(/[\[\]\"]/g, '');
        imageviewshusband = imagehusband.replace(/[\[\]\"]/g, '');
        imageviewsdr = imagedr.replace(/[\[\]\"]/g, '');
        imageviewscc = imagecc.replace(/[\[\]\"]/g, '');
        imageviewsadmin = imageadmin.replace(/[\[\]\"]/g, '');
        $('.location-dropdown-options div').removeClass('selected'); // clear previous
        var $selectedItem = $('.location-dropdown-options div[data-value="'+data.dis_zone_id+'"]');
        $selectedItem.addClass('selected');
        var selectedText = $selectedItem.text();

    $('#edit_zone_id')
        .val(selectedText)
        .attr('data-value', data.dis_zone_id);
            $('#edit_dis_sno').val(data.dis_id);
            $('#hidden_wifemrd').val(data.dis_wife_mrd_no);
            $('#hidden_husmrd').val(data.dis_husband_mrd_no);
            $('#edittd_dis_total_bill').val(data.dis_total_bill);
            $('#edittd_dis_expected_request').val(data.dis_expected_request);
            $('#ex_discount_value_edit').text(data.dis_expected_request);
            $("input[name='disrequest'][value='" + data.dis_form_status + "']").prop("checked", true);
            $('#edittd_dis_post_discount').val(data.dis_post_discount);
            $('#post_discount_value_edit').text(data.dis_post_discount);
            $('#edittd_dis_counselled_by').val(data.dis_counselled_by);
            $('#edittd_dis_final_amt').val(data.dis_final_auth);
            $('#edittd_dis_brno').val(data.dis_branch_no);
            $('#edittd_dis_authorised').val(data.dis_auth_by);
            $('#edittd_dis_approve').val(data.dis_approved_by);
            $('#imagewifesign').css('background-image', 'url(../public/'+ imageviews + ')');
            $('#husimgPreviewe').css('background-image', 'url(../public/' + imageviewshusband + ')');
            $('#drimgPreviewe').css('background-image', 'url(../public/' + imageviewsdr + ')');
            $('#ccimgPreviewe').css('background-image', 'url(../public/' + imageviewscc + ')');
            $('#adminimgPreviewpage').css('background-image', 'url(../public/' + imageviewsadmin + ')');
            // $('#imagewifesign').css('background-image', 'url(../'+ imageviews + ')');
            // $('#husimgPreviewe').css('background-image', 'url(../' + imageviewshusband + ')');
            // $('#drimgPreviewe').css('background-image', 'url(../' + imageviewsdr + ')');
            // $('#ccimgPreviewe').css('background-image', 'url(../' + imageviewscc + ')');
            // $('#adminimgPreviewe').css('background-image', 'url(../' + imageviewsadmin + ')');
          }
        });

      });
    $("#editdiscountform").on("click", function (event) {
  event.preventDefault();
  const formData = new FormData();
  formData.append('dis_zone_id', $('#edit_zone_id').attr('data-value'));
  formData.append('dis_wife_mrd_no', $('#edit_dis_wife_mrd_no').val());
  formData.append('dis_husband_mrd_no', $('#edit_dis_husband_mrd_no').val());
  formData.append('dis_wife_name',$('#edit_dis_wife_name').val());
  formData.append('dis_husband_name',$('#edit_dis_husband_name').val());
  formData.append('dis_service_name',$('#edit_dis_service_name').val());
  formData.append('dis_total_bill', $('#edittd_dis_total_bill').val());
  formData.append('dis_expected_request', parseFloat($('#ex_discount_value_edit').text().replace('₹', '').trim()) || 0);
  formData.append('dis_form_status', $('input[name="disrequeste"]:checked').val() || '');
  formData.append('dis_post_discount', parseFloat($('#post_discount_value_edit').text().replace('₹', '').trim()) || 0);
  formData.append('dis_counselled_by', $('#edittd_dis_counselled_by').val());
  formData.append('dis_final_auth', $('#edittd_dis_final_amt').val());
  formData.append('dis_branch_no', $('#edittd_dis_brno').val());
  formData.append('dis_auth_by', $('#edittd_dis_auth_by').val());
  formData.append('dis_approved_by', $('#edittd_dis_final_approve').val());
  const signers = [
    { field: 'dis_wife_sign', radio: 'editwife-signature', canvasId: 'editwifeCanvas', fileInput: 'imagewsign' },
    { field: 'dis_husband_sign', radio: 'edithusband-signature', canvasId: 'edithusbandCanvas', fileInput: 'husbandsignimge' },
    { field: 'dis_drsign', radio: 'editdr-signature', canvasId: 'editdrCanvas', fileInput: 'drsignimge' },
    { field: 'dis_cc_sign', radio: 'editcc-signature', canvasId: 'editccCanvas', fileInput: 'ccsignimge' },
    { field: 'dis_admin_sign', radio: 'editadmin-signature', canvasId: 'editadminCanvas', fileInput: 'adminsignimge' },
  ];

  let pendingBlobs = 0;
  const finishEditSubmission = () => {
    if (pendingBlobs === 0) {
      $.ajax({
        url: discountformeditUrl,
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
             document.getElementById('analytics-tab-2').click();
          } else {
            window.dispatchEvent(new CustomEvent('swal:toast', {
              detail: {
                title: 'Error!',
                text: response.message,
                icon: 'error',
                background: '#f8d7da',
              }
            }));
          }
          discountformdata();
       discountsaveformdata();
          $("#exampleModal2").modal('hide');
           $('#ex_discount_value_edit').text("");
           $('#post_discount_value_edit').text("");
        },
        error: function (error) {
          console.error(error.responseJSON);
        },
      });
    }
  };

  signers.forEach(signer => {
    const isUpload = $(`input[name="${signer.radio}"]:checked`).val() === 'upload';
    if (isUpload) {
      const file = document.getElementById(signer.fileInput)?.files[0];
      if (file) formData.append(signer.field, file);
    } else {
      const canvas = document.getElementById(signer.canvasId);
      if (canvas) {
        pendingBlobs++;
        canvas.toBlob(blob => {
          if (blob) formData.append(signer.field, blob, signer.field + '.png');
          pendingBlobs--;
          finishEditSubmission();
        });
      }
    }
  });

  if (pendingBlobs === 0) finishEditSubmission();
});

});
$('.dropdown-item-loc').on('click', function () {
        console.log(213123);
    var selectedBranchName = $(this).text();
    var selectedBranchId = $(this).data('value');
    $('#zone_id').val(selectedBranchName);
    $('#zone_id').attr('data-value', selectedBranchId);
    // $('.dropdown-options').hide();
});
// $(document).on('click', '.dropdown-item-loc', function () {
    
//     var selectedBranchName = $(this).text().trim();
//     var selectedBranchId   = $(this).data('value');

//     $('#zone_id').val(selectedBranchName);
//     $('#zone_id').attr('data-value', selectedBranchId);

//     // optional: close dropdown
//     $(this).closest('.dropdown').removeClass('active');
// });

$('.dropdown-item-loc_edit').on('click', function () {
    var selectedBranchName = $(this).text();
    var selectedBranchId = $(this).data('value');
    $('#edit_zone_id').val(selectedBranchName);
    $('#edit_zone_id').attr('data-value', selectedBranchId);
});
var dataSourcedocument = [];

function discountformdata() {
   startLoader();
    $("#document_tbl").hide();
    var moredatefittervale = $('#mydateallviews').text();
    $(".value_views_mysearch").text("");
    $("#my_ticket_details1").show();
    $.ajax({
        url: disdocdetialsUrl,
        type: "GET",
        data: {
            moredatefittervale: moredatefittervale,
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

function handleSuccess(responseData) {
           $("#my_ticket_details1").hide();
            $("#document_tbl").show();
             dataSourcedocument = responseData.checkinData || [];
            totalItems = responseData.length;
            var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val()); // Get selected items per page
            renderPaginationdocument(dataSourcedocument, pageSizedocuments);
            renderTabledocument(dataSourcedocument, pageSizedocuments, 1); // Show first page initially

}

  $(document).on("click", '.edit_dis_details', function () {
        $('#exampleModal2').modal('show');
        var id = $(this).closest('tr').find('#idfetch').data('id');
        console.log("id",id);

        var row = $(this).closest('tr');
        var zoneId = row.find('.locationname').data('id');
        var wifemrdno = row.find('.wifemrdno').text().trim();
        var wifename = row.find('.wifename').text().trim();
        var husmrdno = row.find('.husmrdno').text().trim();
        var husname = row.find('.husname').text().trim();
        var treatmentcat = row.find('.treatmentcat').text().trim();
        var totalbil = row.find('.totalbil').text().trim();
        var expamount = row.find('.expamount').text().trim();
        var requestfor = row.find('.requestfor').text().trim();
        var postdis = row.find('.postdis').text().trim();
        var mobileno = row.find('.mobileno').text().trim();
        var consultby = row.find('.consultby').text().trim();
        var authby = row.find('.authby').text().trim();
        var finalapprove = row.find('.finalapprove').text().trim();
        var brno = row.find('.brno').text().trim();
        var finalamount = row.find('.finalamount').text().trim();
        var wife_sign =row.find('.wife_sign').text().trim();
        var husband_sign =row.find('.husband_sign').text().trim();
        var drsign =row.find('.drsign').text().trim();
        var cc_sign =row.find('.cc_sign').text().trim();
        var admin_sign = row.find('.admin_sign').text().trim();
        imageviewswife = wife_sign.replace(/[\[\]\"]/g, '');
        imageviewshus = husband_sign.replace(/[\[\]\"]/g, '');
        imageviewsdr = drsign.replace(/[\[\]\"]/g, '');
        imageviewscc = cc_sign.replace(/[\[\]\"]/g, '');
        imageviewsadmin = admin_sign.replace(/[\[\]\"]/g, '');

         $('#imagewifesign').css('background-image', 'url(../public/' + imageviewswife + ')');
            $('#husimgPreviewe').css('background-image', 'url(../public/' + imageviewshus + ')');
            $('#drimgPreviewe').css('background-image', 'url(../public/' + imageviewsdr + ')');
        $('#ccimgPreviewe').css('background-image', 'url(../public/' + imageviewscc + ')');
            $('#adminimgPreviewe').css('background-image', 'url(../public/' + imageviewsadmin + ')');

        $('.location-dropdown-options div').removeClass('selected'); // clear previous
        var $selectedItem = $('.location-dropdown-options div[data-value="'+zoneId+'"]');
        $selectedItem.addClass('selected');
        var selectedText = $selectedItem.text();

        $('#edit_zone_id').val(selectedText).attr('data-value', zoneId);
        // $('#edit_dis_sno').val(data.dis_id);
        $('#edit_dis_branch_name').val(data.dis_branch_name);
        $('#edit_dis_s_no').val(data.dis_s_no);
        $('#edit_dis_wife_name').val(wifename);
        $('#edit_dis_wife_mrd_no').val(wifemrdno);
        $('#edit_dis_husband_mrd_no').val(husmrdno);
        $('#edit_dis_husband_name').val(husname);
        $('#edit_dis_service_name').val(treatmentcat);
        $('#edittd_dis_total_bill').val(totalbil);
        $('#edittd_dis_expected_request').val(expamount);
        $("input[name='disrequeste'][value='" + requestfor + "']").prop("checked", true);
        $('#edittd_dis_post_discount').val(postdis);
        $('#post_discount_value_edit').text(postdis);
        $('#ex_discount_value_edit').text(expamount);
        $('#edit_dis_patient_ph').val(mobileno);
        $('#edittd_dis_counselled_by').val(consultby);
        $('#edittd_dis_final_amt').val(finalamount);
        $('#edittd_dis_brno').val(brno);
        $('#edittd_dis_auth_by').val(authby);
        $('#edittd_dis_final_approve').val(finalapprove);
        // $('#wifeimgPreviewpage').css('background-image', 'url(../public/'+ imageviews + ')');
        // $('#husimgPreviewpage').css('background-image', 'url(../public/' + imageviewshusband + ')');
        // $('#drimgPreviewpage').css('background-image', 'url(../public/' + imageviewsdr + ')');
        // $('#ccimgPreviewpage').css('background-image', 'url(../public/' + imageviewscc + ')');
        // $('#adminimgPreviewpage').css('background-image', 'url(../public/' + imageviewsadmin + ')');
        $('#wifeimgPreviewpage').css('background-image', 'url(../'+ imageviewswife + ')');
        $('#husimgPreviewpage').css('background-image', 'url(../' + imageviewshus + ')');
        $('#drimgPreviewpage').css('background-image', 'url(../' + imageviewsdr + ')');
        $('#ccimgPreviewpage').css('background-image', 'url(../' + imageviewscc + ')');
        $('#adminimgPreviewpage').css('background-image', 'url(../' + imageviewsadmin + ')');

      });


function renderTabledocument(data, pageSizedocuments, pageNum) {
  // Calculate start and end index for pagination
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);

    var body = "";
    var totalItems = data.length;  // Total number of records

    // Iterate through the paginated data and build the table body
    $.each(pageData, function(index, user) {
    // Determine gender and assign values accordingly
    let gender = user.gender || '';

    // Pull all possible values
    let wifemrdno = user.dis_wife_mrd_no || (gender === 'F' ? user.phid : '') || 'N/A';
    let wifename = user.dis_wife_name || (gender === 'F' ? user.name : '') || 'N/A';
    let husmrdno = user.dis_husband_mrd_no || (gender === 'M' ? user.phid : '') || 'N/A';
    let husname = user.dis_husband_name || (gender === 'M' ? user.name : '') || 'N/A';

    // These fallback values are used in visible MRD column
    let wmrd = wifemrdno;
    let wname = wifename;
    let hmrd = husmrdno;
    let hname = husname;

    // Additional fields with fallbacks
    let zone = user.zone_name || user.zone || 'N/A';
    let location = user.location_name || user.branch || 'N/A';
    let auth_by = user.dis_auth_by || "-";
    let approved_by = user.dis_approved_by || '-';
    let brno = user.dis_branch_no || '-';
    let treatmentcat = user.dis_service_name || '-';
    let totalbil = user.dis_total_bill || '-';
    let expected_request = user.dis_expected_request || '-';
    let form_status = user.dis_form_status || '-';
    let patient_ph = user.dis_patient_ph || user.mobile || '-';
    let counselled_by = user.dis_counselled_by || user.consultingdr_name || '-';
    let finalamount = user.dis_final_auth || '-';
    let postdis =user.dis_post_discount || '-';
    let locationid = user.locationid || user.dis_zone_id || '-';

    // Build table row
    body += '<tr onclick="rowClick(event)">' +
        '<td class="tdview" id="idfetch" data-id=""><strong>#' + (index + 1) + '</strong></td>' +
        '<td class="tdview locationname" data-id="' + locationid + '">' + zone + '</td>' +
        '<td class="tdview branchname">' + location + '</td>' +
        '<td class="tdview" data-ph_id="' + wmrd + '"><a href="#">' + wmrd + '<br>' + wname + '</a></td>' +
        '<td class="tdview" data-ph_id="' + hmrd + '"><a href="#">' + hmrd + '<br>' + hname + '</a></td>' +
        '<td class="tdview treatmentcat">' + treatmentcat + '</td>' +
        '<td class="tdview totalbil">' + totalbil +
        '<td class="tdview">' + expected_request + ' (' + form_status + ')</td>' +
        '<td class="tdview expamount" style="display:none;">' + expected_request + '</td>' +
        '<td class="tdview requestfor" style="display:none;">' + form_status + '</td>' +
        '<td class="tdview postdis">'+postdis+'</td>' +
        '<td class="tdview mobileno" style="display:none;">' + patient_ph + '</td>' +
        '<td class="tdview consultby">' + counselled_by + '</td>' +
        '<td class="tdview authby">' + auth_by + '</td>' +
        '<td class="tdview finalapprove">' + approved_by + '</td>' +
        '<td class="tdview brno">' + brno + '</td>' +
        '<td class="tdview finalamount">' + finalamount + '</td>' +
        '<td class="tdview wifemrdno" style="display:none;" data-ph_id="' + wmrd + '"><a href="#">' + wmrd + '</a></td>' +
        '<td class="tdview husmrdno" style="display:none;" data-ph_id="' + hmrd + '"><a href="#">' + hmrd + '</a></td>' +
        '<td class="tdview wifename" style="display:none;"><a href="#">' + wname + '</a></td>' +
        '<td class="tdview husname" style="display:none;"><a href="#">' + hname + '</a></td>' +
         '<td class="tdview wife_sign" style="display:none;">' + user.dis_wife_sign + '</td>' +
          '<td class="tdview husband_sign" style="display:none;">' + user.dis_husband_sign + '</td>' +
           '<td class="tdview drsign" style="display:none;">' + user.dis_drsign + '</td>' +
            '<td class="tdview cc_sign" style="display:none;">' + user.dis_cc_sign + '</td>' +
             '<td class="tdview admin_sign" style="display:none;">' + user.dis_admin_sign + '</td>' +
        '<td class="tdview" style="padding:3px;" id="savebtn">' +
            '<a href="#" style="margin-left: 5px;"><img src="../assets/images/edit.png" style="width: 23px;margin-right:5px;" alt="Icon" class="icon edit_dis_details"></a>' +
        '</td>' +
        '</tr>';
});

    if (pageData.length === 0) {
			body += '<tr><td colspan="15" class="tdview" style="text-align: center;">No data available</td></tr>';
		}
    // Update table body
    $("#document_tbl").html(body);
    // Update the total item count display
    $("#mycounts").text(totalItems);
     // Now that rows are added to the DOM, check for MRD existence
}

function printRowData(rowElement) {
     const row = $(rowElement).closest('tr');
  const wifemrdno = row.find(".wifemrdno").text().trim();
  const husmrdno = row.find(".husmrdno").text().trim();
const branchname =row.find('.branchname').text().trim();
  $.ajax({
    url: discountform_data,
    type: "GET",
    data: {
      dis_wife_mrd_no: wifemrdno,
      dis_husband_mrd_no: husmrdno
    },
    success: function (response) {
        if (!response || !Array.isArray(response) || response.length === 0 || !response[0]) {
    alert("No data found to print.");
    return;
}
      const data = response[0];
      const cleanPath = (path) => path ? path.replace(/[\[\]\"]/g, '') : '';
      const imageviews = cleanPath(data.dis_wife_sign);
      const imageviewshusband = cleanPath(data.dis_husband_sign);
      const imageviewsdr = cleanPath(data.dis_drsign);
      const imageviewscc = cleanPath(data.dis_cc_sign);
      const imageviewsadmin = cleanPath(data.dis_admin_sign);
      const printContent = `
        <html>
        <head>
          <title>Print Preview</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .form-container { border: 1px solid #000; padding: 20px; }
            .header { display: flex; justify-content: space-between; font-weight: bold; font-size: 20px; border-bottom: 1px solid #000; padding-bottom: 10px; }
            .header-left { font-size: 22px; }
            .discount-box { border: 2px solid #000; padding: 5px 10px; display: inline-block; margin-left: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            td { padding: 6px; vertical-align: top; }
            tr { border-bottom: 1px dotted #000; }
            .signatures td { height: 60px; }
            img { max-height: 40px; }
          </style>
        </head>
        <body>
          <div class="form-container">
            <div class="header">
              <div class="header-left">
                Dr. ARAVIND’s IVF<br>
                <small style="font-size: 12px;">FERTILITY & PREGNANCY CENTRE</small>
              </div>
              <div><span class="discount-box">DISCOUNT FORM</span></div>
              <div>S. No: ${data.dis_id || ''}</div>
            </div>

            <table>
              <tr><td>Branch Name:</td><td>${branchname || ''}</td></tr>
              <tr><td>Wife Name:</td><td>${data.dis_wife_name || ''}</td><td>MRD No:</td><td>${data.dis_wife_mrd_no || ''}</td></tr>
              <tr><td>Husband Name:</td><td>${data.dis_husband_name || ''}</td><td>MRD No:</td><td>${data.dis_husband_mrd_no || ''}</td></tr>
              <tr><td>Service Name:</td><td colspan="3">${data.dis_service_name || ''}</td></tr>
              <tr><td>Total Bill Value:</td><td>${data.dis_total_bill || ''}</td></tr>
              <tr><td>Discount Expected Request:</td><td>${data.dis_expected_request || ''} (${data.dis_form_status || ''})</td></tr>
              <tr><td>Post Discount:</td><td>${data.dis_post_discount || ''}</td><td>Patient Ph. No:</td><td>${data.dis_patient_ph || ''}</td></tr>
              <tr><td>Counselled By:</td><td colspan="3">${data.dis_counselled_by || ''}</td></tr>
            </table>

            <table class="signatures">
              <tr>
                <td>Wife Sign:<br> ${imageviews ? `<img src="../public/${imageviews}" alt="Wife Sign">` : '-'}</td>
                <td>Husband Sign:<br> ${imageviewshusband ? `<img src="../public/${imageviewshusband}" alt="Husband Sign">` : '-'}</td>
              </tr>
              <tr>
                <td>Dr. Sign:<br> ${imageviewsdr ? `<img src="../public/${imageviewsdr}" alt="Dr Sign">` : '-'}</td>
                <td>CC Sign:<br> ${imageviewscc ? `<img src="../public/${imageviewscc}" alt="CC Sign">` : '-'}</td>
                <td>Admin Sign:<br> ${imageviewsadmin ? `<img src="../public/${imageviewsadmin}" alt="Admin Sign">` : '-'}</td>
              </tr>
            </table>

            <table>
              <tr><td>Authorised By:</td><td colspan="3">${data.dis_auth_by || ''}</td></tr>
              <tr><td>Final Approved By:</td><td colspan="3">${data.dis_approved_by || ''}</td></tr>
              <tr><td>B.R. No.:</td><td colspan="3">${data.dis_branch_no || ''}</td></tr>
              <tr><td>Final Authorised Amount:</td><td colspan="3">${data.dis_final_auth || ''}</td></tr>
            </table>
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
    const iconCell = event.target.closest('.documentclk');
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
 $('#itemsPerPageSelectdocument').change(function() {
        var pageSizedocuments = parseInt($(this).val());
        renderPaginationdocument(dataSourcedocument, pageSizedocuments);
        renderTabledocument(dataSourcedocument, pageSizedocuments, 1);  // Initially show the first page
    });

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

    function dismorefilterview(uniqueResults,url,moredatefittervale, ph_id = '') {
       $("#document_tbl").hide();
       startLoader();

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
                   $("#my_ticket_details1").hide();
                   console.error("AJAX Error:", status, error);
               }
           });
           }else
          {
            $('.clear_my_views').hide();
            $(".my_search_view").hide();
            discountformdata();
           }

       }


  var fitterremovedata = [];
  function ticketdatefillterrange(moredatefittervale,fitterremovedata) {
    $("#document_tbl").hide();
startLoader();
  var currentFilter = moredatefittervale;
	var morefilltersall=fitterremovedata.join(" AND ");
    $.ajax({
        url: disdocdetialsUrl,
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
            $("#document_tbl").hide();
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
            const result = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });

    const moreFilterValues_market = [
        $("#dis_zone_views").val(),
        $('#dis_loc_views').val(),
        $('#dis_mrd_views').val()
    ];

    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));

    dismorefilterview(fitterremovedata, disdocdetialsUrl, moredatefittervale);

});

$(document).on("click", ".value_views_mysearch", function () {
    var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");

    if(clear_filtr == 'diszone_search'){
        $('#dis_zone_views').val('');
    }
    if(clear_filtr == 'disbranch_search'){
        $('#dis_loc_views').val('');
    }
    if(clear_filtr == 'dismrdno_search'){
      $('#dis_mrd_views').val('');
    }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      dismorefilterview(fitterremovedata,disdocdetialsUrl,moredatefittervale);
});


$(document).on("click", ".clear_my_views", function () {

    fitterremovedata.length = 0;
    $('.documnettypesearch').val("");
    $('.documentdatasearch').val("");
    $(".my_value_views").text("");
    $('.morefittersclr').val("");
    $('.clear_my_views').hide();
    $(".my_search_view").hide();
    $(".value_views_mysearch").text("");
    $(".dropdown-options div").removeClass("selected");
    discountformdata();
});



$(document).on("click", ".my_value_views", function () {
    $('.morefittersclr').val("");
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

    // Extract the key from the removed filter string (before '=')
    let key = removedElement.split('=')[0];
  if (key === 'phid') {
        $('#dis_mrd_views').val('');
    }
    // Remove the filter from the list (cleanup logic)
    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim());
    });

    // Call function to refresh view based on updated filters
    dismorefilterview(fitterremovedata, disdocdetialsUrl, moredatefittervale);
});


$('#dis_mrd_views').on('input', function () {
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
        $("#dis_zone_views").val(),
        $('#dis_loc_views').val(),
        $('#dis_mrd_views').val()
    ];

    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    // Finalize filter string array
    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));

    // Send both filters and individual MRD value (if needed separately)
    dismorefilterview(fitterremovedata, disdocdetialsUrl, moredatefittervale, ph_id);
});

var loaderInterval = null;
var loaderProgress = 0;

function startLoader() {
    const progressBar = $('#progress-bar');
    const progressText = $('#progress-bar');
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
    const progressBar = $('#progress-bar');
    const progressText = $('#progress-bar');
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
}
 function datefillterrange(datefiltersave,fitterremovedata) {
    currentFilter = datefiltersave;
	var morefilltersall=fitterremovedata.join(" AND ");
   startLoader2();
        $("#sveddata_tbl").hide();
    $.ajax({
        url: disformsave_data,
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
    discountsavefilterview(fitterremovedata,disformsave_data,moredatefittervale);
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
      discountsavefilterview(fitterremovedata,disformsave_data,moredatefittervale, phid);
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
    discountsaveformdata();
});

// $(document).on("click", ".clear_my_saveviews", function () {
//     fitterremovedata.length = 0;
//     $('.savedatasearch').val("");
//     $(".my_savevalue_views").text("");
//     $('.clear_my_saveviews').hide();
//     $(".my_search_saveview").hide();
//     $(".value_save_mysearch").text("");
//     $(".dropdown-options div").removeClass("selected");
//     $('#getlocationsave > div')
//                 .hide()
//                 .filter(function () {
//                     return Number($(this).data('type')) === Number(selectedType);
//                 })
//                 .show();
//     discountsaveformdata();
// });
$(document).on("click", ".my_savevalue_views", function () {
    $('.morefittersclr').val("");
    var moredatefittervale = $('#mydateallviewssave').text();
    var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();

    // Clear the text of the clicked element
    $(this).text("");

    // Find the index of the filter to remove
    let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

    let removedElement = "";
    if (indexToRemove !== -1) {
        removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // Get removed item
    }

    // Extract the key from the removed filter string (before '=')
    let key = removedElement.split('=')[0];

    // If the filter is for the date input, reset its value
   if (key === 'phid') {
        $('#save_mrd_views').val('');
    }

    // Remove the filter from the list (cleanup logic)
    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim());
    });
   discountsavefilterview(fitterremovedata,disformsave_data,moredatefittervale, phid);
});

$('#save_mrd_views').on('input', function () {
    const phid = $(this).val().trim();
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
    discountsavefilterview(fitterremovedata,disformsave_data,moredatefittervale, phid);
});
    $('#itemsPerPageSelectsave').change(function() {
      var pageSizedocuments = parseInt($(this).val());
       renderPaginationsaved(dataSaved, pageSizedocuments, 1);
    });
function handlesavedSuccess(responseData) {
    $("#sveddata_tbl").show();
    dataSaved = responseData || [];
    const pageSizedocuments = parseInt($('#itemsPerPageSelectsave').val());
    renderPaginationsaved(dataSaved, pageSizedocuments, 1);
    renderStatisticsCards(responseData.statistics);
}
function renderPaginationsaved(data, pageSizedocuments, currentPage = 1) {
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

    $('#paginationsavedata').html(paginationHtml);

    $('.page-btnviews').click(function () {
        const pageNum = $(this).data('page');
        renderPaginationsaved(data.data, pageSizedocuments, pageNum);
        renderTablesaved(data.data, pageSizedocuments, pageNum);
    });
    renderTablesaved(data.data, pageSizedocuments, currentPage);
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


function discountsaveformdata() {
      startLoader2();
        $("#sveddata_tbl").hide();
		var moredatefittervale = $('#mydateallviewssave').text();
		$(".value_save_mysearch").text("");
		$.ajax({
			url: disformsave_data,
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
     function discountsavefilterview(uniqueResults,url,moredatefittervale, ph_id = '') {
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
function renderTablesaved(data, pageSizedocuments, pageNum) {
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);
    var body = "";
    var totalItems = data.length;

    $.each(pageData, function(index, user) {
        // Additional fields with fallbacks
        let id = user.dis_id;
        let zone = user.zone_name || user.zone || 'N/A';
        let location = user.location_name || user.branch || 'N/A';
        let auth_by = user.dis_auth_by || "-";
        let approved_by = user.dis_approved_by || '-';
        let brno = user.dis_branch_no || '-';
        let treatmentcat = user.dis_service_name || '-';
        let totalbil = user.dis_total_bill || '-';
        let expected_request = user.dis_expected_request || '-';
        let form_status = user.dis_form_status || '-';
        let patient_ph = user.dis_patient_ph || user.mobile || '-';
        let counselled_by = user.dis_counselled_by || user.consultingdr_name || '-';
        let finalamount = user.dis_final_auth || '-';
        let postdis = user.dis_post_discount || '-';
        let locationid = user.locationid || user.dis_zone_id || '-';
        // let discountAmount = user.dis_total_bill - user.dis_post_discount;
        // console.log("discountAmount",discountAmount);
        
        let discountPercent = ((user.dis_post_discount / user.dis_total_bill) * 100).toFixed(2);
        
        let dateObj = new Date(user.created_at);
        let date = dateObj.toLocaleDateString('en-GB'); // dd/mm/yyyy

        let access_limits = admin_user.access_limits;

        // Helper → status badge / icon
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

        // Action buttons based on user's role and current approval status
        let actionHtml = '-';
        
        // Access limit 1 - Final Approver (Super Admin)
        if (access_limits == 1) {
            // Show action buttons only if not already approved/rejected by this user
            if (user.final_approver == 0) {
                actionHtml = `
                    <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>
                `;
            }
        }
        // Access limit 2 - Zonal Approver
        else if (access_limits == 2) {
            if (user.zonal_approver == 0) {
                actionHtml = `
                    <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>
                `;
            }
        }
        // Access limit 3 - Admin Approver
        else if (access_limits == 3) {
            if (user.admin_approver == 0) {
                actionHtml = `
                    <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>
                `;
            }
        }
        // Access limit 4 - Audit Approver
        else if (access_limits == 4) {
            if (user.audit_approver == 0) {
                actionHtml = `
                    <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>
                `;
            }
        }

        // Build table row
        body += '<tr onclick="rowClick(event)">' +

            '<td class="tdview" id="idfetch" data-id="' + id + '"><strong>#' + (index + 1) + '</strong></td>' +

            '<td class="tdview">' + date + '</td>' +

            '<td class="tdview locationname" data-id="' + locationid + '">' + zone + '</td>' +

            '<td class="tdview branchname">' + location + '</td>' +

            '<td class="tdview" data-ph_id="' + user.dis_wife_mrd_no + '">' +
                '<a href="#">' + user.dis_wife_mrd_no + '<br>' + user.dis_wife_name + '</a>' +
            '</td>' +

            '<td class="tdview" data-ph_id="' + user.dis_husband_mrd_no + '">' +
                '<a href="#">' + user.dis_husband_mrd_no + '<br>' + user.dis_husband_name + '</a>' +
            '</td>' +

            '<td class="tdview treatmentcat">' + treatmentcat + '</td>' +

            '<td class="tdview totalbil">' + totalbil + '</td>' +

            `<td class="tdview">${expected_request} (${form_status}) ${discountPercent}%</td>`+

            '<td class="tdview expamount" style="display:none;">' + expected_request + '</td>' +
            '<td class="tdview requestfor" style="display:none;">' + form_status + '</td>' +

            '<td class="tdview postdis">' + postdis + '</td>' +

            '<td class="tdview mobileno" style="display:none;">' + patient_ph + '</td>' +

            '<td class="tdview consultby">' + counselled_by + '</td>' +
            '<td class="tdview authby">' + auth_by + '</td>' +
            '<td class="tdview finalapprove">' + approved_by + '</td>' +
            '<td class="tdview brno">' + brno + '</td>' +
            '<td class="tdview finalamount">' + finalamount + '</td>' +
            '<td class="tdview">' + user.username + '</br>' + user.userid + '</td>' +
            
            // Hidden columns
            '<td class="tdview wife_sign" style="display:none;">' + user.dis_wife_sign + '</td>' +
            '<td class="tdview husband_sign" style="display:none;">' + user.dis_husband_sign + '</td>' +
            '<td class="tdview drsign" style="display:none;">' + user.dis_drsign + '</td>' +
            '<td class="tdview cc_sign" style="display:none;">' + user.dis_cc_sign + '</td>' +
            '<td class="tdview admin_sign" style="display:none;">' + user.dis_admin_sign + '</td>' +
            '<td class="tdview wifemrdno" style="display:none;" data-ph_id="' + user.dis_wife_mrd_no + '"><a href="#">' + user.dis_wife_mrd_no + '</a></td>' +
            '<td class="tdview husmrdno" style="display:none;" data-ph_id="' + user.dis_husband_mrd_no + '"><a href="#">' + user.dis_husband_mrd_no + '</a></td>' +
            '<td class="tdview wifename" style="display:none;"><a href="#">' + user.dis_wife_name + '</a></td>' +
            '<td class="tdview husname" style="display:none;"><a href="#">' + user.dis_husband_name + '</a></td>';

        // 🔹 APPROVAL STATUS COLUMNS - Based on access level
        // The order of columns changes based on who is viewing
        
        // ACCESS LIMIT = 1 (Final Approver / Super Admin)
        // Shows: Admin → Zonal → Audit → Final → Actions
        if (access_limits == 1) {
            body += 
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver) + '</td>' +
                '<td class="tdview approver-action">' + actionHtml + '</td>';
        }

        // ACCESS LIMIT = 2 (Zonal Approver)
        // Shows: Admin → Audit → Final → Zonal → Actions
        else if (access_limits == 2) {
            body += 
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver) + '</td>' +
                '<td class="tdview approver-action">' + actionHtml + '</td>';
        }

        // ACCESS LIMIT = 3 (Admin Approver)
        // Shows: Zonal → Audit → Final → Admin → Actions
        else if (access_limits == 3) {
            body += 
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver) + '</td>' +
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver) + '</td>' +
                '<td class="tdview approver-action">' + actionHtml + '</td>';
        }

        // ACCESS LIMIT = 4 (Audit Approver)
        // Shows: Admin → Zonal → Final → Audit → Actions
        else if (access_limits == 4) {
            body += 
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver) + '</td>' +
                '<td class="tdview approver-action">' + actionHtml + '</td>';
        }

        // Default (no specific access)
        else {
            body += 
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver) + '</td>' +
                '<td class="tdview approver-action">-</td>';
        }

        // ✅ Edit button (permission-based)
        if (admin_user.access_limits == 1 || admin_user.access_limits == 2 || admin_user.access_limits == 3 || admin_user.access_limits == 4) {
            body +=
                '<td class="tdview" style="padding:3px;">' +
                    '<a href="#" style="margin-left:5px;">' +
                        '<img src="../assets/images/edit.png" style="width:23px;margin-right:5px;" alt="Edit" class="icon edit_dis_details">' +
                    '</a>' +
                '</td>';
        }

        // ✅ Print button (always visible)
        body +=
            '<td class="tdview" style="padding:3px;">' +
                '<img src="../assets/images/print.png" style="width:23px;" alt="Print" class="icon documentclk">' +
            '</td>' +

        '</tr>';

    });

    if (pageData.length === 0) {
        body += '<tr><td colspan="25" class="tdview" style="text-align: center;">No data available</td></tr>';
    }

    $("#sveddata_tbl").html(body);
    $("#billsavecounts").text(totalItems);
}
// function renderTablesaved(data, pageSizedocuments, pageNum) {

//     var startIdx = (pageNum - 1) * pageSizedocuments;
//     var endIdx = pageNum * pageSizedocuments;
//     var pageData = data.slice(startIdx, endIdx);
//     var body = "";
//     var totalItems = data.length;

//     const access_limits = admin_user.access_limits;

//     // helper → status badge / icon
//     function renderStatus(status) {
//         status = parseInt(status);

//         if (status === 1) {
//             return `<span style="color:green;font-weight:bold;">
//                         ✔ Approved
//                     </span>`;
//         } else if (status === 2) {
//             return `<span style="color:red;font-weight:bold;">
//                         ✖ Rejected
//                     </span>`;
//         } else {
//             return `<span style="color:#f0ad4e;font-weight:bold;">
//                         ⏳ Pending
//                     </span>`;
//         }
//     }

//     $.each(pageData, function (index, user) {

//         let id = user.dis_id;
//         let zone = user.zone_name || 'N/A';
//         let location = user.location_name || 'N/A';
//         let treatmentcat = user.dis_service_name || '-';
//         let totalbil = user.dis_total_bill || '-';
//         let expected_request = user.dis_expected_request || '-';
//         let postdis = user.dis_post_discount || '-';
//         let counselled_by = user.dis_counselled_by || '-';
//         let auth_by = user.dis_auth_by || '-';
//         let approved_by = user.dis_approved_by || '-';
//         let brno = user.dis_branch_no || '-';
//         let finalamount = user.dis_final_auth || '-';


//         // action buttons
//         let actionHtml = '-';
//         if (access_limits == 1 || access_limits == 2) {
//             actionHtml = `
//                 <button class="btn btn-success btn-sm approve-btn"
//                         data-id="${id}" data-status="1">Approve</button>
//                 <button class="btn btn-danger btn-sm reject-btn"
//                         data-id="${id}" data-status="2">Reject</button>
//             `;
//         }

//         body += `
//         <tr onclick="rowClick(event)">
//             <td class="tdview"><strong>#${startIdx + index + 1}</strong></td>

//             <td class="tdview">${zone}</td>
//             <td class="tdview">${location}</td>

//             <td class="tdview">
//                 ${user.dis_wife_mrd_no}<br>${user.dis_wife_name}
//             </td>

//             <td class="tdview">
//                 ${user.dis_husband_mrd_no}<br>${user.dis_husband_name}
//             </td>

//             <td class="tdview">${treatmentcat}</td>
//             <td class="tdview">${totalbil}</td>
//             <td class="tdview">${expected_request}</td>
//             <td class="tdview">${postdis}</td>
//             <td class="tdview">${counselled_by}</td>
//             <td class="tdview">${auth_by}</td>
//             <td class="tdview">${approved_by}</td>
//             <td class="tdview">${brno}</td>
//             <td class="tdview">${finalamount}</td>
//         `;

//         // 🔹 ACCESS LIMIT = 1 (Zonal)
//         if (access_limits == 1) {
//             body += `
//                 <td class="tdview">${renderStatus(user.zonal_approve_status)}</td>
//                 <td class="tdview">${statusHtml}</td>
//                 <td class="tdview">${actionHtml}</td>
//             `;
//         }

//         // 🔹 ACCESS LIMIT = 2
//         else if (access_limits == 2) {
//             body += `
//                 <td class="tdview">${statusHtml}</td>
//                 <td class="tdview">${actionHtml}</td>
//             `;
//         }

//         // 🔹 NORMAL USER
//         else {
//             body += `
//                 <td class="tdview">${renderStatus(user.zonal_approve_status)}</td>
//                 <td class="tdview">${statusHtml}</td>
//             `;
//         }

//         // 🔹 PRINT (always visible)
//         body += `
//             <td class="tdview">
//                 <img src="../assets/images/print.png"
//                      style="width:22px;cursor:pointer"
//                      class="icon documentclk"
//                      data-id="${id}">
//             </td>
//         </tr>`;
//     });

//     if (pageData.length === 0) {
//         body += `
//             <tr>
//                 <td colspan="20" class="tdview" style="text-align:center;">
//                     No data available
//                 </td>
//             </tr>`;
//     }

//     $("#sveddata_tbl").html(body);
//     $("#billsavecounts").text(totalItems);
// }
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
