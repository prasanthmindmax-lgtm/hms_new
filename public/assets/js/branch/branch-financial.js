// ================================================
// BRANCH FINANCIAL REPORTS - COMPLETE JAVASCRIPT
// WITH NEW FEATURES: Date Range, Radiant Not Collected, UPI, Deposit
// ================================================

$(document).ready(function() {
    
    let editMode = false;
    let currentEditId = null;
    let fileStorage = {
        radiant_collection: [],
        radiant_ledger_book: [],
        actual_card: [],
        upi: [],              // NEW
        deposit: [],          // NEW
        bank_deposit: [],
        cashier_info: [],
        additional_amounts: []
    };

    /** Public URL for files stored as branch_financial_files/... */
    function publicFileUrl(filePath) {
        if (!filePath) { return '#'; }
        const p = String(filePath).replace(/^\/+/, '');
        return encodeURI('/' + p);
    }
    
    // ================================================
    // DATE RANGE PICKER FOR RADIANT COLLECTION
    // ================================================
    // $('#radiant_date_range').daterangepicker({
    //     autoUpdateInput: false,
    //     locale: {
    //         cancelLabel: 'Clear',
    //         format: 'DD/MM/YYYY'
    //     }
    // });
    
    // $('#radiant_date_range').on('apply.daterangepicker', function(ev, picker) {
    //     $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        
    //     // Set hidden from/to dates
    //     $('#radiant_collection_from_date').val(picker.startDate.format('YYYY-MM-DD'));
    //     $('#radiant_collection_to_date').val(picker.endDate.format('YYYY-MM-DD'));
        
    //     // Set hidden collection date (use start date)
    //     $('#radiant_collected_date').val(picker.startDate.format('YYYY-MM-DD'));
    // });
    
    // $('#radiant_date_range').on('cancel.daterangepicker', function(ev, picker) {
    //     $(this).val('');
    //     $('#radiant_collection_from_date').val('');
    //     $('#radiant_collection_to_date').val('');
    //     $('#radiant_collected_date').val('');
    // });
    

    // ================================================
    // DATE RANGE PICKER FOR RADIANT COLLECTION (FLATPICKR)
    // ================================================
    flatpickr("#radiant_date_range", {
        mode: "range",
        dateFormat: "d/m/Y",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                // Set hidden from/to dates
                $('#radiant_collection_from_date').val(moment(selectedDates[0]).format('YYYY-MM-DD'));
                $('#radiant_collection_to_date').val(moment(selectedDates[1]).format('YYYY-MM-DD'));
                
                // Set hidden collection date (use start date)
                $('#radiant_collected_date').val(moment(selectedDates[0]).format('YYYY-MM-DD'));
            } else if (selectedDates.length === 0) {
                $('#radiant_collection_from_date').val('');
                $('#radiant_collection_to_date').val('');
                $('#radiant_collected_date').val('');
            }
        },
        onClear: function() {
            $('#radiant_collection_from_date').val('');
            $('#radiant_collection_to_date').val('');
            $('#radiant_collected_date').val('');
        }
    });

    // ================================================
    // RADIANT NOT COLLECTED CHECKBOX HANDLER
    // ================================================
    $('#radiant_not_collected').on('change', function() {
        if ($(this).is(':checked')) {
            // Show remarks container
            $('#radiantRemarksContainer').slideDown();
            $('#radiant_not_collected_remarks').attr('required', true);
            
            // Disable other radiant fields
            $('#radiant_date_range').prop('disabled', true).css('background-color', '#f0f0f0');
            $('#radiant_collection_amount').prop('disabled', true).css('background-color', '#f0f0f0');
            $('#radiant_collection_files').prop('disabled', true);
            $('#radiant_ledger_book_files').prop('disabled', true);
        } else {
            // Hide remarks container
            $('#radiantRemarksContainer').slideUp();
            $('#radiant_not_collected_remarks').attr('required', false).val('');
            
            // Enable other radiant fields
            $('#radiant_date_range').prop('disabled', false).css('background-color', '');
            $('#radiant_collection_amount').prop('disabled', false).css('background-color', '');
            $('#radiant_collection_files').prop('disabled', false);
            $('#radiant_ledger_book_files').prop('disabled', false);
        }
    });
    
    // ================================================
    // OPEN MODAL FOR NEW REPORT
    // ================================================
    $('#openModalBtn').on('click', function() {
        resetForm();
        editMode = false;
        currentEditId = null;
        $('#modalTitle').html('<i class="fas fa-file-invoice-dollar"></i> New Financial Report');
        $('#reportModal').addClass('active');
    });
    
    // ================================================
    // CLOSE MODAL
    // ================================================
    $('#closeModalBtn, #cancelBtn').on('click', function() {
        $('#reportModal').removeClass('active');
    });
    
    // ================================================
    // ZONE SELECTION - UPDATE HIDDEN INPUT
    // ================================================
    $('#zone_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const zoneName = selectedOption.data('name');
        $('#zone_name').val(zoneName);
        
        // Filter branches by zone
        const zoneId = $(this).val();
        $('#branch_id option').hide();
        $('#branch_id option[value=""]').show();
        
        if (zoneId) {
            $(`#branch_id option[data-zone="${zoneId}"]`).show();
        } else {
            $('#branch_id option').show();
        }
        
        $('#branch_id').val('');
        $('#branch_name').val('');
    });
    
    // ================================================
    // BRANCH SELECTION - UPDATE HIDDEN INPUT
    // ================================================
    $('#branch_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const branchName = selectedOption.data('name');
        $('#branch_name').val(branchName);
    });
    
    // ================================================
    // FILE PREVIEW
    // ================================================
    function handleFilePreview(input, previewContainer) {
        const files = Array.from(input.files);
        const fieldName = input.id.replace('_files', '');
        
        // Store files
        fileStorage[fieldName] = files;
        
        // Clear preview
        $(previewContainer).empty();
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const isImage = file.type.startsWith('image/');
                let previewHtml = '';
                
                if (isImage) {
                    previewHtml = `
                        <div class="preview-item">
                            <img src="${e.target.result}" alt="${file.name}">
                            <button type="button" class="remove-file" data-field="${fieldName}" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                } else {
                    previewHtml = `
                        <div class="preview-item" style="display:flex;align-items:center;justify-content:center;background:#f7fafc;">
                            <i class="fas fa-file" style="font-size:30px;color:#667eea;"></i>
                            <button type="button" class="remove-file" data-field="${fieldName}" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }
                
                $(previewContainer).append(previewHtml);
            };
            
            reader.readAsDataURL(file);
        });
    }
    
    // Attach file preview handlers
    $('#radiant_collection_files').on('change', function() {
        handleFilePreview(this, '#radiant_collection_preview');
    });
    
    $('#radiant_ledger_book_files').on('change', function() {
        handleFilePreview(this, '#radiant_ledger_book_preview');
    });
    
    $('#actual_card_files').on('change', function() {
        handleFilePreview(this, '#actual_card_preview');
    });
    
    $('#upi_files').on('change', function() {
        handleFilePreview(this, '#upi_preview');
    });
    
    $('#deposit_files').on('change', function() {
        handleFilePreview(this, '#deposit_preview');
    });
    
    $('#bank_deposit_files').on('change', function() {
        handleFilePreview(this, '#bank_deposit_preview');
    });
    
    $('#cashier_info_files').on('change', function() {
        handleFilePreview(this, '#cashier_info_preview');
    });
    
    $('#additional_amounts_files').on('change', function() {
        handleFilePreview(this, '#additional_amounts_preview');
    });
    
    // ================================================
    // REMOVE FILE FROM PREVIEW
    // ================================================
    $(document).on('click', '.remove-file', function() {
        const field = $(this).data('field');
        const index = $(this).data('index');
        
        // Remove from storage
        fileStorage[field].splice(index, 1);
        
        // Update file input
        const input = $(`#${field}_files`)[0];
        const dt = new DataTransfer();
        fileStorage[field].forEach(file => dt.items.add(file));
        input.files = dt.files;
        
        // Remove preview item
        $(this).closest('.preview-item').remove();
    });
    
    function parseMoney(el) {
        const v = parseFloat(String($(el).val() || '0').replace(/,/g, ''));
        return isFinite(v) ? v : 0;
    }
    
    function hasExistingPreviews(previewSelector) {
        return $(previewSelector + ' .existing-file').length > 0;
    }
    
    function hasNewFiles(key) {
        return fileStorage[key] && fileStorage[key].length > 0;
    }
    
    /** Client-side: amounts &gt; 0 require uploads (or existing files in edit mode). */
    function validateRequiredAttachments() {
        const notCollected = $('#radiant_not_collected').is(':checked');
        const rAmt = parseMoney('#radiant_collection_amount');
        if (!notCollected && rAmt > 0) {
            if (!hasNewFiles('radiant_collection') && !hasExistingPreviews('#radiant_collection_preview')) {
                return 'Radiant cash: upload at least one file under Collection proof, or keep existing files.';
            }
            if (!hasNewFiles('radiant_ledger_book') && !hasExistingPreviews('#radiant_ledger_book_preview')) {
                return 'Radiant cash: ledger book copy is required when the collection amount is greater than 0.';
            }
        }
        if (parseMoney('#deposit_amount') > 0) {
            if (!hasNewFiles('deposit') && !hasExistingPreviews('#deposit_preview')) {
                return 'Deposit: attachment is required when the amount is greater than 0.';
            }
        }
        if (parseMoney('#actual_card_amount') > 0) {
            if (!hasNewFiles('actual_card') && !hasExistingPreviews('#actual_card_preview')) {
                return 'Actual card: attachment is required when the amount is greater than 0.';
            }
        }
        if (parseMoney('#upi_amount') > 0) {
            if (!hasNewFiles('upi') && !hasExistingPreviews('#upi_preview')) {
                return 'UPI: attachment is required when the amount is greater than 0.';
            }
        }
        if (parseMoney('#bank_deposit_amount') > 0) {
            if (!hasNewFiles('bank_deposit') && !hasExistingPreviews('#bank_deposit_preview')) {
                return 'Direct bank deposit: attachment is required when the amount is greater than 0.';
            }
        }
        return null;
    }
    
    // ================================================
    // SUBMIT FORM
    // ================================================
    $('#submitBtn').on('click', function() {
        // Validation first
        if (!$('#report_date').val()) {
            showAlert('Error', 'Please select report date', 'error');
            return;
        }
        
        if (!$('#zone_id').val()) {
            showAlert('Error', 'Please select zone', 'error');
            return;
        }
        
        if (!$('#branch_id').val()) {
            showAlert('Error', 'Please select branch', 'error');
            return;
        }
        
        // Check if radiant not collected checkbox is checked and remarks is empty
        if ($('#radiant_not_collected').is(':checked') && !$('#radiant_not_collected_remarks').val().trim()) {
            showAlert('Error', 'Please provide remarks for not collecting radiant', 'error');
            $('#radiant_not_collected_remarks').focus();
            return;
        }
        
        if (!$('#acknowledgement_agreed').is(':checked')) {
            showAlert('Error', 'Please agree to the acknowledgement', 'error');
            $('#acknowledgement_agreed').focus();
            return;
        }
        
        const attErr = validateRequiredAttachments();
        if (attErr) {
            showAlert('Error', attErr, 'error');
            return;
        }
        
        const formData = new FormData();
        
        // Basic info
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('report_date', $('#report_date').val());
        formData.append('zone_id', $('#zone_id').val());
        formData.append('zone_name', $('#zone_name').val());
        formData.append('branch_id', $('#branch_id').val());
        formData.append('branch_name', $('#branch_name').val());
        
        // Radiant collection (WITH NEW FIELDS)
        formData.append('radiant_collected_date', $('#radiant_collected_date').val() || '');
        formData.append('radiant_collection_from_date', $('#radiant_collection_from_date').val() || '');
        formData.append('radiant_collection_to_date', $('#radiant_collection_to_date').val() || '');
        formData.append('radiant_collection_amount', $('#radiant_collection_amount').val() || 0);
        formData.append('radiant_not_collected', $('#radiant_not_collected').is(':checked') ? 1 : 0);
        formData.append('radiant_not_collected_remarks', $('#radiant_not_collected_remarks').val() || '');
        fileStorage.radiant_collection.forEach(file => {
            formData.append('radiant_collection_files[]', file);
        });
        fileStorage.radiant_ledger_book.forEach(file => {
            formData.append('radiant_ledger_book_files[]', file);
        });
        
        // Actual card
        formData.append('actual_card_amount', $('#actual_card_amount').val() || 0);
        fileStorage.actual_card.forEach(file => {
            formData.append('actual_card_files[]', file);
        });
        
        // UPI (NEW)
        formData.append('upi_amount', $('#upi_amount').val() || 0);
        fileStorage.upi.forEach(file => {
            formData.append('upi_files[]', file);
        });
        
        // Deposit (NEW)
        formData.append('deposit_date', $('#deposit_date').val() || '');
        formData.append('deposit_amount', $('#deposit_amount').val() || 0);
        fileStorage.deposit.forEach(file => {
            formData.append('deposit_files[]', file);
        });
        
        // Bank deposit
        formData.append('bank_deposit_amount', $('#bank_deposit_amount').val() || 0);
        fileStorage.bank_deposit.forEach(file => {
            formData.append('bank_deposit_files[]', file);
        });
        
        // Cashier info
        formData.append('placed_by_whom', $('#placed_by_whom').val());
        formData.append('locker_by_whom', $('#locker_by_whom').val());
        formData.append('who_gave_radiant_cash', $('#who_gave_radiant_cash').val());
        formData.append('cash_in_drawer', $('#cash_in_drawer').val() || 0);
        fileStorage.cashier_info.forEach(file => {
            formData.append('cashier_info_files[]', file);
        });
        
        // Additional amounts
        formData.append('today_discount_amount', $('#today_discount_amount').val() || 0);
        formData.append('cancel_bill_amount', $('#cancel_bill_amount').val() || 0);
        formData.append('refund_bill_amount', $('#refund_bill_amount').val() || 0);
        formData.append('pos_refund_amount', $('#pos_refund_amount').val() || 0);
        fileStorage.additional_amounts.forEach(file => {
            formData.append('additional_amounts_files[]', file);
        });
        
        // Acknowledgement
        formData.append('acknowledgement_agreed', $('#acknowledgement_agreed').is(':checked') ? 1 : 0);
        
        // Determine URL using route names
        let url = storeRoute;
        
        if (editMode && currentEditId) {
            url = updateRoute.replace(':id', currentEditId);
            formData.append('_method', 'PUT');
        }
        
        // Show loading
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Submit
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Report');
                
                if (response.success) {
                    $('#reportModal').removeClass('active');
                    showAlert('Success', response.message, 'success');
                    
                    // Reload table via AJAX instead of full page reload
                    setTimeout(function() {
                        if (typeof reloadTable === 'function') {
                            reloadTable();
                        } else {
                            location.reload();
                        }
                    }, 1000);
                } else {
                    showAlert('Error', response.message || 'Failed to save report', 'error');
                }
            },
            error: function(xhr) {
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Report');
                
                console.error('Error:', xhr);
                
                let errorMessage = 'An error occurred while saving';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                showAlert('Error', errorMessage, 'error');
            }
        });
    });
    
    // ================================================
    // EDIT REPORT
    // ================================================
    $(document).on('click', '.btn-edit', function() {
        const reportId = $(this).data('id');
        
        // Show loading
        showAlert('Loading', 'Please wait...', 'info', false);
        
        const url = showRoute.replace(':id', reportId);
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    populateEditForm(response.data);
                    editMode = true;
                    currentEditId = reportId;
                    $('#modalTitle').html('<i class="fas fa-edit"></i> Edit Financial Report');
                    $('#reportModal').addClass('active');
                } else {
                    showAlert('Error', 'Failed to load report data', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                showAlert('Error', 'Failed to load report data', 'error');
            }
        });
    });
    
    // ================================================
    // POPULATE EDIT FORM (UPDATED WITH NEW FIELDS)
    // ================================================
    function populateEditForm(data) {
        $('#reportId').val(data.id);
        $('#report_date').val(data.report_date);
        
        // Zone and branch
        $('#zone_id').val(data.zone_id).trigger('change');
        setTimeout(() => {
            $('#branch_id').val(data.branch_id).trigger('change');
        }, 100);
        
        // Radiant collection (WITH NEW FIELDS)
        $('#radiant_collected_date').val(data.radiant_collected_date);
        $('#radiant_collection_amount').val(data.radiant_collection_amount);
        
        // Date range
        if (data.radiant_collection_from_date && data.radiant_collection_to_date) {
            const fromDate = moment(data.radiant_collection_from_date).format('DD/MM/YYYY');
            const toDate = moment(data.radiant_collection_to_date).format('DD/MM/YYYY');
            $('#radiant_date_range').val(fromDate + ' - ' + toDate);
            $('#radiant_collection_from_date').val(data.radiant_collection_from_date);
            $('#radiant_collection_to_date').val(data.radiant_collection_to_date);
        }
        
        // Radiant not collected
        $('#radiant_not_collected').prop('checked', data.radiant_not_collected == 1);
        if (data.radiant_not_collected == 1) {
            $('#radiantRemarksContainer').show();
            $('#radiant_not_collected_remarks').val(data.radiant_not_collected_remarks);
            $('#radiant_date_range').prop('disabled', true).css('background-color', '#f0f0f0');
            $('#radiant_collection_amount').prop('disabled', true).css('background-color', '#f0f0f0');
            $('#radiant_collection_files').prop('disabled', true);
            $('#radiant_ledger_book_files').prop('disabled', true);
        } else {
            $('#radiant_ledger_book_files').prop('disabled', false);
        }
        
        showExistingFiles(data.radiant_collection_files, '#radiant_collection_preview', 'Radiant Collection');
        const ledgerJson = data.radiant_ledger_book_files !== undefined ? data.radiant_ledger_book_files : null;
        if (ledgerJson) {
            showExistingFiles(ledgerJson, '#radiant_ledger_book_preview', 'Ledger book');
        } else {
            $('#radiant_ledger_book_preview').empty();
        }
        
        // Actual card
        $('#actual_card_amount').val(data.actual_card_amount);
        showExistingFiles(data.actual_card_files, '#actual_card_preview', 'Actual Card');
        
        // UPI (NEW)
        $('#upi_amount').val(data.upi_amount);
        showExistingFiles(data.upi_files, '#upi_preview', 'UPI Files');
        
        // Deposit (NEW)
        $('#deposit_date').val(data.deposit_date);
        $('#deposit_amount').val(data.deposit_amount);
        showExistingFiles(data.deposit_files, '#deposit_preview', 'Deposit Files');
        
        // Bank deposit
        $('#bank_deposit_amount').val(data.bank_deposit_amount);
        showExistingFiles(data.bank_deposit_files, '#bank_deposit_preview', 'Bank Deposit');
        
        // Cashier info
        $('#placed_by_whom').val(data.placed_by_whom);
        $('#locker_by_whom').val(data.locker_by_whom);
        $('#who_gave_radiant_cash').val(data.who_gave_radiant_cash);
        $('#cash_in_drawer').val(data.cash_in_drawer);
        showExistingFiles(data.cashier_info_files, '#cashier_info_preview', 'Cashier Info');
        
        // Additional amounts
        $('#today_discount_amount').val(data.today_discount_amount);
        $('#cancel_bill_amount').val(data.cancel_bill_amount);
        $('#refund_bill_amount').val(data.refund_bill_amount);
        $('#pos_refund_amount').val(data.pos_refund_amount);
        showExistingFiles(data.additional_amounts_files, '#additional_amounts_preview', 'Additional Amounts');
        
        // Acknowledgement
        $('#acknowledgement_agreed').prop('checked', data.acknowledgement_agreed == 1);
    }
    
    // ================================================
    // SHOW EXISTING FILES IN EDIT MODE
    // ================================================
    function showExistingFiles(filesJson, previewContainer, title) {
        if (!filesJson) return;
        
        let files = [];
        try {
            files = typeof filesJson === 'string' ? JSON.parse(filesJson) : filesJson;
        } catch (e) {
            console.error('Error parsing files:', e);
            return;
        }
        
        if (!files || files.length === 0) return;
        
        $(previewContainer).empty();
        
        files.forEach((filePath, index) => {
            const ext = filePath.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
            
            let previewHtml = '';
            
            if (isImage) {
                previewHtml = `
                    <div class="preview-item existing-file" data-file="${filePath}">
                        <img src="${publicFileUrl(filePath)}" alt="File">
                        <button type="button" class="view-file-btn" style="position:absolute;bottom:5px;right:5px;background:rgba(102,126,234,0.9);border:none;color:white;padding:5px 10px;border-radius:5px;cursor:pointer;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                `;
            } else {
                previewHtml = `
                    <div class="preview-item existing-file" data-file="${filePath}" style="display:flex;align-items:center;justify-content:center;background:#f7fafc;flex-direction:column;">
                        <i class="fas fa-file" style="font-size:30px;color:#667eea;"></i>
                        <button type="button" class="view-file-btn" style="background:#667eea;border:none;color:white;padding:5px 10px;border-radius:5px;cursor:pointer;margin-top:10px;">
                            <i class="fas fa-download"></i> View
                        </button>
                    </div>
                `;
            }
            
            $(previewContainer).append(previewHtml);
        });
    }
    
    // ================================================
    // VIEW EXISTING FILE
    // ================================================
    $(document).on('click', '.view-file-btn', function(e) {
        e.stopPropagation();
        const filePath = $(this).closest('.existing-file').data('file');
        window.open(publicFileUrl(filePath), '_blank');
    });
    
    // ================================================
    // DELETE REPORT
    // ================================================
    $(document).on('click', '.btn-delete', function() {
        const reportId = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                container: 'swal-on-top'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const url = destroyRoute.replace(':id', reportId);
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Deleted!', response.message, 'success');
                            
                            // Reload table via AJAX
                            setTimeout(function() {
                                if (typeof reloadTable === 'function') {
                                    reloadTable();
                                } else {
                                    location.reload();
                                }
                            }, 1000);
                        } else {
                            showAlert('Error', response.message || 'Failed to delete', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showAlert('Error', 'Failed to delete report', 'error');
                    }
                });
            }
        });
    });
    
    // ================================================
    // VIEW FILE PREVIEW FROM TABLE
    // ================================================
    $(document).on('click', '.file-preview-trigger', function() {
        const filesJson = $(this).data('files');
        const title = $(this).data('title');
        
        let files = [];
        try {
            files = typeof filesJson === 'string' ? JSON.parse(filesJson) : filesJson;
        } catch (e) {
            console.error('Error parsing files:', e);
            return;
        }
        
        if (!files || files.length === 0) {
            showAlert('Info', 'No files attached', 'info');
            return;
        }
        
        openFileViewModal(files, title);
    });
    
    $(document).on('click', '.file-preview-all-trigger', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let groups = $(this).data('groups');
        if (typeof groups === 'string') {
            try { groups = JSON.parse(groups); } catch (err) { groups = []; }
        }
        openGroupedFileViewModal(groups || []);
    });
    
    // ================================================
    // OPEN FILE VIEW MODAL
    // ================================================
    function openFileViewModal(files, title) {
        let filesHtml = '';
        const fileList = Array.isArray(files) ? files : [];
        
        fileList.forEach((filePath) => {
            const u = publicFileUrl(filePath);
            const ext = filePath.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
            const isPdf = ext === 'pdf';
            const baseName = filePath.split('/').pop() || 'file';
            const safeName = String(baseName).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
            
            if (isImage) {
                filesHtml += `
                    <div style="margin-bottom:20px;">
                        <img src="${u}" style="max-width:100%;border-radius:10px;" alt="${safeName}">
                        <div style="margin-top:10px;">
                            <a href="${u}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> Open
                            </a>
                            <a href="${u}" download="${baseName}" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                `;
            } else if (isPdf) {
                filesHtml += `
                    <div style="margin-bottom:20px;">
                        <iframe src="${u}" style="width:100%;height:500px;border:1px solid #ddd;border-radius:10px;"></iframe>
                        <div style="margin-top:10px;">
                            <a href="${u}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> Open
                            </a>
                            <a href="${u}" download class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                `;
            } else {
                filesHtml += `
                    <div style="padding:20px;background:#f7fafc;border-radius:10px;margin-bottom:15px;text-align:center;">
                        <i class="fas fa-file" style="font-size:50px;color:#667eea;"></i>
                        <p style="margin-top:10px;">${safeName}</p>
                        <a href="${u}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                            <i class="fas fa-external-link-alt"></i> Open
                        </a>
                        <a href="${u}" download class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                `;
            }
        });
        
        Swal.fire({
            title: title,
            html: filesHtml,
            width: '800px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                container: 'swal-on-top'
            }
        });
    }
    
    /**
     * Grouped "all attachments" from table (several sections in one modal).
     */
    function openGroupedFileViewModal(groups) {
        if (!Array.isArray(groups) || !groups.length) {
            showAlert('Info', 'No files attached', 'info');
            return;
        }
        let html = '';
        groups.forEach((g) => {
            const paths = g.files && g.files.length ? g.files : [];
            if (!paths.length) { return; }
            html += `<h6 style="margin:16px 0 8px;border-bottom:1px solid #e2e8f0;padding-bottom:6px;color:#2d3748;">${(g.title || 'Files').toString()}</h6>`;
            html += '<div style="padding-left:8px;">';
            paths.forEach((filePath) => {
                const u = publicFileUrl(filePath);
                const ext = filePath.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
                const isPdf = ext === 'pdf';
                const name = filePath.split('/').pop();
                if (isImage) {
                    html += `<div style="margin-bottom:12px;"><img src="${u}" style="max-width:100%;max-height:220px;object-fit:contain;border-radius:8px;" alt=""><br><a href="${u}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">${name}</a></div>`;
                } else if (isPdf) {
                    html += `<div style="margin-bottom:12px;"><iframe src="${u}" style="width:100%;height:360px;border:1px solid #ddd;border-radius:8px;"></iframe><a href="${u}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">${name}</a></div>`;
                } else {
                    html += `<div style="margin:8px 0;"><a href="${u}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-file"></i> ${name}</a></div>`;
                }
            });
            html += '</div>';
        });
        Swal.fire({
            title: 'All attachments',
            html: html || '<p class="text-muted">No files</p>',
            width: '860px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: { container: 'swal-on-top' }
        });
    }
    
    // ================================================
    // VIEW EDIT HISTORY
    // ================================================
    $(document).on('click', '.edit-badge', function() {
        const historyJson = $(this).data('history');
        
        let history = [];
        try {
            history = typeof historyJson === 'string' ? JSON.parse(historyJson) : historyJson;
        } catch (e) {
            console.error('Error parsing history:', e);
            return;
        }
        
        if (!history || history.length === 0) {
            showAlert('Info', 'No edit history', 'info');
            return;
        }
        
        let historyHtml = '<div style="text-align:left;">';
        history.forEach((item, index) => {
            historyHtml += `
                <div style="padding:10px;background:#f7fafc;border-radius:8px;margin-bottom:10px;">
                    <strong>Edit #${index + 1}</strong><br>
                    <small>By: ${item.edited_by_name}</small><br>
                    <small>At: ${item.edited_at}</small>
                </div>
            `;
        });
        historyHtml += '</div>';
        
        Swal.fire({
            title: 'Edit History',
            html: historyHtml,
            icon: 'info',
            customClass: {
                container: 'swal-on-top'
            }
        });
    });
    
    // ================================================
    // CUSTOM ALERT FUNCTION
    // ================================================
    function showAlert(title, message, icon, showConfirmButton = true) {
        Swal.fire({
            title: title,
            html: message,
            icon: icon,
            showConfirmButton: showConfirmButton,
            customClass: {
                container: 'swal-on-top'
            },
            timer: icon === 'info' && !showConfirmButton ? 1500 : undefined
        });
    }
    
    // ================================================
    // RESET FORM (UPDATED WITH NEW FIELDS)
    // ================================================
    function resetForm() {
        $('#reportForm')[0].reset();
        $('#reportId').val('');
        $('.file-preview').empty();
        
        // Reset file storage
        fileStorage = {
            radiant_collection: [],
            radiant_ledger_book: [],
            actual_card: [],
            upi: [],
            deposit: [],
            bank_deposit: [],
            cashier_info: [],
            additional_amounts: []
        };
        
        // Reset file inputs
        $('#radiant_collection_files').val('');
        $('#radiant_ledger_book_files').val('');
        $('#actual_card_files').val('');
        $('#upi_files').val('');
        $('#deposit_files').val('');
        $('#bank_deposit_files').val('');
        $('#cashier_info_files').val('');
        $('#additional_amounts_files').val('');
        
        // Reset date range
        $('#radiant_date_range').val('');
        $('#radiant_collection_from_date').val('');
        $('#radiant_collection_to_date').val('');
        $('#radiant_collected_date').val('');
        
        // Reset radiant not collected
        $('#radiant_not_collected').prop('checked', false);
        $('#radiant_not_collected_remarks').val('');
        $('#radiantRemarksContainer').hide();
        $('#radiant_date_range').prop('disabled', false).css('background-color', '');
        $('#radiant_collection_amount').prop('disabled', false).css('background-color', '');
        $('#radiant_collection_files').prop('disabled', false);
        $('#radiant_ledger_book_files').prop('disabled', false);
        
        // Reset acknowledgement
        $('#acknowledgement_agreed').prop('checked', false);
    }
    
    // ================================================
    // CLOSE MODAL ON OUTSIDE CLICK
    // ================================================
    $(document).on('click', '.modal-overlay', function(e) {
        if (e.target === this) {
            $(this).removeClass('active');
        }
    });
    
});