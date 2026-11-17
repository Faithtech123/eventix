@php
    $from = $event->lowestPrice();
@endphp

<a href="{{ route('events.show', $event) }}" class="card event-card">
    <div class="event-img"
        style="background-image: url('{{ $event->bannerOrPlaceholder() }}')">
        <span class="tag">{{ $event->category }}</span>
    </div>

    <div class="event-body">
        <div class="date-chip">
            <strong>{{ $event->starts_at->format('d') }}</strong>
            <span>{{ $event->starts_at->format('M') }}</span>
        </div>

        <div>
            <h3>{{ $event->title }}</h3>
            <p class="muted">
                {{ $event->venue }}, {{ $event->city }}
            </p>
            <p class="price">
                @if ($from === null)
                    Tickets coming soon
                @elseif ($from == 0)
                    Free
                @else
                    From ₦{{ number_format($from) }}
                @endif
            </p>
        </div>
    </div>
</a>
