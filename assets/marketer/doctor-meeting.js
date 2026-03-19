$(document).ready(function(){
    $('.ranges').addClass('doctor_range');
    $('.applyBtn').addClass('doctor_btn');
    $('.drp-selected').addClass('doctor_slct');
    $(".search_meeting").hide();
    $(".add-meeting").hide();
    $(".add-patient").hide();
    $("#analytics-tab-2").on("click",function(){
        $(".add-doctors").hide();
        $(".add-meeting").show();
        $(".add-patient").hide();
        $('.ranges').addClass('meeting_range');
        $('.applyBtn').addClass('meeting_btn');
        $('.drp-selected').addClass('meeting_slct');
        $('.ranges').removeClass('patient_range');
        $('.applyBtn').removeClass('patient_btn');
        $('.drp-selected').removeClass('patient_slct');
        $('.ranges').removeClass('doctor_range');
        $('.applyBtn').removeClass('doctor_btn');
        $('.drp-selected').removeClass('doctor_slct');
    });
    $("#analytics-tab-1").on("click",function(){
        $(".add-doctors").show();
        $(".add-meeting").hide();
        $(".add-patient").hide();
        $('.ranges').addClass('doctor_range');
        $('.applyBtn').addClass('doctor_btn');
        $('.drp-selected').addClass('doctor_slct');
        $('.ranges').removeClass('meeting_range');
        $('.applyBtn').removeClass('meeting_btn');
        $('.drp-selected').removeClass('meeting_slct');
        $('.ranges').removeClass('patient_range');
        $('.applyBtn').removeClass('patient_btn');
        $('.drp-selected').removeClass('patient_slct');
    });
    $("#analytics-tab-3").on("click",function(){
        $(".add-doctors").hide();
        $(".add-meeting").hide();
        $(".add-patient").show();
        $('.ranges').removeClass('meeting_range');
        $('.applyBtn').removeClass('meeting_btn');
        $('.drp-selected').removeClass('meeting_slct');
        $('.ranges').addClass('patient_range');
        $('.applyBtn').addClass('patient_btn');
        $('.drp-selected').addClass('patient_slct');
        $('.ranges').removeClass('doctor_range');
        $('.applyBtn').removeClass('doctor_btn');
        $('.drp-selected').removeClass('doctor_slct');
    });
     // Handle items per page change
     $('#itemsPerPageSelect1').change(function() {
        var pageSizemeetingmark = parseInt($(this).val());
        renderPaginationmeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark);
        renderTablemeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark, 1);  // Initially show the first page
    });
    $("#meeting-doctorname").on("change",function(){
        var selectedOption = $(this).find(":selected");
        var dataId = selectedOption.data("id");
        var meetingvalue = dataId;
        overall_meeting(meetingvalue);
    });
    var meetinginsertvalue = [];
    $("#submit-doctor-meetings").on("click",function(){
        var resultsArray = [];
        feedbacks=$("#additional-notes").val();
        if(feedbacks=='')
        {
            $('.error_feedbackss').text('Please enter the feedback');
            isValid = false;
        }
        else{
        $(".meetingschedule").each(function () {
            var value = $(this).val();
    var results = $(this).attr('name') + "='" + value + "'";
    resultsArray.push(results);
       meetinginsertvalue=resultsArray;
        });
        meetinginsertvalueall(meetinginsertvalue);
    }
    });
    overallmeetingsviews();
    // meeting datefitter options
    var fitterremovedata = []; // Keep this variable persistent
        $(document).on('click', '.meeting_range, .meeting_btn', function() {
        // Check if the click happened on a specific class
        if ($(this).hasClass('meeting_range')) {
           var datefilltervalue = $('#meetingdatefitter').text(); // Get the current text value when '.applyBtn' is clicked
            var morefitterempty=$(".meeting_views").text();
            if(morefitterempty=='')
            {
              meetingdatefillterrange(datefilltervalue);
            }
            else
            {
                dateandoveralldata_meeting(fitterremovedata,datefilltervalue);
            }
        } else if ($(this).hasClass('meeting_btn')) {
            var datefilltervaluenew = $('.meeting_slct').text(); // Get the current text value when '.applyBtn' is clicked
            var dateRange = datefilltervaluenew.split(' - ');
            function convertDateFormat(dateStr) {
                let parts = dateStr.split('/');
                return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            var datefilltervalue = `${startDate} - ${endDate}`;
            var morefitterempty=$(".meeting_views").text();
            if(morefitterempty=='')
            {
            meetingdatefillterrange(datefilltervalue);
            }
            else
            {
                dateandoveralldata_meeting(fitterremovedata,datefilltervalue);
            }
        }
    });
       // More fitter search.....
       var moreFilterValues = [];
       $("#meetingfitter_search").on("click", function () {
           moredatefittervale=$("#meetingdatefitter").text();
           $('.clear_all_meeting').show();
           $(".search_meeting").show();
           var resultsArray = [];
           $(".morefittersclr_meeting").each(function () {
               var value = $(this).val();
       // Check if the value is not empty before processing
       if (value === "") {
           return; // Skip this iteration if the value is empty
       }
       var results = $(this).attr('name') + "='" + value + "'";
       resultsArray.push(results);
          fitterremovedata=resultsArray;
           });
           morefilterview_meeting(fitterremovedata,moredatefittervale);
           var moreFilterValues = [
               $("#doctor_name_meeting").val(),
               $("#employee_more_meeting").val(),
               $("#special_more_meeting").val(),
               $("#city_more_meeting").val(),
               $("#hospital_more_meeting").val()
           ];
           $(".meeting_views").each(function (index) {
               var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
               $(this).text(morefillterdata);
           });
       });
       $(document).on("click", ".meeting_views", function () {
        var morefillterremvedata = $(this).text();
        var datefilltervalue = $('#meetingdatefitter').text();
        $(this).text("");
            $('input[type="checkbox"]').each(function() {
                if (morefillterremvedata.includes($(this).val())) {
                    $(this).prop('checked', false); // Uncheck the checkbox
                }
            });
        $('.morefittersclr_meeting').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.endsWith(morefillterremvedata + "'");
    });
    const checkboxes = document.querySelectorAll('.multiselect-options input[type="checkbox"]');
          morefilltersremoveviews_meetings(fitterremovedata,datefilltervalue);
    });
    $(document).on("click", ".clear_all_meeting", function () {
        $('.morefittersclr_meeting').val("")
        $('.clear_all_meeting').hide();
        $(".search_meeting").hide();
        $(".meeting_views").text("");
        var datefilltervalue = $('#meetingdatefitter').text();
        cleardatemore_meeting(datefilltervalue);
    });
    $(".mainclearall_meeting").on("click",function(){
        $('.morefittersclr_meeting').val("")
        $('.clear_all_meeting').hide();
        $(".search_meeting").hide();
        $(".meeting_views").text("");
        $('#special_more_meeting').prop('checked', false);
        $('#special_more_meeting').val('');
        var datefilltervalue = $('#meetingdatefitter').text();
        cleardatemore_meeting(datefilltervalue);
    });
    $(document).on("click", ".feedbackclk", function () {
        var row = $(this).closest('tr');
        var idsfeed = row.find('#meetids').data('id');
        var docnames = row.find('#doc_meet').data('name');
        var emp_meet = row.find('#emp_meet').data('emps');
        var meetfeed = row.find('#feedback_meet').data('feed');
        $("#feedbackid").text(idsfeed);
        $("#doctor_names_feed").text("Doctorname : " + docnames);
        $("#empname_views_all_feed").text("Marketer Name : " + emp_meet);
        $("#feedback_meetss").text(meetfeed);
    });
});
function overall_meeting(meetingvalue)
{
    $.ajax({
        url: meetingfetch,
        type: "GET",
        data: {
            meetingvalue: meetingvalue,
        },
        success: function (responseData) {
            $("#doctor_details1").hide();
            $("#doctor_details").show();
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
            $("#emp_name_meeting").val(employeename);
            $("#special_name").val(specialname);
            $("#hops_name").val(hopsital_name);
            $("#address_name").val(address);
            $("#city_name").val(city);
            $("#doc_contacts").val(doc_contact);
            $("#hpl_contacts").val(hpl_contact);
            $("#ref_doctor_id").val(userids);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function meetinginsertvalueall(meetinginsertvalue)
{
    $.ajax({
        url: meetinginsert,
        type: "POST",
        data: {
            meetinginsertvalue: meetinginsertvalue,
        },
        success: function (response) {
            $('#exampleModal2').modal('hide');
            $('#exampleModal2').find('input, textarea, select').val('');
            overallmeetingsviews();
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
var dataSourcenewmeetingmark = [];  // Data will be fetched here
function overallmeetingsviews()
{
    $.ajax({
        url: meetingviews,
        type: "GET",
        success: function (responseData) {
            //console.log(responseData);
            dataSourcenewmeetingmark = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetingmark = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark);
            renderTablemeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function meetingdatefillterrange(datefilltervalue)
{
    //alert(datefilltervalue);
    $.ajax({
        url: meetingdatefitter,
        type: "GET",
        data: {
            datefilltervalue: datefilltervalue,
        },
        success: function (responseData) {
           //console.log(responseData);
           dataSourcenewmeetingmark = responseData;
           totalItems = responseData.length; // Get total items count // Set the data fetched from the server
           var pageSizemeetingmark = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
           renderPaginationmeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark);
           renderTablemeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}
function morefilterview_meeting(fitterremovedata,moredatefittervale) {
    $.ajax({
        url: meetingmorefitter,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            moredatefittervale:moredatefittervale,
        },
        success: function (responseData) {
            dataSourcenewmeetingmark = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetingmark = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark);
            renderTablemeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}
function morefilltersremoveviews_meetings(fitterremovedata,datefilltervalue)
{
    if(fitterremovedata!=''){
    $.ajax({
        url: meetingremovefitter,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            datefilltervalue:datefilltervalue,
        },
        success: function (responseData) {
            dataSourcenewmeetingmark = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetingmark = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark);
            renderTablemeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
      });
    }
    else
    {
        $('.clear_all_meeting').hide();
        $(".search_meeting").hide();
      cleardatemore_meeting(datefilltervalue);
    }
}
function cleardatemore_meeting(datefilltervalue)
{
    $.ajax({
        url: meetingclrfitter,
        type: "GET",
        data: {
            datefilltervalue: datefilltervalue,
        },
        success: function (responseData) {
            dataSourcenewmeetingmark = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetingmark = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark);
            renderTablemeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
      });
}
function dateandoveralldata_meeting(fitterremovedata,datefilltervalue)
{
    $.ajax({
        url: meetingdateandfitter,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            datefilltervalue:datefilltervalue,
        },
        success: function (responseData) {
            dataSourcenewmeetingmark = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetingmark = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark);
            renderTablemeetingmark(dataSourcenewmeetingmark, pageSizemeetingmark, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
      });
}
// Render table rows based on the page and page size
function renderTablemeetingmark(data, pageSizemeetingmark, pageNum) {
    var startIdx = (pageNum - 1) * pageSizemeetingmark;
    var endIdx = pageNum * pageSizemeetingmark;
    var pageData = data.slice(startIdx, endIdx);
    // console.log(pageData);
    var bodynew = "";
    let meeting_mark_count=data.length;
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY | HH:MM");
        const hplContact = String(user.hpl_contact);
        var hospitacontact = hplContact.substring(0, 3);
        const docContact = String(user.doc_contact);
        var doctorcontact = docContact.substring(0, 3);
        bodynew += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="meetids" data-id="' + user.id + '" data-date="' + formattedDate + '"> <strong>#' + user.id + ' - refid #'+ user.ref_doctor_id +'</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview" id="doc_meet" data-name="' + user.doctor_name + '" data-special="' + user.special + '">Dr. ' + user.doctor_name + '<br>' + user.special + '<br></td>' +
                '<td class="tdview"  id="feedback_meet" data-feed="' + user.meeting_feedback + '" data-citys="' + user.city + '">' + user.hopsital_name + '<br>' + user.city + '<br></td>' +
                '<td class="tdview"  data-docnum="' + doctorcontact + 'XXXXX" data-hptnum="' + hospitacontact + 'XXXXXX">Doctor Num :' + doctorcontact + 'XXXXXX<br> Hospital Num :' + hospitacontact + 'XXXXX</td>' +
                '<td class="tdview" id="emp_meet" data-emps="' + user.empolyee_name + '">Emp Name : ' + user.empolyee_name + '</td>' +
                '<td class="tdview feedbackclk" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filterfeedback"><img src="../assets/images/note_all.png" style="width: 25px;cursor: pointer;"  alt="Icon" class="icon"></td>' +
                '</tr>';  });
    $("#meetingdetails").html(bodynew);
    $("#total_meeting").text(meeting_mark_count);
   $("#markmeetingcunt").text(meeting_mark_count);
}
// Render pagination controls based on data
function renderPaginationmeetingmark(data, pageSizemeetingmark) {
    var totalPages = Math.ceil(data.length / pageSizemeetingmark);
    var paginationHtml = '';
    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="page-btn1 " style="background-color:#b163a6;"  data-pagemeeting="' + i + '">' + i + '</button>';
    }
    $('#pagination1').html(paginationHtml);
    // Bind click event to each pagination button
    $('.page-btn1').click(function() {
        var pageNum = $(this).data('pagemeeting');
        $('.page-btn1').removeClass('active');
        $(this).addClass('active');
        renderTablemeetingmark(data, pageSizemeetingmark, pageNum);
    });
}