$(document).ready(function () {

    security_fetch(1);

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

      $('#submit-security_details').click(function (event) {
          event.preventDefault();
          let formData = new FormData();

          formData.append('sec_name', $('#sec_name').val());
          formData.append('sec_phone', $('#sec_phone').val());
          formData.append('sec_address', $('#sec_address').val());
          formData.append('sec_shift',$('#sec_shift').val());
          formData.append('sec_joining_date', $('#sec_joining_date').val());
          // formData.append('zone_id', $('#zone_id').val());
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
              url: securitydaily_documentaddedUrl,
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
                $('#sec_name').val('');
                $('#sec_phone').val('');
                $('#sec_address').val('');
                $('#sec_shift').val('');
                $('#sec_joining_date').val('');
                $('#zone_id').attr('data-value', '');
                $('#pf_uploads').val('');
                $("#exampleModaluser").modal('hide');
                security_fetch(1);
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

    $(document).on('click', '.documentclk', function (e) {
        $('#exampleModal1').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pffiles').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
		// $('#pfmain').attr('src', "../public/document_data/" + imageNamefirst);
        $('#pfmain').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdffetch" class="btn btn-primary pdf-btn">'+ imageNames +'</button>';
        });
        $('#button_pdfs').html("");
        $('#button_pdfs').html(views);

		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn').removeClass('active');
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });

    $(document).on("click" , '.edit_sec_details', function (e) {
        $('#exampleModal2').modal('show');

  var id = $(this).closest('tr').find('#idfetch').data('id');
 // alert(id);
        $.ajax({
            url: "/security_detials_edit/" + id,
            type: "GET",
            success: function(response) {
                $('.location-dropdown-options div').removeClass('selected'); // clear previous
    var $selectedItem = $('.location-dropdown-options div[data-value="'+response.zone_id+'"]');
    $selectedItem.addClass('selected');

    // Get the selected branch name
    var selectedText = $selectedItem.text();

    // Set both value and data-value on the input field
    $('#edit_zone_id')
        .val(selectedText)
        .attr('data-value', response.zone_id); // this line sets the data-value

                $('#edit_id').val(response.sec_id);
                $('#edit_sec_name').val(response.sec_name);
                $('#edit_sec_phone').val(response.sec_phone);
                $('#edit_sec_shift').val(response.sec_shift);
                $('#edit_sec_joining_date').val(response.sec_joining_date);
                $('#edit_sec_address').val(response.sec_address);
               $('#pf_update').val(response.sec_id_proof);

            }
        });
    });
    $(document).on('click', '#pdffetch', function (e) {
        fetchvalue=$(this).text();
    //   $('#pfmain').attr('src', "../public/document_data/" + fetchvalue);
      $('#pfmain').attr('src', "../document_data/" + fetchvalue);
    });
    $("#edit_security_details").on("click",function(){
        let formData = new FormData();
        formData.append('sec_id', $('#edit_id').val());
        formData.append('sec_name', $('#edit_sec_name').val());
        formData.append('sec_phone', $('#edit_sec_phone').val());
        formData.append('sec_address', $('#edit_sec_address').val());
        formData.append('sec_shift',$('#edit_sec_shift').val());
        formData.append('sec_joining_date', $('#edit_sec_joining_date').val());
        let zoneId = $('#edit_zone_id').attr('data-value');
        formData.append('zone_id', zoneId);
        console.log(formData);
         // Add images to FormData
         const files = $('#pf_update')[0].files;
         for (let i = 0; i < files.length; i++) {
             formData.append('images[]', files[i]);
         }
           // Include CSRF Token
           formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
           // AJAX Request
           $.ajax({
               url: securityeditUrl,
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
                security_fetch(1);
            },
               error: function (error) {
                   console.error(error.responseJSON);
               },
           });
    });
});
$(document).on('click', '.delete_sec_details', function(e){
e.preventDefault();
var id = $(this).closest('tr').find('#idfetch').data('id');

$.ajax({
url:"/security_detials_delete/" + id,
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
    security_fetch(1);
}
});
});

var dataSourcedocument = [];
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
        body += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="idfetch" data-id="'+ user.sec_id +'"> <strong>#' + user.sec_id + '</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview"  data-filesview="' + user.sec_name + '">' + user.name + '<br>' + user.zone_name+ '<br></td>' +
                '<td style="display:none;" id="pffiles" class="tdview" >'+ user.sec_id_proof +'</td>' +
               '<td class="tdview" >'+ user.sec_name +'</td>' +
               '<td class="tdview" >'+ user.sec_phone +'</td>' +
               '<td class="tdview" >'+ user.sec_address +'</td>' +
               '<td class="tdview" >'+ user.sec_shift +'</td>' +
               '<td id="expiredatess" class="tdview" >'+ user.sec_joining_date +'</td>' +
               '<td class="tdview documentclk"><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>' +
               '<td class="tdview"><img src="../assets/images/edit.png" style="width: 25px;margin-right:5px;"  alt="Icon" class="icon edit_sec_details">'+
               '<a href="#"><img src="../assets/images/delete.png" style="width: 25px;"  alt="Icon" class="icon delete_sec_details"></a>'+
                '</td>' +

                //  '<td class="tdview"><img src="../assets/images/delete.png" style="width: 20px;" alt="Icon" class="" title="Delete"></td>'
               '</tr>';  });
    $("#document_tbl").html(body);
    $("#mycounts").text(totalItems);
}

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


function security_fetch(statusid) {
		var moredatefittervale = $('#mydateallviews').text();
		$(".value_views_mysearch").text("");
		$("#my_ticket_details1").show();
		$.ajax({
			url: securitydetialsUrl,
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
	}
    function secmorefilterview(uniqueResults,urlstatus,url,moredatefittervale) {
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
            security_fetch(2);
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
        url: securityfillterdataUrl,
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


   $(document).on('click', '.sec_options_marketers div', function () {
    $(".my_value_views").text("");
      $('.clear_my_views').show();
    $(".my_search_view").show();
    var moredatefittervale = $('#mydateallviews').text();

    // Initialize an array to hold the filtered results
    let resultsArray_marketer = [];
    // Collect the values from the search inputs
    $(".securitydatasearch").each(function () {

        const value = $(this).val();
        if (value !== "") {
            const result =  $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });


    // Additional filter values
    const moreFilterValues_market = [
        $("#sec_name_views").val(),
        $("#sec_zone_views").val(),
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
    secmorefilterview(fitterremovedata,3,securityfillterdataUrl,moredatefittervale);

});

$(document).on("click", ".value_views_mysearch", function () {
    var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");
    if(clear_filtr == 'created_my_search'){
        $('#sec_name_views').val('');
    }
    if(clear_filtr == 'mybranch_search'){
        $('#sec_zone_views').val('');
    }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      secmorefilterview(fitterremovedata,3,securityfillterdataUrl,moredatefittervale);
});

$(document).on("click", ".my_value_views", function () {
	var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();

    $(this).text("");
    let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

    if (indexToRemove !== -1) {
      // Remove the element at the found index
      var removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // splice returns an array, so we access the first element
    }

    // Split the string at the first '=' and get the part before it
        let key = removedElement.split('=')[0];

        if(key == 'security_details.sec_shift'){
            $('#shift_type').prop('selectedIndex', 0);
        }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
secmorefilterview(fitterremovedata,3,securityfillterdataUrl,moredatefittervale);

});


$(document).on("click", ".clear_my_views", function () {

    fitterremovedata.length = 0;
    $('.securitydatasearch').val("")
    $(".my_value_views").text("");
    $('.morefittersclr').val("");
    $('.clear_my_views').hide();
    $(".my_search_view").hide();
    $(".value_views_mysearch").text("");
    security_fetch(2);
});

var fitterremovedata = [];
$("#security_searchdata").on("click", function () {
     $(".my_search_view").show();
	  $('.clear_my_views').show();
      $(".value_views_mysearch").text("");
      $(".securitydatasearch").val('');
      let sec_shift_type =  $("#shift_type").val();
      var moredatefittervale = $('#mydateallviews').text();
	   var resultsArray = [];
        $(".morefittersclr").each(function () {

            var value = $(this).find(":selected").text();
			if(value == "Select Shift"){
			  value = "";
			}
			// Check if the value is not empty before processing
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
            $("#shift_type").find(":selected").text()
        ];

	 $(".my_value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty

		  if(morefillterdata == "Select Shift Type"){
			  morefillterdata = "";
		  }

            $(this).text(morefillterdata);

        });
		ticketFilterAjax(sec_shift_type,securityshiftfillUrl,3,moredatefittervale);
    });
    function ticketFilterAjax(sec_shift_type,url,urlstatus, moredatefittervale){
        $.ajax({
                url: url,
                type: "GET",
                data: {
                    sec_shift_type: sec_shift_type,
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