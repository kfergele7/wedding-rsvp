@extends('staff.layout', ['title' => 'Account: '.$account->name])

@section('content')
<section class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
    <article class="card-frame bg-white">
        <h2 class="font-heading text-3xl">Account Detail</h2>
        <div class="mt-4 grid gap-2 text-sm">
            <p><span class="font-medium">Name:</span> {{ $account->name }}</p>
            <p><span class="font-medium">Slug:</span> {{ $account->slug }}</p>
            <p><span class="font-medium">Owner:</span> {{ $owner?->name ?? '—' }} ({{ $owner?->email ?? '—' }})</p>
            <p><span class="font-medium">Subscription Status:</span> {{ $account->status }}</p>
            <p><span class="font-medium">Stripe Customer:</span> {{ $account->stripe_customer_id ?? '—' }}</p>
            <p><span class="font-medium">Stripe Subscription:</span> {{ $account->stripe_subscription_id ?? '—' }}</p>
            <p><span class="font-medium">Access Status:</span> {{ $account->access_status }}</p>
            <p><span class="font-medium">Created:</span> {{ $account->created_at?->toDateTimeString() }}</p>
        </div>

        <h3 class="mt-6 font-heading text-2xl">Sites</h3>
        <div class="mt-3 space-y-2 text-sm">
            @foreach ($account->sites as $site)
                <div class="rounded border border-soft p-3">
                    <p class="font-medium">{{ $site->title }}</p>
                    <form method="POST" action="{{ route('staff.accounts.sites.update', [$account, $site]) }}" class="mt-2 grid gap-2 md:grid-cols-[1fr_auto] md:items-end">
                        @csrf
                        @method('PUT')
                        <label class="text-xs uppercase tracking-[0.12em] text-wedding-muted">
                            Public Slug
                            <input
                                type="text"
                                name="public_slug"
                                value="{{ old('public_slug', $site->public_slug) }}"
                                class="mt-1 w-full border border-soft px-3 py-2 lowercase"
                                pattern="[a-z0-9-]+"
                                minlength="4"
                                maxlength="24"
                                required
                            >
                        </label>
                        <button type="submit" class="admin-btn button-dark px-4 py-2 text-xs uppercase tracking-[0.12em]">Save Slug</button>
                    </form>
                    <p class="mt-2 text-wedding-muted">URL: {{ route('wedding.public', ['public_slug' => $site->public_slug]) }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <a href="{{ route('wedding.public', ['public_slug' => $site->public_slug]) }}" target="_blank" rel="noopener noreferrer" class="admin-btn border border-soft bg-white px-3 py-2 text-xs uppercase tracking-[0.1em]">Open Public Site</a>
                        <a href="{{ route('staff.accounts.sites.launch-admin', [$account, $site]) }}" class="button-dark px-3 py-2 text-xs uppercase tracking-[0.1em]">Edit Dashboard</a>
                        <a href="{{ route('staff.accounts.sites.launch-admin', [$account, $site, 'section' => 'content']) }}" class="button-dark px-3 py-2 text-xs uppercase tracking-[0.1em]">Edit Content</a>
                        <a href="{{ route('staff.accounts.sites.launch-admin', [$account, $site, 'section' => 'parties']) }}" class="button-dark px-3 py-2 text-xs uppercase tracking-[0.1em]">Edit Households</a>
                        <a href="{{ route('staff.accounts.sites.launch-admin', [$account, $site, 'section' => 'rsvps']) }}" class="button-dark px-3 py-2 text-xs uppercase tracking-[0.1em]">Edit RSVPs</a>
                    </div>

                </div>
            @endforeach
        </div>

        <h3 class="mt-6 font-heading text-2xl">Support Metrics</h3>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
            <div class="rounded border border-soft p-3 text-sm"><span class="font-medium">Households:</span> {{ $metrics['households'] }}</div>
            <div class="rounded border border-soft p-3 text-sm"><span class="font-medium">Guests:</span> {{ $metrics['guests'] }}</div>
            <div class="rounded border border-soft p-3 text-sm"><span class="font-medium">RSVP Responses:</span> {{ $metrics['rsvp_responses'] }}</div>
            <div class="rounded border border-soft p-3 text-sm"><span class="font-medium">Last RSVP Activity:</span> {{ $metrics['last_rsvp_at'] ?? '—' }}</div>
        </div>
    </article>

    <aside class="space-y-6">
        <article class="card-frame bg-white">
            <h3 class="font-heading text-2xl">Staff Actions</h3>
            <form method="POST" action="{{ route('staff.accounts.update', $account) }}" class="mt-4 space-y-3">
                @csrf
                @method('PUT')

                <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">Access Status
                    <select name="access_status" class="mt-1 w-full border border-soft px-3 py-2">
                        <option value="active" @selected($account->access_status === 'active')>Active</option>
                        <option value="suspended" @selected($account->access_status === 'suspended')>Suspended</option>
                    </select>
                </label>

                <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">Subscription Status
                    <select name="status" class="mt-1 w-full border border-soft px-3 py-2">
                        <option value="draft" @selected($account->status === 'draft')>Draft</option>
                        <option value="active" @selected($account->status === 'active')>Active</option>
                        <option value="gifted" @selected($account->status === 'gifted')>Gifted</option>
                        <option value="past_due" @selected($account->status === 'past_due')>Past Due</option>
                        <option value="cancelled" @selected($account->status === 'cancelled')>Cancelled</option>
                        <option value="suspended" @selected($account->status === 'suspended')>Expired/Suspended</option>
                    </select>
                </label>

                <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">Internal Notes
                    <textarea name="internal_notes" rows="8" class="mt-1 w-full border border-soft px-3 py-2">{{ old('internal_notes', $account->internal_notes) }}</textarea>
                </label>

                <button type="submit" class="button-dark w-full">Save Account Changes</button>
            </form>
        </article>

        <article class="card-frame bg-white">
            <h3 class="font-heading text-2xl">Recent Billing Webhooks</h3>
            <div class="mt-3 space-y-2 text-sm">
                @forelse ($webhookEvents as $event)
                    <div class="rounded border border-soft p-3">
                        <p class="font-medium">{{ $event->type }}</p>
                        <p class="text-xs text-wedding-muted">{{ $event->processed_at?->toDateTimeString() ?? 'Pending' }}</p>
                    </div>
                @empty
                    <p class="text-wedding-muted">No webhook events logged for this account yet.</p>
                @endforelse
            </div>
        </article>
    </aside>
</section>
@endsection
