@extends('layouts.app')

@section('title', 'Discover events')

@section('content')
<section class="hero">
    <div class="container">
        <span class="eyebrow">EVENTIX · LAGOS</span>
        <h1>Find your next <em>memorable</em> moment.</h1>
        <p>Discover thoughtfully curated concerts, conversations and
            experiences. Secure your place and arrive with confidence.</p>

        <form method="GET" action="{{ route('home') }}" class="search">
            <input type="text" name="q" value="{{ $search }}"
                placeholder="Search by event, venue or city">
            <select name="category">
                <option value="">All categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}"
                        @selected($category === $cat)>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn">Search</button>
        </form>
    </div>
</section>

<section class="container section">
    <div class="section-head">
        <h2>Upcoming events</h2>
        <span class="muted">{{ $events->total() }} found</span>
    </div>

    @if ($events->isEmpty())
        <div class="empty">
            <p>No events match that search yet.</p>
            <a href="{{ route('home') }}" class="btn btn-ghost">
                Clear filters
            </a>
        </div>
    @else
        <div class="grid">
            @foreach ($events as $event)
                @include('partials.event-card', ['event' => $event])
            @endforeach
        </div>

        <div class="pager">
            {{ $events->links('partials.pagination') }}
        </div>
    @endif
</section>
@endsection
