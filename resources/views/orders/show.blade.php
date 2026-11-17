@extends('layouts.app')

@section('title', 'Order ' . $order->reference)

@section('content')
<section class="container section narrow">
    <div class="card order-card">
        <div class="order-head">
            <div>
                <span class="muted small">Order</span>
                <h1>{{ $order->reference }}</h1>
            </div>
            <span class="status status-{{ $order->status }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <p>
            <strong>{{ $order->event->title }}</strong><br>
            <span class="muted">
                {{ $order->event->starts_at->format('D, j M Y · g:i A') }}
                &middot; {{ $order->event->venue }}
            </span>
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Code</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->ticketType->name }}</td>
                        <td class="mono">{{ $ticket->formattedCode() }}</td>
                        <td class="right">
                            <a href="{{ route('tickets.show', $ticket) }}">
                                View QR
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-row">
            <span>Total paid</span>
            <strong>
                {{ (float) $order->total === 0.0
                    ? 'Free'
                    : '₦' . number_format($order->total, 2) }}
            </strong>
        </div>

        <a href="{{ route('tickets.index') }}" class="btn btn-block">
            Go to my tickets
        </a>
    </div>
</section>
@endsection
