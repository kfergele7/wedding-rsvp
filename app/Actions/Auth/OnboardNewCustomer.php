<?php

namespace App\Actions\Auth;

use App\Models\Account;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardNewCustomer
{
    public function execute(User $user): Site
    {
        return DB::transaction(function () use ($user) {
            $nameBase = trim((string) $user->name) !== '' ? $user->name : 'Wedding Account';
            $accountSlug = $this->uniqueAccountSlug(Str::slug($nameBase) ?: 'wedding-account');

            $account = Account::query()->create([
                'name' => $nameBase,
                'slug' => $accountSlug,
                'status' => Account::STATUS_DRAFT,
            ]);

            $siteSlug = $this->uniqueSiteSlug();

            $site = Site::query()->create([
                'account_id' => $account->id,
                'title' => $nameBase."'s Wedding Site",
                'public_slug' => $siteSlug,
                'is_published' => false,
            ]);

            $user->forceFill([
                'account_id' => $account->id,
                'role' => 'owner',
            ])->save();

            SiteSetting::query()->create([
                'site_id' => $site->id,
                'key' => 'homepage_content',
                'value' => config('wedding.homepage_content'),
            ]);

            SiteSetting::query()->create([
                'site_id' => $site->id,
                'key' => 'rsvp_settings',
                'value' => config('wedding.rsvp_settings'),
            ]);

            return $site;
        });
    }

    private function uniqueAccountSlug(string $base): string
    {
        $slug = $base;
        $counter = 1;

        while (Account::query()->where('slug', $slug)->exists()) {
            $counter++;
            $slug = $base.'-'.$counter;
        }

        return $slug;
    }

    private function uniqueSiteSlug(): string
    {
        do {
            // Public path-safe token (not derived from customer/couple names).
            $slug = 'w-'.Str::lower(Str::random(10));
        } while (Site::query()->where('public_slug', $slug)->exists());

        return $slug;
    }
}
