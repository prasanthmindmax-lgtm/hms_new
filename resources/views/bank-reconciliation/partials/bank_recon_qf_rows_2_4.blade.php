{{-- Rows 2–3 (+ no-bank row3): 5+5+6 when bank (16 on last row); 5+5+4 when no bank (14) --}}
{{-- Row 2: with bank → Zone, Branch, Nature, Category, Pay | no bank → Nature, Category, Pay, Bill, Radiant --}}
<div class="row g-2 mb-2 align-items-end bank-recon-qf-row--5">
    @if(!empty($bankAccountsEnabled))
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label" title="Filters vendor bill zone, income-tag branch / description, and salary sheet branch (UTR upload).">
            <i class="bi bi-map"></i> Zone
        </div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-zone" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All zones</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-zone">
                <div class="qf-menu-search-wrap">
                    <input type="text" class="qf-search-input" placeholder="Search zones…">
                </div>
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfZone_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfZone_all">All zones</label>
                </div>
                <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
            </div>
        </div>
        <select id="qfZone" multiple class="d-none"></select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label" title="Filters vendor bill branch, income-tag branch / description, and salary Excel branch column.">
            <i class="bi bi-building"></i> Branch
        </div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-branch" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All branches</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-branch">
                <div class="qf-menu-search-wrap">
                    <input type="text" class="qf-search-input" placeholder="Search branches…">
                </div>
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfBranch_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfBranch_all">All branches</label>
                </div>
                <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
            </div>
        </div>
        <select id="qfBranch" multiple class="d-none"></select>
    </div>
    @endif

    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c bank-recon-qf-col--nature">
        <div class="bank-recon-qf-field-label" title="Chart account nature stored on the statement or matched bill.">
            <i class="bi bi-diagram-2"></i> Nature of payment
        </div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-natureAccount" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All natures</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-natureAccount">
                <div class="qf-menu-search-wrap">
                    <input type="text" class="qf-search-input" placeholder="Search nature of payment…">
                </div>
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfNatureAccount_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfNatureAccount_all">All natures</label>
                </div>
                <div class="qf-menu-list" style="max-height: 240px; overflow: auto;">
                    <div class="qf-options-inner"></div>
                </div>
            </div>
        </div>
        <select id="qfNatureAccount" multiple class="d-none"></select>
    </div>

    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-tag"></i> Category</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-category" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-category">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfCategory_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfCategory_all">All</label>
                </div>
                <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
            </div>
        </div>
        <select id="qfCategory" multiple class="d-none">
            <option value="categorized">Categorized</option>
            <option value="uncategorized">Uncategorized</option>
        </select>
    </div>

    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-arrow-down-up"></i> PAY IN / PAY OUT</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-txnType" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All types</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-txnType">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfTxnType_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfTxnType_all">All types</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfTxnType_deposit" value="deposit">
                            <label class="qf-menu-item-text" for="brqf_qfTxnType_deposit">PAY IN</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfTxnType_withdrawal" value="withdrawal">
                            <label class="qf-menu-item-text" for="brqf_qfTxnType_withdrawal">PAY OUT</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfTxnType" multiple class="d-none">
            <option value="deposit">PAY IN</option>
            <option value="withdrawal">PAY OUT</option>
        </select>
    </div>

    @if(empty($bankAccountsEnabled))
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-check-circle"></i> Bill Match</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-expenseMatch" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All statuses</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-expenseMatch">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfExpenseMatch_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_all">All statuses</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfExpenseMatch_unmatched" value="unmatched">
                            <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_unmatched">Unmatched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfExpenseMatch_matched" value="matched">
                            <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_matched">Matched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfExpenseMatch_partially_matched" value="partially_matched">
                            <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_partially_matched">Partially matched</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfExpenseMatch" multiple class="d-none">
            <option value="unmatched">Unmatched</option>
            <option value="matched">Matched</option>
            <option value="partially_matched">Partially matched</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-brightness-high"></i> Radiant</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-radiantMatch" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-radiantMatch">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfRadiantMatch_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_all">All</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfRadiantMatch_radiant_matched" value="radiant_matched">
                            <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_radiant_matched">Radiant linked</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfRadiantMatch_radiant_keyword_only" value="radiant_keyword_only">
                            <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_radiant_keyword_only">Keyword only</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfRadiantMatch_radiant_unmatched" value="radiant_unmatched">
                            <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_radiant_unmatched">Not linked</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfRadiantMatch" multiple class="d-none">
            <option value="radiant_matched">Radiant linked</option>
            <option value="radiant_keyword_only">Keyword only</option>
            <option value="radiant_unmatched">Not linked</option>
        </select>
    </div>
    @endif
</div>

@if(!empty($bankAccountsEnabled))
{{-- Row 3 (bank only): Bill, Radiant, Income, Salary, Matched, Vendor — one row at xl+ (see .bank-recon-qf-row--6 in index) --}}
<div class="row g-2 mb-2 align-items-end bank-recon-qf-row--6">
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf6-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-check-circle"></i> Bill Match</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-expenseMatch" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All statuses</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-expenseMatch">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfExpenseMatch_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_all">All statuses</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfExpenseMatch_unmatched" value="unmatched">
                            <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_unmatched">Unmatched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfExpenseMatch_matched" value="matched">
                            <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_matched">Matched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfExpenseMatch_partially_matched" value="partially_matched">
                            <label class="qf-menu-item-text" for="brqf_qfExpenseMatch_partially_matched">Partially matched</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfExpenseMatch" multiple class="d-none">
            <option value="unmatched">Unmatched</option>
            <option value="matched">Matched</option>
            <option value="partially_matched">Partially matched</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf6-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-brightness-high"></i> Radiant</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-radiantMatch" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-radiantMatch">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfRadiantMatch_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_all">All</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfRadiantMatch_radiant_matched" value="radiant_matched">
                            <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_radiant_matched">Radiant linked</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfRadiantMatch_radiant_keyword_only" value="radiant_keyword_only">
                            <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_radiant_keyword_only">Keyword only</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfRadiantMatch_radiant_unmatched" value="radiant_unmatched">
                            <label class="qf-menu-item-text" for="brqf_qfRadiantMatch_radiant_unmatched">Not linked</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfRadiantMatch" multiple class="d-none">
            <option value="radiant_matched">Radiant linked</option>
            <option value="radiant_keyword_only">Keyword only</option>
            <option value="radiant_unmatched">Not linked</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf6-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-bookmark"></i> Income Tag</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-incomeMatch" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-incomeMatch">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfIncomeMatch_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfIncomeMatch_all">All</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfIncomeMatch_income_matched" value="income_matched">
                            <label class="qf-menu-item-text" for="brqf_qfIncomeMatch_income_matched">Income matched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfIncomeMatch_income_unmatched" value="income_unmatched">
                            <label class="qf-menu-item-text" for="brqf_qfIncomeMatch_income_unmatched">Income unmatched</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfIncomeMatch" multiple class="d-none">
            <option value="income_matched">Income matched</option>
            <option value="income_unmatched">Income unmatched</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf6-c">
        <div class="bank-recon-qf-field-label" title="Filter lines by salary UTR tagging from the salary Excel upload.">
            <i class="bi bi-cash-coin"></i> Salary UTR
        </div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-salaryTag" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-salaryTag">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfSalaryTag_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfSalaryTag_all">All</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfSalaryTag_tagged" value="tagged">
                            <label class="qf-menu-item-text" for="brqf_qfSalaryTag_tagged">Salary UTR matched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfSalaryTag_not" value="not_tagged">
                            <label class="qf-menu-item-text" for="brqf_qfSalaryTag_not">No salary UTR</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfSalaryTag" multiple class="d-none">
            <option value="tagged">Salary UTR matched</option>
            <option value="not_tagged">No salary UTR</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf6-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-person"></i> Matched By</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-matchedBy" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">Anyone</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-matchedBy">
                <div class="qf-menu-search-wrap">
                    <input type="text" class="qf-search-input" placeholder="Search users…">
                </div>
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfMatchedBy_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfMatchedBy_all">Anyone</label>
                </div>
                <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
            </div>
        </div>
        <select id="qfMatchedBy" multiple class="d-none"></select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf6-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-person-badge"></i> Vendor Name</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-vendor" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All vendors</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-vendor">
                <div class="qf-menu-search-wrap">
                    <input type="text" class="qf-search-input" placeholder="Search vendors…">
                </div>
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfVendor_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfVendor_all">All vendors</label>
                </div>
                <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
            </div>
        </div>
        <select id="qfVendor" multiple class="d-none"></select>
    </div>
</div>
@else
{{-- No-bank: row3 — Income, Salary, Matched, Vendor (4) --}}
<div class="row g-2 mb-2 align-items-end bank-recon-qf-row--5">
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-bookmark"></i> Income Tag</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-incomeMatch" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-incomeMatch">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfIncomeMatch_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfIncomeMatch_all">All</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfIncomeMatch_income_matched" value="income_matched">
                            <label class="qf-menu-item-text" for="brqf_qfIncomeMatch_income_matched">Income matched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfIncomeMatch_income_unmatched" value="income_unmatched">
                            <label class="qf-menu-item-text" for="brqf_qfIncomeMatch_income_unmatched">Income unmatched</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfIncomeMatch" multiple class="d-none">
            <option value="income_matched">Income matched</option>
            <option value="income_unmatched">Income unmatched</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label" title="Filter lines by salary UTR tagging from the salary Excel upload.">
            <i class="bi bi-cash-coin"></i> Salary UTR
        </div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-salaryTag" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-salaryTag">
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfSalaryTag_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfSalaryTag_all">All</label>
                </div>
                <div class="qf-menu-list">
                    <div class="qf-options-inner">
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfSalaryTag_tagged" value="tagged">
                            <label class="qf-menu-item-text" for="brqf_qfSalaryTag_tagged">Salary UTR matched</label>
                        </div>
                        <div class="qf-menu-item">
                            <input type="checkbox" id="brqf_qfSalaryTag_not" value="not_tagged">
                            <label class="qf-menu-item-text" for="brqf_qfSalaryTag_not">No salary UTR</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <select id="qfSalaryTag" multiple class="d-none">
            <option value="tagged">Salary UTR matched</option>
            <option value="not_tagged">No salary UTR</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-person"></i> Matched By</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-matchedBy" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">Anyone</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-matchedBy">
                <div class="qf-menu-search-wrap">
                    <input type="text" class="qf-search-input" placeholder="Search users…">
                </div>
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfMatchedBy_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfMatchedBy_all">Anyone</label>
                </div>
                <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
            </div>
        </div>
        <select id="qfMatchedBy" multiple class="d-none"></select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 min-w-0 bank-recon-qf5-c">
        <div class="bank-recon-qf-field-label"><i class="bi bi-person-badge"></i> Vendor Name</div>
        <div class="dropdown w-100">
            <button class="bank-recon-qf-btn w-100 text-start" type="button" id="qfBtn-vendor" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                <span class="qf-btn-text">All vendors</span>
                <i class="bi bi-chevron-down qf-btn-arrow"></i>
            </button>
            <div class="dropdown-menu bank-recon-qf-menu w-100" id="qfMenu-vendor">
                <div class="qf-menu-search-wrap">
                    <input type="text" class="qf-search-input" placeholder="Search vendors…">
                </div>
                <div class="qf-menu-item qf-menu-item-all">
                    <input type="checkbox" class="qf-all-chk" id="brqf_qfVendor_all" checked>
                    <label class="qf-menu-item-text" for="brqf_qfVendor_all">All vendors</label>
                </div>
                <div class="qf-menu-list"><div class="qf-options-inner"></div></div>
            </div>
        </div>
        <select id="qfVendor" multiple class="d-none"></select>
    </div>
</div>
@endif
