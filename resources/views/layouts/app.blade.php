<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Eventix') · Eventix</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/eventix-mark.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('head')
</head>
<body>
    <header class="nav">
        <div class="container nav-inner">
            <a href="{{ route('home') }}" class="logo" aria-label="Eventix home">
                <img class="brand-mark" src="{{ asset('images/eventix-mark.svg') }}"
                    alt="">
                <span class="brand-lockup">
                    <span class="brand-name">EVENTIX</span>
                    <span class="brand-caption">Curated events · Seamless entry</span>
                </span>
            </a>

            <button class="nav-toggle" type="button"
                aria-label="Menu" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>

            <nav class="nav-links" data-nav>
                <a href="{{ route('home') }}">Events</a>

                @auth
                    <a href="{{ route('tickets.index') }}">My Tickets</a>

                    @if (auth()->user()->canManageEvents())
                        <a href="{{ route('organizer.dashboard') }}">
                            Organizer
                        </a>
                    @endif

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link-btn">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-sm">
                        Sign up
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div class="container">
                <div class="alert alert-success" data-dismiss>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container">
                <div class="alert alert-error" data-dismiss>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            &copy; {{ date('Y') }} Eventix. Exceptional events, seamlessly experienced.
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
