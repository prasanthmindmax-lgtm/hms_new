$(document).ready(function() {
    // Fetch data and initialize pagination
    overall_fetch(1);
    $(".search_view").hide();
	$('#start_date').hide();
	$('#end_date').hide();
	 var domainUrl = window.location.origin;

function  morefilltersremoveviews(fitterremovedata,datefilltervalue)
{

    if(fitterremovedata!="")
    {
        var fitterremovedataall=fitterremovedata.join(" AND ");
   

   
   $.ajax({
    url: fetchUrlticketfitterremove,
    type: "GET",
    data: {
        fitterremovedataall: fitterremovedataall,
		moredatefittervale:datefilltervalue,
    },
    success: function (responseData) {
        $("#ticket_details1").hide();
        $("#ticket_details").show();
        ticketdataSource = responseData; 
        totalItems = responseData.length; // Set the data fetched from the server
        var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
    },
    error: function (xhr, status, error) {
        $("#ticket_details1").hide();
        console.error("AJAX Error:", status, error);
    }
  });

   }else
   {

      $('.clear_all_views').hide();
      $(".search_view").hide();
      overall_fetch(2);
   }

}

	var ticketdataSource = [];  // Data will be fetched here
    var marketersearchvalue = [];
	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_minsearch").text("");
		$.ajax({
			url: ticketfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid
			},
			success: function (responseData) {
				$("#ticket_details1").hide();
				$("#ticket_details").show();
				ticketdataSource = responseData; 
				totalItems = responseData.length; // Get total items count // Set the data fetched from the server
				var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
				ticketrenderPagination(ticketdataSource, ticketpageSize);
				ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
			},
			error: function (xhr, status, error) {
				$("#ticket_details1").hide();
				console.error("AJAX Error:", status, error);
			}
		});
	}	
	
var fitterremovedata = [];	
$("#ticket_search").on("click", function () {
	  $(".search_view").show();
	  $('.clear_all_views').show();
	  $(".value_views_minsearch").text("");
	  $('.marketervalues_search').val('');
      let dateVal=$("#dateallviews").text();
	  let statusValues = $(".statusCheckbox:checked").map(function () {
                return $(this).val(); // Get value of checked checkboxes
            }).get(); // Convert to an array
			statusValues = statusValues.join(",");
	  let priorityValues = $(".priorityCheckbox:checked").map(function () {
                return $(this).val(); // Get value of checked checkboxes
            }).get(); // Convert to an array
			priorityValues = priorityValues.join(",");
      let location_id =  $('.loct-dropdown-options div.selected').attr('data-value');    
      let dateType =  $("#dateType").val(); 
      let startDate =  $("#startDate").val(); 
      let endDate =  $("#endDate").val();
	  
	   var resultsArray = [];
        $(".morefittersclr").each(function () {

            var value = $(this).val();
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
       
            $('.statusCheckbox:checked').map(function() {
				return $(this).closest('label').text().trim();
			}).get(),
            $('.priorityCheckbox:checked').map(function() {
				return $(this).closest('label').text().trim();
			}).get(),
            $('.loct-dropdown-options div.selected').text(),
            $("#dateType option:selected").text(),
            $("#startDate").val(),
            $("#endDate").val()
        ];
		
	 $(".value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
         
		  if(morefillterdata == "Select Location"){
			  morefillterdata = "";
		  }
		  if(morefillterdata == "Select Date Type"){
			  morefillterdata = "";
		  }
            $(this).text(morefillterdata);

        });		
	  
		$.ajax({
			url: ticketfillter,
			type: "GET",
			data: {
				dateVal: dateVal,
				statusValues: statusValues,
				priorityValues: priorityValues,
				location_id: location_id,
				startDate: startDate,
				dateType: dateType,
				endDate: endDate,
			},
			success: function (responseData) {
				$("#ticket_details1").hide();
				$("#ticket_details").show();
				ticketdataSource = responseData; 
				totalItems = responseData.length; // Set the data fetched from the server
				var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
				ticketrenderPagination(ticketdataSource, ticketpageSize);
				ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
			},
			error: function (xhr, status, error) {
				$("#ticket_details1").hide();
				console.error("AJAX Error:", status, error);
			}
	  });
        
    });	
	
    $(document).on('click', '.options_marketrs div', function (e) {
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
        var moredatefittervale = $('#dateallviews').text();

        $('.clear_all_views').show();
        $(".search_view").show();
        $(".value_views").text("");
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
                $("#deprt_search").val(),
            ];

            $(".value_views_minsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
				
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale);
        });
	
	// date fitter function
function ticketdatefillterrange(datefiltervalue,fitterremovedata) {
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
    $("#ticket_details1").show();
    $.ajax({
        url: ticketdatefillter,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
			morefilltersall: morefilltersall,
   
        },
        success: function (responseData) {
            $("#ticket_details1").hide();
            $("#ticket_details").show();
            ticketdataSource = responseData; 
            totalItems = responseData.length; // Set the data fetched from the server
            var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            ticketrenderPagination(ticketdataSource, ticketpageSize);
            ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#ticket_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}

function morefilterview(uniqueResults,moredatefittervale) {
 if(uniqueResults!="")
    {
    var morefilltersall=uniqueResults.join(" AND ");

    $.ajax({
        url: fetchUrlmorefitter,
        type: "GET",
        data: {
            morefilltersall: morefilltersall,
            moredatefittervale:moredatefittervale,
   
        },
        success: function (responseData) {
            $("#ticket_details1").hide();
            $("#ticket_details").show();
            ticketdataSource = responseData; 
            totalItems = responseData.length; // Set the data fetched from the server
            var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            ticketrenderPagination(ticketdataSource, ticketpageSize);
            ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#ticket_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
	}else
   {

      $('.clear_all_views').hide();
      $(".search_view").hide();
      overall_fetch(2);
   }
}

// Render pagination controls based on data
function ticketrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';

    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="page-bttn " style="background-color:#6a6ee4;color: #fff;"  data-page="' + i + '">' + i + '</button>';
    }

    $('#ticketpagination').html(paginationHtml);

    // Bind click event to each pagination button
    $('.page-bttn').click(function() {
        var pageNum = $(this).data('page');
        $('.page-bttn').removeClass('active');
        $(this).addClass('active');
        ticketrenderTable(data, ticketpageSize, pageNum);
    });
}

	// Render table rows based on the page and page size
	function ticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);

		var body = "";
		$.each(pageData, function(index, user) {
			let dateStr = user.created_at;
			let target_date = user.target_date;		
			let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
			let	targetedDate = moment(target_date).format("DD MMM YYYY");
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"> <a href='+domainUrl+'/staff/ticketActivity/'+user.ticket_no+'>'  + user.ticket_no + '</a><br>' + formattedDate +'</td>' +
				   '<td class="tdview"> ' + user.name + '</td>' +
				   '<td class="tdview" id="_fetch" data-department_id="' + user.department_id + '">'+ user.from_department_name +'</td>' +
				   '<td class="tdview" id="_fetch" data-department_id="' + user.department_id + '">'+ user.depart_name +'</td>' +
				   '<td class="tdview">' + user.sub_category_name + '</td>' +
				   '<td class="tdview" id="target_date" data-target_date="' + targetedDate + '" >' + targetedDate + '</td>' +
				   '<td class="tdview" id="priority" data-priority="' + user.priority + '">' + user.priority_name + '</td>' +
				  '<td class="tdview Tooltip_Text_container_subject" id="Tooltip_Text_container">' +
						'<i class="fas fa-info-circle" style="cursor: pointer; color: #007bff;"></i>' +
						 '<a href="#"><span class="tooltips" style="z-index:9999;margin-left: -2em;width: 187px;text-align: justify;"><b>' + user.subject + '</b> </span></a></td>' +
				  '<td class="tdview Tooltip_Text_container_description" id="Tooltip_Text_container">' +
						'<i class="fas fa-info-circle" style="cursor: pointer; color: #007bff;"></i>' +
						 '<a href="#"><span class="tooltips" style="z-index:9999;margin-left: -2em;width: 187px;text-align: justify;"><b>' + user.description + '</b> </span></a></td>' +
				   '<td class="tdview" id="fullname" data-fullname="' + user.user_fullname + '">' + user.user_fullname + '</td>' +
				   '<td style="display:none" class="tdview imagecheck" value="' + user.id + '"><a href="#">Ticket Images</a></td>' +
				   '<td style="display:none" class="tdview" id="imageviews">' + user.image_paths + '</td>' +
				   '<td class="tdview">';

				if (user.status_name === "Open") {
					body += '<span class="closed-badge" style="background: #6b75a8;color: #fff;padding: 5px 10px;border-radius: 4px; font-size: 9px;">' + user.status_name + '</span>';
				}else if (user.status_name === "Waiting for Approval") {
					body += '<span class="closed-badge" style="background: #bd478c;color: #fff;padding: 5px 10px;border-radius: 4px; font-size: 9px;">' + user.status_name + '</span>';
				}else if (user.status_name === "In-progress") {
					body += '<span class="closed-badge" style="background: #ecca51;color: #fff;padding: 5px 10px;border-radius: 4px; font-size: 9px;">' + user.status_name + '</span>';
				}else if (user.status_name === "Rejected") {
					body += '<span class="closed-badge" style="background: #ee2f2f;color: #fff;padding: 5px 10px;border-radius: 4px; font-size: 9px;">' + user.status_name + '</span>';
				} else {
					body += '<span class="new-badge">' + user.status_name + '</span>';
				}

				body += '</td>' +
					   '</tr>';

		});

		$("#ticket_details").html(body);
		$("#today_visits").text(totalItems);
		$("#counts").text(totalItems);
	}
	$('.btn-close, #close-button').on('click', function() {
		location.reload();
	});

    $('.ranges, .applyBtn').on('click', function() {
		console.log(fitterremovedata);
        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
            var datefilltervalue = $('#dateallviews').text(); 
            ticketdatefillterrange(datefilltervalue,fitterremovedata);
            
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
            ticketdatefillterrange(datefilltervalue,fitterremovedata);
            
        }
    });
	
    $(document).on("click", ".value_views", function () {
		
        var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();
        $(this).text("");
		
		// Find the element that contains the value
		let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

		if (indexToRemove !== -1) {
		  // Remove the element at the found index
		  var removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // splice returns an array, so we access the first element	  
		}
		
		// Split the string at the first '=' and get the part before it
			let key = removedElement.split('=')[0];
			if(key == 'ticket_status_master.status_name'){
				const checkboxes = document.querySelectorAll('.statusCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('selectedStatus').value = '';
			}
			
			if(key == 'ticket_priority.priority_name'){
				const checkboxes = document.querySelectorAll('.priorityCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('priorityStatus').value = '';
			}
			
			if(key == 'tbl_locations.name'){
				const selectedDiv = document.querySelector('.loct-dropdown-options .selected');
				if (selectedDiv) {
					selectedDiv.classList.remove('selected');
				}
				$('#locationInput').val('');				
			}
		
		$('.morefittersclr').filter(function () {
			return $(this).val().startsWith(morefillterremvedata);
		}).val("");
			
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });	
	var datefilltervalue = $('#dateallviews').text();
          morefilltersremoveviews(fitterremovedata,datefilltervalue);
   
    });
	
    $(document).on("click", ".value_views_minsearch", function () {
		var datefilltervalue = $('#dateallviews').text();	
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
		var clear_filtr = $(this).attr('id');		
        $(this).text("");
		if(clear_filtr == 'created_by_search'){
			$('#marketer_fetch').val('');	
		}
		if(clear_filtr == 'branch_search'){
			$('#branchviews').val('');	
		}
		if(clear_filtr == 'zone_search'){
			$('#zoneviews').val('');	
		}
		if(clear_filtr == 'depart_search'){
			$('#deprt_search').val('');	
		}
		$('.marketervalues_search').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");		

       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
          morefilterview(fitterremovedata,datefilltervalue);
   
    });


    $(document).on("click", ".clear_all_views", function () {
		
		const checkboxes = document.querySelectorAll('.statusCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('selectedStatus').value = '';
				
		const pcheckboxes = document.querySelectorAll('.priorityCheckbox');
				// Loop through each checkbox and uncheck it
				pcheckboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('priorityStatus').value = '';
				
		const selectedDiv = document.querySelector('.loct-dropdown-options .selected');
				if (selectedDiv) {
					selectedDiv.classList.remove('selected');
				}	 
				
		$('.marketervalues_search').val("")
        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_all_views').hide();
        $(".search_view").hide();
		$(".value_views_minsearch").text("");
        overall_fetch(2);
    
    });
		
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
});


$(document).on('change', '#dateType', function() {
	let date = $(this).val();
	if (date != '') {
		$('#start_date').show();
		$('#end_date').show();
	} else {
		$('#start_date').hide();
		$('#end_date').hide();
	}
});
