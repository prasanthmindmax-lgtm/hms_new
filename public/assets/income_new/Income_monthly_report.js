function formatINR(num) {
    if (num == null || isNaN(num)) return '0.00';
    const n = Number(num);
    return '₹' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/** Indian money format (e.g. 38,65,363.73) - no currency symbol */
function formatIndianNum(num) {
    if (num == null || isNaN(num)) return '0.00';
    const n = Number(num);
    return n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

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
            $("#daily_details_recon").show();
        }, 500);
    } else {
        progressText.text('Error loading data');
        progressBar.css('background-color', 'red');
        errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            progressBar.hide();
            $("#daily_details_recon").hide();
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
            console.log("responseData",responseData);

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
// Prevent dropdown from closing when clicking on action buttons
$(document).on("click", ".dropdown-actions", function(e) {
    e.stopPropagation();
});

function overall_fetch(statusid) {
    const moredatefittervale = $('#dateallviews').text();
    $(".value_views_mainsearch").text("");
    startLoader();

    $.ajax({
        url: incomeMonthlyDatafitter,
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

function handleSuccess(responseData, urlstatus) {
    console.log("handleSuccess",responseData);

    if (urlstatus == 1) {
        $("#daily_details_recon").show();

        // Store daily_data for split-up modal and date-wise view
        window.dailyDataForMonthly = (responseData.daily_data && Array.isArray(responseData.daily_data))
            ? responseData.daily_data
            : [];

        // 🔥 FIX: Get the data array correctly
        let mocdocRows = [];

        if (responseData.data && Array.isArray(responseData.data)) {
            mocdocRows = responseData.data;
        } else if (Array.isArray(responseData)) {
            mocdocRows = responseData;
        }

        console.log("📊 MocDoc Rows:", mocdocRows); // Debug log
        ticketdataSource = mocdocRows;


        totalItems = mocdocRows.length;

        var ticketpageSize = parseInt($('#itemsPerPageSelect').val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);

        // If current view is date-wise, refresh date-wise table
        if (window.currentMonthlyViewMode === 'datewise') {
            renderDatewiseTable();
        }
    }
}
function groupByBillDateAndArea(rows) {

  const grouped = rows.reduce((acc, row) => {

    const key = row.billdate + "_" + row.zone_name;

    if (!acc[key]) {
      acc[key] = {
        zone_id: row.zone_id,
        billdate: row.billdate,
        zone_name: row.zone_name,
        total_cash: 0,
        total_card: 0,
        total_upi: 0,
        total_neft: 0,
        total_amount: 0
      };
    }

    acc[key].total_cash  += parseFloat(row.cash_amt  ?? row.total_cash_amt  ?? 0) || 0;
    acc[key].total_card  += parseFloat(row.card_amt  ?? row.total_card_amt  ?? 0) || 0;
    acc[key].total_upi   += parseFloat(row.upi_amt   ?? row.total_upi_amt   ?? 0) || 0;
    acc[key].total_neft  += parseFloat(row.neft_amt  ?? row.total_neft_amt  ?? 0) || 0;
    acc[key].total_amount+= parseFloat(row.total_amt ?? row.total_total_amt ?? 0) || 0;

    return acc;

  }, {});

  return Object.values(grouped);
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

    // morefilterview(fitterremovedata, moredatefittervale, 1, incomeMonthlyDatafitter);
    clearTimeout(filterTriggerTimer);

    filterTriggerTimer = setTimeout(function () {
        morefilterview(
            fitterremovedata,
            moredatefittervale,
            1,
            incomeMonthlyDatafitter
        );
    }, 150);

});


	// date fitter function
function ticketdatefillterrange(datefiltervalue,url,urlstatus,fitterremovedata) {
	startLoader();
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
    $("daily_details_recon").hide();
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

    console.log("📄 Rendering page data:", pageData);


    const consolidated = data.find(r => r.type === 'CONSOLIDATED');
    if (!consolidated) return;


    if (consolidated) {
        $('#total_cash').text(formatIndianNum(consolidated.moc_cash));
        $('#total_card').text(formatIndianNum(consolidated.moc_card));
        $('#total_neft').text(formatIndianNum(consolidated.moc_neft));
        $('#total_upi').text(formatIndianNum(consolidated.moc_upi));
        $('#total_other').text(formatIndianNum(consolidated.moc_others));
        $('#total_upi_card').text(formatIndianNum(consolidated.moc_total_upi_card));
        $('#total_amount').text(formatIndianNum(consolidated.moc_total));

        // Set Actual values
        $('#actuall_cash').text(formatIndianNum(consolidated.actual_cash));
        $('#actuall_card').text(formatIndianNum(consolidated.actual_card));
        $('#actuall_neft').text(formatIndianNum(consolidated.actual_neft));
        $('#actuall_upi').text(formatIndianNum(consolidated.actual_upi));
        $('#actuall_other').text(formatIndianNum(consolidated.actual_others));
        $('#actuall_bank_chagers').text(formatIndianNum(consolidated.bank_chargers));
        $('#actuall_total_upi_card').text(formatIndianNum(consolidated.bank_upi_card));
        $('#actuall_amount').text(formatIndianNum(consolidated.actual_total));

        // Calculate Differences (Actual - MocDoc)
        const cashDiff = parseFloat(consolidated.actual_cash || 0) - parseFloat(consolidated.moc_cash || 0);
        const cardDiff = parseFloat(consolidated.actual_card || 0) - parseFloat(consolidated.moc_card || 0);
        const neftDiff = parseFloat(consolidated.actual_neft || 0) - parseFloat(consolidated.moc_neft || 0);
        const upiDiff = parseFloat(consolidated.actual_upi || 0) - parseFloat(consolidated.moc_upi || 0);
        const otherDiff = parseFloat(consolidated.actual_others || 0) - parseFloat(consolidated.moc_others || 0);
        const upicardDiff = parseFloat((parseFloat((consolidated.moc_upi || 0) + (consolidated.moc_card || 0)))  - (parseFloat((consolidated.bank_chargers || 0) + (consolidated.bank_upi_card || 0))));
        const totalDiff = parseFloat(consolidated.actual_total || 0) - parseFloat(consolidated.moc_total || 0);

        // Set Difference values with color coding
        setDifferenceValue('#actuall_cash_diff', cashDiff);
        setDifferenceValue('#actuall_card_diff', cardDiff);
        setDifferenceValue('#actuall_neft_diff', neftDiff);
        setDifferenceValue('#actuall_upi_diff', upiDiff);
        setDifferenceValue('#actuall_other_diff', otherDiff);
        setDifferenceValue('#actuall_total_upicard_diff', upicardDiff);
        setDifferenceValue('#actuall_amount_diff', totalDiff);

    } else {
        $('#total_cash, #total_card, #total_neft, #total_upi, #total_other, #total_amount').text(formatIndianNum(0));
        $('#actuall_cash, #actuall_card, #actuall_neft, #actuall_upi, #actuall_other, #actuall_amount').text(formatIndianNum(0));
        $('#actuall_cash_diff, #actuall_card_diff, #actuall_neft_diff, #actuall_upi_diff, #actuall_other_diff, #actuall_amount_diff').text(formatIndianNum(0));
    }

    var body = "";
    body = ticketData(pageData, body);
    $("#daily_details_recon").html(body);
    $("#today_visits").text(totalItems);
    $("#dcounts").text(totalItems);

    // Store summary thead once for restoring when switching back from date-wise
    if (window.currentMonthlyViewMode !== 'datewise' && !window.originalMonthlyThead && $('#monthly_report_thead').length) {
        window.originalMonthlyThead = $('#monthly_report_thead').prop('outerHTML');
    }
}
function setDifferenceValue(selector, value) {
    const element = $(selector);
    const card = element.closest('.stat-card');
    // Display absolute value
    element.text(formatIndianNum(Math.abs(value)));
    // Reset
    element.removeClass('text-positive text-negative');
    card.removeClass('bg-positive bg-negative');

    if (value > 0) {
        element.addClass('text-positive');
        card.addClass('bg-positive');
    } else if (value < 0) {
        element.addClass('text-negative');
        card.addClass('bg-negative');
    }
}

// ---------- Split-up modal (like Income_reconciliation_overview) ----------
var TYPE_LABELS = {
    moc_cash_amt: 'MocDoc Cash',
    moc_card_amt: 'MocDoc Card',
    moc_neft_amt: 'MocDoc NEFT',
    moc_upi_amt: 'MocDoc UPI',
    moc_other_amt: 'MocDoc Other',
    moc_overall_total: 'MocDoc Total',
    deposite_amount: 'Actual Cash',
    mespos_card: 'Actual Card',
    bank_neft: 'Actual NEFT',
    mespos_upi: 'Actual UPI',
    bank_others: 'Actual Other',
    actual_total: 'Actual Total',
    diff_cash: 'Difference Cash',
    diff_card: 'Difference Card',
    diff_neft: 'Difference NEFT',
    diff_upi: 'Difference UPI',
    diff_other: 'Difference Other',
    diff_upicard: 'Difference UPI/Card',
    diff_total: 'Difference Total'
};

var TYPE_TO_FILE_FIELD_MONTHLY = {
    deposite_amount: 'deposit_amount_files',
    mespos_card: 'mespos_card_files',
    mespos_upi: 'mespos_upi_files',
    bank_chargers: 'bank_chargers_files',
    bank_upi_card: 'bank_upi_card_files',
    bank_neft: 'bank_neft_files',
    bank_others: 'bank_others_files'
};

var TYPE_TO_UTR_FIELD_MONTHLY = {
    deposite_amount: 'cash_utr_files',
    mespos_card: 'card_upi_utr_files',
    mespos_upi: 'card_upi_utr_files',
    bank_chargers: 'card_upi_utr_files',
    bank_upi_card: 'card_upi_utr_files',
    bank_neft: 'neft_utr_files',
    bank_others: 'other_utr_files'
};

var TYPE_TO_UTR_NUMBER_KEY = {
    deposite_amount: 'cash_utr_number',
    mespos_card: 'bank_upi_card_utr',
    mespos_upi: 'bank_upi_card_utr',
    bank_chargers: 'bank_upi_card_utr',
    bank_upi_card: 'bank_upi_card_utr',
    bank_neft: 'bank_neft_utr',
    bank_others: 'bank_other_utr'
};

function getFilesForRowMonthly(row, type) {
    var field = TYPE_TO_FILE_FIELD_MONTHLY[type];
    if (!field || !row[field]) return [];
    try {
        var val = row[field];
        var arr = typeof val === 'string' ? JSON.parse(val) : val;
        return Array.isArray(arr) ? arr : [];
    } catch (e) { return []; }
}

function getUtrFilesForRowMonthly(row, type) {
    var field = TYPE_TO_UTR_FIELD_MONTHLY[type];
    if (!field || !row[field]) return [];
    try {
        var val = row[field];
        var arr = typeof val === 'string' ? JSON.parse(val) : val;
        return Array.isArray(arr) ? arr : [];
    } catch (e) { return []; }
}

function openFilePreviewMonthly(files) {
    if (!files || !files.length) return;
    var file = files[0];
    var filePath = (file && file.path) ? file.path : (typeof file === 'string' ? file : '');
    if (!filePath) {
        $('#filePreviewBody').html('<p class="text-muted">No preview</p>');
        $('#filePreviewModal').modal('show');
        return;
    }
    var remark = '';
    var html = '';
    if (remark) html += '<div class="alert alert-info mb-3"><strong>Remark:</strong> ' + remark + '</div>';
    var src = (filePath.indexOf('http') === 0 || filePath.indexOf('/') === 0) ? filePath : '../' + filePath;
    var ext = (filePath.split('.').pop() || '').toLowerCase();
    if (['jpg','jpeg','png','gif','webp'].indexOf(ext) !== -1) {
        html += '<img src="' + src + '" class="img-fluid">';
    } else if (ext === 'pdf') {
        html += '<iframe src="' + src + '" width="100%" height="600px"></iframe>';
    } else {
        html += '<p>No preview available</p><a href="' + src + '" download class="btn btn-primary">Download File</a>';
    }
    if (files.length > 1) {
        files.forEach(function (f, i) {
            if (i === 0) return;
            var p = (f && f.path) ? f.path : (typeof f === 'string' ? f : '');
            var s = (p.indexOf('http') === 0 || p.indexOf('/') === 0) ? p : '../' + p;
            html += '<div class="mt-2"><a href="' + s + '" download class="btn btn-sm btn-outline-secondary">Download ' + (i + 1) + '</a></div>';
        });
    }
    $('#filePreviewBody').html(html);
    $('#filePreviewModal').modal('show');
}

$(document).on('click', '.stat-click', function () {
    var type = $(this).data('type');
    if (!type) return;
    var daily = window.dailyDataForMonthly || [];
    // var filtered = daily.filter(function (row) {
    //     var amt = parseFloat(row[type] || 0);
    //     return amt > 0;
    // });
    // buildMocdocTableMonthly(filtered, type);
    // $('#mocdocModal').modal('show');
    let map = {
        diff_cash: ["deposite_amount", "moc_cash_amt"],
        diff_card: ["mespos_card", "moc_card_amt"],
        diff_upi: ["mespos_upi", "moc_upi_amt"],
        diff_neft: ["bank_neft", "moc_neft_amt"],
        diff_other: ["bank_others", "moc_other_amt"],
        diff_total: ["actual_total", "moc_overall_total"]
    };

    let rows = [];
    if (map[type]) {

        let actualField = map[type][0];
        let mocField = map[type][1];

        rows = daily.map(function (row) {

            let actual = parseFloat(row[actualField] || 0);
            let moc = parseFloat(row[mocField] || 0);

            return {...row,[type]: actual - moc};
        }).filter(r => Math.abs(r[type]) > 0);

        buildMocdocTableMonthly(rows, type);
    }

    else if (type === "diff_upicard") {
        rows = daily.map(function (row) {

            let actual = parseFloat(row.bank_chargers || 0) + parseFloat(row.bank_upi_card || 0);
            let moc = parseFloat(row.moc_upi_amt || 0) + parseFloat(row.moc_card_amt || 0);
            return { ...row, diff_upicard: actual - moc};

        }).filter(r => Math.abs(r.diff_upicard) > 0);

        buildMocdocTableMonthly(rows, type);
    }
    else {
        rows = daily.filter(function (row) {
            return parseFloat(row[type] || 0) > 0;
        });

        buildMocdocTableMonthly(rows, type);
    }
    $("#mocdocModal").modal("show");
});

function zoneLoc(row) {
    return [row.zone_name, row.location_name].filter(Boolean).join(' / ') || '-';
}

function buildMocdocTableMonthly(filtered, type, branchFilter) {
    var label = TYPE_LABELS[type] || type.replace(/_/g, ' ');
    window._mocdocFilteredData = filtered;
    window._mocdocType = type;

    var showAll = !branchFilter;
    if (showAll) {
        $('#mocdocModalTitle').text(label + ' – Date Wise');
        $('#mocdocModalBackLink').hide();
        buildBranchTotalsMonthly(filtered, type);
    } else {
        $('#mocdocModalTitle').text('Branch: ' + branchFilter);
        $('#mocdocModalBackLink').show();
        $('#mocdocModalBranchTotals').empty();
    }

    var hasActualFiles = TYPE_TO_FILE_FIELD_MONTHLY[type] || TYPE_TO_UTR_FIELD_MONTHLY[type];
    var utrKey = TYPE_TO_UTR_NUMBER_KEY[type];
    var thead = '<tr><th>Date</th><th>Zone / Location</th>';
    if (hasActualFiles) thead += '<th>UTR / Transaction ID</th>';
    thead += '<th>Amount</th></tr>';
    $('#mocdocModalHead').html(thead);

    window._mocdocAmountFiles = [];
    window._mocdocUtrFiles = [];
    var dataToShow = showAll ? filtered : filtered.filter(function (r) { return zoneLoc(r) === branchFilter; });
    var originalIndices = [];
    if (branchFilter) {
        filtered.forEach(function (r, i) {
            if (zoneLoc(r) === branchFilter) originalIndices.push(i);
        });
    }
    var grandTotal = 0;
    var tbody = '';
    dataToShow.forEach(function (row, idx) {
        var rowIndex = branchFilter ? originalIndices[idx] : idx;
        var amt = parseFloat(row[type] || 0);
        grandTotal += amt;
        var zoneLocStr = zoneLoc(row);
        var amountFiles = hasActualFiles ? getFilesForRowMonthly(row, type) : [];
        var utrFiles = hasActualFiles && utrKey ? getUtrFilesForRowMonthly(row, type) : [];
        if (amountFiles.length) window._mocdocAmountFiles[rowIndex] = amountFiles;
        if (utrFiles.length) window._mocdocUtrFiles[rowIndex] = utrFiles;
        var amountClass = amountFiles.length ? 'text-end amount-with-attachment' : 'text-end';
        var amountData = amountFiles.length ? ' data-row-index="' + rowIndex + '"' : '';
        var utrClass = utrFiles.length ? 'mocdoc-utr-cell utr-with-attachment' : 'mocdoc-utr-cell';
        var utrData = utrFiles.length ? ' data-utr-row-index="' + rowIndex + '"' : '';
        var utrVal = (utrKey && row[utrKey]) ? row[utrKey] : '-';
        var fileIcon = '<i class="fa fa-file-o text-muted" style="font-size:11px;margin-left:4px;"></i>';
        var amountHtml = formatINR(amt) + (amountFiles.length ? fileIcon : '');
        var utrHtml = utrVal + (utrFiles.length ? fileIcon : '');

        tbody += '<tr><td>' + (row.date_range || '-') + '</td><td class="mocdoc-branch-cell">' + zoneLocStr + '</td>';
        if (hasActualFiles) {
            tbody += '<td class="' + utrClass + '"' + utrData + '>' + utrHtml + '</td>';
        }
        tbody += '<td class="' + amountClass + '"' + amountData + '>' + amountHtml + '</td></tr>';
    });
    var colCount = hasActualFiles ? 4 : 3;
    tbody += '<tr class="table-primary fw-bold"><td colspan="' + (colCount - 1) + '" class="text-end">Total</td><td class="text-end">' + formatINR(grandTotal) + '</td></tr>';
    $('#mocdocModalBody').html(tbody);
}

function buildBranchTotalsMonthly(filtered, type) {
    var byBranch = {};
    filtered.forEach(function (row) {
        var key = zoneLoc(row);
        if (!byBranch[key]) byBranch[key] = 0;
        byBranch[key] += parseFloat(row[type] || 0);
    });
    var entries = Object.entries(byBranch).sort(function (a, b) { return b[1] - a[1]; });
    var html = '<strong class="d-block mb-2">Branch-wise total</strong><div class="d-flex flex-wrap gap-2">';
    entries.forEach(function (e) {
        var name = (e[0] || '').replace(/"/g, '&quot;');
        html += '<span class="badge bg-primary mocdoc-branch-total" data-branch-name="' + name + '" style="cursor:pointer;font-size:11px;">' + e[0] + ': ' + formatINR(e[1]) + '</span>';
    });
    html += '</div>';
    $('#mocdocModalBranchTotals').html(html);
}

$(document).on('click', '#mocdocModal .mocdoc-branch-total', function (e) {
    e.preventDefault();
    var branchName = $(this).data('branch-name');
    if (!branchName) return;
    var filtered = window._mocdocFilteredData || [];
    var type = window._mocdocType;
    buildMocdocTableMonthly(filtered, type, branchName);
});

$(document).on('click', '#mocdocModal .mocdoc-back-to-all', function (e) {
    e.preventDefault();
    var filtered = window._mocdocFilteredData || [];
    var type = window._mocdocType;
    buildMocdocTableMonthly(filtered, type);
});

$(document).on('click', '#mocdocModal .amount-with-attachment', function (e) {
    e.preventDefault();
    var rowIndex = $(this).data('row-index');
    if (rowIndex == null || rowIndex === undefined) return;
    var files = (window._mocdocAmountFiles || [])[rowIndex];
    if (files && files.length) openFilePreviewMonthly(files);
});

$(document).on('click', '#mocdocModal .utr-with-attachment', function (e) {
    e.preventDefault();
    var rowIndex = $(this).data('utr-row-index');
    if (rowIndex == null || rowIndex === undefined) return;
    var files = (window._mocdocUtrFiles || [])[rowIndex];
    if (files && files.length) openFilePreviewMonthly(files);
});

// ---------- View mode: Summary vs Date-wise (1-31) ----------
window.currentMonthlyViewMode = 'summary';

$(document).on('click', '.view-mode-btn', function () {
    var mode = $(this).data('mode');
    $('.view-mode-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
    $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
    window.currentMonthlyViewMode = mode;
    if (mode === 'summary') {
        $('#monthly_report_table').css('min-width', '');
        if (window.originalMonthlyThead) {
            $('#monthly_report_thead').replaceWith(window.originalMonthlyThead);
        }
        $('#daily_details_recon').show();
        var ticketpageSize = parseInt($('#itemsPerPageSelect').val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);
    } else {
        renderDatewiseTable();
    }
});

function renderDatewiseTable() {
    var daily = window.dailyDataForMonthly || [];
    // Parse date_range (DD/MM/YYYY) and group by branch, then by day 1-31
    var branchMap = {};
    var daysInMonth = 31;
    daily.forEach(function (row) {
        var key = (row.Zone || '') + '|' + (row.Branch || '');
        if (!branchMap[key]) {
            branchMap[key] = { Zone: row.Zone || '-', Branch: row.Branch || '-', days: {}, total: 0 };
        }
        var parts = (row.date_range || '').split('/');
        var day = parts.length >= 1 ? parseInt(parts[0], 10) : 0;
        if (day >= 1 && day <= 31) {
            var amt = parseFloat(row.moc_overall_total || 0);
            branchMap[key].days[day] = (branchMap[key].days[day] || 0) + amt;
            branchMap[key].total += amt;
        }
    });
    var branchRows = Object.keys(branchMap).sort().map(function (k) { return branchMap[k]; });

    $('#monthly_report_table').css('min-width', '3200px');
    var th = '<tr><th>S.no</th><th>Zone</th><th>Branch</th>';
    for (var d = 1; d <= daysInMonth; d++) th += '<th>' + d + '</th>';
    th += '<th>Total</th></tr>';
    $('#monthly_report_thead').html(th);

    var body = '';
    var sno = 1;
    branchRows.forEach(function (r) {
        body += '<tr><td>' + sno + '</td><td>' + r.Zone + '</td><td>' + r.Branch + '</td>';
        for (var d = 1; d <= daysInMonth; d++) {
            var val = r.days[d];
            body += '<td class="text-end">' + (val != null && val !== 0 ? formatIndianNum(val) : '-') + '</td>';
        }
        body += '<td class="text-end fw-bold">' + (r.total != null ? formatIndianNum(r.total) : formatIndianNum(0)) + '</td></tr>';
        sno++;
    });
    if (branchRows.length === 0) body = '<tr><td colspan="35" class="text-center">No data</td></tr>';
    $('#daily_details_recon').html(body).show();
    $('#ticketpagination').empty();
}



// Enhanced color update function
// function updateDifferenceColors() {
//     const diffElements = [
//         'actuall_cash_diff',
//         'actuall_card_diff',
//         'actuall_neft_diff',
//         'actuall_upi_diff',
//         'actuall_other_diff',
//         'actuall_amount_diff'
//     ];

//     diffElements.forEach(id => {
//         const element = document.getElementById(id);
//         console.log("element",element);

//         if (element) {
//             const value = parseFloat(element.textContent.replace(/[^0-9.-]/g, ''));
//             console.log("11value",value);

//             const card = element.closest('.stat-card');

//             // Reset classes
//             element.classList.remove('text-positive', 'text-negative');
//             card.classList.remove('bg-positive', 'bg-negative');

//             if (value > 0) {
//                 console.log("1pos");

//                 element.classList.add('text-positive');
//                 card.classList.add('bg-positive');
//             } else if (value < 0) {
//                 console.log("1neg");
//                 element.classList.add('text-negative');
//                 card.classList.add('bg-negative');
//             }
//         }
//     });

//     // Update excess card styling (always green if > 0)
//     const excessElements = [
//         'excess_cash',
//         'excess_card',
//         'excess_neft',
//         'excess_upi',
//         'excess_other',
//         'excess_amount'
//     ];

//     excessElements.forEach(id => {
//         const element = document.getElementById(id);
//         if (element) {
//             const value = parseFloat(element.textContent.replace(/[^0-9.-]/g, ''));
//             const card = element.closest('.stat-card');

//             // Reset classes
//             card.classList.remove('bg-excess');

//             if (value > 0) {
//                 card.classList.add('bg-excess');
//             }
//         }
//     });
// }

// Also call loadRadiantValues() when user filters
$(document).on("change", "#izone_views, #ibranch_views, #reportrange", function() {
    $("#daily_details_recon tr").each(function () {
        loadRadiantValues($(this));
    });
});
function diffCell(value) {
    value = parseFloat(value) || 0;

    let cls = 'diff-zero';
    if (value < 0) cls = 'diff-positive';
    else if (value > 0) cls = 'diff-negative';

    return `<td class="tdview ${cls}">${formatIndianNum(Math.abs(value))}</td>`;
}


function ticketData(pageData, body) {
    var sno = 1;

    // Initialize totals
    var totals = {
        moc_cash: 0,
        moc_card: 0,
        moc_upi: 0,
        moc_total_upi_card: 0,
        moc_neft: 0,
        moc_others: 0,
        actual_cash: 0,
        bank_chargers: 0,
        bank_upi_card: 0,
        actual_neft: 0,
        actual_others: 0,
        cash_diff: 0,
        upi_card_diff: 0,
        neft_diff: 0,
        others_diff: 0,

    };

    if (pageData.length === 0) {
        body += '<tr><td colspan="27" style="text-align:center">No Data</td></tr>';
        return body;
    }

    pageData.forEach(usr => {
        if (usr.type === "CONSOLIDATED") return;
        // Parse numeric values
        const moc_cash = parseFloat(usr.moc_cash) || 0;
        const moc_card = parseFloat(usr.moc_card) || 0;
        const moc_upi = parseFloat(usr.moc_upi) || 0;
        const moc_total_upi_card = parseFloat(usr.moc_total_upi_card) || 0;
        const moc_neft = parseFloat(usr.moc_neft) || 0;
        const moc_others = parseFloat(usr.moc_others) || 0;
        const actual_cash = parseFloat(usr.actual_cash) || 0;
        const bank_chargers = parseFloat(usr.bank_chargers) || 0;
        const bank_upi_card = parseFloat(usr.bank_upi_card) || 0;
        const actual_neft = parseFloat(usr.actual_neft) || 0;
        const actual_others = parseFloat(usr.actual_others) || 0;
        const cash_diff = parseFloat(usr.cash_diff) || 0;
        const upi_card_diff = parseFloat(usr.upi_card_diff) || 0;
        const neft_diff = parseFloat(usr.neft_diff) || 0;
        const others_diff = parseFloat(usr.others_diff) || 0;


        // Add to totals
        totals.moc_cash += moc_cash;
        totals.moc_card += moc_card;
        totals.moc_upi += moc_upi;
        totals.moc_total_upi_card += moc_total_upi_card;
        totals.moc_neft += moc_neft;
        totals.moc_others += moc_others;
        totals.actual_cash += actual_cash;
        totals.bank_chargers += bank_chargers;
        totals.bank_upi_card += bank_upi_card;
        totals.actual_neft += actual_neft;
        totals.actual_others += actual_others;
        totals.cash_diff += cash_diff;
        totals.upi_card_diff += upi_card_diff;
        totals.neft_diff += neft_diff;
        totals.others_diff += others_diff;

        body += `
        <tr>
            <td class="tdview">#${sno}</td>
            <td class="tdview zone_name" data-zone_name="${usr.Zone}">${usr.Zone}</td>
            <td class="tdview location-cell" data-location="${usr.Branch}">${usr.Branch}</td>
            <td class="tdview cash_mocdoc">${formatIndianNum(usr.moc_cash)}</td>
            <td class="tdview card_mocdoc">${formatIndianNum(usr.moc_card)}</td>
            <td class="tdview upi_mocdoc">${formatIndianNum(usr.moc_upi)}</td>
            <td class="tdview total_upi_card">${formatIndianNum(usr.moc_total_upi_card)}</td>
            <td class="tdview neft_mocdoc">${formatIndianNum(usr.moc_neft)}</td>
            <td class="tdview other_mocdoc">${formatIndianNum(usr.moc_others)}</td>
            <td class="tdview actual_cash">${formatIndianNum(usr.actual_cash)}</td>
            <td class="tdview bank_chargers">${formatIndianNum(usr.bank_chargers)}</td>
            <td class="tdview bank_upi_card">${formatIndianNum(usr.bank_upi_card)}</td>
            <td class="tdview actual_neft">${formatIndianNum(usr.actual_neft)}</td>
            <td class="tdview actual_others">${formatIndianNum(usr.actual_others)}</td>
            ${diffCell(usr.cash_diff)}
            ${diffCell(usr.upi_card_diff)}
            ${diffCell(usr.neft_diff)}
            ${diffCell(usr.others_diff)}
             <!-- 👁 Eye Icon -->
                <td class="tdview text-center">
                    <i class="fa fa-eye text-primary view-remarks"
                    style="cursor:pointer"
                    data-remarks='${JSON.stringify(usr.date_remarks)}'>
                    </i>
                </td>

        </tr>
        `;
        sno++;
    });

    // Add totals row with color-coded differences
    body += `
    <tr class="total-row" style="background-color: #f8f9fa; font-weight: bold;">
        <td class="tdview" colspan="3">Total</td>
        <td class="tdview">${formatIndianNum(totals.moc_cash)}</td>
        <td class="tdview">${formatIndianNum(totals.moc_card)}</td>
        <td class="tdview">${formatIndianNum(totals.moc_upi)}</td>
        <td class="tdview">${formatIndianNum(totals.moc_total_upi_card)}</td>
        <td class="tdview">${formatIndianNum(totals.moc_neft)}</td>
        <td class="tdview">${formatIndianNum(totals.moc_others)}</td>
        <td class="tdview">${formatIndianNum(totals.actual_cash)}</td>
        <td class="tdview">${formatIndianNum(totals.bank_chargers)}</td>
        <td class="tdview">${formatIndianNum(totals.bank_upi_card)}</td>
        <td class="tdview">${formatIndianNum(totals.actual_neft)}</td>
        <td class="tdview">${formatIndianNum(totals.actual_others)}</td>
        <td class="tdview">${formatIndianNum(totals.cash_diff)}</td>
        <td class="tdview">${formatIndianNum(totals.upi_card_diff)}</td>
        <td class="tdview">${formatIndianNum(totals.neft_diff)}</td>
        <td class="tdview">${formatIndianNum(totals.others_diff)}</td>

    </tr>
    `;

    return body;
}


// EDIT BUTTON
let cashModal = new bootstrap.Modal(document.getElementById("cashRadiantModal"));
let activeRow = null;
let activeField = null;

const fieldLabels = {
    cash_radiant: "Cash Radiant Amount",
    cash_bank: "Cash Bank Amount",
    card_radiant: "Card Radiant Amount",
    card_bank: "Card Bank Amount",
    upi_radiant: "UPI Radiant Amount",
    upi_bank: "UPI Bank Amount",
    neft_bank: "NEFT Bank Amount"
};
$(document).on("click", ".edit-btn", function () {
    let tr = $(this).closest("tr");

    tr.find(".editable span").attr("contenteditable", "true")
                             .addClass("editing");

    $(this).hide();
    tr.find(".plus-icon").hide();
    // tr.find(".apply-btn").show();
    tr.find(".calendar-icon").show();
    tr.find(".save-btn").show();
    tr.find(".back-btn").show();
});
$(document).on("input", ".editable .edit-text", function () {

    let span = $(this);
    let td = span.closest(".editable");
    let value = span.text().trim();

    // show + icon only if user typed something
    if (value !== "") {
        td.find(".plus-icon").fadeIn(150);
    } else {
        td.find(".plus-icon").fadeOut(150);
    }
});
// PLUS ICON CLICK - show modal for the selected field
$(document).on("click", ".plus-icon", function (e) {
    e.preventDefault();
    e.stopPropagation();

    activeRow = $(this).closest("tr");
    activeField = $(this).data("field");

    // hide all inputs first
    $(".modal-input").hide().val("");

    // show only relevant input
    let input = $('.modal-input[data-field="' + activeField + '"]');
    input.show();

    // update modal title
    $(".modal-title").text(fieldLabels[activeField]);

    cashModal.show();
});
$(document).on("click", ".back-btn", function () {
    let tr = $(this).closest("tr");

    tr.find(".editable span").attr("contenteditable", "false")
                             .removeClass("editing");

    $(this).hide();
    tr.find(".edit-btn").show();
    tr.find(".save-btn").hide();
    tr.find(".calendar-icon").hide();
});

    $('.ranges, .applyBtn').on('click', function() {
		console.log(fitterremovedata);

        if (fitterremovedata.length === 0) {
            var defaultLocation = "TN CHENNAI";
            // 🔥 set input value
            $('#izone_views')
                .val(defaultLocation)
                .data('values', [defaultLocation]);
            console.log("121");
            // 🔥 build array correctly
            var defaultArr = [defaultLocation];
            // 🔥 reset + select correct option
            $('.dropdown-options div[data-value]').each(function () {
                   var text = $(this).text().trim();
                if (defaultArr.includes(text)) {
                    $(this).addClass('selected');
                } else {
                    $(this).removeClass('selected');
                }
            });
            // 🔥 push correct filter format
            fitterremovedata.push("tblzones.name='TN CHENNAI'");
        }


        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {

				var datefilltervalue = $('#dateallviews').text();
				var url = dateOverviewIncomeUrl;
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

				var url = dateOverviewIncomeUrl;
				var urlstatus = 1;

            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata);
        }
    });


    $(document).on("click", ".clear_views", function () {
      	 $("#daily_details_recon").hide();
		fitterremovedata.length = 0;
        $(".value_views").text("");
        $('.checkvalues_search').val("");
        $('.clear_views').hide();
        $(".cincome_view").hide();
        $(".searchZone").val("");
        $(".searchZone").data("values", []);
        $(".options_branch .dropdown-option").removeClass("selected");
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
          morefilterview(fitterremovedata,datefilltervalue,1,incomeMonthlyDatafitter);

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
        morefilterview([], moredatefittervale, 1, incomeMonthlyDatafitter);
    });
let $clickedIcon = null;
let currentField = null;
let lastRowId = null;

$(document).on('click', '.calendar-icon', function () {

    $clickedIcon = $(this);
    currentField = $(this).data('field');

    let $tr = $(this).closest('tr');
    let rowId = $tr.find('.location-cell').data('location') + "_" + $tr.index();

    if (rowId !== lastRowId) {
        $('#fromDate').val('');
        $('#toDate').val('');
        $('#remark').val('');
        $('#mocFile').val('');
    }

    lastRowId = rowId;
    $('#dateModal').modal('show');
});
$('#applyDates').on('click', function () {

    let $btn = $(this);

    let fromDate = $('#fromDate').val();
    let toDate   = $('#toDate').val();
    let remark   = $('#remark').val();
    let file     = $('#mocFile')[0].files[0];  // <-- read only

    if (!fromDate || !toDate) return alert("Select both From & To date");
    if (!$clickedIcon) return alert("No field selected");
    if (!file) return alert("Upload file before continue");

    $btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

    let $td = $clickedIcon.closest('td');
    let activetab = $td.attr('id');
    let $tr = $clickedIcon.closest('tr');
    let branch = $tr.find('.location-cell').data('location');
    let formatted = format(fromDate) + " - " + format(toDate);

    /* ----------------------------------------------------
       STEP-1 : CHECK DATE RANGE — DO NOT UPLOAD FILE YET
    ---------------------------------------------------- */
    $.ajax({
        url  : incomedatecheck,
        type : "POST",
        data : {
            branch_name : branch,
            from_date   : fromDate,
            to_date     : toDate,
            column      : currentField,
            type        : 1,
            _token      : $('meta[name="csrf-token"]').attr('content')
        },

        success:function(res){

            // ❌ Stop if blocked by date_filter
            if(res.status === 'exists_in_filter'){
                resetBtn();
                return alert(res.message);
            }

            /* ----------------------------------------------------
               STEP-2 : IF MISSING DATES → FETCH & INSERT FIRST
            ---------------------------------------------------- */
            if(res.missing_dates?.length){

                let missing = res.missing_dates;
                formatted = missing[0] + " - " + missing[missing.length-1];

                rowfilterrange(
                    [`tbl_locations.name='${branch}'`],
                    formatted,
                    1,
                    incomeBranchfitter,
                    function(result){
                        console.log("store result",result);

                        $.ajax({
                            url  : incomestore,
                            type : "POST",
                            data : {
                                mocdata     : result,
                                branch_name : branch,
                                from_date   : fromDate,
                                to_date     : toDate,
                                column      : currentField,
                                remark      : remark,
                                file_path   : "",   // <-- NOT SENT YET
                                _token      : $('meta[name="csrf-token"]').attr('content')
                            },

                            success:function(storeRes){
                                // Now upload the file
                                uploadFileAndFinish(storeRes);
                            },
                            error: fail
                        });
                    }
                );

            } else {

                // No missing dates — just upload file after date check
                uploadFileAndFinish(res);
            }
        },
        error: fail
    });


    /* ----------------------------------------------------
       STEP-3 : NOW ACTUALLY UPLOAD THE FILE
    ---------------------------------------------------- */
    function uploadFileAndFinish(dataFromDB){

        let fd = new FormData();
        fd.append("_token", $('meta[name="csrf-token"]').attr('content'));
        fd.append("file", file);
        fd.append("field", activetab);
        fd.append("remark", remark);
        fd.append("branch", branch);
        fd.append("location_name", branch);
        fd.append("date_range", $('#dateallviews').text().split('-')[0].trim());

        $.ajax({
            url  : incomeuploadFile,
            type : "POST",
            data : fd,
            contentType : false,
            processData : false,

            success:function(fileRes){

                // ⛑ SAFE tooltip build
                buildTooltip(
                    $clickedIcon,
                    dataFromDB,
                    formatted,
                    remark,
                    fileRes.path       // <-- FILE PATH NOW AVAILABLE
                );

                closeAndReset();
                alert("Reconciliation completed & file uploaded");
            },
            error: fail
        });
    }


    function fail(){
        resetBtn();
        alert("Something went wrong");
    }

    function resetBtn(){
        $btn.prop("disabled", false).html("Apply & Upload");
    }

    function closeAndReset(){
        resetBtn();
        $('#dateModal').modal('hide');
    }

    function format(d){
        let p = d.split("-");
        return p[2]+"/"+p[1]+"/"+p[0];
    }

});

function buildTooltip($icon, res, formatted, remark, filePath){
    let td = $icon.closest('td');
    td.find('.custom-tooltip').remove();

    let html = `<div class="custom-tooltip" style="display:none;">
                    <table>`;

    res.totallist.forEach(r=>{
        html += `<tr>
                    <td>${r.date_range}</td>
                    <td style="text-align:right;">${formatINR(r.amount)}</td>
                 </tr>`;
    });

    html += `<tr class="total">
                <td>Total</td>
                <td style="text-align:right;color: black;">${formatINR(res.total_amount)}</td>
             </tr>
             </table>

             <p><b>Remark:</b> ${remark || '-'}</p>

             <a class="file-view" target="_blank"
                href="${window.location.origin}/hms_new/public/radiant_files/${filePath}">
                View Uploaded File
             </a>

             <br>
             <span>Date:</span>
             <span>${formatted}</span>
             </div>`;

    td.find('.custom-tooltip').remove();
    td.append(html);

    td.find('.tooltip-text').text(res.total_amount);
    td.find('.calander-text').text(formatted);
    td.find('.info-icon').show();
}
let tooltipTimeout = null;

$(document).on('mouseenter', '.info-icon', function () {

    clearTimeout(tooltipTimeout);

    const $tip  = $(this).siblings('.custom-tooltip');

    const rect = this.getBoundingClientRect();

    let top  = rect.bottom + 6;
    let left = rect.left;

    const tipW = $tip.outerWidth();
    const vw   = $(window).width();

    if (left + tipW > vw - 10) left = vw - tipW - 10;
    if (left < 10) left = 10;

    $tip.css({
        position: 'fixed',
        top,
        left,
        zIndex: 9999,
        display: 'block'
    }).show();
});

$(document).on('mouseleave', '.info-icon', function () {

    const $tip  = $(this).siblings('.custom-tooltip');

    tooltipTimeout = setTimeout(() => {
        $tip.hide();
    }, 200);
});

$(document).on('mouseenter', '.custom-tooltip', function () {
    clearTimeout(tooltipTimeout);
    $(this).show();
});

$(document).on('mouseleave', '.custom-tooltip', function () {
    $(this).hide();
});

$('#downloadExcelBtn').on('click', function (e) {
    e.preventDefault();

    let datefiltervalue = $('#dateallviews').text();
    let filterRemoveData = fitterremovedata;

    let params = new URLSearchParams();
    params.set('datefiltervalue', datefiltervalue);
    params.set('filterRemoveData', filterRemoveData);

    window.location.href = downloadIncomeUrl + "?" + params.toString();
});
$(document).on("click",".remark-preview-icon, .preview-file, .file-item, .file-list li",function () {

        let file = $(this).data("file") || '';
        let remark = $(this).data("remark") || '';

        let html = '';

        // 👉 Always show remark if exists
        if (remark) {
            html += `
                <div class="alert alert-info mb-3">
                    <strong>Remark:</strong> ${remark}
                </div>
            `;
        }

        // 👉 If NO file → ONLY remark
        if (!file) {
            $("#filePreviewBody").html(html);
            $("#filePreviewModal").modal("show");
            return;
        }

        // 👉 If file exists → show preview
        let filePath = `../${file}`;
        let ext = file.split('.').pop().toLowerCase();

        if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
            html += `<img src="${filePath}" class="img-fluid">`;
        }
        else if (ext === 'pdf') {
            html += `<iframe src="${filePath}" width="100%" height="600px"></iframe>`;
        }
        else {
            html += `
                <p>No preview available</p>
                <a href="${filePath}" download class="btn btn-primary">
                    Download File
                </a>
            `;
        }

        $("#filePreviewBody").html(html);
        $("#filePreviewModal").modal("show");
    }
);

$(document).on('click', '.view-remarks', function () {
    const remarks = $(this).data('remarks');
    let html = '';

    if (remarks && remarks.length > 0) {
        remarks.forEach(r => {
            if (r.remark && r.remark.trim() !== '') {
                html += `
                    <tr>
                        <td>${r.date}</td>
                        <td>${r.remark}</td>
                    </tr>
                `;
            }
        });
    }

    if (html === '') {
        html = `
            <tr>
                <td colspan="2" class="text-center text-muted">
                    No remarks available
                </td>
            </tr>
        `;
    }

    $('#remarksTableBody').html(html);
    $('#remarksModal').modal('show');
});

});


