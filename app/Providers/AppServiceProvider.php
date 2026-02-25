<?php

namespace App\Providers;

use App\Models\Guest;
use App\Models\Party;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class, fn () => new TenantContext());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::bind('party', function ($value) {
            $siteId = app(TenantContext::class)->siteId()
                ?: (request()->user()?->account?->sites()->orderBy('id')->value('id'))
                ?: (int) request()->session()->get('admin_site_id');

            return Party::query()
                ->whereKey($value)
                ->when($siteId, fn ($query) => $query->where('site_id', $siteId))
                ->firstOrFail();
        });

        Route::bind('guest', function ($value) {
            $siteId = app(TenantContext::class)->siteId()
                ?: (request()->user()?->account?->sites()->orderBy('id')->value('id'))
                ?: (int) request()->session()->get('admin_site_id');

            return Guest::query()
                ->whereKey($value)
                ->when($siteId, fn ($query) => $query->where('site_id', $siteId))
                ->firstOrFail();
        });
    }
}
