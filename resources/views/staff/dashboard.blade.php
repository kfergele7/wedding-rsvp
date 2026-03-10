@extends('staff.layout', ['title' => 'Staff Dashboard', 'activeTab' => 'dashboard'])

@section('content')
<section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <article class="card-frame bg-white">
        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Total Accounts</p>
        <p class="mt-2 font-heading text-4xl">{{ $stats['total_accounts'] }}</p>
    </article>
    <article class="card-frame bg-white">
        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Active Subscriptions</p>
        <p class="mt-2 font-heading text-4xl">{{ $stats['active_subscriptions'] }}</p>
    </article>
    <article class="card-frame bg-white">
        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Draft / Unpaid</p>
        <p class="mt-2 font-heading text-4xl">{{ $stats['draft_or_unpaid'] }}</p>
    </article>
    <article class="card-frame bg-white">
        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Cancelled / Expired</p>
        <p class="mt-2 font-heading text-4xl">{{ $stats['cancelled_or_expired'] }}</p>
    </article>
    <article class="card-frame bg-white">
        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Total Sites</p>
        <p class="mt-2 font-heading text-4xl">{{ $stats['total_sites'] }}</p>
    </article>
    <article class="card-frame bg-white">
        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Processed Webhooks</p>
        <p class="mt-2 font-heading text-4xl">{{ $stats['recent_webhooks'] }}</p>
    </article>
</section>
@endsection
