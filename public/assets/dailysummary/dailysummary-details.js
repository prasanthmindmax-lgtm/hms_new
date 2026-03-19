$(document).ready(function() {
	 var domainUrl = window.location.origin;
    // Fetch data and initialize pagination
	//my_ticket_fetch(1);
    overall_fetch(1);
    //all_ticket_fetch(1);

    $(".search_daily").hide();
	
function morefilterview(uniqueResults, moredatefittervale, urlstatus, url) {
	$('#daily_details11').show();
	$("#daily_details").hide();
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
			$('#daily_details11').hide();
			var locations = responseData.dropdown || responseData; 
				var container =$(".options_branch.branchviewsall");
				container.empty(); 
				locations.forEach(function(location) {
					if (location.status === 1) { 
						var option = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', location.id)
							.text(location.name);
						container.append(option);
					}
				});
				handleSuccess(responseData,urlstatus);
		   
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
	}else
   {
	if(urlstatus == 1){
      $(".search_daily").hide();
	  $('.clear_views').hide();
      overall_fetch(2);
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
        console.error("AJAX Error:", status, error);
    }
  });

   }else
   {
	   if(urlstatus == 1){
		  $(".search_daily").hide();
		  $('.clear_views').hide();
		  overall_fetch(2);
		}
   }

}

	var ticketdataSource = [];  // Data will be fetched here

	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		if(statusid ==  1){
			 var defaultLocation = "Kerala - Palakkad";
			$('#branch_views').val(defaultLocation);

			$('.loct-dropdown-options div').each(function() {
				if ($(this).text().trim() === defaultLocation) {
					$(this).addClass('selected'); // Add class for styling the selected item
				}
			});
		}
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_mainsearch").text("");
		$('#daily_details11').show();
		$.ajax({
			url: dailyfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid,
				apistatus:'dailyreport'
			},
			success: function (responseData) {
				$('#daily_details11').hide();
				handleSuccess(responseData,1);
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	}
	
	var fitterremovedata = [];	
		
function handleSuccess(responseData,urlstatus) {
	if(urlstatus == 1){
			$("#daily_details").show();
			ticketdataSource = Array.isArray(responseData?.data) ? responseData.data : responseData;
			totalItems = ticketdataSource.length; // Set the data fetched from the server
			var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
			ticketrenderPagination(ticketdataSource, ticketpageSize);
			ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
	}
}

$(document).on('click', '.options_branch div', function (e) {
		 //return;
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
		$(".value_views").text("");
        var moredatefittervale = $('#dateallviews').text();

        $('.clear_views').show();
        $(".search_daily").show();
        
		var resultsArray_marketer = [];
        $(".dailyvalues_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#zone_views").val(),
                $("#branch_views").val(),
            ];

            $(".value_views_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,1,fetchBranchUrlfitter);
        });		
	
	// date fitter function
function ticketdatefillterrange(datefiltervalue,url,urlstatus,fitterremovedata) {
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
    $("#daily_details11").show();
    $("#daily_details").hide();
    $.ajax({
        url: url,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
			morefilltersall: morefilltersall,
        },
        success: function (responseData) {
			$("#daily_details11").hide();
           handleSuccess(responseData,urlstatus);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

// Render pagination controls based on data
function ticketrenderPagination(data, ticketpageSize) {
	 if (!Array.isArray(data)) {
        if (data && typeof data === 'object') {
            data = [data];
        } else {
            data = [];
        }
    }
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
		 if (!Array.isArray(data)) {
			if (data && typeof data === 'object') {
				data = [data];
			} else {
				data = [];
			}
		}
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
	
		 $('#op_income').text(data[0] && data[0].totalOpIncome ? data[0].totalOpIncome: 0);
		 $('#ip_income').text(data[0] && data[0].totalIpIncome ? data[0].totalIpIncome: 0);
		 $('#phary_income').text(data[0] && data[0].totalPharmacyIncome ? data[0].totalPharmacyIncome: 0);
		var body = "";
			body = ticketData(pageData,body);

		$("#daily_details").html(body);
		$("#today_visits").text(totalItems);
		$("#dcounts").text(totalItems);
	}
	
	function ticketData(pageData,body){
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
			
			let dateStr = usr.date;
			let target_date = usr.billdate;		
			let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
			let	billDate = target_date ? moment(target_date).format("DD MMM YYYY") : "-";
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"><strong>#' + sno + '</strong><br>' + formattedDate +'</td>' +
				   '<td class="tdview">' + usr.name + '</td>' +
				   '<td class="tdview" id="target_date" data-target_date="' + billDate + '" >' + billDate + '</td>' +
				    '<td class="tdview">' + usr.opIncome + '</td>' +
					'<td class="tdview">' + usr.ipIncome + '</td>' +
					'<td class="tdview">' + usr.pharmacyIncome + '</td>' +	
					'<td class="tdview">' + usr.total_amt + '</td>' +					
				   '</tr>';
				   sno++;
			});
		}
		return body;
	}
	
	/*function ticketData(pageData,body){
		var sno = 1;
		$.each(pageData, function(index, usr) {
			
			let dateStr = usr.date;
			let target_date = usr.billdate;		
			let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
			let	billDate = moment(target_date).format("DD MMM YYYY");
			//let opIncome = (user.type === "O/P - Income") ? user.amt : '-';
			//let ipIncome = (user.type === "I/P - Income") ? user.amt : '-';
			//let pharmacyIncome = (user.type === "Pharmacy - Income") ? user.amt : '-';
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"><strong>#' + sno + '</strong><br>' + formattedDate +'</td>' +
				   '<td class="tdview">' + usr.name + '</td>' +
				   '<td class="tdview" id="target_date" data-target_date="' + billDate + '" >' + billDate + '</td>' +
				    '<td class="tdview">' + usr.opIncome + '</td>' +
					'<td class="tdview">' + usr.ipIncome + '</td>' +
					'<td class="tdview">' + usr.pharmacyIncome + '</td>' +	
					'<td class="tdview">' + usr.total_amt + '</td>' +					
				   '</tr>';
				   sno++;
		});
		return body;
	}*/
	
    $('.ranges, .applyBtn').on('click', function() {
		console.log(fitterremovedata);		
		if(fitterremovedata.length ==  0){
			 var defaultLocation = "Kerala - Palakkad";
			$('#branch_views').val(defaultLocation);

			$('.loct-dropdown-options div').each(function() {
				if ($(this).text().trim() === defaultLocation) {
					$(this).addClass('selected'); // Add class for styling the selected item
				}
			});
		}
        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
			
				var datefilltervalue = $('#dateallviews').text();
				var url = datefetchUrl;
				var urlstatus = 1;
						            
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
           
				var url = datefetchUrl;
				var urlstatus = 1;			
			            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata);
        }
    });


    $(document).on("click", ".clear_views", function () {
		$(".options_branch.branchviewsall").empty();
      	 $("#daily_details").hide();
		fitterremovedata.length = 0;		
		$('.dailyvalues_search').val("")
        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_views').hide();
        $(".search_daily").hide();
		$(".value_views_mainsearch").text("");
        overall_fetch(2);
    
    });

	 $(document).on("click", ".value_views_mainsearch", function () {
		var datefilltervalue = $('#dateallviews').text();	
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
		var clear_filr = $(this).attr('id');		
        $(this).text("");
		
		if(clear_filr == 'dbranch_search'){
				$('#zone_views').val('');
		}
		if(clear_filr == 'dzone_search'){
			$('#branch_views').val('');	
		}
		// Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
          morefilterview(fitterremovedata,datefilltervalue,1,fetchBranchUrlfitter);
   
    });
	
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });	
   
});
