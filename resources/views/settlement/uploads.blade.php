<!doctype html>
<html lang="en">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('superadmin.superadminhead')
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

<style>
/* ════════════════════════════════════════════════════════════════
   SETTLEMENT FILE MONITOR — Design System
   Palette: Deep navy + warm amber + teal (matches Radiant RCP)
════════════════════════════════════════════════════════════════ */
:root {
    --navy:     #0f172a;
    --navy2:    #1e293b;
    --navy3:    #334155;
    --amber:    #f59e0b;
    --amber2:   #d97706;
    --amber-lt: #fef3c7;
    --teal:     #0d9488;
    --teal-lt:  #ccfbf1;
    --rose:     #f43f5e;
    --rose-lt:  #ffe4e6;
    --violet:   #8b5cf6;
    --violet-lt:#ede9fe;
    --surface:  #fff;
    --surface2: #f8fafc;
    --border:   #e2e8f0;
    --border2:  #cbd5e1;
    --text:     #0f172a;
    --text2:    #475569;
    --text3:    #94a3b8;
    --radius:   14px;
    --radius-sm:9px;
    --shadow:   0 4px 24px rgba(15,23,42,.08);
    --shadow-lg:0 16px 48px rgba(15,23,42,.14);
    --font:     'Plus Jakarta Sans', sans-serif;
    --mono:     'JetBrains Mono', monospace;
}
*{box-sizing:border-box;}
body{font-family:var(--font);background:#f1f5f9;}

/* ── WRAPPER ── */
.sfm-wrap{padding:28px 24px;}

/* ── PAGE HEADER ── */
.sfm-header{
    background:linear-gradient(135deg,var(--navy) 0%,var(--navy2) 60%,#1e3a5f 100%);
    border-radius:20px;padding:28px 32px;margin-bottom:24px;
    position:relative;overflow:hidden;
}
.sfm-header::before{
    content:'';position:absolute;top:-60px;right:-60px;
    width:260px;height:260px;border-radius:50%;
    background:radial-gradient(circle,rgba(245,158,11,.15),transparent 70%);
}
.sfm-header::after{
    content:'';position:absolute;bottom:-40px;left:160px;
    width:180px;height:180px;border-radius:50%;
    background:radial-gradient(circle,rgba(13,148,136,.12),transparent 70%);
}
.sfm-header-inner{position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;}
.sfm-header-title{display:flex;align-items:center;gap:16px;}
.sfm-header-icon{
    width:52px;height:52px;background:rgba(245,158,11,.15);
    border:1px solid rgba(245,158,11,.3);border-radius:14px;
    display:flex;align-items:center;justify-content:center;font-size:1.5rem;
}
.sfm-header-text h1{font-size:1.4rem;font-weight:800;color:#fff;margin:0 0 3px;letter-spacing:-.4px;}
.sfm-header-text p{font-size:.8rem;color:rgba(255,255,255,.6);margin:0;}
.sfm-header-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;}

/* ── BUTTONS ── */
.hbtn{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 20px;border-radius:var(--radius-sm);
    font-size:.82rem;font-weight:700;cursor:pointer;
    border:none;font-family:var(--font);white-space:nowrap;
    transition:all .15s;text-decoration:none;
}
.hbtn-amber{background:var(--amber);color:var(--navy);}
.hbtn-amber:hover{background:var(--amber2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(245,158,11,.4);color:var(--navy);}
.hbtn-outline{background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.18);}
.hbtn-outline:hover{background:rgba(255,255,255,.15);color:#fff;}

.ibtn{
    display:inline-flex;align-items:center;gap:5px;
    padding:6px 14px;border-radius:var(--radius-sm);
    font-size:.78rem;font-weight:700;cursor:pointer;
    border:none;font-family:var(--font);transition:all .14s;
    text-decoration:none;
}
.ibtn-teal{background:var(--teal);color:#fff;}
.ibtn-teal:hover{background:#0f766e;transform:translateY(-1px);color:#fff;}
.ibtn-outline{background:transparent;color:var(--text2);border:1.5px solid var(--border);}
.ibtn-outline:hover{border-color:var(--border2);background:var(--surface2);}
.ibtn-rose{background:var(--rose);color:#fff;}
.ibtn-rose:hover{background:#e11d48;transform:translateY(-1px);color:#fff;}
.ibtn-ghost{background:var(--surface2);color:var(--text2);border:1.5px solid var(--border);}
.ibtn-ghost:hover{background:var(--border);border-color:var(--border2);}

/* ── TABLE CARD ── */
.table-card{
    background:var(--surface);border-radius:var(--radius);
    border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;
}
.table-card-head{
    padding:16px 22px;border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
    background:var(--surface2);flex-wrap:wrap;gap:10px;
}
.table-card-head h3{font-size:.9rem;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px;margin:0;}
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
thead th{
    background:#f8fafc;padding:.75rem 1rem;text-align:left;
    font-size:.68rem;font-weight:700;text-transform:uppercase;
    letter-spacing:.6px;color:var(--text3);white-space:nowrap;
    border-bottom:1px solid var(--border);
}
tbody tr{border-bottom:1px solid var(--border);transition:.15s;}
tbody tr:last-child{border-bottom:none;}
tbody tr:hover{background:#f8fafc;}
tbody td{padding:.9rem 1rem;font-size:.82rem;color:var(--text);white-space:nowrap;}

/* ── BADGES ── */
.bdg{display:inline-flex;align-items:center;padding:.2rem .65rem;border-radius:99px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
.bdg-green{background:#d1fae5;color:#065f46;}
.bdg-amber{background:var(--amber-lt);color:var(--amber2);}
.bdg-rose{background:var(--rose-lt);color:#be123c;}
.bdg-teal{background:var(--teal-lt);color:var(--teal);}

/* Filename cell */
.filename-cell{display:flex;align-items:center;gap:8px;}
.filename-cell i{color:#217346;font-size:1rem;}
.filename-text{font-weight:700;font-size:.82rem;color:var(--text);}

/* Amount pill */
.amt{font-family:var(--mono);font-weight:700;}
.amt-positive{color:var(--teal);}

/* ── PAGINATION ── */
.pg-wrap{
    padding:14px 22px;display:flex;align-items:center;
    justify-content:space-between;border-top:1px solid var(--border);
    flex-wrap:wrap;gap:.5rem;background:var(--surface2);
}
.pg-info{font-size:.75rem;color:var(--text3);}
.pg-btns{display:flex;gap:.3rem;}
.pg-btn{
    width:34px;height:34px;display:flex;align-items:center;justify-content:center;
    border-radius:8px;border:1.5px solid var(--border);background:#fff;
    color:var(--text);font-size:.78rem;font-weight:700;cursor:pointer;
    transition:.15s;font-family:var(--font);
}
.pg-btn:hover:not(:disabled){border-color:var(--amber);color:var(--amber2);}
.pg-btn.active{background:var(--amber);border-color:var(--amber);color:var(--navy);}
.pg-btn:disabled{opacity:.4;cursor:not-allowed;}

/* ── PER-PAGE SELECT ── */
.per-page-select{
    font-size:.78rem;padding:.35rem .6rem;
    border:1.5px solid var(--border);border-radius:8px;
    font-family:var(--font);color:var(--text);background:var(--surface2);
}
.per-page-select:focus{outline:none;border-color:var(--amber);}

/* ── TOAST ── */
.toast-wrap{position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;}
.sfm-toast{
    display:flex;align-items:center;gap:.75rem;padding:.85rem 1.2rem;
    border-radius:10px;background:var(--navy);color:#fff;
    font-size:.82rem;font-weight:600;min-width:280px;
    box-shadow:var(--shadow-lg);animation:slideIn .3s ease;font-family:var(--font);
}
.sfm-toast.success{background:#064e3b;}
.sfm-toast.error{background:#7f1d1d;}
@keyframes slideIn{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}

/* ── EMPTY STATE ── */
.empty-state{padding:3rem;text-align:center;color:var(--text3);}
.empty-state i{font-size:2.5rem;margin-bottom:1rem;opacity:.3;display:block;}
.empty-state p{font-size:.85rem;}

/* ── SKELETON ── */
.skeleton{
    background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
    background-size:200% 100%;animation:shimmer 1.2s infinite;border-radius:6px;
}
@keyframes shimmer{to{background-position:-200% 0;}}

/* ── DELETE MODAL ── */
.modal-overlay{
    position:fixed;inset:0;background:rgba(15,23,42,.6);
    backdrop-filter:blur(4px);z-index:10050;
    display:none;align-items:center;justify-content:center;
}
.modal-overlay.show{display:flex;}
.modal-box{
    background:var(--surface);border-radius:20px;
    width:100%;max-width:440px;overflow:hidden;
    box-shadow:0 24px 64px rgba(0,0,0,.2);
    animation:slideUp .25s cubic-bezier(.22,1,.36,1);
}
@keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.modal-header{
    background:linear-gradient(135deg,var(--navy),var(--navy2));
    padding:20px 24px;display:flex;align-items:center;gap:12px;
}
.modal-header-icon{
    width:40px;height:40px;background:rgba(244,63,94,.2);
    border:1px solid rgba(244,63,94,.35);border-radius:10px;
    display:flex;align-items:center;justify-content:center;font-size:1.1rem;
}
.modal-header-text h3{font-size:.95rem;font-weight:800;color:#fff;margin:0 0 2px;}
.modal-header-text p{font-size:.72rem;color:rgba(255,255,255,.55);margin:0;}
.modal-body{padding:22px 24px;}
.modal-body p{font-size:.85rem;color:var(--text2);line-height:1.55;}
.modal-footer{padding:12px 24px 20px;display:flex;gap:8px;justify-content:flex-end;}

@media(max-width:768px){
    .sfm-wrap{padding:16px;}
}
</style>

<body style="overflow-x:hidden;">
<div class="page-loader"><div class="bar"></div></div>
@include('superadmin.superadminnav')
@include('superadmin.superadminheader')

<div class="pc-container"><div class="pc-content">
<div class="sfm-wrap">

{{-- ── PAGE HEADER ────────────────────────────────────────────────── --}}
<div class="sfm-header">
  <div class="sfm-header-inner">
    <div class="sfm-header-title">
      <div class="sfm-header-icon">📁</div>
      <div class="sfm-header-text">
        <h1>File Monitor</h1>
        <p>Manage uploaded settlement report files — download or remove records</p>
      </div>
    </div>
    <div class="sfm-header-actions">
      <button type="button" class="hbtn hbtn-outline" onclick="loadUploads()">
        <i class="bi bi-arrow-clockwise"></i> Refresh
      </button>
      <a href="{{ route('settlement.index') }}" class="hbtn hbtn-amber">
        <i class="bi bi-plus-lg"></i> New Upload
      </a>
    </div>
  </div>
</div>

{{-- ── UPLOADS TABLE ───────────────────────────────────────────────── --}}
<div class="table-card">
  <div class="table-card-head">
    <h3><i class="bi bi-folder2-open" style="color:var(--amber2)"></i> Uploaded Settlement Reports</h3>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
      <span style="font-size:.75rem;color:var(--text3);">Per page:</span>
      <select class="per-page-select" id="perPage">
        <option value="10" selected>10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Filename</th>
          <th>Uploaded by</th>
          <th>File Size</th>
          <th>Total Rows</th>
          <th>Accounts</th>
          <th>Transaction Amount (₹)</th>
          <th>Net Settlement (₹)</th>
          <th>Status</th>
          <th>Uploaded At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="uploadsTbody">
        <tr>
          <td colspan="11">
            <div class="skeleton" style="height:42px;margin:.5rem 1rem"></div>
            <div class="skeleton" style="height:42px;margin:.5rem 1rem"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="pg-wrap" id="paginationBar" style="display:none;">
    <div class="pg-info" id="pageInfo"></div>
    <div class="pg-btns" id="pageBtns"></div>
  </div>
</div>

</div>{{-- /.sfm-wrap --}}
</div></div>{{-- /.pc-content /.pc-container --}}

{{-- ── DELETE CONFIRMATION MODAL ───────────────────────────────────── --}}
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-header-icon"><i class="bi bi-trash3-fill" style="color:var(--rose)"></i></div>
      <div class="modal-header-text">
        <h3>Delete Upload</h3>
        <p>This action cannot be undone</p>
      </div>
    </div>
    <div class="modal-body">
      <p>Remove this upload from the dashboard? All linked <strong>account and transaction records</strong> will be deleted. The original Excel file in <code>storage/app/settlement_reports</code> will <strong>not</strong> be deleted.</p>
    </div>
    <div class="modal-footer">
      <button class="ibtn ibtn-ghost" onclick="closeDeleteModal()"><i class="bi bi-x-lg"></i> Cancel</button>
      <button class="ibtn ibtn-rose" id="confirmDeleteBtn"><i class="bi bi-trash3-fill"></i> Delete</button>
    </div>
  </div>
</div>

{{-- ── TOAST CONTAINER ─────────────────────────────────────────────── --}}
<div class="toast-wrap" id="toastWrap"></div>

<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const SETTLEMENT_UPLOAD_DESTROY_URL = @json(route('settlement.uploads.destroy', ['upload' => '__UPLOAD_ID__']));
let currentPage = 1;
let pendingDeleteId = null;

function escHtml(s) {
    if (s == null || s === '') return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ── Toast ──────────────────────────────────────────────────────────────
function toast(msg, type = 'default', duration = 4000) {
    const el = document.createElement('div');
    el.className = `sfm-toast ${type}`;
    const icon = type === 'success' ? 'bi-check-circle-fill' : type === 'error' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill';
    el.innerHTML = `<i class="bi ${icon}"></i> ${msg}`;
    document.getElementById('toastWrap').appendChild(el);
    setTimeout(() => el.remove(), duration);
}

// ── Load Uploads ───────────────────────────────────────────────────────
function loadUploads(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('uploadsTbody');
    tbody.innerHTML = `
        <tr><td colspan="11">
            <div class="skeleton" style="height:42px;margin:.5rem 1rem"></div>
            <div class="skeleton" style="height:42px;margin:.5rem 1rem"></div>
        </td></tr>`;

    const params = new URLSearchParams({ page, per_page: document.getElementById('perPage').value });

    fetch(`{{ route('settlement.api.uploads') }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { toast('Failed to load uploads.', 'error'); return; }

            if (data.data.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="11">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No files uploaded yet. <a href="{{ route('settlement.index') }}" style="color:var(--amber2);font-weight:700;">Upload one now →</a></p>
                        </div>
                    </td></tr>`;
                document.getElementById('paginationBar').style.display = 'none';
                return;
            }

            const statusBadge = { completed: 'bdg-green', processing: 'bdg-amber', failed: 'bdg-rose' };

            let offset = (data.meta.current_page - 1) * data.meta.per_page;
            tbody.innerHTML = data.data.map((u, i) => `
                <tr>
                    <td style="color:var(--text3);font-size:.75rem;font-family:var(--mono);">${offset + i + 1}</td>
                    <td>
                        <div class="filename-cell">
                            <i class="bi bi-file-earmark-excel-fill"></i>
                            <span class="filename-text">${escHtml(u.original_filename)}</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:.8rem;color:var(--text);">${u.uploaded_by_display && u.uploaded_by_display !== '—' ? escHtml(u.uploaded_by_display) : '<span style="color:var(--text3);font-weight:500;">—</span>'}</div>
                        ${u.uploaded_ip ? `<div style="font-size:.7rem;color:var(--text3);font-family:var(--mono);" title="${escHtml(u.upload_user_agent || '')}">IP ${escHtml(u.uploaded_ip)}</div>` : ''}
                    </td>
                    <td style="color:var(--text3);font-size:.78rem;font-family:var(--mono);">${escHtml(u.file_size)}</td>
                    <td style="font-family:var(--mono);font-size:.8rem;">${Number(u.total_rows).toLocaleString()}</td>
                    <td>
                        <span class="bdg bdg-teal">${Number(u.total_accounts).toLocaleString()}</span>
                        ${(u.duplicate_accounts_skipped || 0) > 0 ? `<div style="font-size:.68rem;color:var(--text3);margin-top:3px;">${Number(u.duplicate_accounts_skipped)} dup. skipped</div>` : ''}
                    </td>
                    <td class="amt" style="color:var(--text);">₹${u.total_transaction_amount}</td>
                    <td class="amt amt-positive">₹${u.total_net_settlement_amount}</td>
                    <td><span class="bdg ${statusBadge[u.status_badge] || 'bdg-teal'}">${u.status}</span></td>
                    <td style="color:var(--text3);font-size:.75rem;font-family:var(--mono);">${u.uploaded_at}</td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <a href="/settlement/download/${u.id}/xlsx" class="ibtn ibtn-teal" title="Download XLSX">
                                <i class="bi bi-file-earmark-excel-fill"></i> XLSX
                            </a>
                            <a href="/settlement/download/${u.id}/csv" class="ibtn ibtn-outline" title="Download CSV">
                                <i class="bi bi-filetype-csv"></i> CSV
                            </a>
                            <button class="ibtn ibtn-rose" onclick="openDeleteModal(${u.id})" title="Delete upload">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            renderPagination(data.meta);
        })
        .catch(() => toast('Error loading uploads.', 'error'));
}

// ── Pagination ─────────────────────────────────────────────────────────
function renderPagination(meta) {
    const bar = document.getElementById('paginationBar');
    bar.style.display = 'flex';
    document.getElementById('pageInfo').textContent = `Showing ${meta.from ?? 0}–${meta.to ?? 0} of ${meta.total} files`;

    const cp = meta.current_page, lp = meta.last_page;
    let html = '';
    html += `<button class="pg-btn" onclick="loadUploads(1)" ${cp===1?'disabled':''}><i class="bi bi-chevron-double-left"></i></button>`;
    html += `<button class="pg-btn" onclick="loadUploads(${cp-1})" ${cp===1?'disabled':''}><i class="bi bi-chevron-left"></i></button>`;

    let pages = lp <= 7 ? Array.from({length:lp},(_,i)=>i+1) : [1];
    if (lp > 7) {
        if (cp > 3) pages.push('…');
        for (let p = Math.max(2,cp-1); p <= Math.min(lp-1,cp+1); p++) pages.push(p);
        if (cp < lp-2) pages.push('…');
        pages.push(lp);
    }
    pages.forEach(p => {
        if (p === '…') html += `<span class="pg-btn" style="cursor:default">…</span>`;
        else html += `<button class="pg-btn ${p===cp?'active':''}" onclick="loadUploads(${p})">${p}</button>`;
    });
    html += `<button class="pg-btn" onclick="loadUploads(${cp+1})" ${cp===lp?'disabled':''}><i class="bi bi-chevron-right"></i></button>`;
    html += `<button class="pg-btn" onclick="loadUploads(${lp})" ${cp===lp?'disabled':''}><i class="bi bi-chevron-double-right"></i></button>`;
    document.getElementById('pageBtns').innerHTML = html;
}

// ── Delete Modal ───────────────────────────────────────────────────────
function openDeleteModal(id) {
    pendingDeleteId = id;
    document.getElementById('deleteModal').classList.add('show');
}
function closeDeleteModal() {
    pendingDeleteId = null;
    document.getElementById('deleteModal').classList.remove('show');
}

document.getElementById('deleteModal').addEventListener('click', e => {
    if (e.target === document.getElementById('deleteModal')) closeDeleteModal();
});

document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
    if (!pendingDeleteId) return;
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    fetch(SETTLEMENT_UPLOAD_DESTROY_URL.replace('__UPLOAD_ID__', String(pendingDeleteId)), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json().then(body => ({ ok: r.ok, body })))
    .then(({ ok, body: data }) => {
        btn.disabled = false;
        closeDeleteModal();
        if (ok && data.success) { toast(data.message || 'Upload data removed.', 'success'); loadUploads(currentPage); }
        else toast(data.message || 'Delete failed.', 'error');
    })
    .catch(() => {
        btn.disabled = false;
        toast('Error deleting upload.', 'error');
    });
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDeleteModal();
});

document.getElementById('perPage').addEventListener('change', () => loadUploads(1));

// ── Init ───────────────────────────────────────────────────────────────
loadUploads();
</script>

@include('superadmin.superadminfooter')
</body>
</html>
