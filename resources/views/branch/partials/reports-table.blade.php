<div class="card shadow-sm">
    <!-- Table Header with Per Page Selector -->
    <div class="card-header table-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>Financial Report Data
            </h5>
            <div class="d-flex align-items-center gap-2">
                <label class="text-white mb-0 me-2" style="font-size: 0.875rem;">Show:</label>
                <select id="perPageSelect" class="form-select form-select-sm" style="width: auto; min-width: 80px;">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                </select>
                <span class="text-white" style="font-size: 0.875rem;">entries</span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>Date</th>
                        <th>Zone</th>
                        <th>Branch</th>
                        <th class="text-end">Radiant</th>
                        <th class="text-end">Card</th>
                        <th class="text-end">UPI</th>
                        <th class="text-end">Deposit</th>
                        <th class="text-end">Bank</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Cancel</th>
                        <th class="text-end">Refund</th>
                        <th class="col-amount">Cash In</br> Hand</th>
                        <th>Created By</th>
                        <th class="text-center">Amount Handled </br> By</th>
                        <th class="text-center">Auditor Approval</th>
                        <th class="text-center">Management Approval</th>
                        <th class="text-center">Files</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $report)
                    <tr id="report-row-{{ $report->id }}">
                        <td class="text-center">{{ $reports->firstItem() + $index }}</td>
                        <td>
                            @php
                                $reportDate = $report->report_date;
                                if (is_string($reportDate)) {
                                    $reportDate = \Carbon\Carbon::parse($reportDate);
                                }
                            @endphp
                            <strong>{{ $reportDate ? $reportDate->format('d M Y') : 'N/A' }}</strong>
                            
                            {{-- Show Radiant Collection Date Range if available --}}
                            @if($report->radiant_collection_from_date && $report->radiant_collection_to_date)
                                @php
                                    $fromDate = $report->radiant_collection_from_date;
                                    $toDate = $report->radiant_collection_to_date;
                                    if (is_string($fromDate)) {
                                        $fromDate = \Carbon\Carbon::parse($fromDate);
                                    }
                                    if (is_string($toDate)) {
                                        $toDate = \Carbon\Carbon::parse($toDate);
                                    }
                                @endphp
                                <br><small class="text-muted">RC: {{ $fromDate->format('d M') }} - {{ $toDate->format('d M Y') }}</small>
                            @elseif($report->radiant_collected_date)
                                @php
                                    $radiantDate = $report->radiant_collected_date;
                                    if (is_string($radiantDate)) {
                                        $radiantDate = \Carbon\Carbon::parse($radiantDate);
                                    }
                                @endphp
                                <br><small class="text-muted">RC: {{ $radiantDate->format('d M Y') }}</small>
                            @endif
                            
                            {{-- Show Deposit Date if available --}}
                            @if($report->deposit_date)
                                @php
                                    $depositDate = $report->deposit_date;
                                    if (is_string($depositDate)) {
                                        $depositDate = \Carbon\Carbon::parse($depositDate);
                                    }
                                @endphp
                                <br><small class="text-info">Dep: {{ $depositDate->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $report->zone_name ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $report->branch_name ?? 'N/A' }}</td>
                        
                        {{-- Radiant Collection Amount with "Not Collected" indicator --}}
                        <td class="text-end">
                            @if($report->radiant_not_collected)
                                <span class="badge bg-warning text-dark" title="{{ $report->radiant_not_collected_remarks ?? 'Not collected' }}">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Not Collected
                                </span>
                            @else
                                <span class="text-success fw-bold">₹{{ number_format($report->radiant_collection_amount ?? 0, 2) }}</span>
                            @endif
                        </td>
                        
                        <td class="text-end text-primary fw-bold">₹{{ number_format($report->actual_card_amount ?? 0, 2) }}</td>
                        <td class="text-end text-info fw-bold">₹{{ number_format($report->upi_amount ?? 0, 2) }}</td>
                        <td class="text-end text-teal fw-bold">₹{{ number_format($report->deposit_amount ?? 0, 2) }}</td>
                        <td class="text-end text-warning fw-bold">₹{{ number_format($report->bank_deposit_amount ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($report->today_discount_amount ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($report->cancel_bill_amount ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($report->refund_bill_amount ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($report->cash_in_drawer ?? 0, 2) }}</td>
                        <td class="text-center">
                            <small>{{ optional($report->creator)->user_fullname ?? 'N/A' }}</small>
                        </td>

                        {{-- Personnel / Acknowledgment Column - clickable --}}
                        <td class="text-center">
                            @php
                                $hasPersonnel = $report->placed_by_whom || $report->locker_by_whom || $report->who_gave_radiant_cash;
                            @endphp
                            @if($hasPersonnel)
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="View Personnel Details"
                                    onclick="viewAcknowledgment({{ $report->id }},
                                        {{ json_encode($report->placed_by_whom) }},
                                        {{ json_encode(optional($report->placedBy)->username) }},
                                        {{ json_encode(optional($report->placedBy)->user_fullname) }},
                                        {{ json_encode($report->locker_by_whom) }},
                                        {{ json_encode(optional($report->lockerBy)->username) }},
                                        {{ json_encode(optional($report->lockerBy)->user_fullname) }},
                                        {{ json_encode($report->who_gave_radiant_cash) }},
                                        {{ json_encode(optional($report->radiantGivenBy)->username) }},
                                        {{ json_encode(optional($report->radiantGivenBy)->user_fullname) }}
                                    )"
                                >
                                    <i class="fas fa-users me-1"></i>
                                    <span class="badge bg-primary rounded-pill" style="font-size:0.7rem;">
                                        {{ ($report->placed_by_whom ? 1 : 0) + ($report->locker_by_whom ? 1 : 0) + ($report->who_gave_radiant_cash ? 1 : 0) }}
                                    </span>
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <!-- Auditor Approval Status -->
                        <td class="text-center auditor-approval-cell">
                            @if($report->auditor_approval_status == 0)
                                <span class="approval-badge approval-pending">
                                    <i class="fas fa-clock me-1"></i>Pending
                                </span>
                            @elseif($report->auditor_approval_status == 1)
                                <span class="approval-badge approval-approved">
                                    <i class="fas fa-check-circle me-1"></i>Approved
                                </span>
                                @if($report->auditorApprovedBy)
                                    <br><small class="text-muted">by {{ $report->auditorApprovedBy->user_fullname }}</small>
                                    @if($report->auditor_approved_at)
                                        @php
                                            $auditorDate = $report->auditor_approved_at;
                                            if (is_string($auditorDate)) {
                                                $auditorDate = \Carbon\Carbon::parse($auditorDate);
                                            }
                                        @endphp
                                        <br><small class="text-muted">{{ $auditorDate->format('d M Y h:i A') }}</small>
                                    @endif
                                @endif
                            @elseif($report->auditor_approval_status == 2)
                                <span class="approval-badge approval-rejected">
                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                </span>
                                @if($report->auditorApprovedBy)
                                    <br><small class="text-muted">by {{ $report->auditorApprovedBy->user_fullname }}</small>
                                    @if($report->auditor_approved_at)
                                        @php
                                            $auditorDate = $report->auditor_approved_at;
                                            if (is_string($auditorDate)) {
                                                $auditorDate = \Carbon\Carbon::parse($auditorDate);
                                            }
                                        @endphp
                                        <br><small class="text-muted">{{ $auditorDate->format('d M Y h:i A') }}</small>
                                    @endif
                                @endif
                            @endif
                        </td>
                        
                        <!-- Management Approval Status -->
                        <td class="text-center management-approval-cell">
                            @if($report->auditor_approval_status != 1)
                                <span class="text-muted">
                                    <i class="fas fa-minus-circle me-1"></i>Awaiting Auditor
                                </span>
                            @elseif($report->management_approval_status == 0)
                                <span class="approval-badge approval-pending">
                                    <i class="fas fa-clock me-1"></i>Pending
                                </span>
                            @elseif($report->management_approval_status == 1)
                                <span class="approval-badge approval-approved">
                                    <i class="fas fa-check-circle me-1"></i>Approved
                                </span>
                                @if($report->managementApprovedBy)
                                    <br><small class="text-muted">by {{ $report->managementApprovedBy->user_fullname }}</small>
                                    @if($report->management_approved_at)
                                        @php
                                            $managementDate = $report->management_approved_at;
                                            if (is_string($managementDate)) {
                                                $managementDate = \Carbon\Carbon::parse($managementDate);
                                            }
                                        @endphp
                                        <br><small class="text-muted">{{ $managementDate->format('d M Y h:i A') }}</small>
                                    @endif
                                @endif
                            @elseif($report->management_approval_status == 2)
                                <span class="approval-badge approval-rejected">
                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                </span>
                                @if($report->managementApprovedBy)
                                    <br><small class="text-muted">by {{ $report->managementApprovedBy->user_fullname }}</small>
                                    @if($report->management_approved_at)
                                        @php
                                            $managementDate = $report->management_approved_at;
                                            if (is_string($managementDate)) {
                                                $managementDate = \Carbon\Carbon::parse($managementDate);
                                            }
                                        @endphp
                                        <br><small class="text-muted">{{ $managementDate->format('d M Y h:i A') }}</small>
                                    @endif
                                @endif
                            @endif
                        </td>

                        {{-- Files / Attachments Column --}}
                        <td class="text-center">
                            @php
                                // Decode JSON strings to arrays if needed
                                $radiantFiles = is_string($report->radiant_collection_files) 
                                    ? json_decode($report->radiant_collection_files, true) 
                                    : ($report->radiant_collection_files ?? []);
                                $cardFiles = is_string($report->actual_card_files) 
                                    ? json_decode($report->actual_card_files, true) 
                                    : ($report->actual_card_files ?? []);
                                $bankFiles = is_string($report->bank_deposit_files) 
                                    ? json_decode($report->bank_deposit_files, true) 
                                    : ($report->bank_deposit_files ?? []);
                                $depositFiles = is_string($report->deposit_files) 
                                    ? json_decode($report->deposit_files, true) 
                                    : ($report->deposit_files ?? []);
                                $upiFiles = is_string($report->upi_files) 
                                    ? json_decode($report->upi_files, true) 
                                    : ($report->upi_files ?? []);
                                
                                // Ensure they're arrays
                                $radiantFiles = is_array($radiantFiles) ? $radiantFiles : [];
                                $cardFiles = is_array($cardFiles) ? $cardFiles : [];
                                $bankFiles = is_array($bankFiles) ? $bankFiles : [];
                                $depositFiles = is_array($depositFiles) ? $depositFiles : [];
                                $upiFiles = is_array($upiFiles) ? $upiFiles : [];
                                
                                $hasFiles = (count($radiantFiles) > 0) ||
                                            (count($cardFiles) > 0) ||
                                            (count($bankFiles) > 0) ||
                                            (count($depositFiles) > 0) ||
                                            (count($upiFiles) > 0);
                                $fileCount = count($radiantFiles) + 
                                             count($cardFiles) + 
                                             count($bankFiles) +
                                             count($depositFiles) +
                                             count($upiFiles);
                            @endphp
                            @if($hasFiles)
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    title="View Attachments"
                                    onclick="viewAttachments({{ $report->id }})"
                                >
                                    <i class="fas fa-paperclip me-1"></i>
                                    <span class="badge bg-light text-dark">{{ $fileCount }}</span>
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info" onclick="viewReport({{ $report->id }})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($admin->access_limits == 4 && $report->auditor_approval_status == 0)
                                    <button type="button" class="btn btn-sm btn-success" onclick="approveReportAuditor({{ $report->id }})" title="Auditor Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="openRejectModalAuditor({{ $report->id }})" title="Auditor Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                @if($admin->access_limits == 1 && $report->auditor_approval_status == 1 && $report->management_approval_status == 0)
                                    <button type="button" class="btn btn-sm btn-primary" onclick="approveReportManagement({{ $report->id }})" title="Management Approve">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="openRejectModalManagement({{ $report->id }})" title="Management Reject">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="18" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">No reports found matching your filters</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reports->isNotEmpty())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">TOTALS (Filtered):</td>
                        <td class="text-end text-success">₹{{ number_format($summary['total_radiant'] ?? 0, 2) }}</td>
                        <td class="text-end text-primary">₹{{ number_format($summary['total_card'] ?? 0, 2) }}</td>
                        <td class="text-end text-info">₹{{ number_format($summary['total_upi'] ?? 0, 2) }}</td>
                        <td class="text-end text-teal">₹{{ number_format($summary['total_deposit'] ?? 0, 2) }}</td>
                        <td class="text-end text-warning">₹{{ number_format($summary['total_bank'] ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($summary['total_discount'] ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($summary['total_cancel'] ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($summary['total_refund'] ?? 0, 2) }}</td>
                        <td class="text-end text-danger">₹{{ number_format($summary['total_cash_drawer'] ?? 0, 2) }}</td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($reports->hasPages())
    <div class="pagination-container" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
        <div>
            <small class="text-muted">
                Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries
            </small>
        </div>
        <div class="pagination-links">
            {{ $reports->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>

<style>
.pagination-links .pagination {
    margin: 0;
}
.pagination-links .page-link {
    color: #667eea;
    border: 1px solid #dee2e6;
    margin: 0 2px;
    border-radius: 5px;
}
.pagination-links .page-link:hover {
    background-color: #667eea;
    color: white;
}
.pagination-links .page-item.active .page-link {
    background-color: #667eea;
    border-color: #667eea;
    color: white;
}
.pagination-links .page-item.disabled .page-link {
    color: #6c757d;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}
.text-teal {
    color: #14b8a6 !important;
}
</style>