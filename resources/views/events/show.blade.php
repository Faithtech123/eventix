@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="event-banner"
    style="background-image: url('{{ $event->bannerOrPlaceholder() }}')">
</div>

<section class="container event-layout">
    <div class="event-info">
        <span class="tag">{{ $event->category }}</span>

        @if ($event->status !== 'published')
            <span class="tag tag-warn">{{ ucfirst($event->status) }}</span>
        @endif

        <h1>{{ $event->title }}</h1>

        <ul class="meta">
            <li>
                <strong>When</strong>
                {{ $event->starts_at->format('l, j F Y · g:i A') }}
                @if ($event->ends_at)
                    &ndash; {{ $event->ends_at->format('g:i A') }}
                @endif
            </li>
            <li>
                <strong>Where</strong>
                {{ $event->venue }}, {{ $event->city }}
            </li>
            <li>
                <strong>Organizer</strong>
                {{ $event->organizer->name }}
            </li>
        </ul>

        <h2>About this event</h2>
        <div class="prose">{!! nl2br(e($event->description)) !!}</div>
    </div>

    <aside class="card checkout-box">
        <h2>Tickets</h2>

        @if (! $event->isOnSale())
            <p class="muted">Ticket sales are closed for this event.</p>
        @elseif ($event->ticketTypes->isEmpty())
            <p class="muted">No tickets have been added yet.</p>
        @else
            @guest
                <p class="muted small">
                    <a href="{{ route('login') }}">Log in</a> or
                    <a href="{{ route('register') }}">create an account</a>
                    to buy tickets.
                </p>
            @endguest

            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST"
                action="{{ route('checkout.store', $event) }}"
                data-checkout>
                @csrf

                @foreach ($event->ticketTypes as $type)
                    <div class="ticket-row">
                        <div>
                            <strong>{{ $type->name }}</strong>
                            <div class="muted small">
                                @if ($type->isSoldOut())
                                    Sold out
                                @else
                                    {{ $type->remaining() }} left
                                @endif
                            </div>
                        </div>

                        <div class="ticket-right">
                            <span class="price">
                                {{ $type->isFree()
                                    ? 'Free'
                                    : '₦' . number_format($type->price) }}
                            </span>

                            <select name="qty[{{ $type->id }}]"
                                data-price="{{ $type->price }}"
                                @disabled($type->isSoldOut())>
                                @php
                                    $max = min(
                                        $type->max_per_order,
                                        $type->remaining()
                                    );
                                @endphp
                                @for ($i = 0; $i <= $max; $i++)
                                    <option value="{{ $i }}"
                                        @selected(old("qty.{$type->id}") == $i)>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                @endforeach

                @auth
                    <label>Name on tickets
                        <input type="text" name="buyer_name" required
                            value="{{ old('buyer_name', auth()->user()->name) }}">
                    </label>
                    <label>Email
                        <input type="email" name="buyer_email" required
                            value="{{ old('buyer_email', auth()->user()->email) }}">
                    </label>
                @endauth

                <div class="total-row">
                    <span>Total</span>
                    <strong data-total>₦0</strong>
                </div>

                @auth
                    <button type="submit" class="btn btn-block"
                        data-checkout-btn disabled>
                        Get tickets
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-block">
                        Log in to continue
                    </a>
                @endauth
            </form>
        @endif
    </aside>
</section>
@endsection
