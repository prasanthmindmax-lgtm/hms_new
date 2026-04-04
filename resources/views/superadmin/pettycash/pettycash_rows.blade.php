<div class="qdt-wrap">
    <table class="qdt-table">
        <thead class="qdt-head">
            <tr>
                <th class="qdt-th-check"><input type="checkbox" id="selectAll" /></th>
                <th>DATE</th>
                <th>LOCATION</th>
                <th>REPORT ID</th>
                <th>REPORT NAME</th>
                <th>CATEGORY</th>
                <th class="text-end">AMOUNT</th>
                <th>STATUS</th>
                <th class="text-center">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pettycashlist as $item)
                @php
                    $status = strtolower($item->status ?? 'unknown');
                    $isPending = $status === 'pending';
                    $isApproved = $status === 'approved';
                    $isRejected = in_array($status, ['rejected', 'reject']);
                    $isDraft = $status === 'draft';
                    $isReimbursed = $status === 'reimbursed';
                    $editUrl = route('superadmin.getpettycashcreate') . '?id=' . $item->id;
                    $branchLine = $item->branch_names_display ?? ($item->branch->name ?? '');
                    $erPk = $item->report_id ? (int) $item->report_id : null;
                    $rptOpenUrl = $erPk
                        ? route('superadmin.getpettycashreports') . '?open_er_id=' . $erPk
                        : null;
                @endphp
                <tr class="qdt-row customer-row" data-id="{{ $item->id }}"
                    data-report-code="{{ optional($item->report)->report_id ?? '' }}"
                    data-report-name="{{ optional($item->report)->report_name ?? '' }}"
                    data-expense-date="{{ $item->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d M Y') : '-' }}"
                    data-vendor-name="{{ optional($item->vendor)->display_name ?? ($item->vendor_name ?? '-') }}"
                    data-total-amount="{{ $item->total_amount ?? 0 }}" data-status="{{ $item->status ?? '' }}"
                    data-notes="{{ $item->notes ?? '' }}" data-zone-name="{{ $item->zone->name ?? '' }}"
                    data-company-name="{{ $item->company->company_name ?? '' }}" data-branch-name="{{ $branchLine }}"
                    data-approval_status="{{ $isApproved ? 1 : 0 }}"
                    data-pc-readonly="{{ $isApproved || $isReimbursed ? 1 : 0 }}"
                    data-pettycash='@json($item)'>
                    <td class="qdt-td-check"><input type="checkbox" class="pettycash-checkbox"></td>
                    <td class="qdt-date-cell">
                        <span class="qdt-date-main">
                            {{ $item->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d M Y') : '-' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <span style="font-size:12px;color:#555;">{{ $item->zone->name ?? '' }}</span>
                            <span style="font-size:12px;color:#555;">{{ $item->company->company_name ?? '' }}</span>
                            <span
                                style="font-size:12px;color:#555;line-height:1.4;display:block;max-width:320px;word-break: break-word;overflow-wrap: break-word;">
                                {{ $branchLine }}</span>
                        </div>
                    </td>
                    <td>
                        @if ($rptOpenUrl)
                            <span class="d-inline-flex align-items-center flex-wrap gap-1">
                                <span>{{ $item->report->report_id ?? '-' }}</span>
                                <a href="{{ $rptOpenUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="pc-rpt-ext pc-row-action" title="Open this report in a new tab"
                                    aria-label="Open this report in a new tab" onclick="event.stopPropagation();"><i
                                        class="bi bi-box-arrow-up-right"></i></a>
                            </span>
                        @else
                            {{ $item->report->report_id ?? '-' }}
                        @endif
                    </td>
                    <td>
                        @if ($rptOpenUrl)
                            <a href="{{ $rptOpenUrl }}" target="_blank" rel="noopener noreferrer"
                                class="pc-report-open-link pc-row-action" title="Open this report in a new tab"
                                onclick="event.stopPropagation();">
                                {{ $item->report->report_name ?: 'View report' }}
                                <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.72rem;"
                                    aria-hidden="true"></i>
                            </a>
                        @else
                            {{ $item->report->report_name ?? '' }}
                        @endif
                    </td>
                    <td>
                        @if (strtolower((string) ($item->expense_type ?? 'single')) === 'itemized')
                            Itemized
                        @else
                            {{ $item->category->name ?? '' }}
                        @endif
                    </td>
                    <td class="qdt-amount text-end">₹{{ number_format($item->total_amount ?? 0, 2) }}</td>

                    {{-- Status badge (Zoho-style pills, same qd-badge system as rest of app) --}}
                    <td>
                        @if ($isPending)
                            @php
                                $pendingDays = $item->created_at
                                    ? \Carbon\Carbon::parse($item->created_at)->diffInDays(now())
                                    : 0;
                                $dayColor = $pendingDays > 7 ? '#dc3545' : ($pendingDays > 3 ? '#fd7e14' : '#6c757d');
                            @endphp
                            <div style="display:flex;flex-direction:column;align-items:flex-start;gap:4px;">
                                <span class="qd-badge qd-badge-pc qd-badge-pc-pending">Pending</span>
                                <span style="font-size:10px;color:{{ $dayColor }};font-weight:600;">
                                    {{ $pendingDays }}d pending
                                </span>
                            </div>
                        @elseif ($isApproved)
                            <span class="qd-badge qd-badge-pc qd-badge-pc-approved">Approved</span>
                        @elseif ($isRejected)
                            <span class="qd-badge qd-badge-pc qd-badge-pc-rejected">Rejected</span>
                        @elseif ($isDraft)
                            <span class="qd-badge qd-badge-pc qd-badge-pc-draft">Draft</span>
                        @elseif ($isReimbursed)
                            <span class="qd-badge qd-badge-pc qd-badge-reimbursed">Reimbursed</span>
                        @else
                            <span class="qd-badge qd-badge-pc qd-badge-default">{{ strtoupper($status) }}</span>
                        @endif
                    </td>

                    {{-- Actions: Approved = read-only (no row actions). Others: approve/reject and/or edit as appropriate. --}}
                    <td class="text-center qdt-action-cell">
                        @if ($isApproved)
                            <span class="pc-action-readonly" title="Approved — view only">—</span>
                        @elseif ($isPending)
                            <button type="button" class="qd-action-btn qd-action-approve approver pc-row-action"
                                data-value="approved" data-id="{{ $item->id }}" title="Approve">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button" class="qd-action-btn qd-action-reject approver pc-row-action"
                                data-value="rejected" data-id="{{ $item->id }}" title="Reject">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <a href="{{ $editUrl }}" class="qd-action-btn pc-row-action pc-row-edit"
                                title="Edit" onclick="event.stopPropagation();"><i class="bi bi-pencil"></i></a>
                        @elseif ($isRejected)
                            <a href="{{ $editUrl }}" class="qd-action-btn pc-row-action pc-row-edit"
                                title="Edit" onclick="event.stopPropagation();"><i class="bi bi-pencil"></i></a>
                        @elseif ($isDraft)
                            <a href="{{ $editUrl }}" class="qd-action-btn pc-row-action pc-row-edit"
                                title="Edit" onclick="event.stopPropagation();"><i class="bi bi-pencil"></i></a>
                        @elseif ($isReimbursed)
                            <span class="pc-action-readonly" title="Reimbursed">—</span>
                        @else
                            <a href="{{ $editUrl }}" class="qd-action-btn pc-row-action pc-row-edit"
                                title="Edit" onclick="event.stopPropagation();"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding:24px;color:#aaa;">
                        No petty cash entries found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if (method_exists($pettycashlist, 'links') && $pettycashlist->total() > 10)
    <div class="qd-pagination">
        <div>{{ $pettycashlist->links('pagination::bootstrap-4') }}</div>
        <div>
            <form method="GET" id="perPageForm" class="d-flex align-items-center gap-2">
                <select name="per_page" id="per_page" class="form-control form-control-sm" style="width:80px;">
                    @foreach ([10, 25, 50, 100, 250, 500] as $size)
                        <option value="{{ $size }}" {{ (int) ($perPage ?? 10) === $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
                <span style="font-size:12px;color:#8a94a6;">entries</span>
            </form>
        </div>
    </div>
@endif
