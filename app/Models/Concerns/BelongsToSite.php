<?php

namespace App\Models\Concerns;

use App\Models\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSite
{
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function scopeForSite(Builder $query, int $siteId): Builder
    {
        return $query->where($query->getModel()->getTable().'.site_id', $siteId);
    }
}
