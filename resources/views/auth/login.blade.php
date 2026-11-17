@extends('layouts.app')

@section('title', 'Log in')

@section('content')
<section class="auth">
    <div class="card auth-card">
        <h1>Welcome back</h1>
        <p class="muted">Log in to see your tickets.</p>

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label>Email
                <input type="email" name="email"
                    value="{{ old('email') }}" required autofocus>
            </label>

            <label>Password
                <input type="password" name="password" required>
            </label>

            <label class="check">
                <input type="checkbox" name="remember" value="1">
                Keep me logged in
            </label>

            <button type="submit" class="btn btn-block">Log in</button>
        </form>

        <p class="muted small center">
            New here? <a href="{{ route('register') }}">Create an account</a>
        </p>
    </div>
</section>
@endsection
