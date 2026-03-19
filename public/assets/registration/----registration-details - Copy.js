$(document).ready(function() {
	 var domainUrl = window.location.origin;
    // Fetch data and initialize pagination
	//my_ticket_fetch(1);
    overall_fetch(1);
    //all_ticket_fetch(1);

    $(".search_view").hide();
	$(".search_report").hide();
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
			apistatus:'regreport'
        },
        success: function (responseData) {
			$('#daily_details11').hide();
           handleSuccess(responseData,urlstatus);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
	}else
   {
	if(urlstatus == 1){
      $(".search_view").hide();
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
		  $(".search_view").hide();
		  $('.clear_views').hide();
		  overall_fetch(2);
		}
   }

}

	var ticketdataSource = [];  // Data will be fetched here

	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		$("#table2").hide();
		$("#table1").show();
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_mainsearch").text("");
		$('#daily_details11').show();
	
		$.ajax({
			url: dailyfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid,
				apistatus:'regreport'
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
var totalItems;                                                 		
function handleSuccess(responseData,urlstatus) {	
	if(urlstatus == 1){
		$("#daily_details").show();	
		ticketdataSource = responseData; 
		totalItems = responseData.length; 
		var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); 
		ticketrenderPagination(ticketdataSource, ticketpageSize);			
		ticketrenderTable(ticketdataSource, ticketpageSize, 1); 
	}else{
		ticketdataSource = responseData; 
		totalItems = responseData.length; 
		var ticketpageSize = parseInt($('#reportPerPageSelect').val()); 
		reportrenderPagination(ticketdataSource, ticketpageSize);
		reportrenderTable(ticketdataSource, ticketpageSize, 1)
	}
}

let storedRegAt = null;
let storedPhid = null;
let storedCity = null;
let debounceTimer;
$('#reportviews').on('keyup input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
        $('#report_details').hide();
        $('#report_details11').show();

        let ph_id = $('#reportviews').val().trim();		
        if (!storedRegAt || !storedPhid || !storedCity) {
            storedRegAt = $('td.tdview[data-reg_at]').data('reg_at');
            storedPhid = $('td.tdview[data-ph_id]').data('ph_id');
            storedCity = $('td.tdview[data-city]').data('city');
        }

        $.ajax({
            url: regViewUrlfitter,
            type: "GET",
            data: {
                ph_id: ph_id,
                cdate: storedRegAt,
                phid: storedPhid,
                city: storedCity,
                status_id: 1
            },
            success: function (response) {
                $('#report_details11').hide();
                $('#report_details').show();
                handleSuccess(response, 5);
            }
        });

    }, 300);
});

$(document).on('click', '.options_branch div', function (e) {
		 //return;
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
                $("#branchviews").val(),
            ];

            $(".value_views_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,1,fetchBranchUrlfitter,'regreport');
        });
		
		

/*	$(document).on('click', '.report_branch div[data-fullvalue]', function () {
			$("#report_details").hide();
			$("#report_details11").show();
			$('.clear_report').show();
			$(".search_report").show();
			$(".report_views_mainsearch").show();
		var fullValue = $(this).data('fullvalue'); 
		var mobile = fullValue.split(' | ')[0];
		var cdate  = fullValue.split(' | ')[1];
		var phid  = fullValue.split(' | ')[2];
		var area  = fullValue.split(' | ')[3];
		
		var $input = $(".report_search");
		var value = $input.val();
		
		var moreFilterValue_market = $("#reportviews").val() || "";
		$(".report_views_mainsearch").text(moreFilterValue_market);		
		$.ajax({
                url: regViewUrlfitter,
                type: "GET",
				data: {
						mobile:mobile,
						cdate:cdate,
						phid:phid,
						city:area,
						status_id:1
					},
                success: function(response) {
					$("#report_details11").hide();						
					$("#report_details").show();						
					handleSuccess(response,5);
                }
            });		
	});*/		
	
	// date fitter function
function ticketdatefillterrange(datefiltervalue,url,urlstatus,fitterremovedata,apistatus) {
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
			apistatus:apistatus,
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
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-bttn.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#ticketpagination').html(paginationHtml);
	$("#rcounts").text(totalItems);
    $('.page-bttn').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-bttn').removeClass('active');
            $(this).addClass('active');
            ticketrenderTable(data, ticketpageSize, pageNum);
            ticketrenderPagination(data, ticketpageSize); 
        }
    });
	$('.page-bttn[data-page="' + currentPage + '"]').addClass('active');
}

// Render pagination controls based on data
function reportrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-btn.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-btn" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-btn" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="page-btn" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#reportpagination').html(paginationHtml);
	$("#rpcounts").text(totalItems);
    $('.page-btn').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-btn').removeClass('active');
            $(this).addClass('active');
            reportrenderTable(data, ticketpageSize, pageNum);
            reportrenderPagination(data, ticketpageSize); 
        }
    });
	$('.page-btn[data-page="' + currentPage + '"]').addClass('active');
}

	// Render table rows based on the page and page size
	function ticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
			body = ticketData(pageData,body);

		$("#daily_details").html(body);
		$("#today_visits").text(totalItems);
		$("#rcounts").text(totalItems);
	}	
	
	// Render table rows based on the page and page size
	function reportrenderTable(data, ticketpageSize, pageNum) {
		$("#report_details").show();
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
			
			let dateStr = usr.created_at;
			let regdate = usr.registrationdate;
			let dob = usr.dob;		
			let formattedDate = moment(dateStr, "YYYYMMDDHH:mm:ss").format("DD MMM YYYY HH:mm:ss");
			    regdate = moment(regdate, "YYYYMMDD").format("DD MMM YYYY");
				dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"><strong>#' + sno + '</strong></a><br>' + formattedDate +'</td>' +	
					'<td class="tdview">' + usr.name + '</td>' +					
					'<td class="tdview" data-mobile="' + usr.mobile + '">' + usr.mobile.substring(0, 2) + '********' + usr.mobile.substring(usr.mobile.length - 2) + '</td>' +					
					'<td class="tdview">' + dob + '</td>' +					
					'<td class="tdview">' + usr.gender + '</td>' +					
					'<td class="tdview">' + usr.agey + '</td>' +					
					'<td class="tdview" data-ph_id="' + usr.phid + '">' + usr.phid + '</td>' +					
					'<td class="tdview" data-city="' + usr.city + '">' + usr.city + '</td>' +					
					'<td class="tdview" data-reg_at="' + usr.registrationdate + '">' + regdate + '</td>' +					
				   '</tr>';
				   sno++;
			});
		}

		$("#report_details").html(body);
		$("#today_visits").text(totalItems);
		$("#rpcounts").text(totalItems);
	}
	
	function ticketData(pageData,body){
		var domainUrl = window.location.origin;
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
			
			let dateStr = usr.created_at;
			let dob = usr.dob;		
			let formattedDate = moment(dateStr, "YYYYMMDDHH:mm:ss").format("DD MMM YYYY HH:mm:ss");
				dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"><strong>#' + sno + '</strong></a><br>' + formattedDate +'</td>' +	
					'<td class="tdview" data-phid="' + usr.phid + '">' + usr.area + '</td>' +					
					'<td class="tdview" id="regview" data-area="' + usr.area + '" data-created_at="' + usr.created_at + '" data-phid="' + usr.phid + '" style="cursor: pointer;color: rgb(16 35 255);">' + usr.count + '</td>' +					
				   '</tr>';
				   sno++;
			});
		}
		return body;
	}		
	
	$(document).on('click', '#regview', function (e) {
		$("#table2").show();
		$("#table1").hide();
		$('.report_search').val('');
		$("#report_details11").show();		
		$("#report_details").hide();		
		var row = $(this).closest('tr');
		var phid = row.find('#regview').data('phid');
		var area = row.find('#regview').data('area');
		var cdate = row.find('#regview').data('created_at').substring(0, 8);
		$.ajax({
                url: regViewUrlfitter,
                type: "GET",
				data: {
						phid: phid,
						cdate:cdate,
						city:area
					},
                success: function(response) {					
					/*	const dropdown = document.getElementById('mobileDropdown');
						response.forEach(user => {
							const formattedDate = user.registrationdate;
							const fullValue = `${user.mobile} | ${formattedDate} | ${user.phid} | ${area}`;
							const option = document.createElement('div');
							option.textContent = user.mobile;
							option.setAttribute('data-fullvalue', fullValue);
							dropdown.appendChild(option);
						});
						dropdown.addEventListener('click', function(e) {
							if (e.target && e.target.matches("div[data-value]")) {
								document.getElementById('reportviews').value = e.target.getAttribute('data-value');
							}
						});*/
						$("#report_details11").hide();
					handleSuccess(response,5);
                }
            });
	});	
	
    $('.ranges, .applyBtn').on('click', function() {
		console.log(fitterremovedata);	
		if(fitterremovedata.length ==  0){
			 var defaultLocation = "Kerala - Palakkad";
			$('#branchviews').val(defaultLocation);

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
						            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata,'regreport');
            
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
			            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata,'regreport');
        }
    });


    $(document).on("click", ".clear_views", function () {
      	 $("#daily_details").hide();
		fitterremovedata.length = 0;		
		$('.marketervalues_search').val("")
        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_views').hide();
        $(".search_view").hide();
		$(".value_views_mainsearch").text("");
        overall_fetch(1);
		
		const inputField = $(".searchLocation");
			inputField.val("");
			inputField.siblings(".loct-dropdown-options").find("div").show();
			inputField.siblings(".loct-dropdown-options").find("div").removeClass("selected");
    
    });
	
	$(document).on("click", ".value_views_mainsearch", function () {
		$('.clear_views').hide();
		$("#daily_details").hide();
		fitterremovedata.length = 0;	
          overall_fetch(1);
		  const inputField = $(".searchLocation");
			inputField.val("");
			inputField.siblings(".loct-dropdown-options").find("div").show();
			inputField.siblings(".loct-dropdown-options").find("div").removeClass("selected");   
    });
	
	$('.report_views_mainsearch, .clear_report').on('click', function() {
		$('#mobileDropdown').empty();
		$('#branchviews').val("")
		$(".report_views_mainsearch").hide();
		$('.clear_views').hide();
		$('.clear_report').hide();
		$('.search_view').hide();
		$('.search_report').hide();
		$("#daily_details").hide();		
		fitterremovedata.length = 0;	
          overall_fetch(1);
   
    });
	
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
    // Handle items per page change
    $('#reportPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        reportrenderPagination(ticketdataSource, ticketpageSize);
        reportrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
	
});
