@extends('layouts.app')

@section('title', 'Create account')

@section('content')
<section class="auth">
    <div class="card auth-card">
        <h1>Create your account</h1>
        <p class="muted">Buy tickets or start selling your own.</p>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="role-pick">
                <label>
                    <input type="radio" name="role" value="attendee"
                        @checked(old('role', 'attendee') === 'attendee')>
                    <span>I want to attend events</span>
                </label>
                <label>
                    <input type="radio" name="role" value="organizer"
                        @checked(old('role') === 'organizer')>
                    <span>I organize events</span>
                </label>
            </div>

            <label>Full name
                <input type="text" name="name"
                    value="{{ old('name') }}" required>
            </label>

            <label>Email
                <input type="email" name="email"
                    value="{{ old('email') }}" required>
            </label>

            <label>Phone (optional)
                <input type="text" name="phone"
                    value="{{ old('phone') }}">
            </label>

            <div class="row-2">
                <label>Password
                    <input type="password" name="password" required>
                </label>
                <label>Confirm password
                    <input type="password"
                        name="password_confirmation" required>
                </label>
            </div>

            <button type="submit" class="btn btn-block">
                Create account
            </button>
        </form>

        <p class="muted small center">
            Already have an account?
            <a href="{{ route('login') }}">Log in</a>
        </p>
    </div>
</section>
@endsection
