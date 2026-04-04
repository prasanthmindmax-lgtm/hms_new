@if ($view === 'lines')
    <div class="qdt-wrap">
        <table class="qdt-table">
            <thead class="qdt-head">
                <tr>
                    <th>DATE</th>
                    <th>LOCATION</th>
                    <th>REPORT ID</th>
                    <th>REPORT NAME</th>
                    <th>VENDOR</th>
                    <th>CATEGORY</th>
                    <th class="text-end">AMOUNT</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $item)
                    @php
                        $statusBadge = match ($item->status) {
                            'approved' => '<span class="qd-badge qd-badge-approved">Approved</span>',
                            'pending'  => '<span class="qd-badge qd-badge-pending">Pending</span>',
                            'rejected' => '<span class="qd-badge qd-badge-rejected">Rejected</span>',
                            'draft'    => '<span class="qd-badge qd-badge-draft">Draft</span>',
                            default    => '<span class="qd-badge" style="background:#eee;color:#555;">' . ucfirst((string) ($item->status ?: '-')) . '</span>',
                        };

                        $repCode = $item->expense_report_code ?? '';
                        $repName = $item->expense_report_name ?? '';
                    @endphp
                    <tr class="qdt-row">
                        <td class="qdt-date-cell">
                            <span class="qdt-date-main">{{ $item->expense_date ? \Carbon\Carbon::parse($item->expense_date)->format('d M Y') : '-' }}</span>
                        </td>
                        <td>
                            <div style="display:flex;flex-direction:column;gap:2px;">
                                <span style="font-size:12px;color:#555;">{{ $item->zone_name ?? '-' }}</span>
                                <span style="font-size:12px;color:#555;">{{ $item->company_name ?? '-' }}</span>
                                <span style="font-size:12px;color:#555;">{{ $item->branch_name ?? '-' }}</span>
                            </div>
                        </td>
                        <td>{{ $repCode }}</td>
                        <td>{{ $repName }}</td>
                        <td>{{ $item->vendor_name ?? '-' }}</td>
                        <td>{{ $item->category_name ?? '-' }}</td>
                        <td class="qdt-amount text-end">₹{{ number_format($item->total_amount ?? 0, 2) }}</td>
                        <td>{!! $statusBadge !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding:24px;color:#aaa;">No petty cash report records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@elseif ($view === 'summary')
    @php
        $rptPersonInitials = static function (?string $name): string {
            $name = trim((string) $name);
            if ($name === '') {
                return '?';
            }
            $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
            if ($parts === false || $parts === []) {
                return '?';
            }
            if (count($parts) >= 2) {
                return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
            }

            return mb_strtoupper(mb_substr($name, 0, min(2, mb_strlen($name))));
        };
        $rptAvatarStyle = static function (?string $seed): string {
            $h = abs(crc32((string) $seed)) % 360;

            return 'background:hsl(' . $h . ', 52%, 46%);';
        };
    @endphp
    <div class="qdt-wrap">
        <table class="qdt-table">
            <thead class="qdt-head">
                <tr>
                    <th>SUBMITTER</th>
                    <th>REPORT#</th>
                    <th>REPORT NAME</th>
                    <th>STATUS</th>
                    <th>APPROVER</th>
                    <th class="text-end">TOTAL</th>
                    <th class="text-end">TO BE REIMBURSED</th>
                    <th class="text-center">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $item)
                    @php
                        $wf = $item->workflow_status ?? ($item->rollup_status ?? '');
                        $statusBadge = match ($wf) {
                            'approved' => '<span class="qd-badge qd-badge-approved">APPROVED</span>',
                            'pending_approval' => '<span class="qd-badge qd-badge-pending">PENDING APPROVAL</span>',
                            'pending' => '<span class="qd-badge qd-badge-pending">PENDING</span>',
                            'rejected' => '<span class="qd-badge qd-badge-rejected">REJECTED</span>',
                            'draft' => '<span class="qd-badge qd-badge-draft">DRAFT</span>',
                            'reimbursed' => '<span class="qd-badge" style="background:#047857;color:#fff;">REIMBURSED</span>',
                            default => '<span class="qd-badge" style="background:#eee;color:#555;">' . strtoupper((string) $wf) . '</span>',
                        };
                        $periodSlash = ($item->start_date && $item->end_date)
                            ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($item->end_date)->format('d/m/Y')
                            : '';
                        $link = route('superadmin.getpettycash') . '?expense_report_id=' . (int) $item->er_id;
                        $toReimb = isset($item->to_reimburse_amount) ? (float) $item->to_reimburse_amount : 0;
                        $subName = $item->submitter_name ?? '';
                        $subInitials = $rptPersonInitials($subName);
                        $subStyle = $rptAvatarStyle($subName !== '' ? $subName : 'sub');
                        $appName = trim((string) ($item->approver_name ?? ''));
                        $appInitials = $rptPersonInitials($appName !== '' ? $appName : null);
                        $appStyle = $rptAvatarStyle($appName !== '' ? $appName : 'app');
                        $subOn = '';
                        if (!empty($item->submitted_at)) {
                            try {
                                $subOn = \Carbon\Carbon::parse($item->submitted_at)->format('d/m/Y');
                            } catch (\Throwable $e) {
                                $subOn = '';
                            }
                        }
                        $dueHint = '';
                        if ($wf === 'pending_approval' && !empty($item->submitted_at)) {
                            try {
                                $dueDate = \Carbon\Carbon::parse($item->submitted_at)->addDays(14)->startOfDay();
                                $today = now()->startOfDay();
                                if ($today->gt($dueDate)) {
                                    $dueHint = 'Overdue';
                                } else {
                                    $days = (int) $today->diffInDays($dueDate);
                                    $dueHint = $days === 0 ? 'Due today' : 'Due in ' . $days . ' day' . ($days === 1 ? '' : 's');
                                }
                            } catch (\Throwable $e) {
                                $dueHint = '';
                            }
                        }
                    @endphp
                    <tr class="qdt-row report-summary-row" style="cursor:pointer;" data-er-id="{{ (int) $item->er_id }}">
                        <td>
                            <div class="rpt-person-cell">
                                <span class="rpt-person-avatar" style="{{ $subStyle }}">{{ $subInitials }}</span>
                                <div class="rpt-person-text">
                                    <span class="rpt-person-name">{{ $subName !== '' ? $subName : '—' }}</span>
                                    @if ($subOn !== '')
                                        <span class="rpt-person-sub">on : {{ $subOn }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $item->expense_report_code ?? '' }}</td>
                        <td>
                            <div class="rpt-report-title">{{ $item->report_name ?? '' }}</div>
                            @if ($periodSlash !== '')
                                <div class="rpt-report-period-sub">{{ $periodSlash }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="rpt-status-stack">
                                {!! $statusBadge !!}
                                @if ($dueHint !== '')
                                    <span class="rpt-due-hint">{{ $dueHint }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if ($appName !== '')
                                <div class="rpt-person-cell">
                                    <span class="rpt-person-avatar" style="{{ $appStyle }}">{{ $appInitials }}</span>
                                    <div class="rpt-person-text">
                                        <span class="rpt-person-name">{{ $appName }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="qdt-amount text-end">Rs.{{ number_format($item->total_amount ?? 0, 2) }}</td>
                        <td class="qdt-amount text-end">Rs.{{ number_format($toReimb, 2) }}</td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-outline-primary view-report-link" href="{{ $link }}" onclick="event.stopPropagation()">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding:24px;color:#aaa;">No expense reports found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
