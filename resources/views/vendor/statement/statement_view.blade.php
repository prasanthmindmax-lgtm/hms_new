@php
    $v = $vendor ?? $bills->first()?->Tblvendor;
    $b = $billing ?? $bills->first()?->TblBilling;
@endphp

@if($v || $b)
      <div class="left">
          <p><strong>To</strong><br>
          <a href="#">{{ $v?->display_name ?? '—' }}</a><br>
          {{ $b?->address ?? '—' }}<br>
          {{ $b?->city ?? '—' }}<br>
          {{ $b?->state ?? '—' }} {{ $b?->zip_code ?? '' }} <br>
          </p>
      </div>

      <div class="right">
          <p><strong>{{ $b?->company_name ?? "Dr.Aravind's IVF Private Limited" }}</strong><br>
          {{ $b?->state ?? 'Tamil Nadu' }}<br>
          {{ $b?->city ?? 'Chennai' }}<br>
          {{ $b?->phone ?? '+91 90 2012 2012' }}<br>
          {{ $b?->email ?? 'info@draravindsivf.com' }}</p>
      </div>
@else
      <div class="left"><p class="text-muted">No vendor address on file for this period.</p></div>
@endif
      <div style="clear: both;"></div>

      <div class="statement">
          <h2>Statement of Accounts</h2>
          <small>{{$from->format('d/m/Y')}} to {{$to->format('d/m/Y')}}</small>
      </div>

      <table class="summary">
          <tr><th colspan="2">Account Summary</th></tr>
          <tr><td>Billed Amount</td><td>₹ {{ number_format($billed, 2) }}</td></tr>
          <tr><td>Tax / Withheld</td><td>₹ {{ number_format($withheldTax, 2) }}</td></tr>
          <tr><td>Payments (against bills)</td><td>₹ {{ number_format($cashPayments, 2) }}</td></tr>
          <tr><td><strong>Balance Due</strong></td><td><strong>₹ {{ number_format($balanceDue, 2) }}</strong></td></tr>
      </table>

      <div style="clear: both;"></div>
    <div>
      <table class="statement-table">
          <thead>
              <tr>
                  <th>Date</th>
                  <th>Transactions</th>
                  <th>Details</th>
                  <th>Amount</th>
                  <th>Payments</th>
                  <th>Balance</th>
              </tr>
          </thead>
          <tbody>
              @forelse($transactions as $row)
                  <tr>
                      <td>{{ $row['date'] }}</td>
                      <td>{{ $row['type'] }}</td>
                      <td>{{ $row['details'] }}</td>
                      <td>{{ $row['amount'] > 0 ? number_format($row['amount'], 2) : '' }}</td>
                      <td>{{ $row['payment'] > 0 ? number_format($row['payment'], 2) : '' }}</td>
                      <td>{{ number_format($row['balance'], 2) }}</td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="6" class="text-center text-muted">No bills in this date range.</td>
                  </tr>
              @endforelse

              @if(count($transactions))
              <tr class="balance-row">
                  <td colspan="5" style="text-align:right;">Balance Due</td>
                  <td>₹ {{ number_format($balanceDue, 2) }}</td>
              </tr>
              @endif
          </tbody>
      </table>
    </div>
