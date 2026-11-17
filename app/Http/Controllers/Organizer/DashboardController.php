<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $events = Event::query()
            ->when(! $user->isAdmin(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->withCount('tickets')
            ->withCount([
                'tickets as checked_in_count' => function ($q) {
                    $q->whereNotNull('checked_in_at');
                },
            ])
            ->withSum(
                ['orders as revenue' => function ($q) {
                    $q->where('status', 'paid');
                }],
                'total'
            )
            ->orderByDesc('starts_at')
            ->get();

        $stats = [
            'events' => $events->count(),
            'live' => $events->where('status', 'published')->count(),
            'tickets' => $events->sum('tickets_count'),
            'revenue' => $events->sum('revenue'),
        ];

        return view('organizer.dashboard', compact('events', 'stats'));
    }
}
