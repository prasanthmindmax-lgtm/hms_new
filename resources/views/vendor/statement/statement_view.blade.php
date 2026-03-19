<div class="left">
  
          <p><strong>To</strong><br>
          <a href="#">{{ $bills->first()->Tblvendor->display_name ?? 'null' }}</a><br>
          {{ $bills->first()->TblBilling->address ?? 'null' }}<br>
          {{ $bills->first()->TblBilling->city ?? 'null' }}<br>
          {{ $bills->first()->TblBilling->state ?? 'null' }} {{ $bills->first()->TblBilling->zip_code ?? 'null' }} <br>
          </p>
      </div>

      <div class="right">
          <p><strong>{{ $bills->first()->TblBilling->company_name ?? "Dr.Aravind's IVF Private Limited" }}</strong><br>
          {{ $bills->first()->TblBilling->state ?? 'Tamil Nadu' }}<br>
          {{ $bills->first()->TblBilling->city ?? 'Chennai' }}<br>
          {{ $bills->first()->TblBilling->phone ?? '+91 90 2012 2012' }}<br>
          {{ $bills->first()->TblBilling->email ?? 'info@draravindsivf.com' }}</p>
      </div>
      <div style="clear: both;"></div>

      <div class="statement">
          <h2>Statement of Accounts</h2>
          {{-- <small>{{ now()->startOfMonth()->format('d/m/Y') }} To {{ now()->endOfMonth()->format('d/m/Y') }}</small> --}}
          <small>{{$from->format('d/m/Y')}} to {{$to->format('d/m/Y')}}</small>
      </div>

      {{-- Account Summary --}}
      @php
          // $opening = 137200; // replace with logic
          $billed  = $bills->sum(fn($b)=>$b->grand_total_amount);
          $paid    = $bills->sum(fn($b)=>$b->tax_amount);
          // $balance = $opening + $billed - $paid;
          $balance = $billed - $paid;
      @endphp

      <table class="summary">
          <tr><th colspan="2">Account Summary</th></tr>
          {{-- <tr><td>Opening Balance</td><td>₹ {{ number_format($opening,2) }}</td></tr> --}}
          <tr><td>Billed Amount</td><td>₹ {{ number_format($billed,2) }}</td></tr>
          <tr><td>Amount Paid</td><td>₹ {{ number_format($paid,2) }}</td></tr>
          <tr><td><strong>Balance Due</strong></td><td><strong>₹ {{ number_format($balance,2) }}</strong></td></tr>
      </table>
  </div>

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


              {{-- Loop Bills --}}
              @php $runningBalance = 0; @endphp

              {{-- Loop bills --}}
              @foreach($bills as $bill)
                  @php
                      // $billDate = $bill->bill_date
                      //     ? \Carbon\Carbon::parse($bill->bill_date)->format('d/m/Y')
                      //     : \Carbon\Carbon::parse($bill->created_at ?? now())->format('d/m/Y');

                      $grandTotal = (float) $bill->grand_total_amount;
                      $runningBalance += $grandTotal;
                  @endphp

                  {{-- Bill row --}}
                  <tr>
                      <td>{{ $bill->bill_date }}

                      </td>
                      <td>Bill</td>
                      <td>
                          {{ $bill->bill_gen_number ?? '' }}<br>
                          {{ $bill->bill_number ?? '' }}
                          @if($bill->due_date)
                              - due on {{ $bill->due_date }}
                          @endif
                      </td>
                      <td>{{ number_format($grandTotal, 2) }}</td>
                      <td></td>
                      <td>{{ number_format($runningBalance, 2) }}</td>
                  </tr>

                  {{-- Tax / Withheld row (if any) --}}
                  @if(!empty($bill->tax_type) && (float) $bill->tax_amount > 0)
                      @php
                          $runningBalance -= (float) $bill->tax_amount;
                      @endphp
                      <tr>
                          <td>{{ $bill->bill_date  }}</td>
                          <td>{{ $bill->tax_type ?? 'Tax Withheld' }}</td>
                          <td>Bill Number - {{ $bill->bill_number ?? $bill->bill_gen_number ?? $bill->id }}</td>
                          <td></td>
                          <td>{{ number_format($bill->tax_amount, 2) }}</td>
                          <td>{{ number_format($runningBalance, 2) }}</td>
                      </tr>
                  @endif
              @endforeach

              {{-- Final balance row --}}
              <tr class="balance-row">
                  <td colspan="5" style="text-align:right;">Balance Due</td>
                  <td>₹ {{ number_format($runningBalance, 2) }}</td>
              </tr>
          </tbody>
      </table>
    </div>