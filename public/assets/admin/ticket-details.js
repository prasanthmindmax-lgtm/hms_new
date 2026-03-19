$(document).ready(function() {
    // Fetch data and initialize pagination
    overall_fetch(1);
    my_ticket_fetch(1);
    $(".search_view").hide();
	$(".my_search_view").hide();
	$('#start_date').hide();
	$('#end_date').hide();
	
	 var domainUrl = window.location.origin;

function  morefilltersremoveviews(fitterremovedata,url,urlstatus,datefilltervalue)
{

    if(fitterremovedata!="")
    {
        var fitterremovedataall=fitterremovedata.join(" AND ");
   

   
   $.ajax({
    url: url,
    type: "GET",
    data: {
        fitterremovedataall: fitterremovedataall,
		 moredatefittervale:datefilltervalue,

    },
    success: function (responseData) {
        handleSuccess(responseData,urlstatus);
    },
    error: function (xhr, status, error) {
        $("#ticket_details1").hide();
        console.error("AJAX Error:", status, error);
    }
  });

   }else
   {
	if(urlstatus == 1){
      $(".search_view").hide();
	  $('.clear_views').hide();
      overall_fetch(2);
	}else{
		$('.clear_my_views').hide();
      $(".my_search_view").hide();
      my_ticket_fetch(2);
	}
   }

}

function morefilterview(uniqueResults,moredatefittervale,urlstatus,url) {
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
            $("#ticket_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
	}else
   {
	if(urlstatus == 1){
      $(".search_view").hide();
	  $('.clear_views').hide();
      overall_fetch(2);
	}else{
		$('.clear_my_views').hide();
      $(".my_search_view").hide();
      my_ticket_fetch(2);
	}
   }
}
	
	var ticketdataSource = [];  // Data will be fetched here

	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_mainsearch").text("");
		$("#ticket_details1").show();
		$.ajax({
			url: ticketfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid
			},
			success: function (responseData) {
				handleSuccess(responseData,1);
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	}
	
	// Fetch the data and initialize pagination
	function my_ticket_fetch(statusid) {
		var moredatefittervale = $('#mydateallviews').text();
		$(".value_views_mysearch").text("");
		$("#my_ticket_details1").show();
		$.ajax({
			url: myticketfetchUrl,
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
	var fitterremovedata = [];
	$("#ticket_search").on("click", function () {
	  $(".search_view").show();
	  $('.clear_views').show();
	  $(".value_views_mainsearch").text("");
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
		  let location_id =  $(".loct-dropdown-options div.selected").attr('data-value');      
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
				$(".loct-dropdown-options div.selected").text(),
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
	  ticketFilterAjax(dateVal,statusValues,priorityValues,location_id,dateType,startDate,endDate,ticketfillter,1);
    });	
	
	$("#my_ticket_search").on("click", function () {
	   $('.my_search_view').show();
	  $('.clear_my_views').show();
	  $(".value_views_mysearch").text("");
	  $('.mymarketer_search').val('');
	  let dateVal=$("#mydateallviews").text();
		  let statusValues = $(".mystatusCheckbox:checked").map(function () {
					return $(this).val(); // Get value of checked checkboxes
				}).get(); // Convert to an array
				statusValues = statusValues.join(",");
		  let priorityValues = $(".mypriorityCheckbox:checked").map(function () {
					return $(this).val(); // Get value of checked checkboxes
				}).get(); // Convert to an array
				priorityValues = priorityValues.join(",");
		  let location_id =  $(".myloct-dropdown-options div.selected").attr('data-value');      
		  let dateType =  $("#mydateType").val(); 
		  let startDate =  $("#mystartDate").val(); 
		  let endDate =  $("#myendDate").val();
		  
		  var resultsArray = [];
        $(".myfittersclr").each(function () {

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

			var moreFilterDatas = [
		   
				$('.mystatusCheckbox:checked').map(function() {
					return $(this).closest('label').text().trim();
				}).get().join(","),
				$('.mypriorityCheckbox:checked').map(function() {
					return $(this).closest('label').text().trim();
				}).get().join(","),
				$(".myloct-dropdown-options div.selected").text(),
				$("#mydateType option:selected").text(),
				$("#mystartDate").val(),
				$("#myendDate").val()
			];
			
	 $(".my_value_views").each(function (index) {		
			var morefilltervalue = moreFilterDatas[index] ? moreFilterDatas[index] : ""; // Use "N/A" if value is empty
		 
		  if(morefilltervalue == "Select Location"){
			  morefilltervalue = "";
		  }
		  if(morefilltervalue == "Select Date Type"){
			  morefilltervalue = "";
		  }
			$(this).text(morefilltervalue);

		});	
	  ticketFilterAjax(dateVal,statusValues,priorityValues,location_id,dateType,startDate,endDate,myticketfillter,3);	 
    });
	
	function ticketFilterAjax(dateVal,statusValues,priorityValues,location_id,dateType,startDate,endDate,url,urlstatus){
	$.ajax({
			url: url,
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
				handleSuccess(responseData,urlstatus); // Call the separate function here
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
	  });
}

function handleSuccess(responseData,urlstatus) {
	if(urlstatus == 1){
			$("#ticket_details1").hide();
			$("#ticket_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; // Set the data fetched from the server
			var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
			ticketrenderPagination(ticketdataSource, ticketpageSize);
			ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
	}
	
	if(urlstatus == 3){
			$("#my_ticket_details1").hide();
			$("#my_ticket_details").show();
			myticketdataSource = responseData; 
			mytotalItems = responseData.length; // Set the data fetched from the server
			var myticketpageSize = parseInt($('#myitemsPerPageSelect').val()); // Get selected items per page
			myticketrenderPagination(myticketdataSource, myticketpageSize);
			myticketrenderTable(myticketdataSource, myticketpageSize, 1); // Show first page initially
	}
}

$(document).on('click', '.options_marketers div', function (e) {
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
		$(".value_views").text("");
        var moredatefittervale = $('#dateallviews').text();

        $('.clear_views').show();
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
                $("#depart_search").val(),
            ];

            $(".value_views_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
				
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,1,fetchUrlmorefitter);
        });
		
$(document).on('click', '.my_options_marketers div', function (e) {
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
		$(".my_value_views").text("");
        var moredatefittervale = $('#mydateallviews').text();

        $('.clear_my_views').show();
        $(".my_search_view").show();
        
		var resultsArray_marketer = [];
        $(".mymarketer_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#mymarketer_fetch").val(),
                $("#mybranchviews").val(),
                $("#myzoneviews").val(),
                $("#mydeprt_search").val(),
            ];

            $(".value_views_mysearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
				
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,3,fetchMymorefitter);
        });
	
	// date fitter function
function ticketdatefillterrange(datefiltervalue,url,urlstatus,fitterremovedata) {
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
    $("#ticket_details1").show();
    $.ajax({
        url: url,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
			morefilltersall: morefilltersall,
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

// Render pagination controls based on data
function myticketrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';

    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="my-page-bttn " style="background-color:#6a6ee4;color: #fff;"  data-page="' + i + '">' + i + '</button>';
    }

    $('#myticketpagination').html(paginationHtml);

    // Bind click event to each pagination button
    $('.my-page-bttn').click(function() {
        var pageNum = $(this).data('page');
        $('.my-page-bttn').removeClass('active');
        $(this).addClass('active');
        myticketrenderTable(data, ticketpageSize, pageNum);
    });
}

	// Render table rows based on the page and page size
	function ticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);

		var body = "";
			body = ticketData(pageData,body);	

		$("#ticket_details").html(body);
		$("#today_visits").text(totalItems);
		$("#counts").text(totalItems);
	}
	
		// Render table rows based on the page and page size
	function myticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);

		var body = "";
		let mydata = ticketData(pageData,body);
		
		$("#my_ticket_details").html(mydata);
		$("#today_visits").text(mytotalItems);
		$("#mycounts").text(mytotalItems);
	}
	
	function ticketData(pageData,body){
		$.each(pageData, function(index, user) {
			let dateStr = user.created_at;
			let target_date = user.target_date;		
			let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
			let	targetedDate = moment(target_date).format("DD MMM YYYY");
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"> <a href='+domainUrl+'/admin/ticketActivity/'+user.ticket_no+'>' + user.ticket_no + '</a><br>' + formattedDate +'</td>' +
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
		return body;
	}
	
	$('.btn-close, #close-button').on('click', function() {
		location.reload();
	});

	var activeTabId = "";
	// Listen for tab change event
	$('#myTab').on('shown.bs.tab', function (e) {
	  // Get the ID of the active tab
	   activeTabId = $(e.target).attr('id'); // This gets the ID of the newly activated tab	  
	});

    $('.ranges, .applyBtn').on('click', function() {		
        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
            if(activeTabId == "" || activeTabId == "analytics-tab-1"){
				var datefilltervalue = $('#dateallviews').text(); // Get the current text value when '.ranges' is clicked
				var url = ticketdatefillter;
				var urlstatus = 1;
			}			
			if(activeTabId == "analytics-tab-2"){
				var datefilltervalue = $('#mydateviewsall').text(); // Get the current text value when '.ranges' is clicked
				var url = myticketdatefillter;
				var urlstatus = 3;
			}
            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata);
            
        } else if ($(this).hasClass('applyBtn')) {
            var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
			$('.drp-selected').text("");
            var dateRange = datefilltervaluenew.split(' - ');
            function convertDateFormat(dateStr) {
                let parts = dateStr.split('/');
                return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY   
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            var datefilltervalue = `${startDate} - ${endDate}`;
            if(activeTabId == "" || activeTabId == "analytics-tab-1"){
				var url = ticketdatefillter;
				var urlstatus = 1;
			}
			if(activeTabId == "analytics-tab-2"){
				var url = myticketdatefillter;
				var urlstatus = 3;
			}
			
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata);
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
          morefilltersremoveviews(fitterremovedata,fetchUrlticketfitterremove,1,datefilltervalue);
   
    });
	
	$(document).on("click", ".my_value_views", function () {
		console.log(fitterremovedata);
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
				const checkboxes = document.querySelectorAll('.mystatusCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('myselectedStatus').value = '';
			}
			
			if(key == 'ticket_priority.priority_name'){
				const checkboxes = document.querySelectorAll('.mypriorityCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('mypriorityStatus').value = '';
			}
			
			if(key == 'tbl_locations.name'){
				const selectedDiv = document.querySelector('.myloct-dropdown-options .selected');
				if (selectedDiv) {
					selectedDiv.classList.remove('selected');
				}	 
				$('#mylocationInput').val('');
			}
		
		$('.myfittersclr').filter(function () {
			return $(this).val().startsWith(morefillterremvedata);
		}).val("");
			
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });	
			var datefilltervalue = $('#mydateviewsall').text();
          morefilltersremoveviews(fitterremovedata,fetchMyticketfitterremove,3,datefilltervalue);
   
    });

    $(document).on("click", ".clear_views", function () {

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
		fitterremovedata.length = 0;		
		$('.marketervalues_search').val("")
        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_views').hide();
        $(".search_view").hide();
		$(".value_views_mainsearch").text("");
        overall_fetch(2);
    
    });
	
	$(document).on("click", ".clear_my_views", function () {
		const checkboxes = document.querySelectorAll('.mystatusCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('myselectedStatus').value = '';
				
		const pcheckboxes = document.querySelectorAll('.mypriorityCheckbox');
				// Loop through each checkbox and uncheck it
				pcheckboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('mypriorityStatus').value = '';
				
		const selectedDiv = document.querySelector('.myloct-dropdown-options .selected');
				if (selectedDiv) {
					selectedDiv.classList.remove('selected');
				}	
		fitterremovedata.length = 0;		
		$('.mymarketer_search').val("")
        $(".my_value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_my_views').hide();
        $(".my_search_view").hide();
		$(".value_views_mysearch").text("");
        my_ticket_fetch(2);
    
    });

      $(document).on("click", ".value_views_mainsearch", function () {
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
		if(clear_filtr == 'ddepart_search'){
			$('#depart_search').val('');	
		}
		$('.marketervalues_search').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");		

       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
          morefilterview(fitterremovedata,datefilltervalue,1,fetchUrlmorefitter);
   
    });
    
      $(document).on("click", ".value_views_mysearch", function () {
		var datefilltervalue = $('#mydateallviews').text();	
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
		var clear_filtr = $(this).attr('id');		
        $(this).text("");
		if(clear_filtr == 'created_my_search'){
			$('#mymarketer_fetch').val('');	
		}
		if(clear_filtr == 'mybranch_search'){
			$('#mybranchviews').val('');	
		}
		if(clear_filtr == 'myzone_search'){
			$('#myzoneviews').val('');	
		}
		if(clear_filtr == 'dept_search'){
			$('#mydeprt_search').val('');	
		}
		$('.marketervalues_search').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");		

       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
          morefilterview(fitterremovedata,datefilltervalue,3,fetchMymorefitter);
   
    });
    
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
	
	// Handle items per page change
    $('#myitemsPerPageSelect').change(function() {
        var myticketpageSize = parseInt($(this).val());
        myticketrenderPagination(myticketdataSource, myticketpageSize);
        myticketrenderTable(myticketdataSource, myticketpageSize, 1);  // Initially show the first page
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

