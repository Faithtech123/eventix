<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->whereHas('order', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->with(['event', 'ticketType'])
            ->latest()
            ->get()
            ->groupBy('event_id');

        return view('tickets.index', compact('tickets'));
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['event', 'ticketType', 'order']);
        $user = $request->user();

        $allowed = (int) $ticket->order->user_id === (int) $user->id
            || $user->owns($ticket->event);

        abort_unless($allowed, 403);

        return view('tickets.show', [
            'ticket' => $ticket,
            'qrPayload' => $ticket->qrPayload(),
        ]);
    }
}
