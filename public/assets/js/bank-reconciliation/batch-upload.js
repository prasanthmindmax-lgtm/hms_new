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
            $('#batchTotalHint').text('');
            $('#batchPageInfo').text('');
            return;
        }
        var from = res.from != null ? res.from : 0;
        var to = res.to != null ? res.to : 0;
        $('#batchTotalHint').text('Total: ' + (res.total || 0));
        $('#batchPageInfo').text(from && to ? 'Showing ' + from + '–' + to + ' of ' + (res.total || 0) : '');

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
        var last = res.last_page || 1;
        var cur = res.current_page || 1;
        if (last <= 1) return;

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
