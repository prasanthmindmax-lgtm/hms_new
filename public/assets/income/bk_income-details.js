$(document).ready(function() {
	$(".cincome_view").hide();
	overall_fetch(1);
	
var loaderInterval = null;
var loaderProgress = 0; 

function startLoader() {
    const progressBar = $('#progress-bar');
    const progressText = $('#progress-bar');
    const errorMessage = $('#error-message');

    loaderProgress = 0;  

    progressText.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    errorMessage.hide().text('');

    loaderInterval = setInterval(() => {
        if (loaderProgress < 95) {
            loaderProgress += 5;
            progressText.text(`Loading: ${loaderProgress}%`);
            progressBar.css('width', `${loaderProgress}%`);
        }
    }, 2500);
}

function stopLoader(success = true, error = '') {
    const progressBar = $('#progress-bar');
    const progressText = $('#progress-bar');
    const errorMessage = $('#error-message');

    clearInterval(loaderInterval);  

    if (success) {
        loaderProgress = 100; 
        progressText.text(`Loading: 100% - Done`);
        progressBar.css({
            'width': '100%',
            'background-color': '#007bff'
        });

        setTimeout(() => {
            progressBar.hide();
            $("#daily_details").show(); 
        }, 500);
    } else {
        progressText.text('Error loading data');
        progressBar.css('background-color', 'red');
        errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            progressBar.hide();
            $("#daily_details").hide(); 
        }, 1000);
    }
}

function morefilterview(uniqueResults, moredatefittervale, urlstatus, url) {   
    $("#daily_details").hide(); 
    startLoader();  
    if (!uniqueResults || uniqueResults.length === 0) {
        stopLoader(true); 
        if (urlstatus == 1) {
            $(".search_daily").hide();
            $('.clear_views').hide();
            overall_fetch(2);
        }
        return;
    }
    const morefilltersall = uniqueResults.join(" AND");

    $.ajax({
        url: url,
        type: "GET",
        data: {
            morefilltersall,
            moredatefittervale
        },
        success: function (responseData) {
            stopLoader(true);
            let locations = [];
            if (Array.isArray(responseData)) {
                locations = responseData;
            } else if (responseData.dropdown && Array.isArray(responseData.dropdown)) {
                locations = responseData.dropdown;
            } else {
                console.error('❗ No valid data found:', responseData);
                stopLoader(false, 'No valid data found.');
                return;
            }
            const container = $(".options_branch.branch_viewsall");
            container.empty();
            locations.forEach(function (location) {
                if (location.status === 1) {
                    const option = $('<div></div>')
                        .addClass('dropdown-option')
                        .attr('data-value', location.id)
                        .text(location.name);
                    container.append(option);
                }
            });
            handleSuccess(responseData, urlstatus);
            setTimeout(() => {
                $("#daily_details").show();
            }, 500);
        },
        error: function (xhr, status, error) {
            stopLoader(false, error);
            console.error('❌ AJAX Error:', {
                status,
                error,
                response: xhr.responseText
            });
        }
    });
}

function overall_fetch(statusid) {
    const moredatefittervale = $('#dateallviews').text();
    $(".value_views_mainsearch").text("");
    startLoader();  

    $.ajax({
        url: incomefetchUrl,
        type: "GET",
        data: {
            moredatefittervale,
            statusid,
            apistatus: 'dailyreport'
        },
        success: function (responseData) {
            stopLoader(true); 
            const locations = responseData.dropdown || responseData;
            const container = $(".options_branch.branch_viewsall");
            container.empty(); 
            locations.forEach(function (location) {
                if (location.status === 1) {
                    const option = $('<div></div>')
                        .addClass('dropdown-option')
                        .attr('data-value', location.id)
                        .text(location.name);
                    container.append(option);
                }
            });
            handleSuccess(responseData, 1);
        },
        error: function (xhr, status, error) {
            stopLoader(false, error); 
            console.error("❌ AJAX Error:", status, error);
        }
    });
}

	
	/*function morefilterview(uniqueResults, moredatefittervale, urlstatus, url) {
    $("#daily_details").hide(); 
    let progress = 0;
    const progressText = $('#progress-bar');
    const progressBar = $('#progress-bar');
    const errorMessage = $('#error-message');

    progressText.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    errorMessage.hide().text('');

    const interval = setInterval(() => {
        if (progress < 90) {
            progress += 10;
            progressText.text(`Loading: ${progress}%`);
            progressBar.css('width', `${progress}%`);
        }
    }, 300);

    if (!uniqueResults || uniqueResults.length === 0) {
        clearInterval(interval);
        progressBar.hide();
        progressText.text('');

        if (urlstatus == 1) {
            $(".search_daily").hide();
            $('.clear_views').hide();
            overall_fetch(2);
        }
        return;
    }

    const morefilltersall = uniqueResults.join(" AND");

    $.ajax({
        url: url,
        type: "GET",
        timeout: 15000, 
        data: {
            morefilltersall,
            moredatefittervale
        },
        success: function (responseData) {
            clearInterval(interval);
            progress = 100;
            progressText.text(`Loading: 100% - Done`);
            progressBar.css('width', '100%');

            let locations = [];
            if (Array.isArray(responseData)) {
                locations = responseData;
            } else if (responseData.dropdown && Array.isArray(responseData.dropdown)) {
                locations = responseData.dropdown;
            } else {
                progressText.text('No valid data found.');
                setTimeout(() => progressBar.hide(), 1000);
                return;
            }
            const container = $(".options_branch.branch_viewsall");
            container.empty();
            locations.forEach(function (location) {
                if (location.status === 1) {
                    const option = $('<div></div>')
                        .addClass('dropdown-option')
                        .attr('data-value', location.id)
                        .text(location.name);
                    container.append(option);
                }
            });
            handleSuccess(responseData, urlstatus);
            setTimeout(() => {
                progressBar.hide();
                $("#daily_details").show();
            }, 500);
        },
        error: function (xhr, status, error) {
            clearInterval(interval);
            console.error('AJAX Error:', {
                status,
                error,
                response: xhr.responseText
            });

            progressText.text('Error loading data');
            progressBar.css('background-color', 'red');

            errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`);
            errorMessage.show();

            setTimeout(() => {
                progressBar.hide();
                $("#daily_details").hide();
            }, 1000);
        }
    });
}*/

	var ticketdataSource = [];  
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
        $(".cincome_view").show();
        
		var resultsArray_marketer = [];
        $(".checkvalues_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#izone_views").val(),
                $("#ibranch_views").val(),
                $("#income_views").val(),
            ];

            $(".value_views_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,1,incomeBranchfitter);
        });		
	
	// date fitter function
function ticketdatefillterrange(datefiltervalue,url,urlstatus,fitterremovedata) {
	startLoader();  
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
    $("#daily_details").hide();
    $.ajax({
        url: url,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
			morefilltersall: morefilltersall,
        },
        success: function (responseData) {
			stopLoader(true);
           handleSuccess(responseData,urlstatus);
        },
        error: function (xhr, status, error) {
			stopLoader(false, error); 
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
	
		$('#total_cash').text(parseFloat(data[0]?.total_cash_amt || 0).toFixed(2));
		$('#total_card').text(parseFloat(data[0]?.total_card_amt || 0).toFixed(2));
		$('#total_cheque').text(parseFloat(data[0]?.totalPharmacyIncome || 0).toFixed(2));
		$('#total_dd').text(parseFloat(data[0]?.totalPharmacyIncome || 0).toFixed(2));
		$('#total_neft').text(parseFloat(data[0]?.total_neft_amt || 0).toFixed(2));
		$('#total_credit').text(parseFloat(data[0]?.totalPharmacyIncome || 0).toFixed(2));
		$('#total_upi').text(parseFloat(data[0]?.total_upi_amt || 0).toFixed(2));
		$('#total_amount').text(parseFloat(data[0]?.total_total_amt || 0).toFixed(2));
		var body = "";
			body = ticketData(pageData,body);

		$("#daily_details").html(body);
		$("#today_visits").text(totalItems);
		$("#dcounts").text(totalItems);
	}
	
	function ticketData(pageData,body){
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="11" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
			var zone_area;
			if(usr.zone_type == 1){
				 zone_area = usr.zone_name;
			}else if(usr.zone_type == 2){
				zone_area = usr.area;
			}else{
				zone_area = '-';
			}
			let dateStr = usr.date;
			let target_date = usr.billdate;	
			let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
			let	billDate = target_date ? moment(target_date).format("DD MMM YYYY") : "-";
			let	usrtype = usr.type ? usr.type : "-";
			if(usr.type === "Consolidated"){
				var cash_amt = usr.total_cash_amt ? usr.total_cash_amt : 0;	
				var dd_amt = usr.dd_amt ? usr.dd_amt : 0;	
				var neft_amt = usr.total_neft_amt ? usr.total_neft_amt : 0;	
				var credit_amt = usr.credit_amt ? usr.credit_amt : 0;	
				var card_amt = usr.total_card_amt ? usr.total_card_amt : 0;	
				var upi_amt = usr.total_upi_amt ? usr.total_upi_amt : 0;	
				var cheque_amt = usr.cheque_amt ? usr.cheque_amt : 0;
				var total_amt = usr.total_total_amt ? usr.total_total_amt : 0;				
			}
			else{
				var cash_amt = usr.cash_amt ? usr.cash_amt : 0;		
				var card_amt = usr.card_amt ? usr.card_amt : 0;		
				var upi_amt = usr.upi_amt ? usr.upi_amt : 0;		
				var cheque_amt = usr.cheque_amt ? usr.cheque_amt : 0;		
				var dd_amt = usr.dd_amt ? usr.dd_amt : 0;		
				var neft_amt = usr.neft_amt ? usr.neft_amt : 0;		
				var credit_amt = usr.credit_amt ? usr.credit_amt : 0;
				var total_amt = usr.total_amt ? parseFloat(usr.total_amt).toFixed(2) : 0;
			}
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"><strong>#' + sno + '</strong><br>' + billDate +'</td>' +
				   '<td class="tdview">' + usrtype + '</td>' +
				   '<td class="tdview">' + zone_area + '</td>' +
				    '<td class="tdview">' + cash_amt + '</td>' +
					'<td class="tdview">' + card_amt + '</td>' +
					'<td class="tdview">' + cheque_amt + '</td>' +	
					'<td class="tdview">' + dd_amt + '</td>' +					
					'<td class="tdview">' + neft_amt + '</td>' +					
					'<td class="tdview">' + credit_amt + '</td>' +					
					'<td class="tdview">' + upi_amt + '</td>' +					
					'<td class="tdview">' + total_amt + '</td>' +					
				   '</tr>';
				   sno++;
			});
		}
		return body;
	}
	
    $('.ranges, .applyBtn').on('click', function() {
		console.log(fitterremovedata);		
		if(fitterremovedata.length ==  0){
			 var defaultLocation = "CHENNAI";
			$('#izone_views').val(defaultLocation);
		
			$('.loct-dropdown-options div').each(function() {
				if ($(this).text().trim() === defaultLocation) {
					$(this).addClass('selected'); // Add class for styling the selected item
				}
			});
		}
        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
			
				var datefilltervalue = $('#dateallviews').text();
				var url = dateIncomeUrl;
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
           
				var url = dateIncomeUrl;
				var urlstatus = 1;			
			            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata);
        }
    });


    $(document).on("click", ".clear_views", function () {		
      	 $("#daily_details").hide();
		fitterremovedata.length = 0;	
        $(".value_views").text("");
        $('.checkvalues_search').val("");
        $('.clear_views').hide();
        $(".cincome_view").hide();
		$(".value_views_mainsearch").text("");
        overall_fetch(2);
    
    });

	 $(document).on("click", ".value_views_mainsearch", function () {
		var datefilltervalue = $('#dateallviews').text();	
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
		var clear_filr = $(this).attr('id');		
        $(this).text("");
		
		if(clear_filr == 'cbranch_search'){
				$('#izone_views').val('');
		}
		if(clear_filr == 'czone_search'){
			$('#ibranch_views').val('');	
		}
		if(clear_filr == 'income_search'){
			$('#income_views').val('');	
		}
		// Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
          morefilterview(fitterremovedata,datefilltervalue,1,incomeBranchfitter);
   
    });
	
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });	
   
});
