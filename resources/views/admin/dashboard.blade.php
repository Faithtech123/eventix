@extends('layouts.app')

@section('title', 'Admin')

@section('content')
<section class="container section">
    <h1>Admin</h1>

    <div class="stats">
        <div class="card stat">
            <span>Users</span>
            <strong>{{ number_format($stats['users']) }}</strong>
        </div>
        <div class="card stat">
            <span>Events</span>
            <strong>{{ number_format($stats['events']) }}</strong>
        </div>
        <div class="card stat">
            <span>Tickets</span>
            <strong>{{ number_format($stats['tickets']) }}</strong>
        </div>
        <div class="card stat">
            <span>Revenue</span>
            <strong>₦{{ number_format($stats['revenue']) }}</strong>
        </div>
    </div>

    <div class="admin-grid">
        <div class="card table-wrap">
            <h2>Users</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <form method="POST"
                                    action="{{ route('admin.users.role', $user) }}"
                                    class="inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" data-autosubmit
                                        @disabled((int) $user->id === (int) auth()->id())>
                                        @foreach (\App\Models\User::ROLES as $role)
                                            <option value="{{ $role }}"
                                                @selected($user->role === $role)>
                                                {{ ucfirst($role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links('partials.pagination') }}
        </div>

        <div class="card table-wrap">
            <h2>Recent orders</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Event</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td class="mono small">
                                <a href="{{ route('orders.show', $order) }}">
                                    {{ $order->reference }}
                                </a>
                            </td>
                            <td>{{ $order->event->title }}</td>
                            <td>₦{{ number_format($order->total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="muted center">
                                No orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
