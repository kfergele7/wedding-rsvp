<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_user_id',
        'account_id',
        'action',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
