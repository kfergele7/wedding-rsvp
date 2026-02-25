<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Party;
use App\Models\Rsvp;
use App\Models\Site;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class WeddingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $account = Account::query()->first()
            ?? Account::query()->create([
                'name' => 'Default Account',
                'slug' => 'default-account',
                'status' => Account::STATUS_ACTIVE,
            ]);

        $site = Site::query()->orderBy('id')->first()
            ?? Site::query()->create([
                'account_id' => $account->id,
                'title' => 'Default Wedding Site',
                'public_slug' => 'default-wedding-site',
                'is_published' => true,
            ]);

        SiteSetting::query()->firstOrCreate(
            ['site_id' => $site->id, 'key' => 'homepage_content'],
            ['value' => config('wedding.homepage_content')]
        );
        SiteSetting::query()->firstOrCreate(
            ['site_id' => $site->id, 'key' => 'rsvp_settings'],
            ['value' => config('wedding.rsvp_settings')]
        );

        $parties = [
            [
                'display_name' => 'The Carter Family',
                'code' => 'CARTER',
                'max_guests' => 4,
                'notes' => 'Family table near dance floor',
                'guests' => [
                    ['first_name' => 'James', 'last_name' => 'Carter', 'is_child' => false],
                    ['first_name' => 'Elena', 'last_name' => 'Carter', 'is_child' => false],
                    ['first_name' => 'Liam', 'last_name' => 'Carter', 'is_child' => true],
                ],
                'rsvp' => [
                    'status' => Rsvp::STATUS_ATTENDING,
                    'attending_count' => 3,
                    'meal_choices' => [
                        ['guest_name' => 'James Carter', 'meal' => 'Beef'],
                        ['guest_name' => 'Elena Carter', 'meal' => 'Chicken'],
                        ['guest_name' => 'Liam Carter', 'meal' => 'Veg'],
                    ],
                    'dietary_restrictions' => 'Nut allergy for Liam',
                    'message' => 'Can’t wait to celebrate with you!',
                ],
            ],
            [
                'display_name' => 'Amelia Brooks + Guest',
                'code' => 'BROOKS',
                'max_guests' => 2,
                'notes' => null,
                'guests' => [
                    ['first_name' => 'Amelia', 'last_name' => 'Brooks', 'is_child' => false],
                    ['first_name' => 'Noah', 'last_name' => 'Hale', 'is_child' => false],
                ],
                'rsvp' => [
                    'status' => Rsvp::STATUS_NOT_ATTENDING,
                    'attending_count' => 0,
                    'meal_choices' => [],
                    'dietary_restrictions' => null,
                    'message' => 'Sending love from afar.',
                ],
            ],
            [
                'display_name' => 'Grandma Rose',
                'code' => 'ROSE',
                'max_guests' => 1,
                'notes' => 'Needs easy-access seating',
                'guests' => [
                    ['first_name' => 'Rose', 'last_name' => 'Morgan', 'is_child' => false],
                ],
                'rsvp' => null,
            ],
        ];

        foreach ($parties as $entry) {
            $party = Party::query()->updateOrCreate(
                ['site_id' => $site->id, 'display_name' => $entry['display_name']],
                [
                    'site_id' => $site->id,
                    'code' => $entry['code'],
                    'display_name' => $entry['display_name'],
                    'max_guests' => $entry['max_guests'],
                    'notes' => $entry['notes'],
                ]
            );

            foreach ($entry['guests'] as $guest) {
                $party->guests()->updateOrCreate(
                    [
                        'site_id' => $site->id,
                        'first_name' => $guest['first_name'],
                        'last_name' => $guest['last_name'],
                    ],
                    ['is_child' => $guest['is_child']]
                );
            }

            if ($entry['rsvp']) {
                $party->rsvp()->updateOrCreate(
                    ['party_id' => $party->id, 'site_id' => $site->id],
                    ['site_id' => $site->id, ...$entry['rsvp']]
                );
            }
        }
    }
}
