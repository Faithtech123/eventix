@extends('layouts.app')

@php
    $editing = $event->exists;

    // After a failed submit, rebuild rows from old input
    $rows = old('tickets');

    if ($rows === null) {
        $rows = $ticketTypes->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'price' => $t->price,
            'quantity' => $t->quantity,
            'max_per_order' => $t->max_per_order,
            'sold' => $t->sold,
        ])->all();
    }

    if (empty($rows)) {
        $rows = [[
            'name' => 'Regular',
            'price' => 0,
            'quantity' => 100,
            'max_per_order' => 10,
        ]];
    }
@endphp

@section('title', $editing ? 'Edit event' : 'New event')

@section('content')
<section class="container section narrow-wide">
    <h1>{{ $editing ? 'Edit event' : 'Create an event' }}</h1>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" class="card form-card"
        action="{{ $editing
            ? route('organizer.events.update', $event)
            : route('organizer.events.store') }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <h2>Details</h2>

        <label>Event title
            <input type="text" name="title" required
                value="{{ old('title', $event->title) }}">
        </label>

        <div class="row-2">
            <label>Category
                <select name="category">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}"
                            @selected(old('category', $event->category) === $cat)>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>Status
                <select name="status">
                    @foreach (['draft', 'published', 'cancelled'] as $s)
                        <option value="{{ $s }}"
                            @selected(old('status', $event->status) === $s)>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </label>
        </div>

        <label>Description
            <textarea name="description" rows="6" required>{{
                old('description', $event->description)
            }}</textarea>
        </label>

        <div class="row-2">
            <label>Venue
                <input type="text" name="venue" required
                    value="{{ old('venue', $event->venue) }}">
            </label>
            <label>City
                <input type="text" name="city" required
                    value="{{ old('city', $event->city) }}">
            </label>
        </div>

        <div class="row-2">
            <label>Starts
                <input type="datetime-local" name="starts_at" required
                    value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}">
            </label>
            <label>Ends (optional)
                <input type="datetime-local" name="ends_at"
                    value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}">
            </label>
        </div>

        <label>Banner image URL (optional)
            <input type="url" name="banner_url"
                placeholder="https://..."
                value="{{ old('banner_url', $event->banner_url) }}">
            <span class="hint">
                Paste a link to an image (e.g. from Cloudinary or Imgur).
            </span>
        </label>

        <div class="section-head">
            <h2>Ticket types</h2>
            <button type="button" class="btn btn-ghost btn-sm"
                data-add-ticket>+ Add ticket type</button>
        </div>

        <div data-ticket-rows>
            @foreach ($rows as $i => $row)
                @include('organizer.events.ticket-row', [
                    'i' => $i,
                    'row' => $row,
                ])
            @endforeach
        </div>

        {{-- Blank row the JS clones when "Add ticket type" is clicked --}}
        <template data-ticket-template>
            @include('organizer.events.ticket-row', [
                'i' => '__INDEX__',
                'row' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 100,
                    'max_per_order' => 10,
                ],
            ])
        </template>

        <div class="form-actions">
            <a href="{{ route('organizer.dashboard') }}" class="btn btn-ghost">
                Cancel
            </a>
            <button type="submit" class="btn">
                {{ $editing ? 'Save changes' : 'Create event' }}
            </button>
        </div>
    </form>
</section>
@endsection
