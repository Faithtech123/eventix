@extends('layouts.app')

@section('title', 'Digital pass · ' . $ticket->event->title)

@section('content')
<section class="container section narrow-wide">
    <div class="ticket">
        <div class="ticket-top">
            <div class="ticket-brandline">
                <img src="{{ asset('images/eventix-mark.svg') }}" alt="">
                <span>EVENTIX <i>·</i> DIGITAL ADMISSION</span>
            </div>
            <div class="ticket-title-row">
                <div>
                    <span class="tag">{{ $ticket->ticketType->name }}</span>
                    <h1>{{ $ticket->event->title }}</h1>
                </div>
                <span class="status status-{{ $ticket->order->status }}">
                    {{ $ticket->order->status === 'paid'
                        ? 'Paid · Confirmed'
                        : ucfirst($ticket->order->status) }}
                </span>
            </div>
            <p class="ticket-date">
                {{ $ticket->event->starts_at->format('l, j F Y') }}
                &nbsp;·&nbsp;
                {{ $ticket->event->starts_at->format('g:i A') }}
                @if ($ticket->event->ends_at)
                    – {{ $ticket->event->ends_at->format('g:i A') }}
                @endif
            </p>
        </div>

        <div class="ticket-cut"></div>

        <div class="ticket-bottom">
            <div class="ticket-facts">
                <div>
                    <span>Admit</span>
                    <strong>{{ $ticket->holder_name }}</strong>
                </div>
                <div>
                    <span>Venue</span>
                    <strong>{{ $ticket->event->venue }}</strong>
                    <small>{{ $ticket->event->city }}</small>
                </div>
                <div>
                    <span>Ticket type</span>
                    <strong>{{ $ticket->ticketType->name }}</strong>
                    <small>
                        {{ (float) $ticket->ticketType->price === 0.0
                            ? 'Complimentary'
                            : '₦' . number_format($ticket->ticketType->price, 2) }}
                    </small>
                </div>
                <div>
                    <span>Order reference</span>
                    <strong class="mono">{{ $ticket->order->reference }}</strong>
                    @if ($ticket->order->paid_at)
                        <small>
                            Purchased {{ $ticket->order->paid_at->format('j M Y') }}
                        </small>
                    @endif
                </div>
            </div>

            <div id="qr" class="qr"
                data-qr="{{ $qrPayload }}"
                aria-label="Encrypted admission QR code"></div>

            <p class="mono code-big">{{ $ticket->formattedCode() }}</p>
            <p class="ticket-instruction">
                Present this pass at the entrance. Your QR code is encrypted
                and can only be checked in once.
            </p>

            @if ($ticket->isCheckedIn())
                <span class="status status-used">
                    Checked in {{ $ticket->checked_in_at->format('j M, g:i A') }}
                </span>
            @else
                <span class="status status-paid">Valid</span>
            @endif
        </div>
    </div>

    <div class="ticket-actions">
        <button type="button" class="btn" onclick="window.print()">
            Print or save pass
        </button>
        <a href="{{ route('tickets.index') }}" class="btn btn-ghost">
            My tickets
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js">
</script>
<script>
    (function () {
        var box = document.getElementById('qr');
        if (!box || typeof QRCode === 'undefined') return;

        new QRCode(box, {
            text: box.dataset.qr,
            width: 200,
            height: 200,
            correctLevel: QRCode.CorrectLevel.M
        });
    })();
</script>
@endpush
