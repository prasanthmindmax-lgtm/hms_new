$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });
    // Clear error span text on input
    $('input, select, textarea').on('input change', function () {
        $(this).siblings('span').text('');
    });
    $('#close-button').click(function () {
        // Clear all input fields
        $('input, select, textarea').val('');
        // Clear error messages
        $('span').text('');
    });
    $('#users-datas').click(function (event) {
        event.preventDefault();
        // Validation checks
        let isValid = true;
        // Validate doctor name
        if ($('#user_fullname').val() === "") {
            $('.error_doctor').text('Enter the Doctor Name');
            isValid = false;
        }
        // Validate employee name
        if ($('#username').val() === "") {
            $('.error_employee').text('Please select the Employee Name');
            isValid = false;
        }
        // Validate specialty
        if ($('#password').val() === "") {
            $('.error_special').text('Please select the Specialization');
            isValid = false;
        }
        // Validate specialty
        if ($('#role').val() === "") {
            $('.error_special').text('Please select the Specialization');
            isValid = false;
        }
        // Validate specialty
        if ($('#email').val() === "") {
            $('.error_special').text('Please select the Specialization');
            isValid = false;
        }
        // Validate specialty
        if ($('#mobile').val() === "") {
            $('.error_special').text('Please select the Specialization');
            isValid = false;
        }
        if (!isValid) {
            return; // Stop the form submission if validation fails
        }
        // Create FormData object
        let formData = new FormData();
        formData.append('user_fullname', $('#user_fullname').val());
        formData.append('username', $('#username').val());
        formData.append('password', $('#password').val());
        formData.append('role', $('#role').val());
        formData.append('email', $('#email').val());
        formData.append('mobile', $('#mobile').val());
        // Add images to FormData
        // Include CSRF Token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        // AJAX Request
        $.ajax({
            url: addusermanagement,
            type: "POST",
            data: formData,
            processData: false, // Prevent processing of the data
            contentType: false, // Prevent setting content-type header
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Optional: Refresh the page
                    overallusersview();
                }
            },
            error: function (error) {
                console.error(error.responseJSON);
            },
        });
    });
     // Handle items per page change
     $('#itemsPerPageSelectusers').change(function() {
        var pageSizeviews = parseInt($(this).val());
        paginateviews(dataSourceusersall, pageSizeviews);
        tblviews(dataSourceusersall, pageSizeviews, 1);  // Initially show the first page
    });
    overallusersview();
});
var dataSourceusersall = [];
function overallusersview()
{
    $.ajax({
        url: userviewsall,
        type: "GET",
        success: function (responseData) {
           // console.log(responseData);
            dataSourceusersall = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizeviews = parseInt($('#itemsPerPageSelectusers').val()); // Get selected items per page
            paginateviews(dataSourceusersall, pageSizeviews);
            tblviews(dataSourceusersall, pageSizeviews, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
// Render table rows based on the page and page size
function tblviews(data, pageSizeviews, pageNum) {
    var startIdx = (pageNum - 1) * pageSizeviews;
    var endIdx = pageNum * pageSizeviews;
    var pageData = data.slice(startIdx, endIdx);
    //console.log(pageData);
    var body = "";
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let dateStrupdate = user.updated_at;
        let formattedDate = moment(dateStr).format("| DD MMM YYYY | HH:MM");
        let ipdateformattedDate = moment(dateStrupdate).format("| DD MMM YYYY | HH:MM");
        body += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" > <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview" > Full Name : ' + user.user_fullname + '<br> Login ID : '+ user.username +'<br></td>' +
                '<td class="tdview" > Email : ' + user.email + '<br> Mobile : '+ user.mobile +'<br></td>' +
                '<td class="tdview" > Active </td>' +
                '<td class="tdview" > Craeted At : ' + formattedDate+ '<br> Updated At : '+ ipdateformattedDate +'<br></td>' +
                '<td class="tdview" > Role : ' + user.role + '<br> Created by : '+ user.created_by +'<br></td>' +
               '</tr>';  });
    $("#usersdetails").html(body);
}
function paginateviews(data, pageSizeviews) {
    var totalPages = Math.ceil(data.length / pageSizeviews);
    var paginationHtml = '';
    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
    }
    $('#paginationusers').html(paginationHtml);
    // Bind click event to each pagination button
    $('.page-btnviewss').click(function() {
        var pageNum = $(this).data('page');
        $('.page-btnviewss').removeClass('active');
        $(this).addClass('active');
        tblviews(data, pageSizeviews, pageNum);
    });
}
