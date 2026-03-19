$(document).ready(function() {
    // Fetch data and initialize pagination
    overall_fetch();
    $(".search_view").hide();


    $('.ranges, .applyBtn').on('click', function() {
        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
            var datefilltervalue = $('#dateallviews').text(); // Get the current text value when '.ranges' is clicked
            var morefitterempty=$(".value_views").text();
            //alert(datefilltervalue);
            if(morefitterempty=='')
            {
            datefillterrange(datefilltervalue);
            }
        } else if ($(this).hasClass('applyBtn')) {
            var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
            var dateRange = datefilltervaluenew.split(' - ');
            function convertDateFormat(dateStr) {
                let parts = dateStr.split('/');
                return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY   
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            var datefilltervalue = `${startDate} - ${endDate}`;
            var morefitterempty=$(".value_views").text();
            if(morefitterempty=='')
            {
            datefillterrange(datefilltervalue);
            }
       
        }
    });

    // More fitter search.....
    var moreFilterValues = [];
   
    var fitterremovedata = []; // Keep this variable persistent
   
    $("#morefitter_search").on("click", function () {

        moredatefittervale=$("#dateallviews").text();

        $('.clear_all_views').show();
        $(".search_view").show();
   
        var resultsArray = [];
        $(".morefittersclr").each(function () {

            var value = $(this).val();
    // Check if the value is not empty before processing
    if (value === "") {
        return; // Skip this iteration if the value is empty
    }
    var results = $(this).attr('name') + "='" + value + "'";
    resultsArray.push(results);

       fitterremovedata=resultsArray;

   
        });

        var uniqueResults = [...new Set(resultsArray)];
        morefilterview(uniqueResults,moredatefittervale);

       fitterremovedata;


        var moreFilterValues = [
       
            $("#doctornames_more").val(),
            $("#employee_more").val(),
            $("#special_more").val(),
            $("#city_more").val(),
            $("#hospital_more").val()
        ];
    
        $(".value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
          
            $(this).text(morefillterdata);


        });
    });

    $(document).on("click", ".value_views", function () {
        var morefillterremvedata = $(this).text();
        $(this).text("");

        $('.morefittersclr').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");

       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.endsWith(morefillterremvedata + "'");
    });
 
          morefilltersremoveviews(fitterremovedata);
   
    });


    $(document).on("click", ".clear_all_views", function () {

        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_all_views').hide();
        $(".search_view").hide();

        overall_fetch();
    
    });

       // popup doctor details and image fetch data's
       $(document).on('click', '.imagecheck', function (e) {
        $('#exampleModal1').modal('show');
        var userId = $(this).attr('value');
        
        // Find the corresponding row
        var row = $(this).closest('tr');
        
        // Get data from the row
        var address = row.find('#address_view').data('address');
        var docname = row.find('#_fetch').data('name');
        var special = row.find('#_fetch').data('special');
        var doccontact = row.find('#contact_details').data('docnum');
        var hoscontact = row.find('#contact_details').data('hptnum');
        var dateviews = row.find('#dateviews').data('date');
        var imageviews = row.find('#imageviews').text().trim(); // Get and trim the text content

        // Remove unwanted characters like " ] [" if present
        imageviews = imageviews.replace(/[\[\]\s"]/g, ''); // Remove [ ] " and extra spaces
        // Split the cleaned string into an array of image paths
        var firstImage = imageviews.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#main').attr('src', '../doctor_images/'+imageNamefirst+'');

        var imageArray = imageviews.split(',');
        // Extract the names of the images
        var views = '';
        
        imageArray.forEach(function(image) {
            
            var imageName = image.trim().split('/').pop(); // Get the file name

            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<img src="../doctor_images/'+imageName+'">';
   
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
        $("#visit_date").text("Visit Date : " + dateviews);
        $("#docaddress").text(address);
        $("#doctor_ids").text(userId);
        $("#doctor_names").text("Name : " + docname + " (" + special + ")");
        $("#dcnum").text("Doctor Number : " + doccontact);
        $("#hpnum").text("Hospital Number : " + hoscontact);
    });

    
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var pageSize = parseInt($(this).val());
        renderPagination(dataSource, pageSize);
        renderTable(dataSource, pageSize, 1);  // Initially show the first page
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
            $("#doctor_details1").hide();
            $("#doctor_details").show();
            dataSource = responseData; 
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            renderPagination(dataSource, pageSize);
            renderTable(dataSource, pageSize, 1); // Show first page initially
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

function morefilterview(uniqueResults,moredatefittervale) {

    var morefilltersall=uniqueResults.join(" AND ");

    $.ajax({
        url: fetchUrlmorefitter,
        type: "GET",
        data: {
            morefilltersall: morefilltersall,
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


function  morefilltersremoveviews(fitterremovedata)
{

    if(fitterremovedata!="")
    {
        var fitterremovedataall=fitterremovedata.join(" AND ");
   

   
   $.ajax({
    url: fetchUrlmorefitterremove,
    type: "GET",
    data: {
        fitterremovedataall: fitterremovedataall,

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

   }else
   {

      $('.clear_all_views').hide();
      $(".search_view").hide();
      overall_fetch();
   }

}


// Render table rows based on the page and page size
function renderTable(data, pageSize, pageNum) {
    var startIdx = (pageNum - 1) * pageSize;
    var endIdx = pageNum * pageSize;
    var pageData = data.slice(startIdx, endIdx);

    var body = "";
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY | hh:mm A");

        body += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="dateviews" data-date="' + formattedDate + '"> <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview" id="_fetch" data-name="' + user.doctor_name + '" data-special="' + user.special + '">Dr. ' + user.doctor_name + '<br>' + user.special + '<br></td>' +
                '<td class="tdview">' + user.hopsital_name + '<br>' + user.city + '<br></td>' +
                '<td class="tdview" id="contact_details" data-docnum="' + user.doc_contact + '" data-hptnum="' + user.hpl_contact + '">Doctor Num :' + user.doc_contact + '<br> Hospital Num :' + user.hpl_contact + '</td>' +
                '<td class="tdview" id="address_view" data-address="' + user.address + '">Address : ' + user.address + '</td>' +
                '<td class="tdview imagecheck" value="' + user.id + '"><a href="#">Doctor Status</a></td>' +
                '<td style="display:none" class="tdview" id="imageviews">' + user.image_paths + '</td>' +
                '<td class="tdview"><span class="new-badge">Active</span></td>' +
                '<td class="tdview"><img src="../assets/images/more.png" style="width: 25px;" alt="Icon" class="icon"></td>' +
                '</tr>';
    });

    $("#doctor_details").html(body);
    $("#today_visits").text(totalItems);
    $("#counts").text(totalItems);
}

// Render pagination controls based on data
function renderPagination(data, pageSize) {
    var totalPages = Math.ceil(data.length / pageSize);
    var paginationHtml = '';

    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="page-btn " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
    }

    $('#pagination').html(paginationHtml);

    // Bind click event to each pagination button
    $('.page-btn').click(function() {
        var pageNum = $(this).data('page');
        $('.page-btn').removeClass('active');
        $(this).addClass('active');
        renderTable(data, pageSize, pageNum);
    });
}
