<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@eventix.test'],
            [
                'name' => 'Site Admin',
                'role' => 'admin',
                'password' => 'password',
            ]
        );

        $organizer = User::firstOrCreate(
            ['email' => 'organizer@eventix.test'],
            [
                'name' => 'Lagos Live Events',
                'role' => 'organizer',
                'password' => 'password',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@eventix.test'],
            [
                'name' => 'Test Attendee',
                'role' => 'attendee',
                'password' => 'password',
            ]
        );

        $samples = [
            [
                'title' => 'Lagos Tech Summit 2026',
                'category' => 'Tech',
                'venue' => 'Landmark Event Centre',
                'city' => 'Lagos',
                'days' => 14,
                'tickets' => [
                    ['Regular', 5000, 300],
                    ['VIP', 25000, 50],
                ],
            ],
            [
                'title' => 'Afrobeats Night Live',
                'category' => 'Music',
                'venue' => 'Eko Convention Centre',
                'city' => 'Lagos',
                'days' => 21,
                'tickets' => [
                    ['Early Bird', 10000, 200],
                    ['Regular', 15000, 500],
                    ['Table for 6', 300000, 20],
                ],
            ],
            [
                'title' => 'Startup Founders Meetup',
                'category' => 'Business',
                'venue' => 'Co-Creation Hub, Yaba',
                'city' => 'Lagos',
                'days' => 7,
                'tickets' => [
                    ['Free Pass', 0, 100],
                ],
            ],
            [
                'title' => 'Lagos Design and Culture Week',
                'category' => 'Education',
                'venue' => 'MUSON Centre',
                'city' => 'Lagos',
                'days' => 17,
                'description' => 'A week of ideas, craft and conversation with the people shaping contemporary African design.',
                'tickets' => [
                    ['Day Pass', 7500, 180],
                    ['All-Access Pass', 18000, 60],
                ],
            ],
            [
                'title' => 'Lagos Food and Drink Festival',
                'category' => 'General',
                'venue' => 'Tafawa Balewa Square',
                'city' => 'Lagos',
                'days' => 24,
                'description' => 'Explore standout local kitchens, live demonstrations and a carefully selected collection of drinks.',
                'tickets' => [
                    ['General Entry', 5000, 350],
                    ['Tasting Pass', 15000, 120],
                ],
            ],
            [
                'title' => 'Women in Product Summit',
                'category' => 'Business',
                'venue' => 'Landmark Centre',
                'city' => 'Lagos',
                'days' => 31,
                'description' => 'A practical day of product leadership, career stories and thoughtful networking.',
                'tickets' => [
                    ['Summit Pass', 8000, 250],
                    ['Executive Pass', 22000, 75],
                ],
            ],
            [
                'title' => 'Sunset Jazz by the Lagoon',
                'category' => 'Music',
                'venue' => 'Terra Kulture',
                'city' => 'Lagos',
                'days' => 38,
                'description' => 'An intimate evening of live jazz, exceptional musicians and Lagos at golden hour.',
                'tickets' => [
                    ['Lawn Seat', 10000, 220],
                    ['Front Row', 25000, 60],
                ],
            ],
            [
                'title' => 'Lagos City Run',
                'category' => 'Sports',
                'venue' => 'Eko Atlantic City',
                'city' => 'Lagos',
                'days' => 45,
                'description' => 'Choose your distance, join the city on the move and finish together by the waterfront.',
                'tickets' => [
                    ['5K Entry', 6000, 500],
                    ['10K Entry', 9000, 300],
                ],
            ],
            [
                'title' => 'The Grand Comedy Social',
                'category' => 'Comedy',
                'venue' => 'Federal Palace Hotel',
                'city' => 'Lagos',
                'days' => 52,
                'description' => 'A polished night out with leading stand-up acts, great company and a little friendly mischief.',
                'tickets' => [
                    ['Standard Seat', 12000, 240],
                    ['Premium Seat', 28000, 80],
                ],
            ],
        ];

        foreach ($samples as $s) {
            $start = now()->addDays($s['days'])->setTime(16, 0);

            $event = $organizer->events()->firstOrCreate(
                ['slug' => Str::slug($s['title'])],
                [
                    'title' => $s['title'],
                    'category' => $s['category'],
                    'description' => $s['description']
                        ?? "Join us for {$s['title']} at {$s['venue']}. "
                            .'Expect a memorable experience from start to finish.',
                    'venue' => $s['venue'],
                    'city' => $s['city'],
                    'starts_at' => $start,
                    'ends_at' => $start->copy()->addHours(5),
                    'status' => 'published',
                ]
            );

            foreach ($s['tickets'] as [$name, $price, $qty]) {
                $event->ticketTypes()->firstOrCreate(
                    ['name' => $name],
                    [
                        'price' => $price,
                        'quantity' => $qty,
                        'max_per_order' => 10,
                    ]
                );
            }
        }
    }
}
