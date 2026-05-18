$(document).ready(function () {
    // Fetch data and initialize pagination
    overall_fetch(1);
    document_fetch(1);
    $(".search_view").hide();
    $(".my_search_view").hide();
    $("#start_date").hide();
    $("#end_date").hide();
    $("#close-button").on("click", function () {
        location.reload();
    });
    var targetTab = "";
    $(".nav-link").click(function () {
        targetTab = $(this).data("bs-target");
        if (targetTab == "#analytics-tab-2-pane") {
            $("#documentbtn").show();
            $("#document_btn").hide();
        }
        if (targetTab == "#analytics-tab-1-pane") {
            $("#documentbtn").hide();
            $("#document_btn").show();
        }
    });

    function collectVehicleMarketerFilters() {
        const resultsArray_marketer = [];
        $(".vehicle_marketers").each(function (vm_index, vm_element) {
            const $dropdown_options = $(vm_element);
            const $search_input = $dropdown_options.parent(".dropdown").find(".vehiclevalues_search");
            const selectedDropdownValues = [];
            $dropdown_options.find(".selected").each((do_index, do_element) => {
                const val = $(do_element).attr("data-value");
                if (val !== undefined && val !== "") {
                    selectedDropdownValues.push(val);
                }
            });
            const search_input_name = $search_input.attr("name");
            const search_input_value = ($search_input.val() || "").trim();
            if (search_input_value !== "" && selectedDropdownValues.length === 0) {
                const matchText = search_input_value.toLowerCase();
                $dropdown_options.find("div").each(function () {
                    const optionText = $(this).text().trim().toLowerCase();
                    const val = $(this).attr("data-value");
                    const valText = val ? String(val).toLowerCase() : "";
                    const regPrefix = optionText.split(" - ")[0];
                    const matches =
                        optionText === matchText ||
                        optionText.startsWith(matchText) ||
                        valText === matchText ||
                        regPrefix === matchText ||
                        matchText.startsWith(regPrefix);
                    if (matches && val !== undefined && val !== "") {
                        if (!selectedDropdownValues.includes(val)) {
                            selectedDropdownValues.push(val);
                        }
                    }
                });
            }
            if (search_input_value !== "" && selectedDropdownValues.length > 0) {
                resultsArray_marketer.push(`${search_input_name}=${selectedDropdownValues.join(",")}`);
            }
        });
        return resultsArray_marketer;
    }

    function clearVehicleFilterInput(filterId) {
        const $input = $("#" + filterId);
        $input.val("");
        $input.siblings(".dropdown-options").find("div").removeClass("selected");
    }

    function morefilltersremoveviews(fitterremovedata, url, urlstatus) {
        if (fitterremovedata != "" && fitterremovedata.length > 0) {
            var morefilltersall = fitterremovedata.join(" AND ");
            $.ajax({
                url: url,
                type: "GET",
                data: {
                    morefilltersall: morefilltersall,
                    moredatefittervale: $("#dateallviews").text(),
                },
                success: function (responseData) {
                    handleSuccess(responseData, urlstatus);
                },
                error: function (xhr, status, error) {
                    $("#ticket_details1").hide();
                    console.error("AJAX Error:", status, error);
                },
            });
        } else {
            if (urlstatus == 1) {
                $(".search_view").hide();
                $(".clear_views").hide();
                overall_fetch(2);
            } else {
                $(".clear_my_views").hide();
                $(".my_search_view").hide();
                document_fetch(1);
            }
        }
    }
    var ticketdataSource = []; // Data will be fetched here
    var marketersearchvalue = [];
    // Fetch the data and initialize pagination
    function overall_fetch(statusid) {
        var moredatefittervale = $("#dateallviews").text();
        $(".value_views_mainsearch").text("");
        $.ajax({
            url: vehiclefetchUrl,
            type: "GET",
            data: {
                moredatefittervale: moredatefittervale,
                statusid: statusid,
            },
            success: function (responseData) {
                handleSuccess(responseData, 1);
            },
            error: function (xhr, status, error) {
                $("#ticket_details1").hide();
                console.error("AJAX Error:", status, error);
            },
        });
    }
    // Fetch the data and initialize pagination
    function document_fetch(statusid) {
        var moredatefittervale = $("#mydateallviews").text();
        $(".value_views_mysearch").text("");
        $("#my_ticket_details1").show();
        $.ajax({
            url: vehicledocumentUrl,
            type: "GET",
            data: {
                moredatefittervale: moredatefittervale,
                statusid: statusid,
            },
            success: function (responseData) {
                handleSuccess(responseData, 3);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
            },
        });
    }
    function morefilterview(uniqueResults, urlstatus, url, moredatefittervale, isDateSelected=false) {
        // debugger
        if (uniqueResults != "" || isDateSelected) {
            var morefilltersall = uniqueResults.join(" AND ");
            $.ajax({
                url: url,
                type: "GET",
                data: {
                    morefilltersall: morefilltersall,
                    moredatefittervale: moredatefittervale,
                },
                success: function (responseData) {
                    handleSuccess(responseData, urlstatus);
                },
                error: function (xhr, status, error) {
                    $("#ticket_details1").hide();
                    console.error("AJAX Error:", status, error);
                },
            });
        } else {
            if (urlstatus == 1) {
                $(".search_view").hide();
                $(".clear_views").hide();
                overall_fetch(2);
            } else {
                $(".clear_my_views").hide();
                $(".my_search_view").hide();
                document_fetch(2);
            }
        }
    }
    function handleSuccess(responseData, urlstatus) {
        // dd(responseData, urlstatus);
        // echo"<pre>";
        // print_r(responseData);
        if (urlstatus == 1) {
            $("#ticket_details1").hide();
            $("#ticket_details").show();
            ticketdataSource = responseData;
            totalItems = responseData.length; // Set the data fetched from the server
            var ticketpageSize = parseInt($("#itemsPerPageSelect").val()); // Get selected items per page
            ticketrenderPagination(ticketdataSource, ticketpageSize);
            ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
        }
    }
    // Render pagination controls based on data
    var fitterremovedata = [];
    $("#vehicle_search").on("click", function () {
        $(".search_view").show();
        $(".clear_views").show();
        $(".value_views_mainsearch").text("");
        $(".vehiclevalues_search").val("");
        let vehicle_type = $("#vehicle_type").val();
        let fuel_type = $("#vfuel_type").val();
        var resultsArray = [];
        $(".morefittersclr").each(function () {
            var value = $(this).find(":selected").text();
            if (value == "Select Vehicle Type") {
                value = "";
            }
            if (value == "Select Fuel Type") {
                value = "";
            }
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results =
                $(this).attr("name") +
                "='" +
                value.replace(/\s*-\s*/, "-").trim() +
                "'";
            resultsArray.push(results);
            fitterremovedata = resultsArray;
        });
        fitterremovedata = fitterremovedata.map((filter) =>
            filter.replace(/, /g, ",")
        );
        fitterremovedata;
        var moreFilterValues = [
            $("#vehicle_type").find(":selected").text(),
            $("#vfuel_type").find(":selected").text(),
        ];
        $(".value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index]
                ? moreFilterValues[index]
                : ""; // Use "N/A" if value is empty
            if (morefillterdata == "Select Vehicle Type") {
                morefillterdata = "";
            }
            if (morefillterdata == "Select Fuel Type") {
                morefillterdata = "";
            }
            $(this).text(morefillterdata);
        });
        ticketFilterAjax(vehicle_type, fuel_type, fetchdocUrlfitter, 1);
    });
    function ticketFilterAjax(vehicle_type, fuel_type, url, urlstatus) {
        $.ajax({
            url: url,
            type: "GET",
            data: {
                vehicle_type: vehicle_type,
                fuel_type: fuel_type,
            },
            success: function (responseData) {
                handleSuccess(responseData, urlstatus);
            },
            error: function (xhr, status, error) {
                $("#ticket_details1").hide();
                console.error("AJAX Error:", status, error);
            },
        });
    }


    // date fitter function

    $(document).on("click", ".vehicle_marketers div, .ranges, .applyBtn", function () {
        //  debugger
        let isDateRangeSlected = $(this).hasClass("ranges") || $(this).hasClass("applyBtn");
        // Get the selected registration number value
        var moredatefittervale = $('#dateallviews').text();
        // Show UI elements
        // $(".clear_views").show();
        // $(".search_view").show();
        // $(".value_views").text("");


        $(".value_views").text("");

        let resultsArray_marketer = collectVehicleMarketerFilters();
        // Additional filter values
        const moreFilterValues_market = [
            $("#zone_views").val(),
            $("#branch_views").val(),
            $("#reg_number").val(),
            $("#fuel_type_filter").val(),
            $("#vehicle_type_filter").val(),
            $("#insurance_company_name_filter").val(),
        ];

        if (($(this).hasClass("ranges") || $(this).hasClass("applyBtn")) && !moreFilterValues_market.filter((val) => val).length) {
            $(".clear_views").hide();
            $(".search_view").hide();
        } else {
            $(".clear_views").show();
            $(".search_view").show();
        }


        // Update the UI with the selected filter values
        $(".value_views_mainsearch").each(function (index) {
            const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
            $(this).text(filterValue);
        });
        // Prepare data for the filter function
        fitterremovedata = resultsArray_marketer.map((filter) =>filter.replace(/, /g, ","));
        // Call function with the processed data
        morefilterview(fitterremovedata, 1, fetchUrldocfitter, moredatefittervale, isDateSelected=isDateRangeSlected);
    });


    // value_views_mainsearch ///////////////////////////////////

    $(document).on("click", ".value_views_mainsearch", function () {
        // debugger
        var moredatefittervale = $('#dateallviews').text();
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
        var clear_filtr = $(this).attr("id");

        $(this).text("");

        $(`${clear_filtr}`).val();

        if (clear_filtr == "zone_search") {
            clearVehicleFilterInput("zone_views");
        }
        if (clear_filtr == "branch_search") {
            clearVehicleFilterInput("branch_views");
        }
        if (clear_filtr == "register_number_search") {
            clearVehicleFilterInput("reg_number");
        }
        if (clear_filtr == "fuel_type_search") {
            clearVehicleFilterInput("fuel_type_filter");
        }
        if (clear_filtr == "vehicle_type_search") {
            clearVehicleFilterInput("vehicle_type_filter");
        }
        if (clear_filtr == "insurance_company_search") {
            clearVehicleFilterInput("insurance_company_name_filter");
        }

        let resultsArray_marketer = collectVehicleMarketerFilters();

           // Set isDateRangeSlected based on class or if no filters are selected
            let isDateRangeSlected = $(this).hasClass("ranges") ||
            $(this).hasClass("applyBtn") ||
            resultsArray_marketer.length === 0;

            if (isDateRangeSlected) {
                $(".clear_views").hide();
                $(".search_view").hide();
            } else {
                $(".clear_views").show();
                $(".search_view").show();
            }

        // Update the uniqueResults array to remove the corresponding filter
        fitterremovedata = resultsArray_marketer.map((filter) =>filter.replace(/, /g, ","));
        morefilterview(fitterremovedata, 1, fetchUrldocfitter, moredatefittervale,isDateRangeSlected);
    });

    // value_views_mainsearch //

    function ticketdatefillterrange(datefiltervalue, fitterremovedata) {
        // debugger;
        // alert(datefiltervalue);
        currentFilter = datefiltervalue;
        var morefilltersall = fitterremovedata.join(" AND ");
        // alert(morefilltersall);
        $("#ticket_details1").show();
        $.ajax({
            url: vehicledatefillter,
            type: "GET",
            data: {
                datefiltervalue: currentFilter,
                morefilltersall: morefilltersall,
            },
            success: function (responseData) {
                console.log(responseData);
                handleSuccess(responseData, 1);
            },
            error: function (xhr, status, error) {
                $("#ticket_details1").hide();
                console.error("AJAX Error:", status, error);
            },
        });
    }

    function vechiclemorefilterview(fitterremovedata,moredatefittervale)
    {

    $.ajax({
        url: fetchUrlmorefittervechicle,
        type: "GET",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        data: {
            fitterremovedata:fitterremovedata,
            moredatefittervale:moredatefittervale,
        },
        success: function (responseData) {
            $("#doctor_details1").hide();
            $("#doctor_details").show();
            dataSource = responseData;
            totalItems = responseData.length; // Set the data fetched from the server
            var pageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
            ticketrenderPagination(dataSource, pageSize);
            ticketrenderTable(dataSource, pageSize, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            $("#doctor_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
    }
    // Render pagination controls based on data
    function ticketrenderPagination(data, ticketpageSize) {
        var totalPages = Math.ceil(data.length / ticketpageSize);
        var paginationHtml = "";
        for (var i = 1; i <= totalPages; i++) {
            paginationHtml +=
                '<button class="page-bttn " style="background-color:#6a6ee4;color: #fff;"  data-page="' +
                i +
                '">' +
                i +
                "</button>";
        }
        $("#ticketpagination").html(paginationHtml);
        // Bind click event to each pagination button
        $(".page-bttn").click(function () {
            var pageNum = $(this).data("page");
            $(".page-bttn").removeClass("active");
            $(this).addClass("active");
            ticketrenderTable(data, ticketpageSize, pageNum);
        });
    }
    function formatGtsInstalled(val) {
        if (val === "yes" || val === 1 || val === "1") return "Yes";
        if (val === "no" || val === 0 || val === "0") return "No";
        return val || "-";
    }

    function formatGtsStatus(val) {
        if (!val) return "-";
        const v = String(val).toLowerCase();
        return v.charAt(0).toUpperCase() + v.slice(1);
    }

    // Render table rows based on the page and page size
    function ticketrenderTable(data, ticketpageSize, pageNum) {
        count= data.length;
        var startIdx = (pageNum - 1) * ticketpageSize;
        var endIdx = pageNum * ticketpageSize;
        var pageData = data.slice(startIdx, endIdx);
        // console.log(pageData);
        var body = "";
        $.each(pageData, function (index, user) {
            let dateStr = user.created_at;
            let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
            body +=
                '<tr onclick="rowClick(event)">' +
                '<td class="tdview"> ' +
                (startIdx + index + 1) +
                "</td>" +
                '<td class="tdview"> ' +
                (user.cluster_name || "-") +
                "</td>" +
                '<td class="tdview" style="width: 13%;">' +
                user.year_of_manufacture +
                "<br>" +
                formattedDate +
                "</td>" +
                '<td class="tdview"> ' +
                user.vehicle_no +
                "</td>" +
                '<td class="tdview">' +
                user.branch_name +
                "</td>" +
                '<td class="tdview">' +
                user.vehicle_type +
                "</td>" +
                // hh
                '<td class="tdview">' +
                user.vehicle_number +
                "</td>" +
                // End
                '<td class="tdview">' +
                user.make +
                "</td>" +
                '<td class="tdview">' +
                user.registration_number +
                "</td>" +
                '<td class="tdview">' +
                user.engine_number +
                "</td>" +
                '<td class="tdview">' +
                user.chassis_number +
                "</td>" +
                '<td class="tdview">';
            // Corrected fuel_type handling
            if (user.fuel_type == "1") {
                body += "Petrol";
            } else if (user.fuel_type == "2") {
                body += "Diesel";
            } else if (user.fuel_type === "3") {
                body += "Electronic Vehicle";
            } else if (user.fuel_type === "4") {
                body += "CNG";
            } else {
                body += '<span class="new-badge">' + user.fuel_type + "</span>";
            }
            // Closing the table cell and row tags
            body +=
                "</td>" +


                '<td class="tdview">' +
                    (user.company_name || '-') +
                '</td>' +
                '<td class="tdview">' +
                    (user.expiry_date || '-') +
                '</td>'+


                '<td class="tdview">' +
                "<a href='#' class='add_insurance' data-addinsurance='" + user.vehicle_id + "'>" +
                '<i class="fa-solid fa-file-pen f-40"></i>' +
                "</a>" +
                "</td>" +


                '<td class="tdview">' +
                '<a href="#" id="imagecheck">'
                +'<i class="fas fa-file-image f-40" style="color:#D072AE;"></i>' +
                "</a>" +
                "</td>" +





                '<td class="tdview">' +
                (user.incharge_driver_name || "-") +
                "</td>" +
                '<td class="tdview">' +
                (user.incharge_admin_name || "-") +
                "</td>" +
                '<td class="tdview">' +
                formatGtsInstalled(user.gts_installed) +
                "</td>" +
                '<td class="tdview">' +
                formatGtsStatus(user.gts_status) +
                "</td>" +

                '<td class="tdview">' +
                "<a href='#' class='add_document' data-adddocument='" + user.vehicle_id + "'>" +
                '<i class="fas fa-file-upload f-40"></i>' +
                "</a>" +
                "</td>" +

                // '<td class="tdview">' +
                // (user.document_name && user.document_name.length > 0 ? "<a href='#' class='vehicle_document' data-documentname='" + user.document_name + "'>" +'<i class="fa fa-file f-20" aria-hidden="true"></i>' +
                // "</a>": "<span class='text-danger'>No Data Found</span>") +"</td>" +
                "<td class='tdview'>" +
                (
                    user.document_count > 0 ? "<a href='#' class='vehicle_document' data-documentname=''><i class='fa fa-file-text f-40' aria-hidden='true' style='color:#D072AE;'></i></a>"
                    : "<span class='text-danger'>No Data Found</span>"
                ) +"</td>"+


                '<td class="tdview">' +  (user.daily_photos ? '<a href="#" class="vehicle_activity" data-id="' + user.daily_photos + '">' +
                '<i class="fa fa-eye f-20" aria-hidden="true"></i>' +'</a>' : "<span class='text-danger'>No Data Found</span>") +'</td>'+


                // '<td class="tdview" id="edit_vehicle" data-id="' + user.vehicle_id +'"><img src="../assets/images/edit.png" style="width: 30px;"  alt="Icon" class="icon"></td>' +


                '<td class="tdview" id="edit_vehicle" data-id="' + user.vehicle_id + '" data-insurance_id="' + user.insurance_id + '"><img src="../assets/images/edit.png" style="width:40px;" alt="Icon" class="icon"></td>'


                // '<td class="tdview upload_document_details" data-id="' +
                // user.id +'" data-fetch_id="' +
                // user.vehicle_document_details_id +
                // "\" data-documentname='" +
                // user.document_name +
                // "' data-expiry_date=\"" +
                // user.expiry_date +
                // '" data-model="' +
                // user.make +
                // '" ><img src="../assets/images/policy.png" style="width: 35px;"  alt="Icon" class="icon"></td>' +
                // Insurance Model //
                '<td class="tdview" id="expiry_date" style="display:none;" data-expirydate="' +
                user.expiry_date +
                '" >' +
                user.expiry_date +
                "</td>" +
                '<td class="tdview" id="imagepath" style="display:none;" data-imagepath="' +
                user.image_paths +
                '" >' +
                user.image_paths +
                "</td>" +
                '<td class="tdview" id="companyname" style="display:none;" data-companyname="' +
                user.company_name +
                '" >' +
                user.company_name +
                "</td>" +
                '<td class="tdview" id="renewaldate" style="display:none;" data-renewaldate="' +
                user.renewal_date +
                '" >' +
                user.renewal_date +
                "</td>" +
                '<td class="tdview" id="policy" style="display:none;" data-policy="' +
                user.policy_details +
                '" >' +
                user.policy_details +
                "</td>" +
                '<td class="tdview" id="payment" style="display:none;" data-payment="' +
                user.payment +
                '" >' +
                user.payment +
                "</td>" +
                // Insurance Model //
                '<td class="tdview" id="opening_km_data" style="display:none;" data-opening_km="' +
                user.opening_km +
                '">' +
                user.opening_km +
                "</td>" +
                '<td class="tdview" id="closing_km_data" style="display:none;" data-closing_km="' +
                user.closing_km +
                '">' +
                user.closing_km +
                "</td>" +
                '<td class="tdview" id="driven_day" style="display:none;" data-driven_day="' +
                user.total_km_driven_per_day +
                '">' +
                user.total_km_driven_per_day +
                "</td>" +
                '<td class="tdview" id="driven_month" style="display:none;" data-driven_month="' +
                user.total_km_driven_per_month +
                '">' +
                user.total_km_driven_per_month +
                "</td>" +
                '<td class="tdview" id="daily_photos" style="display:none;" data-daily_photos="' +
                user.daily_photos +
                '">' +
                user.daily_photos +
                "</td>" +
                '<td class="tdview" id="report" style="display:none;" data-report="' +
                user.daily_activity_report +
                '">' +
                user.daily_activity_report +
                "</td>" +
                '<td class="tdview" id="passenger" style="display:none;" data-passenger="' +
                user.passenger_travelled +
                '">' +
                user.passenger_travelled +
                "</td>" +
                "</tr>";
        });
        $("#ticket_details").html(body);
        $("#today_visits").text(count);
        $("#edit_counts").text(count);
    }

    $(document).on("click", "tr .add_insurance", (e) => {
        $("#exampleModal_ins_1").modal("show");
        var row = $(e.target).closest("tr");
        var add_insurance = row.find(".add_insurance").data("addinsurance");
        // alert(add_insurance);
        $("#vehicle_id_ins").val(add_insurance);
    });



    $(document).on("click", "tr .add_document", (e) => {
        $("#exampleModal2").modal("show");
        var row = $(e.target).closest("tr");
        var add_document = row.find(".add_document").data("adddocument");
        // alert(add_document);
        $("#vehicle_id").val(add_document);
    });
    $(document).on("click", "#vehicle-insurance-thumbnails img", (e) => {
        let img_src = $(e.target).prop("src");
        $("#vehicle-insurance-main").prop("src", img_src);
    });

    $(document).on("click", "#imagecheck", function (e) {
        $("#exampleModal3").modal("show");

        var row = $(e.target).closest("tr");
        var vehicle_id = row.find("#edit_vehicle").data("id");

        $.ajax({
            url: vehicleInsuranceDocumentUrl,
            type: "GET",
            data: {
                vehicle_id: vehicle_id,
            },
            success: function (responseData) {
                console.log(responseData);
                $("#all_insurance_list").html("");
                $("#insurance_document_view_buttons").html("");

                // Sort by newest first
                responseData.sort((a, b) => b.id - a.id);

                for (const [index, data] of responseData.entries()) {
                    let imagepath_ins_arr = data.image_paths.split(",");
                    let firstFileName = imagepath_ins_arr[0].split("/").pop(); // Just the file name


                    // if (index === 0) {
                    //     $("#insurance_document_view").prop("src", "../public/" + imagepath_ins_arr[0]);
                    //     setTimeout(() => {
                    //         $(".document-btn").first().addClass("active-document-btn");
                    //     }, 10);
                    // }
                    if (index === 0) {
                        const validImages = imagepath_ins_arr.filter(path => path.trim() !== "");
                        const firstImage = validImages.length > 0 ? "../public/" + validImages[0] : "../public/images/no_image.jpg";

                        $("#insurance_document_view").prop("src", firstImage);

                        setTimeout(() => {
                            if (validImages.length > 0) {
                                $(".document-btn").first().addClass("active-document-btn");
                            }
                        }, 10);
                    }


                    for (const document of imagepath_ins_arr) {
                        if (!document.trim()) continue; // Skip empty or whitespace-only strings

                        let file_name = document.split("/").pop();
                        if (!file_name) continue; // Skip if file_name is empty

                        let button = $(`<button type="button" class="btn btn-primary document-btn" style="border: 6px solid white; border-radius: 30px; background-color: #d36aa5; white-space: normal; text-align: center; word-break: break-word; color: white; padding: 10px 15px; font-size: 14px; max-width: 200px; display: inline-block; margin: 5px;">${file_name}</button>`);

                        button.on("click", function () {
                            $("#insurance_document_view").prop("src", "../public/" + document);
                            $(".document-btn").removeClass("active-document-btn");
                            $(this).addClass("active-document-btn");
                        });

                        $("#insurance_document_view_buttons").append(button);
                    }


                    // Build the info section including file name as heading
                    let html = `<div class="views-ali" style="margin-left: 20px;margin-top: 20px;">
                        <h4 class="alert-heading"  style="font-size: 14px; font-weight: bold; color:#6a6ee4;">${firstFileName }</h4>
                        <br>
                        <h4 class="alert-heading" style="font-size: 12px;">OD Insurance Expiry Date</h4>
                        <p style="font-size: 12px;">${data.expiry_date }</p>
                        <h4 class="alert-heading" style="font-size: 12px; margin-top: 15px;">Insurance Company</h4>
                        <p style="font-size: 12px; margin-top: 0px;">${data.company_name }</p>
                        <h4 class="alert-heading" style="font-size: 12px;">OD Insurance Renewal Date</h4>
                        <p style="font-size: 12px;">${data.renewal_date }</p>
                        <h4 class="alert-heading" style="font-size: 12px;">OD Insurance Policy</h4>
                        <p style="font-size: 12px;">${data.policy_details }</p>
                        <h4 class="alert-heading" style="font-size: 12px;">TP Insurance Company name</h4>
                        <p style="font-size: 12px;">${data.thirdparty_company_name }</p>
                        <h4 class="alert-heading" style="font-size: 12px;">TP Insurance Expiry Date</h4>
                        <p style="font-size: 12px;">${data.thirdparty_expiry_date }</p>
                        <h4 class="alert-heading" style="font-size: 12px;">TP Insurance Renewal Date</h4>
                        <p style="font-size: 12px;">${data.thirdparty_renewal_date }</p>
                        <h4 class="alert-heading" style="font-size: 12px;">TP Insurance Policy</h4>
                        <p style="font-size: 12px;">${data.thirdparty_policy_details }</p>



                        <h4 class="alert-heading" style="font-size: 12px;">Insurance Payment</h4>
                        <p style="font-size: 12px;">${data.payment }</p>
                    </div>
                    ${index < responseData.length - 1 ? "<hr>" : ""}`;

                    $("#all_insurance_list").append(html);
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
            },
        });
    });





    let currentActiveButton = null; // Track currently active button
    $(document).on("click", "tr .vehicle_document", (e) => {
        $("#exampleModal1").modal("show");
        var row = $(e.target).closest("tr");
        var vehicle_id = row.find("#edit_vehicle").data("id");

        $("#exampleModal1 .btn-group-vertical").html("");

        $.ajax({
            url: vehicleDocumentViewUrl,
            type: "GET",
            data: {
                vehicle_id: vehicle_id,
            },
            success: function (responseData) {
                console.log(responseData);

                for (const [rowIndex, data] of responseData.entries()) {
                    let documents = JSON.parse(data.document_name);
                    for (const [docIndex, file] of documents.entries()) {
                        let file_name = file.split("/").pop();

                        let button = $(`
                            <button type="button" class="btn btn-primary document-button"
                                style="border: 6px solid white; border-radius: 30px; background-color: #d36aa5; white-space: normal; text-align: center; word-break: break-word; color: white; padding: 10px 15px; font-size: 14px; max-width: 200px; display: inline-block; margin: 5px;">
                                ${file_name}
                            </button>
                        `);

                        // Handle click
                        button.on("click", function () {
                            $("#pdfmain").prop("src", "../public/" + file);

                            // Reset previous button style
                            if (currentActiveButton) {
                                currentActiveButton.css("background-color", "#d36aa5");
                            }

                            // Highlight current button
                            $(this).css("background-color", "#3A6AD4");
                            currentActiveButton = $(this);
                        });

                        // Set first file as default
                        if (rowIndex === 0 && docIndex === 0) {
                            $("#pdfmain").prop("src", "../public/" + file);
                            button.css("background-color", "#3A6AD4"); // Highlight default
                            currentActiveButton = button;
                        }

                        $("#exampleModal1 .btn-group-vertical").append(button);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr);
            },
        });
    });


    $(document).on("click", ".vehicle_activity", function (e) {
        $("#exampleModal4").modal("show");
        var row = $(this).closest("tr");
        var opening_km_id = row.find("#opening_km_data").data("opening_km");
        var closing_km_id = row.find("#closing_km_data").data("closing_km");
        var driven_day = row.find("#driven_day").data("driven_day");
        var driven_month = row.find("#driven_month").data("driven_month");
        var daily_photos = row.find("#daily_photos").data("daily_photos");
        var report = row.find("#report").data("report");
        var passenger = row.find("#passenger").data("passenger");
        // alert(daily_photos);
        $("#opening_km").text(opening_km_id);
        $("#closing_km").text(closing_km_id);
        $("#drivenperday").text(driven_day);
        $("#drivenpermonth").text(driven_month);
        // $("#dailyphotos").text(daily_photos);
        $("#activity_report").text(report);
        $("#passenger_travel").text(passenger);
        if (daily_photos) {
            $("#dailyphotos").attr("src","../public/" + daily_photos);
        } else {
            $("#dailyphotos").attr("src", "../public/images/street-map-sample.png"); // fallback image if none
        }
    });
    // $(".ranges, .applyBtn").on("click", function () {
    //     debugger
    //     // Check if the click happened on a specific class
    //     if ($(this).hasClass("ranges")) {
    //         var datefilltervalue = $("#dateviewsall").text();
    //         ticketdatefillterrange(datefilltervalue, fitterremovedata);
    //     } else if ($(this).hasClass("applyBtn")) {
    //         var datefilltervaluenew = $(".drp-selected").text(); // Get the current text value when '.applyBtn' is clicked
    //         var dateRange = datefilltervaluenew.split(" - ");
    //         function convertDateFormat(dateStr) {
    //             let parts = dateStr.split("/");
    //             return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
    //         }
    //         var startDate = convertDateFormat(dateRange[0]);
    //         var endDate = convertDateFormat(dateRange[1]);
    //         var datefilltervalue = `${startDate} - ${endDate}`;
    //         ticketdatefillterrange(datefilltervalue, fitterremovedata);
    //     }
    // });
    $(document).on("click", ".value_views", function () {
        // debugger
        var morefillterremvedata = $(this).text().replace(/\s*-\s*/, "-").trim();
        $(this).text("");
        // Find the element that contains the value
        let indexToRemove = fitterremovedata.findIndex((item) =>
            item.includes(morefillterremvedata)
        );
        if (indexToRemove !== -1) {
            // Remove the element at the found index
            var removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // splice returns an array, so we access the first element
        }
        // Split the string at the first '=' and get the part before it
        let key = removedElement.split("=")[0];
        if (key == "vehicle_details.fuel_type") {
            $("#vfuel_type").prop("selectedIndex", 0);
        }
        if (key == "vehicle_type.type") {
            $("#vehicle_type").prop("selectedIndex", 0);
        }
        // Update the uniqueResults array to remove the corresponding filter
        fitterremovedata = fitterremovedata.filter(function (item) {
            return !item.trim().includes(morefillterremvedata.trim() + "'");
        });
        var datefilltervalue = $("#dateallviews").text();
        morefilltersremoveviews(fitterremovedata, fetchdocUrlfitter, 1);
    });



    $(document).on("click", ".clear_views", function () {
        fitterremovedata.length = 0;
        $(".vehiclevalues_search").val("");
        $(".vehicle_marketers div").removeClass("selected");
        $(".value_views").text("");
        $(".morefittersclr").val("");
        $(".clear_views").hide();
        $(".search_view").hide();
        $(".value_views_mainsearch").text("");
        overall_fetch(1);
    });
    // Handle items per page change
    $("#itemsPerPageSelect").change(function () {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Initially show the first page
    });

});


$("#submit-document_update").on("click", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Clear previous errors
    $(".errorss").text("");

    // Gather form values
    const vehicle_id = $("#vehicle_id").val();
    const expire_date = $("#expire_update_date").val();
    const document_type = $("#document_type").val();
    const files = $("#pdf_update")[0].files;

    let hasError = false;

    // Validation
    if (!vehicle_id) {
        $(".error_vehicle_id").text("Vehicle ID is required");
        hasError = true;
    }

    if (!expire_date) {
        $(".error_hplname").text("Expire date is required");
        hasError = true;
    }

    if (!document_type) {
        $(".error_priority").text("Document type is required");
        hasError = true;
    }

    if (files.length === 0) {
        $(".error_images").text("Please upload at least one PDF file.");
        hasError = true;
    }

    if (hasError) return; // Stop if validation failed

    // Create FormData
    let formData = new FormData();
    formData.append("id", $("#id_document").val());
    formData.append("vehicle_id", vehicle_id);
    formData.append("expire_date", expire_date);
    formData.append("expire_dates", $("#expire_dates").val());
    formData.append("document_type", document_type);
    formData.append("year_of_manufacture", $("#year_of_manufacture").val());
    formData.append("make", $("#model").val());
    formData.append("update_documents_all", $("#update_documents_all").val());

    for (const file of files) {
        formData.append("image[]", file);
    }

    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    // AJAX request
    $.ajax({
        url: vehicledocumentupdate,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                window.dispatchEvent(new CustomEvent("swal:toast", {
                    detail: {
                        title: "Info!",
                        text: response.message,
                        icon: "success",
                        background: "success",
                    },
                }));
                location.reload();
            } else {
                window.dispatchEvent(new CustomEvent("swal:toast", {
                    detail: {
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        background: "#f8d7da",
                    },
                }));
            }

            $("#exampleModal2").modal("hide");
            $("#exampleModal2").find("input, textarea").val("");
            $(".vehicle-dropdown-options div").removeClass("selected");
            $("#document_type").prop("selectedIndex", 0);
            $("#pdf_update").replaceWith($("#pdf_update").clone(true));
            document_fetch(1);
        },
        error: function (error) {
            console.error(error.responseJSON);
        },
    });
});





$(document).on("click", ".documentclk", function (e) {
    // alert("hi");
    $("#exampleModal1").modal("show");
    var row = $(this).closest("tr");
    // Get data from the row
    var pdffiles = row.find("#pffiles").text();
    pdffiles = pdffiles.replace(/[\[\]\"]/g, ""); // Remove [ ] " and extra spaces
    var firstImage = pdffiles.split(",")[0].trim();
    var imageNamefirst = firstImage.split("/").pop();
    $("#pdfmain").attr("src", "../public/document_data/" + imageNamefirst);
    var imageArray = pdffiles.split(",");
    var views = "";
    console.log(imageArray);
    imageArray.forEach(function (image) {
        var imageName = image.trim().split("/").pop(); // Get the file name
        imageNames = imageName.replace(/\\/g, ""); // Remove [ ] " and extra spaces
        // alert(imageName);
        // Replace the src value of each image with a new one
        views +=
            '<button style="font-size: 11px;" type="button" id="pdffetchdata" class="btn btn-primary pdf-btn">' +
            imageNames +
            "</button>";
    });
    $("#image_pdfs").html("");
    $("#image_pdfs").html(views);
    // Add event listener for the PDF buttons
    $(document).on("click", ".pdf-btn", function () {
        // Remove 'active' class from all buttons
        $(".pdf-btn").removeClass("active");
        // Add 'active' class to the clicked button
        $(this).addClass("active");
    });
});
$(document).on("click", ".upload_document", function (e) {
    $("#exampleModal2").modal("show");
    $("#docu_vehicle_no").hide();
    $("#vehicle_document_type").hide();
    var row = $(this).closest("tr");
    var fetchid = row.find("#idfetch").data("id");
    var pdffilesviews = row.find("#pdffiles").text();
    var expire_dates = row.find("#expire_dates").text();
    var year_of_manufacture = row.find("#year_of_manufacture").text();
    var model = row.find("#model").text();
    $("#id_document").val(fetchid);
    $("#update_documents_all").val(pdffilesviews);
    $("#expire_dates").val(expire_dates);
    $("#model").val(model);
});
// $(document).on("click", ".upload_document_details", function (e) {
//     $("#exampleModal2").modal("show");
//     $("#docu_vehicle_no").hide();
//     $("#vehicle_document_type").hide();
//     let fetch_id = $(e.target).data("fetch_id");
//     let documentname = $(e.target).data("documentname");
//     let expiry_date = $(e.target).data("expiry_date");
//     let model = $(e.target).data("model");
//     $("#id_document").val(fetch_id);
//     $("#update_documents_all").val(documentname);
//     $("#expire_dates").val(expiry_date);
//     $("#model").val(model);
// });
$(document).on("click", "#pdffetchdata", function (e) {
    fetchvalue = $(this).text();
    $("#pdfmain").attr("src", "../public/document_data/" + fetchvalue);
});
$(document).on("click", "#edit_vehicle", function (e) {
    $("#offcanvas_edit_vehicle").offcanvas("show");
    var id = $(this).closest("tr").find("#edit_vehicle").data("id");
    var insurance_id = $(this).closest("tr").find("#edit_vehicle").data("insurance_id");

    $.ajax({
        url: vehicleEditUrl,
        type: "GET",
        data: { id: id, insurance_id: insurance_id },
        success: function (response) {
            const insurance = response.insurance_details || {};
            const service = response.service_details || {};
            const location = response.location || {};
            const vehicleType = response.vehicle_type || {};

            $(".type-dropdown-options div").removeClass("selected");
            if (vehicleType.id) {
                $('.type-dropdown-options div[data-value="' + vehicleType.id + '"]').addClass("selected");
                $(".searchType").val(vehicleType.type || "");
            } else {
                $(".searchType").val("");
            }

            $("#branch_id").val(location.name || "");
            $("#edit_id").val(response.id || "");
            $("#offcanvas_edit_vehicle #vehicle_model").val(response.make || "");
            $("#offcanvas_edit_vehicle #yr_of_manufacture").val(response.year_of_manufacture || "");
            $("#offcanvas_edit_vehicle #registration_number").val(response.registration_number || "");
            $("#offcanvas_edit_vehicle #engine_number").val(response.engine_number || "");
            $("#offcanvas_edit_vehicle #chassis_number").val(response.chassis_number || "");
            $("#offcanvas_edit_vehicle #fuel_type").val(response.fuel_type || "");
            $("#offcanvas_edit_vehicle #cluster_name").val(response.cluster_name || "");
            $("#offcanvas_edit_vehicle #vehicle_number").val(response.vehicle_number || "");
            $("#edit_regis_owner").val(response.registration_owner || "");
            $("#edit_rto").val(response.rto_location || "");
            $("#edit_gts_installed").val(response.gts_installed || "");
            $("#edit_gts_status").val(response.gts_status || "");

            $("#insurance_company_name").val(insurance.company_name || "");
            $("#insurance_expiry_data").val(insurance.expiry_date || "");
            $("#insurance_renewal_date").val(insurance.renewal_date || "");
            $("#insurance_policy").val(insurance.policy_details || "");
            $("#thirdparty_insurance_company_name").val(insurance.thirdparty_company_name || "");
            $("#thirdparty_insurance_expiry_data").val(insurance.thirdparty_expiry_date || "");
            $("#thirdparty_insurance_renewal_date").val(insurance.thirdparty_renewal_date || "");
            $("#thirdparty_insurance_policy").val(insurance.thirdparty_policy_details || "");
            $("#editpayment").val(insurance.payment || "");
            $("#insurance_id").val(insurance.id || "");

            $("#service_id").val(service.id || "");
            $("#last_service_date").val(service.last_service || "");
            $("#last_tyre_changed_date").val(service.last_tyre_change || "");

            $("#edit_vehicle_incharge_driver").val(response.vehicle_incharge || "");
            $("#edit_vechile_incharge_admin").val(response.vehicle_incharge_admin || "");
        },
        error: function (xhr) {
            console.error("Failed to load vehicle for edit", xhr);
        },
    });
});
