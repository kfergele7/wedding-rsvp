<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Support\TenantContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class Controller
{
    protected function currentSite(): Site
    {
        $site = app(TenantContext::class)->site();

        if (! $site) {
            throw new NotFoundHttpException('No site context available.');
        }

        return $site;
    }

    protected function currentSiteId(): int
    {
        return $this->currentSite()->id;
    }
}
