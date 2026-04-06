@if ($tab === 'pending')
    <div class="adv-pending-shell">
        <table class="adv-pending-table" role="grid" aria-label="Pending advances">
            <thead>
                <tr>
                    <th scope="col" class="adv-pth adv-pth-check"><span class="adv-pth-sr">Select row</span></th>
                    <th scope="col" class="adv-pth adv-pth-amount">Advance amount</th>
                    <th scope="col" class="adv-pth adv-pth-date">Date</th>
                    <th scope="col" class="adv-pth adv-pth-ref">Reference #</th>
                    <th scope="col" class="adv-pth adv-pth-status text-center">Status</th>
                    <th scope="col" class="adv-pth adv-pth-details">Details</th>
                    <th scope="col" class="adv-pth adv-pth-balance text-end">Balance</th>
                    <th scope="col" class="adv-pth adv-pth-actions text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $item)
                    @php
                        $currency = ($item->currency && $item->currency !== 'INR') ? $item->currency . '.' : 'Rs.';
                        $amount   = $currency . number_format($item->advance_amount ?? 0, 2);
                        $date     = $item->advance_date ? \Carbon\Carbon::parse($item->advance_date)->format('d/m/Y') : '-';
                        $ref      = htmlspecialchars((string)($item->reference_no ?? ''), ENT_QUOTES, 'UTF-8');
                        $balanceAmt = (float) ($item->balance_amount ?? $item->advance_amount ?? 0);
                        $usedAmt    = (float) ($item->used_amount ?? 0);
                        $balance    = $currency . number_format($balanceAmt, 2);
                        $hasReport  = !empty($item->report_id);
                        $rptLine    = trim((string) ($item->report_name ?? '')) !== '' ? $item->report_name : ($item->report_code ?? '');
                    @endphp
                    <tr class="adv-pending-card" data-id="{{ $item->id }}">
                        <td class="adv-ptd adv-ptd-check">
                            <div class="adv-pc-check"><input type="checkbox" class="adv-check" data-id="{{ $item->id }}"></div>
                        </td>
                        <td class="adv-ptd adv-ptd-amount">
                            <div class="adv-pc-amount-val">{{ $amount }}</div>
                            @if ($usedAmt > 0)
                                <div class="adv-pc-used">Used: {{ $currency }}{{ number_format($usedAmt, 2) }}</div>
                            @endif
                        </td>
                        <td class="adv-ptd adv-ptd-date">
                            <button type="button" class="adv-pc-date-link">{{ $date }}</button>
                        </td>
                        <td class="adv-ptd adv-ptd-ref">
                            @if ($ref !== '')
                                <span class="adv-pc-ref-cell">{{ $ref }}</span>
                            @else
                                <span class="adv-pc-ref-empty">—</span>
                            @endif
                        </td>
                        <td class="adv-ptd adv-ptd-status text-center">
                            <div class="adv-pc-status-pill adv-pc-status-pill--cell" role="status">
                                <i class="bi bi-hourglass-split" aria-hidden="true"></i> Pending
                            </div>
                        </td>
                        <td class="adv-ptd adv-ptd-details">
                            <div class="adv-pc-info-stack adv-pc-info-stack--details">
                                @if ($hasReport && $rptLine !== '')
                                    <div class="adv-pc-report-line">
                                        <span class="adv-pc-report-k">Report</span>
                                        <strong class="adv-pc-report-val">{{ htmlspecialchars($rptLine, ENT_QUOTES, 'UTF-8') }}</strong>
                                    </div>
                                @endif
                                @if (!empty($item->company_name) || !empty($item->zone_name) || !empty($item->branch_names_display))
                                <div class="adv-pc-location-block">
                                    @if (!empty($item->company_name) || !empty($item->zone_name))
                                    <div class="adv-pc-company-line">
                                        {{ htmlspecialchars($item->company_name ?? '', ENT_QUOTES, 'UTF-8') }}
                                        @if (!empty($item->zone_name))
                                            <span class="adv-pc-zone-sep"></span><span class="adv-pc-zone-name">{{ htmlspecialchars($item->zone_name, ENT_QUOTES, 'UTF-8') }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    @if (!empty($item->branch_names_display))
                                        <div class="adv-pc-branches" title="{{ htmlspecialchars($item->branch_names_display, ENT_QUOTES, 'UTF-8') }}">
                                            <span class="adv-pc-branches-k">Branches</span>
                                            <span class="adv-pc-branches-text">{{ htmlspecialchars($item->branch_names_display, ENT_QUOTES, 'UTF-8') }}</span>
                                        </div>
                                    @endif
                                </div>
                                @endif
                                @if (!($hasReport && $rptLine !== '') && empty($item->company_name) && empty($item->zone_name) && empty($item->branch_names_display))
                                    <span class="adv-pc-details-empty">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="adv-ptd adv-ptd-balance text-end">
                            <div class="adv-pc-trip-val adv-pc-trip-val--emphasis" style="color:{{ $balanceAmt > 0 ? '#15803d' : '#dc2626' }};">{{ $balance }}</div>
                        </td>
                        <td class="adv-ptd adv-ptd-actions text-end">
                            <div class="adv-pc-action-col adv-pc-action-col--pending">
                                <div class="adv-apply-inline-wrap adv-pc-apply-wrap">
                                    <button type="button" class="adv-apply-btn adv-inline-apply-btn" data-advance-id="{{ $item->id }}" title="Link to petty cash report">
                                        Apply to Report <i class="bi bi-chevron-down adv-apply-chevron"></i>
                                    </button>
                                    <div class="adv-apply-inline-dropdown" aria-hidden="true"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="adv-pending-empty-row">
                        <td colspan="8">
                            <div class="adv-empty-state"><i class="bi bi-clock-history"></i><p>No pending advances found.</p></div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <div class="qdt-wrap">
        <table class="qdt-table">
            <thead class="qdt-head">
                <tr>
                    <th style="width:32px;"><input type="checkbox" id="select-all-adv" style="accent-color:#4f46e5;width:15px;height:15px;"></th>
                    <th>DATE</th>
                    <th>REFERENCE#</th>
                    <th class="text-end">AMOUNT</th>
                    <th class="text-end">BALANCE</th>
                    <th>REPORT NAME</th>
                    <th>STATUS</th>
                    <th class="text-center">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $item)
                    @php
                        $status   = strtolower($item->status ?? '');
                        switch ($status) {
                            case 'pending': $badge = '<span class="qd-badge qd-badge-pending"><i class="bi bi-hourglass-split" style="font-size:10px;margin-right:3px;"></i>Pending Approval</span>'; break;
                            case 'applied': $badge = '<span class="qd-badge qd-badge-approved"><i class="bi bi-cash-stack" style="font-size:10px;margin-right:3px;"></i>Approved / Paid</span>'; break;
                            case 'closed':  $badge = '<span class="qd-badge" style="background:#e0e7ff;color:#4f46e5;"><i class="bi bi-patch-check-fill" style="font-size:10px;margin-right:3px;"></i>Settled</span>'; break;
                            case 'draft':   $badge = '<span class="qd-badge qd-badge-draft"><i class="bi bi-pencil" style="font-size:10px;margin-right:3px;"></i>Draft</span>'; break;
                            case 'rejected':$badge = '<span class="qd-badge" style="background:#fee2e2;color:#dc2626;"><i class="bi bi-x-circle" style="font-size:10px;margin-right:3px;"></i>Rejected</span>'; break;
                            default:        $badge = '<span class="qd-badge" style="background:#eee;color:#555;">'.ucfirst($status).'</span>'; break;
                        }
                        $editUrl    = route('superadmin.getadvancescreate') . '?id=' . $item->id;
                        $currency   = ($item->currency && $item->currency !== 'INR') ? $item->currency . '.' : 'Rs.';
                        $amount     = $currency . number_format($item->advance_amount ?? 0, 2);
                        $usedAmt    = (float) ($item->used_amount ?? 0);
                        $balanceAmt = (float) ($item->balance_amount ?? $item->advance_amount ?? 0);
                        $balance    = $currency . number_format($balanceAmt, 2);
                        $date       = $item->advance_date ? \Carbon\Carbon::parse($item->advance_date)->format('d/m/Y') : '-';
                        $ref        = htmlspecialchars((string)($item->reference_no ?? '-'), ENT_QUOTES, 'UTF-8');
                        $balanceColor = $balanceAmt > 0 ? '#16a34a' : '#dc2626';
                    @endphp
                    <tr class="qdt-row advance-row" data-id="{{ $item->id }}" data-status="{{ $item->status }}">
                        <td style="width:32px;"><input type="checkbox" class="adv-check" data-id="{{ $item->id }}" style="accent-color:#4f46e5;width:15px;height:15px;"></td>
                        <td><button type="button" class="adv-date-link">{{ $date }}</button></td>
                        <td>{{ $ref }}</td>
                        <td class="qdt-amount text-end" style="font-weight:600;">{{ $amount }}</td>
                        <td class="text-end">
                            <span style="font-weight:700;color:{{ $balanceColor }};">{{ $balance }}</span>
                            @if ($usedAmt > 0)
                                <div style="font-size:11px;color:#6b7280;">Used: {{ $currency }}{{ number_format($usedAmt,2) }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($item->report_name)
                                <div class="adv-report-name">{{ htmlspecialchars($item->report_name, ENT_QUOTES, 'UTF-8') }}</div>
                                @if ($item->report_code)
                                    <div class="adv-report-code">{{ htmlspecialchars($item->report_code, ENT_QUOTES, 'UTF-8') }}</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{!! $badge !!}</td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            @if ($status === 'pending')
                                @php $advHasReport = (int) ($item->report_id ?? 0) !== 0; @endphp
                                @if (!$advHasReport)
                                <div class="adv-apply-inline-wrap adv-table-apply-wrap" style="position:relative;display:inline-block;vertical-align:middle;">
                                    <button type="button" class="adv-apply-btn adv-inline-apply-btn adv-table-apply-btn" data-advance-id="{{ $item->id }}" title="Apply to report">
                                        Apply to Report <i class="bi bi-chevron-down adv-apply-chevron"></i>
                                    </button>
                                    <div class="adv-apply-inline-dropdown" aria-hidden="true"></div>
                                </div>
                                @endif
                                <button class="btn btn-sm btn-success adv-approve-btn" data-id="{{ $item->id }}" title="Approve &amp; Disburse"><i class="bi bi-check-lg"></i></button>
                                <button class="btn btn-sm btn-outline-danger adv-reject-btn" data-id="{{ $item->id }}" title="Reject"><i class="bi bi-x-lg"></i></button>
                            @elseif ($status === 'applied')
                                <button class="btn btn-sm btn-outline-secondary adv-close-btn" data-id="{{ $item->id }}" title="Settle &amp; Close Advance"><i class="bi bi-patch-check"></i></button>
                            @elseif ($status === 'draft')
                                <button class="btn btn-sm btn-outline-warning adv-submit-btn" data-id="{{ $item->id }}" title="Submit for Approval"><i class="bi bi-send"></i></button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding:30px;color:#aaa;">No advance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
