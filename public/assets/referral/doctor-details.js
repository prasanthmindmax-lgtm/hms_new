function formatDateForBackend(dateStr) {
    const date = new Date(dateStr);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}
 // More fitter search.....
    var moreFilterValues = [];

function date(date){
            moredatefittervale=date;
            $('#morefitter_search').trigger('click');
        }
function applyDateFilter(startDate, endDate) {
    const formattedStart = formatDateForBackend(startDate);
    const formattedEnd = formatDateForBackend(endDate);
    const datefiltervalue = `${formattedStart} - ${formattedEnd}`;
    
    $('#selectedDateRange').text(`${formattedStart} - ${formattedEnd}`);
    $('#dateallviews').text(datefiltervalue);
    
    // Call your existing filter function
    // datefillterrange(datefiltervalue);
    date(datefiltervalue);
    // morefilterview(null,datefiltervalue);
    
}

$(document).ready(function() {


     $('#dateFilterTrigger').click(function() {
        $('#calendarFilterModal').modal('show');
    });

    $('#applyDateFilter').click(function() {
        const startDate = $('#startDateDisplay').text(); // e.g. "June 20, 2025"
        const endDate = $('#endDateDisplay').text();
        applyDateFilter(startDate, endDate);
        $('#calendarFilterModal').modal('hide');
    });
    // Fetch data and initialize pagination
    overall_fetch();
    $(".search_view").hide();
    var fitterremovedata = []; // Keep this variable persistent
    var marketersearchvalue = [];
    $(document).on('click', '.doctor_range, .doctor_btn', function() {


        // Check if the click happened on a specific class
        if ($(this).hasClass('doctor_range')) {
            var datefilltervalue = $('#dateallviews').text(); // Get the current text value when '.ranges' is clicked
            var morefitterempty=$(".value_views").text();
            var morefitterempty_market=$(".value_views_mainsearch").text();

            if (morefitterempty !== '' || morefitterempty_market !== '') {

                fitterremovedatafitters = (morefitterempty === '' || morefitterempty_market === '') ? fitterremovedata: [...new Set(fitterremovedata.concat(marketersearchvalue))];

                dateandoveralldata(fitterremovedatafitters, datefilltervalue);

            } else {

                datefillterrange(datefilltervalue);
            }

        } else if ($(this).hasClass('doctor_btn')) {
            var datefilltervaluenew = $('.doctor_slct').text(); // Get the current text value when '.applyBtn' is clicked
            var dateRange = datefilltervaluenew.split(' - ');
            function convertDateFormat(dateStr) {
                let parts = dateStr.split('/');
                return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            var datefilltervalue = `${startDate} - ${endDate}`;
            var morefitterempty=$(".value_views").text();
            var morefitterempty_market=$(".value_views_mainsearch").text();

            if (morefitterempty !== '' || morefitterempty_market !== '') {

                fitterremovedatafitters = (morefitterempty === '' || morefitterempty_market === '') ? fitterremovedata: [...new Set(fitterremovedata.concat(marketersearchvalue))];

                dateandoveralldata(fitterremovedatafitters, datefilltervalue);

            } else {

                datefillterrange(datefilltervalue);
            }
        }
    });
   

    
        
    $("#morefitter_search").on("click", function () {
       // alert(marketersearchvalue);
        moredatefittervale=$("#dateallviews").text();
        var mainsearchfetch=$(".value_views_mainsearch").text();


        var resultsArray = [];
        $(".morefittersclr").each(function () {
            var value = $(this).val();
    // Check if the value is not empty before processing
    if (value === "") {
        $("#error_throws").text("Please select an item");
        return; // Skip this iteration if the value is empty

    }

    $("#error_throws").text("");

    var offcanvasElement = $("#dismissmodelssss").closest(".offcanvas");
    var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement[0]); // Get the instance
    offcanvas.hide();

    $('.clear_all_views').show();
    $(".search_view").show();
    var results = $(this).attr('name') + "='" + value + "'";
    resultsArray.push(results);

    fitterremovedata = mainsearchfetch === ''? resultsArray: resultsArray.concat(marketersearchvalue);

        });
        

     morefilterview(fitterremovedata,moredatefittervale);

       fitterremovedata;
        var moreFilterValues = [
            $("#doctornames_more").val(),
            $("#special_more").val(),
            $("#hospital_more").val()
        ];
        $(".value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
            $(this).text(morefillterdata);
        });
    });

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


       // popup doctor details and image fetch data's
       $(document).on('click', '.imagecheck1', function (e) {
        $('#exampleModal1').modal('show');
        var userId = $(this).attr('value');
        // Find the corresponding row
        var row = $(this).closest('tr');
        // Get data from the row
        var address = row.find('#address_view').data('address');
        var docname = row.find('#doc_fetch').data('name');
        var special = row.find('#doc_fetch').data('special');

        // var doccontact = row.find('#contact_details').data('docnum');
        // var hoscontact = row.find('#contact_details').data('hptnum');
      var doccontact = typeof row.find('#contact_details').data('docnum') !== 'undefined' 
               ? String(row.find('#contact_details').data('docnum')) 
               : '';
      var hoscontact = typeof row.find('#contact_details').data('hptnum') !== 'undefined'
               ? String(row.find('#contact_details').data('hptnum'))
               : '';

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

      // Masking function
    function maskNumber(number) {
        if (number && number.length >= 4) {
            return number.slice(0, 2) + 'XXXXX' + number.slice(-2);
        }
        return number || 'N/A'; // Return 'N/A' if empty
    }

        // Update modal content
        $("#Doctornamehead").text(docname + " (" + special + ")");
        $("#visit_date").text("Visit Date : " + dateviews);
        $(".empname_views_all").text("Marketer Name: " + employeenames);
        $("#docaddress").text(address);
        $("#doctor_ids").text(ids);
        $(".doctor_names").text("Name : " + docname + " (" + special + ")");

        // Doctor number with initial masking
    $("#dcnum").html('Doctor Number: <span class="masked-doc-modal">' + maskNumber(doccontact) + '</span>' +
                    '<i class="fa fa-eye show-doc-modal" style="cursor:pointer;margin-left:5px;color:#1976d2;" ' +
                    'data-number="' + doccontact + '" title="Click to show/hide"></i>');
    
    // Hospital number with initial masking
    $("#hpnum").html('Hospital Number: <span class="masked-hosp-modal">' + maskNumber(hoscontact) + '</span>' +
                    '<i class="fa fa-eye show-hosp-modal" style="cursor:pointer;margin-left:5px;color:#1976d2;" ' +
                    'data-number="' + hoscontact + '" title="Click to show/hide"></i>');

        $("#maplocation").text("Map Link: " + maplink);
        $(".hosptalnames").text("Hospital Name : " +hosptalname);
        $(".cityviews").text("City : " + cityviews)
    });

   $(document).on('click', '.show-doc-modal, .show-hosp-modal', function(e) {
    e.stopPropagation();
    const $icon = $(this);
    const fullNumber = String($icon.data('number') || '');
    const $maskedSpan = $icon.prev();
    const isMasked = !$icon.hasClass('active');
    
    if (fullNumber.length >= 4) {
        if (isMasked) {
            // Show full number
            $maskedSpan.text(fullNumber);
            $icon.addClass('active').css('color', '#ff5722');
        } else {
            // Show masked number
            $maskedSpan.text(fullNumber.slice(0, 2) + 'XXXXX' + fullNumber.slice(-2));
            $icon.removeClass('active').css('color', '#1976d2');
        }
    }
});

    $(document).on('click', '.editor_doctor', function (e) {
        var userId = $(this).attr('value');
         // Find the corresponding row
         var row = $(this).closest('tr');
             // Get data from the row
        var address = row.find('#address_view').data('address');
        var docname = row.find('#doc_fetch').data('name');
        var empname=row.find('#employeename').data('emp')
        var special = row.find('#doc_fetch').data('special');
        var cityviews = row.find('#cityviews').data('citys');
        var doccontact = row.find('#contact_details').data('docnum');
        var hoscontact = row.find('#contact_details').data('hptnum');
        var dateviews = row.find('#dateviews').data('date');
        var imageviews = row.find('#imageviews').text().trim(); // Get and trim the text content
        $("#uesrids").text(userId);
        $("#doctorname_edits").val(docname);
        $("#emp_name").val(empname);
        $("#special_more").val(special);
        $("#citys").val(cityviews);
        $("#addressviews").val(address);
        $("#contactviews").val(hoscontact);
    });
    $(".editsoveralls").on("click",function(){
        idviews=$("#uesrids").text();
        editsArray=[];
        $(".editsall").each(function () {
        var values = $(this).val();
        // Check if the value is not empty before processing
        if (values === "") {
            return; // Skip this iteration if the value is empty
        }
        var resultsedits = $(this).attr('name') + "='" + values + "'";
        editsArray.push(resultsedits);
        });
        doctoreditdate(editsArray,idviews);
    });
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var pageSize = parseInt($(this).val());
        renderPagination(dataSource, pageSize);
        renderTable(dataSource, pageSize, 1);  // Initially show the first page
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
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#marketer_fetch").val(),
                $("#branchviews").val(),
                $("#zoneviews").val(),
                $("#special").val(),
                $("#zonalHeadFilter").val(),

            ];

            $(".value_views_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });

                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
                morefilterview(fitterremovedata,moredatefittervale);

                //alert(marketersearchvalue);


        });

});
var dataSource = [];  // Data will be fetched here
// Fetch the data and initialize pagination
function overall_fetch() {

    // console.log("Fetching from:", fetchUrl);
   

    $("#doctor_details1").show();
    $.ajax({
        url: fetchUrl,
        type: "GET",
        success: function (responseData) {
            console.log("doctordata:",responseData);
            $("#doctor_details1").hide();
            $("#doctor_details").show();
            dataSource = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            renderPagination(dataSource, pageSize);
            renderTable(dataSource, pageSize, 1); // Show first page initially

            slct="";
            let seenNames = new Set(); // To store unique doctor names

        $.each(dataSource, function(index, user1) {
            if (!seenNames.has(user1.doctor_name)) {
                seenNames.add(user1.doctor_name);
                slct += `<div data-value="${user1.id}">${user1.doctor_name}</div>`;
            }
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
console.log("fetchUrlfitter:",fetchUrlfitter);
    $("#doctor_details1").show();
    $.ajax({
        url: fetchUrlfitter,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
           
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
function morefilterview(fitterremovedata=null,moredatefittervale=null) {

    //alert(fitterremovedata);

    $.ajax({
        url: fetchUrlmorefitter,
        type: "GET",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        data: {
            fitterremovedata:fitterremovedata,
            moredatefittervale:moredatefittervale,
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
    //alert(fitterremovedata);

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
    $.ajax({
        url: fetchUrlmorefitterdateclear,
        type: "GET",
        data: {
            datefilltervalue: datefilltervalue,
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
function doctoreditdate(editsArray,idviews) {
    var editString = editsArray.join(',');
    $.ajax({
        url: doctordetailseditsall, // The URL where the request will be sent
        type: "POST", // Using POST method
        data: {
            editsArray: editsArray, // Sending the edits array to the server
            idviews:idviews,
        },
        success: function(responseData) {
            // Assuming responseData contains the updated doctor data
            idgetvalue(idviews);
            // console.log(responseData);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function idgetvalue(idviews)
{
    $.ajax({
        url: doctordetailsid,
        type: "GET",
        data: {
            idviews: idviews,
        },
        success: function (responseData) {
            // console.log(responseData);
            $(".value_edit").show();
            $(".value_edit").text("Last Update date & time 19:20");
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
        var limitAddress = user.address.substring(0, 30);

        const hplContact = String(user.hpl_contact);
        var hospitacontact = hplContact.substring(0, 3);
        const docContact = String(user.doc_contact);
        var doctorcontact = docContact.substring(0, 3);

body += '<tr onclick="rowClick(event)">' +
        '<td class="tdview" id="dateviews" data-id="' + user.id + '" data-date="' + formattedDate + '"> <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
        '<td class="tdview all_views_new" value="' + user.id + '" id="doc_fetch" data-name="' + user.doctor_name + '" data-special="' + user.special + '"><a href="#" class="imagecheck1">Dr. ' + user.doctor_name + '<a><br>' + user.special + '<br></td>' +
        '<td class="tdview" id="cityviews" data-citys="' + user.location_name + '" data-hospital="' + user.hopsital_name + '">' + user.hopsital_name + '<br>' + limitAddress + '......<br></td>' +
  '<td class="tdview" id="address_view" data-map="' + user.map_link + '" data-address="' + user.address + '">' +
    '<a href="#" class="open-modal" data-img="../assets/images/placeholder.png">' +
        '<img src="../assets/images/placeholder.png" style="width: 20px;" alt="Icon" class="icon">' +
    '</a>' +
'</td>'  +
'<td class="tdview" id="contact_details" ' +
'data-docnum="' + user.doc_contact + '" data-hptnum="' + user.hpl_contact + '">' +
'Doctor: <span class="masked-doc">' + user.doc_contact.slice(0, 2) + 'XXXXX' + user.doc_contact.slice(-2) + '</span>' +
'<i class="fa fa-eye show-doctor" style="cursor: pointer; margin-left:5px; color: #1976d2;" ' +
'data-doctor="' + user.doc_contact + '" title="Click to show/hide number"></i><br>' +
'Hospital: <span class="masked-hpt">' + user.hpl_contact.slice(0, 2) + 'XXXXX' + user.hpl_contact.slice(-2) + '</span>' +
'<i class="fa fa-eye show-hospital" style="cursor: pointer; margin-left:5px; color: #1976d2;" ' +
'data-hospital="' + user.hpl_contact + '" title="Click to show/hide number"></i>' +
'</td>' +

      '<td class="tdview">' + user.location_name + '<br><span style="color: blue;">' + user.zone_name + '</span></td>'+

        '<td style="display:none" class="tdview" id="imageviews">' + user.image_paths + '</td>' +
 '<td class="tdview" id="employeename" data-emp="' + user.user_fullname + '-' + user.empolyee_name + '">' + user.user_fullname + '-' + user.empolyee_name + '</td>' +
    '<td class="tdview">' +
    '<i class="fas fa-plus-circle toggle-details" style="cursor:pointer;font-size:18px;color:#6777ef;" data-id="' + user.id + '"></i>' +
    '</td>' +
    '</tr>' +
    '<tr class="detail-row" id="detail-' + user.id + '" style="display:none">' +
    '<td colspan="100%" class="details-container">' +
    '<div class="detail-row" id="doctor-details-' + user.id + '"></div>' +
    '<div class="detail-row" id="meeting-details-' + user.id + '"></div>' +
    '<div class="detail-row" id="patient-details-' + user.id + '"></div>' +
    '</td>' +        '</tr>';  });

    $("#doctor_details").html(body);
    $("#today_visits").text(count);
    $("#counts").text(count);
}



$(document).on('click', '.open-modal', function (e) {
    e.preventDefault(); 

    var imgSrc = $(this).data('img');
    $('#popupImage').attr('src', imgSrc);
    $('#imageModal').modal('show');
});






   let map;
    // 13.05223383514234, 80.21261319538122
    // let pathCoordinates = [
    //     { lat: 13.05223383514234, lng: 80.21261319538122 }, // Start
    //     { lat: 12.8445, lng: 77.6638 },
    //     { lat: 12.8449, lng: 77.6645 },
    //     { lat: 12.8453, lng: 77.6651 },
    //     { lat: 12.8458, lng: 77.6659 }, // End
    // ];
    let pathCoordinates = [
        { lat: 13.0355689, lng: 80.1668562 }, // Start
        { lat: 13.0355642, lng: 80.1668566 },
        { lat: 13.0355655, lng: 80.1668559 },
        { lat: 13.0355651, lng: 80.1668583 },
        { lat: 12.8458, lng: 77.6659 }, // End
    ];

    let polyline;
    let replayMarker;
    let currentIndex = 0;
    let intervalId;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: pathCoordinates[0],
            zoom: 18,
            mapTypeId: 'roadmap'
        });

        // Draw black line
        polyline = new google.maps.Polyline({
            path: pathCoordinates,
            geodesic: true,
            strokeColor: '#000000',
            strokeOpacity: 1.0,
            strokeWeight: 4
        });

        polyline.setMap(map);

        // Start marker (green)
        new google.maps.Marker({
            position: pathCoordinates[0],
            map: map,
            title: "Start",
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
        });

        // End marker (red)
        new google.maps.Marker({
            position: pathCoordinates[pathCoordinates.length - 1],
            map: map,
            title: "End",
            icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });

        // Replay marker (moving)
        replayMarker = new google.maps.Marker({
            position: pathCoordinates[0],
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        });
    }

    function startReplay() {
        if (intervalId) clearInterval(intervalId);
        currentIndex = 0;
        replayMarker.setPosition(pathCoordinates[0]);

        intervalId = setInterval(() => {
            currentIndex++;
            if (currentIndex >= pathCoordinates.length) {
                clearInterval(intervalId);
                return;
            }
            replayMarker.setPosition(pathCoordinates[currentIndex]);
            map.panTo(pathCoordinates[currentIndex]);
        }, 1000); // Move every second
    }








// function renderPagination(data, pageSize) {
//     var totalPages = Math.ceil(data.length / pageSize);
//     var paginationHtml = '';
//     var maxVisible = 5;

//     for (var i = 1; i <= totalPages; i++) {
//         var displayStyle = (i <= maxVisible) ? '' : 'display:none;';
//         paginationHtml += '<button class="page-btn" data-page="' + i + '" style="' + displayStyle + '">' + i + '</button>';
//     }

//     // Optionally, add a "Next" button or dots
//     if (totalPages > maxVisible) {
//         paginationHtml += '<span class="dots"> ... </span>';
//     }

//     $('#pagination').html(paginationHtml);

//     // Bind click event to each pagination button
//     $('.page-btn').click(function () {
//         var pageNum = $(this).data('page');
//         $('.page-btn').removeClass('active').css('background-color', '#ffffff');
//         $(this).addClass('active').css('background-color', '#080fd399');
//         renderTable(data, pageSize, pageNum);
//     });

//     // Trigger click on first button by default
//     $('.page-btn[data-page="1"]').click();
// }
function renderPagination(data, pageSize) {
    const totalPages = Math.ceil(data.length / pageSize);
    let currentPage = 1;

    function updatePagination() {
        let paginationHtml = '';

        // Previous Button
        if (currentPage > 1) {
            paginationHtml += `<button class="page-btn prev-btn" data-page="${currentPage - 1}" style="background-color:#6777ef; color:#fff;">Prev</button>`;
        }

        // Always show first page
        if (currentPage > 3) {
            paginationHtml += `<button class="page-btn" data-page="1" style="background-color:#6777ef; color:#fff;">1</button>`;
            paginationHtml += `<span class="dots">...</span>`;
        }

        // Pages around current page
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            paginationHtml += `<button class="page-btn ${isActive ? 'active' : ''}" data-page="${i}" style="background-color:${isActive ? '#080fd399' : '#6777ef'}; color:#fff;">${i}</button>`;
        }

        // Always show last page
        if (currentPage < totalPages - 2) {
            paginationHtml += `<span class="dots">...</span>`;
            paginationHtml += `<button class="page-btn" data-page="${totalPages}" style="background-color:#6777ef; color:#fff;">${totalPages}</button>`;
        }

        // Next Button
        if (currentPage < totalPages) {
            paginationHtml += `<button class="page-btn next-btn" data-page="${currentPage + 1}" style="background-color:#6777ef; color:#fff;">Next</button>`;
        }

        $('#pagination').html(paginationHtml);

        // Click Event Binding
        $('.page-btn').click(function () {
            const page = $(this).data('page');
            if (page && page !== currentPage) {
                currentPage = page;
                renderTable(data, pageSize, currentPage);
                updatePagination();
            }
        });
    }

    renderTable(data, pageSize, currentPage);
    updatePagination();
}

//vasanth
$(document).on('click', '.show-doctor', function () {
    var doctor = $(this).data('doctor');
    $(this).closest('td').find('.masked-doc').text(doctor);
});

$(document).on('click', '.show-hospital', function () {
    var hospital = $(this).data('hospital');
    $(this).closest('td').find('.masked-hpt').text(hospital);
});

// Add this click handler for eye icons
$(document).on('click', '.show-doctor, .show-hospital', function(e) {
    e.stopPropagation();
    const $icon = $(this);
    const isDoctor = $icon.hasClass('show-doctor');
    
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

// Initialize Bootstrap tooltips
$(document).ready(function() {
  $('[data-toggle="tooltip"]').tooltip({
    trigger: 'hover',
    delay: { "show": 300, "hide": 100 }
  });
});
