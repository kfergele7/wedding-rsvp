<?php

namespace App\Http\Middleware;

use App\Models\Site;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $site = null;
        $slug = $request->route('public_slug');

        if (is_string($slug) && $slug !== '') {
            $site = Site::query()->where('public_slug', $slug)->first();
        }

        if (! $site && $request->user()?->account_id) {
            $preferredSiteId = $request->session()->get('current_site_id');
            $accountSites = Site::query()->where('account_id', $request->user()->account_id);

            if ($preferredSiteId) {
                $site = (clone $accountSites)->where('id', $preferredSiteId)->first();
            }

            if (! $site) {
                $site = (clone $accountSites)->orderBy('id')->first();
            }
        }

        if (! $site && $request->user()?->is_staff) {
            $staffSiteId = (int) $request->session()->get('staff_site_id');
            $staffAccountId = (int) $request->session()->get('staff_account_id');

            if ($staffSiteId > 0 && $staffAccountId > 0) {
                $site = Site::query()
                    ->where('id', $staffSiteId)
                    ->where('account_id', $staffAccountId)
                    ->first();
            }
        }

        if (! $site && $request->session()->get('admin_authenticated')) {
            $adminSiteId = (int) $request->session()->get('admin_site_id');

            if ($adminSiteId > 0) {
                $site = Site::query()->find($adminSiteId);
            }

            if (! $site) {
                $site = Site::query()->orderBy('id')->first();
                if ($site) {
                    $request->session()->put('admin_site_id', $site->id);
                    $request->session()->put('admin_account_id', $site->account_id);
                }
            }
        }

        if (! $site) {
            $site = Site::query()
                ->where('is_published', true)
                ->orderBy('id')
                ->first()
                ?? Site::query()->orderBy('id')->first();
        }

        app(TenantContext::class)->set($site?->account, $site);

        return $next($request);
    }
}
