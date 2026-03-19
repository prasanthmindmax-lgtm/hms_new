// customer dashboar js
 $(document).ready(function() {
        var customerId="";
        // Handle row click
        $('.customer-row').click(function() {
            // Highlight selected row
            $('.customer-row').removeClass('selected');
            $(this).addClass('selected');

            // Get customer data from data attributes
             customerId = $(this).data('id');
            const customerName = $(this).data('name');
            const customerEmail = $(this).data('email');
            const customerPhone = $(this).data('phone');
            const customerCompany = $(this).data('company');
            const customerBalance = $(this).data('balance');
            const customerCurrency = $(this).data('currency');
            const customerPan = $(this).data('pan');
            const customerType = $(this).data('type');
            const customerpayment_terms= $(this).data('payment_terms');
            const customerLanguage = $(this).data('portal_language');
            const billing_address = JSON.parse($(this).attr('data-billingaddress'));
            const shippingAddress = JSON.parse($(this).attr('data-shippingaddress'));
            const contacts = JSON.parse($(this).attr('data-contacts'));
            const created_at = $(this).data('created_at');
            const created_date = created_at.split(" ")[0];
            const custype=(customerType==0)?'Business':'Individual';
            const billingaddress = `
                ${billing_address.address}</br>
                ${billing_address.city}</br>
                ${billing_address.state}</br>
                ${billing_address.country}</br>
                ${billing_address.zip_code}</br>
                ${billing_address.phone}
            `;
            const shipping_address = `
                ${shippingAddress.address}</br>
                ${shippingAddress.city}</br>
                ${shippingAddress.state}</br>
                ${shippingAddress.country}</br>
                ${shippingAddress.zip_code}</br>
                ${shippingAddress.phone}
            `;
            let contact = ""; // Use let instead of const

            contacts.forEach(element => {
                contact += `
                    <div class="contact-person">
                        <span class="contact-name">${element.first_name} ${element.last_name}</span>
                        <span class="contact-phone">${element.mobile}</span>
                    </div>
                `;
            });

            // Update modal content
            $('.contact-header').text(customerCompany);
            $('.contact-name').text(customerName);
            $('.contact-email').text(customerEmail);
            $('.contact-phone').text(customerPhone);
            $('.created_date').text(created_date);
            $('.customerid').text(customerId);
            $('.cust_payment_term').text(customerpayment_terms);
            $('.billing-address').html(billingaddress);
            $('.shipping-address').html(shipping_address);
            $('.contact-persons-body').html(contact);
            $('#customertype').text(custype);
            $('#customer-balance').text('₹' + customerBalance.toFixed(2));
            $('#customer-currency-display').text(customerCurrency + ' - ' + (customerCurrency === 'INR' ? 'Indian Rupee' : ''));
            $('#pannumber').text(customerPan);
            $('#currency').text(customerCurrency);
            $('.customer_currency').text(customerCurrency);
            $('#customer_language').text(customerLanguage);
            $('#customer-type').text(customerType);
            $('#total-income').text('Total Income (Last 6 Months) - ₹' + customerBalance.toFixed(2));
            updateIncomeChart(customerBalance);
            // Show modal
            $('#customerModal').show();
            $('#customerModalBackdrop').show();
            $('body').css('overflow', 'hidden');
        });
       $('.edit_btn').on('click', function() {
            window.location.href = "{{ route('superadmin.getcustomercreate') }}" + "?id=" + customerId;
        });


        // Close modal
        $('#closeCustomerModal, #customerModalBackdrop').click(function() {
            $('#customerModal').hide();
            $('#customerModalBackdrop').hide();
            $('body').css('overflow', 'auto');
        });

        // Tab switching
        $('.customer-tab').click(function() {
            const tabId = $(this).data('tab');

            // Update active tab
            $('.customer-tab').removeClass('active');
            $(this).addClass('active');

            // Show corresponding content
            $('.tab-content').hide();
            $('#' + tabId + '-tab').show();
        });

        // Prevent modal close when clicking inside
        $('#customerModal').click(function(e) {
            e.stopPropagation();
        });
    });
        let incomeChart = null;

        function updateIncomeChart(balance) {
            const ctx = document.getElementById('incomeChart');

            // Sample data - replace with your actual data
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            const amounts = [0, 0, 0, 0, 0, balance/1000]; // Assuming last month has the balance

            // If chart exists, update its data
            if (incomeChart) {
                incomeChart.data.datasets[0].data = amounts;
                incomeChart.update();
                return;
            }

            // Otherwise, create new chart
            incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Income',
                        data: amounts,
                        backgroundColor: '#4a6cf7',
                        borderColor: '#4a6cf7',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value + 'k';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₹' + (context.raw * 1000).toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }
        $('#closeCustomerModal, #customerModalBackdrop').click(function() {
            // Clean up chart if it exists
            if (incomeChart) {
                incomeChart.destroy();
                incomeChart = null;
            }

            $('#customerModal').hide();
            $('#customerModalBackdrop').hide();
            $('body').css('overflow', 'auto');
        });
        function toggleSection(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.toggle-arrow');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Rotate arrow
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }
         function toggleDetails(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.toggle-arrow');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Rotate arrow
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }
        function toggleContactSection(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.contact-toggle-icon');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Update arrow icon
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }
          function toggleRecordInfo(header) {
            const content = header.nextElementSibling;
            const arrow = header.querySelector('.record-toggle-arrow');

            // Toggle content visibility
            content.classList.toggle('collapsed');

            // Update arrow icon
            if (content.classList.contains('collapsed')) {
                arrow.textContent = '▶';
            } else {
                arrow.textContent = '▼';
            }
        }

// customer create js

            // If it's still a stringified JSON, parse it
            if (typeof existingDocuments === 'string') {
                try {
                    existingDocuments = JSON.parse(existingDocuments);
                } catch (e) {
                    existingDocuments = [];
                    console.error("Invalid JSON in documents field", e);
                }
            }

            $(document).ready(function () {
                const fileList = $('#file-list');

                existingDocuments.forEach(function (file, index) {
                    const extension = file.split('.').pop().toLowerCase();
                    const isPdf = extension === 'pdf';

                    const fileItem = $(`
                        <div class="file-item d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <i class="fas ${isPdf ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary'} me-2"></i>
                                <a href="/path/to/uploaded/files/${file}" target="_blank">${file}</a>
                            </div>
                            <button class="btn btn-sm btn-outline-danger remove-existing-file" data-filename="${file}" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);

                    fileList.append(fileItem);
                });

                $(document).on('click', '.remove-existing-file', function () {
                    const filename = $(this).data('filename');
                    $(this).parent().remove();

                    const removedInput = $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'removed_documents[]')
                        .val(filename);
                    $('#file-upload-container').append(removedInput);
                });
            });
             $(document).ready(function () {
                if (isEditMode) {
                    // Edit mode — show only the existing contact rows (already in your existing code)
                    if (existingContacts.length > 0) {
                        if ($('#contact-persons-header').length === 0) {
                            const headerRow = $(`
                                            <div class="row contact-person-header" id="contact-persons-header">
                                                <div class="col-md-2 col-sm-2"><label class="customer-form-label">Salutation</label></div>
                                                <div class="col-md-1 col-sm-1"><label class="customer-form-label">First Name</label></div>
                                                <div class="col-md-1 col-sm-1"><label class="customer-form-label">Last Name</label></div>
                                                <div class="col-md-2 col-sm-1"><label class="customer-form-label">Email Address</label></div>
                                                <div class="col-md-2 col-sm-1"><label class="customer-form-label">Work Phone</label></div>
                                                <div class="col-md-1 col-sm-1"><label class="customer-form-label">Mobile</label></div>
                                                <div class="col-md-1 col-sm-1"><label class="customer-form-label">Action</label></div>
                                            </div>
                                        `);
                            $('#contact-persons-container').prepend(headerRow);
                        }

                        existingContacts.forEach((contact, index) => {
                            const contactRow = $(`
                                            <div class="row contact-person-row">
                                                <div class="col-md-1 col-sm-1">
                                                    <div class="form-group">
                                                        <input type="hidden" class="form-control"  name="contact_persons[${index}][id]" value="${contact.id}">
                                                        <select class="form-select" name="contact_persons[${index}][salutation]">
                                                            <option value="Mr." ${contact.salutation === 'Mr.' ? 'selected' : ''}>Mr.</option>
                                                            <option value="Mrs." ${contact.salutation === 'Mrs.' ? 'selected' : ''}>Mrs.</option>
                                                            <option value="Ms." ${contact.salutation === 'Ms.' ? 'selected' : ''}>Ms.</option>
                                                            <option value="Dr." ${contact.salutation === 'Dr.' ? 'selected' : ''}>Dr.</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-1">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" placeholder="First Name" name="contact_persons[${index}][first_name]" value="${contact.first_name}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-1">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" placeholder="Last Name" name="contact_persons[${index}][last_name]" value="${contact.last_name}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-1">
                                                    <div class="form-group">
                                                        <input type="email" class="form-control" placeholder="Email Address" name="contact_persons[${index}][email]" value="${contact.email}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-1">
                                                    <div class="form-group">
                                                        <input type="tel" class="form-control" placeholder="Work Phone" name="contact_persons[${index}][work_phone]" value="${contact.work_phone}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-1">
                                                    <div class="form-group">
                                                        <input type="tel" class="form-control" placeholder="Mobile" name="contact_persons[${index}][mobile]" value="${contact.mobile}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-1">
                                                    <div class="remove-contact text-center">
                                                        <i class="fas fa-times"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        `);
                            $('#contact-persons-container').append(contactRow);
                        });
                    }
                } else {
                    // Create mode — add one empty row
                    $('#add-contact-person').trigger('click');
                }
            });

        $(document).ready(function() {
            // Hide additional details container initially
            $('#additional-details-container').hide();
            $('.remove-additional-details').hide();

            // Tab switching functionality
            $('.close').on('click', function() {
                window.location.href = "{{ route('superadmin.getcustomer') }}";
            });

            $('.form-tab').on('click', function() {
                $('.form-tab').removeClass('active');
                $(this).addClass('active');
                const tabId = $(this).data('tab');
                $('.tab-content').removeClass('active');
                $('#' + tabId).addClass('active');
            });

          // Copy billing address to shipping address
            $('#copy-billing').on('click', function() {
                // Copy each field individually to ensure proper mapping
                $('[name="billing_attention"]').val() ? $('[name="shipping_attention"]').val($('[name="billing_attention"]').val()) : '';
                $('[name="billing_country"]').val() ? $('[name="shipping_country"]').val($('[name="billing_country"]').val()) : '';
                $('[name="billing_address"]').val() ? $('[name="shipping_address"]').val($('[name="billing_address"]').val()) : '';
                $('[name="billing_city"]').val() ? $('[name="shipping_city"]').val($('[name="billing_city"]').val()) : '';
                $('[name="billing_state"]').val() ? $('[name="shipping_state"]').val($('[name="billing_state"]').val()) : '';
                $('[name="billing_zip_code"]').val() ? $('[name="shipping_zip_code"]').val($('[name="billing_zip_code"]').val()) : '';
                $('[name="billing_phone"]').val() ? $('[name="shipping_phone"]').val($('[name="billing_phone"]').val()) : '';
                $('[name="billing_fax"]').val() ? $('[name="shipping_fax"]').val($('[name="billing_fax"]').val()) : '';

                // Visual feedback
                const $btn = $(this);
                $btn.html('<i class="fas fa-check"></i> Copied!');
                $btn.addClass('copied');

                setTimeout(() => {
                    $btn.html('<i class="fas fa-copy"></i> Copy from Billing');
                    $btn.removeClass('copied');
                }, 2000);
            });
            // Add contact person functionality
            $('#add-contact-person').on('click', function() {
                // Add header row if it doesn't exist
                if ($('#contact-persons-header').length === 0) {
                    const headerRow = $(`
                        <div class="row contact-person-header" id="contact-persons-header">
                            <div class="col-md-2 col-sm-2">
                                <div class="form-group">
                                    <label class="customer-form-label">Salutation</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">First Name</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Last Name</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Email Address</label>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Work Phone</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Mobile</label>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <div class="form-group">
                                    <label class="customer-form-label">Action</label>
                                </div>
                            </div>
                        </div>
                    `);
                    $('#contact-persons-container').prepend(headerRow);
                }

                // Generate a unique index for the contact person
                const contactIndex = $('.contact-person-row').length;

                // Add new contact person row
                const contactRow = $(`
                    <div class="row contact-person-row">
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <select class="form-select" name="contact_persons[${contactIndex}][salutation]">
                                    <option value="Mr.">Mr.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Dr.">Dr.</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="First Name" name="contact_persons[${contactIndex}][first_name]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Last Name" name="contact_persons[${contactIndex}][last_name]">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-1">
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Email Address" name="contact_persons[${contactIndex}][email]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="tel" class="form-control" placeholder="Work Phone" name="contact_persons[${contactIndex}][work_phone]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="form-group">
                                <input type="tel" class="form-control" placeholder="Mobile" name="contact_persons[${contactIndex}][mobile]">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-1">
                            <div class="remove-contact text-center">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                `);

                contactRow.find('.remove-contact').on('click', function() {
                    $(this).closest('.contact-person-row').remove();
                    // Remove header if no more contact rows exist
                    if ($('.contact-person-row').length === 0) {
                        $('#contact-persons-header').remove();
                    }
                });

                $('#contact-persons-container').append(contactRow);
            });
            $(document).on('click', '.remove-contact', function () {
                $(this).closest('.contact-person-row').remove();

                // Remove header if no more rows
                if ($('.contact-person-row').length === 0) {
                    $('#contact-persons-header').remove();
                }
            });

            $('#file-upload-container').on('click', function(e) {
                // Prevent triggering when clicking on child elements (like the button)
                if (e.target === this) {
                    $('#file-upload-input').click();
                }
            });

            // Click handler for the upload button
            $('#upload-button').on('click', function(e) {
                e.stopPropagation(); // Prevent triggering the container click
                $('#file-upload-input').click();
            });

            // Handle file selection
            $('#file-upload-input').on('change', function() {
                const files = this.files;
                const fileList = $('#file-list');
                fileList.empty();

                if (files.length > 10) {
                    alert('You can upload a maximum of 10 files');
                    $(this).val('');
                    return;
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];

                    // Check file size (10MB limit)
                    if (file.size > 10 * 1024 * 1024) {
                        alert(`File "${file.name}" exceeds the 10MB limit`);
                        continue;
                    }

                    // Check file type
                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert(`File "${file.name}" is not a valid type (PDF, JPG, PNG, GIF)`);
                        continue;
                    }

                    // Display file info
                    const fileItem = $(`
                        <div class="file-item d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <i class="fas ${file.type === 'application/pdf' ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary'} me-2"></i>
                                <span>${file.name}</span>
                                <small class="text-muted ms-2">(${(file.size / (1024 * 1024)).toFixed(2)} MB)</small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger remove-file" data-index="${i}" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);

                    fileList.append(fileItem);
                }

                // Remove file handler
                $('.remove-file').on('click', function(e) {
                    e.stopPropagation();
                    const index = $(this).data('index');
                    const files = $('#file-upload-input')[0].files;
                    const newFiles = Array.from(files).filter((_, i) => i !== index);

                    // Create new FileList (since we can't modify the original)
                    const dataTransfer = new DataTransfer();
                    newFiles.forEach(file => dataTransfer.items.add(file));
                    $('#file-upload-input')[0].files = dataTransfer.files;

                    $(this).parent().remove();
                });
            });

            $('#add-more-details').on('click', function() {
                $('#additional-details-container').show();
                // Disable the add button after adding once
                $(this).hide();
                $('.remove-additional-details').show();
            });

            // Remove additional details
            $(document).on('click', '.remove-additional-details', function() {
                $('#additional-details-container').hide();
                $(this).hide();
                $('#add-more-details').show();
            });


            // Handle Save button click
            $('#save-customer').on('click', function() {
                // Create FormData object to handle file uploads
                const formData = new FormData();
                let isValid = true;

                    if ($('#first_name').val() === "") {
                        $('.error_first_name').text('Name Required');
                        isValid = false;
                    }
                    if ($('#display_name').val() === "") {
                        $('.error_display_name').text('Enter the Display Name');
                        isValid = false;
                    }
                    let email = $('#email').val().trim();
                    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (email === "") {
                        $('.error_email').text('Enter Your Email');
                        isValid = false;
                    } else if (!emailPattern.test(email)) {
                        $('.error_email').text('Enter a valid Email Address');
                        isValid = false;
                    } else {
                        $('.error_email').text('');
                    }

                    // Phone number validation
                    let phone = $('#phone_number').val().trim();
                    let phonePattern = /^[6-9]\d{9}$/;

                    if (phone === "") {
                        $('.error_phone_number').text('Enter the Mobile Number');
                        isValid = false;
                    } else if (!phonePattern.test(phone)) {
                        $('.error_phone_number').text('Enter a valid 10-digit Mobile Number');
                        isValid = false;
                    } else {
                        $('.error_phone_number').text('');
                    }


                    if ($('#pan_number').val() === "") {
                        $('.error_pan_number').text('Enter Pan Number');
                        isValid = false;
                    }

                    let panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                    let panNumber = $('#pan_number').val().toUpperCase();
                    if (!panPattern.test(panNumber)) {
                        $('.error_pan_number').text('Invalid PAN Number');
                        isValid = false;
                    } else {
                        $('.error_pan_number').text('');
                    }



                    if (!isValid) {
                        return;
                    }

                // Append all form data
                $('input, select, textarea').each(function() {
                    if ($(this).attr('type') !== 'file' && $(this).attr('type') !== 'button' && $(this).attr('type') !== 'submit') {
                        const name = $(this).attr('name');
                        const value = $(this).val();

                        if (name) {
                            // Handle checkboxes
                            if ($(this).attr('type') === 'checkbox' ||$(this).attr('type') === 'radio') {
                                formData.append(name, $(this).is(':checked') ? '1' : '0');
                            } else {
                                formData.append(name, value);
                            }
                        }
                    }
                });

                // Append files
                const fileInput = $('#file-upload-input')[0];
                if (fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('documents[]', fileInput.files[i]);
                    }
                }

                // Show loading indicator
                const saveBtn = $(this);
                saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                // Send data to server
                $.ajax({
                    url: '{{ route("superadmin.savecustomer") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                         toastr.success(response.message);
                        // Handle success
                        saveBtn.html('<i class="fas fa-check"></i> Saved!').removeClass('btn-success').addClass('btn-primary');
                        setTimeout(() => {
                            window.location.href = "{{ route('superadmin.getcustomer') }}";
                        }, 1500);
                    },
                    error: function(xhr) {
                        // Handle error
                        saveBtn.prop('disabled', false).html('Save');
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMessage = xhr.responseText;
                        }
                        alert(errorMessage);
                    }
                });
            });
        });











