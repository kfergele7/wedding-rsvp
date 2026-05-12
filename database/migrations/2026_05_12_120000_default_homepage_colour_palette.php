<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')
            ->where('key', 'homepage_content')
            ->orderBy('id')
            ->get()
            ->each(function (object $setting): void {
                $value = json_decode((string) $setting->value, true);

                if (! is_array($value)) {
                    return;
                }

                $theme = is_array($value['theme'] ?? null) ? $value['theme'] : [];

                if (! array_key_exists('palette', $theme) || blank($theme['palette'])) {
                    $theme['palette'] = 'magic_classic';
                }

                unset($theme['palette_colours']);
                $value['theme'] = $theme;

                DB::table('site_settings')
                    ->where('id', $setting->id)
                    ->update([
                        'value' => json_encode($value),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->where('key', 'homepage_content')
            ->orderBy('id')
            ->get()
            ->each(function (object $setting): void {
                $value = json_decode((string) $setting->value, true);

                if (! is_array($value) || ! is_array($value['theme'] ?? null)) {
                    return;
                }

                if (($value['theme']['palette'] ?? null) === 'magic_classic') {
                    unset($value['theme']['palette']);
                }

                unset($value['theme']['palette_colours']);

                DB::table('site_settings')
                    ->where('id', $setting->id)
                    ->update([
                        'value' => json_encode($value),
                        'updated_at' => now(),
                    ]);
            });
    }
};
