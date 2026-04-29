<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RsvpEmailLog extends Model
{
    use BelongsToSite;
    use HasFactory;

    public const TYPE_INVITE = 'invite';
    public const TYPE_REMINDER = 'reminder';

    protected $fillable = [
        'site_id',
        'party_id',
        'sent_by_user_id',
        'sent_to_email',
        'type',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }
}
