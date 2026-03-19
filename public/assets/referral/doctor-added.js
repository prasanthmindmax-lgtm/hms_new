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
        // $('input, select, textarea').val('');
        // Clear error messages
        // $('span').text('');
    });
    $('#submit-doctor-datas').click(function (event) {
        event.preventDefault();

        // Validation checks
        let isValid = true;
        // Validate doctor name
        if ($('#doctor_name').val() === "") {
            $('.error_doctor').text('Enter the Doctor Name');
            isValid = false;
        }
        // Validate employee name
        if ($('#empolyee_name').val() === "") {
            $('.error_employee').text('Please select the Employee Name');
            isValid = false;
        }
        // Validate specialty
        if ($('#specialfitters').val() === "") {
            $('.error_special').text('Please select the Specialization');
            isValid = false;
        }
        // Validate hospital name
        if ($('#hopsital_name').val() === "") {
            $('.error_hplname').text('Enter the hospital name');
            isValid = false;
        }
        // Validate address
        if ($('#address').val() === "") {
            $('.error_adress').text('Enter the Address');
            isValid = false;
        }
        // Validate city
        if ($('#city').val() === "") {
            $('.error_city').text('Enter the city');
            isValid = false;
        }
        // Validate doctor contact number
        if ($('#doc_contact').val() === "") {
            $('.error_docontact').text('Enter the Doctor num');
            isValid = false;
        }
        // Validate hospital contact number
        if ($('#hpl_contact').val() === "") {
            $('.error_hplcontact').text('Enter the Hospital num');
            isValid = false;
        }
         // Validate hospital contact number
         if ($('#hospital_link').val() === "") {
            $('.error_hospital_link').text('Enter the Hospital link');
            isValid = false;
        }
          // Validate hospital contact number
          if ($('#map_link').val() === "") {
            $('.error_map_link').text('Enter the map Link');
            isValid = false;
        }
        // Validate image uploads
        if ($('#image_uploads')[0].files.length === 0) {
            $('.error_images').text('Please select the Images');
            isValid = false;
        }
        if (!isValid) {
            return; // Stop the form submission if validation fails
        }
        // Create FormData object
        let formData = new FormData();
        formData.append('doctor_name', $('#doctor_name').val());
        formData.append('empolyee_name', $('#empolyee_name').val());
        formData.append('special', $('#specialfitters').val());
        formData.append('hopsital_name', $('#hopsital_name').val());
        formData.append('address', $('#address').val());
        formData.append('city', $('#city').val());
        formData.append('doc_contact', $('#doc_contact').val());
        formData.append('hpl_contact', $('#hpl_contact').val());
        formData.append('hospital_link', $('#hospital_link').val());
        formData.append('map_link', $('#map_link').val());
        // Add images to FormData
        const files = $('#image_uploads')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }
        // Include CSRF Token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        // AJAX Request
         console.log("addedUrl:",addedUrl);
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
