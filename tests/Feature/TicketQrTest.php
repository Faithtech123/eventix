<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TicketQrTest extends TestCase
{
    use RefreshDatabase;

    public function test_encrypted_ticket_qr_can_be_checked_in_only_once(): void
    {
        [$organizer, $event, $ticket] = $this->makeTicket();
        $payload = $ticket->qrPayload();

        $this->assertStringStartsWith(Ticket::QR_PREFIX, $payload);
        $this->assertSame(
            $ticket->code,
            Crypt::decryptString(substr($payload, strlen(Ticket::QR_PREFIX)))
        );

        $this->actingAs($organizer)
            ->postJson(route('organizer.checkin.verify', $event), [
                'code' => $payload,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'ok');

        $this->postJson(route('organizer.checkin.verify', $event), [
            'code' => $payload,
        ])
            ->assertOk()
            ->assertJsonPath('status', 'used');
    }

    public function test_tampered_ticket_qr_is_rejected(): void
    {
        [$organizer, $event] = $this->makeTicket();

        $this->actingAs($organizer)
            ->postJson(route('organizer.checkin.verify', $event), [
                'code' => Ticket::QR_PREFIX.'not-a-valid-encrypted-ticket',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'invalid');
    }

    public function test_event_organizer_can_view_ticket_holders_and_passes(): void
    {
        [$organizer, $event, $ticket, $attendee] = $this->makeTicket();

        $this->actingAs($organizer)
            ->get(route('organizer.events.attendees', $event))
            ->assertOk()
            ->assertSee($ticket->holder_name)
            ->assertSee($attendee->email)
            ->assertSee('Digital pass');

        $this->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Civic Hall')
            ->assertSee('data-qr="EVX1:', false)
            ->assertSee($ticket->formattedCode());
    }

    private function makeTicket(): array
    {
        $organizer = User::create([
            'name' => 'Event Organizer',
            'email' => 'organizer@example.test',
            'password' => 'password',
            'role' => 'organizer',
        ]);
        $attendee = User::create([
            'name' => 'Mara Okafor',
            'email' => 'mara@example.test',
            'password' => 'password',
            'role' => 'attendee',
        ]);

        $event = $organizer->events()->create([
            'title' => 'Secure Ticket Showcase',
            'category' => 'Tech',
            'description' => 'A test event.',
            'venue' => 'Civic Hall',
            'city' => 'Lagos',
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHours(4),
            'status' => 'published',
        ]);

        $ticketType = $event->ticketTypes()->create([
            'name' => 'General',
            'price' => 5000,
            'quantity' => 100,
            'max_per_order' => 10,
        ]);

        $order = Order::create([
            'reference' => Order::newReference(),
            'user_id' => $attendee->id,
            'event_id' => $event->id,
            'buyer_name' => $attendee->name,
            'buyer_email' => $attendee->email,
            'total' => 5000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket = Ticket::create([
            'order_id' => $order->id,
            'ticket_type_id' => $ticketType->id,
            'event_id' => $event->id,
            'code' => Ticket::newCode(),
            'holder_name' => $attendee->name,
        ]);

        return [$organizer, $event, $ticket, $attendee];
    }
}
