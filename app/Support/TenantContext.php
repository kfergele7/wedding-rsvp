<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Site;

class TenantContext
{
    private ?Account $account = null;

    private ?Site $site = null;

    public function set(?Account $account, ?Site $site): void
    {
        $this->account = $account;
        $this->site = $site;
    }

    public function account(): ?Account
    {
        return $this->account;
    }

    public function accountId(): ?int
    {
        return $this->account?->id;
    }

    public function site(): ?Site
    {
        return $this->site;
    }

    public function siteId(): ?int
    {
        return $this->site?->id;
    }
}
