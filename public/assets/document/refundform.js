$(document).ready(function () {
   if(fitterremovedata.length ==  0){
			 var defaultLocation = "Chennai - Sholinganallur";
       var defaultZone = "TN CHENNAI";
			$('#ref_loc_views').val(defaultLocation);
		  $('#ref_zone_views').val(defaultZone);
			$('.dropdown-options div').each(function() {

        if ($(this).text().trim() === defaultZone){
          $(this).addClass('selected');
        }
			});
            $('#getlocation > div').each(function(){
        if ($(this).text().trim() === defaultLocation) {
        $(this).addClass('selected');
				}
            });
		}

    $(".my_search_view").hide();
    $(".my_search_saveview").hide();
    refundformdata();
    refundsaveformdata();
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

    $(document).on('click','#submit_refundform',function () {
        alert(55);
    });
    $('#submit_refundform').click(function (event) {
        alert(11);
        event.preventDefault();
       let formData = new FormData();
       let zoneId = $('#zone_id').attr('data-value');
        formData.append('ref_zone_id', zoneId);
        formData.append('ref_wife_name', $('#wife_name').val());
        formData.append('ref_wife_mrd_no', $('#wifemrdno').val());
        formData.append('ref_husband_name', $('#husband_name').val());
        formData.append('ref_husband_mrd_no', $('#husbandmrdno').val());
        formData.append('ref_service_name', $('#service_name').val());
        formData.append('ref_total_bill', $('#totalbill').val());
        formData.append('ref_expected_request', $('#expected_request').val());
        formData.append('ref_form_status', $('input[name="request"]:checked').val() || '');
        formData.append('ref_counselled_by', $('#counselled_by').val());
        formData.append('ref_final_auth', $('#final_amount').val());
        formData.append('ref_branch_no', $('#branch_no').val());
        formData.append('ref_auth_by', $('#authourised_by').val());
        formData.append('ref_patient_ph', $('#patientph').val());
        formData.append('ref_approveded_by', $('#approveded_by').val());

     const signers = [
        { field: 'ref_wife_sign', radioName: 'wife-signature', canvasId: 'wifeCanvas', fileInputId: 'imageUpload', uploadDiv: '#wife-upload' },
        { field: 'ref_husband_sign', radioName: 'husband-signature', canvasId: 'husbandCanvas', fileInputId: 'husbandsignimg', uploadDiv: '#husband-upload' },
        { field: 'ref_drsign', radioName: 'dr-signature', canvasId: 'drCanvas', fileInputId: 'drsignimg', uploadDiv: '#dr-upload' },
        { field: 'ref_cc_sign', radioName: 'cc-signature', canvasId: 'ccCanvas', fileInputId: 'ccsignimg', uploadDiv: '#cc-upload' },
        { field: 'ref_admin_sign', radioName: 'admin-signature', canvasId: 'adminCanvas', fileInputId: 'adminsignimg', uploadDiv: '#admin-upload' },
    ];
    let pendingBlobs = 0;
  const finishEditSubmission = () => {
    if (pendingBlobs === 0) {
        $.ajax({
            url: refundformadded,
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
              clearForm();
                $("#exampleModaluser").modal('hide');
                refundformdata(); // refresh
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
                $('#expected_request').val("");
                $('#ex_ref_value').text("");
                $('input[name="expectdis"]').prop("checked", false);
                $('input[name="request"]').prop("checked", false);
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


     $('#itemsPerPageSelectdocument').change(function() {
        var pageSizedocuments = parseInt($(this).val());
        renderPaginationdocument(dataSourcedocument, pageSizedocuments);
        renderTabledocument(dataSourcedocument, pageSizedocuments, 1);  // Initially show the first page
    });

    $(document).on("click", '.edit_ref_details', function () {
         $('#exampleModal2').modal('show');
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
    var mobileno = row.find('.mobileno').text().trim();
    var consultby = row.find('.consultby').text().trim();
    var authby = row.find('.authby').text().trim();
    var finalapprove = row.find('.finalapprove').text().trim();
    var brno = row.find('.brno').text().trim();
    var finalamount = row.find('.finalamount').text().trim();
    var wife_sign = row.find('.wife_sign').text().trim();
var husband_sign = row.find('.husband_sign').text().trim();
var drsign = row.find('.drsign').text().trim();
var cc_sign = row.find('.cc_sign').text().trim();
var admin_sign = row.find('.admin_sign').text().trim();

function cleanPath(path) {
    return path.replace(/[\[\]\"\\]/g, '');
}

var imageviewswife = cleanPath(wife_sign);
var imageviewshus = cleanPath(husband_sign);
var imageviewsdr = cleanPath(drsign);
var imageviewscc = cleanPath(cc_sign);
var imageviewsadmin = cleanPath(admin_sign);

$('#imagewifesign').css('background-image', 'url(../' + imageviewswife + ')');
$('#husimgPreviewe').css('background-image', 'url(../' + imageviewshus + ')');
$('#drimgPreviewe').css('background-image', 'url(../' + imageviewsdr + ')');
$('#ccimgPreviewe').css('background-image', 'url(../' + imageviewscc + ')');
$('#adminimgPreviewe').css('background-image', 'url(../' + imageviewsadmin + ')');


            $('.location-dropdown-options div').removeClass('selected'); // clear previous
    var $selectedItem = $('.location-dropdown-options div[data-value="'+zoneId+'"]');
    $selectedItem.addClass('selected');
    var selectedText = $selectedItem.text();

    $('#edit_zone_id')
        .val(selectedText)
        .attr('data-value', zoneId);
            // $('#edit_ref_sno').val(data.ref_id);
            // $('#edit_ref_branch_name').val(data.ref_branch_name);
            $('#edit_ref_wife_name').val(wifename);
            $('#edit_ref_wife_mrd_no').val(wifemrdno);
            $('#edit_ref_husband_mrd_no').val(husmrdno);
            $('#edit_ref_husband_name').val(husname);
            $('#edit_ref_service_name').val(treatmentcat);
            $('#edittd_ref_total_bill').val(totalbil);
            $('#edittd_ref_expected_request').val(expamount);
            $('#ex_ref_value_edit').text(expamount);
            $("input[name='disrequest'][value='" +requestfor+ "']").prop("checked", true);
            $('#edit_ref_patient_ph').val(mobileno);
            $('#edit_ref_counselled_by').val(consultby);
            $('#edit_ref_final_auth').val(finalamount);
            $('#edit_ref_branch_no').val(brno);
            $('#edit_ref_auth_by').val(authby);
            $('#edit_ref_final_approve').val(finalapprove);
            // $('#wifeimgPreviewpage').css('background-image', 'url(../public/'+ imageviewswife + ')');
            // $('#husimgPreviewpage').css('background-image', 'url(../public/' + imageviewshus + ')');
            // $('#drimgPreviewpage').css('background-image', 'url(../public/' + imageviewsdr + ')');
            // $('#ccimgPreviewpage').css('background-image', 'url(../public/' + imageviewscc + ')');
            // $('#adminimgPreviewpage').css('background-image', 'url(../public/' + imageviewsadmin + ')');
            $('#wifeimgPreviewpage').css('background-image', 'url(../'+ imageviewswife + ')');
            $('#husimgPreviewpage').css('background-image', 'url(../' + imageviewshus + ')');
            $('#drimgPreviewpage').css('background-image', 'url(../' + imageviewsdr + ')');
            $('#ccimgPreviewpage').css('background-image', 'url(../' + imageviewscc + ')');
            $('#adminimgPreviewpage').css('background-image', 'url(../' + imageviewsadmin + ')');

      });



    $("#editrefundform").on("click",function(event){
        event.preventDefault();
        let formData = new FormData();
        // let zoneId = $('#edit_zone_id').attr('data-value');
        // formData.append('ref_zone_id', zoneId);
        formData.append('ref_zone_id', $('#edit_zone_id').attr('data-value'));
        formData.append('ref_wife_name', $('#edit_ref_wife_name').val());
        formData.append('ref_wife_mrd_no', $('#edit_ref_wife_mrd_no').val());
        formData.append('ref_husband_name', $('#edit_ref_husband_name').val());
        formData.append('ref_husband_mrd_no', $('#edit_ref_husband_mrd_no').val());
        formData.append('ref_service_name', $('#edit_ref_service_name').val());
        formData.append('ref_total_bill', $('#edittd_ref_total_bill').val());
        formData.append('ref_expected_request', parseFloat($('#ex_ref_value_edit').text().replace('₹', '').trim()) || 0);
        formData.append('ref_form_status', $('input[name="disrequest"]:checked').val() || '');
        formData.append('ref_counselled_by', $('#edit_ref_counselled_by').val());
        formData.append('ref_final_auth', $('#edit_ref_final_auth').val());
        formData.append('ref_branch_no', $('#edit_ref_branch_no').val());
        formData.append('ref_auth_by', $('#edit_ref_auth_by').val());
        formData.append('ref_patient_ph', $('#edit_ref_patient_ph').val());
        formData.append('ref_approved_by', $('#edit_ref_final_approve').val());
        formData.append('ref_patient_ph',$('#edit_ref_patient_ph').val());

         const signers = [
    { field: 'ref_wife_sign', radio: 'editwife-signature', canvasId: 'editwifeCanvas', fileInput: 'imagewsign' },
    { field: 'ref_husband_sign', radio: 'edithusband-signature', canvasId: 'edithusbandCanvas', fileInput: 'husbandsignimge' },
    { field: 'ref_drsign', radio: 'editdr-signature', canvasId: 'editdrCanvas', fileInput: 'drsignimge' },
    { field: 'ref_cc_sign', radio: 'editcc-signature', canvasId: 'editccCanvas', fileInput: 'ccsignimge' },
    { field: 'ref_admin_sign', radio: 'editadmin-signature', canvasId: 'editadminCanvas', fileInput: 'adminsignimge' },
  ];
  let pendingBlobs = 0;
  const finishEditSubmission = () => {
    if (pendingBlobs === 0) {
           $.ajax({
               url: refundformeditUrl,
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
                   refundformdata();
                    refundsaveformdata();
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

// Render table rows based on the page and page size
function renderTabledocument(data, pageSizedocuments, pageNum) {
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);
    var body = "";
    var totalItems = data.length;

     $.each(pageData, function(index, user) {
    // Determine gender and assign values accordingly
    let gender = user.gender || '';

    // Pull all possible values
    let wifemrdno = user.ref_wife_mrd_no || (gender === 'F' ? user.phid : '') || 'N/A';
    let wifename = user.ref_wife_name || (gender === 'F' ? user.name : '') || 'N/A';
    let husmrdno = user.ref_husband_mrd_no || (gender === 'M' ? user.phid : '') || 'N/A';
    let husname = user.ref_husband_name || (gender === 'M' ? user.name : '') || 'N/A';

    // These fallback values are used in visible MRD column
    let wmrd = wifemrdno;
    let wname = wifename;
    let hmrd = husmrdno;
    let hname = husname;

    // Additional fields with fallbacks
    let zone = user.zone || user.zone_name || 'N/A';
    let location = user.branch || user.location_name || 'N/A';
    let auth_by = user.ref_auth_by || "-";
    let approved_by = user.ref_approved_by || '-';
    let brno = user.ref_branch_no || '-';
    let treatmentcat = user.ref_service_name || '-';
    let totalbil = user.ref_total_bill || '-';
    let expected_request = user.ref_expected_request || '-';
    let form_status = user.ref_form_status || '-';
    let patient_ph = user.ref_patient_ph || user.mobile || '-';
    let counselled_by = user.ref_counselled_by || user.consultingdr_name || '-';
    let finalamount = user.ref_final_auth || '-';
    let locationid =user.locationid || user.ref_zone_id;
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
         '<td class="tdview wife_sign" style="display:none;">' + user.ref_wife_sign + '</td>' +
          '<td class="tdview husband_sign" style="display:none;">' + user.ref_husband_sign + '</td>' +
           '<td class="tdview drsign" style="display:none;">' + user.ref_drsign + '</td>' +
            '<td class="tdview cc_sign" style="display:none;">' + user.ref_cc_sign + '</td>' +
             '<td class="tdview admin_sign" style="display:none;">' + user.ref_admin_sign + '</td>' +
        '<td class="tdview" style="padding:3px;" id="savebtn">' +
            // '<button class="btn btn-primary tabledatasave" style="padding: 5px;border-radius: 5px;font-size: 12px;margin-left: 5px;">save</button>' +
            // '<img src="../assets/images/print.png" style="width: 23px;" alt="Icon" class="icon documentclk">' +
            '<a href="#" style="margin-left: 5px;"><img src="../assets/images/edit.png" style="width: 23px;margin-right:5px;" alt="Icon" class="icon edit_ref_details"></a>' +
        '</td>' +
        '</tr>';
});

    if (pageData.length === 0) {
			body += '<tr><td colspan="15" class="tdview" style="text-align: center;">No data available</td></tr>';
		}

    $("#document_tbl").html(body);
    $("#mycounts").text(totalItems);
}

function printRowData(trElement) {
  const row = $(trElement).closest('tr');
  const wifemrdno = row.find(".wifemrdno").text().trim();
  const husmrdno = row.find(".husmrdno").text().trim();
  const branchname =row.find('.branchname').text().trim();
  $.ajax({
    url: refundform_data,
    type: "GET",
    data: {
      ref_wife_mrd_no: wifemrdno,
      ref_husband_mrd_no: husmrdno
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
      // Safe image path cleaning
      const cleanPath = (path) => path ? path.replace(/[\[\]\"]/g, '') : '';

      const imageviews = cleanPath(data.ref_wife_sign);
      const imageviewshusband = cleanPath(data.ref_husband_sign);
      const imageviewsdr = cleanPath(data.ref_drsign);
      const imageviewscc = cleanPath(data.ref_cc_sign);
      const imageviewsadmin = cleanPath(data.ref_admin_sign);

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
              <div>S. No: ${data.ref_id || ''}</div>
            </div>

            <table>
              <tr><td>Branch Name:</td><td>${branchname || ''}</td></tr>
              <tr><td>Wife Name:</td><td>${data.ref_wife_name || ''}</td><td>MRD No:</td><td>${data.ref_wife_mrd_no || ''}</td></tr>
              <tr><td>Husband Name:</td><td>${data.ref_husband_name || ''}</td><td>MRD No:</td><td>${data.ref_husband_mrd_no || ''}</td></tr>
              <tr><td>Treatment Category:</td><td colspan="3">${data.ref_service_name || ''}</td></tr>
              <tr><td>Total Bill Value:</td><td>${data.ref_total_bill || ''}</td></tr>
              <tr><td>Discount Expected Request:</td><td>${data.ref_expected_request || ''} (${data.ref_form_status || ''})</td></tr>
              <tr><td>Counselled By:</td><td colspan="3">${data.ref_counselled_by || ''}</td><td>Patient Ph. No:</td><td>${data.ref_patient_ph || ''}</td></tr>
            </table>

            <table class="signatures">
              <tr>
                <td>Wife Sign:<br> ${imageviews ? `<img src="../${imageviews}" alt="Wife Sign">` : '-'}</td>
                <td>Husband Sign:<br> ${imageviewshusband ? `<img src="../${imageviewshusband}" alt="Husband Sign">` : '-'}</td>
              </tr>
              <tr>
                <td>Dr. Sign:<br> ${imageviewsdr ? `<img src="../${imageviewsdr}" alt="Dr Sign">` : '-'}</td>
                <td>CC Sign:<br> ${imageviewscc ? `<img src="../${imageviewscc}" alt="CC Sign">` : '-'}</td>
                <td>Admin Sign:<br> ${imageviewsadmin ? `<img src="../${imageviewsadmin}" alt="Admin Sign">` : '-'}</td>
              </tr>
            </table>

            <table>
              <tr><td>Authorised By:</td><td colspan="3">${data.ref_auth_by || ''}</td></tr>
              <tr><td>Final Approved By:</td><td colspan="3">${data.ref_approved_by || ''}</td></tr>
              <tr><td>B.R. No.:</td><td colspan="3">${data.ref_branch_no || ''}</td></tr>
              <tr><td>Final Authorised Amount:</td><td colspan="3">${data.ref_final_auth || ''}</td></tr>
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

function refundformdata() {
      startLoader();
        $("#document_tbl").hide();
		var moredatefittervale = $('#mydateallviews').text();
		$(".value_views_mysearch").text("");
		$("#my_ticket_details1").show();
		$.ajax({
			url: refdocdetialsUrl,
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

    function refmorefilterview(uniqueResults,url,moredatefittervale, ph_id = '') {
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
                   $("#my_ticket_details1").hide();
                   console.error("AJAX Error:", status, error);
               }
           });
           }else
          {
           $('.clear_my_views').hide();
            $(".my_search_view").hide();
            refundformdata();

          }
       }
      function handleSuccess(responseData) {
    $("#my_ticket_details1").hide();
    $("#document_tbl").show();

    dataSourcedocument = responseData.checkinData || [];
    var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val());
    renderPaginationdocument(dataSourcedocument, pageSizedocuments);
    renderTabledocument(dataSourcedocument, pageSizedocuments, 1);
}

    var fitterremovedata = [];
    function ticketdatefillterrange(moredatefittervale,fitterremovedata) {

    currentFilter = moredatefittervale;
	var morefilltersall=fitterremovedata.join(" AND ");

    $("#document_tbl").show();
    $.ajax({
        url: refdocdetialsUrl,
        type: "GET",
        data: {
      moredatefittervale: currentFilter,
			morefilltersall: morefilltersall,

        },
        success: function (responseData) {
            handleSuccess(responseData);
        },
        error: function (xhr, status, error) {
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
    // Initialize an array to hold the filtered results
    let resultsArray_marketer = [];
    // Collect the values from the search inputs
    $(".documentdatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const result =  $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });

    const moreFilterValues_market = [
        $("#ref_zone_views").val(),
        $('#ref_loc_views').val(),
       $('#ref_mrd_views').val()
    ];
    // alert(moreFilterValues_market);
    // Update the UI with the selected filter values
    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
        $(this).text(filterValue);
    });

     fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    // Call function with the processed data
    refmorefilterview(fitterremovedata,refdocdetialsUrl,moredatefittervale);

});

$(document).on("click", ".value_views_mysearch", function () {
    var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");


    if(clear_filtr == 'refbranch_search'){
        $('#ref_loc_views').val('');
    }
    if(clear_filtr == 'refzone_search'){
        $('#ref_zone_views').val('');
    }
     if(clear_filtr == 'refmrdno_search'){
      $('#ref_mrd_views').val('');
    }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      refmorefilterview(fitterremovedata,refdocdetialsUrl,moredatefittervale);
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
    refundformdata();
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

    // If the filter is for the date input, reset its value
   if (key === 'phid') {
        $('#ref_mrd_views').val('');
    }

    // Remove the filter from the list (cleanup logic)
    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim());
    });

    // Call function to refresh view based on updated filters
    refmorefilterview(fitterremovedata, refdocdetialsUrl, moredatefittervale);
});

var fitterremovedata = [];
    function ticketFilterAjax(sec_date_type,url){
        $.ajax({
                url: url,
                type: "GET",
                data: {
                    sec_date_type: sec_date_type,
                    // moredatefittervale:moredatefittervale,
                },
                success: function (responseData) {
                    handleSuccess(responseData);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
          });
    }

$('#ref_mrd_views').on('input', function () {
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
        $("#ref_zone_views").val(),
        $('#ref_loc_views').val(),
        $('#ref_mrd_views').val()
    ];

    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    refmorefilterview(fitterremovedata,refdocdetialsUrl,moredatefittervale, ph_id);
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
        url: refformsave_data,
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
    refundsavefilterview(fitterremovedata,refformsave_data,moredatefittervale);
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
      refundsavefilterview(fitterremovedata,refformsave_data,moredatefittervale);
});
$(document).on("click", ".clear_my_saveviews", function () {
    fitterremovedata.length = 0;
    $('.savedatasearch').val("");
    $(".my_savevalue_views").text("");
    $('.clear_my_saveviews').hide();
    $(".my_search_saveview").hide();
    $(".value_save_mysearch").text("");
    refundsaveformdata();
});

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

   refundsavefilterview(fitterremovedata,refformsave_data,moredatefittervale, phid);
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
    // alert(fitterremovedata);
    refundsavefilterview(fitterremovedata,refformsave_data,moredatefittervale, phid);
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
function refundsaveformdata() {
      startLoader2();
        $("#sveddata_tbl").hide();
		var moredatefittervale = $('#mydateallviewssave').text();
		$(".value_save_mysearch").text("");
		$.ajax({
			url: refformsave_data,
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
     function refundsavefilterview(uniqueResults,url,moredatefittervale, ph_id = '') {
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

    let zone = user.zone_name || user.zone || 'N/A';
    let location = user.location_name || user.branch || 'N/A';
    let auth_by = user.ref_auth_by || "-";
    let approved_by = user.ref_approved_by || '-';
    let brno = user.ref_branch_no || '-';
    let treatmentcat = user.ref_service_name || '-';
    let totalbil = user.ref_total_bill || '-';
    let expected_request = user.ref_expected_request || '-';
    let form_status = user.ref_form_status || '-';
    let patient_ph = user.ref_patient_ph || user.mobile || '-';
    let counselled_by = user.ref_counselled_by || user.consultingdr_name || '-';
    let finalamount = user.ref_final_auth || '-';
    let locationid = user.locationid || user.ref_zone_id || '-';

    // Build table row
    body += '<tr onclick="rowClick(event)">' +
        '<td class="tdview" id="idfetch" data-id=""><strong>#' + (index + 1) + '</strong></td>' +
        '<td class="tdview locationname" data-id="' + locationid + '">' + zone + '</td>' +
        '<td class="tdview branchname">' + location + '</td>' +
        '<td class="tdview" data-ph_id="' + user.ref_wife_mrd_no + '"><a href="#">' + user.ref_wife_mrd_no + '<br>' + user.ref_wife_name + '</a></td>' +
        '<td class="tdview" data-ph_id="' + user.ref_husband_mrd_no + '"><a href="#">' + user.ref_husband_mrd_no + '<br>' + user.ref_husband_name + '</a></td>' +
        '<td class="tdview treatmentcat">' + treatmentcat + '</td>' +
        '<td class="tdview totalbil">' + totalbil +
        '<td class="tdview">' + expected_request + ' (' + form_status + ')</td>' +
        '<td class="tdview expamount" style="display:none;">' + expected_request + '</td>' +
        '<td class="tdview requestfor" style="display:none;">' + form_status + '</td>' +
        '<td class="tdview mobileno" style="display:none;">' + patient_ph + '</td>' +
        '<td class="tdview consultby">' + counselled_by + '</td>' +
        '<td class="tdview authby">' + auth_by + '</td>' +
        '<td class="tdview finalapprove">' + approved_by + '</td>' +
        '<td class="tdview brno">' + brno + '</td>' +
        '<td class="tdview finalamount">' + finalamount + '</td>' +
          '<td class="tdview wifemrdno" style="display:none;" >' + user.ref_wife_mrd_no +'</td>' +
        '<td class="tdview husmrdno" style="display:none;" >' + user.ref_husband_mrd_no +'</td>' +
         '<td class="tdview wife_sign" style="display:none;">' + user.dis_wife_sign + '</td>' +
          '<td class="tdview husband_sign" style="display:none;">' + user.dis_husband_sign + '</td>' +
           '<td class="tdview drsign" style="display:none;">' + user.dis_drsign + '</td>' +
            '<td class="tdview cc_sign" style="display:none;">' + user.dis_cc_sign + '</td>' +
             '<td class="tdview admin_sign" style="display:none;">' + user.dis_admin_sign + '</td>' +
        '<td class="tdview" style="padding:3px;" id="savebtn">' +
            // '<button class="btn btn-primary tabledatasave" style="padding: 5px;border-radius: 5px;font-size: 12px;margin-left: 5px;">save</button>' +
            '<img src="../assets/images/print.png" style="width: 23px;" alt="Icon" class="icon documentclk">' +
            // '<a href="#" style="margin-left: 5px;"><img src="../assets/images/edit.png" style="width: 23px;margin-right:5px;" alt="Icon" class="icon edit_dis_details"></a>' +
        '</td>' +
        '</tr>';
});

    if (pageData.length === 0) {
			body += '<tr><td colspan="15" class="tdview" style="text-align: center;">No data available</td></tr>';
		}

    $("#sveddata_tbl").html(body);
    $("#billsavecounts").text(totalItems);
}
