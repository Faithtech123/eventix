@extends('layouts.app')

@section('title', 'Organizer dashboard')

@section('content')
<section class="container section">
    <div class="section-head">
        <h1>Your events</h1>
        <a href="{{ route('organizer.events.create') }}" class="btn">
            + New event
        </a>
    </div>

    <div class="stats">
        <div class="card stat">
            <span>Events</span>
            <strong>{{ $stats['events'] }}</strong>
        </div>
        <div class="card stat">
            <span>Live now</span>
            <strong>{{ $stats['live'] }}</strong>
        </div>
        <div class="card stat">
            <span>Tickets sold</span>
            <strong>{{ number_format($stats['tickets']) }}</strong>
        </div>
        <div class="card stat">
            <span>Revenue</span>
            <strong>₦{{ number_format($stats['revenue']) }}</strong>
        </div>
    </div>

    @if ($events->isEmpty())
        <div class="empty">
            <p>You haven't created any events yet.</p>
            <a href="{{ route('organizer.events.create') }}" class="btn">
                Create your first event
            </a>
        </div>
    @else
        <div class="card table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Sold</th>
                        <th>Checked in</th>
                        <th>Revenue</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td>
                                <a href="{{ route('events.show', $event) }}">
                                    {{ $event->title }}
                                </a>
                            </td>
                            <td>{{ $event->starts_at->format('j M Y') }}</td>
                            <td>
                                <span class="status status-{{ $event->status }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td>{{ $event->tickets_count }}</td>
                            <td>{{ $event->checked_in_count }}</td>
                            <td>₦{{ number_format($event->revenue ?? 0) }}</td>
                            <td class="actions">
                                <a href="{{ route('organizer.checkin.show', $event) }}">
                                    Check-in
                                </a>
                                <a href="{{ route('organizer.events.attendees', $event) }}">
                                    Attendees
                                </a>
                                <a href="{{ route('organizer.events.edit', $event) }}">
                                    Edit
                                </a>
                                <form method="POST"
                                    action="{{ route('organizer.events.destroy', $event) }}"
                                    data-confirm="Delete this event?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link-btn danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
