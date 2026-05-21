            <!-- Modal -->
            <div class="modal-overlay" id="reportModal">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 id="modalTitle">
                            <i class="fas fa-file-invoice-dollar"></i> New Financial Report
                        </h3>
                        <button class="modal-close" id="closeModalBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="reportForm">
                            @csrf
                            <input type="hidden" id="reportId" name="report_id">

                            <!-- Hidden inputs for date range -->
                            <input type="hidden" name="radiant_collection_from_date" id="radiant_collection_from_date">
                            <input type="hidden" name="radiant_collection_to_date" id="radiant_collection_to_date">

                            <!-- Basic Info Section -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-info-circle"></i> Basic Information
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Report Date <span class="required">*</span></label>
                                        <input type="date" name="report_date" id="report_date" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Zone <span class="required">*</span></label>
                                        <select name="zone_id" id="zone_id" class="form-control" required>
                                            <option value="">Select Zone</option>
                                            @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" data-name="{{ $zone->name }}">{{ $zone->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="zone_name" id="zone_name">
                                    </div>

                                    <div class="form-group">
                                        <label>Branch <span class="required">*</span></label>
                                        <select name="branch_id" id="branch_id" class="form-control" required>
                                            <option value="">Select Branch</option>
                                            @foreach($locations as $location)
                                            <option value="{{ $location->id }}"
                                                    data-zone="{{ $location->zone_id }}"
                                                    data-name="{{ $location->name }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="branch_name" id="branch_name">
                                    </div>
                                </div>
                            </div>

                            <!-- Radiant Cash Collection (UPDATED) -->
                            <div class="form-section gradient-section blue">
                                <h4 class="section-title">
                                    <i class="fas fa-wallet"></i> Radiant Cash Collection
                                </h4>

                                <!-- Checkbox for Radiant Not Collected -->
                                <div class="form-group full-width" style="margin-bottom: 15px;">
                                    <div class="custom-checkbox-wrapper">
                                        <input type="checkbox" name="radiant_not_collected" id="radiant_not_collected">
                                        <label for="radiant_not_collected">
                                            <i class="fas fa-exclamation-triangle"></i> Radiant Not Collected
                                        </label>
                                    </div>
                                </div>

                                <!-- Remarks Textarea (Hidden by default) -->
                                <div class="form-group full-width" id="radiantRemarksContainer" style="display: none; margin-bottom: 15px;">
                                    <label>Remarks for Not Collecting <span class="required">*</span></label>
                                    <textarea name="radiant_not_collected_remarks" id="radiant_not_collected_remarks" class="form-control" rows="3" placeholder="Please explain why radiant was not collected..."></textarea>
                                </div>

                                <div class="form-grid">
                                    <!-- Hidden Collection Date (Still saves to DB) -->
                                    <input type="hidden" name="radiant_collected_date" id="radiant_collected_date">

                                    <!-- Date Range Picker -->
                                    <div class="form-group">
                                        <label>Collection Date Range</label>
                                        <input type="text" name="radiant_date_range" id="radiant_date_range" class="form-control" placeholder="Select date range" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Collection Amount</label>
                                        <input type="number" step="0.01" name="radiant_collection_amount" id="radiant_collection_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Collection proof <span class="text-muted">(required if amount &gt; 0)</span></label>
                                        <input type="file" name="radiant_collection_files[]" id="radiant_collection_files" class="form-control file-input" multiple accept="image/*,.pdf,.doc,.docx,application/pdf">
                                        <div class="file-preview" id="radiant_collection_preview"></div>
                                    </div>
                                    <div class="form-group full-width">
                                        <label>Ledger book copy <span class="text-muted">(required if amount &gt; 0)</span></label>
                                        <input type="file" name="radiant_ledger_book_files[]" id="radiant_ledger_book_files" class="form-control file-input" multiple accept="image/*,.pdf,.doc,.docx,application/pdf">
                                        <div class="file-preview" id="radiant_ledger_book_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deposit Section (NEW) -->
                            <div class="form-section gradient-section indigo">
                                <h4 class="section-title">
                                    <i class="fas fa-hand-holding-usd"></i> Deposit
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Deposit Date</label>
                                        <input type="date" name="deposit_date" id="deposit_date" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Deposit Amount</label>
                                        <input type="number" step="0.01" name="deposit_amount" id="deposit_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments <span class="text-muted">(required if amount &gt; 0)</span></label>
                                        <input type="file" name="deposit_files[]" id="deposit_files" class="form-control file-input" multiple accept="image/*,.pdf,.doc,.docx,application/pdf">
                                        <div class="file-preview" id="deposit_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actual Card Amount -->
                            <div class="form-section gradient-section green">
                                <h4 class="section-title">
                                    <i class="fas fa-credit-card"></i> Actual Card Amount
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Actual Card Amount</label>
                                        <input type="number" step="0.01" name="actual_card_amount" id="actual_card_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments <span class="text-muted">(required if amount &gt; 0)</span></label>
                                        <input type="file" name="actual_card_files[]" id="actual_card_files" class="form-control file-input" multiple accept="image/*,.pdf,.doc,.docx,application/pdf">
                                        <div class="file-preview" id="actual_card_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- UPI Section (NEW) -->
                            <div class="form-section gradient-section yellow">
                                <h4 class="section-title">
                                    <i class="fas fa-mobile-alt"></i> UPI Collection
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>UPI Amount</label>
                                        <input type="number" step="0.01" name="upi_amount" id="upi_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments <span class="text-muted">(required if amount &gt; 0)</span></label>
                                        <input type="file" name="upi_files[]" id="upi_files" class="form-control file-input" multiple accept="image/*,.pdf,.doc,.docx,application/pdf">
                                        <div class="file-preview" id="upi_preview"></div>
                                    </div>
                                </div>
                            </div>



                            <!-- Direct Bank Deposit -->
                            <div class="form-section gradient-section purple">
                                <h4 class="section-title">
                                    <i class="fas fa-university"></i> Direct Bank Deposit
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Bank Deposit Amount</label>
                                        <input type="number" step="0.01" name="bank_deposit_amount" id="bank_deposit_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments <span class="text-muted">(required if amount &gt; 0)</span></label>
                                        <input type="file" name="bank_deposit_files[]" id="bank_deposit_files" class="form-control file-input" multiple accept="image/*,.pdf,.doc,.docx,application/pdf">
                                        <div class="file-preview" id="bank_deposit_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cashier Info -->
                            <div class="form-section gradient-section orange">
                                <h4 class="section-title">
                                    <i class="fas fa-user-tie"></i> Cashier Information
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Placed By Whom</label>
                                        <input type="text" name="placed_by_whom" id="placed_by_whom" class="form-control" placeholder="eg.12345 emp no">
                                    </div>

                                    <div class="form-group">
                                        <label>Locker By Whom</label>
                                        <input type="text" name="locker_by_whom" id="locker_by_whom" class="form-control" placeholder="eg.12345 emp no">
                                    </div>

                                    <div class="form-group">
                                        <label>Who Gave Radiant Cash</label>
                                        <input type="text" name="who_gave_radiant_cash" id="who_gave_radiant_cash" class="form-control" placeholder="eg.12345 emp no">
                                    </div>

                                    <div class="form-group">
                                        <label>Cash in Drawer</label>
                                        <input type="number" step="0.01" name="cash_in_drawer" id="cash_in_drawer" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="cashier_info_files[]" id="cashier_info_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="cashier_info_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Amounts -->
                            <div class="form-section gradient-section teal">
                                <h4 class="section-title">
                                    <i class="fas fa-calculator"></i> Additional Amounts
                                </h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Today's Discount Amount</label>
                                        <input type="number" step="0.01" name="today_discount_amount" id="today_discount_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label>Cancel Bill Amount</label>
                                        <input type="number" step="0.01" name="cancel_bill_amount" id="cancel_bill_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label>Refund Bill Amount</label>
                                        <input type="number" step="0.01" name="refund_bill_amount" id="refund_bill_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group">
                                        <label>POS Refund</label>
                                        <input type="number" step="0.01" name="pos_refund_amount" id="pos_refund_amount" class="form-control" placeholder="0.00">
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Attachments</label>
                                        <input type="file" name="additional_amounts_files[]" id="additional_amounts_files" class="form-control file-input" multiple>
                                        <div class="file-preview" id="additional_amounts_preview"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acknowledgement Section -->
                            <div class="acknowledgement-section">
                                <h4 class="section-title">
                                    <i class="fas fa-check-circle"></i> Acknowledgement
                                </h4>
                                <div class="acknowledgement-checkbox">
                                    <input type="checkbox" name="acknowledgement_agreed" id="acknowledgement_agreed" required>
                                    <label for="acknowledgement_agreed">
                                        <strong>I acknowledge and agree</strong> that all the information provided in this financial report is accurate and complete to the best of my knowledge. I understand that this data will be used for financial reconciliation and reporting purposes. <span class="required">*</span>
                                    </label>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
                        <button type="button" class="btn-submit" id="submitBtn">
                            <i class="fas fa-save"></i> Save Report
                        </button>
                    </div>
                </div>
            </div>
