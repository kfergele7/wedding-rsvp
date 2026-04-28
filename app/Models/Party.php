<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Party extends Model
{
    use BelongsToSite;
    use HasFactory;

    public const GUEST_TYPE_DAY = 'day';
    public const GUEST_TYPE_EVENING = 'evening';

    protected $fillable = [
        'site_id',
        'code',
        'display_name',
        'guest_type',
        'email',
        'max_guests',
        'notes',
    ];

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function rsvp(): HasOne
    {
        return $this->hasOne(Rsvp::class);
    }

    public function rsvpEmailLogs(): HasMany
    {
        return $this->hasMany(RsvpEmailLog::class);
    }

    public static function guestTypes(): array
    {
        return [
            self::GUEST_TYPE_DAY,
            self::GUEST_TYPE_EVENING,
        ];
    }

    public function guestTypeLabel(): string
    {
        return $this->guest_type === self::GUEST_TYPE_EVENING
            ? 'Evening Guest'
            : 'All Day Guest';
    }

    public static function generateCode(int $siteId, int $length = 5): string
    {
        $length = max(3, min(10, $length));
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (self::query()
            ->forSite($siteId)
            ->whereRaw('UPPER(code) = ?', [$code])
            ->exists());

        return $code;
    }
}
