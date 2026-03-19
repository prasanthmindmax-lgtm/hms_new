$(document).ready(function () {
    licensedoc_fetch();

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
    $(".documentscls div").click(function () {
        var selectedText = $(this).text(); // Get selected city name
        var selectedValue = $(this).attr('data-value'); // Get data-value attribute
        // alert(selectedValue);

        $('#zone_id').val(selectedText); // Set input value (visible text)
        $('#zone_id').attr('data-selected-value', selectedValue); // Store data-value in a custom attribute
    });


    $('#submit-document_data').click(function (event) {

        event.preventDefault();
        let isValid = true;
        if ($('#zone_id').val() === "") {
            $('.error_location').text('Enter the location Name');
            isValid = false;
        }
        if ($('#document_typename').val() === "") {
            $('.error_doc_type').text('Please select the document Type');
            isValid = false;
        }
        if ($('#document_name').val() === "") {
            $('.error_doc_name').text('Please select the document Name');
            isValid = false;
        }
        //  if ($('#expire_date').val() === "") {
        //     $('.error_exdate').text('Please select the Expire Date');
        //     isValid = false;
        // }
         if ($('#pf_uploads').val() === "") {
            $('.error_pdf').text('Please upload pdf file');
            isValid = false;
        }
        if (!isValid) {
            return;
        }
        // Create FormData object
        let formData = new FormData();
        formData.append('document_type_name', $('#document_typename').val());
        // formData.append('document_name', $('#document_name').val());
        formData.append('expire_date', $('#expire_date').val());
        // formData.append('zone_id', $('#zone_id').val());
        let zoneId = $('#zone_id').attr('data-value');
        formData.append('zone_id', zoneId);
        let document_typeId = $('#document_name').attr('data-value');
        formData.append('document_type_id', document_typeId);
        // Add images to FormData
        const files = $('#pf_uploads')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }
        // Include CSRF Token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        // AJAX Request
        $.ajax({
            url: documentaddedUrl,
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
                   $('#document_typename').val('');
                   $('#zone_id').val('');
                   $('#document_name').val('');
                   $('#expire_date').val('');
                   $('#pf_uploads').val('');
                   licensedoc_fetch();
            },
            error: function (error) {
                console.error(error.responseJSON);
            },
        });
    });
    $('.dropdown-item-loc').on('click', function () {
        var selectedBranchName = $(this).text();
        var selectedBranchId = $(this).data('value');
        $('#zone_id').val(selectedBranchName);
        $('#zone_id').attr('data-value', selectedBranchId);
    });


     $('#itemsPerPageSelectdocument').change(function() {
        var pageSizedocuments = parseInt($(this).val());
        renderPaginationdocument(dataSourcedocument, pageSizedocuments);
        renderTabledocument(dataSourcedocument, pageSizedocuments, 1);
    });
    $(document).on('click', '.documentclk', function (e) {
        $('#exampleModal1').modal('show');
        var row = $(this).closest('tr');
        var pdffiles = row.find('#pffiles').text();
        pdffiles = pdffiles.replace(/[\[\]\\"]/g, '');

        var imageArray = pdffiles.split(',').map(item => item.trim());
        var firstImage = imageArray[0];
        var imageNamefirst = firstImage.split('/').pop();
        var safePath = encodeURI("../public/document_data/" + imageNamefirst);
        // var safePath = encodeURI("../document_data/" + imageNamefirst);

        // Set the first PDF in the iframe
        $('#pfmain').attr('src', safePath);

        // Generate buttons for each PDF
        var views = '';
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop().replace(/\\/g, ''); // Extra cleanup
            views += '<button style="font-size: 11px;" type="button" class="btn btn-primary pdf-btn" data-file="' + encodeURIComponent(imageName) + '">' + imageName + '</button>';
        });
        $('#button_pdfs').html(views);

		$(document).on('click', '.pdf-btn', function() {
            $('.pdf-btn').removeClass('active');
            $(this).addClass('active');

            var filename = $(this).data('file');
            $('#pfmain').attr('src', "../public/document_data/" + filename);
            // $('#pfmain').attr('src', "../document_data/" + filename);
        });

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
      $('#pfmain').attr('src', "../public/document_data/" + fetchvalue);
    //   $('#pfmain').attr('src', "../document_data/" + fetchvalue);
    });
    $("#submit-document_update").on("click",function(){
        var updated_id=$("#docu_id").text();
        let formData = new FormData();
        formData.append('id', $('#id_document').val());
        formData.append('expire_date', $('#expire_update_date').val());
        formData.append('document_type', $('#update_documents_all').val());
        // formData.append('expire_dates', $('#expire_dates').val());
         // Add images to FormData
         const files = $('#pf_update')[0].files;
         for (let i = 0; i < files.length; i++) {
             formData.append('images[]', files[i]);
         }
           // Include CSRF Token
           formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
           // AJAX Request
           $.ajax({
               url: documentupdatedUrl,
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
                   $("#exampleModal2").modal('hide');
                   licensedoc_fetch();
               },
               error: function (error) {
                   console.error(error.responseJSON);
               },
           });
    });
    // documentoveralldata();
    document_branchwise();
});
var dataSourcedocument = [];
function documentoveralldata()
{
    $.ajax({
        url: fetchUrldocument,
        type: "GET",
        success: function (responseData) {
            console.log(responseData);
            dataSourcedocument = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val()); // Get selected items per page
            renderPaginationdocument(dataSourcedocument, pageSizedocuments);
            renderTabledocument(dataSourcedocument, pageSizedocuments, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function document_branchwise()
{
    $.ajax({
        url: branchurl,
        type: "GET",
        success: function (responseData) {
            slct="";
            slctview="";
            slctzone="";
            console.log(responseData);
            $.each(responseData, function(index, user) {
                if (!slct.includes('<option value="">Select Branch</option>')) {
                    slct += '<option value="">Select Branch</option>';
                }
                slct += '<option data-id="' + user.id + '" value="' + user.id + '">' + user.Branch_name + '</option>';
                if (!slctzone.includes('<option value="">Select Branch</option>')) {
                    slctzone += '<option value="">Select Branch</option>';
                }
                slctzone += '<option data-id="' + user.zone,+ '" value="' + user.zone+ '">' + user.zone, + '</option>';
                slctview += '<label><input type="checkbox" value="' + user.Branch_name + '" onchange="updateSelectedValues()">'+ user.Branch_name +'</label>';
            });
            $("#zone_id").html(slct);
            $("#branchviews").html(slctview);
            $(".zoneid").html(slctzone);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
// Render table rows based on the page and page size
function renderTabledocument(data, pageSizedocuments, pageNum) {
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);
    //console.log(pageData);
    var body = "";
    var uniqueZones = [];
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD/MM/YYYY");
        let expire_date = moment(user.expire_date).format("DD/MM/YYYY");
        var view = expire_date || "-" ;
        body += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="idfetch" data-id="'+ user.id +'"> <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview"  data-filesview="' + user.document_type + '">' + user.name + '<br>' + user.zone_name+ '<br></td>' +
               '<td style="display:none;" id="pffiles" class="tdview" >'+ user.document_type +'</td>' +
               '<td style="display:none;"id="expiredatess" class="tdview" >'+ user.expire_date +'</td>' +
               '<td class="tdview">'+user.doc_type +'</td>'+
               '<td class="tdview" >'+ user.doc_name +'</td>' +
               '<td class="tdview documentclk"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>' +
                '<td class="tdview upload_document"  ><img src="../assets/images/policy.png" style="width: 35px;"  alt="Icon" class="icon"></td>' +
                '<td class="tdview" ><a href="#">'+ view +'</a></td>' +
                '<td class="tdview" >'+ user.created_by +'</td>' +
                //  '<td class="tdview"><img src="../assets/images/delete.png" style="width: 20px;" alt="Icon" class="" title="Delete"></td>'
               '</tr>';  });
                if (pageData.length === 0) {
            body += '<tr><td colspan="13" class="tdview" style="text-align: center;">No data available</td></tr>';
        }
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

function licensedoc_fetch(statusid) {
		var moredatefittervale = $('#mydateallviews').text();
		$(".value_views_mysearch").text("");
		$("#my_ticket_details1").show();
		$.ajax({
			url: licensedocdetialsUrl,
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
            licensedoc_fetch();
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
                url: licensedocdetialsUrl,
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

    // debugger
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

    // Additional filter values
    const moreFilterValues_market = [
        $("#lic_zone_views").val(),
        $('#lic_loc_views').val(),
        $('#selecttypeoptions').val(),
        $("#lic_name_views").val(),
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
    secmorefilterview(fitterremovedata,3,licensedocdetialsUrl,moredatefittervale);

});

$(document).on("click", ".value_views_mysearch", function () {
    var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");

    if(clear_filtr == 'lic_my_search'){
        $('#selecttypeoptions').val('');
    }
    if(clear_filtr == 'licdocname_search'){
        $('#lic_name_views').val('');
    }
    if(clear_filtr == 'licbranch_search'){
        $('#lic_loc_views').val('');
    }
    if(clear_filtr == 'myzone_search'){
        $('#lic_zone_views').val('');
    }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      secmorefilterview(fitterremovedata,3,licensedocdetialsUrl,moredatefittervale);
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
    licensedoc_fetch();
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
            if (key === 'hms_document_manage.expire_date') {
                $('#search_expire_date').val('');
            }

            // Remove the filter from the list (cleanup logic)
            fitterremovedata = fitterremovedata.filter(function (item) {
                return !item.trim().includes(morefillterremvedata.trim());
            });

            // Call function to refresh view based on updated filters
            secmorefilterview(fitterremovedata, 3, licensedocdetialsUrl, moredatefittervale);
        });


var fitterremovedata = [];
$("#license_search").on("click", function () {
     $(".my_search_view").show();
	  $('.clear_my_views').show();
      $(".value_views_mysearch").text("");
      $(".documentdatasearch").val('');
      $(".documnettypesearch").val('');
      let sec_date_type =  $("#search_expire_date").val();
    //   var moredatefittervale = $('#mydateallviews').text();
	   var resultsArray = [];
        $(".morefittersclr").each(function () {

            var value = $(this).find(":selected").text();

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
            $("#search_expire_date").val()
        ];

	 $(".my_value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : "";

            $(this).text(morefillterdata);

        });
		ticketFilterAjax(sec_date_type,licexpdateUrl,3);
    });
    function ticketFilterAjax(sec_date_type,url,urlstatus){
        $.ajax({
                url: url,
                type: "GET",
                data: {
                    sec_date_type: sec_date_type,
                    // moredatefittervale:moredatefittervale,
                },
                success: function (responseData) {
                    handleSuccess(responseData,urlstatus);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
          });
    }

