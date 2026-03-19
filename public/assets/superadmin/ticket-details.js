$(document).ready(function() {
	 var domainUrl = window.location.origin;
    // Fetch data and initialize pagination
	my_ticket_fetch(1);
    overall_fetch(1);
    all_ticket_fetch(1);

    $(".search_view").hide();
    $(".all_search_view").hide();
    $(".my_search_view").hide();
	$('#start_date').hide();
	$('#end_date').hide();

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
	  $('.clear_view').hide();
      overall_fetch(2);
	}else if(urlstatus == 2){
		  $(".all_search_view").hide();
		  $('.clear_all_views').hide();
		  all_ticket_fetch(2);
	}else{
		$('.clear_my_views').hide();
      $(".my_search_view").hide();
      my_ticket_fetch(2);
	}
   }
}

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
		  $('.clear_view').hide();
		  overall_fetch(2);
		}else if(urlstatus == 2){
		  $(".all_search_view").hide();
		  $('.clear_all_views').hide();
		  all_ticket_fetch(2);
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
		$(".value_views_main").text("");
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
	function all_ticket_fetch(statusid) {
		var moredatefittervale = $('#alldateallviews').text();
		$(".value_views_allsearch").text("");
		$("#all_ticket_details1").show();
		$.ajax({
			url: allticketfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid
			},
			success: function (responseData) {
				handleSuccess(responseData,2);
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
	  $('.clear_view').show();
	  $(".value_views_main").text("");
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
	  ticketFilterAjax(dateVal,statusValues,priorityValues,location_id,dateType,startDate,endDate,ticketfillter,1);
    });

	$("#all_ticket_search").on("click", function () {
	  $('.all_search_view').show();
	  $('.clear_all_views').show();
	  $(".value_views_allsearch").text("");
	  $('.allmarketer_search').val('');
	  let dateVal=$("#alldateallviews").text();
		  let statusValues = $(".allstatusCheckbox:checked").map(function () {
					return $(this).val(); // Get value of checked checkboxes
				}).get(); // Convert to an array
				statusValues = statusValues.join(",");
		  let priorityValues = $(".allpriorityCheckbox:checked").map(function () {
					return $(this).val(); // Get value of checked checkboxes
				}).get(); // Convert to an array
				priorityValues = priorityValues.join(",");
		  let location_id =  $('.allloct-dropdown-options div.selected').attr('data-value');
		  let dateType =  $("#alldateType").val();
		  let startDate =  $("#allstartDate").val();
		  let endDate =  $("#allendDate").val();
		var resultsArray = [];
        $(".allfittersclr").each(function () {

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

				$('.allstatusCheckbox:checked').map(function() {
					return $(this).closest('label').text().trim();
				}).get().join(","),
				$('.allpriorityCheckbox:checked').map(function() {
					return $(this).closest('label').text().trim();
				}).get().join(","),
				$('.allloct-dropdown-options div.selected').text(),
				$("#alldateType option:selected").text(),
				$("#allstartDate").val(),
				$("#allendDate").val()
			];

	 $(".all_value_views").each(function (index) {
			var morefilltervalue = moreFilterDatas[index] ? moreFilterDatas[index] : ""; // Use "N/A" if value is empty

		  if(morefilltervalue == "Select Location"){
			  morefilltervalue = "";
		  }

			$(this).text(morefilltervalue);

		});
	ticketFilterAjax(dateVal,statusValues,priorityValues,location_id,dateType,startDate,endDate,allticketfillter,2);
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
		  let location_id =  $('.myloct-dropdown-options div.selected').attr('data-value');
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
				$('.myloct-dropdown-options div.selected').text(),
				$("#mydateType option:selected").text(),
				$("#mystartDate").val(),
				$("#myendDate").val()
			];

	 $(".my_value_views").each(function (index) {
			var morefilltervalue = moreFilterDatas[index] ? moreFilterDatas[index] : ""; // Use "N/A" if value is empty

		  if(morefilltervalue == "Select Location"){
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
	if(urlstatus == 2){
			$("#all_ticket_details1").hide();
			$("#all_ticket_details").show();
			allticketdataSource = responseData;
			alltotalItems = responseData.length; // Set the data fetched from the server
			var allticketpageSize = parseInt($('#allitemsPerPageSelect').val()); // Get selected items per page
			allticketrenderPagination(allticketdataSource, allticketpageSize);
			allticketrenderTable(allticketdataSource, allticketpageSize, 1); // Show first page initially
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

$(document).on('click', '.option_marketers div', function (e) {
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
		$(".value_views").text("");
        var moredatefittervale = $('#dateallviews').text();

        $('.clear_all_views').hide();
        $('.clear_view').show();
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
                $("#dept_views").val(),
            ];

            $(".value_views_main").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,1,fetchticketfitter);
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
                $("#mydeptviews").val(),
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

$(document).on('click', '.all_options_marketers div', function (e) {
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
		$(".all_value_views").text("");
        var moredatefittervale = $('#alldateallviews').text();

        $('.clear_all_views').show();
        $(".all_search_view").show();

		var resultsArray_marketer = [];
        $(".allmarketer_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#allmarketer_fetch").val(),
                $("#allbranchviews").val(),
                $("#allzoneviews").val(),
                $("#alldeptviews").val(),
            ];

            $(".value_views_allsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });

                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,2,fetchAllmorefitter);
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
function allticketrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';

    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="all-page-bttn " style="background-color:#6a6ee4;color: #fff;"  data-page="' + i + '">' + i + '</button>';
    }

    $('#allticketpagination').html(paginationHtml);

    // Bind click event to each pagination button
    $('.all-page-bttn').click(function() {
        var pageNum = $(this).data('page');
        $('.all-page-bttn').removeClass('active');
        $(this).addClass('active');
        allticketrenderTable(data, ticketpageSize, pageNum);
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
			body = ticketData(pageData,body,'app');

		$("#ticket_details").html(body);
		$("#today_visits").text(totalItems);
		$("#counts").text(totalItems);
	}

	// Render table rows based on the page and page size
	function allticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);

		var body = "";
		let alldata = ticketData(pageData,body,'app');

		$("#all_ticket_details").html(alldata);
		$("#today_visits").text(alltotalItems);
		$("#allcounts").text(alltotalItems);
	}

	// Render table rows based on the page and page size
	function myticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);

		var body = "";
		let mydata = ticketData(pageData,body,'');

		$("#my_ticket_details").html(mydata);
		$("#today_visits").text(mytotalItems);
		$("#my_counts").text(data.length);
	}

	function ticketData(pageData,body,table_type){
		$.each(pageData, function(index, user) {
			let dateStr = user.created_at;
			let target_date = user.target_date;
			let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
			let	targetedDate = moment(target_date).format("DD MMM YYYY");

			body += '<tr onclick="rowClick(event)" data-id="' + user.id + '">'+
				   '<td class="tdview"> <a href='+domainUrl+'/superadmin/ticketActivity/'+user.ticket_no+'>' + user.ticket_no + '</a><br>' + formattedDate +'</td>' +
				   '<td class="tdview"> ' + user.name + '</td>' +
				   '<td class="tdview" id="_fetch" data-department_id="' + user.department_id + '">'+ user.from_department_name +'</td>' +
				   '<td class="tdview" id="_fetch" data-department_id="' + user.department_id + '">'+ user.depart_name +'</td>' +
				   '<td class="tdview">' + user.sub_category_name + '</td>' +
				   '<td class="tdview" id="target_date" data-target_date="' + targetedDate + '" >' + targetedDate + '</td>' +
				   '<td class="tdview" id="priority" data-priority="' + user.priority + '">' + user.priority_name + '</td>' +
				  '<td class="tdview Tooltip_Text_container_subject" id="Tooltip_Text_container">' +'<b>' + user.subject + '</b>'+
					'</td>'+
				  '<td class="tdview Tooltip_Text_container_subject" id="Tooltip_Text_container">' +'<b>' + user.description + '</b>'+
					'</td>'+
				  // '<td class="tdview Tooltip_Text_container_subject" id="Tooltip_Text_container">' +
					// 	'<i class="fas fa-info-circle" style="cursor: pointer; color: #007bff;"></i>' +
					// 	 '<a href="#"><span class="tooltips" style="z-index:9999;margin-left: -2em;width: 187px;text-align: justify;"><b>' + user.subject + '</b> </span></a></td>' +
				  // '<td class="tdview Tooltip_Text_container_description" id="Tooltip_Text_container">' +
					// 	'<i class="fas fa-info-circle" style="cursor: pointer; color: #007bff;"></i>' +
					// 	 '<a href="#"><span class="tooltips" style="z-index:9999;margin-left: -2em;width: 187px;text-align: justify;"><b>' + user.description + '</b> </span></a></td>' +
				   '<td class="tdview" id="fullname" data-fullname="' + user.user_fullname + '">' + user.user_fullname + '</td>' +
				   '<td style="display:none" class="tdview imagecheck" value="' + user.id + '"><a href="#">Ticket Images</a></td>' +
				   '<td style="display:none" class="tdview" id="imageviews">' + user.image_paths + '</td>' ;
					 if (user.image_paths && user.image_paths.length > 0) {
							body += `
									<td class="tdview" style="text-align:center;">
											<i class="fas fa-file-pdf text-danger file-view"
												title="View Attachments"
												data-files='${JSON.stringify(user.image_paths)}'
												style="cursor:pointer; font-size:16px;"></i>
									</td>
							`;
					} else {
							body += `<td class="tdview" style="text-align:center;">-</td>`;
					}

					 if (table_type !== 'app') {

									body += '<td class="tdview">';
									if (user.status_name === "Open") {
											body += '<span class="closed-badge" style="background:#6b75a8;color:#fff;padding:5px 10px;border-radius:4px;font-size:9px;">' + user.status_name + '</span>';
									} else if (user.status_name === "Waiting for Approval") {
											body += '<span class="closed-badge" style="background:#bd478c;color:#fff;padding:5px 10px;border-radius:4px;font-size:9px;">' + user.status_name + '</span>';
									} else if (user.status_name === "In-progress") {
											body += '<span class="closed-badge" style="background:#ecca51;color:#fff;padding:5px 10px;border-radius:4px;font-size:9px;">' + user.status_name + '</span>';
									} else if (user.status_name === "Rejected") {
											body += '<span class="closed-badge" style="background:#ee2f2f;color:#fff;padding:5px 10px;border-radius:4px;font-size:9px;">' + user.status_name + '</span>';
									} else {
											body += '<span class="new-badge">' + user.status_name + '</span>';
									}

									body += '</td>';
							} else {
									body += `
											<td class="tdview">
													<div class="status-container" style="position:relative;display:inline-block;">
															<span class="status-badge"
																	data-id="${user.id}"
																	style="background:${getStatusColor(user.status_name)};color:#fff;padding:5px 10px;border-radius:4px;font-size:9px;cursor:pointer;">
																	${user.status_name}
															</span>
															<div class="status-options"
																	style="display:none;position:absolute;top:25px;left:0;background:#fff;border:1px solid #ccc;border-radius:4px;z-index:99;min-width:130px;">
															</div>
													</div>
											</td>`;
											body += `
												<td class="tdview">
														<button class="chat-btn"
																		data-ticket_id="${user.id}"
																		data-ticket_no="${user.ticket_no}"
																		data-user="${user.user_fullname}"
																		style="background:#007bff;color:#fff;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;font-size:10px;">
																Chat
														</button>
												</td>
											`;
							}

							body += '</tr>';
		});
		return body;
	}

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
				var datefilltervalue = $('#mydateviewsall').text();
				var url = myticketdatefillter;
				var urlstatus = 3;
			}
			if(activeTabId == "analytics-tab-2"){
				var datefilltervalue = $('#dateviewsall').text();
				var url = ticketdatefillter;
				var urlstatus = 1;
			}
			if(activeTabId == "analytics-tab-3"){
				var datefilltervalue = $('#alldateviewsall').text();
				var url = allticketdatefillter;
				var urlstatus = 2;
			}
            console.log("datefilltervalue");
            console.log(datefilltervalue);
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
				var url = myticketdatefillter;
				var urlstatus = 3;
			}
			if(activeTabId == "analytics-tab-2"){
				var url = ticketdatefillter;
				var urlstatus = 1;
			}
			if(activeTabId == "analytics-tab-3"){
				var url = allticketdatefillter;
				var urlstatus = 2;
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

    $(document).on("click", ".all_value_views", function () {

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
				const checkboxes = document.querySelectorAll('.allstatusCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('allselectedStatus').value = '';
			}

			if(key == 'ticket_priority.priority_name'){
				const checkboxes = document.querySelectorAll('.allpriorityCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('allpriorityStatus').value = '';
			}

			if(key == 'tbl_locations.name'){
				const selectedDiv = document.querySelector('.allloct-dropdown-options .selected');
				if (selectedDiv) {
					selectedDiv.classList.remove('selected');
				}
				$('#alllocationInput').val('');
			}

		$('.allfittersclr').filter(function () {
			return $(this).val().startsWith(morefillterremvedata);
		}).val("");

       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });
          morefilltersremoveviews(fitterremovedata,fetchAllticketfitterremove,2);

    });

	$(document).on("click", ".my_value_views", function () {

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


    $(document).on("click", ".clear_view", function () {

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
        $('.clear_view').hide();
        $(".search_view").hide();
		$(".value_views_main").text("");
        overall_fetch(2);

    });

    $(document).on("click", ".clear_all_views", function () {
		const checkboxes = document.querySelectorAll('.allstatusCheckbox');
				// Loop through each checkbox and uncheck it
				checkboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('allselectedStatus').value = '';

		const pcheckboxes = document.querySelectorAll('.allpriorityCheckbox');
				// Loop through each checkbox and uncheck it
				pcheckboxes.forEach(checkbox => {
					checkbox.checked = false;
				});
				document.getElementById('allpriorityStatus').value = '';

		const selectedDiv = document.querySelector('.allloct-dropdown-options .selected');
				if (selectedDiv) {
					selectedDiv.classList.remove('selected');
				}
        $(".all_value_views").text("");
        $('.allfittersclr').val("");
        $('.clear_all_views').hide();
        $(".all_search_view").hide();
		fitterremovedata.length = 0;
		$('.allmarketer_search').val("")
		$(".value_views_allsearch").text("");
        all_ticket_fetch(2);

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
        $(".my_value_views").text("");
        $('.myfittersclr').val("");
        $('.clear_my_views').hide();
        $(".my_search_view").hide();
		fitterremovedata.length = 0;
		$('.mymarketer_search').val("")
		$(".value_views_mysearch").text("");
        my_ticket_fetch(2);

    });

	 $(document).on("click", ".value_views_main", function () {
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
		if(clear_filtr == 'dept_views'){
			$('#dept_search').val('');
		}
		$('.marketervalues_search').filter(function () {
            return $(this).val().startsWith(morefillterremvedata);
        }).val("");

       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });
          morefilterview(fitterremovedata,datefilltervalue,1,fetchticketfitter);

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
		if(clear_filtr == 'mydept_search'){
			$('#mydeptviews').val('');
		}


       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });
          morefilterview(fitterremovedata,datefilltervalue,3,fetchMymorefitter);

    });

	$(document).on("click", ".value_views_allsearch", function () {
		var datefilltervalue = $('#alldateallviews').text();
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
		var clear_filtr = $(this).attr('id');
        $(this).text("");
		if(clear_filtr == 'created_all_search'){
			$('#allmarketer_fetch').val('');
		}
		if(clear_filtr == 'allbranch_search'){
			$('#allbranchviews').val('');
		}
		if(clear_filtr == 'allzone_search'){
			$('#allzoneviews').val('');
		}
		if(clear_filtr == 'alldept_search'){
			$('#alldeptviews').val('');
		}


       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });
          morefilterview(fitterremovedata,datefilltervalue,2,fetchAllmorefitter);

    });

    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });

    // Handle items per page change
    $('#allitemsPerPageSelect').change(function() {
        var allticketpageSize = parseInt($(this).val());
        allticketrenderPagination(allticketdataSource, allticketpageSize);
        allticketrenderTable(allticketdataSource, allticketpageSize, 1);  // Initially show the first page
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

$(document).on('click', '.file-view', function () {
    let filesData = $(this).data('files');
    let files = [];

    if (typeof filesData === 'string') {
        try {
            files = JSON.parse(filesData);
            if (typeof files === 'string') files = JSON.parse(files);
        } catch (e) {
            files = [filesData];
        }
    } else if (Array.isArray(filesData)) {
        files = filesData;
    } else if (filesData) {
        files = [filesData];
    }

    if (!Array.isArray(files)) files = [files];

    console.log("Parsed files:", files);

    let html = '';
    if (files.length > 0) {
        files.forEach(file => {
            const fileUrl = window.baseUrl + file;  // ✅ uses global set in Blade
            console.log("fileUrl:", fileUrl);

            const ext = file.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                html += `
                    <div class="mb-3 text-center">
                        <img src="${fileUrl}" class="img-fluid rounded shadow-sm" style="max-height:200px;">
                        <br><a href="${fileUrl}" target="_blank">Open Image</a>
                    </div>`;
            } else if (ext === 'pdf') {
                html += `
                    <div class="mb-3 text-center">
                        <iframe src="${fileUrl}" width="100%" height="400px"></iframe>
                        <br><a href="${fileUrl}" target="_blank">Open PDF</a>
                    </div>`;
            } else {
                html += `
                    <div class="mb-3 text-center">
                        <a href="${fileUrl}" target="_blank">Download File</a>
                    </div>`;
            }
        });
    } else {
        html = '<p class="text-center text-muted">No attachments found.</p>';
    }

    $('#fileViewerBody').html(html);
    $('#fileViewerModal').modal('show');
});




