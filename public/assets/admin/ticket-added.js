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

    $('#submit-ticket-datas').click(function (event) {
        event.preventDefault();

        // Validation checks
        let isValid = true;

        if ($('#location').val() === "") {
            $('.error_location').text('Please enter the location');
            isValid = false;
        }
		
        if ($('#department_id').val() === "") {
            $('.error_department').text('Please enter the department');
            isValid = false;
        }
		
        if ($('#sub_department_id').val() === "") {
            $('.error_sub_department').text('Enter enter the sub department');
            isValid = false;
        }

        if ($('#targetDate').val() === "") {
            $('.error_target_date').text('Enter the target date');
            isValid = false;
        }
		
        if ($('#ticket_priority').val() === "") {
            $('.error_priority').text('Enter the priority');
            isValid = false;
        }
		
        if ($('#ticket_subject').val() === "") {
            $('.error_subject').text('Enter the subject');
            isValid = false;
        }
		
        if ($('#ticket_description').val() === "") {
            $('.error_description').text('Enter the description');
            isValid = false;
        }
		
		if ($('#image_uploads')[0].files.length === 0) {
            $('.error_images').text('Please select the Images');
            isValid = false;
        }

        if (!isValid) {
            return; // Stop the form submission if validation fails
        }
		
		
        // Create FormData object
        let formData = new FormData();
		
        formData.append('location_id', $('#location').val());
        formData.append('department_id', $('#department_id').val());
        formData.append('sub_department_id', $('#sub_department_id').val());
        formData.append('target_date', $('#targetDate').val());
        formData.append('subject', $('#ticket_subject').val());
        formData.append('description', $('#ticket_description').val());
        formData.append('priority', $('#ticket_priority').val());
		
		// Add images to FormData
        const files = $('#image_uploads')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }

        // Include CSRF Token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // AJAX Request
        $.ajax({
            url: addedUrl,
            type: "POST",
            data: formData,
            processData: false, // Prevent processing of the data
            contentType: false, // Prevent setting content-type header
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Optional: Refresh the page
                }
            },
            error: function (error) {
                console.error(error.responseJSON);
            },
        });
    });
});
