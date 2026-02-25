<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Account extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_GIFTED = 'gifted';
    public const STATUS_PAST_DUE = 'past_due';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'access_status',
        'internal_notes',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'subscription_status',
        'subscription_current_period_end',
        'subscription_cancel_at_period_end',
        'subscription_canceled_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'subscription_current_period_end' => 'datetime',
        'subscription_cancel_at_period_end' => 'boolean',
        'subscription_canceled_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function hasActivePaidAccess(): bool
    {
        if (($this->access_status ?? 'active') === 'suspended') {
            return false;
        }

        if (in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_GIFTED], true)) {
            return true;
        }

        if ($this->status === self::STATUS_CANCELLED && $this->subscription_current_period_end instanceof Carbon) {
            return $this->subscription_current_period_end->isFuture();
        }

        return false;
    }

    public function syncStripeSubscription(array $subscription): void
    {
        $status = (string) ($subscription['status'] ?? '');
        $periodEndTimestamp = (int) ($subscription['current_period_end'] ?? 0);
        $canceledAtTimestamp = (int) ($subscription['canceled_at'] ?? 0);
        $cancelAtPeriodEnd = (bool) ($subscription['cancel_at_period_end'] ?? false);

        $mappedStatus = match ($status) {
            'active', 'trialing' => self::STATUS_ACTIVE,
            'past_due', 'unpaid', 'incomplete', 'incomplete_expired' => self::STATUS_PAST_DUE,
            'canceled' => $periodEndTimestamp > now()->timestamp ? self::STATUS_CANCELLED : self::STATUS_SUSPENDED,
            default => self::STATUS_DRAFT,
        };

        if ($cancelAtPeriodEnd && $mappedStatus === self::STATUS_ACTIVE) {
            $mappedStatus = self::STATUS_CANCELLED;
        }

        $this->forceFill([
            'status' => $mappedStatus,
            'stripe_subscription_id' => (string) ($subscription['id'] ?? $this->stripe_subscription_id),
            'stripe_price_id' => (string) ($subscription['items']['data'][0]['price']['id'] ?? $this->stripe_price_id),
            'subscription_status' => $status,
            'subscription_current_period_end' => $periodEndTimestamp > 0 ? Carbon::createFromTimestamp($periodEndTimestamp) : null,
            'subscription_cancel_at_period_end' => $cancelAtPeriodEnd,
            'subscription_canceled_at' => $canceledAtTimestamp > 0 ? Carbon::createFromTimestamp($canceledAtTimestamp) : null,
            'subscription_ends_at' => $periodEndTimestamp > 0 && in_array($mappedStatus, [self::STATUS_CANCELLED, self::STATUS_SUSPENDED], true)
                ? Carbon::createFromTimestamp($periodEndTimestamp)
                : null,
        ])->save();
    }
}
