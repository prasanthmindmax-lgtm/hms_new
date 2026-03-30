{{-- Single partial: counts + page nav (no separate pagination view) --}}
<div class="pg-wrap rcp-pg-row" id="rcpPgWrapInner">
  <div class="rcp-pg-meta">
    <span class="rcp-pg-line">
      Showing <strong id="rcpResFrom">{{ $records->firstItem() ?? 0 }}</strong>–<strong id="rcpResTo">{{ $records->lastItem() ?? 0 }}</strong>
      of <strong id="rcpResTotal">{{ $records->total() }}</strong> records
      @if($records->total() > 0)
        <span class="rcp-pg-sep">·</span>
        Page {{ $records->currentPage() }} of {{ max(1, $records->lastPage()) }}
      @endif
    </span>
  </div>
  @if($records->hasPages())
    <div class="rcp-pg-links-col pg-links rcp-pg-links">
      <nav class="rcp-pag-nav" aria-label="Pagination">
        <ul class="pagination mb-0 flex-wrap justify-content-end align-items-center">
          @if ($records->onFirstPage())
            <li class="page-item disabled" aria-disabled="true"><span class="page-link">&lsaquo;</span></li>
          @else
            <li class="page-item"><a class="page-link" href="{{ $records->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
          @endif

          @foreach ($records->getUrlRange(1, $records->lastPage()) as $page => $url)
            @if ($page == $records->currentPage())
              <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
            @else
              <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
            @endif
          @endforeach

          @if ($records->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $records->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
          @else
            <li class="page-item disabled" aria-disabled="true"><span class="page-link">&rsaquo;</span></li>
          @endif
        </ul>
      </nav>
    </div>
  @endif
</div>
