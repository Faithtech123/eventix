@if ($paginator->hasPages())
    <nav class="pagination">
        @if ($paginator->onFirstPage())
            <span class="disabled">&larr; Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">&larr; Prev</a>
        @endif

        <span class="muted">
            Page {{ $paginator->currentPage() }}
            of {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Next &rarr;</a>
        @else
            <span class="disabled">Next &rarr;</span>
        @endif
    </nav>
@endif
