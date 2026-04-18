<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')

<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('/assets/css/tickets.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/licence_documents.css') }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<body class="ld-licence-branch-page" style="overflow-x: hidden;"
    data-ld-flash-success="{{ session('success') ? e(session('success')) : '' }}"
    data-ld-flash-error="{{ session('error') ? e(session('error')) : '' }}">
    <div class="page-loader">
        <div class="bar"></div>
    </div>

    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('superadmin.licence_documents.index') }}">Licence documents</a>
                                </li>
                                <li class="breadcrumb-item active">{{ $branch->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="qd-card tk-tickets-page" style="margin-bottom: 24px;">
                <div class="tk-hero">
                    <div class="tk-hero-inner d-flex justify-content-between align-items-center">

                        <h1 class="ld-hero-title d-flex align-items-center gap-2">
                            <i class="bi bi-building"></i>
                            {{ $branch->name }}

                            @if ($branch->zone)
                                <span class="ld-zone-badge">
                                    {{ $branch->zone->name }}
                                </span>
                            @endif
                        </h1>

                        <a class="ld-back-btn" href="{{ route('superadmin.licence_documents.index') }}">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="tk-dash-body" style="padding: 0 18px 22px;">
                    @php
                        $onFileCount = collect($documentRows)->whereNotNull('file_path')->count();
                    @endphp
                    <div class="ld-section mt-4">
                        <div class="ld-section-head ld-section-head--premium">
                            <div class="ld-section-head-text">
                                @if ($assignedLevel === 2)
                                    <span class="ld-level-pill ld-level-pill--l2">Level 2</span>
                                    <h2 class="ld-section-title">Level 2 documents</h2>
                                @else
                                    <span class="ld-level-pill">Level 1</span>
                                    <h2 class="ld-section-title">Level 1 documents</h2>
                                @endif
                                <p class="ld-section-sub">
                                    <strong>{{ $onFileCount }}</strong> of <strong>{{ $docTotal }}</strong> on file
                                    · <span class="ld-section-sub-muted">{{ $docTotal }} required for this branch</span>
                                </p>
                            </div>
                        </div>
                        <div class="ld-table-premium-wrap">
                            <table class="ld-table ld-table-premium">
                                <thead>
                                    <tr>
                                        <th class="ld-col-num" scope="col"><span class="ld-th-inner">#</span></th>
                                        <th class="ld-col-doc" scope="col"><span class="ld-th-inner">Document</span></th>
                                        <th class="ld-col-file" scope="col"><span class="ld-th-inner">Current file</span></th>
                                        <th class="ld-col-renew" scope="col"><span class="ld-th-inner">Renewal</span></th>
                                        <th class="ld-col-actions" scope="col"><span class="ld-th-inner">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documentRows as $row)
                                        <tr class="ld-doc-row ld-doc-row--{{ $row['status'] }}">
                                            <td class="ld-col-num">
                                                <span class="ld-row-idx"
                                                    aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td class="ld-col-doc">
                                                <div class="ld-doc-block">
                                                    <span class="ld-doc-title">{{ $row['label'] }}</span>
                                                    <span class="ld-status-chip ld-status-chip--{{ $row['status'] }}"
                                                        title="Compliance status for this row">{{ $row['status_label'] }}</span>
                                                </div>
                                            </td>
                                            <td class="ld-col-file">
                                                @if (!empty($row['file_path']))
                                                    @php $fileUrl = asset($row['file_path']); @endphp
                                                    <div class="ld-file-tile">
                                                        <span class="ld-file-tile-icon" aria-hidden="true"><i
                                                                class="bi bi-file-earmark-check"></i></span>
                                                        <div class="ld-file-tile-body">
                                                            <button type="button"
                                                                class="ld-file-tile-name ld-file-preview-trigger"
                                                                data-url="{{ $fileUrl }}"
                                                                data-name="{{ e($row['original_filename'] ?? '') }}"
                                                                data-doc="{{ e($row['label']) }}">
                                                                {{ $row['original_filename'] ?: 'View file' }}
                                                            </button>
                                                            @if (!empty($row['updated_at']))
                                                                <span class="ld-file-tile-meta">Updated
                                                                    {{ $row['updated_at'] }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="ld-file-empty">
                                                        <i class="bi bi-inbox" aria-hidden="true"></i>
                                                        <span>No file yet</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="ld-col-renew">
                                                @if (!empty($row['renewal_date']))
                                                    <span
                                                        class="ld-renew-pill ld-renew-pill--{{ $row['status'] }}">{{ \Carbon\Carbon::parse($row['renewal_date'])->format('d M Y') }}</span>
                                                @else
                                                    <span class="ld-renew-dash">—</span>
                                                @endif
                                            </td>
                                            <td class="ld-col-actions">
                                                <div class="ld-action-tools" role="group"
                                                    aria-label="Document actions for {{ e($row['label']) }}">
                                                    <button type="button"
                                                        class="ld-icon-btn ld-icon-btn--upload ld-open-doc-modal"
                                                        data-mode="upload" data-document-key="{{ e($row['key']) }}"
                                                        data-renewal="{{ e($row['renewal_date'] ?? '') }}"
                                                        data-label="{{ e($row['label']) }}"
                                                        title="Upload a document file" aria-label="Upload file">
                                                        <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="ld-icon-btn ld-icon-btn--update ld-open-doc-modal"
                                                        data-mode="update" data-document-key="{{ e($row['key']) }}"
                                                        data-renewal="{{ e($row['renewal_date'] ?? '') }}"
                                                        data-label="{{ e($row['label']) }}"
                                                        title="Update file or renewal date" aria-label="Update file or date">
                                                        <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="sm-modal-overlay" id="ldLicenceModalOverlay"></div>

    <div class="sm-modal" id="ldDocumentEditModal" role="dialog" aria-modal="true"
        aria-labelledby="ldDocumentEditTitle">
        <div class="sm-modal-box ld-lic-modal-box">
            <div class="sm-modal-header ld-lic-modal-header">
                <div class="ld-lic-modal-header-text">
                    <span id="ldDocumentEditModeBadge" class="ld-lic-modal-badge ld-lic-modal-badge--upload"
                        aria-live="polite">—</span>
                    <h2 class="sm-modal-title ld-lic-modal-title" id="ldDocumentEditTitle">—</h2>
                </div>
                <button type="button" class="sm-modal-close ld-licence-sm-close ld-lic-modal-close"
                    aria-label="Close dialog"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
            </div>
            <form id="ldDocumentEditForm" class="ld-lic-modal-form" method="post" novalidate
                action="{{ route('superadmin.licence_documents.save') }}" enctype="multipart/form-data"
                data-current-mode="upload">
                @csrf
                <input type="hidden" name="branch_id" id="ld_modal_branch_id" value="{{ (int) $branch->id }}">
                <input type="hidden" name="level" id="ld_modal_level" value="{{ (int) $assignedLevel }}">
                <input type="hidden" name="document_key" id="ld_modal_document_key" value="">

                <div class="ld-lic-field">
                    <label class="ld-lic-label" for="ld_modal_renewal">
                        <span class="ld-lic-label-icon" aria-hidden="true"><i class="bi bi-calendar-event"></i></span>
                        Renewal date <span class="ld-lic-req" aria-hidden="true">*</span>
                    </label>
                    <input type="date" class="form-control ld-lic-input" name="renewal_date" id="ld_modal_renewal"
                        value="" autocomplete="off" aria-describedby="ld_modal_renewal_err">
                    <p class="ld-lic-field-error" id="ld_modal_renewal_err" role="alert" hidden></p>
                </div>
                <div class="ld-lic-field ld-lic-field--file">
                    <label class="ld-lic-label" for="ld_modal_file" id="ld_modal_file_label">
                        <span class="ld-lic-label-icon" aria-hidden="true"><i class="bi bi-cloud-arrow-up"></i></span>
                        <span class="ld-lic-file-label-text">Document file</span>
                        <span class="ld-lic-req ld-lic-file-req" id="ld_modal_file_req" aria-hidden="true">*</span>
                    </label>
                    <div class="ld-lic-file-shell">
                        <input type="file" class="form-control ld-modal-file-input ld-lic-file-input" name="file"
                            id="ld_modal_file"
                            accept=".pdf,.png,.jpg,.jpeg,.gif,.webp,.doc,.docx,.xls,.xlsx"
                            aria-describedby="ld_modal_file_err">
                    </div>
                    <p class="ld-lic-field-error" id="ld_modal_file_err" role="alert" hidden></p>
                </div>

                <div class="sm-modal-footer ld-lic-modal-footer">
                    <button type="button" class="sm-btn-cancel ld-licence-sm-close ld-lic-btn-cancel">Cancel</button>
                    <button type="submit" class="sm-btn-primary ld-lic-btn-save"><i class="bi bi-check2-circle"
                            aria-hidden="true"></i><span>Save</span></button>
                </div>
            </form>
        </div>
    </div>

    <div class="sm-modal" id="ldFilePreviewModal" role="dialog" aria-modal="true"
        aria-labelledby="ldFilePreviewModalLabel">
        <div class="sm-modal-box wide ld-lic-modal-box ld-lic-preview-modal-box">
            <div class="sm-modal-header ld-lic-modal-header">
                <div class="ld-lic-modal-header-text ld-lic-preview-header-text">
                    <span class="ld-lic-preview-kicker">Current file</span>
                    <h2 class="sm-modal-title ld-lic-modal-title" id="ldFilePreviewModalLabel">—</h2>
                    <p class="ld-lic-preview-subtitle d-none" id="ldFilePreviewSubtitle"></p>
                </div>
                <button type="button" class="sm-modal-close ld-licence-sm-close ld-lic-modal-close"
                    aria-label="Close dialog"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
            </div>
            <div id="ldFilePreviewBody" class="ld-lic-preview-body">
                <div class="ld-lic-preview-placeholder">Loading…</div>
            </div>
            <div class="sm-modal-footer ld-lic-modal-footer ld-lic-preview-footer">
                <a href="#" class="sm-btn-primary text-decoration-none ld-lic-btn-open-tab" id="ldFilePreviewOpenTab"
                    target="_blank" rel="noopener">
                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span>Open in new tab</span>
                </a>
                <button type="button" class="sm-btn-cancel ld-licence-sm-close ld-lic-btn-cancel">Close</button>
            </div>
        </div>
    </div>

    @include('superadmin.superadminfooter')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        (function() {
            if (typeof toastr === 'undefined') {
                return;
            }
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000,
            };
            var body = document.body;
            var ok = body.getAttribute('data-ld-flash-success') || '';
            var err = body.getAttribute('data-ld-flash-error') || '';
            if (ok) {
                toastr.success(ok);
            }
            if (err) {
                toastr.error(err);
            }
        })();
    </script>
    <script>
        (function() {
            'use strict';

            var overlay = document.getElementById('ldLicenceModalOverlay');
            var editModalEl = document.getElementById('ldDocumentEditModal');
            var previewModalEl = document.getElementById('ldFilePreviewModal');
            var ldEditForm = document.getElementById('ldDocumentEditForm');
            var LD_MAX_FILE_BYTES = 15360 * 1024;
            var LD_FILE_EXT_RE = /\.(pdf|png|jpe?g|gif|webp|doc|docx|xls|xlsx)$/i;

            function ldClearLicModalErrors() {
                var re = document.getElementById('ld_modal_renewal_err');
                var fe = document.getElementById('ld_modal_file_err');
                var rin = document.getElementById('ld_modal_renewal');
                var fin = document.getElementById('ld_modal_file');
                if (re) {
                    re.textContent = '';
                    re.hidden = true;
                }
                if (fe) {
                    fe.textContent = '';
                    fe.hidden = true;
                }
                if (rin) {
                    rin.classList.remove('is-invalid');
                }
                if (fin) {
                    fin.classList.remove('is-invalid');
                }
            }

            function ldSetRenewalError(msg) {
                var el = document.getElementById('ld_modal_renewal_err');
                var rin = document.getElementById('ld_modal_renewal');
                if (el) {
                    el.textContent = msg;
                    el.hidden = false;
                }
                if (rin) {
                    rin.classList.add('is-invalid');
                }
            }

            function ldSetFileError(msg) {
                var el = document.getElementById('ld_modal_file_err');
                var fin = document.getElementById('ld_modal_file');
                if (el) {
                    el.textContent = msg;
                    el.hidden = false;
                }
                if (fin) {
                    fin.classList.add('is-invalid');
                }
            }

            function ldResetPreviewBody() {
                var bodyEl = document.getElementById('ldFilePreviewBody');
                if (!bodyEl) return;
                bodyEl.textContent = '';
                var ph = document.createElement('div');
                ph.className = 'ld-lic-preview-placeholder';
                ph.textContent = 'Loading…';
                bodyEl.appendChild(ph);
            }

            function ldCloseLicenceModals() {
                if (editModalEl) editModalEl.classList.remove('show');
                if (previewModalEl) previewModalEl.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
                document.body.style.overflow = '';
                ldResetPreviewBody();
                ldClearLicModalErrors();
            }

            /** Same behaviour as Ticket Management `openModal`: overlay + .show on target sm-modal */
            function ldOpenLicenceModal(which) {
                if (!overlay) return;
                if (which === 'edit' && editModalEl) {
                    if (previewModalEl) previewModalEl.classList.remove('show');
                    editModalEl.classList.add('show');
                } else if (which === 'preview' && previewModalEl) {
                    if (editModalEl) editModalEl.classList.remove('show');
                    previewModalEl.classList.add('show');
                }
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            document.querySelectorAll('.ld-licence-sm-close').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    ldCloseLicenceModals();
                });
            });
            if (overlay) {
                overlay.addEventListener('click', function() {
                    ldCloseLicenceModals();
                });
            }
            [editModalEl, previewModalEl].forEach(function(shell) {
                if (!shell) return;
                shell.addEventListener('click', function(e) {
                    if (e.target === shell) ldCloseLicenceModals();
                });
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') ldCloseLicenceModals();
            });

            /* ── Upload / Update (sm-modal) ── */
            if (editModalEl) {
                document.querySelectorAll('.ld-open-doc-modal').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var mode = btn.getAttribute('data-mode') || 'upload';
                        var key = btn.getAttribute('data-document-key') || '';
                        var renewal = btn.getAttribute('data-renewal') || '';
                        var label = btn.getAttribute('data-label') || 'Document';

                        ldClearLicModalErrors();

                        document.getElementById('ld_modal_document_key').value = key;
                        document.getElementById('ld_modal_renewal').value = renewal;
                        var fileEl = document.getElementById('ld_modal_file');
                        if (fileEl) fileEl.value = '';

                        if (ldEditForm) {
                            ldEditForm.setAttribute('data-current-mode', mode);
                        }

                        var fileReq = document.getElementById('ld_modal_file_req');
                        if (fileReq) {
                            fileReq.hidden = mode !== 'upload';
                        }
                        var fileLabel = document.getElementById('ld_modal_file_label');
                        if (fileLabel) {
                            fileLabel.setAttribute('aria-required', mode === 'upload' ? 'true' : 'false');
                        }

                        document.getElementById('ldDocumentEditTitle').textContent = label;
                        var badge = document.getElementById('ldDocumentEditModeBadge');
                        if (badge) {
                            badge.className = 'ld-lic-modal-badge ' + (mode === 'update' ?
                                'ld-lic-modal-badge--update' : 'ld-lic-modal-badge--upload');
                            badge.textContent = mode === 'update' ? 'Update' : 'Upload';
                        }

                        ldOpenLicenceModal('edit');
                        var renewalIn = document.getElementById('ld_modal_renewal');
                        if (renewalIn) {
                            renewalIn.focus();
                        }
                    });
                });

                if (ldEditForm) {
                    var rin = document.getElementById('ld_modal_renewal');
                    var fin = document.getElementById('ld_modal_file');
                    if (rin) {
                        rin.addEventListener('input', function() {
                            ldClearLicModalErrors();
                        });
                    }
                    if (fin) {
                        fin.addEventListener('change', function() {
                            ldClearLicModalErrors();
                        });
                    }

                    ldEditForm.addEventListener('submit', function(ev) {
                        ldClearLicModalErrors();
                        var mode = ldEditForm.getAttribute('data-current-mode') || 'upload';
                        var renewalVal = rin && rin.value ? rin.value.trim() : '';

                        if (!renewalVal) {
                            ev.preventDefault();
                            ldSetRenewalError('Please select a renewal date.');
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Please select a renewal date.');
                            }
                            if (rin) {
                                rin.focus();
                            }
                            return;
                        }

                        var t = renewalVal.split('-');
                        if (t.length !== 3 || isNaN(Date.parse(renewalVal))) {
                            ev.preventDefault();
                            ldSetRenewalError('Please enter a valid renewal date.');
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Please enter a valid renewal date.');
                            }
                            if (rin) {
                                rin.focus();
                            }
                            return;
                        }

                        if (mode === 'upload') {
                            if (!fin || !fin.files || !fin.files.length) {
                                ev.preventDefault();
                                ldSetFileError('Please choose a file to upload.');
                                if (typeof toastr !== 'undefined') {
                                    toastr.error('Please choose a file to upload.');
                                }
                                if (fin) {
                                    fin.focus();
                                }
                                return;
                            }
                        }

                        if (fin && fin.files && fin.files.length) {
                            var f = fin.files[0];
                            if (!LD_FILE_EXT_RE.test(f.name)) {
                                ev.preventDefault();
                                ldSetFileError('Allowed types: PDF, images, Word, Excel.');
                                if (typeof toastr !== 'undefined') {
                                    toastr.error('This file type is not allowed.');
                                }
                                fin.focus();
                                return;
                            }
                            if (f.size > LD_MAX_FILE_BYTES) {
                                ev.preventDefault();
                                ldSetFileError('Maximum file size is 15 MB.');
                                if (typeof toastr !== 'undefined') {
                                    toastr.error('File is too large (max 15 MB).');
                                }
                                fin.focus();
                                return;
                            }
                        }

                    });
                }
            }

            function fileExt(name, url) {
                var s = (name || '').trim();
                if (s && s.indexOf('.') !== -1) return s.split('.').pop().toLowerCase();
                try {
                    var u = new URL(url, window.location.origin);
                    var p = u.pathname || '';
                    var i = p.lastIndexOf('.');
                    return i === -1 ? '' : p.slice(i + 1).toLowerCase();
                } catch (err) {
                    return '';
                }
            }

            function renderPreview(bodyEl, url, ext) {
                bodyEl.textContent = '';
                var imgExt = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'svg'];
                if (ext === 'pdf') {
                    var ifr = document.createElement('iframe');
                    ifr.className = 'ld-preview-iframe';
                    ifr.title = 'PDF preview';
                    ifr.src = url + (url.indexOf('#') === -1 ? '#toolbar=1' : '');
                    bodyEl.appendChild(ifr);
                    return;
                }
                if (imgExt.indexOf(ext) !== -1) {
                    var wrap = document.createElement('div');
                    wrap.className = 'p-3 text-center bg-white';
                    var img = document.createElement('img');
                    img.src = url;
                    img.className = 'img-fluid rounded shadow-sm';
                    img.style.maxHeight = '78vh';
                    img.alt = '';
                    wrap.appendChild(img);
                    bodyEl.appendChild(wrap);
                    return;
                }
                var msg = document.createElement('div');
                msg.className = 'ld-lic-preview-msg';
                msg.innerHTML = '<p class="ld-lic-preview-msg-title">No inline preview for <strong>.' + (ext || 'file') +
                    '</strong> files.</p>' +
                    '<p class="ld-lic-preview-msg-hint">Use <strong>Open in new tab</strong> below to view or download.</p>';
                bodyEl.appendChild(msg);
            }

            document.querySelectorAll('.ld-file-preview-trigger').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var url = btn.getAttribute('data-url');
                    var name = btn.getAttribute('data-name') || '';
                    var docLabel = btn.getAttribute('data-doc') || 'Document';
                    if (!url) return;

                    var ext = fileExt(name, url);
                    var titleEl = document.getElementById('ldFilePreviewModalLabel');
                    var subEl = document.getElementById('ldFilePreviewSubtitle');
                    var bodyEl = document.getElementById('ldFilePreviewBody');
                    var openTab = document.getElementById('ldFilePreviewOpenTab');

                    titleEl.textContent = docLabel;
                    if (name) {
                        subEl.textContent = name;
                        subEl.classList.remove('d-none');
                    } else {
                        subEl.textContent = '';
                        subEl.classList.add('d-none');
                    }
                    openTab.href = url;

                    bodyEl.textContent = '';
                    renderPreview(bodyEl, url, ext);

                    ldOpenLicenceModal('preview');
                });
            });
        })();
    </script>
</body>

</html>
