<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'events' => Event::count(),
            'tickets' => Ticket::count(),
            'revenue' => Order::where('status', 'paid')->sum('total'),
        ];

        $users = User::latest()->paginate(15);

        $recentOrders = Order::with('event')
            ->latest()
            ->take(8)
            ->get();

        return view(
            'admin.dashboard',
            compact('stats', 'users', 'recentOrders')
        );
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        if ((int) $user->id === (int) $request->user()->id) {
            return back()->with('error', "You can't change your own role.");
        }

        $user->update(['role' => $request->input('role')]);

        return back()->with('success', "{$user->name} is now {$user->role}.");
    }
}
