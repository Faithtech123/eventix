@extends('layouts.app')

@section('title', 'Attendees · ' . $event->title)

@section('content')
<section class="container section">
    <div class="section-head">
        <div>
            <a href="{{ route('organizer.dashboard') }}" class="muted small">
                &larr; Dashboard
            </a>
            <h1>{{ $event->title }}</h1>
            <p class="muted">Ticket holders and confirmed purchases</p>
        </div>
        <a href="{{ route('organizer.checkin.show', $event) }}" class="btn">
            Open check-in
        </a>
    </div>

    <div class="stats">
        @foreach ($byType as $type)
            <div class="card stat">
                <span>{{ $type->name }}</span>
                <strong>{{ $type->sold }} / {{ $type->quantity }}</strong>
            </div>
        @endforeach
    </div>

    <div class="card table-wrap">
        <input type="search" class="table-filter"
            placeholder="Filter by name, email or code"
            data-table-filter="#attendees">

        <table class="table" id="attendees">
            <thead>
                <tr>
                    <th>Holder</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Code</th>
                    <th>Order</th>
                    <th>Purchased</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->holder_name }}</td>
                        <td>{{ $ticket->order->buyer_email }}</td>
                        <td>{{ $ticket->ticketType->name }}</td>
                        <td class="mono">{{ $ticket->formattedCode() }}</td>
                        <td class="mono small">{{ $ticket->order->reference }}</td>
                        <td class="small">
                            {{ $ticket->order->paid_at?->format('j M Y, g:i A') ?? '—' }}
                        </td>
                        <td>
                            @if ($ticket->isCheckedIn())
                                <span class="status status-used">
                                    In · {{ $ticket->checked_in_at->format('g:i A') }}
                                </span>
                            @else
                                <span class="status status-paid">Not yet</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}"
                                class="btn btn-ghost btn-sm">
                                Digital pass
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="center muted">
                            No tickets sold yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
