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

    var selectedValue = $(this).data('value');
    var selectedText  = $(this).text().trim();

    $(".value_views").text("");
    var moredatefittervale = $('#dateallviews').text();

    $('.clear_views').show();
    $(".cincome_view").show();

    // 🔥 Get related input
    var relatedInput = $(this).closest('.dropdown, .loct-dropdown').find('input');

    /* =========================
       🔥 MULTI SELECT FIX
    ========================= */
    var currentVal = relatedInput.val().trim();
    var valuesArr  = currentVal ? currentVal.split(',').map(v => v.trim()) : [];

    // toggle select
    if (!valuesArr.includes(selectedText)) {
        valuesArr.push(selectedText);
    }

    relatedInput.val(valuesArr.join(', ')); // 🔥 SET VALUE FIRST

    /* =========================
       Clear dependent filters
    ========================= */
    if (relatedInput.attr('id') === 'izone_views') {
        $('#ibranch_views').val('');
        $('#income_views').val('');
    }

    /* =========================
       BUILD FILTER ARRAY (AFTER VALUE SET)
    ========================= */
    var resultsArray_marketer = [];

    $(".checkvalues_search").each(function () {

        var value = $(this).val().trim();
        if (!value) return; // ignore empty safely

        var result = $(this).attr('name') + "='" + value.replace(/, /g, ',') + "'";
        resultsArray_marketer.push(result);
    });

    console.log("resultsArray_marketer", resultsArray_marketer);

    /* =========================
       UPDATE UI VALUES (FIXED)
    ========================= */
    var moreFilterValues_market = [
        $("#izone_views").val() || "",
        $("#ibranch_views").val() || "",
        $("#income_views").val() || "",
    ];

    $(".value_views_mainsearch").each(function (index) {
        $(this).text(moreFilterValues_market[index]);
    });

    /* =========================
       FINAL ASSIGNMENTS
    ========================= */
    marketersearchvalue = resultsArray_marketer;
    fitterremovedata    = resultsArray_marketer;
    fitterremovedata    = fitterremovedata.map(f => f.replace(/, /g, ','));

    // morefilterview(fitterremovedata, moredatefittervale, 1, incomeBranchfitter);
    clearTimeout(filterTriggerTimer);

    filterTriggerTimer = setTimeout(function () {
        morefilterview(
            fitterremovedata,
            moredatefittervale,
            1,
            incomeBranchfitter
        );
    }, 150);

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
		// 	stopLoader(true);
        //    handleSuccess(responseData,urlstatus);
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
			if(usrtype === "Consolidated"){
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
			const wrap = (value) => (usrtype === "Consolidated" ? `<strong>${value}</strong>` : value);
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview">' + wrap('#' + sno) + '<br>' + wrap(billDate) + '</td>' +
				   '<td class="tdview">' + wrap(zone_area) + '</td>' +
					'<td class="tdview">' + wrap(usrtype) + '</td>' +
					'<td class="tdview">' + wrap(cash_amt) + '</td>' +
					'<td class="tdview">' + wrap(card_amt) + '</td>' +
					'<td class="tdview">' + wrap(cheque_amt) + '</td>' +
					'<td class="tdview">' + wrap(dd_amt) + '</td>' +
					'<td class="tdview">' + wrap(neft_amt) + '</td>' +
					'<td class="tdview">' + wrap(credit_amt) + '</td>' +
					'<td class="tdview">' + wrap(upi_amt) + '</td>' +
					'<td class="tdview">' + wrap(total_amt) + '</td>' +
				   '</tr>';
				   sno++;
			});
		}
		return body;
	}

    $('.ranges, .applyBtn').on('click', function() {
		console.log(fitterremovedata);
		// if(fitterremovedata.length ==  0){
		// 	 var defaultLocation = "CHENNAI";
		// 	$('#izone_views').val(defaultLocation);

		// 	$('.loct-dropdown-options div').each(function() {
		// 		if ($(this).text().trim() === defaultLocation) {
		// 			$(this).addClass('selected'); // Add class for styling the selected item
		// 		}
		// 	});
		// }
        if (fitterremovedata.length === 0) {
            var defaultLocation = "TN CHENNAI";
            $('#izone_views')
                .val(defaultLocation)
                .data('values', [defaultLocation]);
            var defaultArr = [defaultLocation];
            $('.dropdown-options div[data-value]').each(function () {
                var text = $(this).text().trim();
                if (defaultArr.includes(text)) {
                    $(this).addClass('selected');
                } else {
                    $(this).removeClass('selected');
                }
            });
            fitterremovedata.push("tblzones.name='TN CHENNAI'");
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

    /* =========================
        🔥 SELECT ALL
        ========================= */
        $(document).on('click', '.select-all', function (e) {
            e.stopPropagation();

            const dropdown = $(this).closest('.dropdown-options');

            dropdown.find('div[data-value]').each(function () {
                if (!$(this).hasClass('selected')) {
                    $(this).trigger('click'); // calls SAME logic
                }
            });
        });



        /* =========================
        🔥 DESELECT ALL
        ========================= */
        $(document).on('click', '.deselect-all', function (e) {
            e.stopPropagation();

            // Clear inputs
            $('#izone_views').val('');
            $('#ibranch_views').val('');
            $('#income_views').val('');

            // Remove selected UI
            $('.options_branch div').removeClass('selected');

            // Clear display values
            $(".value_views_mainsearch").text("");

            var moredatefittervale = $('#dateallviews').text();

            // Empty filter array
            marketersearchvalue = [];
            fitterremovedata = [];

            // Call existing filter
            morefilterview([], moredatefittervale, 1, incomeBranchfitter);
        });
});
