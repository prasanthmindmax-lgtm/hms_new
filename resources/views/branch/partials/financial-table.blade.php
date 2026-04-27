<div class="financial-reports-wrapper">
    <!-- Per Page Selector Header -->
    <div class="table-controls-header">
        <div class="entries-selector">
            <span class="control-label">Show:</span>
            <select id="perPageSelect" class="entries-dropdown">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
            </select>
            <span class="control-label">entries</span>
        </div>
        <div class="entries-info">
            <small>Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries</small>
        </div>
    </div>

    <!-- Main Table -->
    <div class="table-responsive">
        <table class="financial-reports-table" id="reportsTable">
            <thead>
                <tr>
                    <th class="col-sno">S.No</th>
                    <th class="col-date">Date</th>
                    <th class="col-zone">Zone</th>
                    <th class="col-branch">Branch</th>
                    <th class="col-amount">Radiant </br>Collection</th>
                    <th class="col-amount">Actual Card</th>
                    <th class="col-amount">UPI</th>
                    <th class="col-amount">Deposit</th>
                    <th class="col-amount">Bank Deposit</th>
                    <th class="col-amount">Discount</th>
                    <th class="col-amount">Cancel</th>
                    <th class="col-amount">Refund</th>
                    <th class="col-amount">Cash In</br> Hand</th>
                    <th class="col-created">Created By</th>
                    <th class="col-edits">Edits</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $report)
                @php
                    $fRadiant = json_decode($report->radiant_collection_files ?? '[]', true) ?: [];
                    $fLedger = (isset($report->radiant_ledger_book_files) && $report->radiant_ledger_book_files)
                        ? (json_decode($report->radiant_ledger_book_files, true) ?: [])
                        : [];
                    $fCard = json_decode($report->actual_card_files ?? '[]', true) ?: [];
                    $fUpi = json_decode($report->upi_files ?? '[]', true) ?: [];
                    $fDep = json_decode($report->deposit_files ?? '[]', true) ?: [];
                    $fBank = json_decode($report->bank_deposit_files ?? '[]', true) ?: [];
                    $fCashier = json_decode($report->cashier_info_files ?? '[]', true) ?: [];
                    $fAdd = json_decode($report->additional_amounts_files ?? '[]', true) ?: [];
                    $allAttachmentGroups = array_values(array_filter([
                        ['title' => 'Radiant – Collection proof', 'files' => $fRadiant],
                        ['title' => 'Radiant – Ledger book', 'files' => $fLedger],
                        ['title' => 'Deposit', 'files' => $fDep],
                        ['title' => 'Actual card', 'files' => $fCard],
                        ['title' => 'UPI', 'files' => $fUpi],
                        ['title' => 'Direct bank deposit', 'files' => $fBank],
                        ['title' => 'Cashier info', 'files' => $fCashier],
                        ['title' => 'Additional amounts', 'files' => $fAdd],
                    ], function ($g) { return ! empty($g['files']); }));
                    $hasAnyAttachment = count($allAttachmentGroups) > 0;
                @endphp
                <tr class="report-row">
                    <!-- S.No -->
                    <td class="text-center">{{ $reports->firstItem() + $index }}</td>
                    
                    <!-- Date -->
                    <td class="date-cell">
                        {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                    </td>
                    
                    <!-- Zone -->
                    <td class="zone-cell">
                        <span class="zone-badge">{{ $report->zone_name }}</span>
                    </td>
                    
                    <!-- Branch -->
                    <td class="branch-cell">{{ $report->branch_name }}</td>
                    
                    <!-- Radiant Collection -->
                    <td class="amount-cell">
                        <div class="amount-container">
                            <div class="amount-value">
                                ₹{{ number_format($report->radiant_collection_amount, 2) }}
                                @if(count($fRadiant))
                                <i class="fas fa-paperclip attachment-icon file-preview-trigger text-primary" title="Collection proof"
                                   data-files='@json($fRadiant)'
                                   data-title="Radiant – Collection proof"></i>
                                @endif
                                @if(count($fLedger))
                                <i class="fas fa-book attachment-icon file-preview-trigger text-info" title="Ledger book"
                                   data-files='@json($fLedger)'
                                   data-title="Radiant – Ledger book copy"></i>
                                @endif
                            </div>
                            @if($report->radiant_not_collected)
                            <span class="not-collected-badge" title="{{ $report->radiant_not_collected_remarks }}">
                                <i class="fas fa-exclamation-circle"></i> Not Collected
                            </span>
                            @endif
                            @if($report->radiant_collection_from_date && $report->radiant_collection_to_date)
                            <small class="date-range">
                                {{ \Carbon\Carbon::parse($report->radiant_collection_from_date)->format('d M') }} - 
                                {{ \Carbon\Carbon::parse($report->radiant_collection_to_date)->format('d M') }}
                            </small>
                            @elseif($report->radiant_collected_date)
                            <small class="date-info">
                                {{ \Carbon\Carbon::parse($report->radiant_collected_date)->format('d M, h:i A') }}
                            </small>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Actual Card -->
                    <td class="amount-cell">
                        <div class="amount-container">
                            <div class="amount-value">
                                ₹{{ number_format($report->actual_card_amount, 2) }}
                                @if(count($fCard))
                                <i class="fas fa-paperclip attachment-icon file-preview-trigger"
                                   data-files='@json($fCard)'
                                   data-title="Actual card files"></i>
                                @endif
                            </div>
                        </div>
                    </td>
                    
                    <!-- UPI -->
                    <td class="amount-cell">
                        <div class="amount-container">
                            <div class="amount-value">
                                ₹{{ number_format($report->upi_amount ?? 0, 2) }}
                                @if(count($fUpi))
                                <i class="fas fa-paperclip attachment-icon file-preview-trigger"
                                   data-files='@json($fUpi)'
                                   data-title="UPI files"></i>
                                @endif
                            </div>
                        </div>
                    </td>
                    
                    <!-- Deposit -->
                    <td class="amount-cell">
                        <div class="amount-container">
                            <div class="amount-value">
                                ₹{{ number_format($report->deposit_amount ?? 0, 2) }}
                                @if(count($fDep))
                                <i class="fas fa-paperclip attachment-icon file-preview-trigger"
                                   data-files='@json($fDep)'
                                   data-title="Deposit files"></i>
                                @endif
                            </div>
                            @if(isset($report->deposit_date) && $report->deposit_date)
                            <small class="date-info">{{ \Carbon\Carbon::parse($report->deposit_date)->format('d M Y') }}</small>
                            @endif
                        </div>
                    </td>
                    
                    <!-- Bank Deposit -->
                    <td class="amount-cell">
                        <div class="amount-container">
                            <div class="amount-value">
                                ₹{{ number_format($report->bank_deposit_amount, 2) }}
                                @if(count($fBank))
                                <i class="fas fa-paperclip attachment-icon file-preview-trigger"
                                   data-files='@json($fBank)'
                                   data-title="Direct bank deposit files"></i>
                                @endif
                            </div>
                        </div>
                    </td>
                    
                    <!-- Discount -->
                    <td class="amount-cell">
                        ₹{{ number_format($report->today_discount_amount, 2) }}
                    </td>
                    
                    <!-- Cancel -->
                    <td class="amount-cell">
                        ₹{{ number_format($report->cancel_bill_amount, 2) }}
                    </td>
                    
                    <!-- Refund -->
                    <td class="amount-cell">
                        ₹{{ number_format($report->refund_bill_amount, 2) }}
                    </td>
                    <td class="amount-cell">
                        ₹{{ number_format($report->cash_in_drawer, 2) }}
                    </td>
                    
                    <!-- Created By -->
                    <td class="created-cell">
                        <div class="user-info">
                            <div class="user-name">{{ $report->created_by_name }}</div>
                            <small class="user-date">{{ \Carbon\Carbon::parse($report->created_at)->format('d M, h:i A') }}</small>
                        </div>
                    </td>
                    
                    <!-- Edits -->
                    <td class="edits-cell">
                        @if($report->edit_count > 0)
                        <span class="edit-badge" data-history="{{ $report->edit_history }}">
                            <span class="edit-count">{{ $report->edit_count }}</span> edits
                        </span>
                        @else
                        <span class="no-edits">-</span>
                        @endif
                    </td>
                    
                    <!-- Actions -->
                    <td class="actions-cell">
                        <div class="action-buttons">
                            @if($hasAnyAttachment)
                            <button type="button" class="btn-action btn-files file-preview-all-trigger" data-groups='@json($allAttachmentGroups)' title="View all attachments">
                                <i class="fas fa-folder-open"></i>
                            </button>
                            @endif
                            <button class="btn-action btn-edit" data-id="{{ $report->id }}" title="Edit Report">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="empty-state">
                        <div class="empty-content">
                            <i class="fas fa-inbox"></i>
                            <p>No reports found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($reports->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            <small>Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() }} entries</small>
        </div>
        <div class="pagination-controls">
            {{ $reports->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>