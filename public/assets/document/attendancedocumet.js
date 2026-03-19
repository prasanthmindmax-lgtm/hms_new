$(document).ready(function () {
    attendance_fetch(1);
    $(".my_search_view").hide();
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
   
    $('.dropdown-item-loc').on('click', function () {
        var selectedBranchName = $(this).text(); 
        var selectedBranchId = $(this).data('value');
        $('#zone_id').val(selectedBranchName); 
        $('#zone_id').attr('data-value', selectedBranchId); 
    });

      $('#submit-dailyattendance_document').click(function (event) {
        // alert("test");
          event.preventDefault();
          let isValid = true;
          if ($('#zone_id').val() === "") {
              $('.error_doctor').text('Enter the Location');
              isValid = false;
          }
          if (!isValid) {
              return; 
          }
          // Create FormData object
          let formData = new FormData();
  
          formData.append('att_from_date', $('#att_fromdate').val());
          formData.append('att_to_date', $('#att_todate').val());
          let zoneId = $('#zone_id').attr('data-value');
          formData.append('zone_id', zoneId);
          // Add images to FormData
          const files = $('#pf_uploads')[0].files;
          for (let i = 0; i < files.length; i++) {
              formData.append('images[]', files[i]);
          }
          // Include CSRF Token
          formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
          // AJAX Request
          $.ajax({
              url: dailyattendance_documentaddUrl,
              type: "POST",
              data: formData,
              processData: false, 
              contentType: false, 
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
                $("#exampleModaluser").modal('hide');
                attendance_fetch(1);
            },
              error: function (error) {
                  console.error(error.responseJSON);
              },
          });
      });
    
      
     $('#itemsPerPageSelectdocument').change(function() {
        var pageSizedocuments = parseInt($(this).val());
        renderPaginationdocument(dataSourcedocument, pageSizedocuments);
        renderTabledocument(dataSourcedocument, pageSizedocuments, 1);  // Initially show the first page
    });


    $(document).on('click', '.imagecheck', function (e) {
        $('#exampleModal1').modal('show');
        var userId = $(this).attr('value');
        
        // Find the corresponding row
        var row = $(this).closest('tr');
        
        // Get data from the row
        var fromdate = row.find('#fromdate').text();
        var todate = row.find('#todate').text();
        var location = row.find('#location').text();
        var created_by = row.find('#createdby').text();
        var imageviews = row.find('#imageviews').text().trim();
// alert(location);
        // Remove unwanted characters like "] [" if present
        imageviews = imageviews.replace(/[\[\]\s"]/g, '', " "); // Remove [ ] " and extra spaces
        // Split the cleaned string into an array of image paths
        var firstImage = imageviews.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
				
        $('#main').attr('src', '../attendance_data/'+imageNamefirst+'');

        var imageArray = imageviews.split(',');
        // Extract the names of the images
        var views = '';
        
        imageArray.forEach(function(image) {
            
            var imageName = image.trim().split('/').pop(); // Get the file name

            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<img src="../attendance_data/'+imageName+'">';
   
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
        $("#attlocation").text(location);
        $("#attfromdate").text(fromdate);
        $("#atttodate").text(todate);
        $("#created_name").text(created_by);
    });
    $(document).on("click" , '.upload_document', function (e) {
        $('#exampleModal2').modal('show');
        var row = $(this).closest('tr');
        var fetchid=row.find('#idfetch').data('id');
        var pdffilesviews = row.find('#pffiles').text();
        var expireda = row.find('#expiredatess').text();
        $("#docu_id").text(fetchid);
        $("#id_document").val(fetchid);
        $("#update_documents_all").val(pdffilesviews);
        $("#expire_dates").val(expireda);
    });
    $(document).on('click', '#pdffetch', function (e) {
        fetchvalue=$(this).text();
    //   $('#pfmain').attr('src', "../public/document_data/" + fetchvalue);
      $('#pfmain').attr('src', "../document_data/" + fetchvalue);
    });
    $("#submit-document_update").on("click",function(){
         let formData = new FormData();
         formData.append('att_id', $('#edit_id').val());
        formData.append('att_from_date', $('#edit_att_fromdate').val());
        formData.append('att_to_date', $('#edit_att_todate').val());

        let zoneId = $('#edit_zone_id').attr('data-value');
        formData.append('zone_id', zoneId);
         // Add images to FormData
         const files = $('#pf_update')[0].files;
         for (let i = 0; i < files.length; i++) {
             formData.append('images[]', files[i]);
         }
           // Include CSRF Token
           formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
           // AJAX Request
           $.ajax({
               url: editattendancedUrl,
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
                $('#exampleModal2').modal('hide');
                attendance_fetch();
               },
               error: function (error) {
                console.error(error.responseJSON);
            },
           });
    });
});
$(document).on('click', '.delete_att_details', function(e){
    e.preventDefault();
    var id = $(this).closest('tr').find('#idfetch').data('id');
    
    $.ajax({
    url:"/attendance_delete/" + id,
    type:"POST",
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
        attendance_fetch();
    }
    });
});

var dataSourcedocument = [];

$(document).on("click" , '.edit_att_details', function (e) {
    $('#exampleModal2').modal('show');

var id = $(this).closest('tr').find('#idfetch').data('id');
// alert(id);
    $.ajax({
        url: "/att_detials_edit/" + id,
        type: "GET",
        success: function(response) {
            $('.location-dropdown-options div').removeClass('selected'); // clear previous
var $selectedItem = $('.location-dropdown-options div[data-value="'+response.zone_id+'"]');
$selectedItem.addClass('selected');

var selectedText = $selectedItem.text();

$('#edit_zone_id')
    .val(selectedText)
    .attr('data-value', response.zone_id); // this line sets the data-value
            
            $('#edit_id').val(response.att_id);
            $('#edit_att_fromdate').val(response.att_from_date);
            $('#edit_att_todate').val(response.att_to_date);
           $('#pf_update').val(response.att_document);
        }
    });	
});

function renderTabledocument(data, pageSizedocuments, pageNum) {
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);
    //console.log(pageData);
    var body = "";
    var uniqueZones = [];
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm");
        var view = user.expire_date ;
        body += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="idfetch" data-id="'+ user.att_id +'"> <strong>#' + user.att_id + '</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview" data-filesview="' + user.att_document + '">' + user.name + '<br>' + user.zone_name+ '<br></td>' +
               '<td style="display:none;" id="location" class="tdview" >'+ user.name +'</td>' +
               '<td style="display:none;" id="createdby" class="tdview" >'+ user.created_by +'</td>' +
               '<td class="tdview" id="fromdate">'+ user.att_from_date +'</td>' +
               '<td class="tdview" id="todate">'+ user.att_to_date +'</td>' +
               '<td style="display:none" class="tdview" id="imageviews">' + user.att_document + '</td>' +
               '<td class="tdview" value="' + user.att_id + '"><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon imagecheck"></td>' +
               '<td class="tdview"><img src="../assets/images/edit.png" style="width: 25px; margin-right:5px;"  alt="Icon" class="icon edit_att_details">'+
               '<a href="#"><img src="../assets/images/delete.png" style="width: 25px;"  alt="Icon" class="icon delete_att_details"></a>'+
                '</td>' +
               '</tr>';  });
    $("#document_tbl").html(body);
    $("#mycounts").text(totalItems);
}
function renderPaginationdocument(data, pageSizedocuments, currentPage = 1) {
    const totalPages = Math.ceil(data.length / pageSizedocuments);
    let paginationHtml = '';

    // Previous Button
    if (currentPage > 1) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
    }

    const maxVisible = 3;
    const pageRange = [];

    // Always show first page
    pageRange.push(1);

    // Add dots if currentPage is far from 1
    if (currentPage > maxVisible) {
        pageRange.push('...');
    }

    // Middle pages around currentPage
    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
        pageRange.push(i);
    }

    // Add dots if currentPage is far from the last page
    if (currentPage < totalPages - maxVisible + 1) {
        pageRange.push('...');
    }

    // Always show last page if more than 1
    if (totalPages > 1) {
        pageRange.push(totalPages);
    }

    // Generate buttons from pageRange
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

    // Next Button
    if (currentPage < totalPages) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage + 1}">Next</button>`;
    }

    $('#paginationdocument').html(paginationHtml);

    // Click event
    $('.page-btnviews').click(function () {
        const pageNum = $(this).data('page');
        renderPaginationdocument(data, pageSizedocuments, pageNum);
        renderTabledocument(data, pageSizedocuments, pageNum);
    });

    // Initial table render
    renderTabledocument(data, pageSizedocuments, currentPage);
}


$('.ranges, .applyBtn').on('click', function() {
    // Check if the click happened on a specific class
    if ($(this).hasClass('ranges')) {
     
        var moredatefittervale = $('#mydateviewsall').text(); 
        ticketdatefillterrange(moredatefittervale,fitterremovedata);
        
    } else if ($(this).hasClass('applyBtn')) {

        var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
        var dateRange = datefilltervaluenew.split(' - ');
        function convertDateFormat(dateStr) {
            let parts = dateStr.split('/');
            return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY   
        }
        var startDate = convertDateFormat(dateRange[0]);
        var endDate = convertDateFormat(dateRange[1]);
        var moredatefittervale = `${startDate} - ${endDate}`;
        ticketdatefillterrange(moredatefittervale,fitterremovedata);
        
    }
   
});
function attendance_fetch(statusid) {
    var moredatefittervale = $('#mydateallviews').text();
    $(".value_views_mysearch").text("");
    $("#my_ticket_details1").show();
    $.ajax({
        url: attendancedetialsUrl,
        type: "GET",
        data: {
            moredatefittervale: moredatefittervale,
            statusid:statusid
        },
        success: function (responseData) {
            handleSuccess(responseData,3);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}  function secmorefilterview(uniqueResults,urlstatus,url,moredatefittervale) {
    if(uniqueResults!="")
       {
       var morefilltersall=uniqueResults.join(" AND ");
       $.ajax({
           url: url,
           type: "GET",
           data: {
               morefilltersall: morefilltersall,
               moredatefittervale:moredatefittervale,			
           },
           success: function (responseData) {
               handleSuccess(responseData,urlstatus);
           },
           error: function (xhr, status, error) {
               $("#my_ticket_details1").hide();
               console.error("AJAX Error:", status, error);
           }
       });
       }else
      {
       if(urlstatus == 3){
        $('.clear_my_views').hide();
        $(".my_search_view").hide();
        attendance_fetch();
       }
      }
   }
   function handleSuccess(responseData,urlstatus) {
    if(urlstatus == 3){
            $("#my_ticket_details1").hide();
            $("#document_tbl").show();
            dataSourcedocument = responseData;
            totalItems = responseData.length; 
            var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val()); // Get selected items per page
            renderPaginationdocument(dataSourcedocument, pageSizedocuments);
            renderTabledocument(dataSourcedocument, pageSizedocuments, 1); // Show first page initially
            
    }
}

var fitterremovedata = [];	
function ticketdatefillterrange(moredatefittervale,fitterremovedata) {
currentFilter = moredatefittervale;
var morefilltersall=fitterremovedata.join(" AND ");

$("#document_tbl").show();
$.ajax({
    url: attendancedetialsUrl,
    type: "GET",
    data: {
        moredatefittervale: currentFilter,
        morefilltersall: morefilltersall,

    },
    success: function (responseData) {
        handleSuccess(responseData,3);
    },
    error: function (xhr, status, error) {
        $("#document_tbl").hide();
        console.error("AJAX Error:", status, error);
    }
});
}


$(document).on('click', '.att_options_marketers div', function () {
$(".my_value_views").text("");
  $('.clear_my_views').show();
$(".my_search_view").show();
var moredatefittervale = $('#mydateallviews').text();

let resultsArray_marketer = [];
// Collect the values from the search inputs
$(".attendancedatasearch").each(function () {

    const value = $(this).val();
    if (value !== "") {
        const result =  $(this).attr('name') + "='" + value + "'";
        resultsArray_marketer.push(result);
    }
});

// Additional filter values
const moreFilterValues_market = [
    $("#att_zone_views").val(),
];
// alert(moreFilterValues_market);
// Update the UI with the selected filter values
$(".value_views_mysearch").each(function (index) {
    const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
    $(this).text(filterValue);
});

// Prepare data for the filter function
 fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));

// Call function with the processed data
secmorefilterview(fitterremovedata,3,attendancedetialsUrl,moredatefittervale);

});

$(document).on("click", ".value_views_mysearch", function () {
var moredatefittervale = $('#mydateallviews').text();	
var morefillterremvedata = $(this).text().replace(/, /g, ",");
var clear_filtr = $(this).attr('id');		
$(this).text("");

if(clear_filtr == 'mybranch_search'){
    $('#att_zone_views').val('');	
}

// Update the uniqueResults array to remove the corresponding filter
fitterremovedata = fitterremovedata.filter(function (item) {
return !item.trim().includes(morefillterremvedata.trim() + "'");
});			
  secmorefilterview(fitterremovedata,3,attendancedetialsUrl,moredatefittervale);
});

$(document).on("click", ".clear_my_views", function () {

fitterremovedata.length = 0;		
$('.attendancedatasearch').val("")
$(".my_value_views").text("");
$('.morefittersclr').val("");
$('.clear_my_views').hide();
$(".my_search_view").hide();
$(".value_views_mysearch").text("");
attendance_fetch();
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
    if (key === 'hms_attendance.att_from_date') {
        $('#search_att_fromdate').val(''); 
    }

    // Remove the filter from the list (cleanup logic)
    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim());
    });

    // Call function to refresh view based on updated filters
    secmorefilterview(fitterremovedata, 3, attendancedetialsUrl, moredatefittervale);
});


var fitterremovedata = [];	
$("#attendance_search").on("click", function () {
     $(".my_search_view").show();
	  $('.clear_my_views').show();
      $(".value_views_mysearch").text("");
      $(".attendancedatasearch").val('');
      let sec_date_type =  $("#search_att_fromdate").val(); 
      var moredatefittervale = $('#mydateallviews').text();	
	   var resultsArray = [];
        $(".morefittersclr").each(function () {

            var value = $(this).find(":selected").text();
			if(value == "Select Date"){
			  value = "";
			}

			if (value === "") {
				return; // Skip this iteration if the value is empty
			}
			var results = $(this).attr('name') + "='" + value.replace(/\s*-\s*/, '-').trim() + "'";
			
			resultsArray.push(results);

			   fitterremovedata=resultsArray;
		   
				});
			fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
			   fitterremovedata;
	  
	  var moreFilterValues = [
            $("#search_att_fromdate").val()
        ];

	 $(".my_value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; 
		 
            $(this).text(morefillterdata);

        });	
		ticketFilterAjax(sec_date_type,attendancedateUrl,3,moredatefittervale);	
    });	
    function ticketFilterAjax(sec_date_type,url,urlstatus, moredatefittervale){
        $.ajax({
                url: url,
                type: "GET",
                data: {
                    sec_date_type: sec_date_type,
                    moredatefittervale:moredatefittervale,	
                },
                success: function (responseData) {
                    handleSuccess(responseData,urlstatus);
                },
                error: function (xhr, status, error) {
                    $("#ticket_details1").hide();
                    console.error("AJAX Error:", status, error);
                }
          });
    }
