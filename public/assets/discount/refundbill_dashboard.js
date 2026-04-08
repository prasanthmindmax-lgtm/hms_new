$(document).ready(function () {
    // Initialize default location selection from old refund form
    var fitterremovedata = [];
    if(fitterremovedata.length == 0){
        var defaultLocation = "Chennai - Sholinganallur";
        var defaultZone = "TN CHENNAI";
        $('#ref_loc_views').val(defaultLocation);
        $('#ref_zone_views').val(defaultZone);
        $('.dropdown-options div').each(function() {
            if ($(this).text().trim() === defaultZone){
                $(this).addClass('selected');
            }
        });
        $('#getlocation > div').each(function(){
            if ($(this).text().trim() === defaultLocation) {
                $(this).addClass('selected');
            }
        });
    }

    $(".my_search_view").hide();
    $(".my_search_saveview").hide();
    refundformdata();
    refundsaveformdata();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    function hmsFormSaveBtnLoading($btn, loading) {
        if (!$btn || !$btn.length) return;
        if (loading) {
            if (!$btn.data('hms-orig-html')) $btn.data('hms-orig-html', $btn.html());
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
            );
        } else {
            $btn.prop('disabled', false).html($btn.data('hms-orig-html') || $btn.html());
            $btn.removeData('hms-orig-html');
        }
    }

    // Clear error span text on input
    $('input, select, textarea').on('input change', function () {
        $(this).siblings('span').text('');
    });

    $('#close-button').click(function () {
        $('input, select, textarea').val('');
        $('span').text('');
        $('#form_error_box').hide().find('#form_error_list').empty();
    });

    $('.dropdown-item-loc').on('click', function () {
        var selectedBranchName = $(this).text();
        var selectedBranchId = $(this).data('value');
        $('#zone_id').val(selectedBranchName);
        $('#zone_id').attr('data-value', selectedBranchId);
    });

    $('.dropdown-item-loc_edit').on('click', function () {
        var selectedBranchName = $(this).text();
        var selectedBranchId = $(this).data('value');
        $('#edit_zone_id').val(selectedBranchName);
        $('#edit_zone_id').attr('data-value', selectedBranchId);
        $('#locationid').val(selectedBranchId);
    });

    // =====================================================
    // TAB SWITCHING
    // =====================================================
    $('.nav-tab').on('click', function () {
        const tab = $(this).data('tab');
        $('.nav-tab').removeClass('active');
        $(this).addClass('active');

        if (tab === 'pending') {
            $('.pending_overview').show();
            $('.saved_overview').hide();
            refundformdata();
        } else if (tab === 'saved') {
            $('.pending_overview').hide();
            $('.saved_overview').show();
            refundsaveformdata();
        }
    });

    // =====================================================
    // DATA FETCHING
    // =====================================================
    refundformdata();
    refundsaveformdata();

    // =====================================================
    // FORM VALIDATION (Add Form)
    // =====================================================
    function validateRefundAddForm() {
        var errors = [];
        if (!$('#zone_id').attr('data-value') && !$('#zone_id').val().trim()) {
            errors.push('Branch Name is required.');
        }
        if (!$('#wife_name').val().trim()) errors.push('Wife Name is required.');
        if (!$('#wifemrdno').val().trim()) errors.push('Wife MRD No is required.');
        if (!$('#husband_name').val().trim()) errors.push('Husband Name is required.');
        if (!$('#husbandmrdno').val().trim()) errors.push('Husband MRD No is required.');
        if (!$('#service_name').val().trim()) errors.push('Treatment Category is required.');
        if (!$('#totalbill').val().trim()) errors.push('Total Bill Value is required.');
        if (!$('#expected_request').val().trim()) errors.push('Refund Expected Request is required.');
        if (!$('input[name="expectdis"]:checked').length) errors.push('Please select Amount or Percentage for expected discount.');
        if (!$('input[name="request"]:checked').length) errors.push('Please select Request type (OP/IP/Pharmacy).');
        if (!$('#counselled_by').val().trim()) errors.push('Counselled By is required.');
        if (!$('#patientph').val().trim()) errors.push('Patient Phone is required.');
        if (!$('#final_amount').val().trim()) errors.push('Final Authorised Amount is required.');
        if (!$('#branch_no').val().trim()) errors.push('B.R. No is required.');
        if (!$('#authourised_by').val().trim()) errors.push('Authorised By is required.');
        if (!$('#approveded_by').val().trim()) errors.push('Final Approved By is required.');

        var signIds = { wife: 'imageUpload', husband: 'husbandsignimg', dr: 'drsignimg', admin: 'adminsignimg', cc: 'ccsignimg' };
        ['wife', 'husband', 'dr', 'admin', 'cc'].forEach(function (t) {
            var isUpload = $('input[name="' + t + '-signature"]:checked').val() === 'upload';
            var hasSign = false;
            if (isUpload) {
                var fid = signIds[t];
                hasSign = document.getElementById(fid) && document.getElementById(fid).files && document.getElementById(fid).files.length > 0;
            } else {
                var canvas = document.getElementById(t + 'Canvas');
                if (canvas) {
                    var ctx = canvas.getContext('2d');
                    var imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    for (var i = 3; i < imgData.data.length; i += 4) { if (imgData.data[i] > 0) { hasSign = true; break; } }
                }
            }
            if (!hasSign) {
                var lbl = (t === 'cc' ? 'CC (Zonal)' : t.charAt(0).toUpperCase() + t.slice(1)) + ' Sign';
                errors.push(lbl + ' is required.');
            }
        });

        if (errors.length > 0) {
            $('#form_error_list').empty();
            errors.forEach(function (e) { $('#form_error_list').append('<li>' + e + '</li>'); });
            $('#form_error_box').show();
            return false;
        }
        $('#form_error_box').hide();
        return true;
    }

    // =====================================================
    // SUBMIT REFUND FORM (ADD)
    // =====================================================
    $('#submit_refundform').click(function (event) {
        event.preventDefault();
        if (!validateRefundAddForm()) return;

        var $refundAddBtn = $('#submit_refundform');
        if ($refundAddBtn.prop('disabled')) return;
        hmsFormSaveBtnLoading($refundAddBtn, true);

        let formData = new FormData();
        let zoneId = $('#zone_id').attr('data-value');
        formData.append('ref_zone_id', zoneId);
        formData.append('ref_wife_name', $('#wife_name').val());
        formData.append('ref_wife_mrd_no', $('#wifemrdno').val());
        formData.append('ref_husband_name', $('#husband_name').val());
        formData.append('ref_husband_mrd_no', $('#husbandmrdno').val());
        formData.append('ref_service_name', $('#service_name').val());
        formData.append('ref_total_bill', $('#totalbill').val());
        formData.append('ref_expected_request', $('#expected_request').val());
        formData.append('ref_form_status', $('input[name="request"]:checked').val() || '');
        formData.append('ref_counselled_by', $('#counselled_by').val());
        formData.append('ref_final_auth', $('#final_amount').val());
        formData.append('ref_branch_no', $('#branch_no').val());
        formData.append('ref_auth_by', $('#authourised_by').val());
        formData.append('ref_patient_ph', $('#patientph').val());
        formData.append('ref_approveded_by', $('#approveded_by').val());

        const signers = [
            { field: 'ref_wife_sign', radioName: 'wife-signature', canvasId: 'wifeCanvas', fileInputId: 'imageUpload' },
            { field: 'ref_husband_sign', radioName: 'husband-signature', canvasId: 'husbandCanvas', fileInputId: 'husbandsignimg' },
            { field: 'ref_drsign', radioName: 'dr-signature', canvasId: 'drCanvas', fileInputId: 'drsignimg' },
            { field: 'ref_admin_sign', radioName: 'admin-signature', canvasId: 'adminCanvas', fileInputId: 'adminsignimg' },
            { field: 'ref_zonal_sign', radioName: 'cc-signature', canvasId: 'ccCanvas', fileInputId: 'ccsignimg' },
        ];

        let pendingBlobs = 0;
        let refundAddSubmitSent = false;

        const finishSubmission = () => {
            if (pendingBlobs !== 0 || refundAddSubmitSent) return;
            refundAddSubmitSent = true;
            $.ajax({
                    url: refundformadded,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            window.dispatchEvent(new CustomEvent('swal:toast', {
                                detail: {
                                    title: 'Info!',
                                    text: response.message,
                                    icon: 'success',
                                    background: 'success',
                                }
                            }));
                            clearForm();
                            $("#exampleModaluser").modal('hide');
                            // Fix body overflow after modal closes
                            setTimeout(function() {
                                $('body').removeClass('modal-open').css({
                                    'overflow': '',
                                    'padding-right': ''
                                });
                                $('.modal-backdrop').remove();
                            }, 300);
                            refundformdata();
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function (error) {
                        showError(error.responseJSON?.message || 'An error occurred');
                    },
                    complete: function () {
                        hmsFormSaveBtnLoading($refundAddBtn, false);
                        refundAddSubmitSent = false;
                    },
                });
        };

        function showError(message) {
            window.dispatchEvent(new CustomEvent('swal:toast', {
                detail: {
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    background: '#f8d7da',
                }
            }));
        }

        signers.forEach(signer => {
            const isUpload = $(`input[name="${signer.radioName}"]:checked`).val() === 'upload';
            if (isUpload) {
                const file = document.getElementById(signer.fileInputId)?.files[0];
                if (file) formData.append(signer.field, file);
            } else {
                const canvas = document.getElementById(signer.canvasId);
                if (canvas) {
                    canvas.toBlob(blob => {
                        if (blob) {
                            formData.append(signer.field, blob, `${signer.field}.png`);
                        }
                        pendingBlobs--;
                        finishSubmission();
                    });
                    pendingBlobs++;
                }
            }
        });

        if (pendingBlobs === 0) finishSubmission();
    });

    function clearForm() {
        $('.dropdown-item-loc.selected').removeClass('selected');
        $('#zone_id').attr('data-value', '');
        $('#zone_id').val("");
        $('#wife_name').val("");
        $('#wifemrdno').val("");
        $('#husband_name').val("");
        $('#husbandmrdno').val("");
        $('#service_name').val("");
        $('#totalbill').val("");
        $('#expected_request').val("");
        $('#ex_ref_value').text("");
        $('input[name="expectdis"]').prop("checked", false);
        $('input[name="request"]').prop("checked", false);
        $('#counselled_by').val("");
        $('#final_amount').val("");
        $('#branch_no').val("");
        $('#authourised_by').val("");
        $('#approveded_by').val("");
        $('#patientph').val("");
        $('#patient_mobile').val("");

        $('.signature-option-group').each(function () {
            const group = $(this);
            const target = group.data('target');
            const canvas = document.getElementById(`${target}Canvas`);

            if (canvas) {
                const ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }

            $(`#${target}-upload input[type="file"]`).val('');
            $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');
        });
    }

    // =====================================================
    // EDIT REFUND FORM
    // =====================================================
    // Fill ADD form from pending row (like old refund – set data so user can submit)
    function fillAddFormFromRow(row) {
        var zoneId = row.find('.locationname').data('id');
        var branchName = row.find('.branchname').text().trim();
        var wifemrdno = row.find('.wifemrdno').data('ph_id') || row.find('.wifemrdno').text().trim().split(/\s+/)[0] || '';
        var wifename = row.find('.wifename').text().trim();
        var husmrdno = row.find('.husmrdno').data('ph_id') || row.find('.husmrdno').text().trim().split(/\s+/)[0] || '';
        var husname = row.find('.husname').text().trim();
        var treatmentcat = row.find('.treatmentcat').text().trim();
        var totalbil = row.find('.totalbil').text().trim();
        var expamount = row.find('.expamount').text().trim();
        var requestfor = row.find('.requestfor').text().trim();
        var mobileno = row.find('.mobileno').text().trim();
        var consultby = row.find('.consultby').text().trim();

        $('#exampleModaluser #zone_id').val(branchName);
        $('#exampleModaluser #zone_id').attr('data-value', zoneId || '');
        $('#exampleModaluser #wife_name').val(wifename);
        $('#exampleModaluser #wifemrdno').val(wifemrdno);
        $('#exampleModaluser #husband_name').val(husname);
        $('#exampleModaluser #husbandmrdno').val(husmrdno);
        $('#exampleModaluser #service_name').val(treatmentcat);
        $('#exampleModaluser #totalbill').val(totalbil);
        $('#exampleModaluser #expected_request').val(expamount);
        $('#exampleModaluser #ex_ref_value').text(expamount || '');
        $('#exampleModaluser #counselled_by').val(consultby);
        $('#exampleModaluser #patientph').val(mobileno);
        if (requestfor) {
            $('#exampleModaluser input[name="request"][value="' + requestfor + '"]').prop('checked', true);
        }
    }

    // Pending tab: only View – open ADD form with row data (like old refund)
    $(document).on('click', '.view_ref_details', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var row = $(this).closest('tr');
        if (row.closest('#document_tbl').length) {
            fillAddFormFromRow(row);
            $('#exampleModaluser').modal('show');
            return;
        }
    });

    // Saved tab: Edit – load via API by ref_id (like Cancel loadCancelBillForEdit)
    $(document).on('click', '.edit_ref_details', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var row = $(this).closest('tr');
        var ref_id = row.find('.ref_id').text().trim();
        if (ref_id) {
            openRefundEditModalByRefId(ref_id);
        } else {
            alert('Refund ID not found.');
        }
    });

    function openRefundEditModalByRefId(refId) {
        $.ajax({
            url: refundform_edit,
            type: "GET",
            data: { ref_id: refId },
            success: function (res) {
                if (!res.exists || !res.record) {
                    alert('Refund record not found.');
                    return;
                }
                var r = res.record;
                function cleanPath(path) {
                    if (!path) return '';
                    try {
                        var arr = typeof path === 'string' ? JSON.parse(path) : path;
                        return Array.isArray(arr) ? arr[0] : (arr || '');
                    } catch (e) {
                        return (path || '').replace(/[\[\]\"\\]/g, '');
                    }
                }
                var wifeSign = cleanPath(r.ref_wife_sign);
                var husbandSign = cleanPath(r.ref_husband_sign);
                var drSign = cleanPath(r.ref_drsign);
                var adminSign = cleanPath(r.ref_admin_sign);
                var zonalSign = cleanPath(r.ref_zonal_sign);

                $('#imagewifesign').css('background-image', wifeSign ? 'url(../' + wifeSign + ')' : 'none');
                $('#husimgPreviewe').css('background-image', husbandSign ? 'url(../' + husbandSign + ')' : 'none');
                $('#drimgPreviewe').css('background-image', drSign ? 'url(../' + drSign + ')' : 'none');
                $('#adminimgPreviewe').css('background-image', adminSign ? 'url(../' + adminSign + ')' : 'none');
                $('#ccimgPreviewe').css('background-image', zonalSign ? 'url(../' + zonalSign + ')' : 'none');

                $('#edit_refund_id').val(r.ref_id);
                $('#locationid').val(r.ref_zone_id);
                $('#edit_zone_id').val(r.location_name || '');
                $('#edit_zone_id').attr('data-value', r.ref_zone_id);
                $('#edit_ref_wife_name').val(r.ref_wife_name || '');
                $('#edit_ref_wife_mrd_no').val(r.ref_wife_mrd_no || '');
                $('#edit_ref_husband_mrd_no').val(r.ref_husband_mrd_no || '');
                $('#edit_ref_husband_name').val(r.ref_husband_name || '');
                $('#edit_ref_service_name').val(r.ref_service_name || '');
                $('#edit_ref_total_bill').val(r.ref_total_bill || '');
                $('#edit_ref_expected_request').val(r.ref_expected_request || '');
                $('#ex_ref_value_edit').text(r.ref_expected_request || '');
                $("input[name='request_edit'][value='" + (r.ref_form_status || '') + "']").prop("checked", true);
                $('#edit_ref_patient_ph').val(r.ref_patient_ph || '');
                $('#edit_ref_counselled_by').val(r.ref_counselled_by || '');
                $('#edit_ref_final_auth').val(r.ref_final_auth || '');
                $('#edit_ref_branch_no').val(r.ref_branch_no || '');
                $('#edit_ref_auth_by').val(r.ref_auth_by || '');
                $('#edit_ref_final_approve').val(r.ref_approved_by || '');
                $('#edit_ref_sno').val(r.ref_id || '');

                $('#exampleModal2').modal('show');
            },
            error: function () {
                alert('Failed to load refund form.');
            }
        });
    }

    $("#editrefundform").on("click", function (event) {
        event.preventDefault();
        var $editRefundBtn = $('#editrefundform');
        if ($editRefundBtn.prop('disabled')) return;
        hmsFormSaveBtnLoading($editRefundBtn, true);

        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('ref_id', $('#edit_refund_id').val());
        formData.append('ref_zone_id', $('#locationid').val());
        formData.append('ref_wife_name', $('#edit_ref_wife_name').val());
        formData.append('ref_wife_mrd_no', $('#edit_ref_wife_mrd_no').val());
        formData.append('ref_husband_name', $('#edit_ref_husband_name').val());
        formData.append('ref_husband_mrd_no', $('#edit_ref_husband_mrd_no').val());
        formData.append('ref_service_name', $('#edit_ref_service_name').val());
        formData.append('ref_total_bill', $('#edit_ref_total_bill').val());
        formData.append('ref_expected_request', parseFloat($('#ex_ref_value_edit').text().replace(/[^\d.]/g, '')) || $('#edit_ref_expected_request').val() || 0);
        formData.append('ref_form_status', $('input[name="request_edit"]:checked').val() || '');
        formData.append('ref_counselled_by', $('#edit_ref_counselled_by').val());
        formData.append('ref_final_auth', $('#edit_ref_final_auth').val());
        formData.append('ref_branch_no', $('#edit_ref_branch_no').val());
        formData.append('ref_auth_by', $('#edit_ref_auth_by').val());
        formData.append('ref_patient_ph', $('#edit_ref_patient_ph').val());
        formData.append('ref_approved_by', $('#edit_ref_final_approve').val());

        const signers = [
            { field: 'ref_wife_sign', radio: 'editwife-signature', canvasId: 'editwifeCanvas', fileInput: 'imagewsign' },
            { field: 'ref_husband_sign', radio: 'edithusband-signature', canvasId: 'edithusbandCanvas', fileInput: 'husbandsignimge' },
            { field: 'ref_drsign', radio: 'editdr-signature', canvasId: 'editdrCanvas', fileInput: 'drsignimge' },
            { field: 'ref_admin_sign', radio: 'editadmin-signature', canvasId: 'editadminCanvas', fileInput: 'adminsignimge' },
            { field: 'ref_zonal_sign', radio: 'editcc-signature', canvasId: 'editccCanvas', fileInput: 'ccsignimge' },
        ];

        let pendingBlobs = 0;
        let refundEditSubmitSent = false;
        const finishEditSubmission = () => {
            if (pendingBlobs !== 0 || refundEditSubmitSent) return;
            refundEditSubmitSent = true;
            $.ajax({
                    url: refundformeditsave,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            window.dispatchEvent(new CustomEvent('swal:toast', {
                                detail: {
                                    title: 'Info!',
                                    text: response.message,
                                    icon: 'success',
                                    background: 'success',
                                }
                            }));
                            var rec = response.updatedRecord;
                            var patched = !!(rec && rec.ref_id && patchRefundSavedTableRow(rec));
                            if (typeof tableData !== 'undefined' && Array.isArray(tableData) && rec && rec.ref_id) {
                                var ri = tableData.findIndex(function (r) { return String(r.ref_id) === String(rec.ref_id); });
                                if (ri >= 0) $.extend(true, tableData[ri], rec);
                            }
                            if (!patched) {
                                refundsaveformdata();
                            }
                            if ($('#savedTab').length) $('#savedTab').click();
                        } else {
                            window.dispatchEvent(new CustomEvent('swal:toast', {
                                detail: {
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    background: '#f8d7da',
                                }
                            }));
                        }
                        $("#exampleModal2").modal('hide');
                        // Fix body overflow after modal closes
                        setTimeout(function() {
                            $('body').removeClass('modal-open').css({
                                'overflow': '',
                                'padding-right': ''
                            });
                            $('.modal-backdrop').remove();
                        }, 300);
                    },
                    error: function (error) {
                        console.error(error.responseJSON);
                        if (typeof window.dispatchEvent === 'function') {
                            window.dispatchEvent(new CustomEvent('swal:toast', {
                                detail: {
                                    title: 'Error!',
                                    text: (error.responseJSON && error.responseJSON.message) || 'Request failed',
                                    icon: 'error',
                                    background: '#f8d7da',
                                }
                            }));
                        }
                    },
                    complete: function () {
                        hmsFormSaveBtnLoading($editRefundBtn, false);
                        refundEditSubmitSent = false;
                    },
                });
        };

        signers.forEach(signer => {
            const isUpload = $(`input[name="${signer.radio}"]:checked`).val() === 'upload';
            const fileInput = document.getElementById(signer.fileInput);
            const canvas = document.getElementById(signer.canvasId);

            if (isUpload && fileInput && fileInput.files.length > 0) {
                formData.append(signer.field, fileInput.files[0]);
            } else if (!isUpload && canvas) {
                pendingBlobs++;
                canvas.toBlob(blob => {
                    if (blob) {
                        formData.append(signer.field, blob, signer.field + '.png');
                    }
                    pendingBlobs--;
                    finishEditSubmission();
                });
            }
        });

        if (pendingBlobs === 0) finishEditSubmission();
    });

    // =====================================================
    // PAGINATION & ITEMS PER PAGE
    // =====================================================
    $('#itemsPerPageSelectdocument').change(function () {
        const pageSizedocuments = parseInt($(this).val());
        renderPaginationdocument(dataSourcedocument, pageSizedocuments);
        renderTabledocument(dataSourcedocument, pageSizedocuments, 1);
    });

    $('#itemsPerPageSelectsave').off('change').on('change', function () {
        const pageSizedocuments = parseInt($(this).val(), 10) || 10;
        renderPaginationsaved(tableData, pageSizedocuments, 1);
    });

    // =====================================================
    // ZONE/LOCATION FILTERING
    // =====================================================
    $(document).ready(function () {
        $('.selectzone > div').off('click').on('click', function () {
            const selectedType = $(this).data('value');
            const selectedText = $(this).text();
            $('#ref_zone_views').val(selectedText);
            $('#ref_zone_id').val(selectedType);
            $('#ref_loc_views').val('');
            $('#getlocation').hide();

            $('#getlocation > div').removeClass('selected');

            $('#getlocation > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === Number(selectedType);
                })
                .show();
        });

        $('#ref_zone_views').on('input', function () {
            $('#ref_zone_id').val('');
            $('#getlocation > div').show();
            $('#ref_loc_views').val('');
            $('#getlocation > div').removeClass('selected');
        });

        $('#ref_loc_views').on('focus', function () {
            const selectedType = Number($('#ref_zone_id').val());

            if (selectedType) {
                $('#getlocation > div')
                    .hide()
                    .filter(function () {
                        return Number($(this).data('type')) === selectedType;
                    })
                    .show();
            }

            $('#getlocation').show();
        });

        $('#getlocation > div').off('click').on('click', function () {
            const name = $(this).data('value');
            $('#ref_loc_views').val(name);

            $('#getlocation > div').removeClass('selected');
            $(this).addClass('selected');
            $('#getlocation').hide();
        });

        $('input.searchInput').attr('autocomplete', 'off');

        // SAVE TAB - zone/location
        $('.selectzonesave > div').off('click').on('click', function () {
            const selectedType = $(this).data('value');
            const selectedText = $(this).text();
            $('#save_zone_views').val(selectedText);
            $('#dissave_zone_id').val(selectedType);
            $('#save_loc_views').val('');
            $('#getlocationsave').hide();

            $('#getlocationsave > div').removeClass('selected');

            $('#getlocationsave > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === Number(selectedType);
                })
                .show();
        });

        $('#save_zone_views').on('input', function () {
            $('#dissave_zone_id').val('');
            $('#getlocationsave > div').show();
            $('#save_loc_views').val('');
            $('#getlocationsave > div').removeClass('selected');
        });

        $('#save_loc_views').on('focus', function () {
            const selectedType = Number($('#dissave_zone_id').val());

            if (selectedType) {
                $('#getlocationsave > div')
                    .hide()
                    .filter(function () {
                        return Number($(this).data('type')) === selectedType;
                    })
                    .show();
            }

            $('#getlocationsave').show();
        });

        $('#getlocationsave > div').off('click').on('click', function () {
            const name = $(this).data('value');
            $('#save_loc_views').val(name);

            $('#getlocationsave > div').removeClass('selected');
            $(this).addClass('selected');
            $('#getlocationsave').hide();
        });
    });

    // =====================================================
    // DISCOUNT CALCULATION
    // =====================================================
    $('#expected_request').on('blur', function () {
        const totalBill = parseFloat($('#totalbill').val()) || 0;
        const rawValue = $(this).val().replace(/[^0-9.]/g, '');
        const discountValue = parseFloat(rawValue) || "";
        const expercentage = $('#expercentage').is(':checked');
        const examount = $('#examount').is(':checked');

        let formattedInput = '';
        let discountAmount = 0;
        if (!expercentage && !examount) {
            alert("Please select either ₹ or % for the expected discount.");
            $(this).val('');
            $('#ex_ref_value').text("");
            return;
        }

        if (expercentage) {
            if (discountValue > 100) {
                alert('Please enter a valid discount: percentage cannot exceed 100%.');
                $(this).val('');
                $('#ex_ref_value').text('');
                return;
            }
            discountAmount = (totalBill * discountValue) / 100;
            formattedInput = discountValue + '%';

        } else if (examount) {
            if (discountValue > totalBill) {
                alert('Please enter a valid discount: amount cannot exceed total bill.');
                $(this).val('');
                $('#ex_ref_value').text('');
                return;
            }
            discountAmount = discountValue;
            formattedInput = discountValue + '₹';
        }

        $(this).val(formattedInput);
        $('#ex_ref_value').text(discountAmount.toFixed(2) + '₹');
    });

    $('#totalbill').on('blur', function () {
        let inputVal = $(this).val().trim();
        let numericVal = inputVal.replace(/[^0-9.]/g, '');
        if (!numericVal || isNaN(numericVal)) {
            alert('Please enter a valid number for the total bill value.');
            $(this).val('');
            return;
        }
        const totalBill = parseFloat(numericVal);
        const formattedInput = totalBill.toFixed(2) + '₹';

        $(this).val(formattedInput);
    });

    function upFinalAmount() {
        const totalBill = parseFloat($('#totalbill').val().replace(/[^0-9.]/g, '')) || 0;
        const discountAmount = parseFloat($('#ex_ref_value').text().replace(/[^0-9.]/g, '')) || 0;

        const finalAmount = discountAmount;
        $('#final_amount').val(finalAmount.toFixed(2));
    }
    $('#totalbill').on('input', upFinalAmount);
    const observerfun = new MutationObserver(upFinalAmount);
    const discountElement = document.getElementById('ex_ref_value');
    if (discountElement) {
        observerfun.observe(discountElement, { childList: true, characterData: true, subtree: true });
    }

    // EDIT (Edit modal uses edit_ref_* IDs)
    $('#edit_ref_expected_request').on('blur', function () {
        const totalBill = parseFloat($('#edit_ref_total_bill').val().replace(/[^0-9.]/g, '')) || 0;
        var rawVal = $(this).val().trim();
        var isPct = rawVal.indexOf('%') >= 0;
        const rawValue = parseFloat(rawVal.replace(/[^0-9.]/g, '')) || 0;
        let discountAmount = 0;
        if (rawValue && totalBill) {
            if (isPct) {
                if (rawValue > 100) { alert('Percentage cannot exceed 100%.'); return; }
                discountAmount = (totalBill * rawValue) / 100;
            } else {
                if (rawValue > totalBill) { alert('Amount cannot exceed total bill.'); return; }
                discountAmount = rawValue;
            }
        }
        $('#ex_ref_value_edit').text(discountAmount ? discountAmount.toFixed(2) : '');
        updateEditFinalAmount();
    });

    $('#edit_ref_total_bill').on('blur', function () {
        let inputVal = $(this).val().trim();
        let numericVal = inputVal.replace(/[^0-9.]/g, '');
        if (!numericVal || isNaN(numericVal)) return;
        const totalBill = parseFloat(numericVal);
        $(this).val(totalBill.toFixed(2));
    });

    function updateEditFinalAmount() {
        const totalBill = parseFloat($('#edit_ref_total_bill').val().replace(/[^0-9.]/g, '')) || 0;
        const discountAmount = parseFloat($('#ex_ref_value_edit').text().replace(/[^0-9.]/g, '')) || 0;
        $('#edit_ref_final_auth').val(discountAmount ? discountAmount.toFixed(2) : '');
    }
    $('#edit_ref_total_bill').on('input', updateEditFinalAmount);
    const observerEdit = new MutationObserver(updateEditFinalAmount);
    const discountElEdit = document.getElementById('ex_ref_value_edit');
    if (discountElEdit) {
        observerEdit.observe(discountElEdit, { childList: true, characterData: true, subtree: true });
    }

    // =====================================================
    // IMAGE PREVIEW
    // =====================================================
    function readURL(input, preview) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(preview).css('background-image', 'url(' + e.target.result + ')');
                $(preview).hide();
                $(preview).fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload").change(function () {
        readURL(this, '#imagePreview');
    });
    $("#husbandsignimg").change(function () {
        readURL(this, '#husimgPreview');
    });
    $("#drsignimg").change(function () {
        readURL(this, '#drimgPreview');
    });
    $("#adminsignimg").change(function () {
        readURL(this, '#adminimgPreview');
    });
    $("#ccsignimg").change(function () {
        readURL(this, '#ccimgPreview');
    });

    // EDIT
    $('#imagewsign').change(function () {
        readURL(this, '#imagewifesign');
    });
    $("#husbandsignimge").change(function () {
        readURL(this, '#husimgPreviewe');
    });
    $("#drsignimge").change(function () {
        readURL(this, '#drimgPreviewe');
    });
    $("#adminsignimge").change(function () {
        readURL(this, '#adminimgPreviewe');
    });
    $("#ccsignimge").change(function () {
        readURL(this, '#ccimgPreviewe');
    });

    // =====================================================
    // SIGNATURE TOGGLE
    // =====================================================
    $('input[type="radio"]').change(function () {
        const type = $(this).val();
        const group = $(this).closest('.signature-option-group');
        const target = group.data('target');

        if (type === 'upload') {
            $(`#${target}-upload`).show();
            $(`#${target}-sign`).hide();
            const canvas = document.getElementById(`${target}Canvas`);
            if (canvas) {
                const ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        } else {
            $(`#${target}-upload`).hide();
            $(`#${target}-sign`).show();
            $(`#${target}-upload input[type="file"]`).val('');
            $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');

            setupCanvas(`${target}Canvas`);
        }
    });

    $(document).on('click', '.clear-sign', function () {
        const target = $(this).data('target');
        $(`#${target}-upload input[type="file"]`).val('');
        $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');
    });

    function setupCanvas(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || canvas.dataset.initialized === "true") return;

        const ctx = canvas.getContext("2d");
        let drawing = false;

        canvas.addEventListener("mousedown", () => drawing = true);
        canvas.addEventListener("mouseup", () => drawing = false);
        canvas.addEventListener("mouseout", () => drawing = false);
        canvas.addEventListener("mousemove", function (e) {
            if (!drawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineWidth = 2;
            ctx.lineCap = "round";
            ctx.strokeStyle = "#000";
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        });

        canvas.dataset.initialized = "true";
    }

    function clearCanvas(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
    window.clearCanvas = clearCanvas;

    ['wifeCanvas', 'husbandCanvas', 'drCanvas', 'adminCanvas', 'ccCanvas'].forEach(setupCanvas);
    ['editwifeCanvas', 'edithusbandCanvas', 'editdrCanvas', 'editadminCanvas', 'editccCanvas'].forEach(setupCanvas);

});

// =====================================================
// DATA FETCHING & RENDERING
// =====================================================
var dataSourcedocument = [];

/**
 * Pending (#mydateallviews): normalize labels to DD/MM/YYYY - DD/MM/YYYY.
 * Saved (#mydateallviewssave): use { allowAll: true } so empty / "All" always sends All (full list on first load).
 */
function getDateRangeForApi(selector, opts) {
    opts = opts || {};
    var allowAll = !!opts.allowAll;
    var dateText = ($(selector).text() || '').trim();
    if (allowAll) {
        if (!dateText || dateText.toLowerCase() === 'all') {
            return 'All';
        }
        return dateText;
    }
    if (!dateText || dateText === 'Today' || dateText === 'Yesterday' || dateText.indexOf('-') === -1) {
        return moment().format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY');
    }
    return dateText;
}

function refundformdata() {
    startLoader();
    $("#document_tbl").hide();
    var moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
    $(".value_views_mysearch").text("");

    $.ajax({
        url: refdocdetialsUrl,
        type: "GET",
        data: {
            moredatefittervale: moredatefittervale,
        },
        success: function (responseData) {
            stopLoader(true);
            handleSuccessRefund(responseData);
        },
        error: function (xhr, status, error) {
            stopLoader(false, error);
            console.error("AJAX Error:", status, error);
        }
    });
}

function handleSuccessRefund(responseData) {
    $("#document_tbl").show();

    dataSourcedocument = responseData.checkinData || [];
    var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val());
    renderPaginationdocument(dataSourcedocument, pageSizedocuments);
    renderTabledocument(dataSourcedocument, pageSizedocuments, 1);
}

function renderTabledocument(data, pageSizedocuments, pageNum) {
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);
    var body = "";
    var totalItems = data.length;

    $.each(pageData, function (index, user) {
        let gender = user.gender || '';

        let wifemrdno = user.ref_wife_mrd_no || (gender === 'F' ? user.phid : '') || 'N/A';
        let wifename = user.ref_wife_name || (gender === 'F' ? user.name : '') || 'N/A';
        let husmrdno = user.ref_husband_mrd_no || (gender === 'M' ? user.phid : '') || 'N/A';
        let husname = user.ref_husband_name || (gender === 'M' ? user.name : '') || 'N/A';

        let wmrd = wifemrdno;
        let wname = wifename;
        let hmrd = husmrdno;
        let hname = husname;

        let zone = user.zone || user.zone_name || 'N/A';
        let location = user.branch || user.location_name || 'N/A';
        let treatmentcat = user.ref_service_name || '-';
        let totalbil = user.ref_total_bill || '-';
        let expected_request = user.ref_expected_request || '-';
        let patient_ph = user.ref_patient_ph || user.mobile || '-';
        let counselled_by = user.ref_counselled_by || user.consultingdr_name || '-';
        let locationid = user.locationid || user.ref_zone_id;

        body += '<tr onclick="rowClick(event)">' +
            '<td class="tdview" id="idfetch" data-id=""><strong>#' + (startIdx + index + 1) + '</strong></td>' +
            '<td class="tdview locationname" data-id="' + locationid + '">' + zone + '</td>' +
            '<td class="tdview branchname">' + location + '</td>' +
            '<td class="tdview wifemrdno" data-ph_id="' + wmrd + '"><a href="#">' + wmrd + '<br>' + wname + '</a></td>' +
            '<td class="tdview husmrdno" data-ph_id="' + hmrd + '"><a href="#">' + hmrd + '<br>' + hname + '</a></td>' +
            '<td class="tdview treatmentcat">' + treatmentcat + '</td>' +
            '<td class="tdview totalbil">' + totalbil + '</td>' +
            '<td class="tdview expamount">' + expected_request + '</td>' +
            '<td class="tdview consultby">' + counselled_by + '</td>' +
            '<td class="tdview mobileno">' + patient_ph + '</td>' +
            '<td class="tdview wifename" style="display:none;"><a href="#">' + wname + '</a></td>' +
            '<td class="tdview husname" style="display:none;"><a href="#">' + hname + '</a></td>' +
            '<td class="tdview requestfor" style="display:none;">' + (user.ref_form_status || '') + '</td>' +
            '<td class="tdview authby" style="display:none;">' + (user.ref_auth_by || '') + '</td>' +
            '<td class="tdview finalapprove" style="display:none;">' + (user.ref_approved_by || '') + '</td>' +
            '<td class="tdview brno" style="display:none;">' + (user.ref_branch_no || '') + '</td>' +
            '<td class="tdview finalamount" style="display:none;">' + (user.ref_final_auth || '') + '</td>' +
            '<td class="tdview wife_sign" style="display:none;">' + (user.ref_wife_sign || '') + '</td>' +
            '<td class="tdview husband_sign" style="display:none;">' + (user.ref_husband_sign || '') + '</td>' +
            '<td class="tdview drsign" style="display:none;">' + (user.ref_drsign || '') + '</td>' +
            '<td class="tdview admin_sign" style="display:none;">' + (user.ref_admin_sign || '') + '</td>' +
            '<td class="tdview zonal_sign" style="display:none;">' + (user.ref_zonal_sign || '') + '</td>' +
            '<td class="tdview ref_id" style="display:none;">' + (user.ref_id || '') + '</td>' +
            '<td class="tdview" style="padding:3px;" id="savebtn">' +
            '<a href="#" class="view_ref_details"><img src="../assets/images/view.png" style="width: 23px;" alt="View" class="icon" title="View / Fill Form"></a>' +
            '</td>' +
            '</tr>';
    });

    if (pageData.length === 0) {
        body += '<tr><td colspan="15" class="tdview" style="text-align: center;">No data available</td></tr>';
    }

    $("#document_tbl").html(body);
    $("#mycounts").text(totalItems);
}

function renderPaginationdocument(data, pageSizedocuments, currentPage = 1) {
    const totalPages = Math.ceil(data.length / pageSizedocuments);
    let paginationHtml = '';

    if (currentPage > 1) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
    }

    const maxVisible = 3;
    const pageRange = [];

    pageRange.push(1);

    if (currentPage > maxVisible) {
        pageRange.push('...');
    }

    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
        pageRange.push(i);
    }

    if (currentPage < totalPages - maxVisible + 1) {
        pageRange.push('...');
    }

    if (totalPages > 1) {
        pageRange.push(totalPages);
    }

    for (let i = 0; i < pageRange.length; i++) {
        if (pageRange[i] === '...') {
            paginationHtml += `<span class="dots">...</span>`;
        } else {
            const page = pageRange[i];
            const activeClass = page === currentPage ? 'active' : '';
            const bgColor = page === currentPage ? 'style="background-color: #080fd399;"' : '';
            paginationHtml += `<button class="page-btnviews ${activeClass}" data-page="${page}" ${bgColor}>${page}</button>`;
        }
    }

    if (currentPage < totalPages) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage + 1}">Next</button>`;
    }

    $('#paginationdocument').html(paginationHtml);

    $('.page-btnviews').click(function () {
        const pageNum = $(this).data('page');
        renderPaginationdocument(data, pageSizedocuments, pageNum);
        renderTabledocument(data, pageSizedocuments, pageNum);
    });

    renderTabledocument(data, pageSizedocuments, currentPage);
}

// =====================================================
// FILTERING - PENDING TAB
// =====================================================
var fitterremovedata = [];

$(document).on('click', '.sec_options_marketers div', function () {
    $(".value_views_mysearch").text("");
    $('.clear_my_views').show();
    $(".my_search_view").show();
    var moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
    let resultsArray_marketer = [];
    $(".documentdatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const result = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });

    const moreFilterValues_market = [
        $("#ref_zone_views").val(),
        $('#ref_loc_views').val(),
        $('#ref_mrd_views').val()
    ];
    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    refmorefilterview(fitterremovedata, refdocdetialsUrl, moredatefittervale);

});

$(document).on("click", ".value_views_mysearch", function () {
    var moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");

    if (clear_filtr == 'refbranch_search') {
        $('#ref_loc_views').val('');
    }
    if (clear_filtr == 'refzone_search') {
        $('#ref_zone_views').val('');
    }
    if (clear_filtr == 'refmrdno_search') {
        $('#ref_mrd_views').val('');
    }

    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });
    refmorefilterview(fitterremovedata, refdocdetialsUrl, moredatefittervale);
});

$(document).on("click", ".clear_my_views", function () {
    fitterremovedata.length = 0;
    $('.documentdatasearch').val("");
    $(".value_views_mysearch").text("");
    $('.clear_my_views').hide();
    $(".my_search_view").hide();
    refundformdata();
});

$('#ref_mrd_views').on('input', function () {
    const ph_id = $(this).val().trim();
    $(".value_views_mysearch").text("");
    $('.clear_my_views').show();
    $(".my_search_view").show();

    const moredatefittervale = getDateRangeForApi('#mydateallviews', { allowAll: false });
    let resultsArray_marketer = [];

    $(".documentdatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const filterStr = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(filterStr);
        }
    });

    const moreFilterValues_market = [
        $("#ref_zone_views").val(),
        $('#ref_loc_views').val(),
        $('#ref_mrd_views').val()
    ];

    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    refmorefilterview(fitterremovedata, refdocdetialsUrl, moredatefittervale, ph_id);
});

function refmorefilterview(uniqueResults, url, moredatefittervale, ph_id = '') {
    startLoader();
    $("#document_tbl").hide();
    if (uniqueResults != "") {
        var morefilltersall = uniqueResults.join(" AND ");
        $.ajax({
            url: url,
            type: "GET",
            data: {
                morefilltersall: morefilltersall,
                moredatefittervale: moredatefittervale,
                mrodnofilter: ph_id
            },
            success: function (responseData) {
                stopLoader(true);
                handleSuccessRefund(responseData);
            },
            error: function (xhr, status, error) {
                stopLoader(false, error);
                console.error("AJAX Error:", status, error);
            }
        });
    } else {
        $('.clear_my_views').hide();
        $(".my_search_view").hide();
        refundformdata();
    }
}

// =====================================================
// LOADER FUNCTIONS
// =====================================================
var loaderInterval = null;
var loaderProgress = 0;

function startLoader() {
    const progressBar = $('.progress-bar');
    const errorMessage = $('#error-message');

    loaderProgress = 0;

    progressBar.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    errorMessage.hide().text('');

    loaderInterval = setInterval(() => {
        if (loaderProgress < 90) {
            loaderProgress += 10;
            progressBar.text(`Loading: ${loaderProgress}%`);
            progressBar.css('width', `${loaderProgress}%`);
        }
    }, 950);
}

function stopLoader(success = true, error = '') {
    const progressBar = $('.progress-bar');
    const errorMessage = $('#error-message');

    clearInterval(loaderInterval);

    if (success) {
        loaderProgress = 100;
        progressBar.text(`Loading: 100% - Done`);
        progressBar.css({
            'width': '100%',
            'background-color': '#007bff'
        });

        setTimeout(() => {
            progressBar.hide();
            $("#document_tbl").show();
        }, 500);
    } else {
        progressBar.text('Error loading data');
        progressBar.css('background-color', 'red');
        errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            progressBar.hide();
            $("#document_tbl").hide();
        }, 1000);
    }
}

function startLoader2() {
    const progressBar = $('.progress-bar2');
    const errorMessage = $('#error-message');

    loaderProgress = 0;

    progressBar.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    errorMessage.hide().text('');

    loaderInterval = setInterval(() => {
        if (loaderProgress < 90) {
            loaderProgress += 10;
            progressBar.text(`Loading: ${loaderProgress}%`);
            progressBar.css('width', `${loaderProgress}%`);
        }
    }, 950);
}

function stopLoader2(success = true, error = '') {
    const progressBar = $('.progress-bar2');
    const errorMessage = $('#error-message');

    clearInterval(loaderInterval);

    if (success) {
        loaderProgress = 100;
        progressBar.text(`Loading: 100% - Done`);
        progressBar.css({
            'width': '100%',
            'background-color': '#007bff'
        });

        setTimeout(() => {
            progressBar.hide();
            $("#sveddata_tbl").show();
        }, 500);
    } else {
        progressBar.text('Error loading data');
        progressBar.css('background-color', 'red');
        errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            progressBar.hide();
            $("#sveddata_tbl").hide();
        }, 1000);
    }
}

// =====================================================
// SAVED REFUND FORMS (TAB 2)
// =====================================================
let tableData = [];
let isApprover = false;

function refundsaveformdata() {
    startLoader2();
    $("#sveddata_tbl").hide();
    var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
    $(".value_save_mysearch").text("");
    $.ajax({
        url: refundformsave_data,
        type: "GET",
        data: {
            moredatefittervale: moredatefittervale,
            status_filter: $('#save_status_filter').val() || ''
        },
        success: function (responseData) {
            stopLoader2(true);
            handlesavedSuccess(responseData);
        },
        error: function (xhr, status, error) {
            stopLoader2(false, error);
            console.error("AJAX Error:", status, error);
        }
    });
}

function handlesavedSuccess(responseData) {
    console.log("responseData", responseData);

    $("#sveddata_tbl").show();

    tableData = Array.isArray(responseData?.data)
        ? responseData.data
        : [];

    isApprover = !!responseData?.isApprover;

    const pageSizedocuments = parseInt($('#itemsPerPageSelectsave').val()) || 10;

    renderPaginationsaved(tableData, pageSizedocuments, 1);
    renderStatisticsCards(responseData.statistics, responseData.counts);
}

function renderPaginationsaved(dataArray, pageSizedocuments, currentPage = 1) {
    if (!Array.isArray(dataArray)) return;

    const totalPages = Math.ceil(dataArray.length / pageSizedocuments);
    let paginationHtml = '';

    if (currentPage > 1) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
    }

    const maxVisible = 3;
    const pageRange = [1];

    if (currentPage > maxVisible) pageRange.push('...');

    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
        pageRange.push(i);
    }

    if (currentPage < totalPages - maxVisible + 1) pageRange.push('...');

    if (totalPages > 1) pageRange.push(totalPages);

    pageRange.forEach(page => {
        if (page === '...') {
            paginationHtml += `<span class="dots">...</span>`;
        } else {
            const active = page === currentPage ? 'active' : '';
            paginationHtml += `<button class="page-btnviews ${active}" data-page="${page}">${page}</button>`;
        }
    });

    if (currentPage < totalPages) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage + 1}">Next</button>`;
    }

    $('#paginationsavedata').html(paginationHtml);

    $('.page-btnviews').off('click').on('click', function () {
        const pageNum = $(this).data('page');
        renderPaginationsaved(dataArray, pageSizedocuments, pageNum);
        renderTablesaved(dataArray, pageSizedocuments, pageNum, isApprover);
    });

    renderTablesaved(dataArray, pageSizedocuments, currentPage, isApprover);
}

var labelToStatKey = { 'Total Raised': 'total_raised', 'Admin Approved': 'admin_approved', 'Zonal Approved': 'zonal_approved', 'Audit Approved': 'audit_approved', 'Final Approved': 'final_approved', 'Pending': 'pending', 'Total Refund': 'total_refund_amount' };

function renderStatisticsCards(statistics, counts) {
    var data = {};
    if (counts && typeof counts === 'object') {
        data = counts;
    } else if (statistics && $.isArray(statistics)) {
        $.each(statistics, function (i, s) {
            var key = labelToStatKey[s.label] || s.label.toLowerCase().replace(/\s+/g, '_');
            data[key] = s.count;
        });
    }
    var $container = $('#stats-container');
    if (!$container.length) return;
    $container.find('.stat-card').each(function () {
        var key = $(this).data('stat-key');
        var val = data[key];
        if (val === undefined) val = 0;
        var $val = $(this).find('.stat-value');
        if (key === 'total_refund_amount') {
            $val.text('₹' + (parseFloat(val) || 0).toFixed(2));
        } else {
            $val.text(val);
        }
        if ($(this).data('filter')) $(this).data('filter', (key || '').toLowerCase().replace(/\s+/g, '-'));
    });
}

function renderTablesaved(data, pageSizedocuments, pageNum, isApprover) {
    if (!Array.isArray(data)) return;

    const startIdx = (pageNum - 1) * pageSizedocuments;
    const endIdx = pageNum * pageSizedocuments;
    const pageData = data.slice(startIdx, endIdx);
    const totalItems = data.length;

    let body = '';
    let access_limits = admin_user.access_limits;
    console.log("access_limits",access_limits);
    
    function renderStatus(status, approverName) {
        status = parseInt(status);
        let icon = '';
        if (status === 1) icon = `<span style="color:green;font-weight:bold;font-size:20px;">✔</span>`;
        else if (status === 2) icon = `<span style="color:red;font-weight:bold;font-size:20px;">✖</span>`;
        else icon = `<span style="color:#f0ad4e;font-weight:bold;font-size:14px;">⏳ Pending</span>`;
        const name = (approverName && String(approverName).trim()) ? String(approverName).trim() : '—';
        return `<div>${icon}</div><div class="small text-muted" style="font-size:11px;margin-top:2px;">${name}</div>`;
    }

    $.each(pageData, function (index, user) {
        let id = user.ref_id || '';
        let wifemrdno = user.ref_wife_mrd_no || 'N/A';
        let wifename = user.ref_wife_name || 'N/A';
        let husmrdno = user.ref_husband_mrd_no || 'N/A';
        let husname = user.ref_husband_name || 'N/A';

        let wmrd = wifemrdno;
        let wname = wifename;
        let hmrd = husmrdno;
        let hname = husname;

        let zone = user.zone_name || 'N/A';
        let location = user.location_name || 'N/A';
        let treatmentcat = user.ref_service_name || '-';
        let totalbil = user.ref_total_bill || '-';
        let expected_request = user.ref_expected_request || '-';
        let phone = user.ref_patient_ph || '-';
        let consultant = user.ref_counselled_by || '-';
        let uploaded_by = user.created_by_name || '-';

        let actionHtml = '-';
        // Helper: treat 0, null, undefined as pending (same logic as cancel)
        function isPending(val) {
            var v = parseInt(val, 10);
            return (v !== 1 && v !== 2);
        }

        if (access_limits == 1 && isPending(user.final_approver)) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }
        else if (access_limits == 2 && isPending(user.zonal_approver)) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }
        else if (access_limits == 3 && isPending(user.admin_approver)) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }
        else if (access_limits == 4 && isPending(user.audit_approver)) {
            actionHtml = `
                <span class="action-icons" data-id="${id}">
                        <span class="approve-btn" data-id="${id}" data-status="1" title="Approve" style="cursor:pointer;color:green;font-size:20px;margin:0 5px;">✔</span>
                        <span class="reject-btn" data-id="${id}" data-status="2" title="Reject" style="cursor:pointer;color:red;font-size:20px;margin:0 5px;">✖</span>
                    </span>`;
        }

        
        let editHtml = `<a href="#"><img src="../assets/images/edit.png" class="edit_ref_details" style="width:23px;" alt="Edit" title="Edit"></a>`;
        let printHtml = `<a href="#" class="print_ref_details" data-id="${id}"><img src="../assets/images/print.png" style="width:23px;" alt="Print" title="Print"></a>`;

        var isPendingRow = (access_limits == 1 && isPending(user.final_approver)) ||
            (access_limits == 2 && isPending(user.zonal_approver)) ||
            (access_limits == 3 && isPending(user.admin_approver)) ||
            (access_limits == 4 && isPending(user.audit_approver));
        var checkboxCell = '';
        if (access_limits == 1 || access_limits == 2 || access_limits == 3 || access_limits == 4) {
            if (isPendingRow) {
                checkboxCell = '<td class="tdview" onclick="event.stopPropagation();"><input type="checkbox" class="form-check-input approve-row-cb" data-id="' + id + '"></td>';
            } else {
                checkboxCell = '<td class="tdview"></td>';
            }
        }

        body += '<tr onclick="rowClick(event)">';
        body += checkboxCell;
        body += `<td class="tdview"><strong>#${startIdx + index + 1}</strong></td>`;
        body += `<td class="tdview locationname" data-id="${user.ref_zone_id}">${zone}</td>`;
        body += `<td class="tdview branchname">${location}</td>`;
        body += `<td class="tdview wifemrdno" data-ph_id="${wmrd}"><a href="#">${wmrd}<br>${wname}</a></td>`;
        body += `<td class="tdview husmrdno" data-ph_id="${hmrd}"><a href="#">${hmrd}<br>${hname}</a></td>`;
        body += `<td class="tdview treatmentcat">${treatmentcat}</td>`;
        body += `<td class="tdview totalbil">${totalbil}</td>`;
        body += `<td class="tdview expamount">${expected_request}</td>`;
        body += `<td class="tdview consultby">${consultant}</td>`;
        body += `<td class="tdview mobileno">${phone}</td>`;
        body += `<td class="tdview">${uploaded_by}</td>`;
        body += `<td class="tdview reject-reason-cell" title="${(user.reject_reason || '').replace(/"/g, '&quot;')}">${user.reject_reason ? (user.reject_reason.length > 50 ? user.reject_reason.substring(0, 50) + '...' : user.reject_reason) : '-'}</td>`;

        if (access_limits == 1) {
            body += `<td class="tdview">${renderStatus(user.admin_approver, user.admin_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.zonal_approver, user.zonal_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.audit_approver, user.audit_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.final_approver, user.final_approver_name)}</td>`;
        } else if (access_limits == 2) {
            body += `<td class="tdview">${renderStatus(user.admin_approver, user.admin_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.audit_approver, user.audit_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.final_approver, user.final_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.zonal_approver, user.zonal_approver_name)}</td>`;
        } else if (access_limits == 3) {
            body += `<td class="tdview">${renderStatus(user.zonal_approver, user.zonal_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.audit_approver, user.audit_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.final_approver, user.final_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.admin_approver, user.admin_approver_name)}</td>`;
        } else if (access_limits == 4) {
            body += `<td class="tdview">${renderStatus(user.admin_approver, user.admin_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.zonal_approver, user.zonal_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.final_approver, user.final_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.audit_approver, user.audit_approver_name)}</td>`;
        } else {
            body += `<td class="tdview">${renderStatus(user.admin_approver, user.admin_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.zonal_approver, user.zonal_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.audit_approver, user.audit_approver_name)}</td>`;
            body += `<td class="tdview">${renderStatus(user.final_approver, user.final_approver_name)}</td>`;
        }

        body += `<td class="tdview">${actionHtml}</td>`;
        body += `<td class="tdview">${editHtml}</td>`;
        body += `<td class="tdview">${printHtml}</td>`;

        // Hidden fields
        body += `<td class="tdview wifename" style="display:none;">${wname}</td>`;
        body += `<td class="tdview husname" style="display:none;">${hname}</td>`;
        body += `<td class="tdview requestfor" style="display:none;">${user.ref_form_status || ''}</td>`;
        body += `<td class="tdview authby" style="display:none;">${user.ref_auth_by || ''}</td>`;
        body += `<td class="tdview finalapprove" style="display:none;">${user.ref_approved_by || ''}</td>`;
        body += `<td class="tdview brno" style="display:none;">${user.ref_branch_no || ''}</td>`;
        body += `<td class="tdview finalamount" style="display:none;">${user.ref_final_auth || ''}</td>`;
        body += `<td class="tdview wife_sign" style="display:none;">${user.ref_wife_sign || ''}</td>`;
        body += `<td class="tdview husband_sign" style="display:none;">${user.ref_husband_sign || ''}</td>`;
        body += `<td class="tdview drsign" style="display:none;">${user.ref_drsign || ''}</td>`;
        body += `<td class="tdview admin_sign" style="display:none;">${user.ref_admin_sign || ''}</td>`;
        body += `<td class="tdview zonal_sign" style="display:none;">${user.ref_zonal_sign || ''}</td>`;
        body += `<td class="tdview ref_id" style="display:none;">${id}</td>`;

        body += '</tr>';
    });

    if (pageData.length === 0) {
        body += '<tr><td colspan="22" class="tdview" style="text-align: center;">No data available</td></tr>';
    }

    $("#sveddata_tbl").html(body);
    $("#billsavecounts").text(totalItems);

    if ($('#sveddata_tbl .approve-row-cb').length) {
        $('#saved-bulk-actions').show();
    } else {
        $('#saved-bulk-actions').hide();
    }
    $('#saved-select-all').prop('checked', false);
}

function patchRefundSavedTableRow(rec) {
    if (!rec || rec.ref_id == null) return false;
    var id = String(rec.ref_id);
    var $tr = $('#sveddata_tbl tr').filter(function () {
        return ($(this).find('.ref_id').text() || '').trim() === id;
    });
    if (!$tr.length) return false;

    function renderStatus(status, approverName) {
        status = parseInt(status, 10);
        var icon = '';
        if (status === 1) icon = '<span style="color:green;font-weight:bold;font-size:20px;">✔</span>';
        else if (status === 2) icon = '<span style="color:red;font-weight:bold;font-size:20px;">✖</span>';
        else icon = '<span style="color:#f0ad4e;font-weight:bold;font-size:14px;">⏳ Pending</span>';
        var name = (approverName && String(approverName).trim()) ? String(approverName).trim() : '—';
        return '<div>' + icon + '</div><div class="small text-muted" style="font-size:11px;margin-top:2px;">' + name + '</div>';
    }

    var wmrd = rec.ref_wife_mrd_no || 'N/A';
    var wname = rec.ref_wife_name || 'N/A';
    var hmrd = rec.ref_husband_mrd_no || 'N/A';
    var hname = rec.ref_husband_name || 'N/A';

    $tr.find('.locationname').text(rec.zone_name || 'N/A').attr('data-id', rec.ref_zone_id != null ? rec.ref_zone_id : '');
    $tr.find('.branchname').text(rec.location_name || 'N/A');
    $tr.find('.wifemrdno').html('<a href="#">' + wmrd + '<br>' + wname + '</a>').attr('data-ph_id', wmrd);
    $tr.find('.husmrdno').html('<a href="#">' + hmrd + '<br>' + hname + '</a>').attr('data-ph_id', hmrd);
    $tr.find('.treatmentcat').text(rec.ref_service_name || '-');
    $tr.find('.totalbil').text(rec.ref_total_bill || '-');
    var exp = rec.ref_expected_request || '-';
    var fst = rec.ref_form_status || '-';
    $tr.find('.totalbil').next('td').text(exp + ' (' + fst + ')');
    $tr.find('.expamount').text(exp);
    $tr.find('.requestfor').text(fst);
    $tr.find('.consultby').text(rec.ref_counselled_by || '-');
    $tr.find('.mobileno').text(rec.ref_patient_ph || '-');
    $tr.find('.mobileno').next('td').text(rec.created_by_name || '-');
    var rr = rec.reject_reason || '';
    $tr.find('.reject-reason-cell').attr('title', rr.replace(/"/g, '&quot;')).text(rr ? (rr.length > 50 ? rr.substring(0, 50) + '...' : rr) : '-');

    var al = admin_user.access_limits;
    var $statusCells = $tr.find('.reject-reason-cell').nextAll('td.tdview');
    if (al == 1 && $statusCells.length >= 4) {
        $statusCells.eq(0).html(renderStatus(rec.admin_approver, rec.admin_approver_name));
        $statusCells.eq(1).html(renderStatus(rec.zonal_approver, rec.zonal_approver_name));
        $statusCells.eq(2).html(renderStatus(rec.audit_approver, rec.audit_approver_name));
        $statusCells.eq(3).html(renderStatus(rec.final_approver, rec.final_approver_name));
    } else if (al == 2 && $statusCells.length >= 4) {
        $statusCells.eq(0).html(renderStatus(rec.admin_approver, rec.admin_approver_name));
        $statusCells.eq(1).html(renderStatus(rec.audit_approver, rec.audit_approver_name));
        $statusCells.eq(2).html(renderStatus(rec.final_approver, rec.final_approver_name));
        $statusCells.eq(3).html(renderStatus(rec.zonal_approver, rec.zonal_approver_name));
    } else if (al == 3 && $statusCells.length >= 4) {
        $statusCells.eq(0).html(renderStatus(rec.zonal_approver, rec.zonal_approver_name));
        $statusCells.eq(1).html(renderStatus(rec.audit_approver, rec.audit_approver_name));
        $statusCells.eq(2).html(renderStatus(rec.final_approver, rec.final_approver_name));
        $statusCells.eq(3).html(renderStatus(rec.admin_approver, rec.admin_approver_name));
    } else if (al == 4 && $statusCells.length >= 4) {
        $statusCells.eq(0).html(renderStatus(rec.admin_approver, rec.admin_approver_name));
        $statusCells.eq(1).html(renderStatus(rec.zonal_approver, rec.zonal_approver_name));
        $statusCells.eq(2).html(renderStatus(rec.final_approver, rec.final_approver_name));
        $statusCells.eq(3).html(renderStatus(rec.audit_approver, rec.audit_approver_name));
    } else if ($statusCells.length >= 4) {
        $statusCells.eq(0).html(renderStatus(rec.admin_approver, rec.admin_approver_name));
        $statusCells.eq(1).html(renderStatus(rec.zonal_approver, rec.zonal_approver_name));
        $statusCells.eq(2).html(renderStatus(rec.audit_approver, rec.audit_approver_name));
        $statusCells.eq(3).html(renderStatus(rec.final_approver, rec.final_approver_name));
    }

    $tr.find('.wifename').html('<a href="#">' + wname + '</a>');
    $tr.find('.husname').html('<a href="#">' + hname + '</a>');
    $tr.find('.authby').text(rec.ref_auth_by || '');
    $tr.find('.finalapprove').text(rec.ref_approved_by || '');
    $tr.find('.brno').text(rec.ref_branch_no || '');
    $tr.find('.finalamount').text(rec.ref_final_auth || '');
    $tr.find('.ref_id').text(id);

    return true;
}

// =====================================================
// APPROVAL ACTIONS
// =====================================================
$(document).on('click', '.print_ref_details', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var refId = $(this).data('id') || $(this).closest('.print_ref_details').data('id') || $(this).closest('tr').find('.ref_id').text().trim();
    if (!refId) return;
    $.ajax({
        url: refundform_edit,
        type: "GET",
        data: { ref_id: refId },
        success: function (res) {
            if (!res.exists || !res.record) {
                alert('Refund record not found.');
                return;
            }
            var d = res.record;
            function cleanPath(path) {
                if (!path) return '';
                try {
                    var arr = typeof path === 'string' ? JSON.parse(path) : path;
                    return Array.isArray(arr) ? arr[0] : (arr || '');
                } catch (e) {
                    return (path || '').replace(/[\[\]\"\\]/g, '');
                }
            }
            var baseUrl = (typeof PUBLIC_BASE_URL !== 'undefined' ? PUBLIC_BASE_URL : window.location.origin) + (window.location.pathname.indexOf('/public/') >= 0 ? '' : '/public');
            var wifeSign = cleanPath(d.ref_wife_sign);
            var husbandSign = cleanPath(d.ref_husband_sign);
            var drSign = cleanPath(d.ref_drsign);
            var adminSign = cleanPath(d.ref_admin_sign);
            var zonalSign = cleanPath(d.ref_zonal_sign);
            var signPath = function(p) { if (!p) return ''; return p.indexOf('http') === 0 ? p : baseUrl + '/' + p.replace(/^\/+/, ''); };
            var printContent = '<html><head><title>Refund Form Print</title><style>body{font-family:Arial,sans-serif;padding:20px;}.form-container{border:1px solid #000;padding:20px;}.header{display:flex;justify-content:space-between;align-items:center;font-weight:bold;font-size:18px;border-bottom:1px solid #000;padding-bottom:10px;}.form-title{font-size:22px;font-weight:bold;}.form-subtitle{font-size:12px;}.discount-box{border:2px solid #000;padding:5px 10px;display:inline-block;margin-left:10px;font-weight:bold;}.form-row-line{display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px;}.signatures{margin-top:40px;display:flex;justify-content:space-between;}.signature-box{text-align:center;}.signature-box img{max-height:50px;margin-top:10px;}</style></head><body><div class="form-container"><div class="header"><div><div class="form-title">Dr. ARAVIND\'s IVF</div><div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div></div><div class="discount-box">REFUND FORM</div><div>S. No: ' + (d.ref_id || '') + '</div></div><div class="form-row-line"><div>Branch: ' + (d.location_name || '') + '</div><div>Zone: ' + (d.zone_name || '') + '</div></div><div class="form-row-line"><div>Wife MRD: ' + (d.ref_wife_mrd_no || '') + ' - ' + (d.ref_wife_name || '') + '</div><div>Husband MRD: ' + (d.ref_husband_mrd_no || '') + ' - ' + (d.ref_husband_name || '') + '</div></div><div class="form-row-line"><div>Service: ' + (d.ref_service_name || '') + '</div><div>Total Bill: ' + (d.ref_total_bill || '') + '</div><div>Expected Request: ' + (d.ref_expected_request || '') + '</div></div><div class="form-row-line"><div>Request: ' + (d.ref_form_status || '') + '</div><div>Mobile: ' + (d.ref_patient_ph || '') + '</div><div>Counselled By: ' + (d.ref_counselled_by || '') + '</div></div><div class="form-row-line"><div>Final Auth: ' + (d.ref_final_auth || '') + '</div><div>Branch No: ' + (d.ref_branch_no || '') + '</div><div>Auth By: ' + (d.ref_auth_by || '') + '</div><div>Approved By: ' + (d.ref_approved_by || '') + '</div></div><div class="signatures"><div class="signature-box">Wife Sign' + (wifeSign ? '<br><img src="' + signPath(wifeSign) + '" alt="Wife">' : '') + '</div><div class="signature-box">Husband Sign' + (husbandSign ? '<br><img src="' + signPath(husbandSign) + '" alt="Husband">' : '') + '</div><div class="signature-box">Dr Sign' + (drSign ? '<br><img src="' + signPath(drSign) + '" alt="Dr">' : '') + '</div><div class="signature-box">Admin Sign' + (adminSign ? '<br><img src="' + signPath(adminSign) + '" alt="Admin">' : '') + '</div><div class="signature-box">Zonal Sign' + (zonalSign ? '<br><img src="' + signPath(zonalSign) + '" alt="Zonal">' : '') + '</div></div></div><script>window.onload=function(){window.print();};<\/script></body></html>';
            var printWindow = window.open('', '', 'width=1000,height=700');
            printWindow.document.write(printContent);
            printWindow.document.close();
        },
        error: function () {
            alert('Failed to load data for printing.');
        }
    });
});

$(document).on('click', '.approve-btn, .reject-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const id = $(this).data('id');
    const status = $(this).data('status');
    const $btn = $(this);
    const $row = $btn.closest('tr');

    if (status === 2) {
        $('#reject_reason_text').val('');
        $('#reject_reason_error').hide();
        $('#rejectReasonModal').data('reject-id', id).data('reject-row', $row).data('reject-btn', $btn).modal('show');
        return;
    }

    if (!confirm('Are you sure you want to approve this refund form?')) return;

    $.ajax({
        url: refundform_approval,
        type: "POST",
        data: {
            id: id,
            status: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                window.dispatchEvent(new CustomEvent('swal:toast', {
                    detail: {
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        background: 'success',
                    }
                }));
                refundsaveformdata();
            } else {
                window.dispatchEvent(new CustomEvent('swal:toast', {
                    detail: {
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        background: '#f8d7da',
                    }
                }));
            }
        },
        error: function (error) {
            console.error("Approval error:", error);
        }
    });
});

$('#reject_reason_submit_btn').on('click', function () {
    var reason = $('#reject_reason_text').val().trim();
    $('#reject_reason_error').hide();
    if (!reason) {
        $('#reject_reason_error').show();
        return;
    }
    var ids = $('#rejectReasonModal').data('reject-ids');
    var id = $('#rejectReasonModal').data('reject-id');
    var $row = $('#rejectReasonModal').data('reject-row');
    var $btn = $('#rejectReasonModal').data('reject-btn');
    if (ids && ids.length) {
        doBulkRejectRefund(ids, reason);
        $('#rejectReasonModal').modal('hide');
        $('#reject_reason_text').val('');
        $('#rejectReasonModal').data('reject-ids', null);
        return;
    }
    if (!id) return;
    $row.addClass('updating-row');
    if ($btn && $btn.length) $btn.css('opacity', '0.5');
    $.ajax({
        url: refundform_approval,
        type: 'POST',
        data: {
            id: id,
            status: 2,
            reject_reason: reason,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#rejectReasonModal').modal('hide');
            $('#reject_reason_text').val('');
            $row.removeClass('updating-row');
            if ($btn && $btn.length) $btn.css('opacity', '1');
            if (response.success) {
                window.dispatchEvent(new CustomEvent('swal:toast', {
                    detail: { title: 'Rejected!', text: response.message, icon: 'success', background: 'success' }
                }));
                refundsaveformdata();
            } else {
                window.dispatchEvent(new CustomEvent('swal:toast', {
                    detail: { title: 'Error!', text: response.message || 'Update failed', icon: 'error', background: '#f8d7da' }
                }));
            }
        },
        error: function (xhr) {
            $row.removeClass('updating-row');
            if ($btn && $btn.length) $btn.css('opacity', '1');
            window.dispatchEvent(new CustomEvent('swal:toast', {
                detail: { title: 'Error!', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Request failed', icon: 'error', background: '#f8d7da' }
            }));
        }
    });
});

$('#saved-select-all').on('change', function () {
    var checked = $(this).prop('checked');
    $('#sveddata_tbl .approve-row-cb').prop('checked', checked);
});

$('#btn-approve-selected, #btn-reject-selected').on('click', function () {
    var status = $(this).attr('id') === 'btn-approve-selected' ? 1 : 2;
    var actionText = status === 1 ? 'Approve' : 'Reject';
    var ids = [];
    $('#sveddata_tbl .approve-row-cb:checked').each(function () {
        ids.push($(this).data('id'));
    });
    if (!ids.length) {
        window.dispatchEvent(new CustomEvent('swal:toast', {
            detail: { title: 'No selection', text: 'Please select at least one row to ' + actionText.toLowerCase() + '.', icon: 'warning', background: '#fff3cd' }
        }));
        return;
    }
    if (status === 2) {
        $('#reject_reason_text').val('');
        $('#reject_reason_error').hide();
        $('#rejectReasonModal').data('reject-id', null).data('reject-row', null).data('reject-btn', null).data('reject-ids', ids).modal('show');
        return;
    }
    if (!confirm('You want to approve ' + ids.length + ' selected refund form(s). Continue?')) return;
    doBulkApproveRefund(ids);
});

function doBulkApproveRefund(ids) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var done = 0, total = ids.length, failed = [];
    function runNext() {
        if (done >= total) {
            window.dispatchEvent(new CustomEvent('swal:toast', {
                detail: {
                    title: failed.length ? 'Partially done' : 'Approved',
                    text: failed.length ? (total - failed.length) + ' succeeded, ' + failed.length + ' failed.' : 'All selected rows have been approved.',
                    icon: failed.length ? 'warning' : 'success',
                    background: failed.length ? '#fff3cd' : 'success'
                }
            }));
            refundsaveformdata();
            return;
        }
        $.ajax({
            url: refundform_approval,
            type: 'POST',
            data: { id: ids[done], status: 1, _token: token },
            success: function (res) {
                if (!res || !res.success) failed.push(ids[done]);
                done++;
                runNext();
            },
            error: function () {
                failed.push(ids[done]);
                done++;
                runNext();
            }
        });
    }
    runNext();
}

function doBulkRejectRefund(ids, reason) {
    var token = $('meta[name="csrf-token"]').attr('content');
    var done = 0, total = ids.length, failed = [];
    function runNext() {
        if (done >= total) {
            window.dispatchEvent(new CustomEvent('swal:toast', {
                detail: {
                    title: failed.length ? 'Partially done' : 'Rejected',
                    text: failed.length ? (total - failed.length) + ' succeeded, ' + failed.length + ' failed.' : 'All selected rows have been rejected.',
                    icon: failed.length ? 'warning' : 'success',
                    background: failed.length ? '#fff3cd' : 'success'
                }
            }));
            refundsaveformdata();
            return;
        }
        $.ajax({
            url: refundform_approval,
            type: 'POST',
            data: { id: ids[done], status: 2, reject_reason: reason, _token: token },
            success: function (res) {
                if (!res || !res.success) failed.push(ids[done]);
                done++;
                runNext();
            },
            error: function () {
                failed.push(ids[done]);
                done++;
                runNext();
            }
        });
    }
    runNext();
}

// =====================================================
// FILTERING - SAVED TAB
// =====================================================
$(document).on('click', '.savedata_options div', function () {
    $(".value_save_mysearch").text("");
    $('.clear_my_saveviews').show();
    $(".my_search_saveview").show();
    var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
    let resultsArray_marketer = [];
    $(".savedatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const result = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });
    const moreFilterValues_market = [
        $("#save_zone_views").val(),
        $('#save_loc_views').val(),
        $('#save_mrd_views').val()
    ];
    $(".value_save_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    refundsavefilterview(fitterremovedata, refundformsave_data, moredatefittervale, '', $('#save_status_filter').val());
});

$(document).on("click", ".value_save_mysearch", function () {

    var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");
    if (clear_filtr == 'savebranch_search') {
        $('#save_loc_views').val('');
    }
    if (clear_filtr == 'savezone_search') {
        $('#save_zone_views').val('');
    }
    if (clear_filtr == 'savemrdno_search') {
        $('#save_mrd_views').val('');
    }

    fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });
    refundsavefilterview(fitterremovedata, refundformsave_data, moredatefittervale, '', $('#save_status_filter').val());
});

$(document).on("click", ".clear_my_saveviews", function () {
    fitterremovedata.length = 0;
    $('.savedatasearch').val("");
    $('#save_status_filter').val("");
    $(".value_save_mysearch").text("");
    $('.clear_my_saveviews').hide();
    $(".my_search_saveview").hide();
    refundsaveformdata();
});

$('#save_mrd_views').on('input', function () {
    const phid = $(this).val().trim();
    $(".value_save_mysearch").text("");
    $('.clear_my_saveviews').show();
    $(".my_search_saveview").show();
    const moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
    let resultsArray_marketer = [];
    $(".savedatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const filterStr = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(filterStr);
        }

    });
    const moreFilterValues_market = [
        $("#save_zone_views").val(),
        $('#save_loc_views').val(),
        $('#save_mrd_views').val()
    ];

    $(".value_save_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || "";
        $(this).text(filterValue);
    });

    fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    refundsavefilterview(fitterremovedata, refundformsave_data, moredatefittervale, phid, $('#save_status_filter').val());
});

function refundsavefilterview(uniqueResults, url, moredatefittervale, ph_id = '', status_filter = '') {
    startLoader2();
    $("#sveddata_tbl").hide();
    if (uniqueResults != "" || status_filter != "") {
        var morefilltersall = (uniqueResults && uniqueResults.length) ? uniqueResults.join(" AND ") : "";
        $.ajax({
            url: url,
            type: "GET",
            data: {
                morefilltersall: morefilltersall,
                moredatefittervale: moredatefittervale,
                mrodnofilter: ph_id,
                status_filter: status_filter || $('#save_status_filter').val() || ''
            },
            success: function (responseData) {
                stopLoader2(true);
                handlesavedSuccess(responseData);
            },
            error: function (xhr, status, error) {
                stopLoader2(false, error);
                console.error("AJAX Error:", status, error);
            }
        });
    } else {
        $('.clear_my_saveviews').hide();
        $(".my_search_saveview").hide();
        refundsaveformdata();
    }
}

$('#save_status_filter').on('change', function () {
    var moredatefittervale = getDateRangeForApi('#mydateallviewssave', { allowAll: true });
    var phid = ($('#save_mrd_views').val() || '').trim();
    var statusVal = $(this).val();
    if (fitterremovedata && fitterremovedata.length) {
        refundsavefilterview(fitterremovedata, refundformsave_data, moredatefittervale, phid, statusVal);
    } else {
        refundsaveformdata();
    }
});
