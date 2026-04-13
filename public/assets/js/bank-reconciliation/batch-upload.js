/* global $, toastr */
(function () {
    var cfg = window.bankBatchPage || {};
    if (!cfg.bankAccountsEnabled) {
        return;
    }

    var batchFilters = {};
    var batchPerPage = 25;
    var batchCurrentPage = 1;

    function esc(s) {
        if (s == null || s === '') return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;');
    }

    function fmtTs(iso) {
        if (!iso) return '-';
        try {
            var d = new Date(iso.replace(' ', 'T'));
            return isNaN(d.getTime()) ? iso : d.toLocaleString();
        } catch (e) {
            return iso;
        }
    }

    function readFiltersFromUi() {
        batchFilters = {};
        var acc = $('#fltAccount').val().trim();
        var fn = $('#fltFile').val().trim();
        var u = $('#fltUser').val().trim();
        var df = $('#fltDateFrom').val();
        var dt = $('#fltDateTo').val();
        if (acc) batchFilters.account_number = acc;
        if (fn) batchFilters.file_name = fn;
        if (u) batchFilters.uploaded_by = u;
        if (df) batchFilters.date_from = df;
        if (dt) batchFilters.date_to = dt;
        batchPerPage = parseInt($('#fltPerPage').val(), 10) || 25;
    }

    function loadBatches(page) {
        batchCurrentPage = page || 1;
        readFiltersFromUi();
        var params = $.extend({ page: batchCurrentPage, per_page: batchPerPage }, batchFilters);
        $('#batchTableBody').html('<tr><td colspan="9" class="text-center py-4 text-muted">Loading…</td></tr>');
        $.get(cfg.uploadBatches, params, function (res) {
            renderBatchTable(res);
            renderBatchPagination(res);
        }).fail(function () {
            toastr.error('Could not load batches');
            $('#batchTableBody').html('<tr><td colspan="9" class="text-center py-4 text-danger">Failed to load</td></tr>');
        });
    }

    function renderBatchTable(res) {
        var rows = res.data || [];
        var tbody = $('#batchTableBody');
        tbody.empty();
        if (!rows.length) {
            tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">No batches match your filters.</td></tr>');
            var ztot = parseInt(res.total, 10) || 0;
            $('#batchTotalHint').text(ztot ? 'Total: ' + ztot : 'Total: 0');
            $('#batchPageInfo').text('');
            return;
        }
        var total = parseInt(res.total, 10) || 0;
        var from = res.from != null ? parseInt(res.from, 10) : null;
        var to = res.to != null ? parseInt(res.to, 10) : null;
        $('#batchTotalHint').text(total ? 'Total: ' + total : 'Total: 0');
        if (!total) {
            $('#batchPageInfo').text('');
        } else if (from != null && to != null && !isNaN(from) && !isNaN(to)) {
            $('#batchPageInfo').text('Showing ' + from + '–' + to + ' of ' + total + ' · page ' + (parseInt(res.current_page, 10) || 1) + ' / ' + (parseInt(res.last_page, 10) || 1));
        } else {
            $('#batchPageInfo').text('Page ' + (parseInt(res.current_page, 10) || 1) + ' of ' + (parseInt(res.last_page, 10) || 1) + ' · ' + total + ' total');
        }

        rows.forEach(function (b) {
            var uid = esc(b.upload_batch_id);
            var dl = cfg.batchFileBase + '/' + encodeURIComponent(b.upload_batch_id);
            var by = b.uploaded_by_name || b.uploaded_by_username || '-';
            var tr = '<tr>' +
                '<td><span class="badge bg-secondary">' + esc(b.id) + '</span></td>' +
                '<td><small>' + esc(fmtTs(b.created_at)) + '</small></td>' +
                '<td><strong>' + esc(b.account_number) + '</strong>' +
                (b.bank_name ? '<br><small class="text-muted">' + esc(b.bank_name) + '</small>' : '') + '</td>' +
                '<td><small>' + esc(b.original_file_name) + '</small><br><code class="small">' + esc(b.upload_batch_id) + '</code></td>' +
                '<td>' + esc(b.rows_imported) + '</td>' +
                '<td>' + esc(b.duplicates) + '</td>' +
                '<td>' + esc(b.skipped) + '</td>' +
                '<td><small>' + esc(by) + '</small></td>' +
                '<td class="text-end text-nowrap">' +
                '<a class="btn btn-sm btn-outline-primary me-1" href="' + dl + '" title="Download"><i class="bi bi-download"></i></a>' +
                '<button type="button" class="btn btn-sm btn-outline-secondary btn-batch-preview" data-batch="' + uid + '">' +
                '<i class="bi bi-eye"></i></button>' +
                '</td></tr>';
            tbody.append(tr);
        });
    }

    function renderBatchPagination(res) {
        var ul = $('#batchPagination');
        ul.empty();
        var last = parseInt(res.last_page, 10) || 1;
        var cur = parseInt(res.current_page, 10) || 1;
        var total = parseInt(res.total, 10) || 0;
        if (total === 0 || last <= 1) {
            return;
        }

        if (res.links && Array.isArray(res.links) && res.links.length) {
            res.links.forEach(function (lnk) {
                var isActive = !!lnk.active;
                var hasUrl = !!(lnk.url && String(lnk.url).trim());
                var li = $('<li>').addClass('page-item').toggleClass('active', isActive).toggleClass('disabled', !hasUrl && !isActive);
                var a = $('<a class="page-link" href="#">').attr('href', '#').html(String(lnk.label == null ? '' : lnk.label));
                if (hasUrl && !isActive) {
                    a.on('click', function (e) {
                        e.preventDefault();
                        try {
                            var u = new URL(lnk.url, window.location.origin);
                            var p = parseInt(u.searchParams.get('page'), 10) || 1;
                            loadBatches(p);
                        } catch (err) {
                            loadBatches(cur);
                        }
                    });
                } else {
                    a.on('click', function (e) { e.preventDefault(); });
                }
                li.append(a);
                ul.append(li);
            });
            return;
        }

        function addLi(label, p, disabled, active) {
            var li = $('<li class="page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '') + '">');
            var a = $('<a class="page-link" href="#">').text(label);
            if (!disabled && !active) {
                a.on('click', function (e) {
                    e.preventDefault();
                    loadBatches(p);
                });
            }
            li.append(a);
            ul.append(li);
        }

        addLi('«', cur - 1, cur <= 1, false);
        var start = Math.max(1, cur - 2);
        var end = Math.min(last, cur + 2);
        for (var p = start; p <= end; p++) {
            addLi(String(p), p, false, p === cur);
        }
        addLi('»', cur + 1, cur >= last, false);
    }

    $('#btnApplyBatchFilters').on('click', function () {
        loadBatches(1);
    });

    $('#btnClearBatchFilters').on('click', function () {
        $('#fltAccount,#fltFile,#fltUser').val('');
        $('#fltDateFrom,#fltDateTo').val('');
        $('#fltPerPage').val('25');
        batchFilters = {};
        loadBatches(1);
    });

    $('#fltPerPage').on('change', function () {
        loadBatches(1);
    });

    $(function () {
        loadBatches(1);
    });
})();
