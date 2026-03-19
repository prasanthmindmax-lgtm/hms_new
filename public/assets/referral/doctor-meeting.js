function formatDateForBackend(dateStr) {
    const date = new Date(dateStr);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}
 // More fitter search.....
    var moreFilterValues = [];

function date1(date){
            moredatefittervale = date;

            $('#meetingfitter_search').trigger('click');
        }
function filterMeetings1(startDate, endDate) {
    const formattedStart = formatDateForBackend(startDate);
    const formattedEnd = formatDateForBackend(endDate);
    const datefiltervalue = `${formattedStart} - ${formattedEnd}`;
    
    $('#selectedMeetingDateRange').text(`${formattedStart} - ${formattedEnd}`);
    $('#meetingdatefitter').text(datefiltervalue);
    
    date1(datefiltervalue);
    
}


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
        var pageSizemeetings = parseInt($(this).val());
        renderPaginationmeeting(dataSourcenewmeetings, pageSizemeetings);
        renderTablemeeting(dataSourcenewmeetings, pageSizemeetings, 1);  // Initially show the first page
    });

      $(document).on("click", ".meeting-doctorname div", function () {
    let selectedText = $(this).text();
    let selectedId = $(this).data("value"); 
    $("#meeting-doctorname").val(selectedText);
    overall_meeting(selectedId);
});

    $("#meeting-doctorname").on("change",function(){
        var selectedOption = $(this).find(":selected");
        var dataId = selectedOption.data("id");
        var meetingvalue = dataId;
        overall_meeting(meetingvalue);
    });


    var meetinginsertvalue = [];
    var marketersearchvalue_meeting=[];

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
 var fileInput = $('#meeting_image')[0];
     if (fileInput && fileInput.files && fileInput.files.length > 0) {
        var file = fileInput.files[0];
        var fileData = new FormData();
        fileData.append("meeting_image", file);
        fileData.append("_token", $('meta[name="csrf-token"]').attr("content")); 

      
        $.ajax({
            url:"/hms/superadmin/upload-meeting-image",
            method: "POST",
            data: fileData,
            processData: false,
            contentType: false,
            success: function (response) {
                resultsArray.push("meeting_image='" + response.filename + "'");
                meetinginsertvalueall(resultsArray);
            },
            error: function () {
                alert("File upload failed.");
            }
        });
    }
        
    //    meetinginsertvalueall(resultsArray);

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
            var morefitterempty_market_meeting=$(".value_meeting_mainsearch").text();

            if (morefitterempty !== '' || morefitterempty_market_meeting !== '') {

                fitterremovedatafitters = (morefitterempty === '' || morefitterempty_market_meeting === '') ? fitterremovedata: [...new Set(fitterremovedata.concat(marketersearchvalue_meeting))];

                dateandoveralldata_meeting(fitterremovedatafitters, datefilltervalue);

            } else {

                meetingdatefillterrange(datefilltervalue);
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
            var morefitterempty_market_meeting=$(".value_meeting_mainsearch").text();

            if (morefitterempty !== '' || morefitterempty_market_meeting !== '') {

                fitterremovedatafitters = (morefitterempty === '' || morefitterempty_market_meeting === '') ? fitterremovedata: [...new Set(fitterremovedata.concat(marketersearchvalue_meeting))];

                dateandoveralldata_meeting(fitterremovedatafitters, datefilltervalue);

            } else {

                meetingdatefillterrange(datefilltervalue);
            }
        }
    });
       // More fitter search.....
       var moreFilterValues = [];
       $("#meetingfitter_search").on("click", function () {
        //    moredatefittervale=$("#meetingdatefitter").text();
           var mainsearchfetch=$(".value_meeting_mainsearch").text();
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

          fitterremovedata = mainsearchfetch === ''? resultsArray: resultsArray.concat(marketersearchvalue_meeting);
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

       $(document).on('click', '.meeting_views,.value_meeting_mainsearch', function() {
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

        $('.marketervalues_search_meeting').filter(function () {
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

    $(document).on('click', '.imagecheck', function (e) {
        $('#exampleModal1').modal('show');
        var userId = $(this).attr('value');
        // Find the corresponding row
        var row = $(this).closest('tr');
        // Get data from the row
        var address = row.find('#address_view').data('address');
        var docname = row.find('#doc_fetch').data('name');
        var special = row.find('#doc_fetch').data('special');
        var doccontact = row.find('#contact_details').data('docnum');
        var hoscontact = row.find('#contact_details').data('hptnum');
        var dateviews = row.find('#dateviews').data('date');
        var employeenames = row.find('#employeename').data('emp');
        var maplink = row.find('#address_view').data('map');
        var ids = row.find('#dateviews').data('id');
        var hosptalname = row.find('#cityviews').data('hospital');
        var cityviews = row.find('#cityviews').data('citys');
        var imageviews = row.find('#imageviews').text().trim(); // Get and trim the text content
        // Remove unwanted characters like " ] [" if present
        imageviews = imageviews.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        // Split the cleaned string into an array of image paths
        var firstImage = imageviews.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        // alert(imageNamefirst);
        $('#main').attr('src', "../public/doctor_images/" + imageNamefirst);
        var imageArray = imageviews.split(',');
        // Extract the names of the images
        var views = '';
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<img src="../public/doctor_images/'+imageName+'">';
        });
         $('#thumbnails').html("");
        $('#thumbnails').html(views);
        var thumbnails = document.getElementById("thumbnails")
        var imgs = thumbnails.getElementsByTagName("img")
        var main = document.getElementById("main")
        var counter=0;
        for(let i=0;i<imgs.length;i++){
        let img=imgs[i]
        img.addEventListener("click",function(){
        main.src=this.src
        })
    }
        // Update modal content
        $("#Doctornamehead").text(docname + " (" + special + ")");
        $("#visit_date").text("Visit Date : " + dateviews);
        $(".empname_views_all").text("Marketer Name: " + employeenames);
        $("#docaddress").text(address);
        $("#doctor_ids").text(ids);
        $(".doctor_names").text("Name : " + docname + " (" + special + ")");
        $("#dcnum").text("Doctor Number : " + doccontact);
        $("#hpnum").text("Hospital Number : " + hoscontact);
        $("#maplocation").text("Map Link: " + maplink);
        $(".hosptalnames").text("Hospital Name : " +hosptalname);
        $(".cityviews").text("City : " + cityviews)
    });

    $(document).on('click', '.options_meeting div', function (e) {
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
        var moredatefittervale = $('#meetingdatefitter').text();

        $('.clear_all_meeting').show();
        $(".search_meeting").show();


        var resultsArray_marketer_meeting = [];
        $(".marketervalues_search_meeting").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer_meeting.push(results);

            var moreFilterValues_market_meeting = [
                $("#meeting_mark").val(),
                $("#meeting_brans").val(),
                $("#meeting_zonss").val(),
            ];

            $(".value_meeting_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market_meeting[index] ? moreFilterValues_market_meeting[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

                        });

                marketersearchvalue_meeting=resultsArray_marketer_meeting;
                fitterremovedata=resultsArray_marketer_meeting;
                morefilterview_meeting(fitterremovedata,moredatefittervale);

                //alert(marketersearchvalue);


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
              userfullname="";
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
               userfullname=user.userfullname;
              specialname=user.special;
              hopsital_name=user.hopsital_name;
              address=user.address;
              city=user.city;
              doc_contact=user.doc_contact;
              hpl_contact=user.hpl_contact;

            });
            $("#emp_name_meeting").val(employeename);
             $("#userfullname").val(userfullname);
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
              alert(response.message);
            overallmeetingsviews();
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
var dataSourcenewmeetings = [];  // Data will be fetched here

function overallmeetingsviews()
{

         console.log("meeting_views:", meetingviews);
    $.ajax({
        url: meetingviews,
        type: "GET",
        success: function (responseData) {
            //console.log(responseData);
            dataSourcenewmeetings = responseData;
            totalItemsmeetings = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetings = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeeting(dataSourcenewmeetings, pageSizemeetings);
            renderTablemeeting(dataSourcenewmeetings, pageSizemeetings, 1); // Show first page initially
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
           dataSourcenewmeetings = responseData;
           totalItemsmeetings = responseData.length; // Get total items count // Set the data fetched from the server
           var pageSizemeetings = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
           renderPaginationmeeting(dataSourcenewmeetings, pageSizemeetings);
           renderTablemeeting(dataSourcenewmeetings, pageSizemeetings, 1); // Show first page initially
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
            dataSourcenewmeetings = responseData;
            totalItemsmeetings = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetings = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeeting(dataSourcenewmeetings, pageSizemeetings);
            renderTablemeeting(dataSourcenewmeetings, pageSizemeetings, 1); // Show first page initially
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
            dataSourcenewmeetings = responseData;
            totalItemsmeetings = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetings = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeeting(dataSourcenewmeetings, pageSizemeetings);
            renderTablemeeting(dataSourcenewmeetings, pageSizemeetings, 1); // Show first page initially
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
            dataSourcenewmeetings = responseData;
            totalItemsmeetings = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetings = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeeting(dataSourcenewmeetings, pageSizemeetings);
            renderTablemeeting(dataSourcenewmeetings, pageSizemeetings, 1); // Show first page initially
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
            dataSourcenewmeetings = responseData;
            totalItemsmeetings = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizemeetings = parseInt($('#itemsPerPageSelect1').val()); // Get selected items per page
            renderPaginationmeeting(dataSourcenewmeetings, pageSizemeetings);
            renderTablemeeting(dataSourcenewmeetings, pageSizemeetings, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
      });
}
// Render table rows based on the page and page size
function renderTablemeeting(data, pageSizemeetings, pageNum) {
    let count_meeting = data.length;
    var startIdx = (pageNum - 1) * pageSizemeetings;
    var endIdx = pageNum * pageSizemeetings;
    var pageData = data.slice(startIdx, endIdx);
     console.log("meetingvalue:", meetingfetch);
    var bodynew = "";
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY | HH:MM");
        const hplContact = String(user.hpl_contact);
        var hospitacontact = hplContact.substring(0, 3);
        const docContact = String(user.doc_contact);
        var doctorcontact = docContact.substring(0, 3);
        var limitAddress = user.address.substring(0, 30);
        bodynew += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="meetids" data-id="' + user.id + '" data-date="' + formattedDate + '"> <strong>#' + user.id + ' - refid #'+ user.ref_doctor_id +'</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview" id="doc_meet" data-name="' + user.doctor_name + '" data-special="' + user.special + '"><a href="#" class="imagecheck">Dr. ' + user.doctor_name + '</a><br>' + user.special + '<br></td>' +
                '<td class="tdview"  id="feedback_meet" data-feed="' + user.meeting_feedback + '" data-citys="' + user.city + '">' + user.hopsital_name + '<br>' + limitAddress + '...<br></td>' +
  '<td class="tdview" id="contact_details_meeting">' +
            'Doctor: <span class="masked-doc-meeting">' + user.doc_contact.slice(0, 2) + 'XXXXX' + user.doc_contact.slice(-2) + '</span>' +
            '<i class="fa fa-eye show-doctor-meeting" style="cursor: pointer; margin-left:5px; color: #1976d2;" ' +
            'data-doctor="' + user.doc_contact + '" title="Click to show/hide number"></i><br>' +
            'Hospital: <span class="masked-hpt-meeting">' + user.hpl_contact.slice(0, 2) + 'XXXXX' + user.hpl_contact.slice(-2) + '</span>' +
            '<i class="fa fa-eye show-hospital-meeting" style="cursor: pointer; margin-left:5px; color: #1976d2;" ' +
            'data-hospital="' + user.hpl_contact + '" title="Click to show/hide number"></i>' +
            '</td>' +
'<td class="tdview" id="emp_meet" data-emps="' + user.employee_name + '">' + user.userfullname + '-' + user.empolyee_name + '</td>' +                '<td class="tdview feedbackclk" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filterfeedback"><img src="../assets/images/note_all.png" style="width: 20px;cursor: pointer;"  alt="Icon" class="icon"></td>' +
                '<td style="display:none" class="tdview" id="imageviews">' + user.image_paths + '</td>' +
                '</tr>'; 
             });
    $("#meetingdetails").html(bodynew);
    $("#total_meeting").text(count_meeting);
    $("#counts1").text(count_meeting);
}

$(document).on('click', '.show-doctor-meeting, .show-hospital-meeting', function(e) {
    e.stopPropagation();
    const $icon = $(this);
    const isDoctor = $icon.hasClass('show-doctor-meeting');
    
    // Convert to string explicitly and trim whitespace
    const fullNumber = String($icon.data(isDoctor ? 'doctor' : 'hospital')).trim();
    const $maskedSpan = $icon.prev();
    
    // Check if we have a valid number
    if (!fullNumber || fullNumber.length < 4) {
        console.error("Invalid phone number format");
        return;
    }
    
    // Toggle between masked and full number
    if ($icon.hasClass('active')) {
        // Show masked number (first 2 and last 2 digits)
        const maskedNumber = fullNumber.slice(0, 2) + 'XXXXX' + fullNumber.slice(-2);
        $maskedSpan.text(maskedNumber);
        $icon.removeClass('active').css('color', '#1976d2');
    } else {
        // Show full number
        $maskedSpan.text(fullNumber);
        $icon.addClass('active').css('color', '#ff5722');
    }
});

// Render pagination controls based on data
function renderPaginationmeeting(data, pageSizemeetings, currentPage = 1) {
    var totalPages = Math.ceil(data.length / pageSizemeetings);
    var paginationHtml = '';
    
    if (totalPages <= 1) {
        $('#pagination1').html('');
        return;
    }
    
    // Previous button
    if (currentPage > 1) {
        paginationHtml += '<button class="page-btn1 prev-btn" data-pagemeeting="' + (currentPage - 1) + '" style="background-color:#080fd399; margin-right: 5px;">❮ Prev</button>';
    }
    
    // First page
    paginationHtml += '<button class="page-btn1 ' + (currentPage === 1 ? 'active' : '') + '" data-pagemeeting="1" style="background-color:#080fd399;">1</button>';
    
    if (totalPages <= 5) {
        // If total pages <= 5, show all pages
        for (var i = 2; i <= totalPages; i++) {
            paginationHtml += '<button class="page-btn1 ' + (currentPage === i ? 'active' : '') + '" data-pagemeeting="' + i + '" style="background-color:#080fd399;">' + i + '</button>';
        }
    } else {
        // Show page range with ellipsis
        if (currentPage > 3) {
            paginationHtml += '<span class="pagination-dots" style="padding: 5px 10px;">...</span>';
        }
        
        // Show pages around current page
        var startPage = Math.max(2, currentPage - 1);
        var endPage = Math.min(totalPages - 1, currentPage + 1);
        
        // Adjust range if near start or end
        if (currentPage <= 3) {
            startPage = 2;
            endPage = Math.min(4, totalPages - 1);
        } else if (currentPage >= totalPages - 2) {
            startPage = Math.max(2, totalPages - 3);
            endPage = totalPages - 1;
        }
        
        for (var i = startPage; i <= endPage; i++) {
            paginationHtml += '<button class="page-btn1 ' + (currentPage === i ? 'active' : '') + '" data-pagemeeting="' + i + '" style="background-color:#080fd399;">' + i + '</button>';
        }
        
        if (currentPage < totalPages - 2) {
            paginationHtml += '<span class="pagination-dots" style="padding: 5px 10px;">...</span>';
        }
        
        // Last page
        paginationHtml += '<button class="page-btn1 ' + (currentPage === totalPages ? 'active' : '') + '" data-pagemeeting="' + totalPages + '" style="background-color:#080fd399;">' + totalPages + '</button>';
    }
    
    // Next button
    if (currentPage < totalPages) {
        paginationHtml += '<button class="page-btn1 next-btn" data-pagemeeting="' + (currentPage + 1) + '" style="background-color:#080fd399; margin-left: 5px;">Next ❯</button>';
    }
    
    $('#pagination1').html(paginationHtml);
    
    // Bind click event to each pagination button
    $('.page-btn1').click(function() {
        var pageNum = $(this).data('pagemeeting');
        renderTablemeeting(data, pageSizemeetings, pageNum);
        renderPaginationmeeting(data, pageSizemeetings, pageNum); // Re-render pagination with new current page
    });
}
