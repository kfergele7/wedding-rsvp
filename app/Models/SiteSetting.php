<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use BelongsToSite;
    use HasFactory;

    protected $fillable = [
        'site_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
