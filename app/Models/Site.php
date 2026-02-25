<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'title',
        'public_slug',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function parties(): HasMany
    {
        return $this->hasMany(Party::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(SiteSetting::class);
    }
}
