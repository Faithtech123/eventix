@extends('layouts.app')

@section('title', 'My tickets')

@section('content')
<section class="container section">
    <div class="section-head">
        <h1>My tickets</h1>
    </div>

    @if ($tickets->isEmpty())
        <div class="empty">
            <p>You haven't bought any tickets yet.</p>
            <a href="{{ route('home') }}" class="btn">Browse events</a>
        </div>
    @else
        @foreach ($tickets as $group)
            @php $event = $group->first()->event; @endphp

            <div class="card ticket-group">
                <div class="ticket-group-head">
                    <div>
                        <h3>{{ $event->title }}</h3>
                        <p class="muted small">
                            {{ $event->starts_at->format('D, j M Y · g:i A') }}
                            &middot; {{ $event->venue }}
                        </p>
                    </div>
                    <span class="muted small">
                        {{ $group->count() }}
                        {{ Str::plural('ticket', $group->count()) }}
                    </span>
                </div>

                <div class="ticket-list">
                    @foreach ($group as $ticket)
                        <a href="{{ route('tickets.show', $ticket) }}"
                            class="ticket-pill">
                            <span>{{ $ticket->ticketType->name }}</span>
                            <span class="mono">
                                {{ $ticket->formattedCode() }}
                            </span>
                            @if ($ticket->isCheckedIn())
                                <span class="status status-used">Used</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</section>
@endsection
