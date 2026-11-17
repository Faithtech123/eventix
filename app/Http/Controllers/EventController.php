<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $category = $request->query('category');

        $events = Event::published()
            ->upcoming()
            ->with('ticketTypes')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('venue', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->orderBy('starts_at')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', [
            'events' => $events,
            'search' => $search,
            'category' => $category,
            'categories' => Event::CATEGORIES,
        ]);
    }

    public function show(Request $request, Event $event)
    {
        $user = $request->user();

        // Drafts are only visible to the owner or an admin
        if ($event->status !== 'published') {
            abort_unless($user && $user->owns($event), 404);
        }

        $event->load(['ticketTypes', 'organizer']);

        return view('events.show', compact('event'));
    }
}
