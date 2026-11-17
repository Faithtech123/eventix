<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(Request $request, Event $event)
    {
        abort_unless($event->isOnSale(), 404);

        $request->validate([
            'qty' => ['required', 'array'],
            'qty.*' => ['integer', 'min:0', 'max:20'],
            'buyer_name' => ['required', 'string', 'max:100'],
            'buyer_email' => ['required', 'email', 'max:150'],
        ]);

        // keep only rows where something was picked
        $wanted = collect($request->input('qty'))
            ->map(fn ($n) => (int) $n)
            ->filter(fn ($n) => $n > 0);

        if ($wanted->isEmpty()) {
            throw ValidationException::withMessages([
                'qty' => 'Pick at least one ticket.',
            ]);
        }

        $user = $request->user();

        $order = DB::transaction(function () use (
            $wanted, $event, $user, $request
        ) {
            // Lock the rows so two buyers can't grab the last seat
            $types = TicketType::where('event_id', $event->id)
                ->whereIn('id', $wanted->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;

            foreach ($wanted as $typeId => $count) {
                $type = $types->get($typeId);

                if (! $type) {
                    throw ValidationException::withMessages([
                        'qty' => 'One of the tickets is no longer available.',
                    ]);
                }

                if ($count > $type->max_per_order) {
                    throw ValidationException::withMessages([
                        'qty' => "Max {$type->max_per_order} {$type->name} tickets per order.",
                    ]);
                }

                if ($count > $type->remaining()) {
                    throw ValidationException::withMessages([
                        'qty' => "Only {$type->remaining()} {$type->name} tickets left.",
                    ]);
                }

                $total += $count * (float) $type->price;
            }

            $order = Order::create([
                'reference' => Order::newReference(),
                'user_id' => $user->id,
                'event_id' => $event->id,
                'buyer_name' => $request->input('buyer_name'),
                'buyer_email' => $request->input('buyer_email'),
                'total' => $total,
                // Payment is simulated for now (see README)
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            foreach ($wanted as $typeId => $count) {
                $type = $types->get($typeId);

                for ($i = 0; $i < $count; $i++) {
                    Ticket::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $type->id,
                        'event_id' => $event->id,
                        'code' => Ticket::newCode(),
                        'holder_name' => $order->buyer_name,
                    ]);
                }

                $type->increment('sold', $count);
            }

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order confirmed. Your tickets are ready.');
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless(
            (int) $order->user_id === (int) $user->id || $user->isAdmin(),
            403
        );

        $order->load(['event', 'tickets.ticketType']);

        return view('orders.show', compact('order'));
    }
}
