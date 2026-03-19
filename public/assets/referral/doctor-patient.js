function formatDateForBackend(dateStr) {
    const date = new Date(dateStr);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}
 // More fitter search.....
    var moreFilterValues = [];

function date2(date){
            moredatefittervale=date;
            $('#patientfitter_search').trigger('click');
        }
function applyPatientDateFilter(startDate, endDate) {
    const formattedStart = formatDateForBackend(startDate);
    const formattedEnd = formatDateForBackend(endDate);
    const datefiltervalue = `${formattedStart} - ${formattedEnd}`;
    
    $('#patientSelectedDateRange').text(`${formattedStart} - ${formattedEnd}`);
    $('#patientviews').text(datefiltervalue);
    
    date2(datefiltervalue);
    
}






$(document).ready(function(){
    $(".search_view_patient").hide();
    overall_patient_getsdata();
    $("#patient-doctorname").on("change",function(){
        var selectedOption = $(this).find(":selected");
        var dataId = selectedOption.data("id");
        var meetingvalue = dataId;
        overall_patient(meetingvalue);
    });

 $(document).on("click", ".patient-doctorname div", function () {
    let selectedText = $(this).text();
    let selectedId = $(this).data("value"); 
    $("#patient-doctorname").val(selectedText);
    overall_patient(selectedId);
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
            var marketersearchvalue_patient=[];
            $(document).on('click', '.patient_range, .patient_btn', function() {
            // Check if the click happened on a specific class
            if ($(this).hasClass('patient_range')) {
            var datefilltervalue = $('#patientviews').text(); // Get the current text value when '.applyBtn' is clicked
                var morefitterempty=$(".value_patient").text();
                var morefitterempty_market_patient=$(".value_patient_mainsearch").text();

                if (morefitterempty !== '' || morefitterempty_market_patient !== '') {

                    fitterremovedatafitters = (morefitterempty === '' || morefitterempty_market_patient === '') ? fitterremovedata: [...new Set(fitterremovedata.concat(marketersearchvalue_patient))];

                    dateandoveralldata_patient(fitterremovedatafitters, datefilltervalue);

                } else {

                    patientdatefillterrange(datefilltervalue);
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
                var morefitterempty_market_patient=$(".value_patient_mainsearch").text();

                if (morefitterempty !== '' || morefitterempty_market_patient !== '') {

                    fitterremovedatafitters = (morefitterempty === '' || morefitterempty_market_patient === '') ? fitterremovedata: [...new Set(fitterremovedata.concat(marketersearchvalue_patient))];

                    dateandoveralldata_patient(fitterremovedatafitters, datefilltervalue);

                } else {

                    patientdatefillterrange(datefilltervalue);
                }
             }
            });
              // More fitter search.....
       var moreFilterValues = [];
       $("#patientfitter_search").on("click", function () {
        //    moredatefittervale=$("#patientviews").text();
           var mainsearchfetch=$(".value_patient_mainsearch").text();
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
       fitterremovedata = mainsearchfetch === ''? resultsArray: resultsArray.concat(marketersearchvalue_patient);
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
       $(document).on('click', '.value_patient,.value_patient_mainsearch', function() {
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

        $('.marketervalues_search_patient').filter(function () {
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
        $('.marketervalues_search_patient').val("")
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

        $(document).on('click', '.options_patient div', function (e) {
            // Get the selected value and text
            var selectedValue = $(this).data('value');
            var selectedText = $(this).text();
            var moredatefittervale = $('#patientviews').text();

            $('.clear_all_views_patient').show();
            $(".search_view_patient").show();


            var resultsArray_marketer_patient = [];
            $(".marketervalues_search_patient").each(function () {
                var value = $(this).val();
                // Check if the value is not empty before processing
                if (value === "") {
                    return; // Skip this iteration if the value is empty
                }
                var results = $(this).attr('name') + "='" + value + "'";
                resultsArray_marketer_patient.push(results);

                var moreFilterValues_market_patient = [
                    $("#patient_marketer").val(),
                    $("#patient_branch").val(),
                    $("#patient_zone").val(),
                ];

                $(".value_patient_mainsearch").each(function (index) {
                    var morefillterdata_market = moreFilterValues_market_patient[index] ? moreFilterValues_market_patient[index] : ""; // Use "N/A" if value is empty
                    $(this).text(morefillterdata_market);
                });

                            });

                    marketersearchvalue_patient=resultsArray_marketer_patient;
                    fitterremovedata=resultsArray_marketer_patient;
                    morefilterview_patient(fitterremovedata,moredatefittervale);

                    //alert(marketersearchvalue);


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
            $("#patient_emp_name").val(employeename);
              $("#userfullnamepatient").val(userfullname);
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
              alert(response.message);
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

        console.log("patient_views:", patientviews);
    $.ajax({
        url: patientviews,
        type: "GET",
        success: function (responseData) {
            //console.log(responseData);
            dataSourcenew = responseData;
            totalItemspatients = responseData.length; // Get total items count // Set the data fetched from the server
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
           totalItemspatients = responseData.length; // Get total items count // Set the data fetched from the server
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
           totalItemspatients = responseData.length; // Get total items count // Set the data fetched from the server
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
                totalItemspatients = responseData.length; // Get total items count // Set the data fetched from the server
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
                totalItemspatients = responseData.length; // Get total items count // Set the data fetched from the server
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
function dateandoveralldata_patient(fitterremovedatafitters,datefilltervalue)
{
    $.ajax({
        url: patientdateandfitter,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedatafitters,
            datefilltervalue:datefilltervalue,
        },
        success: function (responseData) {
            dataSourcenew = responseData;
            totalItemspatients = responseData.length; // Get total items count // Set the data fetched from the server
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
    let count_patient = data.length;
    var startIdx = (pageNum - 1) * pageSize;
    var endIdx = pageNum * pageSize;
    var pageData = data.slice(startIdx, endIdx);
    var bodynew = "";
    let patient_count=0;
    $.each(pageData, function(index, user) {
        patient_count++;
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY | HH:MM");
        const wifeContact = String(user.wife_contact);
        var wifeContactview = wifeContact.substring(0, 3);
        const husContact = String(user.husband_contact);
        var husbandcontact = husContact.substring(0, 3);
        bodynew += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="ids"  data-id="' + user.id + '" data-date="' + formattedDate + '"> <strong>#' + user.id + ' - refid #'+ user.ref_doctor_id +'</strong><br> MRN :' + user.mrn_number + '<br></td>' +
'<td class="tdview" id="wifename" data-wife="' + user.wifename + '">' + user.wifename + '<br>' +
            '<span class="masked-wife">' + wifeContact.slice(0, 2) + 'XXXXX' + wifeContact.slice(-2) + '</span>' +
            '<i class="fa fa-eye show-wife" style="cursor:pointer;margin-left:5px;color:#1976d2;" data-wife="' + wifeContact + '"></i><br></td>' +                '<td class="tdview" id="notesfeed" data-notes="' + user.notes + '">' + formattedDate + '<br><br></td>' +
 '<td class="tdview" id="husbandname" data-husband="' + user.husband_name + '">' + user.husband_name + '<br>' +
            '<span class="masked-husband">' + husContact.slice(0, 2) + 'XXXXX' + husContact.slice(-2) + '</span>' +
            '<i class="fa fa-eye show-husband" style="cursor:pointer;margin-left:5px;color:#1976d2;" data-husband="' + husContact + '"></i></td>' +             
              '<td class="tdview" id="employeenames" data-emp="' + user.empolyee_name + '">' + user.userfullname + '-' + user.empolyee_name +'<br></td>' +
                '<td class="tdview" id="doctornames" data-name="' + user.doctor_name + '" data-special="' + user.special + '"><a href="#" class="imagecheck">Dr. ' + user.doctor_name + '</a><br>' + user.special + '<br></td>' +
                '<td class="tdview  value="' + user.hopsital_name + '">'+user.hopsital_name+'</td>' +
                '<td class="tdview notesviews" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filternotes"><img src="../assets/images/note_all.png" style="width: 25px;cursor: pointer;"  alt="Icon" class="icon"></td>' +
                '<td style="display:none" class="tdview" id="imageviews">' + user.image_paths + '</td>' +
                '</tr>';  });
    $("#patient_details").html(bodynew);
    $("#patient_totals").text(patient_count);
     $("#counts2").text(count_patient);
}

//vasanth
// Updated toggle function for wife/husband contacts
$(document).on('click', '.show-wife, .show-husband', function(e) {
    e.stopPropagation();
    const $icon = $(this);
    const isWife = $icon.hasClass('show-wife');
    
    // Ensure we have a string and provide empty string as fallback
    const fullNumber = String($icon.data(isWife ? 'wife' : 'husband') || '');
    const $maskedSpan = $icon.prev();
    
    // If empty or too short, just show as-is
    if (fullNumber.length < 2) {
        $maskedSpan.text(fullNumber);
        return;
    }
    
    // Toggle between masked and full number
    if ($icon.hasClass('active')) {
        // Mask the number (show first 2 and last 2 digits)
        $maskedSpan.text(fullNumber.slice(0, 2) + 'XXXXX' + fullNumber.slice(-2));
        $icon.removeClass('active').css('color', '#1976d2');
    } else {
        // Show full number
        $maskedSpan.text(fullNumber);
        $icon.addClass('active').css('color', '#ff5722');
    }
});


// Render pagination controls based on data
// function renderPaginationpatient(data, pageSize) {
//     var totalPages = Math.ceil(data.length / pageSize);
//     var paginationHtml = '';
//     for (var i = 1; i <= totalPages; i++) {
//         paginationHtml += '<button class="page-btn2 " style="background-color:#080fd399;"  data-pagepatient="' + i + '">' + i + '</button>';
//     }
//     $('#pagination2').html(paginationHtml);
//     // Bind click event to each pagination button
//     $('.page-btn2').click(function() {
//         var pageNum = $(this).data('pagepatient');
//         $('.page-btn2').removeClass('active');
//         $(this).addClass('active');
//         renderTablepatient(data, pageSize, pageNum);
//     });
// }
function renderPaginationpatient(data, pageSize) {
    var totalPages = Math.ceil(data.length / pageSize);
    var currentPage = 1;
    var maxVisible = 3; // show only 3 page numbers at a time

    function renderButtons() {
        var paginationHtml = '';

        var startPage = Math.floor((currentPage - 1) / maxVisible) * maxVisible + 1;
        var endPage = Math.min(startPage + maxVisible - 1, totalPages);

        // Previous Button
        if (currentPage > 1) {
            paginationHtml += '<button class="nav-btn" data-page="' + (currentPage - 1) + '">Prev</button>';
        }

        // Page Numbers (only 3 at a time)
        for (var i = startPage; i <= endPage; i++) {
            paginationHtml += '<button class="page-btn2 ' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</button>';
        }

        // Show Last Page if not included
        if (endPage < totalPages) {
            paginationHtml += '<span style="padding:5px;">...</span>';
            paginationHtml += '<button class="page-btn2" data-page="' + totalPages + '">' + totalPages + '</button>';
        }

        // Next Button
        if (currentPage < totalPages) {
            paginationHtml += '<button class="nav-btn" data-page="' + (currentPage + 1) + '">Next</button>';
        }

        $('#pagination2').html(paginationHtml);
    }

    // Initial Render
    renderButtons();
    renderTablepatient(data, pageSize, currentPage);

    // Click Event
    $('#pagination2').off('click').on('click', 'button', function () {
        currentPage = parseInt($(this).data('page'));
        renderButtons();
        renderTablepatient(data, pageSize, currentPage);
    });
}
