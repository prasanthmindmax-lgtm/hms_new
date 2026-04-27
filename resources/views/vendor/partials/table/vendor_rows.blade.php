<div class="table-scroll" style="overflow-x: auto;">
  <table class="table table-hover mb-0">
      <thead>
          <tr>
              <th style="width: 30px;"><input type="checkbox" id="selectAllRows" /></th>
              <th>ID</th>
              <th>NAME</th>
              <th>COMPANY NAME</th>
              <th>PAN NUMBER</th>
              <th>GST NUMBER</th>
              <th>EMAIL</th>
              <th>WORK PHONE</th>
              <th>REFERENCE</th>
              <th>RECEIVABLES (BCY)</th>
              <th>UNUSED CREDITS (BCY)</th>
              <th>STATUS</th>
          </tr>
      </thead>
      <tbody>
          @forelse ($vendor as $v)
              <tr class="customer-row"
                  data-id="{{ $v->id }}"
                  data-name="{{ $v->display_name }}"
                  data-email="{{ $v->email }}"
                  data-phone="{{ $v->work_phone }}"
                  data-company="{{ $v->company_name }}"
                  data-balance="{{ $v->opening_balance ?? 0 }}"
                  data-currency="INR"
                  data-pan="{{ $v->pan_number ?? '' }}"
                  data-payment_terms="{{ $v->payment_terms }}"
                  data-portal_language="{{ $v->portal_language }}"
                  data-type="{{ $v->customer_type }}"
                  data-billingAddress='@json($v->billingAddress)'
                  data-shippingAddress='@json($v->shippingAddress)'
                  data-contacts='@json($v->contacts)'
                  data-bankdetails='@json($v->bankdetails)'
                  data-created_at="{{ $v->created_at }}"
                  data-remarks="{{ $v->remarks }}"
                  data-history='@json($v->history)'
                  data-created_by="{{ $v->user_id }}"
                  data-created-by-name="{{ e(trim((string) ($v->creator?->user_fullname ?? $v->creator?->username ?? '')) ?: '—') }}"
                  data-all_data='@json($v)'
              >
                  <td><input type="checkbox" class="row-checkbox" value="{{ $v->id }}" /></td>
                  <td>{{ $v->vendor_id }}</td>
                  <td>
                      <a href="#" class="customer-link">
                          <span title="{{ $v->display_name }}">
                              {{ \Illuminate\Support\Str::limit($v->display_name, 20, '...') }}
                          </span>
                          <br><span style="font-size:11px;color:#8898aa;">{{ $v->created_at->format('d/m/Y') }}</span>
                      </a>
                  </td>
                  <td>
                      <span title="{{ $v->company_name }}">
                          {{ \Illuminate\Support\Str::limit($v->company_name ?? '', 25, '...') }}
                      </span>
                  </td>
                  <td>{{ $v->pan_number ?? '-' }}</td>
                  <td>{{ $v->gst_number ?? '-' }}</td>
                  <td>{{ $v->email ?? '-' }}</td>
                  <td>{{ $v->work_phone ?? '-' }}</td>
                  <td>{{ $v->reference ?? '-' }}</td>
                  <td>₹{{ number_format($v->opening_balance ?? 0, 2) }}</td>
                  <td>₹0.00</td>
                  <td>
                      <div class="form-check form-switch d-flex align-items-center gap-2" style="padding-left:0;">
                          <input class="form-check-input vendor-status-toggle" type="checkbox"
                              data-id="{{ $v->id }}"
                              {{ $v->active_status == 0 ? 'checked' : '' }}
                              style="cursor:pointer; width:38px; height:20px; margin:0;">
                          <span class="vendor-status-label badge {{ $v->active_status == 0 ? 'bg-success' : 'bg-secondary' }}"
                              style="font-size:11px;">
                              {{ $v->active_status == 0 ? 'Active' : 'Inactive' }}
                          </span>
                      </div>
                  </td>
              </tr>
          @empty
              <tr>
                  <td colspan="12" class="text-center py-5 text-muted">
                      <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                      No vendors found
                  </td>
              </tr>
          @endforelse
      </tbody>
  </table>
</div>

{{-- Pagination: only show when there is more than 1 page --}}
@if($vendor->lastPage() > 1)
<div class="qd-pagination d-flex justify-content-between align-items-center mt-2 px-1">
  <div>{{ $vendor->links('pagination::bootstrap-4') }}</div>
  <div style="display:flex;align-items:center;gap:6px;">
    <select id="per_page" class="form-control form-control-sm" style="width:70px;">
      @foreach([10, 25, 50, 100, 250, 500] as $size)
        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
      @endforeach
    </select>
    <span style="font-size:13px;color:#6c757d;">entries</span>
  </div>
</div>
@endif
