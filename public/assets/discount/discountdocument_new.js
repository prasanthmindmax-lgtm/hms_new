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

    /** Disable save buttons + show spinner; prevents double submit on discount add/edit */
    function hmsFormSaveBtnLoading($btn, loading) {
        if (!$btn || !$btn.length) return;
        if (loading) {
            if (!$btn.data('hms-orig-html')) $btn.data('hms-orig-html', $btn.html());
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
            );
        } else {
            $btn.prop('disabled', false).html($btn.data('hms-orig-html') || $btn.html());
            $btn.removeData('hms-orig-html');
        }
    }

    // Clear error span text on input
    $('input, select, textarea').on('input change', function () {
        $(this).siblings('.errorss').text('');
    });
    $('#close-button').click(function () {
        // Clear all input fields
        $('input, select, textarea').val('');
        // Clear error messages
        $('.errorss').text('');
        $('#exampleModaluser').hide();
    });
    // Custom modal: close on overlay or close button (discount dashboard)
    $(document).on('click', '.custom-form-modal-close, .custom-form-modal-overlay', function (e) {
        if ($(e.target).closest('.custom-form-modal-content').length) return;
        $('#exampleModaluser').hide();
    });
    $(document).on('click', '.custom-form-modal-close', function () {
        $('#exampleModaluser').hide();
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
        // Clear previous errors (same as reference discountform_document)
        $('.error_location, .error_wifename, .error_wifemrd, .error_husname, .error_husmrd, .error_treatment, .error_total, .error_exp, .error_post, .error_patient, .error_counsel, .error_brno, .error_authouris, .error_approve').text('');
        $('.sign-error').remove();

        let isValid = true;

        if ($('#zone_id').val() === "" || !$('#zone_id').attr('data-value')) {
            $('.error_location').text('Please select the location Name');
            isValid = false;
        }
        if ($('#wife_name').val() === "") {
            $('.error_wifename').text('Enter the Wife Name');
            isValid = false;
        }
        if ($('#wifemrdno').val() === ""){
          $(".error_wifemrd").text('Enter the Wife MRD No');
          isValid = false;
        }
        if ($('#husband_name').val() === ""){
          $(".error_husname").text('Enter the Husband Name');
          isValid = false;
        }
        if ($('#husbandmrdno').val() === ""){
          $(".error_husmrd").text('Enter the Husband MRD No');
          isValid = false;
        }
        if ($('#service_name').val() === ""){
          $(".error_treatment").text('Enter the Service Name');
          isValid = false;
        }
        if ($('#totalbill').val() === ""){
          $(".error_total").text('Enter the Total Bill Value');
          isValid = false;
        }
        if ($('#ex_discount_display').val() === ""){
          $(".error_exp").text('Enter the Expected Discount');
          isValid = false;
        }
        if ($('#post_discount_display').val() === ""){
          $(".error_post").text('Enter the Post Discount');
          isValid = false;
        }
        if ($('#patientph').val() === ""){
          $(".error_patient").text('Enter the Patient Ph. No');
          isValid = false;
        }
        var counselMain = ($('#counselled_by').val() || '').trim();
        var counselInclude = ($('#counselled_include_chk').prop('checked') && $('#counselled_by_include').val().trim() !== '') ? $('#counselled_by_include').val().trim() : '';
        var counselNotInclude = ($('#counselled_not_include_chk').prop('checked') && $('#counselled_by_not_include').val().trim() !== '') ? $('#counselled_by_not_include').val().trim() : '';
        if (counselMain === '' && counselInclude === '' && counselNotInclude === '') {
          $(".error_counsel").text('Please fill Counselled By or at least one of Include / Not Include');
          isValid = false;
        }
        if (typeof admin_user !== 'undefined' && admin_user.access_limits == 2){
            if ($('#branch_no').val() === ""){
              $(".error_brno").text('Enter the B.R. No.');
              isValid = false;
            }
            if ($('#authourised_by').val() === ""){
              $(".error_authouris").text('Enter the Authorised By');
              isValid = false;
            }
        }
        if (typeof admin_user !== 'undefined' && admin_user.access_limits == 1){
            if ($('#approveded_by').val() === ""){
              $(".error_approve").text('Enter the Final Approved By');
              isValid = false;
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
        'dis_counselled_by': counselMain,
        'dis_counselled_by_include': counselInclude,
        'dis_counselled_by_not_include': counselNotInclude,
        'dis_final_auth': $('#final_amount').val(),
        'dis_branch_no': $('#branch_no').val(),
        'dis_auth_by': $('#authourised_by').val(),
        'dis_approved_by' : $('#approveded_by').val(),
        '_token': $('meta[name="csrf-token"]').attr('content')
    };

    for (const key in fields) formData.append(key, fields[key]);
    var attachmentFiles = document.getElementById('discount_attachments');
    if (attachmentFiles && attachmentFiles.files && attachmentFiles.files.length) {
        for (var i = 0; i < attachmentFiles.files.length; i++) {
            formData.append('discount_attachments[]', attachmentFiles.files[i]);
        }
    }
    const signers = [
        { field: 'dis_wife_sign', radioName: 'wife-signature', canvasId: 'wifeCanvas', fileInputId: 'imageUpload', uploadDiv: '#wife-upload' },
        { field: 'dis_husband_sign', radioName: 'husband-signature', canvasId: 'husbandCanvas', fileInputId: 'husbandsignimg', uploadDiv: '#husband-upload' },
        { field: 'dis_drsign', radioName: 'dr-signature', canvasId: 'drCanvas', fileInputId: 'drsignimg', uploadDiv: '#dr-upload' },
        { field: 'dis_cc_sign', radioName: 'cc-signature', canvasId: 'ccCanvas', fileInputId: 'ccsignimg', uploadDiv: '#cc-upload' },
        { field: 'dis_admin_sign', radioName: 'admin-signature', canvasId: 'adminCanvas', fileInputId: 'adminsignimg', uploadDiv: '#admin-upload' },
    ];

    // Validate signatures before building formData / starting async toBlob
    let allSignaturesValid = true;
    signers.forEach(signer => {
        const isUpload = $(`input[name="${signer.radioName}"]:checked`).val() === 'upload';
        const isDraw = $(`input[name="${signer.radioName}"]:checked`).val() === 'draw';

        if (!isUpload && !isDraw) {
            allSignaturesValid = false;
            $(`${signer.uploadDiv} .sign-error`).remove();
            $(`${signer.uploadDiv}`).append('<span class="text-danger sign-error">Please select and provide a signature.</span>');
        } else {
            if (isUpload) {
                const file = document.getElementById(signer.fileInputId)?.files[0];
                if (!file) {
                    allSignaturesValid = false;
                    $(`${signer.uploadDiv} .sign-error`).remove();
                    $(`${signer.uploadDiv}`).append('<span class="text-danger sign-error">Please upload a signature image.</span>');
                }
            }
            if (isDraw) {
                const canvas = document.getElementById(signer.canvasId);
                if (canvas && isCanvasBlank(canvas)) {
                    allSignaturesValid = false;
                    $(`${signer.uploadDiv} .sign-error`).remove();
                    $(`${signer.uploadDiv}`).append('<span class="text-danger sign-error">Please sign on the canvas.</span>');
                }
            }
        }
    });
    if (!allSignaturesValid) return;

    var $discountAddBtn = $('#submit_discountform');
    if ($discountAddBtn.prop('disabled')) return;
    hmsFormSaveBtnLoading($discountAddBtn, true);

    let pendingBlobs = 0;
    let discountAddSubmitSent = false;
    const finishSubmission = () => {
        if (pendingBlobs !== 0 || discountAddSubmitSent) return;
        discountAddSubmitSent = true;
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
                    // Fix body overflow after modal closes
                    setTimeout(function() {
                        $('body').removeClass('modal-open').css({
                            'overflow': '',
                            'padding-right': ''
                        });
                        $('.modal-backdrop').remove();
                    }, 300);
                    discountformdata();
                    clearForm();
                },
                error: function (error) {
                    console.error(error.responseJSON);
                },
                complete: function () {
                    hmsFormSaveBtnLoading($discountAddBtn, false);
                    discountAddSubmitSent = false;
                },
            });
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
                $('#counselled_by_include').val("");
                $('#counselled_by_not_include').val("");
                $('#counselled_include_chk, #counselled_not_include_chk').prop('checked', false);
                $('#final_amount').val("");
                $('#discount_attachments').val('');
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
         $('#exampleModal2').modal('show');
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
        .attr('data-value', data.dis_zone_id)
        .attr('data-selected-value', data.dis_zone_id);
            $('#edit_disid').val(data.dis_id || '');
            $('#edit_dis_sno').val(data.dis_id);
            $('#edit_dis_wife_name').val(data.dis_wife_name || '');
            $('#edit_dis_wife_mrd_no').val(data.dis_wife_mrd_no || '');
            $('#edit_dis_husband_name').val(data.dis_husband_name || '');
            $('#edit_dis_husband_mrd_no').val(data.dis_husband_mrd_no || '');
            $('#edit_dis_service_name').val(data.dis_service_name || '');
            $('#edit_dis_patient_ph').val(data.dis_patient_ph || '');
            $('#hidden_wifemrd').val(data.dis_wife_mrd_no);
            $('#hidden_husmrd').val(data.dis_husband_mrd_no);
            $('#edittd_dis_total_bill').val(data.dis_total_bill);
            $('#edittd_dis_expected_request').val(data.dis_expected_request);
            $('#ex_discount_value_edit').text(data.dis_expected_request);
            $("input[name='disrequeste'][value='" + data.dis_form_status + "']").prop("checked", true);
            $('#edittd_dis_post_discount').val(data.dis_post_discount);
            $('#post_discount_value_edit').text(data.dis_post_discount);
            $('#edittd_dis_counselled_by').val(data.dis_counselled_by);
            $('#edittd_dis_final_amt').val(data.dis_final_auth);
            $('#edittd_dis_brno').val(data.dis_branch_no);
            $('#edittd_dis_auth_by').val(data.dis_auth_by);
            $('#edittd_dis_final_approve').val(data.dis_approved_by);
            $('#imagewifesign').css('background-image', 'url(../public'+ imageviews + ')');
            $('#husimgPreviewe').css('background-image', 'url(../public' + imageviewshusband + ')');
            $('#drimgPreviewe').css('background-image', 'url(../public' + imageviewsdr + ')');
            $('#ccimgPreviewe').css('background-image', 'url(../public' + imageviewscc + ')');
            $('#adminimgPreviewe').css('background-image', 'url(../public' + imageviewsadmin + ')');
            // $('#imagewifesign').css('background-image', 'url(../'+ imageviews + ')');
            // $('#husimgPreviewe').css('background-image', 'url(../' + imageviewshusband + ')');
            // $('#drimgPreviewe').css('background-image', 'url(../' + imageviewsdr + ')');
            // $('#ccimgPreviewe').css('background-image', 'url(../' + imageviewscc + ')');
            // $('#adminimgPreviewe').css('background-image', 'url(../' + imageviewsadmin + ')');
          }
        });

      });
    $(document).on("click", "#editdiscountform", function (event) {
  event.preventDefault();
  event.stopPropagation();
  console.log('editdiscountform: button clicked (check Console + Network tab)');
  if (typeof discountformeditUrl === 'undefined' || !discountformeditUrl) {
    console.error('discountformeditUrl is not defined. Ensure the page defines it (e.g. discount_dashboard blade).');
    if (typeof window.dispatchEvent === 'function') {
      window.dispatchEvent(new CustomEvent('swal:toast', { detail: { title: 'Error!', text: 'Save URL not configured.', icon: 'error', background: '#f8d7da' } }));
    }
    return false;
  }
  var $editDiscountBtn = $('#editdiscountform');
  if ($editDiscountBtn.prop('disabled')) return false;
  hmsFormSaveBtnLoading($editDiscountBtn, true);
  try {
  var exText = ($('#ex_discount_value_edit').text() || '').toString().replace(/₹/g, '').trim();
  var postText = ($('#post_discount_value_edit').text() || '').toString().replace(/₹/g, '').trim();
  const formData = new FormData();
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
  var editDisId = ($('#edit_disid').val() || '').toString().trim();
  if (editDisId) formData.append('dis_id', editDisId);
  var editZoneId = $('#edit_zone_id').attr('data-value') || $('#edit_zone_id').attr('data-selected-value');
  formData.append('dis_zone_id', editZoneId || '');
  formData.append('dis_wife_mrd_no', $('#edit_dis_wife_mrd_no').val() || '');
  formData.append('dis_husband_mrd_no', $('#edit_dis_husband_mrd_no').val() || '');
  formData.append('dis_wife_name', $('#edit_dis_wife_name').val() || '');
  formData.append('dis_husband_name', $('#edit_dis_husband_name').val() || '');
  formData.append('dis_service_name', $('#edit_dis_service_name').val() || '');
  formData.append('dis_total_bill', $('#edittd_dis_total_bill').val() || '');
  formData.append('dis_expected_request', parseFloat(exText) || 0);
  formData.append('dis_form_status', $('input[name="disrequeste"]:checked').val() || '');
  formData.append('dis_post_discount', parseFloat(postText) || 0);
  formData.append('dis_patient_ph', $('#edit_dis_patient_ph').val() || '');
  formData.append('dis_counselled_by', $('#edittd_dis_counselled_by').val() || '');
  formData.append('dis_counselled_by_include', $('#edittd_dis_counselled_by_include').val() || '');
  formData.append('dis_counselled_by_not_include', $('#edittd_dis_counselled_by_not_include').val() || '');
  formData.append('dis_final_auth', $('#edittd_dis_final_amt').val() || '');
  formData.append('dis_branch_no', $('#edittd_dis_brno').val() || '');
  formData.append('dis_auth_by', $('#edittd_dis_auth_by').val() || '');
  formData.append('dis_approved_by', $('#edittd_dis_final_approve').val() || '');
  var editAttachments = document.getElementById('edit_discount_attachments');
  if (editAttachments && editAttachments.files && editAttachments.files.length) {
    for (var ei = 0; ei < editAttachments.files.length; ei++) {
      formData.append('discount_attachments[]', editAttachments.files[ei]);
    }
  }
  const signers = [
    { field: 'dis_wife_sign', radio: 'editwife-signature', canvasId: 'editwifeCanvas', fileInput: 'imagewsign' },
    { field: 'dis_husband_sign', radio: 'edithusband-signature', canvasId: 'edithusbandCanvas', fileInput: 'husbandsignimge' },
    { field: 'dis_drsign', radio: 'editdr-signature', canvasId: 'editdrCanvas', fileInput: 'drsignimge' },
    { field: 'dis_cc_sign', radio: 'editcc-signature', canvasId: 'editccCanvas', fileInput: 'ccsignimge' },
    { field: 'dis_admin_sign', radio: 'editadmin-signature', canvasId: 'editadminCanvas', fileInput: 'adminsignimge' },
  ];

  let pendingBlobs = 0;
  let editSubmitSent = false;
  const finishEditSubmission = () => {
    if (pendingBlobs === 0 && !editSubmitSent) {
      editSubmitSent = true;
      console.log('editdiscountform: sending POST to', discountformeditUrl, '(check Network tab for this request)');
      $.ajax({
        url: discountformeditUrl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          var rowPatched = false;
          if (response.success) {
            window.dispatchEvent(new CustomEvent('swal:toast', {
              detail: {
                title: 'Info!',
                text: response.message,
                icon: 'success',
                background: 'success',
              }
            }));
            var rec = response.updatedRecord;
            if (rec && rec.dis_id) {
              var $tr = $('#sveddata_tbl tr').filter(function () {
                return String($(this).find('td[id="idfetch"]').data('id')) === String(rec.dis_id);
              });
              if ($tr.length) {
                function parseCounselledPatch(val) {
                  if (!val) return '-';
                  if (typeof val === 'string' && val.indexOf('[') === 0) {
                    try { var a = JSON.parse(val); return (a && a[0]) ? a[0] : val; } catch (e) { return val; }
                  }
                  return val;
                }
                var includeText = parseCounselledPatch(rec.dis_counselled_by_include);
                var notIncludeText = parseCounselledPatch(rec.dis_counselled_by_not_include);
                var exp = rec.dis_expected_request || '-';
                var fst = rec.dis_form_status || '-';
                $tr.find('.locationname').text(rec.zone_name || 'N/A').attr('data-id', rec.dis_zone_id != null ? rec.dis_zone_id : '');
                $tr.find('.branchname').text(rec.location_name || 'N/A');
                var $visPh = $tr.find('> td[data-ph_id]').not('.wifemrdno').not('.husmrdno');
                if ($visPh.length >= 2) {
                  $($visPh[0]).html('<a href="#">' + (rec.dis_wife_mrd_no || '') + '<br>' + (rec.dis_wife_name || '') + '</a>').attr('data-ph_id', rec.dis_wife_mrd_no || '');
                  $($visPh[1]).html('<a href="#">' + (rec.dis_husband_mrd_no || '') + '<br>' + (rec.dis_husband_name || '') + '</a>').attr('data-ph_id', rec.dis_husband_mrd_no || '');
                }
                $tr.find('.treatmentcat').text(rec.dis_service_name || '-');
                $tr.find('.totalbil').text(rec.dis_total_bill || '-');
                $tr.find('.totalbil').next('td').text(exp + ' (' + fst + ')');
                $tr.find('.expamount').text(exp);
                $tr.find('.requestfor').text(fst);
                $tr.find('.postdis').text(rec.dis_post_discount || '-');
                $tr.find('.include-col').text(includeText);
                $tr.find('.notinclude-col').text(notIncludeText);
                $tr.find('.authby').text(rec.dis_auth_by || '-');
                $tr.find('.finalapprove').text(rec.dis_approved_by || '-');
                $tr.find('.brno').text(rec.dis_branch_no || '-');
                $tr.find('.finalamount').text(rec.dis_final_auth || '-');
                $tr.find('.wifemrdno').html('<a href="#">' + (rec.dis_wife_mrd_no || '') + '</a>').attr('data-ph_id', rec.dis_wife_mrd_no || '');
                $tr.find('.husmrdno').html('<a href="#">' + (rec.dis_husband_mrd_no || '') + '</a>').attr('data-ph_id', rec.dis_husband_mrd_no || '');
                $tr.find('.wifename').html('<a href="#">' + (rec.dis_wife_name || '') + '</a>');
                $tr.find('.husname').html('<a href="#">' + (rec.dis_husband_name || '') + '</a>');
                var uploader = (rec.username || '') + '</br>' + (rec.userid || '');
                $tr.find('.reject-reason-cell').prev('td').html(uploader);
                rowPatched = true;
                if (typeof dataSaved !== 'undefined' && Array.isArray(dataSaved)) {
                  var di = dataSaved.findIndex(function (d) { return String(d.dis_id) === String(rec.dis_id); });
                  if (di >= 0) $.extend(true, dataSaved[di], rec);
                }
              }
            }
            if (!rowPatched) {
              discountsaveformdata();
            }
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
          if (!rowPatched) {
            discountformdata();
          }
          $("#exampleModal2").modal('hide');
          setTimeout(function() {
              $('body').removeClass('modal-open').css({
                  'overflow': '',
                  'padding-right': ''
              });
              $('.modal-backdrop').remove();
          }, 300);
           $('#ex_discount_value_edit').text("");
           $('#post_discount_value_edit').text("");
        },
        error: function (xhr, status, err) {
          console.error('editdiscountform AJAX error:', status, err, xhr.responseJSON || xhr.responseText);
          if (typeof window.dispatchEvent === 'function') {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || xhr.statusText || 'Request failed';
            window.dispatchEvent(new CustomEvent('swal:toast', { detail: { title: 'Error!', text: msg, icon: 'error', background: '#f8d7da' } }));
          }
        },
        complete: function () {
          hmsFormSaveBtnLoading($editDiscountBtn, false);
          editSubmitSent = false;
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
        }, 'image/png', 0.9);
      }
    }
  });

  if (pendingBlobs === 0) {
    finishEditSubmission();
  } else {
    console.log('editdiscountform: waiting for', pendingBlobs, 'signature blob(s), then sending POST');
    var fallbackTimer = setTimeout(function () {
      if (!editSubmitSent) {
        console.warn('editdiscountform: fallback send (toBlob may not have fired)');
        pendingBlobs = 0;
        finishEditSubmission();
      }
    }, 2500);
  }
  } catch (err) {
    console.error('editdiscountform click handler error:', err);
    hmsFormSaveBtnLoading($editDiscountBtn, false);
    if (typeof window.dispatchEvent === 'function') {
      window.dispatchEvent(new CustomEvent('swal:toast', { detail: { title: 'Error!', text: 'Could not submit: ' + (err.message || 'unknown error'), icon: 'error', background: '#f8d7da' } }));
    }
  }
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
var dataSourcedocument = [];

/** Pending: normalize Today/labels to d/m/Y range. Saved: pass through "All" when allowAll. */
function getDateRangeForApi(selector, opts) {
    opts = opts || {};
    var allowAll = !!opts.allowAll;
    var val = ($(selector).text() || '').trim();
    if (allowAll && (!val || val.toLowerCase() === 'all')) {
        return 'All';
    }
    if (!val || val === 'Today' || val === 'Yesterday' || val.indexOf('-') === -1) {
        if (typeof moment !== 'undefined') {
            var m = moment();
            return m.format('DD/MM/YYYY') + ' - ' + m.format('DD/MM/YYYY');
        }
        return val;
    }
    return val;
}

function discountformdata() {
   startLoader();
    $("#pending_table_Body").closest('.table-container').hide();
    var moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
    $(".value_views_mysearch").text("");
    $("#loader-container").show();
    $.ajax({
        url: disdocdetialsUrl,
        type: "GET",
        data: {
            moredatefittervale: moredatefittervale,
        },
        success: function (responseData) {
        stopLoader(true);
            discountFormHandleSuccess(responseData);
        },
        error: function (xhr, status, error) {
           stopLoader(false, error);
            console.error("AJAX Error:", status, error);
        }
    });
}

function discountFormHandleSuccess(responseData) {
            $("#pending_table_Body").closest('.table-container').show();
            var raw = responseData.checkinData != null ? responseData.checkinData : responseData;
             dataSourcedocument = Array.isArray(raw) ? raw : [];
            totalItems = dataSourcedocument.length;
            var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val()); // Get selected items per page
            renderPaginationdocument(dataSourcedocument, pageSizedocuments);
            renderTabledocument(dataSourcedocument, pageSizedocuments, 1); // Show first page initially

}

  $(document).on("click", '.edit_dis_details', function () {
        var id = $(this).closest('tr').find('[data-id]').first().data('id');
        $('#edit_disid').val(id || '');
        $('#edit_discount_attachments').val('');
        $('#edit_attachment_existing_list').empty();

        function normPath(s) {
            if (!s || !s.trim()) return '';
            return s.replace(/[\[\]\"]/g, '').replace(/\\/g, '/').trim();
        }
        function setPreview($el, path) {
            if (!path) {
                $el.css('background-image', 'none');
                return;
            }
            var baseUrl = window.location.origin + (window.location.pathname.indexOf('/public') !== -1 ? '' : '/hms/public');
            var cleanPath = path.replace('../public', '');
            var url = baseUrl + '/' + cleanPath;
            console.log("url", url);
            console.log("url", url);
            console.log("url", url);
            
            $el.css('background-image', 'url("' + url + '")');
        }
        function fillEditFormFromRow(row) {
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
            var wife_sign = normPath(row.find('.wife_sign').text().trim());
            var husband_sign = normPath(row.find('.husband_sign').text().trim());
            var drsign = normPath(row.find('.drsign').text().trim());
            var cc_sign = normPath(row.find('.cc_sign').text().trim());
            var admin_sign = normPath(row.find('.admin_sign').text().trim());
            setPreview($('#imagewifesign'), wife_sign);
            setPreview($('#husimgPreviewe'), husband_sign);
            setPreview($('#drimgPreviewe'), drsign);
            setPreview($('#ccimgPreviewe'), cc_sign);
            setPreview($('#adminimgPreviewe'), admin_sign);
            $('.location-dropdown-options div').removeClass('selected');
            var $selectedItem = $('.location-dropdown-options div[data-value="'+zoneId+'"]');
            $selectedItem.addClass('selected');
            $('#edit_zone_id').val($selectedItem.text()).attr('data-value', zoneId);
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
            $('#edittd_dis_counselled_by').val(consultby || '');
            $('#edittd_dis_counselled_by_include').val('');
            $('#edittd_dis_counselled_by_not_include').val('');
            $('#edit_counselled_include_chk, #edit_counselled_not_include_chk').prop('checked', false);
            $('#edittd_dis_final_amt').val(finalamount);
            $('#edittd_dis_brno').val(brno);
            $('#edittd_dis_auth_by').val(authby);
            $('#edittd_dis_final_approve').val(finalapprove);
        }

        if (id && typeof discountform_edit_fetch_url !== 'undefined') {
            $('#exampleModal2').modal('show');
            $.get(discountform_edit_fetch_url, { dis_id: id }, function (data) {
                if (!data) return;
                var d = data;
                var zoneId = d.dis_zone_id;
                $('.location-dropdown-options div').removeClass('selected');
                var $selectedItem = $('.location-dropdown-options div[data-value="'+zoneId+'"]');
                $selectedItem.addClass('selected');
                $('#edit_zone_id').val($selectedItem.text()).attr('data-value', zoneId);
                $('#edit_dis_wife_name').val(d.dis_wife_name);
                $('#edit_dis_wife_mrd_no').val(d.dis_wife_mrd_no);
                $('#edit_dis_husband_mrd_no').val(d.dis_husband_mrd_no);
                $('#edit_dis_husband_name').val(d.dis_husband_name);
                $('#edit_dis_service_name').val(d.dis_service_name);
                $('#edittd_dis_total_bill').val(d.dis_total_bill);
                $('#edittd_dis_expected_request').val(d.dis_expected_request);
                $('#ex_discount_value_edit').text(d.dis_expected_request || '');
                $("input[name='disrequeste'][value='" + (d.dis_form_status || '') + "']").prop("checked", true);
                $('#edittd_dis_post_discount').val(d.dis_post_discount);
                $('#post_discount_value_edit').text(d.dis_post_discount || '');
                $('#edit_dis_patient_ph').val(d.dis_patient_ph || '');
                $('#edittd_dis_counselled_by').val(d.dis_counselled_by || '');
                $('#edittd_dis_counselled_by_include').val(typeof d.dis_counselled_by_include === 'string' && d.dis_counselled_by_include.indexOf('[') === 0 ? (JSON.parse(d.dis_counselled_by_include || '[]')[0] || '') : (d.dis_counselled_by_include || ''));
                $('#edittd_dis_counselled_by_not_include').val(typeof d.dis_counselled_by_not_include === 'string' && d.dis_counselled_by_not_include.indexOf('[') === 0 ? (JSON.parse(d.dis_counselled_by_not_include || '[]')[0] || '') : (d.dis_counselled_by_not_include || ''));
                if (d.dis_counselled_by_include) $('#edit_counselled_include_chk').prop('checked', true);
                if (d.dis_counselled_by_not_include) $('#edit_counselled_not_include_chk').prop('checked', true);
                $('#edittd_dis_final_amt').val(d.dis_final_auth || '');
                $('#edittd_dis_brno').val(d.dis_branch_no || '');
                $('#edittd_dis_auth_by').val(d.dis_auth_by || '');
                $('#edittd_dis_final_approve').val(d.dis_approved_by || '');
                var $attList = $('#edit_attachment_existing_list');
                $attList.empty();
                var paths = [];
                try {
                    paths = typeof d.dis_attachments === 'string' ? JSON.parse(d.dis_attachments || '[]') : (d.dis_attachments || []);
                } catch (e) { paths = []; }
                
                console.log("paths", paths);
                console.log("baseUrl", baseUrl);
                
                paths.forEach(function (p) {
                    var name = (p && p.split) ? p.split('/').pop() : p;
                    var url = (p.indexOf('http') === 0) ? p : (baseUrl + '/' + p.replace(/^\//, ''));
                    $attList.append(
                        $('<a class="attachment-file-link" href="#" data-url="' + url + '" data-name="' + (name || '') + '">').text(name || 'File')
                    );
                });
                setPreview($('#imagewifesign'), normPath(d.dis_wife_sign));
                setPreview($('#husimgPreviewe'), normPath(d.dis_husband_sign));
                setPreview($('#drimgPreviewe'), normPath(d.dis_drsign));
                setPreview($('#ccimgPreviewe'), normPath(d.dis_cc_sign));
                setPreview($('#adminimgPreviewe'), normPath(d.dis_admin_sign));
            });
        } else {
            var row = $(this).closest('tr');
            $('#exampleModal2').modal('show');
            fillEditFormFromRow(row);
        }
      });

    $(document).on('click', '#edit_attachment_existing_list .attachment-file-link', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var name = $(this).data('name') || '';
        if (!url) return;
        var ext = (name.split('.').pop() || '').toLowerCase();
        var canEmbed = /^(pdf|jpg|jpeg|png|gif|webp)$/.test(ext);
        var $frame = $('#attachmentViewFrame');
        var $download = $('#attachmentViewDownload');
        var $link = $('#attachmentViewLink');
        if (canEmbed) {
            $frame.show().attr('src', url);
            $download.hide();
        } else {
            $frame.hide().attr('src', '');
            $link.attr('href', url).text('Open in new tab');
            $download.show();
        }
        $('#attachmentViewModal').modal('show');
    });
    $('#attachmentViewModal').on('hidden.bs.modal', function () {
        $('#attachmentViewFrame').attr('src', '');
    });

    $(document).on('click', '.attachment-row-icon', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var raw = $(this).data('attachments');
        if (!raw) return;
        var paths = [];
        try {
            paths = typeof raw === 'string' ? JSON.parse(raw.replace(/&quot;/g, '"')) : (raw || []);
        } catch (err) {
            paths = [];
        }
        if (!baseUrl.includes('/public')) {
            baseUrl = baseUrl + '/public';
        }
        if (!paths.length) return;
        var urls = paths.map(function (p) {
            return (p.indexOf('http') === 0) ? p : (baseUrl + '/' + p.replace(/^\//, ''));
        });
        var firstUrl = urls[0];
        var firstName = (paths[0] && paths[0].split) ? paths[0].split('/').pop() : '';
        var ext = (firstName.split('.').pop() || '').toLowerCase();
        var canEmbed = /^(pdf|jpg|jpeg|png|gif|webp)$/.test(ext);
        var $frame = $('#attachmentViewFrame');
        var $download = $('#attachmentViewDownload');
        var $link = $('#attachmentViewLink');
        if (canEmbed) {
            $frame.show().attr('src', firstUrl);
            $download.hide();
        } else {
            $frame.hide().attr('src', '');
            $link.attr('href', firstUrl).text('Open in new tab');
            $download.show();
        }
        if (urls.length > 1) {
            var listHtml = '';
            urls.forEach(function (u, i) {
                var n = (paths[i] && paths[i].split) ? paths[i].split('/').pop() : 'File ' + (i + 1);
                listHtml += '<a href="' + u + '" target="_blank" class="d-block small mb-1">' + n + '</a>';
            });
            if (!$('#attachmentViewModal .attachment-list-extra').length) {
                $('#attachmentViewModal .modal-body').append('<div class="attachment-list-extra mt-2 small"></div>');
            }
            $('#attachmentViewModal .attachment-list-extra').html('<span class="text-muted">Other files:</span>' + listHtml).show();
        } else {
            $('#attachmentViewModal .attachment-list-extra').empty().hide();
        }
        $('#attachmentViewModal').modal('show');
    });

    $('#saved-select-all').on('change', function () {
        var checked = $(this).prop('checked');
        $('#sveddata_tbl .approve-row-cb').prop('checked', checked);
    });

    $('#btn-approve-selected, #btn-reject-selected').on('click', function () {
        if (typeof discount_approvereject_url === 'undefined') return;
        var status = $(this).attr('id') === 'btn-approve-selected' ? 1 : 2;
        var actionText = status === 1 ? 'Approve' : 'Reject';
        var ids = [];
        $('#sveddata_tbl .approve-row-cb:checked').each(function () {
            ids.push($(this).data('id'));
        });
        if (!ids.length) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'No selection', text: 'Please select at least one row to ' + actionText.toLowerCase() + '.' });
            } else {
                alert('Please select at least one row to ' + actionText.toLowerCase() + '.');
            }
            return;
        }
        if (status === 2) {
            $('#reject_reason_text').val('');
            $('#reject_reason_error').hide();
            $('#rejectReasonModal').data('reject-id', null).data('reject-row', null).data('reject-btn', null).data('reject-ids', ids).modal('show');
            return;
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to approve ' + ids.length + ' selected discount(s).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve selected!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (!result.isConfirmed) return;
                doBulkApproveReject(ids, 1, 'Approve');
            });
        } else {
            if (confirm('You want to approve ' + ids.length + ' selected discount(s). Continue?')) {
                doBulkApproveReject(ids, 1, 'Approve');
            }
        }
    });

    function doBulkApproveReject(ids, status, actionText, rejectReason) {
        var token = $('meta[name="csrf-token"]').attr('content');
        var done = 0;
        var total = ids.length;
        var failed = [];
        function runNext() {
            if (done >= total) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: failed.length ? 'warning' : 'success',
                        title: actionText + 'd',
                        text: failed.length ? (total - failed.length) + ' succeeded, ' + failed.length + ' failed.' : 'All selected rows have been ' + actionText.toLowerCase() + 'd.'
                    });
                }
                discountsaveformdata();
                return;
            }
            var id = ids[done];
            var data = { id: id, status: status, _token: token };
            if (status === 2 && rejectReason) data.reject_reason = rejectReason;
            $.ajax({
                url: discount_approvereject_url,
                type: 'POST',
                data: data,
                success: function (res) {
                    if (!res.success) failed.push(id);
                    done++;
                    runNext();
                },
                error: function () {
                    failed.push(id);
                    done++;
                    runNext();
                }
            });
        }
        runNext();
    }

function renderTabledocument(data, pageSizedocuments, pageNum) {
    var dataArr = Array.isArray(data) ? data : [];
  // Calculate start and end index for pagination
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    
    var pageData = dataArr.slice(startIdx, endIdx);

    var body = "";
    var totalItems = dataArr.length;  // Total number of records
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
        '<td class="tdview totalbil">' + totalbil + '</td>' +
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
        console.log("pageData.length",pageData.length);
        
    // Update table body
    $("#pending_table_Body").html(body);
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
    var moredatefittervale = $('#mydateviewsall').text().trim();
    var datefiltersave = $('#mydateviewsallsave').text().trim();
    if ($(this).hasClass('applyBtn')) {
        var datefilltervaluenew = $('.drp-selected').text();
        if (datefilltervaluenew) {
            var dateRange = datefilltervaluenew.split(' - ');
            function convertDateFormat(dateStr) {
                let parts = dateStr.split('/');
                return `${parts[1]}/${parts[0]}/${parts[2]}`;
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            moredatefittervale = `${startDate} - ${endDate}`;
            datefiltersave = `${startDate} - ${endDate}`;
        }
    }
    var isSavedVisible = $('.saved_overview').is(':visible');
    if (!isSavedVisible && moredatefittervale) ticketdatefillterrange(moredatefittervale, fitterremovedata);
    else if (isSavedVisible && datefiltersave) datefillterrange(datefiltersave, fitterremovedata);
});

    function dismorefilterview(uniqueResults,url,moredatefittervale, ph_id = '') {
       $("#pending_table_Body").closest('.table-container').hide();
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
                   discountFormHandleSuccess(responseData);
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
            discountformdata();
           }

       }


  var fitterremovedata = [];
function ticketdatefillterrange(moredatefittervale,fitterremovedata) {
    $("#pending_table_Body").closest('.table-container').hide();
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
            discountFormHandleSuccess(responseData);
        },
        error: function (xhr, status, error) {
          stopLoader(false, error);
            $("#pending_table_Body").closest('.table-container').hide();
            console.error("AJAX Error:", status, error);
        }
    });
}

$(document).on('click', '.sec_options_marketers div', function () {
    console.log("sefsdfdsf");
    
    $(".my_value_views").text("");
    $('.clear_my_views').show();
    $(".my_search_view").show();

    var moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
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
    var moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
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
    discountformdata();
});



$(document).on("click", ".my_value_views", function () {
    $('.morefittersclr').val("");
    var moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });

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

    const moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
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
    const loaderContainer = $('#loader-container');

    loaderProgress = 0;
    if (loaderContainer.length) loaderContainer.show();
    progressText.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    if (errorMessage.length) errorMessage.hide().text('');

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
            $('#loader-container').hide();
            $("#pending_table_Body").closest('.table-container').show();
        }, 500);
    } else {
        progressText.text('Error loading data');
        progressBar.css('background-color', 'red');
        if (errorMessage.length) errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            $('#loader-container').hide();
            $("#pending_table_Body").closest('.table-container').hide();
        }, 1000);
    }
}
function startLoader2() {
    const progressBar = $('.progress-bar2');
    const progressText = $('.progress-bar2');
    const errorMessage = $('#error-message');
    const loaderContainer = $('#loader-container-save');
    var $tbody = $('#sveddata_tbl');
    var $container = $('#saved-table-container');

    loaderProgress = 0;
    if (loaderContainer.length) loaderContainer.show();
    progressText.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    if (errorMessage.length) errorMessage.hide().text('');

    if ($container.length) $container.show();
    if ($tbody.length) {
        $tbody.html('<tr id="saved-table-loading-row"><td colspan="26" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 mb-0 small text-muted">Loading...</p></td></tr>');
    }

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
            $('#loader-container-save').hide();
            $("#sveddata_tbl").closest('.table-container').show();
        }, 500);
    } else {
        progressText.text('Error loading data');
        progressBar.css('background-color', 'red');
        if (errorMessage.length) errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            $('#loader-container-save').hide();
            $("#sveddata_tbl").closest('.table-container').hide();
        }, 1000);
    }
}
 function datefillterrange(datefiltersave,fitterremovedata) {
    currentFilter = datefiltersave;
	var morefilltersall=fitterremovedata.join(" AND ");
   startLoader2();
        $("#sveddata_tbl").closest('.table-container').hide();
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
// Saved filter: single-select for Zone and Branch
function savedFilterSyncBadgesAndApply(phid) {
    phid = (phid !== undefined) ? phid : ($('#save_mrd_views').val() || '').trim();
    $(".my_savevalue_views").text("");
    $('.clear_my_saveviews').show();
    $(".my_search_saveview").show();
    var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
    var resultsArray_marketer = [];
    $(".savedatasearch").each(function () {
        var value = $(this).val();
        if (value !== "") {
            resultsArray_marketer.push($(this).attr('name') + "='" + value + "'");
        }
    });
    var moreFilterValues_market = [$("#save_zone_views").val(), $('#save_loc_views').val(), $('#save_mrd_views').val(), $('#save_status_filter option:selected').text()];
    $(".value_save_mysearch").each(function (index) {
        $(this).text(moreFilterValues_market[index] || "");
    });
    fitterremovedata = resultsArray_marketer.map(function(f) { return f.replace(/, /g, ','); });
    discountsavefilterview(fitterremovedata, disformsave_data, moredatefittervale, phid, $('#save_status_filter').val());
}
// Single-select: one zone, one branch (saved tab)
$(document).on('click', '.savedata_options div', function (e) {
    if ($(this).hasClass('no-results')) return;
    e.stopPropagation();
    var $opt = $(this);
    var text = $opt.text().trim();
    var value = $opt.data('value');
    if ($opt.parent().hasClass('selectzonesave')) {
        $('#save_zone_views').val(text);
        $('#dissave_zone_id').val(value || '');
    } else if ($opt.parent().attr('id') === 'getlocationsave') {
        $('#save_loc_views').val(text);
    }
    $(this).closest('.dropdown').removeClass('active');
    savedFilterSyncBadgesAndApply();
});
$(document).on("click", ".value_save_mysearch", function () {
    var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");
    if (clear_filtr === 'savebranch_search') $('#save_loc_views').val('');
    if (clear_filtr === 'savezone_search') { $('#save_zone_views').val(''); $('#dissave_zone_id').val(''); }
    if (clear_filtr === 'savemrdno_search') $('#save_mrd_views').val('');
    if (clear_filtr === 'savestatus_search') $('#save_status_filter').val('');
    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });
    discountsavefilterview(fitterremovedata, disformsave_data, moredatefittervale, ($('#save_mrd_views').val() || '').trim(), $('#save_status_filter').val());
});
// $(document).on("click", ".clear_my_saveviews", function () {
//     fitterremovedata.length = 0;
//     $('.savedatasearch').val("");
//     $(".my_savevalue_views").text("");
//     $('.clear_my_saveviews').hide();
//     $(".my_search_saveview").hide();
//     $(".value_save_mysearch").text("");
//     discountsaveformdata();
// });

$(document).on("click", ".clear_my_saveviews", function () {
    fitterremovedata.length = 0;
    $('.savedatasearch').val("");
    $(".my_savevalue_views").text("");
    $(".value_save_mysearch").text("");
    $('.clear_my_saveviews').hide();
    $(".my_search_saveview").hide();
    $('#dissave_zone_id').val('');
    $('#save_zone_views').val('');
    $('#save_loc_views').val('');
    $('#save_status_filter').val('');
    discountsaveformdata();
});

$(document).on("click", ".my_savevalue_views", function () {
    $('.morefittersclr').val("");
    var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
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
   discountsavefilterview(fitterremovedata,disformsave_data,moredatefittervale, phid, $('#save_status_filter').val());
});

$('#save_status_filter').on('change', function () {
    savedFilterSyncBadgesAndApply();
});

$('#save_mrd_views').on('input', function () {
    const phid = $(this).val().trim();
     $(".my_savevalue_views").text("");
    $('.clear_my_saveviews').show();
    $(".my_search_saveview").show();
    const moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
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
        $('#save_mrd_views').val(),
        $('#save_status_filter option:selected').text()
    ];

    $(".value_save_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    discountsavefilterview(fitterremovedata,disformsave_data,moredatefittervale, phid, $('#save_status_filter').val());
});
    $('#itemsPerPageSelectsave').change(function() {
      var pageSizedocuments = parseInt($(this).val());
       renderPaginationsaved(dataSaved, pageSizedocuments, 1);
    });
function handlesavedSuccess(responseData) {
    $("#sveddata_tbl").closest('.table-container').show();
    var raw = (responseData && responseData.data != null) ? responseData.data : (responseData || []);
    dataSaved = Array.isArray(raw) ? raw : [];
    const pageSizedocuments = parseInt($('#itemsPerPageSelectsave').val()) || 10;
    renderPaginationsaved(dataSaved, pageSizedocuments, 1);
    if (responseData && (responseData.counts || responseData.statistics)) renderStatisticsCards(responseData.statistics, responseData);
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
        renderPaginationsaved(data, pageSizedocuments, pageNum);
        renderTablesaved(data, pageSizedocuments, pageNum);
    });
    renderTablesaved(data, pageSizedocuments, currentPage);
}

function renderStatisticsCards(statistics, responseData) {
    var counts = (responseData && responseData.counts) ? responseData.counts : {};
    var totalDiscountAmount = (responseData && responseData.total_discount_amount !== undefined) ? responseData.total_discount_amount : (counts.total_discount_amount || 0);

    // Update all stat cards by data-stat-key (dynamic cards per access_limits)
    $('.stats-container .stat-card').each(function() {
        var key = $(this).data('stat-key');
        if (!key) return;
        var val = counts[key];
        var $valEl = $(this).find('.stat-value');
        if (key === 'total_discount_amount') {
            $valEl.text('₹' + (parseFloat(totalDiscountAmount) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        } else {
            $valEl.text(typeof val === 'number' ? val : (parseInt(val, 10) || 0));
        }
    });
}


function discountsaveformdata() {
      startLoader2();
		var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
		$(".value_save_mysearch").text("");
		$.ajax({
			url: disformsave_data,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				status_filter: $('#save_status_filter').val() || ''
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
     function discountsavefilterview(uniqueResults,url,moredatefittervale, ph_id = '', status_filter = '') {
          startLoader2();
        if(uniqueResults!="" || status_filter!="")
           {
           var morefilltersall = (uniqueResults && uniqueResults.length) ? uniqueResults.join(" AND ") : '';
           $.ajax({
               url: url,
               type: "GET",
               data: {
                   morefilltersall: morefilltersall,
                   moredatefittervale:moredatefittervale,
                   mrodnofilter: ph_id,
                   status_filter: status_filter || ''
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
           } else {
           $('.clear_my_saveviews').hide();
            $(".my_search_saveview").hide();
            discountsaveformdata();
          }
       }
function renderTablesaved(data, pageSizedocuments, pageNum) {
    var dataArr = Array.isArray(data) ? data : [];
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = dataArr.slice(startIdx, endIdx);
    var body = "";
    var totalItems = dataArr.length;

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
        let finalamount = user.dis_final_auth || '-';
        let postdis = user.dis_post_discount || '-';
        let locationid = user.locationid || user.dis_zone_id || '-';
        let dateObj = new Date(user.created_at);
        let date = dateObj.toLocaleDateString('en-GB'); // dd/mm/yyyy

        function parseCounselled(val) {
            if (!val) return '-';
            if (typeof val === 'string' && val.indexOf('[') === 0) {
                try { var a = JSON.parse(val); return (a && a[0]) ? a[0] : val; } catch (e) { return val; }
            }
            return val;
        }
        let includeText = parseCounselled(user.dis_counselled_by_include);
        let notIncludeText = parseCounselled(user.dis_counselled_by_not_include);

        let attachmentsJson = '';
        let attachmentsArr = [];
        try {
            attachmentsArr = typeof user.dis_attachments === 'string' ? JSON.parse(user.dis_attachments || '[]') : (user.dis_attachments || []);
            if (attachmentsArr && attachmentsArr.length) attachmentsJson = typeof user.dis_attachments === 'string' ? user.dis_attachments : JSON.stringify(attachmentsArr);
        } catch (e) {}
        let attachmentIconHtml = attachmentsArr.length
            ? '<span class="attachment-row-icon bi bi-paperclip" data-attachments="' + (attachmentsJson.replace(/"/g, '&quot;')) + '" title="' + attachmentsArr.length + ' file(s) – click to preview" style="cursor:pointer;font-size:1.2rem;"></span>'
            : '-';

        let access_limits = admin_user.access_limits;

        function renderStatus(status, approverName) {
            status = parseInt(status);
            let icon = '';
            if (status === 1) icon = `<span style="color:green;font-weight:bold;font-size: 20px;" title="Approved">✔</span>`;
            else if (status === 2) icon = `<span style="color:red;font-weight:bold;font-size: 20px;" title="Rejected">✖</span>`;
            else icon = `<span style="color:#f0ad4e;font-weight:bold;font-size: 14px;" title="Pending">⏳ Pending</span>`;
            const name = (approverName && String(approverName).trim()) ? String(approverName).trim() : '—';
            return `<div>${icon}</div><div class="small text-muted" style="font-size:11px;margin-top:2px;">${name}</div>`;
        }

        var isPending = false;
        if (access_limits == 1) isPending = (user.final_approver == 0);
        else if (access_limits == 2) isPending = (user.zonal_approver == 0);
        else if (access_limits == 3) isPending = (user.admin_approver == 0);
        else if (access_limits == 4) isPending = (user.audit_approver == 0);

        var checkboxCell = '';
        if (access_limits == 1 || access_limits == 2 || access_limits == 3 || access_limits == 4) {
            if (isPending) {
                checkboxCell = '<td class="tdview" onclick="event.stopPropagation();"><input type="checkbox" class="form-check-input approve-row-cb" data-id="' + id + '"></td>';
            } else {
                checkboxCell = '<td class="tdview"></td>';
            }
        }

        // Build table row
        body += '<tr onclick="rowClick(event)">' +
            (checkboxCell) +
            '<td class="tdview" id="idfetch" data-id="' + id + '"><strong>#' + (startIdx + index + 1) + '</strong></td>' +
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
            '<td class="tdview">' + expected_request + ' (' + form_status + ')</td>' +
            '<td class="tdview expamount" style="display:none;">' + expected_request + '</td>' +
            '<td class="tdview requestfor" style="display:none;">' + form_status + '</td>' +
            '<td class="tdview postdis">' + postdis + '</td>' +
            '<td class="tdview mobileno" style="display:none;">' + patient_ph + '</td>' +
            '<td class="tdview include-col">' + includeText + '</td>' +
            '<td class="tdview notinclude-col">' + notIncludeText + '</td>' +
            '<td class="tdview attach-col">' + attachmentIconHtml + '</td>' +
            '<td class="tdview authby">' + auth_by + '</td>' +
            '<td class="tdview finalapprove">' + approved_by + '</td>' +
            '<td class="tdview brno">' + brno + '</td>' +
            '<td class="tdview finalamount">' + finalamount + '</td>' +
            '<td class="tdview">' + (user.username || '') + '</br>' + (user.userid || '') + '</td>' +
            '<td class="tdview reject-reason-cell" title="' + (user.reject_reason || '').replace(/"/g, '&quot;') + '">' + (user.reject_reason ? (user.reject_reason.length > 50 ? user.reject_reason.substring(0, 50) + '...' : user.reject_reason) : '-') + '</td>' +
            '<td class="tdview wife_sign" style="display:none;">' + (user.dis_wife_sign || '') + '</td>' +
            '<td class="tdview husband_sign" style="display:none;">' + (user.dis_husband_sign || '') + '</td>' +
            '<td class="tdview drsign" style="display:none;">' + (user.dis_drsign || '') + '</td>' +
            '<td class="tdview cc_sign" style="display:none;">' + (user.dis_cc_sign || '') + '</td>' +
            '<td class="tdview admin_sign" style="display:none;">' + (user.dis_admin_sign || '') + '</td>' +
            '<td class="tdview wifemrdno" style="display:none;" data-ph_id="' + user.dis_wife_mrd_no + '"><a href="#">' + user.dis_wife_mrd_no + '</a></td>' +
            '<td class="tdview husmrdno" style="display:none;" data-ph_id="' + user.dis_husband_mrd_no + '"><a href="#">' + user.dis_husband_mrd_no + '</a></td>' +
            '<td class="tdview wifename" style="display:none;"><a href="#">' + user.dis_wife_name + '</a></td>' +
            '<td class="tdview husname" style="display:none;"><a href="#">' + user.dis_husband_name + '</a></td>';

        if (access_limits == 1) {
            body +=
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver, user.admin_approver_name) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver, user.zonal_approver_name) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver, user.audit_approver_name) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver, user.final_approver_name) + '</td>';
        } else if (access_limits == 2) {
            body +=
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver, user.admin_approver_name) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver, user.audit_approver_name) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver, user.final_approver_name) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver, user.zonal_approver_name) + '</td>';
        } else if (access_limits == 3) {
            body +=
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver, user.zonal_approver_name) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver, user.audit_approver_name) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver, user.final_approver_name) + '</td>' +
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver, user.admin_approver_name) + '</td>';
        } else if (access_limits == 4) {
            body +=
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver, user.admin_approver_name) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver, user.zonal_approver_name) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver, user.final_approver_name) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver, user.audit_approver_name) + '</td>';
        } else {
            body +=
                '<td class="tdview" data-column="admin-approver">' + renderStatus(user.admin_approver, user.admin_approver_name) + '</td>' +
                '<td class="tdview" data-column="zonal-approver">' + renderStatus(user.zonal_approver, user.zonal_approver_name) + '</td>' +
                '<td class="tdview" data-column="audit-approver">' + renderStatus(user.audit_approver, user.audit_approver_name) + '</td>' +
                '<td class="tdview" data-column="final-approver">' + renderStatus(user.final_approver, user.final_approver_name) + '</td>';
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
        body += '<tr><td colspan="27" class="tdview" style="text-align: center;">No data available</td></tr>';
    }

    $("#sveddata_tbl").html(body);
    $("#billsavecounts").text(totalItems);

    if ($('#sveddata_tbl .approve-row-cb').length) {
        $('#saved-bulk-actions').show();
    } else {
        $('#saved-bulk-actions').hide();
    }
    $('#saved-select-all').prop('checked', false);
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
    $adminCell.html(renderStatus(record.admin_approver, record.admin_approver_name));
    $zonalCell.html(renderStatus(record.zonal_approver, record.zonal_approver_name));
    $auditCell.html(renderStatus(record.audit_approver, record.audit_approver_name));
    $finalCell.html(renderStatus(record.final_approver, record.final_approver_name));
    
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

// Approve / Reject discount (Saved form) – update table row and stats box
// $(document).on('click', '.approve-btn, .reject-btn', function (e) {
//     if (typeof discount_approvereject_url === 'undefined') return;
//     e.preventDefault();
//     e.stopPropagation();
//     var $btn = $(this);
//     var status = $btn.data('status');
//     var id = $btn.data('id');
//     var $row = $btn.closest('tr');
//     var actionText = status == 1 ? 'Approve' : 'Reject';
//     if (!confirm('Are you sure you want to ' + actionText.toLowerCase() + ' this discount?')) return;
//     $row.addClass('updating-row');
//     $btn.css('opacity', '0.5');
//     $.ajax({
//         url: discount_approvereject_url,
//         type: 'POST',
//         data: {
//             id: id,
//             status: status,
//             _token: $('meta[name="csrf-token"]').attr('content')
//         },
//         success: function (res) {
//             $row.removeClass('updating-row');
//             $btn.css('opacity', '1');
//             if (res.success) {
//                 if (typeof updateTableRow === 'function') updateTableRow($row, res.record, status);
//                 if (res.counts && typeof renderStatisticsCards === 'function') {
//                     renderStatisticsCards(res.statistics || [], res);
//                 }
//                 if (typeof dataSaved !== 'undefined' && Array.isArray(dataSaved) && res.record) {
//                     var idx = dataSaved.findIndex(function (r) { return r.dis_id == id; });
//                     if (idx !== -1) dataSaved[idx] = res.record;
//                 }
//             }
//         },
//         error: function (xhr) {
//             $row.removeClass('updating-row');
//             $btn.css('opacity', '1');
//             alert((xhr.responseJSON && xhr.responseJSON.message) || xhr.statusText || 'Request failed');
//         }
//     });
// });

$(document).on('click', '.approve-btn, .reject-btn', function (e) {

    if (typeof discount_approvereject_url === 'undefined') return;

    e.preventDefault();
    e.stopPropagation();

    var $btn = $(this);
    var status = $btn.data('status');
    var id = $btn.data('id');
    var $row = $btn.closest('tr');
    var actionText = status == 1 ? 'Approve' : 'Reject';

    if (status == 2) {
        $('#reject_reason_text').val('');
        $('#reject_reason_error').hide();
        $('#rejectReasonModal').data('reject-id', id).data('reject-row', $row).data('reject-btn', $btn).modal('show');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to approve this discount.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $row.addClass('updating-row');
        $btn.css('opacity', '0.5');

        $.ajax({
            url: discount_approvereject_url,
            type: 'POST',
            data: {
                id: id,
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {

                $row.removeClass('updating-row');
                $btn.css('opacity', '1');

                if (res.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Discount has been successfully approved.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    if (typeof updateTableRow === 'function')
                        updateTableRow($row, res.record, status);

                    if (res.counts && typeof renderStatisticsCards === 'function') {
                        renderStatisticsCards(res.statistics || [], res);
                    }

                    if (typeof dataSaved !== 'undefined' && Array.isArray(dataSaved) && res.record) {
                        var idx = dataSaved.findIndex(function (r) {
                            return r.dis_id == id;
                        });
                        if (idx !== -1) dataSaved[idx] = res.record;
                    }

                } else {
                    Swal.fire('Error', res.message || 'Something went wrong', 'error');
                }
            },
            error: function (xhr) {

                $row.removeClass('updating-row');
                $btn.css('opacity', '1');

                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: (xhr.responseJSON && xhr.responseJSON.message) 
                            || xhr.statusText 
                            || 'Something went wrong!'
                });
            }
        });

    });

});

$('#reject_reason_submit_btn').on('click', function () {
    var reason = $('#reject_reason_text').val().trim();
    $('#reject_reason_error').hide();
    if (!reason) {
        $('#reject_reason_error').show();
        return;
    }
    var ids = $('#rejectReasonModal').data('reject-ids');
    var id = $('#rejectReasonModal').data('reject-id');
    var $row = $('#rejectReasonModal').data('reject-row');
    var $btn = $('#rejectReasonModal').data('reject-btn');
    if (ids && ids.length) {
        doBulkApproveReject(ids, 2, 'Reject', reason);
        $('#rejectReasonModal').modal('hide');
        $('#reject_reason_text').val('');
        $('#rejectReasonModal').data('reject-ids', null);
        return;
    }
    if (!id) return;
    $row.addClass('updating-row');
    if ($btn && $btn.length) $btn.css('opacity', '0.5');
    $.ajax({
        url: discount_approvereject_url,
        type: 'POST',
        data: {
            id: id,
            status: 2,
            reject_reason: reason,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            $('#rejectReasonModal').modal('hide');
            $('#reject_reason_text').val('');
            $row.removeClass('updating-row');
            if ($btn && $btn.length) $btn.css('opacity', '1');
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Rejected!', text: 'Discount has been rejected.', timer: 2000, showConfirmButton: false });
                if (typeof updateTableRow === 'function' && res.record) updateTableRow($row, res.record, 2);
                if (res.counts && typeof renderStatisticsCards === 'function') renderStatisticsCards(res.statistics || [], res);
                if (typeof dataSaved !== 'undefined' && Array.isArray(dataSaved) && res.record) {
                    var idx = dataSaved.findIndex(function (r) { return r.dis_id == id; });
                    if (idx !== -1) dataSaved[idx] = res.record;
                }
                discountsaveformdata();
            } else {
                Swal.fire('Error', res.message || 'Something went wrong', 'error');
            }
        },
        error: function (xhr) {
            $row.removeClass('updating-row');
            if ($btn && $btn.length) $btn.css('opacity', '1');
            Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message) || xhr.statusText || 'Request failed.');
        }
    });
});

    // $(document).on('click', '.approve-btn, .reject-btn', function (e) {
    //     e.preventDefault();
    //     e.stopPropagation();

    //     const $btn = $(this);
    //     const status = $btn.data('status');
    //     const id = $btn.data('id');
    //     const $row = $btn.closest('tr');

    //     const actionText = status == 1 ? 'Approve' : 'Reject';
    //     const actionColor = status == 1 ? '#2e7d32' : '#c62828';

    //     Swal.fire({
    //         title: `Confirm ${actionText}?`,
    //         text: `Are you sure you want to ${actionText.toLowerCase()} this discount?`,
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: actionColor,
    //         cancelButtonColor: '#6c757d',
    //         confirmButtonText: `Yes, ${actionText}`,
    //         cancelButtonText: 'Cancel'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
                
    //             // Show loading state
    //             $row.addClass('updating-row');
    //             $btn.css('opacity', '0.5');

    //             $.ajax({
    //                 url: discount_approvereject_url,
    //                 type: "POST",
    //                 data: {
    //                     id: id,
    //                     status: status,
    //                     _token: $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 success: function (res) {
    //                     if (res.success) {
    //                         // Show success message
    //                         Swal.fire({
    //                             title: 'Success!',
    //                             text: res.message,
    //                             icon: 'success',
    //                             timer: 2000,
    //                             showConfirmButton: false
    //                         });

    //                         // Update the specific row
    //                         updateTableRow($row, res.record, status);
                            
    //                         // Update statistics cards
    //                         if (res.statistics) {
    //                             renderStatisticsCards(res.statistics);
    //                         }
                            
    //                         // Update count badges
    //                         if (res.counts) {
    //                             updateCountBadges(res.counts);
    //                         }

    //                         // Remove loading state
    //                         $row.removeClass('updating-row');

    //                     } else {
    //                         Swal.fire('Error!', res.message || 'Update failed', 'error');
    //                         $row.removeClass('updating-row');
    //                         $btn.css('opacity', '1');
    //                     }
    //                 },
    //                 error: function (xhr) {
    //                     let errorMsg = 'Something went wrong';
    //                     if (xhr.responseJSON && xhr.responseJSON.message) {
    //                         errorMsg = xhr.responseJSON.message;
    //                     }
                        
    //                     Swal.fire('Error!', errorMsg, 'error');
    //                     $row.removeClass('updating-row');
    //                     $btn.css('opacity', '1');
    //                 }
    //             });
    //         }
    //     });
    // });