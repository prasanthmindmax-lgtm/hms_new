$(document).ready(function () {
   $(".checkBtn").first().prop("checked", true).trigger("click");
    $("#reportrangecamp").on("click", function () {
        $(".ranges").removeClass("doctor_range activit_range");
        $(".ranges").addClass("camp_range");
        $(".drp-selected").removeClass("doctor_slct actvit_slct");
        $(".drp-selected").addClass("camp_slct");
        $(".applyBtn").removeClass("doctor_btn activit_btn");
        $(".applyBtn").addClass("camp_btn ");
    });
    $(".search_camp_view").hide();
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    // Clear error span text on input
    $("input, select, textarea").on("input change", function () {
        $(this).siblings("span").text("");
    });
    $("#close-button").click(function () {
        // Clear all input fields
        $("input, select, textarea").val("");
        // Clear error messages
        $("span").text("");
    });
    $("#submit-campdatas").click(function (event) {
        // alert('fhs');
        event.preventDefault();
        // Validation checks
        const validations = [
            {
                field: "#Branch",
                error: ".error_doctor",
                message: "Please select the branch",
            },
            {
                field: "#camp_date",
                error: ".error_employee",
                message: "Please select the camp_date",
            },
            {
                field: "#Camp_enddate",
                error: ".error_employee",
                message: "Please select the Camp_enddate",
            },
            {
                field: "#Camp_Centre_Name",
                error: ".error_special",
                message: "Please select the Camp_Centre_Name",
            },
            {
                field: "#camp_location",
                error: ".error_hplname",
                message: "Enter the camp_location",
            },
            {
                field: "#Digital_Marketing_coordinator",
                error: ".error_adress",
                message: "Enter the Digital_Marketing_coordinator",
            },
            {
                field: "#Digital_Marketing_Cost",
                error: ".error_adress",
                message: "Enter the Digital_Marketing_Cost",
            },
            {
                field: "#Digi_Days",
                error: ".error_adress",
                message: "Enter the Digi_Days",
            },
            {
                field: "#Total_Cost",
                error: ".error_adress",
                message: "Enter the Total_Cost",
            },
            {
                field: "#Budget_For_Auto",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Auto_Cost",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Auto_Days",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Auto_Total Cost",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Budget_For_Snacks",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Snacks_Cost",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Notices_Cost",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Notices_Count",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Banner_Cost",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#Banner_Count",
                error: ".error_adress",
                message: "Enter the Address",
            },
            {
                field: "#created_at",
                error: ".error_adress",
                message: "Enter the Address",
            },
        ];

        if ($("#Notices_img")[0].files.length === 0) {
            $(".error_adress").text("Please select the Images");
            isValid = false;
        }

        if ($("#Banner_img")[0].files.length === 0) {
            $(".error_adress").text("Please select the Images");
            isValid = false;
        }

        let isValid = true;

        validations.forEach(({ field, error, message }) => {
            if ($(field).val() === "") {
                $(error).text(message);
                isValid = false;
            } else {
                $(error).text(""); // Clear error if valid
            }
        });

        // Create FormData object
        let formData = new FormData();

        const files = $("#Notices_img")[0].files;
        const creativesFiles = $("#Banner_img")[0].files;

        for (let i = 0; i < files.length; i++) {
            formData.append("Notices_img[]", files[i]);
        }

        // Append multiple files from 'image_creatives_expenses'
        for (let i = 0; i < creativesFiles.length; i++) {
            formData.append("Banner_img[]", creativesFiles[i]);
        }

        formData.append("Branch", $("#Branch").val());
        formData.append("Camp_Date", $("#Camp_Date").val());
        formData.append("Camp_enddate", $("#Camp_enddate").val());
        formData.append("Camp_Centre_Name", $("#Camp_Centre_Name").val());
        formData.append("Camp_Location", $("#Camp_Location").val());
        formData.append(
            "Digital_Marketing_coordinator",
            $("#Digital_Marketing_coordinator").val()
        );
        formData.append(
            "Digital_Marketing_Cost",
            $("#Digital_Marketing_Cost").val()
        );
        formData.append("Digi_Days", $("#Digi_Days").val());
        formData.append("Total_Cost", $("#Total_Cost").val());
        formData.append("Budget_For_Auto", $("#Budget_For_Auto").val());
        formData.append("Auto_Cost", $("#Auto_Cost").val());
        formData.append("Auto_Days", $("#Auto_Days").val());
        formData.append("Auto_Total_Cost", $("#Auto_Total_Cost").val());
        formData.append("Budget_For_Snacks", $("#Budget_For_Snacks").val());
        formData.append("Snacks_Cost", $("#Snacks_Cost").val());
        formData.append("Notices_Cost", $("#Notices_Cost").val());
        formData.append("Notices_Count", $("#Notices_Count").val());
        formData.append("Banner_Cost", $("#Banner_Cost").val());
        formData.append("Banner_Count", $("#Banner_Count").val());
        formData.append("Camp_Executives", $("#Camp_Executives").val());
        formData.append("Dr_attended", $("#Dr_attended").val());

        // Include CSRF Token
        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
        // AJAX Request
        $.ajax({
            url: campdetailsadded,
            type: "POST",
            data: formData,
            processData: false, // Prevent processing of the data
            contentType: false, // Prevent setting content-type header
            success: function (response) {
                if (response.success) {
                    // alert(response.message);
                    // location.reload(); // Optional: Refresh the page
                     window.location.href = '/superadmin/camp';
                }
            },
            error: function (error) {
                console.error(error.responseJSON);
            },
        });
    });
    // Handle items per page change
    $("#itemsPerPagecamp").change(function () {
        var pageSizecamp = parseInt($(this).val());
        paginatecamps(dataSourceuserscamp, pageSizecamp);
        camptbl(dataSourceuserscamp, pageSizecamp, 1); // Initially show the first page
    });
    var fitterremovedata = []; // Keep this variable persistent
    $(document).on("click", ".camp_range, .camp_btn", function () {
        // Check if the click happened on a specific class
        if ($(this).hasClass("camp_range")) {
            var datefilltervalue = $("#datecampfitters").text(); // Get the current text value when '.ranges' is clicked
            var morefitterempty = $(".camp_views").text();
            if (morefitterempty == "") {
                campdatefitter(datefilltervalue);
            } else {
                campandoveralldata(fitterremovedata, datefilltervalue);
            }
        } else if ($(this).hasClass("camp_btn")) {
            var datefilltervaluenew = $(".camp_slct").text(); // Get the current text value when '.applyBtn' is clicked
            var dateRange = datefilltervaluenew.split(" - ");
            function convertDateFormat(dateStr) {
                let parts = dateStr.split("/");
                return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            var datefilltervalue = `${startDate} - ${endDate}`;
            var morefitterempty = $(".camp_views").text();
            if (morefitterempty == "") {
                campdatefitter(datefilltervalue);
            } else {
                campandoveralldata(fitterremovedata, datefilltervalue);
            }
        }
    });
    overallcampmanagement();
    // More fitter search.....
    var moreFilterValues = [];
    $("#morefitter_camp_search").on("click", function () {
        moredatefittervale = $("#datecampfitters").text();
        // alert(moredatefittervale);
        $(".clear_camp_views").show();
        $(".search_camp_view").show();
        var resultsArray = [];
        $(".campfitters").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr("name") + "='" + value + "'";
            resultsArray.push(results);
            fitterremovedata = resultsArray;
        });
        morefiltercamps(fitterremovedata, moredatefittervale);
        fitterremovedata;
        var moreFilterValues = [
            $("#Branch_more").val(),
            $("#camp_type_more").val(),
            $("#camp_incharge_more").val(),
            $("#organized_by_more").val(),
            $("#doctor_name_more").val(),
        ];
        $(".camp_views").each(function (index) {
            var morefillterdata = moreFilterValues[index]
                ? moreFilterValues[index]
                : ""; // Use "N/A" if value is empty
            $(this).text(morefillterdata);
        });
    });
    $(document).on("click", ".camp_views", function () {
        var morefillterremvedata = $(this).text();
        var datefilltervalue = $("#datecampfitters").text();
        $(this).text("");
        $('input[type="checkbox"]').each(function () {
            if (morefillterremvedata.includes($(this).val())) {
                $(this).prop("checked", false); // Uncheck the checkbox
            }
        });
        $(".marketervalues_search")
            .filter(function () {
                return $(this).val().startsWith(morefillterremvedata);
            })
            .val("");
        // Update the uniqueResults array to remove the corresponding filter
        fitterremovedata = fitterremovedata.filter(function (item) {
            return !item.endsWith(morefillterremvedata + "'");
        });
        campandoveralldataremove(fitterremovedata, datefilltervalue);
    });
    $(".mainclearallcamp").on("click", function () {
        $(".marketervalues_search").val("");
        $(".clear_camp_views").hide();
        $(".search_camp_view").hide();
        $(".camp_views").text("");
    });
    $(document).on("click", ".clear_camp_views", function () {
        $(".marketervalues_search").val("");
        $(".clear_camp_views").hide();
        $(".search_camp_view").hide();
        $(".camp_views").text("");

        var datefilltervalue = $("#datecampfitters").text();
        campdatefitter(datefilltervalue);
    });

    $(document).on("click", ".addactivites", function (e) {
        var userId = $(this).attr("value");
        var row = $(this).closest("tr");
        // Get data from the row
        var datefrmt = row.find("#dateviewssss").data("datefamat");
        // Find the corresponding row
        $("#camp_id_views").val(userId);
        $("#date_activites").val(datefrmt);
    });

    $(document).on("click", ".addexpensive", function (e) {
        var userIdexpensive = $(this).attr("value");
        var row = $(this).closest("tr");
        // Get data from the row
        var datefrmt = row.find("#dateviewssss").data("datefamat");
        // Find the corresponding row
        $("#Branch_expenses").val(userIdexpensive);
        $("#expensivedate").val(datefrmt);
    });

    // popup doctor details and image fetch data's
    $(document).on("click", ".acitivityclk", function (e) {
        var userId = $(this).attr("value");
        $.ajax({
            url: campactivitespopuop,
            type: "GET",
            data: {
                useridss: userId,
            },
            success: function (responseData) {
                if (!responseData || responseData.length === 0) {
                    Swal.fire({
                        toast: true, // Enables toast mode (small popup)
                        position: "top-end", // Places it in the top-right corner
                        icon: "error",
                        title: "Error!",
                        text: "No Activity Data",
                        showConfirmButton: false,
                        timer: 3000, // Auto-close after 3 seconds
                        timerProgressBar: true,
                        customClass: {
                            popup: "small-toast", // Custom class to reduce height
                        },
                        showClass: {
                            popup: "animate__animated animate__fadeInRight", // Smooth entry
                        },
                        hideClass: {
                            popup: "animate__animated animate__fadeOutRight", // Smooth exit
                        },
                    });

                    return;
                } else {
                    $("#exampleModal1").modal("show");
                }

                let allImages = [];
                var tblsviews = "";
                $.each(responseData, function (index, user) {
                    allImages = allImages.concat(user.Notices_img);
                });

                $("#meeting_popdata").html(tblsviews);

                if (allImages.length === 0) {
                    alert("No images available for this doctor.");
                    return;
                }

                var imageviewsss = allImages;
                let imageviewsvv = JSON.stringify(imageviewsss);
                // Remove unwanted characters like " ] [" if present
                imageviews = imageviewsvv.replace(/"\],\["/g, '","');

                // Split the cleaned string into an array of image paths
                var firstImage = imageviews.split(",")[0].trim();
                var imageNamefirst = firstImage.split("/").pop();

                firtsimgseee = imageNamefirst.replace(/["\\]/g, "");

                $("#main").attr(
                    "src",
                    "../public/camp_expenses/" + firtsimgseee
                );
                var imageArray = imageviews.split(",");
                // Extract the names of the images
                var views = "";
                imageArray.forEach(function (image) {
                    var imageName = image.trim().split("/").pop(); // Get the file name
                    //alert(imageName);
                    // Replace the src value of each image with a new one
                    views +=
                        '<img src="../public/camp_expenses/' + imageName + '">';
                });
                $("#thumbnails").html("");
                $("#thumbnails").html(views);
                var thumbnails = document.getElementById("thumbnails");
                var imgs = thumbnails.getElementsByTagName("img");
                var main = document.getElementById("main");
                var counter = 0;
                for (let i = 0; i < imgs.length; i++) {
                    let img = imgs[i];
                    img.addEventListener("click", function () {
                        main.src = this.src;
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
            },
        });
    });

    // popup doctor details and image fetch data's
    $(document).on("click", ".expensiveclk", function (e) {
        var userId = $(this).attr("value");
        $.ajax({
            url: campexpensivepopuop,
            type: "GET",
            data: {
                useridss: userId,
            },
            success: function (responseData) {
                if (!responseData || responseData.length === 0) {
                    Swal.fire({
                        toast: true, // Enables toast mode (small popup)
                        position: "top-end", // Places it in the top-right corner
                        icon: "error",
                        title: "Error!",
                        text: "No Activity Data",
                        showConfirmButton: false,
                        timer: 3000, // Auto-close after 3 seconds
                        timerProgressBar: true,
                        customClass: {
                            popup: "small-toast", // Custom class to reduce height
                        },
                        showClass: {
                            popup: "animate__animated animate__fadeInRight", // Smooth entry
                        },
                        hideClass: {
                            popup: "animate__animated animate__fadeOutRight", // Smooth exit
                        },
                    });

                    return;
                } else {
                    $("#exampleModal1").modal("show");
                }

                let allImagessss = [];
                $.each(responseData, function (index, user) {
                    allImagessss = allImagessss.concat(user.Banner_img);
                });

                if (allImagessss.length === 0) {
                    alert("No images available for this doctor.");
                    return;
                }

                var imageviewssss = allImagessss;
                let imageviewsvvv = JSON.stringify(imageviewssss);
                // Remove unwanted characters like " ] [" if present
                imageviews = imageviewsvvv.replace(/"\],\["/g, '","');

                // Split the cleaned string into an array of image paths
                var firstImage = imageviews.split(",")[0].trim();
                var imageNamefirst = firstImage.split("/").pop();

                firtsimgseee = imageNamefirst.replace(/["\\]/g, "");

                $(".viewssss").attr(
                    "src",
                    "../public/camp_expenses/" + firtsimgseee
                );
                var imageArray = imageviews.split(",");
                // Extract the names of the images
                var viewsexpensive = "";
                imageArray.forEach(function (image) {
                    var imageName = image.trim().split("/").pop(); // Get the file name
                    //alert(imageName);
                    // Replace the src value of each image with a new one
                    viewsexpensive +=
                        '<img src="../public/camp_expenses/' + imageName + '">';
                });
                $(".thumnike").html("");
                $(".thumnike").html(viewsexpensive);
                var thumbnails = document.getElementsByClassName("thumnike");
                var imgs = thumbnails.getElementsByTagName("img");
                var main = document.getElementById("main");
                var counter = 0;
                for (let i = 0; i < imgs.length; i++) {
                    let img = imgs[i];
                    img.addEventListener("click", function () {
                        main.src = this.src;
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
            },
        });
    });

    fitterremovedata = [];

    $(document).on("click", ".options_marketers div", function (e) {
        // Get the selected value and text
        var selectedValue = $(this).data("value");
        var selectedText = $(this).text();
        var moredatefittervale = $("#datecampfitters").text();

        $(".clear_camp_views").show();
        $(".search_camp_view").show();

        var resultsArray_marketer = [];
        $(".marketervalues_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing

            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr("name") + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#zoneviews").val(),
                $("#branchviews").val(),
                $("#act_name_views").val(),
            ];

            $(".camp_views").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index]
                    ? moreFilterValues_market[index]
                    : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });
        });

        marketersearchvalue = resultsArray_marketer;
        fitterremovedata = resultsArray_marketer;
        morefilterviewcamp(fitterremovedata, moredatefittervale);

        //alert(marketersearchvalue);
    });
});

var dataSourceuserscamp = [];
function overallcampmanagement() {
    $.ajax({
        url: campalldetails,
        type: "GET",
        success: handleSuccess,
        error: handleError,
    });
}
function morefiltercamps(fitterremovedata, moredatefittervale) {
    //alert(moredatefittervale);
    $.ajax({
        url: campfetchurlfitters,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            moredatefittervale: moredatefittervale,
        },
        success: handleSuccess,
        error: handleError,
    });
}
function campdatefitter(datefilltervalue) {
    $.ajax({
        url: supercampdatefitters,
        type: "GET",
        data: {
            datefilltervalue: datefilltervalue,
        },
        success: handleSuccess,
        error: handleError,
    });
}
function campandoveralldata(fitterremovedata, datefilltervalue) {
    $.ajax({
        url: campdateandsearchfitters,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            datefilltervalue: datefilltervalue,
        },
        success: handleSuccess,
        error: handleError,
    });
}
function campandoveralldataremove(fitterremovedata, datefilltervalue) {
    //alert(fitterremovedata);

    $.ajax({
        url: campdateandsearchfitters,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            datefilltervalue: datefilltervalue,
        },
        success: handleSuccess,
        error: handleError,
    });
}

function morefilterviewcamp(fitterremovedata, moredatefittervale) {
    // alert(fitterremovedata);
    $.ajax({
        url: campfetchurlfitters,
        type: "GET",
        data: {
            fitterremovedata: fitterremovedata,
            moredatefittervale: moredatefittervale,
        },
        success: handleSuccess,
        error: handleError,
    });
}



function handleSuccess(responseData) {
    // console.log(responseData);
    var raw = responseData != null ? responseData : [];
    dataSourceuserscamp = Array.isArray(raw) ? raw : (raw && raw.data != null ? raw.data : []);
    if (!Array.isArray(dataSourceuserscamp)) dataSourceuserscamp = [];
    totalItemscamp = dataSourceuserscamp.length; // Get total items count
    var pageSizecamp = parseInt($("#itemsPerPagecamp").val()); // Get selected items per page
    paginatecamps(dataSourceuserscamp, pageSizecamp);
    camptbl(dataSourceuserscamp, pageSizecamp, 1); // Show first page initially
}
function handleError(xhr, status, error) {
    console.error("AJAX Error:", status, error);
}










// Render table rows based on the page and page size

// function camptbl(data, pageSizecamp, pageNum) {
//     // console.log(data);
//     var startIdx = (pageNum - 1) * pageSizecamp;
//     var endIdx = pageNum * pageSizecamp;
//     var pageData = data.slice(startIdx, endIdx);
//     var body = "";
//     let camp_count = 0;
//     $.each(pageData, function (index, user) {
//         camp_count++;
//         //  console.log(pageData);
//         let timestamp = user.Camp_Date;
//         let dateStr = timestamp.split(" ")[0];
//         let [year, month, day] = dateStr.split("-");
//         let formattedDate = `${day}-${month}-${year}`;
//         let address = user.Camp_Location.split(" ").slice(0, 2).join(" ");
//         // Main row
//         body += `

//         <li class="items">
//                                     <div class="item-content">
//                                         <div class="d-flex">
//                                             <label class="item-name "><input type="checkbox" class="checkBtn" value="${user.id}" data-campid="${user.id}">
//                                             <div>${user.Camp_Centre_Name}</div></label>
//                                         </div>
//                                         <div class="item-units">${user.Camp_Location}</div>
//                                     </div>
//                                     <div class="box">
//                                         <span class="item-price">${formattedDate}</span>
//                                     </div>
//                                 </li>


//     `;
//     });

//     $("#camp_details").html(body);
//     $("#total_camps").text(camp_count);
//     $('.tabses').empty();
// }

function camptbl(data, pageSizecamp, pageNum) {
    var dataArr = Array.isArray(data) ? data : [];
    var startIdx = (pageNum - 1) * pageSizecamp;
    var endIdx = pageNum * pageSizecamp;
    var pageData = dataArr.slice(startIdx, endIdx);
    var body = "";
    let camp_count = 0;

    $.each(pageData, function (index, user) {
        camp_count++;
        let timestamp = user.Camp_Date;
        let dateStr = timestamp.split(" ")[0];
        let [year, month, day] = dateStr.split("-");
        let formattedDate = `${day}-${month}-${year}`;
        let address = user.Camp_Location.split(" ").slice(0, 2).join(" ");

        body += `
            <li class="items">
                <div class="item-content">
                    <div class="d-flex">
                        <label class="item-name">
                            <input type="checkbox" class="checkBtn" value="${user.id}" data-campid="${user.id}">
                            <div>${user.Camp_Centre_Name}</div>
                        </label>
                    </div>
                    <div class="item-units">${user.Camp_Location}</div>
                </div>
                <div class="box">
                    <span class="item-price">${formattedDate}</span>
                </div>
            </li>
        `;
    });

    $("#camp_details").html(body);
    $("#total_camps").text(camp_count);
    $('.tabses').empty();

    // ✅ Delay auto-click to ensure DOM is ready
    // setTimeout(() => {
    //     // autoClickFirstCheckbox();
    // }, 100);
}


$(document).ready(function () {
    // Checkbox click handler
    $(document).on("change", ".checkBtn", function () {
        // Uncheck all other checkboxes
        $(".checkBtn").not(this).prop("checked", false);

        if ($(this).is(":checked")) {
            var campId = $(this).data("campid");

            // Update the href attributes dynamically
            $(".activities_url").attr("href", `${campId}/addcampActivities`);
$(".leads_url").attr("href", `${campId}/addcamplead`);

            $.ajax({
                url: `/campaign/${campId}/details`,
                method: "GET",
                success: function(response){
                    $('.tabses').html(response);
                },
                error: function () {
                    $('.tabses').html("Failed to load details.");
                },
            });
        } else {
            $('.tabses').html("");
            // Optionally reset links if no checkbox is selected
            $(".activities_url").attr("href", "#");
            $(".leads_url").attr("href", "#");
        }
    });

    // Ensure checkboxes exist before triggering click
    function autoClickFirstCheckbox() {
        const $firstCheckbox = $(".checkBtn").first();
        if ($firstCheckbox.length) {
            $firstCheckbox.prop("checked", true).trigger("change");
        } else {
            // Retry if not loaded yet (for dynamic content)
            setTimeout(autoClickFirstCheckbox, 100);
        }
    }

    autoClickFirstCheckbox();
});


// $(document).ready(function () {
//     // Checkbox click handler
//     $(document).on("change", ".checkBtn", function () {
//         // Uncheck all other checkboxes
//         $(".checkBtn").not(this).prop("checked", false);

//         if ($(this).is(":checked")) {
//             var campId = $(this).data("campid");

//             $.ajax({
//                 url: `/campaign/${campId}/details`,
//                 method: "GET",
//                 success: function(response){
//                     $('.tabses').html(response);
//                 },
//                 error: function () {
//                     $('.tabses').html("Failed to load details.");
//                 },
//             });
//         } else {
//             $('.tabses').html("");
//         }
//     });

//     // Ensure checkboxes exist before triggering click
//     function autoClickFirstCheckbox() {
//         const $firstCheckbox = $(".checkBtn").first();
//         if ($firstCheckbox.length) {
//             $firstCheckbox.prop("checked", true).trigger("change");
//         } else {
//             // Retry if not loaded yet (for dynamic content)
//             setTimeout(autoClickFirstCheckbox, 100);
//         }
//     }

//     autoClickFirstCheckbox();
// });



// $(document).on("click", ".toggle-details-camps", function () {
//     // var campId = 13;
//     var campId = $(this).data("campid");
//     var $icon = $(this);
//     var $detailsRow = $('.details-row[data-id="' + campId + '"]');

//     if ($detailsRow.is(":visible")) {
//         $detailsRow.hide();
//         $icon.removeClass("fa-minus-circle").addClass("fa-plus-circle");
//     } else {
//         $detailsRow.show();
//         $icon.removeClass("fa-plus-circle").addClass("fa-minus-circle");

//         // Load data if not loaded yet
//         if (!$detailsRow.data("loaded")) {
//             $.ajax({
//                 url: `/campaign/${campId}/details`, // Adjust URL
//                 method: "GET",
//                 success: function (response) {
//                     let html = "";

//                     // Activities section
//                     html += "<div class='row'>";
//                     html += "<h5>Activities</h5>";

//                     if (
//                         Array.isArray(response.camp_activity) &&
//                         response.camp_activity.length > 0
//                     ) {
//                         response.camp_activity.forEach((activity) => {
//                             html += `<div class="col-lg-4">
//         <div class="card card-test mb-3 p-3 shadow-sm">
//           <h6>${activity.campa_name} (${activity.campa_days})</h6>
//           <p><strong>Budget:</strong> ${activity.campa_budget}</p>
//           <p><strong>Description:</strong> ${activity.campa_description}</p>
//           <p><strong>Login:</strong> ${activity.campa_login_time} - <strong>Logout:</strong> ${activity.campa_logout_time}</p>
//           <p><strong>Location:</strong> ${activity.campa_loc_track}</p>
//           <p><strong>Created By:</strong> ${activity.created_by}</p>
//       `;

//                             // Optional images (banner and notes)
//                             if (activity.campa_banner_img) {
//                                 try {
//                                     const banners = JSON.parse(
//                                         activity.campa_banner_img
//                                     );
//                                     banners.forEach((img) => {
//                                         html += `<img src="/storage/${img}" class="img-thumbnail me-2" style="max-width:150px;">`;
//                                     });
//                                 } catch (e) {}
//                             }

//                             if (activity.campa_notes_img) {
//                                 try {
//                                     const notes = JSON.parse(
//                                         activity.campa_notes_img
//                                     );
//                                     notes.forEach((img) => {
//                                         html += `<img src="/storage/${img}" class="img-thumbnail me-2" style="max-width:150px;">`;
//                                     });
//                                 } catch (e) {}
//                             }

//                             html += `</div></div>`; // end card
//                         });
//                     } else {
//                         html += "<p>No activities available.</p>";
//                     }

//                     html += "</div></div>"; // end activities section

//                     // Leads section
//                     html += "<div class='row mt-4'>";
//                     html += "<h5>Leads</h5>";

//                     if (
//                         Array.isArray(response.camp_lead) &&
//                         response.camp_lead.length > 0
//                     ) {
//                         response.camp_lead.forEach((lead) => {
//                             html += `
//                             <div class="col-lg-4">
//         <div class="card card-test mb-3 p-3 shadow-sm">
//           <h6>${lead.camp_name}</h6>
//           <p><strong>Husband:</strong> ${lead.camp_husband_name} (${lead.camp_husband_age} yrs)</p>
//           <p><strong>Wife:</strong> ${lead.camp_wife_name} (${lead.camp_wife_age} yrs)</p>
//           <p><strong>Email:</strong> ${lead.camp_email}</p>
//           <p><strong>Mobile:</strong> ${lead.camp_husband_mobile} / ${lead.camp_wife_mobile}</p>
//           <p><strong>Address:</strong> ${lead.camp_city}, ${lead.camp_state}, ${lead.camp_country} - ${lead.camp_zipcode}</p>
//           <p><strong>Preferred Call Time:</strong> ${lead.camp_prefered_call}</p>
//           <p><strong>Language:</strong> ${lead.camp_prefered_language}</p>
//         </div></div>
//       `;
//                         });
//                     } else {
//                         html += "<p>No leads available.</p>";
//                     }

//                     html += "</div>"; // end leads section

//                     $detailsRow.find(".details-content").html(html);
//                     $detailsRow.data("loaded", true);
//                 },

//                 error: function () {
//                     $detailsRow
//                         .find(".details-content")
//                         .html("Failed to load details.");
//                 },
//             });
//         }
//     }
// });

// function paginatecamps(data, pageSizecamp) {
//     var totalPages = Math.ceil(data.length / pageSizecamp);
//     var paginationHtml = "";
//     for (var i = 1; i <= totalPages; i++) {
//         paginationHtml +=
//             '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' +
//             i +
//             '">' +
//             i +
//             "</button>";
//     }
//     $("#paginationcamp").html(paginationHtml);
//     // Bind click event to each pagination button
//     $(".page-btnviewss").click(function () {
//         var pageNum = $(this).data("page");
//         $(".page-btnviewss").removeClass("active");
//         $(this).addClass("active");
//         camptbl(data, pageSizecamp, pageNum);
//     });
// }
function paginatecamps(data, pageSizecamp, currentPage = 1) {
    var totalPages = Math.ceil(data.length / pageSizecamp);
    var paginationHtml = "";

    // Always show first 3 pages, last page, and current +/- 1 page
    function shouldShowPage(i) {
        return (
            i <= 2 || // first 3 pages
            i === totalPages || // last page
            Math.abs(i - currentPage) <= 1 // pages around current
        );
    }

    var prevWasHidden = false;
    for (var i = 1; i <= totalPages; i++) {
        if (shouldShowPage(i)) {
            paginationHtml +=
                '<button class="page-btnviewss ' +
                (i === currentPage ? "active" : "") +
                '" style="background-color:#b163a6;" data-page="' +
                i +
                '">' +
                i +
                "</button>";
            prevWasHidden = false;
        } else if (!prevWasHidden) {
            paginationHtml += '<span class="ellipsis">...</span>';
            prevWasHidden = true;
        }
    }

    $("#paginationcamp").html(paginationHtml);

    // Bind click event to each pagination button
    $(".page-btnviewss").click(function () {
        var pageNum = $(this).data("page");
        paginatecamps(data, pageSizecamp, pageNum); // re-render buttons with currentPage
        camptbl(data, pageSizecamp, pageNum); // show data
    });
}
