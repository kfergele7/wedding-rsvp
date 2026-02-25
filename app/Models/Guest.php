<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    use BelongsToSite;
    use HasFactory;

    protected $fillable = [
        'site_id',
        'party_id',
        'first_name',
        'last_name',
        'is_child',
    ];

    protected $casts = [
        'is_child' => 'boolean',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
