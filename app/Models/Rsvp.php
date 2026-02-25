<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    use BelongsToSite;
    use HasFactory;

    public const STATUS_ATTENDING = 'attending';
    public const STATUS_NOT_ATTENDING = 'not_attending';

    protected $fillable = [
        'site_id',
        'party_id',
        'status',
        'attending_count',
        'meal_choices',
        'dietary_restrictions',
        'message',
    ];

    protected $casts = [
        'meal_choices' => 'array',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
