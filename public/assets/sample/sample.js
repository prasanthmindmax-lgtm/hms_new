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
    $('#close-button').click(function () {
        // Clear all input fields
        $('input, select, textarea').val('');
        // Clear error messages
        $('span').text('');
    });
    $('#submit-sample-datas').click(function (event) {
        event.preventDefault();

        let isValid = true;
        if ($('#created_by').val() === "") {
            $('.error_created_by').text('Enter the Created');
            isValid = false;
        }
        if ($('#vendor').val() === "") {
            $('.error_vendor').text('Please select the Employee Name or Vendor');
            isValid = false;
        }
        if ($('#neft_amount').val() === "") {
            $('.error_neft_amount').text('Enter the NEFT Amount');
            isValid = false;
        }
        if ($('#description').val() === "") {
            $('.error_description').text('Enter the Description');
            isValid = false;
        }
        if ($('#pan_number').val() === "") {
            $('.error_pan_number').text('Enter the PAN Number');
            isValid = false;
        }
     
        if ($('#account_number').val() === "") {
            $('.error_account_number').text('Enter the Account num');
            isValid = false;
        }
        if ($('#ifsc_code').val() === "") {
            $('.error_ifsc_code').text('Enter the IFSC code');
            isValid = false;
        }
         if ($('#invoice_number').val() === "") {
            $('.error_invoice_number').text('Enter the Inovice Number');
            isValid = false;
        }
          if ($('#invoice_amount').val() === "") {
            $('.error_invoice_amount').text('Enter the Inovice Amount');
            isValid = false;
        }
          if ($('#aressio_paid').val() === "") {
            $('.error_aressio_paid').text('Enter Amount');
            isValid = false;
        }
          if ($('#already_paid').val() === "") {
            $('.error_already_paid').text('Enter Amount');
            isValid = false;
        }
        // Validate image uploads
        if ($('#bank_upload')[0].files.length === 0) {
            $('.error_bank_upload').text('Please select the Images');
            isValid = false;
        }
        if ($('#invoice_upload')[0].files.length === 0) {
            $('.error_invoice_upload').text('Please select the Images');
            isValid = false;
        }
        if ($('#pan_upload')[0].files.length === 0) {
            $('.error_pan_upload').text('Please select the Images');
            isValid = false;
        }
        if (!isValid) {
            return; // Stop the form submission if validation fails
        }
        // Create FormData object
        let formData = new FormData();
        formData.append('serial_number', $('#serial_number').val());
        formData.append('created_by', $('#created_by').val());
        formData.append('vendor', $('#vendor').val());
        formData.append('description', $('#description').val());
        formData.append('neft_amount', $('#neft_amount').val());
        formData.append('pan_number', $('#pan_number').val());
        formData.append('account_number', $('#account_number').val());
        formData.append('ifsc_code', $('#ifsc_code').val());
        formData.append('invoice_amount', $('#invoice_amount').val());
        formData.append('aressio_paid', $('#aressio_paid').val());
        formData.append('already_paid', $('#already_paid').val());
        // Add images to FormData
        const files = $('#pan_upload')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('pan_upload[]', files[i]);
        }
        const invoice_upload = $('#invoice_upload')[0].files;
        for (let i = 0; i < invoice_upload.length; i++) {
            formData.append('invoice_upload[]', invoice_upload[i]);
        }
        const bank_upload = $('#bank_upload')[0].files;
        for (let i = 0; i < bank_upload.length; i++) {
            formData.append('bank_upload[]', bank_upload[i]);
        }
        // Include CSRF Token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        // AJAX Request
        $.ajax({
            url: samplesaveUrl,
            type: "POST",
            data: formData,
            processData: false, // Prevent processing of the data
            contentType: false, // Prevent setting content-type header
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Optional: Refresh the page
                }
            },
            error: function (error) {
                console.error(error.responseJSON);
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
    $(document).on('click', '.doctor_range, .doctor_btn', function() {

        //alert(marketersearchvalue);

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
    // More fitter search.....
    var moreFilterValues = [];
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
    $("#doctor_details1").show();
	
    $.ajax({
        url: fetchUrl,
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
function morefilterview(fitterremovedata,moredatefittervale) {

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
                '<td class="tdview all_views_new" value="' + user.id + '" id="doc_fetch" data-name="' + user.doctor_name + '" data-special="' + user.special + '"><a href="#" class="imagecheck">Dr. ' + user.doctor_name + '<a><br>' + user.special + '<br></td>' +
                '<td class="tdview" id="cityviews" data-citys="' + user.Branch_name + '" data-hospital="' + user.hopsital_name + '">' + user.hopsital_name + '<br>' + limitAddress + '......<br></td>' +
                '<td class="tdview " id="address_view" data-map="'+ user.map_link +'" data-address="' + user.address + '" ><a target="_blank" href="https://www.google.com/search?q='+ user.map_link +'"><img src="../assets/images/placeholder.png" style="width: 20px;"  alt="Icon" class="icon"></a></td>' +
                '<td class="tdview" id="contact_details" data-docnum="' + user.doc_contact + '" data-hptnum="' + hospitacontact + 'XXXXXX">Doctor:' + doctorcontact + 'XXXXXXX<br> Hospital:' + hospitacontact + 'XXXXXX</td>' +
               '<td class="tdview">' + user.Branch_name + '</td>' +
               '<td style="display:none" class="tdview" id="imageviews">' + user.image_paths + '</td>' +
               '<td class="tdview tooltip-container" id="employeename" data-emp="' + user.user_fullname +  '-'+ user.empolyee_name +'"><img src="../assets/images/user/avatar-1.jpg" style="width: 20px;" alt="Icon"class="img-user rounded-circle" title="' + user.user_fullname + '-'+ user.empolyee_name +'"></td>'+
        //    '<td class="tdview editor_doctor" value="' + user.id + '"  data-bs-toggle="offcanvas" data-bs-target="#announcement" ><img src="../assets/images/edit.png" style="width: 20px;"  alt="Icon" class="icon"></td>' +
                '</tr>';  });
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

