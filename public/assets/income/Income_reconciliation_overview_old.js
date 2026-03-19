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

        // Radiant DB values
        let radiantRows = responseData.radiant ?? responseData.radiantValues ?? [];

        // Convert radiantRows → map by id for fast merging
        let radiantMap = {};
        radiantRows.forEach(r => {
           radiantMap[r.mocdoc_id] = r;
        });

        // 🔥 Merge MocDoc + Radiant by ID
        ticketdataSource = mocdocRows.map(m => {
            let r = radiantMap[m.id] || {};

            return {
                ...m,   // keep MocDoc values

                // Merge Radiant DB values (if exists)
                cash_radiant: r.cash_radiant ?? "",
                cash_bank: r.cash_bank ?? "",
                card_radiant: r.card_radiant ?? "",
                card_bank: r.card_bank ?? "",
                upi_radiant: r.upi_radiant ?? "",
                upi_bank: r.upi_bank ?? "",
                neft_bank: r.neft_bank ?? "",

                // Differences
                cash_diff: r.cash_diff ?? "0.00",
                cash_bank_diff: r.cash_bank_diff ?? "0.00",
                card_diff: r.card_diff ?? "0.00",
                card_bank_diff: r.card_bank_diff ?? "0.00",
                upi_diff: r.upi_diff ?? "0.00",
                upi_bank_diff: r.upi_bank_diff ?? "0.00",
                neft_diff: r.neft_diff ?? "0.00"
            };
        });

        console.log("✅ Final ticketdataSource:", ticketdataSource); // Debug log

        totalItems = ticketdataSource.length;

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

    console.log("📄 Rendering page data:", pageData); // Debug log

    // 🔥 FIX: Find the Consolidated row to get totals
    let consolidatedRow = data.find(row => row.type === "Consolidated");

    if (consolidatedRow) {
        $('#total_cash').text(parseFloat(consolidatedRow.total_cash_amt || 0).toFixed(2));
        $('#total_card').text(parseFloat(consolidatedRow.total_card_amt || 0).toFixed(2));
        $('#total_neft').text(parseFloat(consolidatedRow.total_neft_amt || 0).toFixed(2));
        $('#total_upi').text(parseFloat(consolidatedRow.total_upi_amt || 0).toFixed(2));
        $('#total_amount').text(parseFloat(consolidatedRow.total_total_amt || 0).toFixed(2));
    } else {
        // Reset if no consolidated row found
        $('#total_cash').text('0.00');
        $('#total_card').text('0.00');
        $('#total_neft').text('0.00');
        $('#total_upi').text('0.00');
        $('#total_amount').text('0.00');
    }

    var body = "";
    body = ticketData(pageData, body);

    $("#daily_details_recon").html(body);
    //  applyRadiantFiles();
    $("#today_visits").text(totalItems);
    $("#dcounts").text(totalItems);
    // 🔥 RELOAD radiant values for every row
$("#daily_details_recon tr").each(function () {
    // loadRadiantValues($(this));
});
}
// Also call loadRadiantValues() when user filters
$(document).on("change", "#izone_views, #ibranch_views, #reportrange", function() {
    $("#daily_details_recon tr").each(function () {
        loadRadiantValues($(this));
    });
});

function format(v) {
    return parseFloat(v || 0).toFixed(2);
}
function groupByLocation(data) {
    const grouped = {};

    data.forEach(row => {
        if (row.type === "Consolidated") return; // skip consolidated, we calculate manually

        const loc = row.area;

        if (!grouped[loc]) {
            grouped[loc] = {
                billdate: row.billdate,
                area: row.area,
                cash: 0,
                card: 0,
                upi: 0,
                neft: 0,
                total: 0
            };
        }

        grouped[loc].cash += parseFloat(row.cash_amt || 0);
        grouped[loc].card += parseFloat(row.card_amt || 0);
        grouped[loc].upi += parseFloat(row.upi_amt || 0);
        grouped[loc].neft += parseFloat(row.neft_amt || 0);
        grouped[loc].total += parseFloat(row.total_amt || 0);
    });

    return Object.values(grouped);
}
function ticketData(pageData, body) {
    var sno = 1;

    // Grouping happens here
    const groupedData = groupByLocation(pageData);

    if (groupedData.length === 0) {
        body += '<tr><td colspan="22" style="text-align:center">No Data</td></tr>';
        return body;
    }

    groupedData.forEach(usr => {

        let billDate = usr.billdate ? moment(usr.billdate).format("DD MMM YYYY") : "-";

        body += `
        <tr>

        </tr>
        `;
        sno++;
    });

    return body;
}


function applyRadiantFiles() {
    $("tbody tr").each(function () {

        let tr = $(this);
        let location = tr.find(".location-cell").data("location");
        let dateRange = $('#dateallviews').text().split('-')[0].trim();

        if (!location || !dateRange) return;

        $.ajax({
            url: incomeradiantfetch,
            type: "GET",
            data: {
                location_name: location,
                date_range: dateRange
            },
            success: function (res) {
                console.log("res",res);

                if (res.status !== 200 || !res.data) return;
                console.log("1212");

                const fileMap = {
                    cash_radiant: 'cash_radiant_file',
                    cash_bank: 'cash_bank_file',
                    card_radiant: 'card_radiant_file',
                    card_bank: 'card_bank_file',
                    upi_radiant: 'upi_radiant_file',
                    upi_bank: 'upi_bank_file',
                    neft_bank: 'neft_bank_file'
                };

                Object.keys(fileMap).forEach(field => {
                    let filePath = res.data[fileMap[field]];
                    let td = tr.find(`.${field}`);
                    console.log("filePath",filePath);

                    if (filePath) {
                        td.find(".plus-icon").hide();
                        td.find(".file-view")
                          .removeClass("d-none")
                          .attr("href", window.location.origin + "/hms/public/radiant_files/" + filePath);

                    }
                });
            }
        });
    });
}


function calculateRow(tr) {

    const toNum = v => parseFloat(v) || 0;

    const setVal = (dis, el, val) => {

        el.text(val % 1 === 0 ? val : val.toFixed(2));

        dis.removeClass("diff-zero diff-positive diff-negative");

        if (val === 0) {
            dis.addClass("diff-zero");
        }
        else if (val > 0) {
            dis.addClass("diff-negative");
        }
        else {
            dis.addClass("diff-positive");
        }
    };

    /* ------------ CASH ------------ */
    let cash_moc  = toNum(tr.find(".cash_radiant .tooltip-text").text());
    let cash_rad  = toNum(tr.find(".cash_radiant .edit-text").text());
    let cash_bank = toNum(tr.find(".cash_bank .edit-text").text());

    setVal(tr.find(".cash_diff"), tr.find(".cash_diff span"), cash_moc - cash_rad);
    setVal(tr.find(".cash_bank_diff"), tr.find(".cash_bank_diff span"),cash_rad - cash_bank );


    /* ------------ CARD ------------ */
    let card_moc  = toNum(tr.find(".card_radiant .tooltip-text").text());
    let card_rad_change  = toNum(tr.find(".card_radiant .edit-text").text());
    let card_bank = toNum(tr.find(".card_bank .edit-text").text());

    setVal(tr.find(".card_diff"), tr.find(".card_diff span"), card_moc - card_rad_change);
    setVal(tr.find(".card_bank_diff"), tr.find(".card_bank_diff span"), card_bank - card_rad_change);


    /* ------------ UPI ------------ */
    let upi_moc  = toNum(tr.find(".upi_radiant .tooltip-text").text());
    let upi_rad_change  = toNum(tr.find(".upi_radiant .edit-text").text());
    let upi_bank = toNum(tr.find(".upi_bank .edit-text").text());

    setVal(tr.find(".upi_diff"), tr.find(".upi_diff span"), upi_moc - upi_rad_change);
    setVal(tr.find(".upi_bank_diff"), tr.find(".upi_bank_diff span"), upi_bank - upi_rad_change);


    /* ------------ NEFT ------------ */
    let neft_moc  = toNum(tr.find(".neft_mocdoc").text());   // mocdoc column already plain text
    let neft_bank = toNum(tr.find(".neft_bank .edit-text").text());

    setVal(tr.find(".neft_diff"), tr.find(".neft_diff span"), neft_moc - neft_bank);


    /* ------------ BANK STATEMENT DIFFERENCE ------------ */
    let card_rad = toNum(tr.find(".card_radiant .edit-text").text());
    let upi_rad  = toNum(tr.find(".upi_radiant .edit-text").text());
    let charge   = toNum(tr.find(".bank_stmt_charge .edit-text").text());
    let bank_amt = toNum(tr.find(".bank_stmt_amount .edit-text").text());

    let expected = (card_rad + upi_rad) - charge;
    let difference = expected - bank_amt;

    setVal(tr.find(".bank_stmt_diff"), tr.find(".bank_stmt_diff span"), difference);
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


// function loadRadiantValues(tr) {
//     let zone = $("#izone_views").val();
//     let location = tr.find(".location-cell").data("location");

//     if (!location) return;

//     let dummy = $('#dateallviews').text();
//     let firstDate = dummy.split('-')[0].trim();

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
//             function updateBlock(cls, radiant, date, amt,column) {
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
//                                 _token     : $('meta[name="csrf-token"]').attr('content')
//                             },
//                             success: function(res) {
//                                 // Directly send the response to buildTooltip
//                                 buildTooltip(td.find('.info-icon'), res, date);
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
//             updateBlock("cash_radiant", d.cash_radiant, d.cash_date_filter, d.cash_date_amt_filter,'cash_moc_amt');
//             updateBlock("cash_bank"   , d.cash_bank   , d.cash_date_filter, d.cash_bank_amt_filter,'');

//             updateBlock("card_radiant", d.card_radiant, d.card_date_filter, d.card_date_amt_filter,'card_moc_amt');
//             updateBlock("card_bank"   , d.card_bank   , d.card_date_filter, d.card_bank_amt_filter,'');

//             updateBlock("upi_radiant" , d.upi_radiant , d.upi_date_filter , d.upi_date_amt_filter,'upi_moc_amt');
//             updateBlock("upi_bank"    , d.upi_bank    , d.upi_date_filter , d.upi_bank_amt_filter,'');

//             updateBlock("neft_bank"   , d.neft_bank   , d.neft_date_filter, d.neft_date_amt_filter,'neft_moc_amt');


//             // -------- FILE LINKS --------
//             const fileMap = {
//                 cash_radiant: 'cash_radiant_file',
//                 cash_bank: 'cash_bank_file',
//                 card_radiant: 'card_radiant_file',
//                 card_bank: 'card_bank_file',
//                 upi_radiant: 'upi_radiant_file',
//                 upi_bank: 'upi_bank_file',
//                 neft_bank: 'neft_bank_file'
//             };

//             Object.keys(fileMap).forEach(field => {

//                 let filePath = d[fileMap[field]];
//                 let td = tr.find(`.${field}`);

//                 if(filePath){

//                     td.find(".plus-icon").hide();

//                     td.find(".file-view")
//                         .removeClass("d-none")
//                         .attr(
//                             "href",
//                             window.location.origin +
//                             "/hms/public/radiant_files/" +
//                             filePath
//                         );
//                 }
//             });

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
      	 $("#daily_details_recon").hide();
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
});


