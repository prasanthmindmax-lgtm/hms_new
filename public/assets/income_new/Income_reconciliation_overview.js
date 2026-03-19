$(document).ready(function() {
	$(".cincome_view").hide();
	overall_fetch(1);

    // Toggle Statistics Section
    $('#toggleStatsBtn').on('click', function() {
        const statsSection = $('#statsSection');
        const toggleText = $(this).find('.toggle-text');
        const icon = $(this).find('i');
        
        if (statsSection.is(':visible')) {
            statsSection.slideUp(300);
            toggleText.text('Show Statistics');
            icon.removeClass('fa-chart-bar').addClass('fa-chart-bar');
        } else {
            statsSection.slideDown(300);
            toggleText.text('Hide Statistics');
            icon.removeClass('fa-chart-bar').addClass('fa-chart-area');
        }
    });

    // Clear All Filters Button
    $('#clearFiltersBtn').on('click', function() {
        $('.clear_views').trigger('click');
    });

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

function handleSuccess(responseData, urlstatus) {
    console.log("handleSuccess",responseData);

    if (urlstatus == 1) {
        $("#daily_details_recon").show();

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

var rowdataSource = [];
var groupedTotals = [];

function rowhandleSuccess(responseData, urlstatus) {

    if (urlstatus == 1) {

        let rows = Array.isArray(responseData?.data)
            ? responseData.data
            : Array.isArray(responseData)
                ? responseData
                : [];
        console.log("rows",rows);

        groupedTotals = groupByBillDateAndArea(rows);

        console.log(groupedTotals);
    }

    return groupedTotals;
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

    // morefilterview(fitterremovedata, moredatefittervale, 1, incomeOverviewDatafitter);
    clearTimeout(filterTriggerTimer);

    filterTriggerTimer = setTimeout(function () {
        morefilterview(
            fitterremovedata,
            moredatefittervale,
            1,
            incomeOverviewDatafitter
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
// function ticketrenderPagination(data, ticketpageSize) {
// 	 if (!Array.isArray(data)) {
//         if (data && typeof data === 'object') {
//             data = [data];
//         } else {
//             data = [];
//         }
//     }
//     var totalPages = Math.ceil(data.length / ticketpageSize);
//     var paginationHtml = '';

//     for (var i = 1; i <= totalPages; i++) {
//         paginationHtml += '<button class="page-bttn " style="background-color:#6a6ee4;color: #fff;"  data-page="' + i + '">' + i + '</button>';
//     }

//     $('#ticketpagination').html(paginationHtml);

//     // Bind click event to each pagination button
//     $('.page-bttn').click(function() {
//         var pageNum = $(this).data('page');
//         $('.page-bttn').removeClass('active');
//         $(this).addClass('active');
//         ticketrenderTable(data, ticketpageSize, pageNum);
//     });
// }
function ticketrenderPagination(data, ticketpageSize, currentPage = 1) {

    if (!Array.isArray(data)) {
        data = data ? [data] : [];
    }

    const totalPages = Math.ceil(data.length / ticketpageSize);
    let paginationHtml = '';

    function addButton(page) {
        paginationHtml += `
            <button class="page-bttn ${page === currentPage ? 'active' : ''}"
                    data-page="${page}">
                ${page}
            </button>`;
    }

    function addDots() {
        paginationHtml += `<span class="pagination-dots">...</span>`;
    }

    // --- Always show first 3 pages
    for (let i = 1; i <= Math.min(3, totalPages); i++) {
        addButton(i);
    }

    // --- Dots before middle
    if (currentPage > 5) {
        addDots();
    }

    // --- Middle pages (current ±1)
    for (let i = currentPage - 1; i <= currentPage + 1; i++) {
        if (i > 3 && i < totalPages - 2) {
            addButton(i);
        }
    }

    // --- Dots after middle
    if (currentPage < totalPages - 4) {
        addDots();
    }

    // --- Always show last 3 pages
    for (let i = Math.max(totalPages - 2, 4); i <= totalPages; i++) {
        addButton(i);
    }

    $('#ticketpagination').html(paginationHtml);

    // Bind click
    $('.page-bttn').off('click').on('click', function () {
        const pageNum = parseInt($(this).data('page'));
        ticketrenderTable(data, ticketpageSize, pageNum);
        ticketrenderPagination(data, ticketpageSize, pageNum);
    });
}

function ticketrenderTable(data, ticketpageSize, pageNum) {
    window.fullMocDocData = data;

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

    // Find Consolidated and Actual rows
    let consolidatedRow = data.find(row => row.type === "Consolidated");
    let Actual = data.find(row => row.type === "Actual");

    console.log("consolidatedRow", consolidatedRow);
    console.log("Actual", Actual);

    // Update MocDoc values
    if (consolidatedRow) {
        $('#total_cash').text(formatINR(parseFloat(consolidatedRow.cash || 0).toFixed(2)));
        $('#total_card').text(formatINR(parseFloat(consolidatedRow.card || 0).toFixed(2)));
        $('#total_neft').text(formatINR(parseFloat(consolidatedRow.neft || 0).toFixed(2)));
        $('#total_upi').text(formatINR(parseFloat(consolidatedRow.upi || 0).toFixed(2)));
        $('#total_other').text(formatINR(parseFloat(consolidatedRow.others || 0).toFixed(2)));
        $('#total_upi_card').text(formatINR(parseFloat((consolidatedRow.upi || 0) + (consolidatedRow.card || 0)).toFixed(2)));
        $('#total_amount').text(formatINR(parseFloat(consolidatedRow.total || 0).toFixed(2)));
    } else {
        $('#total_cash, #total_card, #total_neft, #total_upi, #total_other, #total_amount').text('0.00');
    }

    // Update Actual values and calculate Differences
    if (Actual && consolidatedRow) {
        // Set Actual values
        $('#actuall_cash').text(formatINR(parseFloat(Actual.cash || 0).toFixed(2)));
        $('#actuall_card').text(formatINR(parseFloat(Actual.card || 0).toFixed(2)));
        $('#actuall_neft').text(formatINR(parseFloat(Actual.neft || 0).toFixed(2)));
        $('#actuall_upi').text(formatINR(parseFloat(Actual.upi || 0).toFixed(2)));
        $('#actuall_other').text(formatINR(parseFloat(Actual.others || 0).toFixed(2)));
        $('#actuall_bank_chagers').text(formatINR(parseFloat(Actual.bank_chargers || 0).toFixed(2)));
        $('#actuall_total_upi_card').text(formatINR(parseFloat(Actual.bank_upi_card || 0).toFixed(2)));
        $('#actuall_amount').text(formatINR(parseFloat(Actual.total || 0).toFixed(2)));

        // Calculate Differences (Actual - MocDoc)
        const cashDiff = parseFloat(Actual.cash || 0) - parseFloat(consolidatedRow.cash || 0);
        const cardDiff = parseFloat(Actual.card || 0) - parseFloat(consolidatedRow.card || 0);
        const neftDiff = parseFloat(Actual.neft || 0) - parseFloat(consolidatedRow.neft || 0);
        const upiDiff = parseFloat(Actual.upi || 0) - parseFloat(consolidatedRow.upi || 0);
        const otherDiff = parseFloat(Actual.others || 0) - parseFloat(consolidatedRow.others || 0);
        // const upicardDiff = parseFloat((parseFloat((consolidatedRow.upi || 0) + (consolidatedRow.card || 0)))  - (parseFloat((Actual.bank_chargers || 0) + (Actual.bank_upi_card || 0))));
        const upicardDiff = parseFloat((parseFloat((Actual.bank_chargers || 0) + (Actual.bank_upi_card || 0)))  - (parseFloat((consolidatedRow.upi || 0) + (consolidatedRow.card || 0))));
        const totalDiff = parseFloat(Actual.total || 0) - parseFloat(consolidatedRow.total || 0);
        // const totalDiff = parseFloat((Actual.cash || 0) + (Actual.neft || 0) + (Actual.others || 0) + (Actual.bank_upi_card || 0)) - parseFloat(consolidatedRow.total || 0);
        // const totalDiff = Math.abs(cashDiff) + Math.abs(neftDiff) + Math.abs(otherDiff) + Math.abs(upicardDiff);
        console.log('formatINR(cashDiff)',formatINR(cashDiff));
        console.log('formatINR(totalDiff)',formatINR(totalDiff));
        
        // Set Difference values with color coding
        // setDifferenceValue('#actuall_cash_diff', formatINR(cashDiff));
        // setDifferenceValue('#actuall_card_diff',formatINR(cardDiff));
        // setDifferenceValue('#actuall_neft_diff', formatINR(neftDiff));
        // setDifferenceValue('#actuall_upi_diff', formatINR(upiDiff));
        // setDifferenceValue('#actuall_other_diff', formatINR(otherDiff));
        // setDifferenceValue('#actuall_total_upicard_diff', formatINR(upicardDiff));
        // setDifferenceValue('#actuall_amount_diff', formatINR(totalDiff));

        setDifferenceValue('#actuall_cash_diff', cashDiff);
        setDifferenceValue('#actuall_card_diff', cardDiff);
        setDifferenceValue('#actuall_neft_diff', neftDiff);
        setDifferenceValue('#actuall_upi_diff', upiDiff);
        setDifferenceValue('#actuall_other_diff', otherDiff);
        setDifferenceValue('#actuall_total_upicard_diff', upicardDiff);
        setDifferenceValue('#actuall_amount_diff', totalDiff);


        // Calculate Excess (only positive differences)
        const excessCash = cashDiff > 0 ? cashDiff : 0;
        const excessCard = cardDiff > 0 ? cardDiff : 0;
        const excessNeft = neftDiff > 0 ? neftDiff : 0;
        const excessUpi = upiDiff > 0 ? upiDiff : 0;
        const excessOther = otherDiff > 0 ? otherDiff : 0;
        const excessTotal = excessCash + excessCard + excessNeft + excessUpi + excessOther;

        // Set Excess values
        $('#excess_cash').text(excessCash.toFixed(2));
        $('#excess_card').text(excessCard.toFixed(2));
        $('#excess_neft').text(excessNeft.toFixed(2));
        $('#excess_upi').text(excessUpi.toFixed(2));
        $('#excess_other').text(excessOther.toFixed(2));
        $('#excess_amount').text(excessTotal.toFixed(2));

        // Apply color styling to cards
        // updateDifferenceColors();
    } else {
        // Reset all values if no data
        $('#actuall_cash, #actuall_card, #actuall_neft, #actuall_upi, #actuall_other, #actuall_amount').text('0.00');
        $('#actuall_cash_diff, #actuall_card_diff, #actuall_neft_diff, #actuall_upi_diff, #actuall_other_diff, #actuall_amount_diff').text('0.00');
        $('#excess_cash, #excess_card, #excess_neft, #excess_upi, #excess_other, #excess_amount').text('0.00');
    }

    var body = "";
    body = ticketData(pageData, body);
    $("#daily_details_recon").html(body);
    $("#today_visits").text(totalItems);
    $("#dcounts").text(totalItems);

    // Reload radiant values for every row
    $("#daily_details_recon tr").each(function () {
        loadRadiantValues($(this));
    });
}
const formatINR = (val) => {
    val = Number(val) || 0;
    return val.toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};
// Map stat type to attachment field name in row (for Actual types only)
const TYPE_TO_FILE_FIELD = {
    deposite_amount: "deposit_amount_files",
    mespos_card: "mespos_card_files",
    mespos_upi: "mespos_upi_files",
    bank_neft: "bank_neft_files",
    bank_others: "bank_others_files",
    bank_chargers: "bank_chargers_files",
    bank_upi_card: "bank_upi_card_files"
};

// Map stat type to UTR attachment field (for UTR column click-to-preview)
const TYPE_TO_UTR_FILE_FIELD = {
    deposite_amount: "cash_utr_files",
    mespos_card: "card_upi_utr_files",
    mespos_upi: "card_upi_utr_files",
    bank_neft: "neft_utr_files",
    bank_others: "other_utr_files",
    bank_chargers: "card_upi_utr_files",
    bank_upi_card: "card_upi_utr_files"
};

function getFilesForRow(row, type) {
    const field = TYPE_TO_FILE_FIELD[type];
    if (!field || !row[field]) return [];
    try {
        const parsed = typeof row[field] === "string" ? JSON.parse(row[field]) : row[field];
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        return [];
    }
}

function getUtrFilesForRow(row, type) {
    const field = TYPE_TO_UTR_FILE_FIELD[type];
    if (!field || !row[field]) return [];
    try {
        const parsed = typeof row[field] === "string" ? JSON.parse(row[field]) : row[field];
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        return [];
    }
}

$(document).on("click", ".stat-click", function () {

    const type = $(this).data("type");

    const filtered = window.fullMocDocData.filter(row =>
        row.type !== "Consolidated" &&
        row.type !== "Actual" &&
        parseFloat(row[type] || 0) > 0
    );

    window._mocdocFilteredData = filtered;
    window._mocdocType = type;

    buildMocdocTable(filtered, type, true);

    $("#mocdocModal").modal("show");
});

function buildMocdocTable(filtered, type, showBranchTotals) {

    let thead = "";
    let tbody = "";
    let grandTotal = 0;
    let Title = "";
    let columns = [];
    const isActual = Object.keys(TYPE_TO_FILE_FIELD).includes(type);

    // =========================
    // MocDoc types
    // =========================
    if ([
        "moc_cash_amt","moc_card_amt","moc_upi_amt","moc_neft_amt","moc_other_amt","moc_overall_total"].includes(type)) {

        Title = `MocDoc ${type.replace(/_/g, " ").toUpperCase()} – Date Wise`;

        columns = [
            { key: "date_range", label: "Date", align: "center" },
            { key: "zone_location", label: "Zone/Location", align: "center" },
            { key: type, label: "Amount", align: "end" }
        ];
    }

    // =========================
    // Actual / Bank types
    // =========================
    const obj = {
        deposite_amount: "cash_utr_number",
        mespos_card: "bank_upi_card_utr",
        mespos_upi: "bank_upi_card_utr",
        bank_neft: "bank_neft_utr",
        bank_others: "bank_other_utr",
        bank_chargers: "bank_upi_card_utr",
        bank_upi_card: "bank_upi_card_utr"
    };
    const utrColumnKey = obj[type] || null;

    if (Object.keys(obj).includes(type)) {

        Title = `Actual ${type.replace(/_/g, " ").toUpperCase()} – Date Wise`;

        columns = [
            { key: "date_range", label: "Date", align: "center" },
            { key: "zone_location", label: "Zone/Location", align: "center" },
            { key: obj[type], label: "UTR/Transaction ID", align: "center" },
            { key: type, label: "Amount", align: "end" }
        ];
    }

    // =========================
    // Branch-wise totals (top of modal)
    // =========================
    let branchTotalsHtml = "";
    if (!showBranchTotals || filtered.length === 0) {
        $("#mocdocModalBranchTotals").empty();
    }
    if (showBranchTotals && filtered.length > 0) {
        const byBranch = {};
        filtered.forEach(row => {
            const zoneLoc = [row.zone_name, row.location_name].filter(Boolean).join(' / ') || "Unknown";
            if (!byBranch[zoneLoc]) byBranch[zoneLoc] = 0;
            byBranch[zoneLoc] += parseFloat(row[type] || 0);
        });
        const branchEntries = Object.entries(byBranch).sort((a, b) => b[1] - a[1]);
        branchTotalsHtml = `
            <div class="border rounded p-2 mb-2 bg-light">
                <strong class="d-block mb-2">Branch-wise total</strong>
                <div class="d-flex flex-wrap gap-2">
                    ${branchEntries.map(([name, amt]) => `
                        <span class="mocdoc-branch-total badge bg-primary" data-branch-name="${name.replace(/"/g, "&quot;")}" style="cursor:pointer;font-size:11px;">
                            ${name}: ${formatINR(amt)}
                        </span>
                    `).join("")}
                </div>
            </div>
        `;
    }
    $("#mocdocModalBranchTotals").html(branchTotalsHtml);

    // =========================
    // Build THEAD
    // =========================
    thead = `
        <tr>
            ${columns.map(col =>
                `<th class="text-${col.align}">${col.label}</th>`
            ).join("")}
        </tr>
    `;

    // =========================
    // Build TBODY (clickable branch, amount with attachment)
    // =========================
    if (filtered.length === 0) {

        tbody = `
            <tr>
                <td colspan="${columns.length}" class="text-center text-muted">
                    No data found
                </td>
            </tr>
        `;

    } else {

        window._mocdocAmountFiles = [];
        window._mocdocUtrFiles = [];
        filtered.forEach((row, rowIndex) => {
            const amount = parseFloat(row[type] || 0);
            grandTotal += amount;

            const files = getFilesForRow(row, type);
            const hasAttachment = isActual && files.length > 0;
            if (hasAttachment) window._mocdocAmountFiles[rowIndex] = files;

            const utrFiles = isActual && utrColumnKey ? getUtrFilesForRow(row, type) : [];
            if (utrFiles.length > 0) window._mocdocUtrFiles[rowIndex] = utrFiles;

            const amountClass = hasAttachment ? "text-end amount-with-attachment" : "text-end";
            const amountData = hasAttachment ? ` data-row-index="${rowIndex}"` : "";
            const utrClass = utrFiles.length > 0 ? "text-center mocdoc-utr-cell utr-with-attachment" : "text-center mocdoc-utr-cell";
            const utrData = utrFiles.length > 0 ? ` data-utr-row-index="${rowIndex}"` : "";

            tbody += `
                <tr class="mocdoc-modal-row">
                    ${columns.map(col => {
                        if (col.key === type) {
                            return `<td class="${amountClass}"${amountData}>${formatINR(amount.toFixed(2))}</td>`;
                        }
                        if (col.key === "zone_location") {
                            const zoneLoc = [row.zone_name, row.location_name].filter(Boolean).join(' / ') || '-';
                            return `<td class="text-${col.align} mocdoc-branch-cell" data-branch-name="${String(zoneLoc).replace(/"/g, "&quot;")}" style="cursor:pointer;text-decoration:underline;color:#0d6efd;">${zoneLoc}</td>`;
                        }
                        if (col.key === utrColumnKey) {
                            const utrVal = row[col.key] ?? "-";
                            return `<td class="${utrClass}"${utrData}>${utrVal}</td>`;
                        }
                        if (col.key === "zone_location") {
                            const zoneLoc = [row.zone_name, row.location_name].filter(Boolean).join(' / ') || '-';
                            return `<td class="text-${col.align}">${zoneLoc}</td>`;
                        }
                        return `<td class="text-${col.align}">${row[col.key] ?? "-"}</td>`;
                    }).join("")}
                </tr>
            `;
        });

        tbody += `
            <tr class="table-primary fw-bold">
                <td colspan="${columns.length - 1}" class="text-end">
                    Grand Total
                </td>
                <td class="text-end">${formatINR(grandTotal.toFixed(2))}</td>
            </tr>
        `;
    }

    // =========================
    // Inject into DOM
    // =========================
    $("#mocdocModalTitle").text(Title);
    $("#mocdocModalHead").html(thead);
    $("#mocdocModalBody").html(tbody);
}

function buildBranchDetailModal(branchFiltered, type, branchName) {

    let thead = "";
    let tbody = "";
    let grandTotal = 0;
    let columns = [];
    const isActual = Object.keys(TYPE_TO_FILE_FIELD).includes(type);

    if (["moc_cash_amt","moc_card_amt","moc_upi_amt","moc_neft_amt","moc_other_amt","moc_overall_total"].includes(type)) {
        columns = [
            { key: "date_range", label: "Date", align: "center" },
            { key: "zone_location", label: "Zone/Location", align: "center" },
            { key: type, label: "Amount", align: "end" }
        ];
    } else {
        const obj = {
            deposite_amount: "cash_utr_number",
            mespos_card: "bank_upi_card_utr",
            mespos_upi: "bank_upi_card_utr",
            bank_neft: "bank_neft_utr",
            bank_others: "bank_other_utr",
            bank_chargers: "bank_upi_card_utr",
            bank_upi_card: "bank_upi_card_utr"
        };
        columns = [
            { key: "date_range", label: "Date", align: "center" },
            { key: "zone_location", label: "Zone/Location", align: "center" },
            { key: obj[type], label: "UTR/Transaction ID", align: "center" },
            { key: type, label: "Amount", align: "end" }
        ];
    }
    const utrColumnKeyBranch = isActual ? (columns.find(c => c.label === "UTR" || c.label === "UTR/Transaction ID") || {}).key : null;

    thead = `<tr>${columns.map(col => `<th class="text-${col.align}">${col.label}</th>`).join("")}</tr>`;

    if (branchFiltered.length === 0) {
        tbody = `<tr><td colspan="${columns.length}" class="text-center text-muted">No data for this branch</td></tr>`;
    } else {
        window._mocdocAmountFiles = [];
        window._mocdocUtrFiles = [];
        branchFiltered.forEach((row, rowIndex) => {
            const amount = parseFloat(row[type] || 0);
            grandTotal += amount;
            const files = getFilesForRow(row, type);
            const hasAttachment = isActual && files.length > 0;
            if (hasAttachment) window._mocdocAmountFiles[rowIndex] = files;

            const utrFiles = isActual && utrColumnKeyBranch ? getUtrFilesForRow(row, type) : [];
            if (utrFiles.length > 0) window._mocdocUtrFiles[rowIndex] = utrFiles;

            const amountClass = hasAttachment ? "text-end amount-with-attachment" : "text-end";
            const amountData = hasAttachment ? ` data-row-index="${rowIndex}"` : "";
            const utrClass = utrFiles.length > 0 ? "text-center mocdoc-utr-cell utr-with-attachment" : "text-center mocdoc-utr-cell";
            const utrData = utrFiles.length > 0 ? ` data-utr-row-index="${rowIndex}"` : "";

            tbody += `<tr class="mocdoc-modal-row">`;
            columns.forEach(col => {
                if (col.key === type) {
                    tbody += `<td class="${amountClass}"${amountData}>${formatINR(amount.toFixed(2))}</td>`;
                } else if (col.key === "zone_location") {
                    const zoneLoc = [row.zone_name, row.location_name].filter(Boolean).join(' / ') || '-';
                    tbody += `<td class="text-${col.align}">${zoneLoc}</td>`;
                } else if (col.key === utrColumnKeyBranch) {
                    const utrVal = row[col.key] ?? "-";
                    tbody += `<td class="${utrClass}"${utrData}>${utrVal}</td>`;
                } else {
                    tbody += `<td class="text-${col.align}">${row[col.key] ?? "-"}</td>`;
                }
            });
            tbody += `</tr>`;
        });
        tbody += `<tr class="table-primary fw-bold"><td colspan="${columns.length - 1}" class="text-end">Total</td><td class="text-end">${formatINR(grandTotal.toFixed(2))}</td></tr>`;
    }

    $("#mocdocBranchModalTitle").text("Branch: " + branchName);
    $("#mocdocBranchModalHead").html(thead);
    $("#mocdocBranchModalBody").html(tbody);
    $("#mocdocBranchModal").modal("show");
}

// Branch click: open branch detail modal
$(document).on("click", "#mocdocModal .mocdoc-branch-cell, #mocdocModal .mocdoc-branch-total", function () {
    const branchName = $(this).data("branch-name");
    if (!branchName) return;
    const filtered = window._mocdocFilteredData || [];
    const type = window._mocdocType;
    const zoneLoc = (row) => [row.zone_name, row.location_name].filter(Boolean).join(' / ') || '';
    const branchFiltered = filtered.filter(row => zoneLoc(row) === branchName);
    buildBranchDetailModal(branchFiltered, type, branchName);
});

// Amount click: if attachment exists, show file preview (files stored in window._mocdocAmountFiles by row index)
$(document).on("click", "#mocdocModal .amount-with-attachment, #mocdocBranchModal .amount-with-attachment", function (e) {
    e.preventDefault();
    const rowIndex = $(this).data("row-index");
    if (rowIndex == null || rowIndex === undefined) return;
    const files = (window._mocdocAmountFiles || [])[rowIndex];
    if (files && files.length) openFilePreview(files);
});

// UTR click: if UTR has attached files, show file preview (files stored in window._mocdocUtrFiles by row index)
$(document).on("click", "#mocdocModal .utr-with-attachment, #mocdocBranchModal .utr-with-attachment", function (e) {
    e.preventDefault();
    const rowIndex = $(this).data("utr-row-index");
    if (rowIndex == null || rowIndex === undefined) return;
    const files = (window._mocdocUtrFiles || [])[rowIndex];
    if (files && files.length) openFilePreview(files);
});

// Main table: Bank UTR cell (UPI/CARD, NEFT, OTHERS) – click opens file preview (files stored on td via data-utr-files)
$(document).on("click", ".bank_utr_transcation_ids", function (e) {
    if ($(e.target).closest(".utr-icon").length) return;
    e.preventDefault();
    var files = $(this).data("utr-files");
    if (!files) files = [];
    if (!Array.isArray(files)) files = [];
    openFilePreview(files);
});

// function setDifferenceValue(selector, value) {
//     const element = $(selector);
//     const card = element.closest('.stat-card');
//     // Display absolute value
//     element.text(Math.abs(value).toFixed(2));
//     // Reset
//     element.removeClass('text-positive text-negative');
//     card.removeClass('bg-positive bg-negative');

//     if (value > 0) {
//         element.addClass('text-positive');
//         card.addClass('bg-positive');
//     } else if (value < 0) {
//         element.addClass('text-negative');
//         card.addClass('bg-negative');
//     }
// }
function setDifferenceValue(selector, value) {
    const element = $(selector);
    const card = element.closest('.stat-card');

    const num = Number(value) || 0;

    // Display formatted INR
    element.text(formatINR(Math.abs(num)));

    // Reset styles
    element.removeClass('text-positive text-negative');
    card.removeClass('bg-positive bg-negative');

    if (num > 0) {
        element.addClass('text-positive');
        card.addClass('bg-positive');
    } else if (num < 0) {
        element.addClass('text-negative');
        card.addClass('bg-negative');
    }
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

function ticketData(pageData, body) {
    var sno = 1;

    // Initialize totals
    var totals = {
        moc_cash_amt: 0,
        moc_card_amt: 0,
        moc_upi_amt: 0,
        moc_total_upi_card: 0,
        moc_neft_amt: 0,
        moc_other_amt: 0,
        moc_overall_total: 0,
        collection_amount: 0,
        deposite_amount: 0,
        mespos_card: 0,
        mespos_upi: 0,
        bank_chargers: 0,
        bank_upi_card: 0,
        bank_neft: 0,
        bank_others: 0,
        radiant_diff: 0,
        cash_diff: 0,
        card_upi_diff: 0,
        neft_others_diff: 0
    };

    if (pageData.length === 0) {
        body += '<tr><td colspan="28" style="text-align:center">No Data</td></tr>';
        return body;
    }

    pageData.forEach(usr => {
        if (usr.type === "Consolidated") return;
        if (usr.type === "Actual") return;

        // Parse numeric values
        const moc_cash_amt = parseFloat(usr.moc_cash_amt) || 0;
        const moc_card_amt = parseFloat(usr.moc_card_amt) || 0;
        const moc_upi_amt = parseFloat(usr.moc_upi_amt) || 0;
        const moc_total_upi_card = parseFloat(usr.moc_total_upi_card) || 0;
        const moc_neft_amt = parseFloat(usr.moc_neft_amt) || 0;
        const moc_other_amt = parseFloat(usr.moc_other_amt) || 0;
        const moc_overall_total = parseFloat(usr.moc_overall_total) || 0;
        const collection_amount = parseFloat(usr.collection_amount) || 0;
        const deposite_amount = parseFloat(usr.deposite_amount) || 0;
        const mespos_card = parseFloat(usr.mespos_card) || 0;
        const mespos_upi = parseFloat(usr.mespos_upi) || 0;

        // Calculate differences
        const bank_chargers = parseFloat(usr.bank_chargers) || 0;
        const bank_upi_card = parseFloat(usr.bank_upi_card) || 0;
        const bank_neft = parseFloat(usr.bank_neft) || 0;
        const bank_others = parseFloat(usr.bank_others) || 0;
        const radiant_diff = parseFloat(usr.radiant_diff) || 0;
        const cash_diff = parseFloat(usr.cash_diff) || 0;
        const card_upi_diff = parseFloat(usr.card_upi_diff) || 0;
        const neft_others_diff = parseFloat(usr.neft_others_diff) || 0;

        // Add to totals
        totals.moc_cash_amt += moc_cash_amt;
        totals.moc_card_amt += moc_card_amt;
        totals.moc_upi_amt += moc_upi_amt;
        totals.moc_total_upi_card += moc_total_upi_card;
        totals.moc_neft_amt += moc_neft_amt;
        totals.moc_other_amt += moc_other_amt;
        totals.moc_overall_total += moc_overall_total;
        totals.collection_amount += collection_amount;
        totals.deposite_amount += deposite_amount;
        totals.mespos_card += mespos_card;
        totals.mespos_upi += mespos_upi;
        totals.bank_chargers += bank_chargers;
        totals.bank_upi_card += bank_upi_card;
        totals.bank_neft += bank_neft;
        totals.bank_others += bank_others;
        totals.radiant_diff += radiant_diff;
        totals.cash_diff += cash_diff;
        totals.card_upi_diff += card_upi_diff;
        totals.neft_others_diff += neft_others_diff;

        const zoneLocationText = [usr.zone_name, usr.location_name].filter(Boolean).join(' / ') || '-';
        body += `
        <tr>
            <td class="tdview">#${sno}</td>
            <td class="tdview date-cell" data-date="${usr.date_range}">${usr.date_range}</td>
            <td class="tdview zone_name location-cell zone_location" data-zone_name="${usr.zone_name || ''}" data-location="${usr.location_name || ''}">${zoneLocationText}</td>
            <td class="tdview cash_mocdoc">${usr.moc_cash_amt}</td>
            <td class="tdview card_mocdoc">${usr.moc_card_amt}</td>
            <td class="tdview upi_mocdoc">${usr.moc_upi_amt}</td>
            <td class="tdview total_upi_card">${usr.moc_total_upi_card}</td>
            <td class="tdview neft_mocdoc">${usr.moc_neft_amt}</td>
            <td class="tdview other_mocdoc">${usr.moc_other_amt}</td>
            <td class="tdview total_moc">${usr.moc_overall_total}</td>
            <td class="tdview date_collection" data-field="upi_date">
                <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
                <div>
                    <span class="selected-date">${usr.date_collection}</span>
                </div>
            </td>
            <td class="tdview collection_amount editable" id="cash_radiant">
                <span class="edit-text">${usr.collection_amount}</span>
            </td>
            <td class="tdview date_deposited" data-field="upi_date">
                <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
                <div>
                    <span class="selected-date">${usr.date_deposited}</span>
                </div>
            </td>
            <td class="tdview deposite_amount editable">
                <span class="edit-text">${usr.deposite_amount}</span>
                <i class="fa fa-paperclip upload-icon" title="Upload Documents" style="cursor:pointer; margin-left:8px; color:#007bff;display:none;"></i>
            </td>
            <td class="tdview utr_transcation_ids editable">
                <span class="edit-text_new">
                    ${usr.cash_utr_number && Number(usr.cash_utr_number) !== 0 ? usr.cash_utr_number : '-'}
                </span>

            </td>
            <td class="tdview mespos_card editable" id="cash_radiant">
                <span class="edit-text">${usr.mespos_card}</span>
            </td>
            <td class="tdview mespos_upi editable" id="cash_radiant">
                <span class="edit-text">${usr.mespos_upi}</span>
            </td>
            <td class="tdview date_settlement" data-field="upi_date">
                <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
                <div>
                    <span class="selected-date">${usr.date_settlement}</span>
                </div>
            </td>
            <td class="tdview bank_chargers editable" id="cash_radiant">
                <span class="edit-text">${usr.bank_chargers}</span>
            </td>
            <td class="tdview bank_upi_card editable" id="cash_radiant">
                <span class="edit-text">${usr.bank_upi_card}</span>
            </td>
            <td class="tdview bank_neft editable" id="cash_radiant">
                <span class="edit-text">${usr.bank_neft}</span>
            </td>
            <td class="tdview bank_others editable" id="cash_radiant">
                <span class="edit-text">${usr.bank_others}</span>
            </td>
            <td class="text-left bank_utr_transcation_ids">
                <span class="utr-text">${usr.bank_upi_card_utr}</span><br>

                <i class="fa fa-receipt utr-icon text-primary"
                style="cursor:pointer;font-size:16px; margin-top:4px; display:none;"
                data-upi=""
                data-neft=""
                data-others=""
                data-bs-toggle="tooltip"
                data-bs-html="true"
                title="No UTR added">
                </i>
            </td>
            <td class="tdview radiant_diff"><span>0</span></td>
            <td class="tdview cash_diff"><span>0</span></td>
            <td class="tdview card_upi_diff"><span>0</span></td>
            <td class="tdview neft_others_diff"><span>0</span></td>
            <td class="tdview cash_radiant editable" id="cash_radiant">
                <input type="hidden" class="hidden-range">
                <i class="fa fa-calendar calendar-icon" data-field="cash_moc_amt" style="display:none"></i>
                <span class="calander-text" style="display:none"></span>
            </td>
        </tr>
        `;
        sno++;
    });

    // Add totals row with color-coded differences
    body += `
    <tr class="total-row" style="background-color: #f8f9fa; font-weight: bold;">
        <td class="tdview" colspan="3">Total</td>
        <td class="tdview">${totals.moc_cash_amt.toFixed(2)}</td>
        <td class="tdview">${totals.moc_card_amt.toFixed(2)}</td>
        <td class="tdview">${totals.moc_upi_amt.toFixed(2)}</td>
        <td class="tdview">${totals.moc_total_upi_card.toFixed(2)}</td>
        <td class="tdview">${totals.moc_neft_amt.toFixed(2)}</td>
        <td class="tdview">${totals.moc_other_amt.toFixed(2)}</td>
        <td class="tdview">${totals.moc_overall_total.toFixed(2)}</td>
        <td class="tdview">-</td>
        <td class="tdview">${totals.collection_amount.toFixed(2)}</td>
        <td class="tdview">-</td>
        <td class="tdview">${totals.deposite_amount.toFixed(2)}</td>
        <td class="tdview">-</td>
        <td class="tdview">${totals.mespos_card.toFixed(2)}</td>
        <td class="tdview">${totals.mespos_upi.toFixed(2)}</td>
        <td class="tdview">-</td>
        <td class="tdview">${totals.bank_chargers.toFixed(2)}</td>
        <td class="tdview">${totals.bank_upi_card.toFixed(2)}</td>
        <td class="tdview">${totals.bank_neft.toFixed(2)}</td>
        <td class="tdview">${totals.bank_others.toFixed(2)}</td>
        <td class="tdview">-</td>
        <td class="tdview">${totals.radiant_diff.toFixed(2)}</td>
        <td class="tdview">${totals.cash_diff.toFixed(2)}</td>
        <td class="tdview">${totals.card_upi_diff.toFixed(2)}</td>
        <td class="tdview">${totals.neft_others_diff.toFixed(2)}</td>
    </tr>
    `;

    return body;
}


// function ticketData(pageData, body) {
//     var sno = 1;
//     console.log("ticketData pageData",pageData);

//     if (pageData.length === 0) {
//         body += '<tr><td colspan="22" style="text-align:center">No Data</td></tr>';
//         return body;
//     }

//     pageData.forEach(usr => {
//         if (usr.type === "Consolidated") return;
//         if (usr.type === "Actual") return;
//         let billDate = usr.date_range ? moment(usr.date_range).format("DD MMM YYYY") : "-";
//         let billformateDate = usr.date_range ? moment(usr.date_range).format("DD/MM/YYYY") : "-";

//         body += `
//         <tr>
//             <td class="tdview">#${sno}</td>
//             <td class="tdview date-cell" data-date="${usr.date_range}">${usr.date_range}</td>
//             <td class="tdview zone_name" data-zone_name="${usr.zone_name}">${usr.zone_name}</td>
//             <td class="tdview location-cell" data-location="${usr.location_name}">${usr.location_name}</td>
//             <td class="tdview cash_mocdoc">${usr.moc_cash_amt}</td>
//             <td class="tdview  card_mocdoc">${usr.moc_card_amt}</td>
//             <td class="tdview upi_mocdoc">${usr.moc_upi_amt}</td>
//             <td class="tdview total_upi_card">${usr.moc_total_upi_card}</td>
//             <td class="tdview neft_mocdoc">${usr.moc_neft_amt}</td>
//             <td class="tdview other_mocdoc">${usr.moc_other_amt}</td>
//             <td class="tdview total_moc">${usr.moc_overall_total}</td>
//             <td class="tdview date_collection" data-field="upi_date">
//                 <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
//                 <div>
//                     <span class="selected-date">${usr.date_collection}</span>
//                 </div>
//             </td>
//             <td class="tdview collection_amount editable" id="cash_radiant">
//                 <span class="edit-text">${usr.collection_amount}</span>
//             </td>
//             <td class="tdview date_deposited" data-field="upi_date">
//                 <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
//                 <div>
//                     <span class="selected-date">${usr.date_deposited}</span>
//                 </div>
//             </td>
//             <td class="tdview deposite_amount editable">
//                 <span class="edit-text">${usr.deposite_amount}</span>

//                 <!-- Upload Icon -->
//                 <i class="fa fa-paperclip upload-icon"
//                 title="Upload Documents"
//                 style="cursor:pointer; margin-left:8px; color:#007bff;display:none;"></i>
//             </td>

//             <td class="tdview mespos_card editable" id="cash_radiant">
//                 <span class="edit-text">${usr.mespos_card}</span>
//             </td>
//             <td class="tdview mespos_upi editable" id="cash_radiant">
//                 <span class="edit-text">${usr.mespos_upi}</span>
//             </td>
//             <td class="tdview date_settlement" data-field="upi_date">
//                 <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
//                 <div>
//                     <span class="selected-date">${usr.date_settlement}</span>
//                 </div>
//             </td>
//             <td class="tdview bank_chargers editable" id="cash_radiant">
//                 <span class="edit-text">${usr.bank_chargers}</span>
//             </td>
//             <td class="tdview bank_upi_card editable" id="cash_radiant">
//                 <span class="edit-text">${usr.bank_upi_card}</span>
//             </td>
//             <td class="tdview bank_neft editable" id="cash_radiant">
//                 <span class="edit-text">${usr.bank_neft}</span>
//             </td>
//             <td class="tdview bank_others editable" id="cash_radiant">
//                 <span class="edit-text">${usr.bank_others}</span>
//             </td>
//             <td class="tdview radiant_diff" ><span>0</span></td>
//             <td class="tdview cash_diff" ><span>0</span></td>
//             <td class="tdview card_upi_diff" ><span>0</span></td>
//             <td class="tdview neft_others_diff" ><span>0</span></td>
//             <td class="tdview cash_radiant editable" id="cash_radiant">
//                 <input type="hidden" class="hidden-range" >
//                 <i class="fa fa-calendar calendar-icon" data-field="cash_moc_amt" style="display:none"></i>
//                 <span class="calander-text" style="display:none"></span>

//             </td>
//         </tr>
//         `;
//         sno++;
//     });

//     return body;
// }


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
function setDiffValue(container, span, value) {

    let rawVal = parseFloat(value) || 0;
    let absVal = Math.abs(rawVal);   // <-- remove negative sign for display

    span.text(absVal % 1 === 0 ? absVal : absVal.toFixed(2));

    // reset classes
    container.removeClass("diff-zero diff-positive diff-negative");

    if (rawVal === 0) {
        container.addClass("diff-zero");
    }
    else if (rawVal > 0) {
        container.addClass("diff-negative");
    }
    else {
        // negative value → red background but positive number shown
        container.addClass("diff-positive");
    }
}
$(document).on("input keyup", ".editable span", function () {

    let tr = $(this).closest("tr");
    const toNum = v => parseFloat(v) || 0;

    /* ================= RADIANT DIFF =================
       cash_mocdoc - deposite_amount
    ================================================== */
   let cashMoc     = toNum(tr.find(".cash_mocdoc").text());
    let collectionAmt = toNum(tr.find(".collection_amount .edit-text").text());
    setDiffValue(
        tr.find(".radiant_diff"),
        tr.find(".radiant_diff span"),
        cashMoc - collectionAmt
    );

    /* ================= CASH DIFF =================
    collection_amount - deposite_amount
    ================================================ */
    let depositAmt  = toNum(tr.find(".deposite_amount   .edit-text").text());
    setDiffValue(
        tr.find(".cash_diff"),
        tr.find(".cash_diff span"),
        collectionAmt - depositAmt
    );

    /* ================= CARD + UPI DIFF =================
       total_upi_card - (bank_chargers + bank_upi_card)
    ===================================================== */
    let totalUpiCard = toNum(tr.find(".total_upi_card").text());
    let bankCharges  = toNum(tr.find(".bank_chargers .edit-text").text());
    let bankUpiCard  = toNum(tr.find(".bank_upi_card .edit-text").text());

    setDiffValue(
        tr.find(".card_upi_diff"),
        tr.find(".card_upi_diff span"),
        totalUpiCard - (bankCharges + bankUpiCard)
    );

    /* ================= NEFT + OTHERS DIFF =================
       (neft_mocdoc + other_mocdoc) - (bank_neft + bank_others)
    ======================================================== */
    let neftMoc   = toNum(tr.find(".neft_mocdoc").text());
    let otherMoc  = toNum(tr.find(".other_mocdoc").text());
    let bankNeft  = toNum(tr.find(".bank_neft .edit-text").text());
    let bankOther = toNum(tr.find(".bank_others .edit-text").text());

    setDiffValue(
        tr.find(".neft_others_diff"),
        tr.find(".neft_others_diff span"),
        (neftMoc + otherMoc) - (bankNeft + bankOther)
    );

});
// MODAL SAVE - upload file
$("#modalSaveCash").on("click", function () {
    let input = $('.modal-input[data-field="' + activeField + '"]')[0];
    if (!input.files.length) {
        alert("Please select a file");
        return;
    }

    let file = input.files[0];
    let tr = activeRow;

    let formData = new FormData();
    formData.append("file", file);
    formData.append("field", activeField);
    formData.append("location_name", tr.find(".location-cell").data("location"));
    formData.append("date_range", $('#dateallviews').text().split('-')[0].trim());
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    $.ajax({
        url: incomeuploadFile,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
    success: function (res) {
            if (res.status === 200) {

                let td = activeRow.find(`.${activeField}`);
                console.log("res.path",res.path);

                // hide + icon
                td.find(".plus-icon").hide();

                // show view link
                td.find(".file-view")
                .removeClass("d-none")
                .attr("href", window.location.origin + "/hms/public/radiant_files/" + filePath);


                cashModal.hide();
            } else {
                alert("Upload failed");
            }
        },
        error: function () {
            alert("Server error");
        }
    });
});

function calculateRow(tr) {

    const toNum = v => parseFloat(v) || 0;

    /* ================= RADIANT DIFF =================
       cash_mocdoc - deposite_amount
    ================================================== */
    let cashMoc     = toNum(tr.find(".cash_mocdoc").text());
    let collectionAmt = toNum(tr.find(".collection_amount .edit-text").text());
    setDiffValue(
        tr.find(".radiant_diff"),
        tr.find(".radiant_diff span"),
        cashMoc - collectionAmt
    );

    /* ================= CASH DIFF =================
    collection_amount - deposite_amount
    ================================================ */
    let depositAmt  = toNum(tr.find(".deposite_amount   .edit-text").text());
    setDiffValue(
        tr.find(".cash_diff"),
        tr.find(".cash_diff span"),
        collectionAmt - depositAmt
    );

    /* ================= CARD + UPI DIFF =================
       total_upi_card - (bank_chargers + bank_upi_card)
    ===================================================== */
    let totalUpiCard = toNum(tr.find(".total_upi_card").text());
    let bankCharges  = toNum(tr.find(".bank_chargers .edit-text").text());
    let bankUpiCard  = toNum(tr.find(".bank_upi_card .edit-text").text());

    setDiffValue(
        tr.find(".card_upi_diff"),
        tr.find(".card_upi_diff span"),
        totalUpiCard - (bankCharges + bankUpiCard)
    );

    /* ================= NEFT + OTHERS DIFF =================
       (neft_mocdoc + other_mocdoc) - (bank_neft + bank_others)
    ======================================================== */
    let neftMoc   = toNum(tr.find(".neft_mocdoc").text());
    let otherMoc  = toNum(tr.find(".other_mocdoc").text());
    let bankNeft  = toNum(tr.find(".bank_neft .edit-text").text());
    let bankOther = toNum(tr.find(".bank_others .edit-text").text());

    setDiffValue(
        tr.find(".neft_others_diff"),
        tr.find(".neft_others_diff span"),
        (neftMoc + otherMoc) - (bankNeft + bankOther)
    );
}



$(document).on("keypress", ".editable span", function (e) {
    // Allow: backspace(8), delete(46), arrows(37-40)
    if ($.inArray(e.which, [8, 0, 46]) !== -1 ||
        (e.which >= 37 && e.which <= 40)) {
        return true;
    }

    // Allow numbers only (48–57)
    if (e.which >= 48 && e.which <= 57) {
        return true;
    }

    // Block everything else
    return false;
});

$(document).on("click", ".save-btn", function () {

    let btn = $(this);
    let tr = btn.closest("tr");

    if (tr.hasClass("saved-row")) {
        alert("Already saved. Editing disabled!");
        return;
    }

    // CASH RADIANT validation
    let cashRadiant = tr.find(".cash_radiant span").text().trim();
    if (cashRadiant === "") {
        alert("Cash Radiant Required");
        return;
    }

    // CARD RADIANT validation
    let cardRadiant = tr.find(".card_radiant span").text().trim();
    if (cardRadiant === "") {
        alert("Card Orange Required");
        return;
    }


    let zone = $("#izone_views").val();
    let location = tr.find(".location-cell").data("location");
    let dummy = $('#dateallviews').text();
    let firstDate = dummy.split('-')[0].trim();

    let data = {
        zone_name: zone,
        location_name: location,
        date_range: firstDate,

        cash_radiant : tr.find(".cash_radiant .edit-text").text(),
        cash_date_filter : tr.find(".cash_radiant .calander-text").text(),
        cash_date_amt_filter : tr.find(".cash_radiant .tooltip-text").text(),
        cash_bank    : tr.find(".cash_bank span").text(),

        card_radiant : tr.find(".card_radiant .edit-text").text(),
        card_date_filter : tr.find(".card_radiant .calander-text").text(),
        card_date_amt_filter : tr.find(".card_radiant .tooltip-text").text(),

        upi_radiant  : tr.find(".upi_radiant .edit-text").text(),
        upi_date_filter : tr.find(".upi_radiant .calander-text").text(),
        upi_date_amt_filter : tr.find(".upi_radiant .tooltip-text").text(),

        bank_stmt_charge  : tr.find(".bank_stmt_charge span").text(),
        bank_stmt_amount  : tr.find(".bank_stmt_amount span").text(),
        bank_stmt_diff  : tr.find(".bank_stmt_diff span").text(),

        neft_bank    : tr.find(".neft_bank .edit-text").text(),
        neft_date_filter : tr.find(".neft_bank .calander-text").text(),
        neft_date_amt_filter : tr.find(".neft_bank .tooltip-text").text(),
        _token: $("meta[name='csrf-token']").attr("content")
    };
    console.log("data",data);

        $.ajax({
            url: incomestore,
            type: "POST",
            data: {
                ...data,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                if(res.status !== 200){
                    Swal.fire("Error","Save failed!","error");
                    return;
                }
                const d = res.data;
                let tr = $(".save-btn:focus").closest("tr");
                let btn = $(".save-btn:focus");

                function clean(v){ return Number(String(v).replace(/[^0-9.-]/g,'')) || 0; }
                function format(v){ return "₹" + Number(v).toLocaleString(); }

                function updateBlock(cls, radiant, date, amt){
                    let td = tr.find("."+cls);
                    td.find(".edit-text").text(clean(radiant));
                    td.find(".tooltip-text").text(clean(amt));
                    td.find(".calander-text").text(date || "-");
                    td.find(".custom-tooltip").remove();
                    let html = `
                    <div class="custom-tooltip">
                        <table>
                            <tr>
                                <td>${date || "-"}</td>
                                <td style="text-align:right;">${format(amt)}</td>
                            </tr>
                            <tr class="total">
                                <td>Total</td>
                                <td style="text-align:right;">${format(amt)}</td>
                            </tr>
                        </table>
                    </div>`;
                    td.append(html);

                    td.find('.info-icon').show();
                    td.find('.calendar-icon').show();
                }
                // ----- CASH -----
                updateBlock("cash_radiant", d.cash_radiant, d.cash_date_filter, d.cash_date_amt_filter);
                // ----- CARD -----
                updateBlock("card_radiant", d.card_radiant, d.card_date_filter, d.card_date_amt_filter);
                // ----- UPI -----
                updateBlock("upi_radiant", d.upi_radiant, d.upi_date_filter, d.upi_date_amt_filter);
                // ----- NEFT -----
                updateBlock("neft_bank", d.neft_bank, d.neft_date_filter, d.neft_date_amt_filter);

                tr.find(".bank_stmt_charge .edit-text").text(clean(d.bank_stmt_charge));
                tr.find(".bank_stmt_amount .edit-text").text(clean(d.bank_stmt_amount));

                // If you want to update the diff column too
                let diff = clean(d.bank_stmt_amount) - clean(d.bank_stmt_charge);
                tr.find(".bank_stmt_diff span").text(format(diff));
                /* ------------------------------------------------
                ✅ BUTTON + ROW STATE (WHAT YOU REQUESTED)
                ------------------------------------------------ */
                // hide save / back — show apply icon etc.
                tr.find(".apply-btn-icon").show();
                tr.find(".save-btn").hide();
                tr.find(".back-btn").hide();
                // disable save button & change style/text
                btn.prop("disabled", true)
                .text("Saved")
                .removeClass("btn-primary")
                .addClass("btn-secondary");
                // lock all editable text
                tr.find(".editable span")
                .attr("contenteditable","false")
                .addClass("saved-cell");
                // mark row saved
                tr.addClass("saved-row");
                // recalc totals if you have function
                if(typeof calculateRow === "function"){
                    calculateRow(tr);
                }
                Swal.fire("Success","Saved successfully!","success");
            },
            error: function () {
                Swal.fire("Error", "Server error!", "error");
            }
        });
});
let existingFileStorage = {};
function loadRadiantValues(tr) {
    let zone = $(tr).find('td.zone_name[data-zone_name]').data('zone_name');
    let location = $(tr).find('td.location-cell[data-location]').data('location');
    let firstDate = $(tr).find('td.date-cell[data-date]').data('date');
    let rowIndex = tr.index();
    if (!location) return;

    $.ajax({
        url: incomeradiantfetch, // your route
        type: "GET",
        data: {
            zone_name: zone,
            location_name: location,
            date_range: firstDate
        },
        success: function(res) {
            if (res.status !== 200 || !res.data) return;

            let d = res.data;

            // ------------------- Helper functions -------------------
            const clean = v => Number(String(v).replace(/[^0-9.-]/g, '')) || 0;
            const format = v => "₹" + Number(v).toLocaleString();

            // ------------------- UPDATE FILE ICONS -------------------
            function setFileIcon(tdSelector, files) {
                let td = tr.find(tdSelector);
                td.find(".uploaded-preview, .remark-preview-icon").remove();

                if (files && files.length > 0) {
                    // Create the single icon
                    let icon = $(`
                        <i class="fa fa-eye uploaded-preview"
                        style="cursor:pointer; color:#28a745; position: relative;">
                        </i>
                    `);

                    // Create the custom tooltip div
                    let tooltip = $('<div class="file-tooltip" style="display:none; position:absolute; top:-50px; left:0; background:#fff; border:1px solid #ccc; padding:5px; z-index:999; max-height:200px; overflow:auto; min-width:150px;"></div>');

                    // Add file links
                    files.forEach(file => {
                        let fileLink = $(`<div class="file-item" style="cursor:pointer; padding:2px 5px;" data-file="${file}">${file}</div>`);
                        tooltip.append(fileLink);

                        // Click to open preview
                        fileLink.on("click", function() {
                            let f = $(this).data("file");
                            openFilePreview(f); // Your modal preview function
                        });
                    });

                    icon.append(tooltip);
                    td.append(icon);

                    // -------- Hover logic: show tooltip when hovering icon or tooltip --------
                    function showTooltip() { tooltip.show(); }
                    function hideTooltip() {
                        setTimeout(() => {
                            if (!icon.is(":hover") && !tooltip.is(":hover")) tooltip.hide();
                        }, 50);
                    }

                    icon.on("mouseenter", showTooltip);
                    icon.on("mouseleave", hideTooltip);
                    tooltip.on("mouseenter", showTooltip);
                    tooltip.on("mouseleave", hideTooltip);
                }
            }



            function setRemarkIcon(tdSelector, file, remarkText) {
                let td = tr.find(tdSelector);
                td.find(".remark-preview-icon").remove();

                // show icon if either file OR remark exists
                if (file || remarkText) {
                    let remarkIcon = $(`
                        <i class="fa fa-eye remark-preview-icon"
                        data-file="${file ?? ''}"
                        data-remark="${remarkText ?? ''}"
                        title="View Remark"
                        style="cursor:pointer;margin-left:6px;color:#007bff;">
                        </i>
                    `);

                    td.append(remarkIcon);
                }
            }

            /* ======================================================
               ⭐ STORE EXISTING FILES (ONLY ADDITION – NOTHING REMOVED)
            ====================================================== */
            existingFileStorage[rowIndex] = {
                collection_amount_files : d.collection_amount_files ? JSON.parse(d.collection_amount_files) : [],
                deposit_amount_files    : d.deposit_amount_files ? JSON.parse(d.deposit_amount_files) : [],
                mespos_card_files       : d.mespos_card_files ? JSON.parse(d.mespos_card_files) : [],
                mespos_upi_files        : d.mespos_upi_files ? JSON.parse(d.mespos_upi_files) : [],
                bank_upi_card_files     : d.bank_upi_card_files ? JSON.parse(d.bank_upi_card_files) : [],
                bank_neft_files         : d.bank_neft_files ? JSON.parse(d.bank_neft_files) : [],
                bank_others_files       : d.bank_others_files ? JSON.parse(d.bank_others_files) : [],
                cash_utr_files          : d.cash_utr_files ? JSON.parse(d.cash_utr_files) : [],
                card_upi_utr_files      : d.card_upi_utr_files ? JSON.parse(d.card_upi_utr_files) : [],
                neft_utr_files          : d.neft_utr_files ? JSON.parse(d.neft_utr_files) : [],
                other_utr_files         : d.other_utr_files ? JSON.parse(d.other_utr_files) : []
            };


            function bindClickPreview(td, json) {
                if (!json) return;

                let files = [];
                try {
                    files = JSON.parse(json);
                } catch (e) {}

                if (!files.length) return;

                td
                    .addClass("text-primary")
                    .css({ cursor: "pointer", textDecoration: "underline" })
                    .off("click.preview")
                    .on("click.preview", function () {
                        openFilePreview(files);
                    });
            }



            tr.find(".cash_mocdoc").text(clean(d.moc_cash_amt));
            tr.find(".card_mocdoc").text(clean(d.moc_card_amt));
            tr.find(".upi_mocdoc").text(clean(d.moc_upi_amt));
            tr.find(".total_upi_card").text(clean(d.moc_total_upi_card));
            tr.find(".neft_mocdoc").text(clean(d.moc_neft_amt));
            tr.find(".other_mocdoc").text(clean(d.moc_other_amt));
            tr.find(".total_moc").text(clean(d.moc_overall_total));

            // ------------------- SET FILES -------------------
            // setFileIcon(".deposite_amount", d.deposite_amount_attachment ? JSON.parse(d.deposite_amount_attachment) : []);
            setRemarkIcon(".cash_radiant", d.remark_files,d.remark);

            // ------------------- SET DATES -------------------
            if (d.date_collection) tr.find(".date_collection .selected-date").text(d.date_collection);
            if (d.collection_amount) tr.find(".collection_amount .edit-text").text(d.collection_amount);
            if (d.date_deposited) tr.find(".date_deposited .selected-date").text(d.date_deposited);
            if (d.deposite_amount) tr.find(".deposite_amount .edit-text").text(d.deposite_amount);
            if (d.date_settlement) tr.find(".date_settlement .selected-date").text(d.date_settlement);

             tr.find(".utr_transcation_ids .edit-text_new")
              .text(d.cash_utr_number || "-");

            tr.find(".mespos_card .edit-text").text(clean(d.mespos_card));
            tr.find(".mespos_upi .edit-text").text(clean(d.mespos_upi));
            // ------------------- BANK VALUES -------------------
            tr.find(".bank_chargers .edit-text").text(clean(d.bank_chargers));
            tr.find(".bank_upi_card .edit-text").text(clean(d.bank_upi_card));
            tr.find(".bank_neft .edit-text").text(clean(d.bank_neft));
            tr.find(".bank_others .edit-text").text(clean(d.bank_others));

             bindClickPreview(tr.find(".collection_amount"), d.collection_amount_files);
            bindClickPreview(tr.find(".deposite_amount"), d.deposit_amount_files);
            bindClickPreview(tr.find(".mespos_card"), d.mespos_card_files);
            bindClickPreview(tr.find(".mespos_upi"), d.mespos_upi_files);
            bindClickPreview(tr.find(".bank_upi_card"), d.bank_upi_card_files);
            bindClickPreview(tr.find(".bank_neft"), d.bank_neft_files);
            bindClickPreview(tr.find(".bank_others"), d.bank_others_files);
            bindClickPreview(tr.find(".utr_transcation_ids"), d.cash_utr_files);

            // UTR files stored on cell and opened via delegated click on .bank_utr_transcation_ids
            // ------------------- DIFFS -------------------
            // tr.find(".radiant_diff span").text(clean(d.radiant_diff));
            // tr.find(".cash_diff span").text(clean(d.cash_diff));
            // tr.find(".card_upi_diff span").text(clean(d.card_upi_diff));
            // tr.find(".neft_others_diff span").text(clean(d.neft_others_diff));

            // ------------------- RECALCULATE ROW -------------------
             // =================================================
            // ✅ BANK UTR (SPAN + ICON + TOOLTIP)
            // =================================================
            let utrIcon = tr.find(".utr-icon")[0];
            let utrTd   = tr.find(".bank_utr_transcation_ids");

            let upi    = d.bank_upi_card_utr || "";
            let neft   = d.bank_neft_utr || "";
            let others = d.bank_other_utr || "";

            // Build combined UTR files (support string or array, alternate keys)
            function parseUtrFileList(v) {
                if (!v) return [];
                try { return Array.isArray(v) ? v : JSON.parse(v); } catch (e) { return []; }
            }
            var combinedUtrFiles = [
                ...parseUtrFileList(d.card_upi_utr_files),
                ...parseUtrFileList(d.bank_upi_card_utr_files),
                ...parseUtrFileList(d.neft_utr_files),
                ...parseUtrFileList(d.other_utr_files)
            ];
            if (combinedUtrFiles.length === 0) {
                combinedUtrFiles = [
                    ...parseUtrFileList(d.card_upi_utr_files),
                    ...parseUtrFileList(d.neft_utr_files),
                    ...parseUtrFileList(d.other_utr_files)
                ];
            }
            utrTd.data("utr-files", combinedUtrFiles);
            if (upi || neft || others) {
                utrTd.addClass("utr-cell-clickable").css({ cursor: "pointer", textDecoration: "underline" });
            }

            if (utrIcon) {
                const normalize = v =>
                    v !== null && v !== undefined && v !== '' && Number(v) !== 0 ? v : '-';


                utrIcon.dataset.upi    = normalize(upi);
                utrIcon.dataset.neft   = normalize(neft);
                utrIcon.dataset.others = normalize(others);

                // store in dataset
                // utrIcon.dataset.upi    = upi !==0 ? upi :'-';
                // utrIcon.dataset.neft   = neft !==0 ? neft :'-';
                // utrIcon.dataset.others = others !==0 ? others :'-';

                // span text (multi-line, no hide)
                let spanHtml = `
                    <b>UPI / CARD:</b> ${upi  || "-"}<br>
                    <b>NEFT:</b> ${neft || "-"}<br>
                    <b>OTHERS:</b> ${others || "-"}
                `;

                utrTd.find(".utr-text")
                    .html(spanHtml)
                    .removeClass("text-muted")
                    .addClass("text-dark");

                // tooltip
                utrIcon.setAttribute("title", spanHtml);

                // icon visibility + color
                if (upi || neft || others) {
                    utrIcon.style.display = "inline-block";
                    utrIcon.classList.remove("text-primary");
                    utrIcon.classList.add("text-success");
                }

                // reinit tooltip
                if ($(utrIcon).data("bs.tooltip")) {
                    $(utrIcon).tooltip("dispose");
                }
                $(utrIcon).tooltip({ html: true, placement: "top" });
                $(utrIcon).hide();
            }


            calculateRow(tr);
            // ------------------- BUTTON VISIBILITY -------------------
            if (d.cash_radiant === 1) {
                tr.find(".edit-btn").hide();
                tr.find(".apply-btn-icon").show();
            } else {
                tr.find(".edit-btn").show();
                tr.find(".apply-btn-icon").hide();
            }

            // ------------------- TOOLTIP INIT -------------------
            $('[title]').tooltip();
        },
        error: function(xhr, status, error) {
            console.error("Error fetching radiant values:", error);
        }
    });
}
function openFilePreview(files, remark = "") {

    if (!files || !files.length) {
        Swal.fire("Info", "No files available", "info");
        return;
    }

    if (!Array.isArray(files)) files = [files];
    // Filter: only include non-empty string paths (file existence shown via onerror in viewer)
    files = files.filter(f => f && typeof f === "string" && f.trim() !== "");

    if (files.length === 0) {
        Swal.fire("Info", "No valid file paths to preview.", "info");
        return;
    }

    let modal = $("#filePreviewModal");
    let body  = $("#filePreviewBody");

    body.empty();

    modal.find(".remark-text").text(remark || "");

    files.forEach(file => {

        let fileUrl = window.APP_BASE_URL + '/' + file.replace(/^\/+/, "");
        let ext = (file.split(".").pop() || "").toLowerCase();
        let fileName = file.split('/').pop() || file;
        let html = "";
        const fileNotFoundHtml = '<p class="text-danger small mb-0">File not found or unable to load.</p>';

        if (["jpg", "jpeg", "png", "webp", "gif"].includes(ext)) {
            html = `
                <img src="${fileUrl}" alt="${fileName}"
                     style="max-width:100%;max-height:500px;display:block;margin:auto;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <p class="text-danger small mb-0" style="display:none;">File not found or unable to load.</p>
            `;
        }
        else if (ext === "pdf") {
            html = `
                <embed src="${fileUrl}" type="application/pdf" width="100%" height="600px"
                       onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <p class="text-danger small mb-0" style="display:none;">File not found or unable to load.</p>
            `;
        }
        else {
            html = `
                <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                    Download ${fileName}
                </a>
            `;
        }

        body.append(`
            <div class="mb-3 border rounded p-2 file-preview-item">
                ${html}
            </div>
        `);
    });

    $("#downloadFileBtn")
        .off("click")
        .on("click", function () {
            if (files[0]) window.open((window.APP_BASE_URL || "") + "/" + files[0].replace(/^\/+/, ""), "_blank");
        });

    modal.modal("show");
}


// function loadRadiantValues(tr) {
//      let zone = $(tr).find('td.zone_name[data-zone_name]').data('zone_name');
//     let location = $(tr).find('td.location-cell[data-location]').data('location');
//     let firstDate = $(tr).find('td.date-cell[data-date]').data('date');

//     if (!location) return;

//     // let dummy = $('#dateallviews').text();
//     // let firstDate = dummy.split('-')[0].trim();
//     // // console.log("firstDate",firstDate);
//     // let zone = $("#izone_views").val();
//     // let location = tr.find(".location-cell").data("location");
//     // console.log("location",location);

//     // if (!location) return;

//     // let dummy = $('#dateallviews').text();
//     // let firstDate = dummy.split('-')[0].trim();
//     // console.log("incomeradiantfetch",incomeradiantfetch);

//     $.ajax({
//         url: incomeradiantfetch,
//         type: "GET",
//         data: {
//             zone_name: zone,
//             location_name: location,
//             date_range: firstDate
//         },

//         success:function(res){

//             console.log("radiant fetch",res);


//             let d = res.data;

//             // helpers
//             const clean  = v => Number(String(v).replace(/[^0-9.-]/g,'')) || 0;
//             const format = v => "₹" + Number(v).toLocaleString();


//             // -------- UPDATE ONE CELL --------
//             function updateBlock(cls, radiant, date, amt,remark,file,column) {
//                 let td = tr.find("." + cls);

//                 td.find(".edit-text").text(clean(radiant));
//                 td.find(".tooltip-text").text(clean(amt));
//                 td.find(".calander-text").text(date || "-");

//                 td.find(".custom-tooltip").remove();
//                 td.append(`<div class="custom-tooltip">Loading...</div>`); // temporary loading

//                 td.find(".info-icon").show();
//                 // td.find(".calendar-icon").show();

//                 if (date && date !== "-") {
//                     let dates = date.split(" - ");
//                     let fromDate = dates[0].trim();
//                     let toDate = dates[1] ? dates[1].trim() : fromDate;
//                     let branch_name = tr.find('.location-cell').data('location');   // adjust if your tr has branch info
//                     if(column !==''){
//                         $.ajax({
//                             url: incomedatecheck,
//                             type: "POST",
//                             data: {
//                                 branch_name: branch_name,
//                                 from_date  : fromDate,
//                                 to_date    : toDate,
//                                 column     : column,
//                                 type     : 2,
//                                 _token     : $('meta[name="csrf-token"]').attr('content')
//                             },
//                             success: function(res) {
//                                 // Directly send the response to buildTooltip
//                                 buildTooltip(td.find('.info-icon'), res, date,remark,file);
//                             },
//                             error: function() {
//                                 td.find('.custom-tooltip').text("Error fetching data!");
//                             }
//                         });
//                     }
//                 } else {
//                     // If no date, show static tooltip
//                     td.find('.custom-tooltip').html(`
//                         <table>
//                             <tr>
//                                 <td>${date || "-"}</td>
//                                 <td style="text-align:right;">${format(amt)}</td>
//                             </tr>
//                             <tr class="total">
//                                 <td>Total</td>
//                                 <td style="text-align:right;">${format(amt)}</td>
//                             </tr>
//                         </table>
//                     `);
//                 }
//                 td.find(".calendar-icon").hide();
//             }



//             // -------- APPLY --------
//             updateBlock("cash_radiant", d.cash_radiant, d.cash_date_filter, d.cash_date_amt_filter,d.cash_radiant_remark,d.cash_radiant_file,'cash_moc_amt');
//             updateBlock("cash_bank"   , d.cash_bank   , d.cash_date_filter, d.cash_bank_amt_filter,d.cash_radiant_remark,d.cash_bank_file,'');

//             updateBlock("card_radiant", d.card_radiant, d.card_date_filter, d.card_date_amt_filter,d.card_radiant_remark,d.card_radiant_file,'card_moc_amt');
//             updateBlock("card_bank"   , d.card_bank   , d.card_date_filter, d.card_bank_amt_filter,d.card_radiant_remark,'','');

//             updateBlock("upi_radiant" , d.upi_radiant , d.upi_date_filter , d.upi_date_amt_filter,d.upi_radiant_remark,d.upi_radiant_file,'upi_moc_amt');
//             updateBlock("upi_bank"    , d.upi_bank    , d.upi_date_filter , d.upi_bank_amt_filter,d.upi_radiant_remark,'','');

//             updateBlock("neft_bank"   , d.neft_bank   , d.neft_date_filter, d.neft_date_amt_filter,d.neft_radiant_remark,d.neft_bank_file,'neft_moc_amt');


//             tr.find(".bank_stmt_charge .edit-text").text(clean(d.bank_stmt_charge));
//             tr.find(".bank_stmt_amount .edit-text").text(clean(d.bank_stmt_amount));

//             // If you want to update the diff column too
//             let diff = clean(d.bank_stmt_amount) - clean(d.bank_stmt_charge);
//             tr.find(".bank_stmt_diff span").text(format(diff));
//             // -------- RECALC --------
//             calculateRow(tr);


//             // -------- BUTTONS --------
//             let allFilled =
//                 d.cash_radiant && d.cash_bank &&
//                 d.card_radiant && d.bank_stmt_charge &&
//                 d.upi_radiant  && d.bank_stmt_amount  &&
//                 d.neft_bank;
//             console.log("allFilled",allFilled);

//             if(allFilled){
//                 tr.find(".edit-btn").hide();
//                 tr.find(".apply-btn-icon").show();
//             } else {
//                 tr.find(".edit-btn").show();
//                 tr.find(".apply-btn-icon").hide();
//             }
//         }
//     });
// }


// preview js
let previewModal = new bootstrap.Modal(
    document.getElementById("filePreviewModal")
);

$(document).on("click", ".file-view", function (e) {
    e.preventDefault();
    $('.custom-tooltip').hide();
    let fileUrl = $(this).attr("href");
    let ext = fileUrl.split('.').pop().toLowerCase();

    let field = $(this).data("field");
    let titleText = fieldLabels[field] || "File Preview";

    // ✅ set dynamic heading
    $("#filePreviewModal .modal-title").text(titleText);

    let previewBody = $("#filePreviewBody");
    let downloadBtn = $("#downloadFileBtn");

    previewBody.html("");
    downloadBtn.attr("href", fileUrl);

    // IMAGE
    if (["jpg", "jpeg", "png", "gif", "webp",'jfif'].includes(ext)) {
        previewBody.html(`<img src="${fileUrl}" class="img-fluid rounded">`);
    }

    // PDF
    else if (ext === "pdf") {
        previewBody.html(`<iframe src="${fileUrl}" width="100%" height="500px"></iframe>`);
    }

    // TEXT / HTML
    else if (["txt", "html", "htm"].includes(ext)) {
        previewBody.html(`<iframe src="${fileUrl}" width="100%" height="500px"></iframe>`);
    }

    // DOC / XLS / PPT
    else if (["doc", "docx", "xls", "xlsx", "ppt", "pptx"].includes(ext)) {
        let viewerUrl =
            "https://docs.google.com/gview?url=" +
            encodeURIComponent(window.location.origin + fileUrl) +
            "&embedded=true";

        previewBody.html(`<iframe src="${viewerUrl}" width="100%" height="500px"></iframe>`);
    }

    // FALLBACK
    else {
        previewBody.html(`
            <p class="text-muted">Preview not supported.</p>
            <p>Please download the file.</p>
        `);
    }

    previewModal.show();
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
		$("#total_upi_card").text("0.00");
		$("#actuall_bank_chagers").text("0.00");
		$("#actuall_total_upi_card").text("0.00");
		$("#actuall_total_upicard_diff").text("0.00");
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
          morefilterview(fitterremovedata,datefilltervalue,1,incomeOverviewDatafitter);

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
        morefilterview([], moredatefittervale, 1, incomeOverviewDatafitter);
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
                    <td style="text-align:right;">₹${r.amount}</td>
                 </tr>`;
    });

    html += `<tr class="total">
                <td>Total</td>
                <td style="text-align:right;color: black;">₹${res.total_amount}</td>
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
        let filePath = `../radiant_files/${file}`;
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


});


