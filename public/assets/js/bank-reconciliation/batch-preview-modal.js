/**
 * Batch upload preview: full paginated statement grid (shared: reconciliation + batch page).
 * Pagination / per-page: only table + toolbar line + pager refresh (meta card stays put).
 */
(function ($) {
    'use strict';

    var state = { batchId: null, perPage: 25 };

    function showBsModal(modalEl) {
        if (!modalEl) return;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
                new bootstrap.Modal(modalEl).show();
            }
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(modalEl).modal('show');
        }
    }

    function esc(s) {
        if (s == null || s === '') return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;');
    }

    function fmtMoney(v) {
        if (v == null || v === '') return '—';
        var n = parseFloat(v);
        if (isNaN(n)) return '—';
        return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function fmtTs(iso) {
        if (!iso) return '—';
        try {
            var d = new Date(String(iso).replace(' ', 'T'));
            return isNaN(d.getTime()) ? iso : d.toLocaleString();
        } catch (e) {
            return iso;
        }
    }

    function statusClass(st) {
        return String(st || 'none').replace(/[^a-z0-9_-]/gi, '').toLowerCase() || 'none';
    }

    function buildMetaHtml(res) {
        var B = res.batch;
        var total = res.total != null ? res.total : (res.rows || []).length;
        return '<div class="br-bpv-meta">' +
            '<div class="br-bpv-meta-grid">' +
            '<div class="br-bpv-meta-item"><span class="br-bpv-meta-label">Batch serial</span>' +
            '<span class="br-bpv-meta-value">#' + esc(B.id) + '</span></div>' +
            '<div class="br-bpv-meta-item"><span class="br-bpv-meta-label">Upload batch ID</span>' +
            '<span class="br-bpv-meta-value br-bpv-mono">' + esc(B.upload_batch_id) + '</span></div>' +
            '<div class="br-bpv-meta-item"><span class="br-bpv-meta-label">Account</span>' +
            '<span class="br-bpv-meta-value">' + esc(B.account_number) +
            (B.bank_name ? ' · ' + esc(B.bank_name) : '') + '</span></div>' +
            '<div class="br-bpv-meta-item"><span class="br-bpv-meta-label">File</span>' +
            '<span class="br-bpv-meta-value">' + esc(B.original_file_name) + '</span></div>' +
            '<div class="br-bpv-meta-item"><span class="br-bpv-meta-label">Uploaded</span>' +
            '<span class="br-bpv-meta-value">' + esc(fmtTs(B.created_at)) + '</span></div>' +
            '<div class="br-bpv-meta-item"><span class="br-bpv-meta-label">By</span>' +
            '<span class="br-bpv-meta-value">' + esc(B.uploaded_by_name || B.uploaded_by_username || '—') + '</span></div>' +
            '<div class="br-bpv-meta-item br-bpv-meta-wide"><span class="br-bpv-meta-label">Import summary</span>' +
            '<span class="br-bpv-meta-value">' +
            '<span class="br-bpv-pill br-bpv-pill-ok">' + esc(B.rows_imported) + ' imported</span> ' +
            '<span class="br-bpv-pill br-bpv-pill-warn">' + esc(B.duplicates) + ' duplicates</span> ' +
            '<span class="br-bpv-pill br-bpv-pill-muted">' + esc(B.skipped) + ' skipped</span>' +
            ' · <strong>' + esc(total) + '</strong> rows in database</span></div>' +
            '</div></div>';
    }

    function buildToolbarHtml(res) {
        var total = res.total != null ? res.total : (res.rows || []).length;
        var from = res.from;
        var to = res.to;
        var perPage = res.per_page || state.perPage;
        return '<div class="br-bpv-toolbar">' +
            '<div class="br-bpv-toolbar-info text-muted small">Showing ' +
            (from != null ? from : 0) + '–' + (to != null ? to : 0) +
            ' of <strong>' + esc(total) + '</strong> transactions</div>' +
            '<div class="d-flex align-items-center gap-2">' +
            '<label class="small text-muted mb-0">Per page</label>' +
            '<select class="form-select form-select-sm br-bpv-perpage" id="batchPreviewPerPage" style="width: auto;">' +
            [[10, 10], [25, 25], [50, 50], [100, 100]].map(function (o) {
                return '<option value="' + o[0] + '"' + (perPage === o[1] ? ' selected' : '') + '>' + o[0] + '</option>';
            }).join('') +
            '</select></div></div>';
    }

    function buildTableScrollHtml(res) {
        var rows = res.rows || [];
        var cur = res.current_page || 1;
        var perPage = res.per_page || state.perPage;
        var baseIdx = (cur - 1) * perPage;

        var table = '<div class="br-bpv-table-scroll"><table class="br-bpv-table"><thead><tr>' +
            '<th class="br-bpv-th-num">Sn</th>' +
            '<th>Txn date</th><th>Value</th><th>Posted</th>' +
            '<th>Txn ID</th><th>Reference</th><th>Cheque</th>' +
            '<th>Description</th>' +
            '<th class="text-end">Withdrawal</th><th class="text-end">Deposit</th><th class="text-end">Balance</th>' +
            '<th>Category</th><th>Match</th>' +
            '</tr></thead><tbody>';

        if (!rows.length) {
            table += '<tr><td colspan="13" class="text-center text-muted py-5">No statement lines in this batch.</td></tr>';
        } else {
            rows.forEach(function (r, idx) {
                var w = parseFloat(r.withdrawal) || 0;
                var d = parseFloat(r.deposit) || 0;
                var wClass = w > 0 ? 'br-bpv-neg' : '';
                var dClass = d > 0 ? 'br-bpv-pos' : '';
                var st = statusClass(r.match_status);
                table += '<tr>' +
                    '<td class="br-bpv-td-num text-muted">' + (baseIdx + idx + 1) + '</td>' +
                    '<td><small>' + esc(r.transaction_date) + '</small></td>' +
                    '<td><small>' + esc(r.value_date || '—') + '</small></td>' +
                    '<td><small>' + esc(r.transaction_posted_date || '—') + '</small></td>' +
                    '<td><code class="br-bpv-mono small">' + esc(r.transaction_id || '—') + '</code></td>' +
                    '<td><code class="br-bpv-mono small">' + esc(r.reference_number || '—') + '</code></td>' +
                    '<td><small>' + esc(r.cheque_number || '—') + '</small></td>' +
                    '<td class="br-bpv-desc"><span class="br-bpv-desc-inner" title="' + esc(r.description || '') + '">' +
                    esc(r.description || '') + '</span></td>' +
                    '<td class="text-end ' + wClass + '">' + (w > 0 ? fmtMoney(w) : '—') + '</td>' +
                    '<td class="text-end ' + dClass + '">' + (d > 0 ? fmtMoney(d) : '—') + '</td>' +
                    '<td class="text-end br-bpv-balance">' + fmtMoney(r.balance) + '</td>' +
                    '<td><span class="br-bpv-badge">' + esc(r.category || '—') + '</span></td>' +
                    '<td><span class="br-bpv-status br-bpv-st-' + st + '">' + esc(r.match_status || '—') + '</span></td>' +
                    '</tr>';
            });
        }
        table += '</tbody></table></div>';
        return table;
    }

    function buildPaginationHtml(res) {
        var cur = res.current_page || 1;
        var last = res.last_page || 1;
        var pag = '<nav class="br-bpv-nav mt-2"><ul class="pagination pagination-sm justify-content-center flex-wrap mb-0">';
        if (last > 1) {
            function item(label, p, dis, active) {
                return '<li class="page-item' + (dis ? ' disabled' : '') + (active ? ' active' : '') + '">' +
                    '<a class="page-link br-bpv-page" href="#" data-page="' + p + '">' + label + '</a></li>';
            }
            pag += item('«', cur - 1, cur <= 1, false);
            var s = Math.max(1, cur - 2);
            var e = Math.min(last, cur + 2);
            for (var p = s; p <= e; p++) {
                pag += item(String(p), p, false, p === cur);
            }
            pag += item('»', cur + 1, cur >= last, false);
        } else {
            pag += '<li class="page-item disabled"><span class="page-link text-muted">Page 1 of 1</span></li>';
        }
        pag += '</ul></nav>';
        return pag;
    }

    function buildFullRootHtml(res) {
        return '<div class="br-bpv-root" id="brBpvRoot">' +
            '<div id="brBpvMetaHost">' + buildMetaHtml(res) + '</div>' +
            '<div id="brBpvToolbarHost">' + buildToolbarHtml(res) + '</div>' +
            '<div id="brBpvTableHost">' + buildTableScrollHtml(res) + '</div>' +
            '<div id="brBpvPaginationHost">' + buildPaginationHtml(res) + '</div>' +
            '</div>';
    }

    function applyPreviewPartial(res) {
        $('#brBpvToolbarHost').html(buildToolbarHtml(res));
        $('#brBpvTableHost').html(buildTableScrollHtml(res));
        $('#brBpvPaginationHost').html(buildPaginationHtml(res));
        var sc = document.querySelector('#brBpvTableHost .br-bpv-table-scroll');
        if (sc) {
            sc.scrollTop = 0;
        }
    }

    function setTableUpdating(on) {
        var $t = $('#brBpvTableHost');
        if (!$t.length) return;
        if (on) {
            $t.addClass('br-bpv-table-host-updating');
        } else {
            $t.removeClass('br-bpv-table-host-updating');
        }
    }

    function loadBatchPreview(batchId, page, partial) {
        var base = window.BANK_RECON_BATCH_PREVIEW_BASE;
        if (!base || !batchId) return;

        var canPartial = !!partial && $('#brBpvRoot').length;

        if (!canPartial) {
            $('#batchPreviewBody').html(
                '<div class="br-bpv-loading text-center py-5 text-muted">' +
                '<div class="spinner-border br-bpv-spinner" role="status"></div>' +
                '<p class="mt-3 mb-0">Loading all batch transactions…</p></div>'
            );
        } else {
            setTableUpdating(true);
        }

        $.get(base + '/' + encodeURIComponent(batchId), { page: page || 1, per_page: state.perPage }, function (res) {
            if (!res.batch) {
                $('#batchPreviewBody').html('<p class="text-danger p-3 mb-0">Batch not found.</p>');
                setTableUpdating(false);
                return;
            }
            state.perPage = res.per_page || state.perPage;

            if (canPartial) {
                applyPreviewPartial(res);
                setTableUpdating(false);
            } else {
                $('#batchPreviewBody').html(buildFullRootHtml(res));
                var sc = document.querySelector('#batchPreviewBody .br-bpv-table-scroll');
                if (sc) {
                    sc.scrollTop = 0;
                }
            }
        }).fail(function () {
            setTableUpdating(false);
            if (!canPartial) {
                $('#batchPreviewBody').html('<p class="text-danger p-3 mb-0">Could not load preview.</p>');
            }
        });
    }

    $(document).on('click', '.btn-batch-preview', function () {
        var bid = $(this).data('batch');
        if (!bid) return;
        if (!window.BANK_RECON_BATCH_PREVIEW_BASE) return;
        state.batchId = bid;
        state.perPage = 25;
        showBsModal(document.getElementById('batchPreviewModal'));
        loadBatchPreview(bid, 1, false);
    });

    $(document).on('change', '#batchPreviewPerPage', function () {
        state.perPage = parseInt($(this).val(), 10) || 25;
        if (state.batchId) {
            loadBatchPreview(state.batchId, 1, true);
        }
    });

    $(document).on('click', '.br-bpv-page', function (e) {
        e.preventDefault();
        var $li = $(this).closest('li');
        if ($li.hasClass('disabled') || $li.hasClass('active')) return;
        var p = parseInt($(this).data('page'), 10);
        if (!p || !state.batchId) return;
        loadBatchPreview(state.batchId, p, true);
    });
})(jQuery);
