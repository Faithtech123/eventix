<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManageEventController extends Controller
{
    public function create()
    {
        return view('organizer.events.form', [
            'event' => new Event(['status' => 'draft']),
            'ticketTypes' => collect(),
            'categories' => Event::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateEvent($request);

        $event = DB::transaction(function () use ($request, $data) {
            $event = $request->user()->events()->create($data);
            $this->syncTicketTypes($event, $request->input('tickets', []));

            return $event;
        });

        return redirect()
            ->route('organizer.dashboard')
            ->with('success', "\"{$event->title}\" has been created.");
    }

    public function edit(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);

        return view('organizer.events.form', [
            'event' => $event,
            'ticketTypes' => $event->ticketTypes,
            'categories' => Event::CATEGORIES,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);

        $data = $this->validateEvent($request);

        DB::transaction(function () use ($request, $event, $data) {
            $event->update($data);
            $this->syncTicketTypes($event, $request->input('tickets', []));
        });

        return redirect()
            ->route('organizer.dashboard')
            ->with('success', 'Event updated.');
    }

    public function destroy(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);

        if ($event->tickets()->exists()) {
            // Don't wipe sold tickets; cancel instead
            $event->update(['status' => 'cancelled']);

            return back()->with(
                'success',
                'Tickets were already sold, so the event was cancelled instead of deleted.'
            );
        }

        $event->delete();

        return back()->with('success', 'Event deleted.');
    }

    public function attendees(Request $request, Event $event)
    {
        $this->authorizeOwner($request, $event);

        $tickets = $event->tickets()
            ->whereHas('order', fn ($query) => $query->where('status', 'paid'))
            ->with(['ticketType', 'order'])
            ->latest()
            ->get();

        $byType = $event->ticketTypes()->get();

        return view(
            'organizer.events.attendees',
            compact('event', 'tickets', 'byType')
        );
    }

    private function authorizeOwner(Request $request, Event $event): void
    {
        abort_unless($request->user()->owns($event), 403);
    }

    private function validateEvent(Request $request): array
    {
        $request->validate([
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*.id' => ['nullable', 'integer'],
            'tickets.*.name' => ['required', 'string', 'max:60'],
            'tickets.*.price' => ['required', 'numeric', 'min:0'],
            'tickets.*.quantity' => ['required', 'integer', 'min:1'],
            'tickets.*.max_per_order' => [
                'nullable', 'integer', 'min:1', 'max:20',
            ],
        ], [
            'tickets.required' => 'Add at least one ticket type.',
        ]);

        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::in(Event::CATEGORIES)],
            'description' => ['required', 'string'],
            'venue' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:80'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'banner_url' => ['nullable', 'url', 'max:500'],
            'status' => [
                'required',
                Rule::in(['draft', 'published', 'cancelled']),
            ],
        ]);
    }

    private function syncTicketTypes(Event $event, array $rows): void
    {
        $keptIds = [];

        foreach ($rows as $row) {
            $values = [
                'name' => $row['name'],
                'price' => $row['price'],
                'quantity' => (int) $row['quantity'],
                'max_per_order' => (int) ($row['max_per_order'] ?? 10),
            ];

            $existing = empty($row['id'])
                ? null
                : $event->ticketTypes()->find($row['id']);

            if ($existing) {
                if ($values['quantity'] < $existing->sold) {
                    throw ValidationException::withMessages([
                        'tickets' => "{$existing->name}: quantity can't be lower than the {$existing->sold} already sold.",
                    ]);
                }

                $existing->update($values);
                $keptIds[] = $existing->id;
            } else {
                $keptIds[] = $event->ticketTypes()->create($values)->id;
            }
        }

        // Remove rows the organizer deleted, but only if unsold
        $event->ticketTypes()
            ->whereNotIn('id', $keptIds)
            ->where('sold', 0)
            ->delete();
    }
}
