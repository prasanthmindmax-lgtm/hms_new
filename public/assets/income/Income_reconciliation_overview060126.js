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
        $('#total_cash').text(parseFloat(consolidatedRow.cash || 0).toFixed(2));
        $('#total_card').text(parseFloat(consolidatedRow.card || 0).toFixed(2));
        $('#total_neft').text(parseFloat(consolidatedRow.neft || 0).toFixed(2));
        $('#total_upi').text(parseFloat(consolidatedRow.upi || 0).toFixed(2));
        $('#total_amount').text(parseFloat(consolidatedRow.total || 0).toFixed(2));
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
    $("#today_visits").text(totalItems);
    $("#dcounts").text(totalItems);
    // 🔥 RELOAD radiant values for every row
$("#daily_details_recon tr").each(function () {
    loadRadiantValues($(this));
});
}
// Also call loadRadiantValues() when user filters
$(document).on("change", "#izone_views, #ibranch_views, #reportrange", function() {
    $("#daily_details_recon tr").each(function () {
        loadRadiantValues($(this));
    });
});

function ticketData(pageData, body) {
    var sno = 1;

    if (pageData.length === 0) {
        body += '<tr><td colspan="22" style="text-align:center">No Data</td></tr>';
        return body;
    }

    pageData.forEach(usr => {
        if (usr.type === "Consolidated") return;
        body += `
        <tr>
            <td class="tdview">#${sno}</td>
            <td class="tdview date-cell" data-date="${usr.date_range}">${usr.date_range}</td>
            <td class="tdview location-cell">${usr.zone_name}</td>
            <td class="tdview location-cell" data-location="${usr.location_name}">${usr.location_name}</td>
            <td class="tdview cash_mocdoc">${usr.cash_moc_amt}</td>
            <td class="tdview cash_radiant editable">
                <input type="hidden" class="hidden-range" >
                <i class="fa fa-calendar calendar-icon disabled" data-field="cash_moc_amt" style="display:none"></i>
                <span class="calander-text" style="display:none">${usr.cash_date_filter}</span>
                <i class="fa fa-info-circle info-icon" style="display:none"></i>
                <span class="tooltip-text" style="display:none">${usr.cash_date_amt_filter}</span>
                <span class="edit-text">${usr.cash_radiant}</span>
                <i class="fa fa-plus plus-icon" data-field="cash_radiant"></i>
                <!-- view -->
                <a href="#" class="file-view d-none" target="_blank"data-field="cash_radiant">
                        <i class="fa fa-info-circle text-success"></i>
                </a>
            </td>
            <td class="tdview cash_diff"><span>0</span></td>

            <td class="tdview cash_bank editable">
                <span class="edit-text">${usr.cash_bank}</span>
                <i class="fa fa-plus plus-icon" data-field="cash_bank"></i>
                <!-- view -->
                <a href="#" class="file-view d-none" target="_blank"data-field="cash_bank">
                        <i class="fa fa-info-circle text-success"></i>
                </a>
            </td>
            <td class="tdview cash_bank_diff"><span>0</span></td>
            <td class="tdview  card_mocdoc">${usr.card_moc_amt}</td>
            <td class="tdview card_radiant editable">
                <input type="hidden" class="hidden-range" >
                <i class="fa fa-calendar calendar-icon disabled" data-field="card_moc_amt" style="display:none"></i>
                <span class="calander-text" style="display:none">${usr.card_moc_amt}</span>
                <i class="fa fa-info-circle info-icon" style="display:none"></i>
                <span class="tooltip-text" style="display:none">${usr.card_date_amt_filter}</span>
                <span class="edit-text">${usr.card_radiant}</span>
                <i class="fa fa-plus plus-icon" data-field="card_radiant"></i>
                <!-- view -->
                <a href="#" class="file-view d-none" target="_blank"data-field="card_radiant">
                        <i class="fa fa-info-circle text-success"></i>
                </a>
            </td>
            <td class="tdview card_diff"><span>0</span></td>


            <td class="tdview upi_mocdoc">${usr.upi_moc_amt}</td>
            <td class="tdview upi_radiant editable">
                <input type="hidden" class="hidden-range" >
                <i class="fa fa-calendar calendar-icon disabled" data-field="upi_moc_amt" style="display:none"></i>
                <span class="calander-text" style="display:none">${usr.upi_date_filter}</span>
                <i class="fa fa-info-circle info-icon" style="display:none"></i>
                <span class="tooltip-text" style="display:none">${usr.upi_date_amt_filter}</span>
                <span class="edit-text">${usr.upi_radiant}</span>
                <i class="fa fa-plus plus-icon" data-field="upi_radiant"></i>
                <!-- view -->
                <a href="#" class="file-view d-none" target="_blank" data-field="upi_radiant">
                        <i class="fa fa-info-circle text-success"></i>
                </a>
            </td>
            <td class="tdview upi_diff" ><span>0</span></td>
            <td class="tdview bank_stmt_charge editable"> <span class="edit-text">${usr.bank_stmt_charge}</span>
                <i class="fa fa-plus plus-icon" data-field="bank_stmt_charge"></i>
                <a href="#" class="file-view d-none" target="_blank" data-field="bank_stmt_charge">
                            <i class="fa fa-info-circle text-success"></i>
                </a>
            </td>
            <td class="tdview bank_stmt_amount editable"> <span class="edit-text">${usr.bank_stmt_amount}</span>
                <i class="fa fa-plus plus-icon" data-field="bank_stmt_amount"></i>
                <a href="#" class="file-view d-none" target="_blank" data-field="bank_stmt_amount">
                            <i class="fa fa-info-circle text-success"></i>
                </a>
            </td>
            <td class="tdview bank_stmt_diff"><span>0</span></td>
            <td class="tdview">${usr.neft_moc_amt}</td>

            <td class="tdview neft_bank editable">
                <input type="hidden" class="hidden-range" >
                <i class="fa fa-calendar calendar-icon disabled" data-field="neft_moc_amt" style="display:none"></i>
                <span class="calander-text" style="display:none">${usr.neft_date_filter}</span>
                <i class="fa fa-info-circle info-icon" style="display:none"></i>
                <span class="tooltip-text" style="display:none">${usr.neft_date_amt_filter}</span>
                <span class="edit-text">${usr.neft_bank}</span>
                <i class="fa fa-plus plus-icon" data-field="neft_bank"></i>
                    <!-- view -->
                <a href="#" class="file-view d-none" target="_blank"data-field="neft_bank">
                    <i class="fa fa-info-circle text-success"></i>

                </a>
            </td>
            <td class="tdview neft_diff" ><span>0</span></td>

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
$(document).on("input keyup", ".editable span", function () {

    let tr   = $(this).closest("tr");
    let cell = $(this).closest("td");
    const toNum = v => parseFloat(v) || 0;

    let cls = cell.attr("class");

    /* ---------------- CASH ---------------- */
    if (cls.includes("cash_radiant")) {
        let moc = toNum(cell.find(".tooltip-text").text());
        let rad = toNum(cell.find(".edit-text").text());
        tr.find(".cash_diff span").text((moc - rad).toFixed(2));
    }

    if (cls.includes("cash_bank")) {
        let bank = toNum(cell.find(".edit-text").text());
        let rad  = toNum(tr.find(".cash_radiant .edit-text").text());
        tr.find(".cash_bank_diff span").text((bank - rad).toFixed(2));
    }

    /* ---------------- CARD ---------------- */
    if (cls.includes("card_radiant")) {
        let moc = toNum(cell.find(".tooltip-text").text());
        let rad = toNum(cell.find(".edit-text").text());
        tr.find(".card_diff span").text((moc - rad).toFixed(2));
    }

    /* ---------------- UPI ---------------- */
    if (cls.includes("upi_radiant")) {
        let moc = toNum(cell.find(".tooltip-text").text());
        let rad = toNum(cell.find(".edit-text").text());
        tr.find(".upi_diff span").text((moc - rad).toFixed(2));
    }

    /* ---------------- CALCULATE BANK STATEMENT DIFFERENCE ---------------- */
    // Always calculate when any of these fields change
    if (cls.includes("card_radiant") ||
        cls.includes("upi_radiant") ||
        cls.includes("bank_stmt_charge") ||
        cls.includes("bank_stmt_amount")) {

        // Get values
        let card_rad = toNum(tr.find(".card_radiant .edit-text").text());
        let upi_rad = toNum(tr.find(".upi_radiant .edit-text").text());
        let charge = toNum(tr.find(".bank_stmt_charge .edit-text").text());
        let bank_stmt_amt = toNum(tr.find(".bank_stmt_amount .edit-text").text());

        // Calculate: (Card Radiant + UPI Radiant - Charge) - Bank Statement Amount
        let expected = (card_rad + upi_rad) - charge;
        let difference = expected - bank_stmt_amt;

        // Update difference
        tr.find(".bank_stmt_diff span").text(difference.toFixed(2));
    }

    /* ---------------- NEFT ---------------- */
    if (cls.includes("neft_bank")) {
        alert(12);
        let moc = toNum(cell.find(".tooltip-text").text());
        let bank = toNum(cell.find(".edit-text").text());
        console.log("moc",moc);
        console.log("bank",bank);

        tr.find(".neft_diff span").text((moc - bank).toFixed(2));
    }
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
function loadRadiantValues(tr) {
    let zone = $("#izone_views").val();
    console.log("zone",zone);
    let location = $(tr).find('td.location-cell[data-location]').data('location');
    let firstDate = $(tr).find('td.date-cell[data-date]').data('date');
    console.log("location",location);
    console.log("firstDate",firstDate);

    if (!location) return;

    // let dummy = $('#dateallviews').text();
    // let firstDate = dummy.split('-')[0].trim();
    // console.log("firstDate",firstDate);

    $.ajax({
        url: incomeradiantfetch,
        type: "GET",
        data: {
            zone_name: zone,
            location_name: location,
            date_range: firstDate
        },

        success:function(res){

            console.log("radiant fetch",res);

            let d = res.data;
            if(d !== null){

                // helpers
                const clean  = v => Number(String(v).replace(/[^0-9.-]/g,'')) || 0;
                const format = v => "₹" + Number(v).toLocaleString();


                // -------- UPDATE ONE CELL --------
                function updateBlock(cls, radiant, date, amt,column) {
                    let td = tr.find("." + cls);

                    td.find(".edit-text").text(clean(radiant));
                    td.find(".tooltip-text").text(clean(amt));
                    td.find(".calander-text").text(date || "-");

                    td.find(".custom-tooltip").remove();
                    td.append(`<div class="custom-tooltip">Loading...</div>`); // temporary loading

                    td.find(".info-icon").show();
                    // td.find(".calendar-icon").show();

                    if (date && date !== "-") {
                        let dates = date.split(" - ");
                        let fromDate = dates[0].trim();
                        let toDate = dates[1] ? dates[1].trim() : fromDate;
                        let branch_name = $(tr).find('td.location-cell[data-location]').data('location');   // adjust if your tr has branch info
                        if(column !==''){
                            $.ajax({
                                url: incomedatecheck,
                                type: "POST",
                                data: {
                                    branch_name: branch_name,
                                    from_date  : fromDate,
                                    to_date    : toDate,
                                    column     : column,
                                    _token     : $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(res) {
                                    // Directly send the response to buildTooltip
                                    buildTooltip(td.find('.info-icon'), res, date);
                                },
                                error: function() {
                                    td.find('.custom-tooltip').text("Error fetching data!");
                                }
                            });
                        }
                    } else {
                        // If no date, show static tooltip
                        td.find('.custom-tooltip').html(`
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
                        `);
                    }
                    td.find(".calendar-icon").hide();
                }



                // -------- APPLY --------
                updateBlock("cash_radiant", d.cash_radiant, d.cash_date_filter, d.cash_date_amt_filter,'cash_moc_amt');
                updateBlock("cash_bank"   , d.cash_bank   , d.cash_date_filter, d.cash_bank_amt_filter,'');

                updateBlock("card_radiant", d.card_radiant, d.card_date_filter, d.card_date_amt_filter,'card_moc_amt');
                updateBlock("card_bank"   , d.card_bank   , d.card_date_filter, d.card_bank_amt_filter,'');

                updateBlock("upi_radiant" , d.upi_radiant , d.upi_date_filter , d.upi_date_amt_filter,'upi_moc_amt');
                updateBlock("upi_bank"    , d.upi_bank    , d.upi_date_filter , d.upi_bank_amt_filter,'');

                updateBlock("neft_bank"   , d.neft_bank   , d.neft_date_filter, d.neft_date_amt_filter,'neft_moc_amt');


                // -------- FILE LINKS --------
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

                    let filePath = d[fileMap[field]];
                    let td = tr.find(`.${field}`);

                    if(filePath){

                        td.find(".plus-icon").hide();

                        td.find(".file-view")
                            .removeClass("d-none")
                            .attr(
                                "href",
                                window.location.origin +
                                "/hms/public/radiant_files/" +
                                filePath
                            );
                    }
                });

                tr.find(".bank_stmt_charge .edit-text").text(clean(d.bank_stmt_charge));
                tr.find(".bank_stmt_amount .edit-text").text(clean(d.bank_stmt_amount));

                // If you want to update the diff column too
                let diff = clean(d.bank_stmt_amount) - clean(d.bank_stmt_charge);
                tr.find(".bank_stmt_diff span").text(format(diff));
                // -------- RECALC --------
                calculateRow(tr);


                // -------- BUTTONS --------
                let allFilled =
                    d.cash_radiant && d.cash_bank &&
                    d.card_radiant && d.bank_stmt_charge &&
                    d.upi_radiant  && d.bank_stmt_amount  &&
                    d.neft_bank;
                console.log("allFilled",allFilled);

                if(allFilled){
                    tr.find(".edit-btn").hide();
                    tr.find(".apply-btn-icon").show();
                } else {
                    tr.find(".edit-btn").show();
                    tr.find(".apply-btn-icon").hide();
                }
            }
        }
    });
}

// preview js
let previewModal = new bootstrap.Modal(
    document.getElementById("filePreviewModal")
);

$(document).on("click", ".file-view", function (e) {
    e.preventDefault();

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
let $clickedIcon = null;
let currentField = null;

// ---- 1️⃣ Calendar icon click ----
$(document).on('click', '.calendar-icon', function () {

    $clickedIcon = $(this);
    currentField = $(this).data('field');

    // clear modal inputs
    $('#fromDate').val('');
    $('#toDate').val('');

    $('#dateModal').modal('show');
});

// ---- 2️⃣ Apply Dates ----
$('#applyDates').on('click', function () {

    let $btn = $(this);
    let fromDate = $('#fromDate').val();
    let toDate   = $('#toDate').val();

    if (!fromDate || !toDate) {
        alert('Please select both From and To dates.');
        return;
    }

    if(!$clickedIcon){
        alert("No field selected");
        return;
    }

    // ===== START LOADING STATE =====
    $btn.prop('disabled', true);
    let oldText = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...');

    let $td = $clickedIcon.closest('td');
    let $tr = $clickedIcon.closest('tr');
    let branch_name = $tr.find('.location-cell').data('location');

    function format(d){
        let p = d.split("-");
        return p[2] + "/" + p[1] + "/" + p[0];
    }

    let formatted = format(fromDate) + " - " + format(toDate);
    $td.find('.calander-text').text(formatted);

    // ---- 3️⃣ Check dates in backend ----
    $.ajax({
        url  : incomedatecheck,
        type : "POST",
        data : {
            branch_name : branch_name,
            from_date   : fromDate,
            to_date     : toDate,
            column      : currentField,
            _token      : $('meta[name="csrf-token"]').attr('content')
        },
        success:function(res){

            // ---- If missing dates exist ----
            if(res.missing_dates && res.missing_dates.length){

                let missingDates = res.missing_dates;
                $td.find('.hidden-range').val(formatted);

                let fitterremovedata = [];
                fitterremovedata.push(`tbl_locations.name='${branch_name}'`);

                formatted = missingDates[0] + " - " + missingDates[missingDates.length-1];

                // ---- send ONLY missing range ----
                rowfilterrange(fitterremovedata, formatted, 1, incomeOverviewDatafitter, function(result){

                    $.ajax({
                        url  : incomestore,
                        type : "POST",
                        data : {
                            mocdata     : result,
                            branch_name : branch_name,
                            from_date   : fromDate,
                            to_date     : toDate,
                            column      : currentField,
                            _token      : $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(res){
                            buildTooltip($clickedIcon, res, formatted);

                            // stop loading and close modal
                            $btn.prop('disabled', false);
                            $btn.html(oldText);
                            $('#dateModal').modal('hide');

                            alert("Missing data stored & totals updated!");
                        },
                        error:function(){
                            $btn.prop('disabled', false);
                            $btn.html(oldText);
                            alert("Something went wrong. Try again.");
                        }
                    });

                });

            } else {
                // ---- All dates exist ----
                buildTooltip($clickedIcon, res, formatted);

                // stop loading and close modal
                $btn.prop('disabled', false);
                $btn.html(oldText);
                $('#dateModal').modal('hide');

                alert("No missing dates — all dates already reconciled.");
            }
        },
        error:function(){
            $btn.prop('disabled', false);
            $btn.html(oldText);
            alert("Something went wrong. Try again.");
        }
    });

});

// ---- 4️⃣ Tooltip builder function ----
function buildTooltip($icon, res, formatted){
    let td = $icon.closest('td');
    let html = `<div class="custom-tooltip" style="display:none;"><table>`;

    res.totallist.forEach(r=>{
        html += `<tr>
                    <td>${r.date_range}</td>
                    <td style="text-align:right;">₹${r.amount}</td>
                 </tr>`;
    });

    html += `<tr class="total">
                <td>Total</td>
                <td class="moc_total" style="text-align:right;">₹${res.total_amount}</td>
             </tr>
             </table>
             <span>Date :</span>
             <span class="datte_range">${res.coming_dates}</span>
             </div>`;

    td.find('.custom-tooltip').remove();
    td.append(html);

    td.find('.info-icon').show();
    td.find('.calendar-icon').show();
    td.find('.tooltip-text').text(res.total_amount);
    td.find('.calander-text').text(formatted);
}

// ---- 5️⃣ Tooltip hover using delegation ----
// $(document).on('mouseenter', '.info-icon', function() {
//     let tip = $(this).siblings('.custom-tooltip');
//     tip.show();

//     tip.css({left:"", right:""});
//     let rect = tip[0].getBoundingClientRect();
//     let vw   = $(window).width();
//     if(rect.right > vw - 10){
//         tip.css({right: 0, left: "auto"});
//     }
// }).on('mouseleave', '.info-icon', function(){
//     $(this).siblings('.custom-tooltip').hide();
// });

$(document).on('mouseenter', '.info-icon', function () {

    const $tip  = $(this).siblings('.custom-tooltip');

    // get icon position
    const rect = this.getBoundingClientRect();

    // default: show under icon
    let top  = rect.bottom + 6;
    let left = rect.left;

    // keep tooltip inside viewport
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

$(document).on('mouseleave', '.info-icon, .custom-tooltip', function () {
    $('.custom-tooltip').hide();
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




});


