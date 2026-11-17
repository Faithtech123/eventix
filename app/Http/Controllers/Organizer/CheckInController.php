<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    public function show(Request $request, Event $event)
    {
        abort_unless($request->user()->owns($event), 403);

        $total = $event->tickets()->count();
        $checkedIn = $event->tickets()
            ->whereNotNull('checked_in_at')
            ->count();

        return view(
            'organizer.checkin',
            compact('event', 'total', 'checkedIn')
        );
    }

    // Called by fetch() from the check-in page, returns JSON
    public function verify(Request $request, Event $event)
    {
        abort_unless($request->user()->owns($event), 403);

        $request->validate([
            'code' => ['required', 'string', 'max:2048'],
        ]);

        $input = trim($request->input('code'));

        if (str_starts_with($input, Ticket::QR_PREFIX)) {
            try {
                $raw = Crypt::decryptString(
                    substr($input, strlen(Ticket::QR_PREFIX))
                );
            } catch (DecryptException) {
                return $this->reply('invalid', 'QR code could not be verified.');
            }
        } else {
            $raw = basename($input);
        }

        $code = Ticket::normalize($raw);

        if (strlen($code) !== 12) {
            return $this->reply('invalid', 'Ticket code not recognized.');
        }

        return DB::transaction(function () use ($event, $code, $request) {
            $ticket = Ticket::where('code', $code)
                ->lockForUpdate()
                ->with(['ticketType', 'order'])
                ->first();

            if (! $ticket) {
                return $this->reply('invalid', 'Ticket not found.');
            }

            if ($ticket->order->status !== 'paid') {
                return $this->reply('invalid', 'Payment is not confirmed.');
            }

            if ((int) $ticket->event_id !== (int) $event->id) {
                return $this->reply(
                    'invalid',
                    'This ticket is for a different event.'
                );
            }

            if ($ticket->isCheckedIn()) {
                $when = $ticket->checked_in_at->format('g:i A');

                return $this->reply(
                    'used',
                    "Already checked in at {$when}.",
                    $ticket
                );
            }

            $ticket->update([
                'checked_in_at' => now(),
                'checked_in_by' => $request->user()->id,
            ]);

            return $this->reply('ok', 'Welcome in!', $ticket);
        });
    }

    private function reply(
        string $status,
        string $message,
        ?Ticket $ticket = null
    ) {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'ticket' => $ticket ? [
                'code' => $ticket->formattedCode(),
                'holder' => $ticket->holder_name,
                'type' => $ticket->ticketType->name,
            ] : null,
        ]);
    }
}
