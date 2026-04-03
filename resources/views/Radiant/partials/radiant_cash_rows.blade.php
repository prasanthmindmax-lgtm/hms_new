@forelse($records as $r)
<tr class="rcp-row-clickable"
    data-id="{{ $r->id }}"
    data-location="{{ e($r->location ?? '') }}"
    data-date="{{ $r->pickup_date_parsed ? $r->pickup_date_parsed->format('Y-m-d') : '' }}"
    style="cursor:pointer;"
    title="Click to compare with Branch Report &amp; Bank Statement">
  <td class="sno-cell">{{ $r->sno ?? '—' }}</td>
  <td style="white-space:nowrap;">
    <span style="font-size:.78rem;font-weight:700;color:var(--text);">{{ $r->pickup_date ?? '—' }}</span>
  </td>
  <td>
    @php
      $stateMap = ['Tamilnadu'=>'bdg-amber','Karnataka'=>'bdg-teal','Kerala'=>'bdg-violet','Andhra Pradesh'=>'bdg-navy'];
      $stCls = $stateMap[$r->state_name] ?? 'bdg-navy';
    @endphp
    <span class="bdg {{ $stCls }}">{{ $r->state_name ?? '—' }}</span>
  </td>
  <td>
    <div style="font-size:.8rem;font-weight:700;color:var(--text);">{{ $r->region ?? '—' }}</div>
  </td>
  <td>
    <div style="font-size:.8rem;font-weight:600;color:var(--text2);">{{ $r->location ?? '—' }}</div>
    @if($r->pickup_point_code)
      <div style="font-size:.7rem;color:var(--text3);font-family:var(--mono);">{{ $r->pickup_point_code }}</div>
    @endif
  </td>
  <td>
    <span style="font-family:var(--mono);font-size:.78rem;color:var(--teal);font-weight:700;">{{ $r->hci_slip_no ?? '—' }}</span>
  </td>
  <td>
    <span style="font-family:var(--mono);font-size:.72rem;color:var(--text3);">{{ $r->point_id ?? '—' }}</span>
  </td>
  <td class="amt {{ $r->pickup_amount > 100000 ? 'amt-big' : '' }}">
    ₹{{ number_format($r->pickup_amount ?? 0, 0) }}
  </td>
  <td style="font-size:.75rem;color:var(--text3);font-family:var(--mono);">
    ₹{{ number_format($r->cash_limit ?? 0, 0) }}
  </td>
  <td>
    <span class="bdg bdg-green">{{ $r->deposit_mode ?? '—' }}</span>
  </td>
  <td>
    <div class="denom-row">
      @foreach([2000=>$r->denom_2000,1000=>$r->denom_1000,500=>$r->denom_500,200=>$r->denom_200,100=>$r->denom_100,50=>$r->denom_50,20=>$r->denom_20,10=>$r->denom_10,5=>$r->denom_5] as $d=>$cnt)
        @if($cnt > 0)
          <span class="denom-pill has-val">₹{{ $d }}×{{ (int)$cnt }}</span>
        @endif
      @endforeach
    </div>
  </td>
  <td class="sno-cell">{{ $r->coins > 0 ? '₹'.$r->coins : '—' }}</td>
  <td style="font-family:var(--mono);font-size:.75rem;color:{{ ($r->difference ?? 0) != 0 ? 'var(--rose)' : 'var(--text3)' }};">
    {{ $r->difference != 0 ? '₹'.number_format($r->difference, 0) : '—' }}
  </td>
  <td>
    <span style="font-size:.73rem;color:var(--text2);">{{ $r->remarks ?? '—' }}</span>
  </td>
  <td>
    <span class="bdg {{ strtoupper($r->ccv ?? '') === 'YES' ? 'ccv-yes' : 'ccv-no' }}">
      {{ $r->ccv ?? '—' }}
    </span>
  </td>
  <td>
    @if($r->upload_batch_id)
      <span style="font-size:.65rem;font-family:var(--mono);color:var(--text3);">
        {{ substr($r->upload_batch_id, 0, 16) }}…
      </span>
    @else
      <span style="color:var(--text3);">—</span>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="16">
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      <p>No records found. Try adjusting filters or upload an Excel file.</p>
    </div>
  </td>
</tr>
@endforelse
