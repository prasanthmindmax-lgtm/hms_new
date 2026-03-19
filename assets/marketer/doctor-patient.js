$(document).ready(function(){
    $(".search_view_patient").hide();
    overall_patient_getsdata();
    $("#patient-doctorname").on("change",function(){
        var selectedOption = $(this).find(":selected");
        var dataId = selectedOption.data("id");
        var meetingvalue = dataId;
        overall_patient(meetingvalue);
    });
    // Handle items per page change
    $('#itemsPerPageSelect2').change(function() {
        var pageSize = parseInt($(this).val());
        renderPaginationpatient(dataSourcenew, pageSize);
        renderTablepatient(dataSourcenew, pageSize, 1);  // Initially show the first page
    });
    var patientinsertvalue = [];
    $("#submit-doctor-patient").on("click",function(){
        var resultsArray = [];
        $(".patientschedule").each(function () {
            var value = $(this).val();
    // Check if the value is not empty before processing
    let alertShown = false; // Flag to track if the alert has been displayed
    if (value === "" && !alertShown) {
        alert("Please fill the empty data");
        alertShown = true; // Set the flag to true after showing the alert
        return;
    }
    var results = $(this).attr('name') + "='" + value + "'";
    resultsArray.push(results);
       patientinsertvalue=resultsArray;
        });
        patientinsertvalueall(patientinsertvalue);
    });
// meeting datefitter options
            var fitterremovedata = []; // Keep this variable persistent
            $(document).on('click', '.patient_range, .patient_btn', function() {
            // Check if the click happened on a specific class
            if ($(this).hasClass('patient_range')) {
            var datefilltervalue = $('#patientviews').text(); // Get the current text value when '.applyBtn' is clicked
                var morefitterempty=$(".value_patient").text();
                if(morefitterempty=='')
                {
                    patientdatefillterrange(datefilltervalue);
                }
                else
                {
                    dateandoveralldata_patient(fitterremovedata,datefilltervalue);
                }
            } else if ($(this).hasClass('patient_btn')) {
                var datefilltervaluenew = $('.patient_slct').text(); // Get the current text value when '.applyBtn' is clicked
                var dateRange = datefilltervaluenew.split(' - ');
                function convertDateFormat(dateStr) {
                    let parts = dateStr.split('/');
                    return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
                }
                var startDate = convertDateFormat(dateRange[0]);
                var endDate = convertDateFormat(dateRange[1]);
                var datefilltervalue = `${startDate} - ${endDate}`;
                var morefitterempty=$(".value_patient").text();
                if(morefitterempty=='')
                {
                    patientdatefillterrange(datefilltervalue);
                }
                else
                {
                    dateandoveralldata_patient(fitterremovedata,datefilltervalue);
                }
            }
            });
              // More fitter search.....
       var moreFilterValues = [];
       $("#patientfitter_search").on("click", function () {
           moredatefittervale=$("#patientviews").text();
           $('.clear_all_views_patient').show();
           $(".search_view_patient").show();
           var resultsArray = [];
           $(".morefittersclr_patient").each(function () {
               var value = $(this).val();
       // Check if the value is not empty before processing
       if (value === "") {
           return; // Skip this iteration if the value is empty
       }
       var results = $(this).attr('name') + "='" + value + "'";
       resultsArray.push(results);
          fitterremovedata=resultsArray;
           });
          morefilterview_patient(fitterremovedata,moredatefittervale);
           var moreFilterValues = [
               $("#doctor_name_patient").val(),
               $("#employee_more_patient").val(),
               $("#special_more_patient").val(),
               $("#city_more_patient").val(),
               $("#hospital_more_patient").val()
           ];
           $(".value_patient").each(function (index) {
               var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
               $(this).text(morefillterdata);
           });
       });
       $(document).on("click", ".value_patient", function () {
        var morefillterremvedata = $(this).text();
        var datefilltervalue = $('#patientviews').text();
        $(this).text("");
            $('input[type="checkbox"]').each(function() {
                if (morefillterremvedata.includes($(this).val())) {
                    $(this).prop('checked', false); // Uncheck the checkbox
                }
            });
        $('.morefittersclr_patient').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.endsWith(morefillterremvedata + "'");
    });
    const checkboxes = document.querySelectorAll('.multiselect-options input[type="checkbox"]');
          morefilltersremoveviews_patient(fitterremovedata,datefilltervalue);
    });
    $(document).on("click", ".clear_all_views_patient", function () {
        $('.morefittersclr_patient').val("")
        $('.clear_all_views_patient').hide();
        $(".search_view_patient").hide();
        $(".value_patient").text("");
        var datefilltervalue = $('#patientviews').text();
        cleardatemore_patient(datefilltervalue);
    });
    $(".mainclearall_patient").on("click",function(){
        $('.morefittersclr_patient').val("")
        $('.clear_all_views_patient').hide();
        $(".search_view_patient").hide();
        $(".value_patient").text("");
        $('#special_more_patient').prop('checked', false);
        $('#special_more_patient').val('');
        var datefilltervalue = $('#patientviews').text();
        cleardatemore_patient(datefilltervalue);
    });
        $(document).on("click", ".notesviews", function () {
            var row = $(this).closest('tr');
            var idsview = row.find('#ids').data('id');
            var wifenmes = row.find('#wifename').data('wife');
            var husbandname = row.find('#husbandname').data('husband');
            var docnames = row.find('#doctornames').data('name');
            var employeenames = row.find('#employeenames').data('emp');
            var notesfeedbacks = row.find('#notesfeed').data('notes');
            $("#notesid").text(idsview);
            $("#wifenames").text("Wife Name : " + wifenmes);
            $("#husbandnames").text("Husband Name : " + husbandname);
            $("#doctor_names").text("Doctorname : " + docnames);
            $("#empname_views_all").text("Marketer Name: " + employeenames);
            $("#notesfeedback").text(notesfeedbacks);
        });
});
function overall_patient(meetingvalue)
{
    $.ajax({
        url: patientfetch,
        type: "GET",
        data: {
            meetingvalue: meetingvalue,
        },
        success: function (responseData) {
            dataSource = responseData;
            employeename="";
            specialname="";
            hopsital_name="";
            address="";
            city="";
            doc_contact="";
            hpl_contact="";
            userids="";
            $.each(dataSource, function(index, user) {
                userids=user.id;
              employeename=user.empolyee_name;
              specialname=user.special;
              hopsital_name=user.hopsital_name;
              address=user.address;
              city=user.city;
              doc_contact=user.doc_contact;
              hpl_contact=user.hpl_contact;
            });
            $("#patient_emp_name").val(employeename);
            $("#patient_special_name").val(specialname);
            $("#patient_hops_name").val(hopsital_name);
            $("#patient_address_name").val(address);
            $("#patient_city_name").val(city);
            $("#patient_doc_contacts").val(doc_contact);
            $("#patient_hpl_contacts").val(hpl_contact);
            $("#ref_patient_id").val(userids);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function patientinsertvalueall(patientinsertvalue)
{
    $.ajax({
        url: patientinsert,
        type: "POST",
        data: {
            patientinsertvalue: patientinsertvalue,
        },
        success: function (response) {
            $('#exampleModal3').modal('hide');
            $('#exampleModal3').find('input, textarea, select').val('');
            overall_patient_getsdata();
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
var dataSourcenew = [];  // Data will be fetched here
function overall_patient_getsdata()
{
    $.ajax({
        url: patientviews,
        type: "GET",
        success: function (responseData) {
            //console.log(responseData);
            dataSourcenew = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect2').val()); // Get selected items per page
            renderPaginationpatient(dataSourcenew, pageSize);
            renderTablepatient(dataSourcenew, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function patientdatefillterrange(datefilltervalue)
{
   $.ajax({
        url: patientdatefitter,
        type: "GET",
        data: {
            datefilltervalue: datefilltervalue,
        },
        success: function (responseData) {
           //console.log(responseData);
           dataSourcenew = responseData;
           totalItems = responseData.length; // Get total items count // Set the data fetched from the server
           var pageSize = parseInt($('#itemsPerPageSelect2').val()); // Get selected items per page
           renderPaginationpatient(dataSourcenew, pageSize);
           renderTablepatient(dataSourcenew, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}
function morefilterview_patient(fitterremovedata,moredatefittervale)
{
    $.ajax({
        url: patientmorefitter,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            moredatefittervale:moredatefittervale,
        },
        success: function (responseData) {
           //console.log(responseData);
           dataSourcenew = responseData;
           totalItems = responseData.length; // Get total items count // Set the data fetched from the server
           var pageSize = parseInt($('#itemsPerPageSelect2').val()); // Get selected items per page
           renderPaginationpatient(dataSourcenew, pageSize);
           renderTablepatient(dataSourcenew, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}
function morefilltersremoveviews_patient(fitterremovedata,datefilltervalue)
{
    if(fitterremovedata!=''){
        $.ajax({
            url: patientremovefitter,
            type: "GET",
            data: {
                fitterremovedata: fitterremovedata,
                datefilltervalue:datefilltervalue,
            },
            success: function (responseData) {
                dataSourcenew = responseData;
                totalItems = responseData.length; // Get total items count // Set the data fetched from the server
                var pageSize = parseInt($('#itemsPerPageSelect2').val()); // Get selected items per page
                renderPaginationpatient(dataSourcenew, pageSize);
                renderTablepatient(dataSourcenew, pageSize, 1); // Show first page initially
            },
            error: function (xhr, status, error) {
                $("#doctor_details1").hide();
                console.error("AJAX Error:", status, error);
            }
          });
        }
        else
        {
            $('.clear_all_views_patient').hide();
            $(".search_view_patient").hide();
          cleardatemore_patient(datefilltervalue);
        }
}
function cleardatemore_patient(datefilltervalue)
{
    $.ajax({
        url: patientclrfitter,
        type: "GET",
        data: {
            datefilltervalue: datefilltervalue,
        },
        success: function (responseData) {
            dataSourcenew = responseData;
                totalItems = responseData.length; // Get total items count // Set the data fetched from the server
                var pageSize = parseInt($('#itemsPerPageSelect2').val()); // Get selected items per page
                renderPaginationpatient(dataSourcenew, pageSize);
                renderTablepatient(dataSourcenew, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
      });
}
function dateandoveralldata_patient(fitterremovedata,datefilltervalue)
{
    $.ajax({
        url: patientdateandfitter,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            datefilltervalue:datefilltervalue,
        },
        success: function (responseData) {
            dataSourcenew = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect2').val()); // Get selected items per page
            renderPaginationpatient(dataSourcenew, pageSize);
            renderTablepatient(dataSourcenew, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
      });
}
// Render table rows based on the page and page size
function renderTablepatient(data, pageSize, pageNum) {
    var startIdx = (pageNum - 1) * pageSize;
    var endIdx = pageNum * pageSize;
    var pageData = data.slice(startIdx, endIdx);
    var bodynew = "";
    let patient_mar_count=0;
    $.each(pageData, function(index, user) {
        patient_mar_count++;
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY | HH:MM");
        const wifeContact = String(user.wife_contact);
        var wifeContactview = wifeContact.substring(0, 3);
        const husContact = String(user.husband_contact);
        var husbandcontact = husContact.substring(0, 3);
        bodynew += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="ids"  data-id="' + user.id + '" data-date="' + formattedDate + '"> <strong>#' + user.id + ' - refid #'+ user.ref_doctor_id +'</strong><br> MRN :' + user.mrn_number + '<br></td>' +
                '<td class="tdview" id="wifename" data-wife="' + user.wifename + '" data-special="' + user.special + '">' + user.wifename + '<br>' + wifeContactview + 'XXXXXX<br></td>' +
                '<td class="tdview" id="notesfeed" data-notes="' + user.notes + '">' + formattedDate + '<br><br></td>' +
                '<td class="tdview" id="husbandname" data-husband="' + user.husband_name + '" data-hptnum="' + user.hpl_contact + '">' + user.husband_name + '<br> ' + husbandcontact + 'XXXXXX</td>' +
                '<td class="tdview" id="employeenames" data-emp="' + user.empolyee_name + '">Emp Name : ' + user.empolyee_name + '<br></td>' +
                '<td class="tdview" id="doctornames" data-name="' + user.doctor_name + '" data-special="' + user.special + '">Dr. ' + user.doctor_name + '<br>' + user.special + '<br></td>' +
                '<td class="tdview  value="' + user.hopsital_name + '">'+user.hopsital_name+'</td>' +
                '<td class="tdview notesviews" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filternotes"><img src="../assets/images/note_all.png" style="width: 25px;cursor: pointer;"  alt="Icon" class="icon"></td>' +
                '</tr>';  });
    $("#patient_details").html(bodynew);
    $("#patient_totals").text(patient_mar_count);
}
// Render pagination controls based on data
function renderPaginationpatient(data, pageSize) {
    var totalPages = Math.ceil(data.length / pageSize);
    var paginationHtml = '';
    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="page-btn2 " style="background-color:#b163a6;"  data-pagepatient="' + i + '">' + i + '</button>';
    }
    $('#pagination2').html(paginationHtml);
    // Bind click event to each pagination button
    $('.page-btn2').click(function() {
        var pageNum = $(this).data('pagepatient');
        $('.page-btn2').removeClass('active');
        $(this).addClass('active');
        renderTablepatient(data, pageSize, pageNum);
    });
}