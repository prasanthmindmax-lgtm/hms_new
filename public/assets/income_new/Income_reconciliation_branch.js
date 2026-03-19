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
// function rowfilterrange(uniqueResults, moredatefittervale, urlstatus, url) {
//     $("#daily_details").hide();
//     if (!uniqueResults || uniqueResults.length === 0) {
//         stopLoader(true);
//         if (urlstatus == 1) {
//             $(".search_daily").hide();
//             $('.clear_views').hide();
//             overall_fetch(2);
//         }
//         return;
//     }
//     const morefilltersall = uniqueResults.join(" AND");

//     $.ajax({
//         url: url,
//         type: "GET",
//         data: {
//             morefilltersall,
//             moredatefittervale
//         },
//         success: function (responseData) {
//             var result = rowhandleSuccess(responseData, urlstatus);
//             return result;
//         },
//         error: function (xhr, status, error) {
//             stopLoader(false, error);
//             console.error('❌ AJAX Error:', {
//                 status,
//                 error,
//                 response: xhr.responseText
//             });
//         }
//     });
// }
function rowfilterrange(uniqueResults, moredatefittervale, urlstatus, url, onDone) {

    $("#daily_details").hide();

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

            var result = rowhandleSuccess(responseData, urlstatus);
            console.log("result",result);

            // 🔥 call your callback here
            if (typeof onDone === "function") {
                onDone(result);
            }
        },
        error: function(xhr,status,error){
            stopLoader(false,error);
            console.error(error);
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
            console.log("locations",locations);

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
                neft_diff: r.neft_diff ?? "0.00",
                isApprover: responseData.isApprover,
            };
        });

        console.log("✅ Final ticketdataSource:", ticketdataSource); // Debug log

        totalItems = ticketdataSource.length;

        console.log("totalItems",totalItems);

        var ticketpageSize = parseInt($('#itemsPerPageSelect').val());

        console.log("ticketpageSize",ticketpageSize);

        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);
    }
}

function groupByBillDateAndArea(data) {
    const grouped = {};

    data.forEach(row => {

        if (row.type === "Consolidated") return;

        const loc = row.area || "";
        const date = row.billdate || "";
        const zone_name = row.zone_name || "";

        // unique key for SAME AREA + SAME DATE
        const key = loc + "|" + date;
        console.log("key",key);

        if (!grouped[key]) {
            grouped[key] = {
                billdate: date,
                zone_name: zone_name,
                area: loc,
                total_cash: 0,
                total_card: 0,
                total_upi: 0,
                total_neft: 0,
                total_amount: 0
            };
        }
        console.log("upper grouped",grouped);

        grouped[key].total_cash  += parseFloat(row.cash_amt  || 0);
        grouped[key].total_card  += parseFloat(row.card_amt  || 0);
        grouped[key].total_upi   += parseFloat(row.upi_amt   || 0);
        grouped[key].total_neft  += parseFloat(row.neft_amt  || 0);
        grouped[key].total_amount += parseFloat(row.total_amt || 0);
        console.log("lower grouped",grouped);

    });

    return Object.values(grouped);
}



var rowdataSource = [];
var groupedTotals = [];

function rowhandleSuccess(responseData, urlstatus) {
    console.log("urlstatus",urlstatus);
    console.log("rowhandleSuccess responseData",responseData);

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
    totalPages =totalPages/2;
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
    console.log("ticketrenderTable data",data);

    if (!Array.isArray(data)) {
        if (data && typeof data === 'object') {
            data = [data];
        } else {
            data = [];
        }
    }
    var startIdx = (pageNum - 1) * (ticketpageSize *2) ;
    var endIdx = pageNum * (ticketpageSize *2) ;
    var pageData = data.slice(startIdx, endIdx);
    console.log("📄 Rendering page data:", pageData); // Debug log
    // 🔥 FIX: Find the Consolidated row to get totals
    let consolidatedRow = data.find(row => row.type === "Consolidated");
    console.log("consolidatedRow",consolidatedRow);

    if (consolidatedRow) {
        $('#total_cash').text(parseFloat(consolidatedRow.total_cash_amt || 0).toFixed(2));
        $('#total_card').text(parseFloat(consolidatedRow.total_card_amt || 0).toFixed(2));
        $('#total_neft').text(parseFloat(consolidatedRow.total_neft_amt || 0).toFixed(2));
        $('#total_upi').text(parseFloat(consolidatedRow.total_upi_amt || 0).toFixed(2));
        $('#total_other_amt').text(parseFloat(consolidatedRow.total_other_amt || 0).toFixed(2));
        $('#total_amount').text(parseFloat(consolidatedRow.total_total_amt || 0).toFixed(2));
    } else {
        // Reset if no consolidated row found
        $('#total_cash').text('0.00');
        $('#total_card').text('0.00');
        $('#total_neft').text('0.00');
        $('#total_upi').text('0.00');
        $('#total_other_amt').text('0.00');
        $('#total_amount').text('0.00');
    }

    var body = "";
    console.log("111");

    body = ticketData(pageData, body);

    $("#daily_details_recon").html(body);
    $("#today_visits").text(totalItems);
    $("#dcounts").text(totalItems);
    // 🔥 RELOAD radiant values for every row
    $("#daily_details_recon tr").each(function () {
    //  applyRadiantFiles();
    initDatePickers();
    loadRadiantValues($(this));
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

        if (row.type === "Consolidated") return;

        const loc = row.area || "";
        const date = row.billdate || "";
        const isApprover = row.isApprover || "";

        // unique key for SAME AREA + SAME DATE
        const key = loc + "|" + date;

        if (!grouped[key]) {
            grouped[key] = {
                billdate: date,
                area: loc,
                isApprover: isApprover,
                cash: 0,
                card: 0,
                upi: 0,
                neft: 0,
                others: 0,
                total: 0
            };
        }

        grouped[key].cash  += parseFloat(row.cash_amt  || 0);
        grouped[key].card  += parseFloat(row.card_amt  || 0);
        grouped[key].upi   += parseFloat(row.upi_amt   || 0);
        grouped[key].neft  += parseFloat(row.neft_amt  || 0);
        grouped[key].others  += parseFloat(row.other_amt  || 0);
        grouped[key].total += parseFloat(row.total_amt || 0);

    });

    return Object.values(grouped);
}

// function ticketData(pageData, body) {
//     var sno = 1;
//     console.log("ticketData pageData",pageData);

//     // Grouping happens here
//     const groupedData = groupByLocation(pageData);
//     console.log("groupedData",groupedData);

//     if (groupedData.length === 0) {
//         body += '<tr><td colspan="22" style="text-align:center">No Data</td></tr>';
//         return body;
//     }

//     groupedData.forEach(usr => {

//         let billDate = usr.billdate ? moment(usr.billdate).format("DD MMM YYYY") : "-";
//         let billformateDate = usr.billdate ? moment(usr.billdate).format("DD/MM/YYYY") : "-";

//         body += `
//         <tr>
//             <td class="tdview">#${sno}</td>
//             <td class="tdview bill-date" data-date="${billformateDate}">
//                 ${billDate}
//             </td>
//             <td class="tdview location-cell" data-location="${usr.area}">${usr.area}</td>
//             <td class="tdview cash_mocdoc">${usr.cash.toFixed(2)}</td>
//             <td class="tdview cash_radiant editable" id="cash_radiant">
//                 <input type="hidden" class="hidden-range" >
//                 <i class="fa fa-calendar calendar-icon" data-field="cash_moc_amt" style="display:none"></i>
//                 <span class="calander-text" style="display:none"></span>
//                 <i class="fa fa-info-circle info-icon" style="display:none"></i>
//                 <span class="tooltip-text" style="display:none"></span>
//                 <span class="edit-text"></span>

//             </td>
//             <td class="tdview cash_diff"><span>0</span></td>

//             <td class="tdview cash_bank editable" id="cash_bank">
//                 <span class="edit-text"></span>
//                 <i class="fa fa-plus plus-icon" data-field="cash_bank"></i>
//                 <!-- view -->
//                 <a href="#" class="file-view d-none" target="_blank"data-field="cash_bank">
//                         <i class="fa fa-info-circle text-success"></i>
//                 </a>
//             </td>
//             <td class="tdview cash_bank_diff"><span>0</span></td>
//             <td class="tdview  card_mocdoc">${usr.card.toFixed(2)}</td>
//             <td class="tdview card_radiant editable" id="card_radiant">
//                 <input type="hidden" class="hidden-range" >
//                 <i class="fa fa-calendar calendar-icon" data-field="card_moc_amt" style="display:none"></i>
//                 <span class="calander-text" style="display:none"></span>
//                 <i class="fa fa-info-circle info-icon" style="display:none"></i>
//                 <span class="tooltip-text" style="display:none"></span>
//                 <span class="edit-text"></span>
//             </td>
//             <td class="tdview card_diff"><span>0</span></td>


//             <td class="tdview upi_mocdoc">${usr.upi.toFixed(2)}</td>
//             <td class="tdview upi_radiant editable" id="upi_radiant">
//                 <input type="hidden" class="hidden-range" >
//                 <i class="fa fa-calendar calendar-icon" data-field="upi_moc_amt" style="display:none"></i>
//                 <span class="calander-text" style="display:none"></span>
//                 <i class="fa fa-info-circle info-icon" style="display:none"></i>
//                 <span class="tooltip-text" style="display:none"></span>
//                 <span class="edit-text"></span>

//             </td>
//             <td class="tdview upi_diff" ><span>0</span></td>
//             <td class="tdview bank_stmt_charge editable" id="bank_stmt_charge"> <span class="edit-text"></span>
//                 <i class="fa fa-plus plus-icon" data-field="bank_stmt_charge"></i>
//                 <a href="#" class="file-view d-none" target="_blank" data-field="bank_stmt_charge">
//                             <i class="fa fa-info-circle text-success"></i>
//                 </a>
//             </td>
//             <td class="tdview bank_stmt_amount editable" id="bank_stmt_amount"> <span class="edit-text"></span>
//                 <i class="fa fa-plus plus-icon" data-field="bank_stmt_amount"></i>
//                 <a href="#" class="file-view d-none" target="_blank" data-field="bank_stmt_amount">
//                             <i class="fa fa-info-circle text-success"></i>
//                 </a>
//             </td>
//             <td class="tdview bank_stmt_diff"><span>0</span></td>
//             <td class="tdview neft_mocdoc">${usr.neft.toFixed(2)}</td>

//             <td class="tdview neft_bank editable" id="neft_bank">
//                 <input type="hidden" class="hidden-range" >
//                 <i class="fa fa-calendar calendar-icon" data-field="neft_moc_amt" style="display:none"></i>
//                 <span class="calander-text" style="display:none"></span>
//                 <i class="fa fa-info-circle info-icon" style="display:none"></i>
//                 <span class="tooltip-text" style="display:none"></span>
//                 <span class="edit-text"></span>

//             </td>
//             <td class="tdview neft_diff" ><span>0</span></td>
//             <td>
//                 <button class="edit-btn">Edit</button>
//                 <button class="back-btn" style="display:none">Back</button>
//                 <div class="apply-btn-icon" style="display:none"><i class="fa fa-check"></i></div>
//                 <button class="verify-btn">Verify</button>
//                 <button class="save-btn" style="display:none">Save</button>
//             </td>

//         </tr>
//         `;
//         sno++;
//     });

//     return body;
// }

function ticketData(pageData, body) {
    var sno = 1;
    console.log("ticketData pageData",pageData);

    // Grouping happens here
    const groupedData = groupByLocation(pageData);
    console.log("groupedData",groupedData);

    if (groupedData.length === 0) {
        body += '<tr><td colspan="28" style="text-align:center">No Data</td></tr>';
        return body;
    }

    groupedData.forEach(usr => {

        let billDate = usr.billdate ? moment(usr.billdate).format("DD MMM YYYY") : "-";
        let billformateDate = usr.billdate ? moment(usr.billdate).format("DD/MM/YYYY") : "-";

        body += `
        <tr>
            <td class="tdview">#${sno}</td>
            <td class="tdview bill-date" data-date="${billformateDate}">
                ${billDate}
            </td>
            <td class="tdview location-cell" data-location="${usr.area}">${usr.area}</td>
            <td class="tdview cash_mocdoc">${usr.cash.toFixed(2)}</td>
            <td class="tdview  card_mocdoc">${usr.card.toFixed(2)}</td>
            <td class="tdview upi_mocdoc">${usr.upi.toFixed(2)}</td>
            <td class="tdview total_upi_card">${ (Number(usr.card) + Number(usr.upi)).toFixed(2) }</td>
            <td class="tdview neft_mocdoc">${usr.neft.toFixed(2)}</td>
            <td class="tdview other_mocdoc">${usr.others.toFixed(2)}</td>
            <td class="tdview total_moc">${ (Number(usr.cash) + Number(usr.card) + Number(usr.upi) + Number(usr.neft)).toFixed(2) }</td>
            <td class="tdview date_collection" data-field="upi_date">
                <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
                <div>
                    <span class="selected-date"></span>
                </div>
            </td>
            <td class="tdview collection_amount editable" id="cash_radiant">
                <span class="edit-text"></span>
            </td>
            <td class="tdview date_deposited" data-field="upi_date">
                <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
                <div>
                    <span class="selected-date"></span>
                </div>
            </td>
            <td class="tdview deposite_amount editable">
                <span class="edit-text"></span>
            </td>
            <td class="tdview utr_transcation_ids editable">
                <span class="edit-text_new"></span>
            </td>

            <td class="tdview mespos_card editable" id="cash_radiant">
                <span class="edit-text"></span>
            </td>
            <td class="tdview mespos_upi editable" id="cash_radiant">
                <span class="edit-text"></span>
            </td>
            <td class="tdview date_settlement" data-field="upi_date">
                <i class="fa fa-calendar datepicker calendar-icon1" data-field="cash_moc_amt" style="display:none"></i>
                <div>
                    <span class="selected-date"></span>
                </div>
            </td>
            <td class="tdview bank_chargers editable" id="cash_radiant">
                <span class="edit-text"></span>
            </td>
            <td class="tdview bank_upi_card editable" id="cash_radiant">
                <span class="edit-text"></span>
            </td>
            <td class="tdview bank_neft editable" id="cash_radiant">
                <span class="edit-text"></span>
            </td>
            <td class="tdview bank_others editable" id="cash_radiant">
                <span class="edit-text"></span>
            </td>
            <td class="text-left bank_utr_transcation_ids">
                <span class="utr-text">No UTR</span><br>

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
            <td class="tdview radiant_diff" ><span>0</span></td>
            <td class="tdview cash_diff" ><span>0</span></td>
            <td class="tdview card_upi_diff" ><span>0</span></td>
            <td class="tdview neft_others_diff" ><span>0</span></td>
            <td class="tdview cash_radiant editable" id="cash_radiant">
                <input type="hidden" class="hidden-range" >
                <i class="fa fa-calendar calendar-icon" data-field="cash_moc_amt" style="display:none"></i>
                <span class="calander-text" style="display:none"></span>
                
            </td>
            <td class="tdview file_uploads editable" id="cash_radiant">
                 <i class="fa fa-cloud-upload-alt open-upload-modal text-primary"
                    style="cursor:pointer;font-size:18px;display:none;"
                    title="Upload Documents" >
                    </i>
            </td>
            <td>
                <button class="edit-btn">Edit</button>
                <button class="back-btn" style="display:none">Back</button>
                <div class="apply-btn-icon" style="display:none"><i class="fa fa-check"></i></div>
                <button class="verify-btn">Verify</button>
                <button class="save-btn" style="display:none">Save</button>
            </td>

        </tr>
        `;
        sno++;
    });

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
    upi_radiant: "UPI Radiant Amount",
    upi_bank: "UPI Bank Amount",
    neft_bank: "NEFT Bank Amount",
    bank_stmt_charge: "Bank Statement Charges",
    bank_stmt_amount: "Bank Statement Amount",
};
$(document).on("click", ".edit-btn", function () {
    let tr = $(this).closest("tr");

    tr.find(".editable span").attr("contenteditable", "true")
                             .addClass("editing");

    $(this).hide();
    tr.find(".plus-icon").hide();
    // tr.find(".apply-btn").show();
    tr.find(".calendar-icon").show();
    tr.find(".utr-icon").show();
    tr.find(".calendar-icon1").show();
    tr.find(".open-upload-modal").show();
    tr.find(".save-btn").show();
    tr.find(".verify-btn").hide();
    tr.find(".back-btn").show();
});
$(document).on("input", ".editable .edit-text", function () {

    let span = $(this);
    let td = span.closest(".editable");
    let value = span.text().trim();

    // show + icon only if user typed something
    if (value !== "" && value>0) {
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

    // clear remark
    $(".modal-remark").val("");

    // (optional) always show remark
    $(".remark-wrap").show();
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
    tr.find(".verify-btn").show();
    tr.find(".calendar-icon").hide();
    tr.find(".calendar-icon1").hide();
    tr.find(".open-upload-modal").hide();
    tr.find(".utr-icon").hide();
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
let $clickedIcon = null;
let currentField = null;
let lastRowId = null;
let activeRowIndex = null;   // ✅ important

$(document).on('click', '.calendar-icon', function () {

    $clickedIcon = $(this);
    currentField = $(this).data('field');

    let $tr = $(this).closest('tr');
    activeRowIndex = $tr.index();   // ✅ SET ROW INDEX HERE

    let rowId = $tr.find('.location-cell').data('location') + "_" + activeRowIndex;

    if (rowId !== lastRowId) {
        $('#remark').val('');
        $('#mocFile').val('');
    }

    lastRowId = rowId;
    $('#dateModal').modal('show');
});

let remarkUploads = {};   // ensure declared globally

$("#applyDates").on("click", function () {

    let remark = $("#remark").val().trim();
    let file   = $("#mocFile")[0].files[0] || null;

    if (!remark && !file) {
        alert("Please enter remark or select a file");
        return;
    }

    if (activeRowIndex === null) {
        alert("Row not detected");
        return;
    }

    remarkUploads[activeRowIndex] = {
        remark: remark,
        remark_file: file
    };

    console.log("Stored rowUploads:", remarkUploads);

    $("#dateModal").modal("hide");
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
    formData.append("remark", $('.modal-remark').val());

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
                .attr("href", window.location.origin + "/hms_new/public/radiant_files/" + res.path)
                .attr("data-remark", res.remark || "")
                .attr("data-bs-toggle", "tooltip");



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


$(document).on("keypress", ".editable .edit-text", function (e) {
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
     // new files (before save)
let existingFileStorage = {};

// $(document).on("click", ".save-btn", function () {
//     let btn = $(this);
//     let tr = btn.closest("tr");
//     let rowIndex = tr.index(); // Get row index

//     if (tr.hasClass("saved-row")) {
//         Swal.fire("Info", "Already saved!", "info");
//         return;
//     }

//     // Store row index for file attachment
//     $('#rowUploadModal').data('current-row', rowIndex);

//     /* ================= FORM DATA ================= */
//     let formData = new FormData();
//     formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
//     formData.append("zone_name", $("#izone_views").val());
//     formData.append("location_name", tr.find(".location-cell").data("location"));

//     let dateRange = $('#dateallviews').text().split('-')[0].trim();
//     formData.append("date_range", dateRange);

//     /* ================= NUMERIC VALUES ================= */
//     formData.append("cash_mocdoc", tr.find(".cash_mocdoc").text().trim());
//     formData.append("card_mocdoc", tr.find(".card_mocdoc").text().trim());
//     formData.append("upi_mocdoc", tr.find(".upi_mocdoc").text().trim());
//     formData.append("total_upi_card", tr.find(".total_upi_card").text().trim());
//     formData.append("neft_mocdoc", tr.find(".neft_mocdoc").text().trim());
//     formData.append("other_mocdoc", tr.find(".other_mocdoc").text().trim());
//     formData.append("total_moc", tr.find(".total_moc").text().trim());

//     formData.append("date_collection", tr.find(".date_collection .selected-date").text().trim());
//     formData.append("collection_amount", tr.find(".collection_amount .edit-text").text().trim());

//     formData.append("date_deposited", tr.find(".date_deposited .selected-date").text().trim());
//     formData.append("deposite_amount", tr.find(".deposite_amount .edit-text").text().trim());

//     formData.append("mespos_card", tr.find(".mespos_card .edit-text").text().trim());
//     formData.append("mespos_upi", tr.find(".mespos_upi .edit-text").text().trim());

//     formData.append("date_settlement", tr.find(".date_settlement .selected-date").text().trim());

//     formData.append("bank_chargers", tr.find(".bank_chargers .edit-text").text().trim());
//     formData.append("bank_upi_card", tr.find(".bank_upi_card .edit-text").text().trim());
//     formData.append("bank_neft", tr.find(".bank_neft .edit-text").text().trim());
//     formData.append("bank_others", tr.find(".bank_others .edit-text").text().trim());

//     formData.append("radiant_diff", tr.find(".radiant_diff span").text().trim());
//     formData.append("cash_diff", tr.find(".cash_diff span").text().trim());
//     formData.append("card_upi_diff", tr.find(".card_upi_diff span").text().trim());
//     formData.append("neft_others_diff", tr.find(".neft_others_diff span").text().trim());

//     /* ================= UTR VALUES ================= */
//     let utrIcon = tr.find(".utr-icon")[0];
//     formData.append("cash_utr_number", tr.find(".utr_transcation_ids span").text().trim());
//     formData.append("bank_upi_card_utr", utrIcon?.dataset.upi || "");
//     formData.append("bank_neft_utr", utrIcon?.dataset.neft || "");
//     formData.append("bank_other_utr", utrIcon?.dataset.others || "");

//     /* ================= ATTACH STORED FILES ================= */
//     let hasFiles = false;
    
//     // Get files from storage
//     if (rowFileStorage[rowIndex]) {
//         Object.keys(rowFileStorage[rowIndex]).forEach(fieldName => {
//             const files = rowFileStorage[rowIndex][fieldName];
            
//             if (files && files.length > 0) {
//                 hasFiles = true;
                
//                 // Append each file to FormData
//                 files.forEach((file) => {
//                     formData.append(fieldName, file);
//                 });
                
//                 console.log(`✓ Attached ${files.length} file(s) for ${fieldName}`);
//             }
//         });
//     }
    
//     if (hasFiles) {
//         console.log("✓ Files found and attached to FormData");
//     } else {
//         console.log("⚠ No files to attach");
//     }

//     // Debug: Log all form data entries
//     console.log("=== FORM DATA ENTRIES ===");
//     for (let pair of formData.entries()) {
//         if (pair[1] instanceof File) {
//             console.log(pair[0] + ': ' + pair[1].name + ' (' + pair[1].size + ' bytes)');
//         } else {
//             console.log(pair[0] + ': ' + pair[1]);
//         }
//     }

//     /* ================= AJAX ================= */
//     $.ajax({
//         url: incomestore,
//         type: "POST",
//         data: formData,
//         processData: false,
//         contentType: false,
//         success: function (res) {

//             if (res.status !== 200) {
//                 Swal.fire("Error", "Save failed", "error");
//                 return;
//             }

//             btn.prop("disabled", true).text("Saved");
//             tr.addClass("saved-row");

//             /* ================= CLICK TO PREVIEW ================= */

//             bindPreview(tr, ".collection_amount", res.files?.collection_amount);
//             bindPreview(tr, ".deposite_amount", res.files?.deposit_amount);
//             bindPreview(tr, ".mespos_card", res.files?.mespos_card);
//             bindPreview(tr, ".mespos_upi", res.files?.mespos_upi);
//             bindPreview(tr, ".bank_upi_card", res.files?.bank_upi_card);
//             bindPreview(tr, ".bank_neft", res.files?.bank_neft);
//             bindPreview(tr, ".bank_others", res.files?.bank_others);
//             bindPreview(tr, ".utr_transcation_ids ", res.files?.cash_utr);

//             bindPreview(tr, ".bank_utr_transcation_ids", [
//                 ...(res.files?.card_upi_utr || []),
//                 ...(res.files?.neft_utr || []),
//                 ...(res.files?.other_utr || [])
//             ]);

//             /* ================= CLEANUP ================= */
//             delete rowFileStorage[rowIndex];
//             $('#rowUploadModal input[type="file"]').val('');
//             $('.preview-box').empty();

//             Swal.fire("Success", "Saved successfully!", "success");
//         },

//         error: function (xhr) {
//             Swal.fire("Error", "Server error", "error");
//             console.error(xhr.responseText);
//         }
//     });
// });


function loadRadiantValues(tr) {

    let zone = $("#izone_views").val();
    let location = tr.find(".location-cell").data("location");
    if (!location) return;

    let date = tr.find(".bill-date").data("date");
    let rowIndex = tr.index();

    $.ajax({
        url: incomeradiantfetch,
        type: "GET",
        data: {
            zone_name: zone,
            location_name: location,
            date_range: date
        },
        success: function (res) {

            if (res.status !== 200 || !res.data) return;
            let d = res.data;

            /* ================= ⭐ STORE EXISTING FILES (ADDED) ================= */
            existingFileStorage[rowIndex] = {
                collection_amount_files: d.collection_amount_files ? JSON.parse(d.collection_amount_files) : [],
                deposit_amount_files: d.deposit_amount_files ? JSON.parse(d.deposit_amount_files) : [],
                mespos_card_files: d.mespos_card_files ? JSON.parse(d.mespos_card_files) : [],
                mespos_upi_files: d.mespos_upi_files ? JSON.parse(d.mespos_upi_files) : [],
                bank_upi_card_files: d.bank_upi_card_files ? JSON.parse(d.bank_upi_card_files) : [],
                bank_neft_files: d.bank_neft_files ? JSON.parse(d.bank_neft_files) : [],
                bank_others_files: d.bank_others_files ? JSON.parse(d.bank_others_files) : [],
                cash_utr_files: d.cash_utr_files ? JSON.parse(d.cash_utr_files) : [],
                card_upi_utr_files: d.card_upi_utr_files ? JSON.parse(d.card_upi_utr_files) : [],
                neft_utr_files: d.neft_utr_files ? JSON.parse(d.neft_utr_files) : [],
                other_utr_files: d.other_utr_files ? JSON.parse(d.other_utr_files) : []
            };

            /* ================= YOUR ORIGINAL VALUE SETS ================= */
            tr.find(".collection_amount .edit-text").text(d.collection_amount || "");
            tr.find(".deposite_amount .edit-text").text(d.deposite_amount || "");
            tr.find(".mespos_card .edit-text").text(d.mespos_card || "");
            tr.find(".mespos_upi .edit-text").text(d.mespos_upi || "");
            tr.find(".bank_upi_card .edit-text").text(d.bank_upi_card || "");
            tr.find(".bank_neft .edit-text").text(d.bank_neft || "");
            tr.find(".bank_others .edit-text").text(d.bank_others || "");

            calculateRow(tr);
        }
    });
}



/* ================= HELPER ================= */
function bindPreview(tr, selector, files) {

    let td = tr.find(selector);

    if (!files || !files.length) return;

    td
      .addClass("text-primary")
      .css({ cursor: "pointer", textDecoration: "underline" })
      .off("click.preview")
      .on("click.preview", function () {
          openFilePreview(files);
      });
}
function loadRadiantValues(tr) {

    let zone     = $("#izone_views").val();
    let location = tr.find(".location-cell").data("location");
    if (!location) return;

    let date = tr.find('.bill-date').data('date');
    let rowIndex = tr.index(); // ⭐ added

    $.ajax({
        url: incomeradiantfetch,
        type: "GET",
        data: {
            zone_name: zone,
            location_name: location,
            date_range: date
        },
        success: function (res) {

            if (res.status !== 200 || !res.data) return;
            let d = res.data;

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

            /* ================= HELPERS ================= */
            const clean = v => Number(String(v).replace(/[^0-9.-]/g, '')) || 0;

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

            /* ================= MOC VALUES ================= */
            tr.find(".cash_mocdoc").text(clean(d.moc_cash_amt));
            tr.find(".card_mocdoc").text(clean(d.moc_card_amt));
            tr.find(".upi_mocdoc").text(clean(d.moc_upi_amt));
            tr.find(".total_upi_card").text(clean(d.moc_total_upi_card));
            tr.find(".neft_mocdoc").text(clean(d.moc_neft_amt));
            tr.find(".other_mocdoc").text(clean(d.moc_other_amt));
            tr.find(".total_moc").text(clean(d.moc_overall_total));

            /* ================= DATES & AMOUNTS ================= */
            if (d.date_collection)
                tr.find(".date_collection .selected-date").text(d.date_collection);

            if (d.collection_amount)
                tr.find(".collection_amount .edit-text").text(d.collection_amount);

            if (d.date_deposited)
                tr.find(".date_deposited .selected-date").text(d.date_deposited);

            if (d.deposite_amount)
                tr.find(".deposite_amount .edit-text").text(d.deposite_amount);

            if (d.date_settlement)
                tr.find(".date_settlement .selected-date").text(d.date_settlement);

            /* ================= MESPOS ================= */
            tr.find(".mespos_card .edit-text").text(clean(d.mespos_card));
            tr.find(".mespos_upi .edit-text").text(clean(d.mespos_upi));

            /* ================= BANK ================= */
            tr.find(".bank_chargers .edit-text").text(clean(d.bank_chargers));
            tr.find(".bank_upi_card .edit-text").text(clean(d.bank_upi_card));
            tr.find(".bank_neft .edit-text").text(clean(d.bank_neft));
            tr.find(".bank_others .edit-text").text(clean(d.bank_others));

            /* ================= CLICK → FILE PREVIEW ================= */
            bindClickPreview(tr.find(".collection_amount"), d.collection_amount_files);
            bindClickPreview(tr.find(".deposite_amount"), d.deposit_amount_files);
            bindClickPreview(tr.find(".mespos_card"), d.mespos_card_files);
            bindClickPreview(tr.find(".mespos_upi"), d.mespos_upi_files);
            bindClickPreview(tr.find(".bank_upi_card"), d.bank_upi_card_files);
            bindClickPreview(tr.find(".bank_neft"), d.bank_neft_files);
            bindClickPreview(tr.find(".bank_others"), d.bank_others_files);
            bindClickPreview(tr.find(".utr_transcation_ids"), d.cash_utr_files);

            bindClickPreview(
                tr.find(".bank_utr_transcation_ids"),
                JSON.stringify([
                    ...(d.card_upi_utr_files ? JSON.parse(d.card_upi_utr_files) : []),
                    ...(d.neft_utr_files ? JSON.parse(d.neft_utr_files) : []),
                    ...(d.other_utr_files ? JSON.parse(d.other_utr_files) : [])
                ])
            );

            /* ================= UTR TEXT ================= */
            tr.find(".utr_transcation_ids .edit-text_new")
                .text(d.cash_utr_number || "-");

            /* ================= RECALC ================= */
            calculateRow(tr);

            /* ================= BUTTON STATE ================= */
            if (d.cash_radiant === 1) {
                tr.find(".edit-btn").hide();
                tr.find(".apply-btn-icon").show();
            } else {
                tr.find(".edit-btn").show();
                tr.find(".apply-btn-icon").hide();
            }
        },
        error: function (xhr, status, error) {
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

    let modal = $("#filePreviewModal");
    let body  = $("#filePreviewBody"); // ✅ EXISTING CONTAINER

    body.empty();

    // remark text
    modal.find(".remark-text").text(remark || "");

    files.forEach(file => {

        let fileUrl = window.APP_BASE_URL + '/' + file.replace(/^\/+/, "");
        console.log("fileUrl",fileUrl);
        
        let ext = file.split(".").pop().toLowerCase();

        let html = "";

        if (["jpg", "jpeg", "png", "webp"].includes(ext)) {
            html = `
                <img src="${fileUrl}"
                     style="max-width:100%;max-height:500px;display:block;margin:auto;">
            `;
        }
        else if (ext === "pdf") {
            html = `
                <embed src="${fileUrl}"
                       type="application/pdf"
                       width="100%"
                       height="600px">
            `;
        }
        else {
            html = `
                <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                    Download ${file.split('/').pop()}
                </a>
            `;
        }
        console.log("html",html);
        
        body.append(`
            <div class="mb-3 border rounded p-2">
                ${html}
            </div>
        `);
    });

    // download button (downloads first file)
    $("#downloadFileBtn")
        .off("click")
        .on("click", function () {
            window.open("/" + files[0], "_blank");
        });

    modal.modal("show");
}


function populateExistingFilesInModal(rowIndex) {
    console.log("rowIndex",rowIndex);
    
    // clear old previews
    $(".preview-box").empty();

    let files = existingFileStorage[rowIndex];
    if (!files) return;

    const map = {
        collection_amount_files : "#cash_preview",
        deposit_amount_files    : "#deposit_preview",
        mespos_card_files       : "#mespos_card_preview",
        mespos_upi_files        : "#mespos_upi_preview",
        bank_chargers_files     : "#bank_charges_preview",
        bank_upi_card_files     : "#bank_upi_card_preview",
        bank_neft_files         : "#bank_neft_preview",
        bank_others_files       : "#bank_others_preview",
        cash_utr_files          : "#utr_cash_preview",
        card_upi_utr_files      : "#utr_card_upi_preview",
        neft_utr_files          : "#utr_neft_preview",
        other_utr_files         : "#utr_others_preview"
    };

    Object.keys(map).forEach(key => {

        if (!files[key] || !files[key].length) return;

        let previewBox = $(map[key]);

        files[key].forEach(path => {

            let fileName = path.split("/").pop();

            let item = $(`
                <div class="existing-file d-flex mb-1">
                    <span class="text-primary" style="cursor:pointer">${fileName}</span>
                    <i class="fa fa-eye text-success ms-2" style="cursor:pointer"></i>
                </div>
            `);

            // click name OR eye → preview
            item.find("span, i").on("click", function () {
                openFilePreview([path]);
            });

            previewBox.append(item);
        });
    });
}




// preview js
let previewModal = new bootstrap.Modal(
    document.getElementById("filePreviewModal")
);

$(document).on("click", ".file-view", function (e) {
    e.preventDefault();
    $('.custom-tooltip').hide();
    let fileUrl = $(this).attr("href");
    let remark = $(this).data("remark");
    let ext = fileUrl.split('.').pop().toLowerCase();

    let field = $(this).data("field");
    let titleText = fieldLabels[field] || "File Preview";

    // ✅ set dynamic heading
    $("#filePreviewModal .modal-title").text(titleText);

    let previewBody = $("#filePreviewBody");
    let downloadBtn = $("#downloadFileBtn");

    previewBody.html("");
    downloadBtn.attr("href", fileUrl);
    $(".remark_value").text(remark);

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
$(document).on("mouseenter", ".file-view", function(e){

    let remark = $(this).data("remark") || "";

    if(!remark) return;

    $("body").append('<div class="tooltip-box">'+remark+'</div>');

    $(".tooltip-box")
        .css({ top:e.pageY+10, left:e.pageX+10 })
        .fadeIn(100);
});


$(document).on("mousemove", ".file-view", function(e){
    $(".tooltip-box").css({ top:e.pageY+10, left:e.pageX+10 });
});

$(document).on("mouseleave", ".file-view", function(){
    $(".tooltip-box").remove();
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

$(document).on('change', '.datepicker', function () {
    const date = $(this).val(); // dd/MM/yyyy
    const $td = $(this).closest('td');

    console.log('Selected Date:', date);
});

function initDatePickers() {
    document.querySelectorAll('.datepicker').forEach(input => {
        if (!input._flatpickr) {
            flatpickr(input, {
                dateFormat: "d/m/Y",
                allowInput: true,
                maxDate: "today",

                onChange: function (selectedDates, dateStr, instance) {
                    const inputEl = instance.element;

                    // 🔥 find SAME TD
                    const td = inputEl.closest('td');
                    const span = td.querySelector('.selected-date');

                    span.textContent = dateStr; // set selected date

                    console.log("Selected Date:", dateStr);
                }
            });
        }
    });
}
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
let currentUtrIcon = null;

// Open modal
$(document).on('click', '.utr-icon', function () {
    currentUtrIcon = this;

    $('#upi_utr').val(this.dataset.upi || '');
    $('#neft_utr').val(this.dataset.neft || '');
    $('#others_utr').val(this.dataset.others || '');

    $('#utrModal').modal('show');
});
// ---------- SAVE UTR & UPDATE UI ----------
$(document).on('click', '#saveUtrBtn', function () {

    if (!currentUtrIcon) return;

    // Get values from modal
    const upi    = $('#upi_utr').val().trim();
    const neft   = $('#neft_utr').val().trim();
    const others = $('#others_utr').val().trim();

    // Save values into icon dataset (SOURCE OF TRUTH)
    currentUtrIcon.dataset.upi    = upi;
    currentUtrIcon.dataset.neft   = neft;
    currentUtrIcon.dataset.others = others;

    // Find parent TD
    let td = $(currentUtrIcon).closest('td');

    // ---------- UPDATE SPAN TEXT (MULTI-LINE, NO HIDE) ----------
    let displayHtml = `
        <b>UPI / CARD:</b> ${upi || '-'}<br>
        <b>NEFT:</b> ${neft || '-'}<br>
        <b>OTHERS:</b> ${others || '-'}
    `;

    td.find('.utr-text')
        .html(displayHtml)
        .removeClass('text-muted')
        .addClass('text-dark');

    // ---------- UPDATE TOOLTIP ----------
    let tooltipHtml = `
        <b>UPI / CARD:</b> ${upi || '-'}<br>
        <b>NEFT:</b> ${neft || '-'}<br>
        <b>OTHERS:</b> ${others || '-'}
    `;

    currentUtrIcon.setAttribute('title', tooltipHtml);

    // ---------- SHOW ICON + COLOR ----------
    if (upi || neft || others) {
        currentUtrIcon.style.display = 'inline-block';
        currentUtrIcon.classList.remove('text-primary');
        currentUtrIcon.classList.add('text-success');
    } else {
        currentUtrIcon.style.display = 'none';
        currentUtrIcon.classList.remove('text-success');
        currentUtrIcon.classList.add('text-primary');
    }

    // ---------- RE-INIT BOOTSTRAP TOOLTIP ----------
    if ($(currentUtrIcon).data('bs.tooltip')) {
        $(currentUtrIcon).tooltip('dispose');
    }
    $(currentUtrIcon).tooltip({
        html: true,
        placement: 'top'
    });

    // Close modal
    $('#utrModal').modal('hide');
});


let rowFileStorage = {};

$(document).ready(function() {
    
    // ============================================
    // 1. OPEN UPLOAD MODAL
    // ============================================
    $(document).on("click", ".open-upload-modal", function () {

        let tr = $(this).closest("tr");
        let rowIndex = tr.index();

        $("#rowUploadModal").data("current-row", rowIndex);

        populateExistingFilesInModal(rowIndex);

        $("#rowUploadModal").modal("show");
    });
    
    // ============================================
    // 2. FILE INPUT CHANGE - PREVIEW & STORE
    // ============================================
    $(document).on('change', '.upload-input', function() {
        const $input = $(this);
        const files = this.files;
        const previewId = $input.data('preview');
        const fieldName = $input.attr('name');
        const rowIndex = $('#rowUploadModal').data('current-row');
        
        if (files.length === 0) return;
        
        // Initialize storage for this row if not exists
        if (!rowFileStorage[rowIndex]) {
            rowFileStorage[rowIndex] = {};
        }
        
        // Store files in memory
        if (!rowFileStorage[rowIndex][fieldName]) {
            rowFileStorage[rowIndex][fieldName] = [];
        }
        
        // Add new files to storage
        Array.from(files).forEach(file => {
            rowFileStorage[rowIndex][fieldName].push(file);
        });
        
        // Show preview
        showFilePreview(previewId, rowFileStorage[rowIndex][fieldName], fieldName, rowIndex);
    });
    
    // ============================================
    // 3. SHOW FILE PREVIEW
    // ============================================
    function showFilePreview(previewId, files, fieldName, rowIndex) {

        const $preview = $(`#${previewId}`);
        $preview.empty();

        if (!files || files.length === 0) return;

        files.forEach((file, index) => {

            const fileExt = file.name.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);

            if (isImage) {

                const reader = new FileReader();
                reader.onload = function (e) {

                    const imgHtml = `
                        <div class="preview-item" data-index="${index}">
                            <img src="${e.target.result}" alt="${file.name}">
                            <div class="preview-overlay">
                                <span class="file-name" title="${file.name}">
                                    ${file.name}
                                </span>
                                <button class="btn-remove-file"
                                        data-field="${fieldName}"
                                        data-index="${index}">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    $preview.append(imgHtml);
                };
                reader.readAsDataURL(file);

            } else {

                const fileHtml = `
                    <div class="preview-item file-item d-flex flex-column justify-content-center align-items-center"
                        data-index="${index}"
                        style="cursor:pointer">

                        <i class="fa fa-file-${getFileIcon(fileExt)} fa-2x text-primary"></i>
                        <small class="mt-1 text-truncate" title="${file.name}">
                            ${file.name}
                        </small>

                        <button class="btn-remove-file position-absolute top-0 end-0 m-1"
                                data-field="${fieldName}"
                                data-index="${index}">
                            <i class="fa fa-times text-danger"></i>
                        </button>
                    </div>
                `;

                const $el = $(fileHtml);

                // 🔥 CLICK FILE → OPEN IN NEW TAB
                $el.on("click", function (e) {
                    if ($(e.target).closest(".btn-remove-file").length) return;

                    const blobUrl = URL.createObjectURL(file);
                    window.open(blobUrl, "_blank");
                });

                $preview.append($el);
            }
        });
    }

    
    // ============================================
    // 4. REMOVE FILE FROM PREVIEW
    // ============================================
    $(document).on('click', '.btn-remove-file', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $btn = $(this);
        const fieldName = $btn.data('field');
        const fileIndex = $btn.data('index');
        const rowIndex = $('#rowUploadModal').data('current-row');
        
        // Remove from storage
        if (rowFileStorage[rowIndex] && rowFileStorage[rowIndex][fieldName]) {
            rowFileStorage[rowIndex][fieldName].splice(fileIndex, 1);
            
            // If no files left, remove the field
            if (rowFileStorage[rowIndex][fieldName].length === 0) {
                delete rowFileStorage[rowIndex][fieldName];
            }
        }
        
        // Remove preview item
        $btn.closest('.preview-item').remove();
    });
    
    // ============================================
    // 5. SAVE UPLOADS BUTTON
    // ============================================
    $(document).on('click', '#saveUploadsfile', function() {
        const rowIndex = $('#rowUploadModal').data('current-row');
        
        // Check if any files are uploaded
        const hasFiles = rowFileStorage[rowIndex] && 
                        Object.keys(rowFileStorage[rowIndex]).length > 0;
        
        if (!hasFiles) {
            Swal.fire('Info', 'No files uploaded', 'info');
            return;
        }
        
        // Close modal
        $('#rowUploadModal').modal('hide');
        
        // Show success message
        const fileCount = Object.values(rowFileStorage[rowIndex])
            .reduce((sum, files) => sum + files.length, 0);
        
        Swal.fire('Success', `${fileCount} file(s) ready to upload`, 'success');
        
        // Add visual indicator on the row
        const $tr = $('table tbody tr').eq(rowIndex);
        $tr.find('.open-upload-modal')
           .removeClass('text-primary')
           .addClass('text-success')
           .attr('title', `${fileCount} file(s) attached`);
    });
    
    // ============================================
    // 6. ATTACH FILES TO FORMDATA (Called from save-btn)
    // ============================================
    window.attachFilesToFormData = function(formData) {
        const rowIndex = $('#rowUploadModal').data('current-row') || 
                        $('.save-btn').closest('tr').index();
        
        if (!rowFileStorage[rowIndex]) {
            return false;
        }
        
        let hasFiles = false;
        
        // Attach all files to FormData
        Object.keys(rowFileStorage[rowIndex]).forEach(fieldName => {
            const files = rowFileStorage[rowIndex][fieldName];
            
            if (files && files.length > 0) {
                hasFiles = true;
                
                // Append each file
                files.forEach((file, index) => {
                    formData.append(fieldName, file);
                });
                
                console.log(`Attached ${files.length} file(s) for ${fieldName}`);
            }
        });
        
        return hasFiles;
    };
    
    // ============================================
    // 7. CLEAR STORED FILES (Called after successful save)
    // ============================================
    window.clearStoredFiles = function() {
        const rowIndex = $('#rowUploadModal').data('current-row') || 
                        $('.save-btn').closest('tr').index();
        
        if (rowFileStorage[rowIndex]) {
            delete rowFileStorage[rowIndex];
        }
        
        // Clear all previews
        $('.preview-box').empty();
        
        // Reset file inputs
        $('#rowUploadModal input[type="file"]').val('');
        
        console.log('Cleared stored files for row', rowIndex);
    };
    
    // ============================================
    // 8. LOAD EXISTING FILE PREVIEWS
    // ============================================
    function loadExistingFilePreviews(rowIndex) {
        if (!rowFileStorage[rowIndex]) return;
        
        Object.keys(rowFileStorage[rowIndex]).forEach(fieldName => {
            const files = rowFileStorage[rowIndex][fieldName];
            const $input = $(`input[name="${fieldName}"]`);
            const previewId = $input.data('preview');
            
            if (files && files.length > 0) {
                showFilePreview(previewId, files, fieldName, rowIndex);
            }
        });
    }
    
    // ============================================
    // HELPER FUNCTIONS
    // ============================================
    function getFileIcon(ext) {
        const icons = {
            'pdf': 'pdf',
            'doc': 'word',
            'docx': 'word',
            'xls': 'excel',
            'xlsx': 'excel',
            'txt': 'alt',
            'zip': 'archive',
            'rar': 'archive'
        };
        return icons[ext] || 'alt';
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    // ============================================
    // MODAL CLOSE - DON'T CLEAR FILES
    // ============================================
    $('#rowUploadModal').on('hidden.bs.modal', function() {
        // Don't clear files when modal closes
        // Files are only cleared after successful save
    });
    
});


});


