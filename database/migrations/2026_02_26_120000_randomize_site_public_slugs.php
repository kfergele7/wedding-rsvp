<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $siteIds = DB::table('sites')->orderBy('id')->pluck('id');

        foreach ($siteIds as $siteId) {
            do {
                $slug = 'w-'.Str::lower(Str::random(10));
            } while (DB::table('sites')->where('public_slug', $slug)->exists());

            DB::table('sites')->where('id', $siteId)->update([
                'public_slug' => $slug,
            ]);
        }
    }

    public function down(): void
    {
        // Slug values are intentionally not reversed.
    }
};
