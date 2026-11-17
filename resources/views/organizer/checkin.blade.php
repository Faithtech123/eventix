@extends('layouts.app')

@section('title', 'Check-in · ' . $event->title)

@section('content')
<section class="container section narrow">
    <a href="{{ route('organizer.dashboard') }}" class="muted small">
        &larr; Dashboard
    </a>
    <h1>Check-in</h1>
    <p class="muted">
        {{ $event->title }} · Encrypted digital passes are verified here.
    </p>

    <div class="card checkin-count">
        <span>Checked in</span>
        <strong>
            <span data-count>{{ $checkedIn }}</span> / {{ $total }}
        </strong>
    </div>

    <div class="card form-card">
        <div id="reader" class="reader" hidden></div>

        <button type="button" class="btn btn-ghost btn-block"
            data-start-scan>
            Scan QR with camera
        </button>

        <form data-checkin-form
            action="{{ route('organizer.checkin.verify', $event) }}">
            <label>Or type the ticket code
                <input type="text" name="code" class="mono"
                    placeholder="XXXX-XXXX-XXXX" autocomplete="off"
                    required>
            </label>
            <button type="submit" class="btn btn-block">Verify</button>
        </form>

        <div class="checkin-result" data-result hidden></div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js">
</script>
<script src="{{ asset('js/checkin.js') }}"></script>
@endpush
