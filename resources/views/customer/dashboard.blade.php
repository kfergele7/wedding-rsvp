<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Dashboard | Wedding RSVP</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell py-12 md:py-16">
    <header class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.18em] text-wedding-muted">Customer Dashboard</p>
            <h1 class="mt-3 font-heading text-5xl">{{ $accountName }}</h1>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="button-dark inline-flex items-center gap-2"><span class="material-symbols-outlined btn-icon">logout</span>Logout</button>
        </form>
    </header>

    <nav class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('customer.dashboard') }}" class="admin-btn inline-flex items-center gap-2 border px-4 py-2 text-xs uppercase tracking-[0.12em] {{ $activeTab === 'overview' ? 'border-wedding-band bg-wedding-band text-white' : 'border-soft bg-white' }}">
            <span class="material-symbols-outlined btn-icon">dashboard</span>
            Overview
        </a>
        <a href="{{ route('customer.dashboard', ['tab' => 'account']) }}" class="admin-btn inline-flex items-center gap-2 border px-4 py-2 text-xs uppercase tracking-[0.12em] {{ $activeTab === 'account' ? 'border-wedding-band bg-wedding-band text-white' : 'border-soft bg-white' }}">
            <span class="material-symbols-outlined btn-icon">person</span>
            Account
        </a>
    </nav>

    @if (request()->boolean('verified'))
        <p class="mt-6 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">Email verified successfully.</p>
    @endif

    @if (session('status'))
        <p class="mt-6 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if ($activeTab === 'overview' && $billing['status'] === 'draft')
        <p class="mt-6 rounded border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">Your account is in draft mode. Subscribe to publish your wedding site.</p>
    @elseif ($activeTab === 'overview' && $billing['status'] === 'active')
        <p class="mt-6 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">Subscription active. You can publish and keep your site live.</p>
    @elseif ($activeTab === 'overview' && $billing['status'] === 'gifted')
        <p class="mt-6 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">You are on a gifted subscription managed by support.</p>
    @elseif ($activeTab === 'overview' && $billing['status'] === 'cancelled')
        <p class="mt-6 rounded border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">Subscription set to cancel at period end. Reactivate anytime in billing portal.</p>
    @elseif ($activeTab === 'overview' && ($billing['status'] === 'past_due' || $billing['status'] === 'suspended'))
        <p class="mt-6 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">Billing needs attention. Your site may be unavailable until billing is resolved.</p>
    @endif

    @if ($activeTab === 'overview')
    <section class="mt-8 grid gap-5 md:grid-cols-2">
        <article class="card-frame bg-white">
            <h2 class="font-heading text-3xl">Site</h2>
            <p class="mt-4 text-sm uppercase tracking-[0.14em] text-wedding-muted">Title</p>
            <p class="mt-1 text-lg">{{ $siteTitle }}</p>
            <p class="mt-4 text-sm uppercase tracking-[0.14em] text-wedding-muted">Public URL</p>
            <p class="mt-1 text-lg">{{ url('/w/'.$siteSlug) }}</p>
            <p class="mt-4 text-sm uppercase tracking-[0.14em] text-wedding-muted">Site Visibility</p>
            <p class="mt-1 text-lg">{{ $sitePublished ? 'Published' : 'Draft' }}</p>

            <form method="POST" action="{{ $billing['publish_url'] }}" class="mt-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="is_published" value="{{ $sitePublished ? 0 : 1 }}">
                <button type="submit" class="button-dark inline-flex items-center gap-2"><span class="material-symbols-outlined btn-icon">{{ $sitePublished ? 'visibility_off' : 'publish' }}</span>{{ $sitePublished ? 'Move To Draft' : 'Publish Site' }}</button>
            </form>
        </article>

        <article class="card-frame bg-white">
            <h2 class="font-heading text-3xl">Billing</h2>
            <p class="mt-4 text-sm uppercase tracking-[0.14em] text-wedding-muted">Subscription Status</p>
            <p class="mt-1 text-lg">{{ $billing['status_label'] }}</p>

            @if ($billing['period_end'])
                <p class="mt-3 text-sm text-wedding-muted">Current period ends: {{ $billing['period_end'] }}</p>
            @endif

            @if ($billing['cancel_at_period_end'])
                <p class="mt-2 text-sm text-amber-700">Your subscription is set to cancel at period end.</p>
            @endif
            @if ($billing['status'] === 'gifted')
                <p class="mt-2 text-sm text-wedding-muted">Billing changes and cancellation are handled by your administrator for gifted subscriptions.</p>
            @endif

            <div class="mt-5 flex flex-wrap gap-3">
                @if (! $billing['has_paid_access'])
                    <form method="POST" action="{{ $billing['checkout_url'] }}">
                        @csrf
                        <button type="submit" class="button-dark inline-flex items-center gap-2"><span class="material-symbols-outlined btn-icon">credit_card</span>Subscribe Monthly</button>
                    </form>
                @endif

                @if ($billing['status'] !== 'draft' && $billing['status'] !== 'gifted')
                    <form method="POST" action="{{ $billing['portal_url'] }}">
                        @csrf
                        <button type="submit" class="button-dark inline-flex items-center gap-2"><span class="material-symbols-outlined btn-icon">account_balance_wallet</span>Manage Billing</button>
                    </form>
                @endif

                @if ($billing['has_paid_access'] && ! $billing['cancel_at_period_end'] && $billing['status'] !== 'gifted')
                    <form method="POST" action="{{ $billing['cancel_url'] }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 border border-red-400 bg-white px-8 py-4 text-xs font-medium uppercase tracking-[0.2em] text-red-700 transition hover:bg-red-50"><span class="material-symbols-outlined btn-icon">event_busy</span>Cancel At Period End</button>
                    </form>
                @endif
            </div>
        </article>
    </section>

    <section class="card-frame mt-8 bg-white">
        <h2 class="font-heading text-3xl">Create your website and manage your invites now</h2>
        <p class="mt-3 text-wedding-muted">Manage content, households, and RSVPs in your tenant-scoped customer admin.</p>
        <div class="mt-5">
            <a href="{{ route('customer.admin.dashboard') }}" class="button-dark inline-flex items-center gap-2"><span class="material-symbols-outlined btn-icon">edit_note</span>get started now</a>
        </div>
    </section>

    <section class="card-frame mt-8 bg-white">
        <h2 class="font-heading text-3xl">Quick Stats</h2>
        <dl class="mt-5 space-y-3 text-sm">
            <div class="flex items-center justify-between border-b border-soft pb-2">
                <dt>Households</dt>
                <dd>{{ $stats['households'] }}</dd>
            </div>
            <div class="flex items-center justify-between border-b border-soft pb-2">
                <dt>Invited Guests</dt>
                <dd>{{ $stats['guests'] }}</dd>
            </div>
            <div class="flex items-center justify-between pb-1">
                <dt>Attending</dt>
                <dd>{{ $stats['attending'] }}</dd>
            </div>
        </dl>
    </section>
    @endif

    @if ($activeTab === 'account')
    <section class="mt-8 grid gap-5 md:grid-cols-2">
        <article class="card-frame bg-white">
            <h2 class="font-heading text-3xl">Profile Details</h2>
            <form method="POST" action="{{ route('customer.account.profile.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                <label class="block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Full Name
                    <input type="text" name="name" value="{{ old('name', $currentUser->name) }}" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <label class="block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Email Address
                    <input type="email" name="email" value="{{ old('email', $currentUser->email) }}" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <button type="submit" class="button-dark inline-flex items-center gap-2">
                    <span class="material-symbols-outlined btn-icon">save</span>
                    Save Profile
                </button>
            </form>
        </article>

        <article class="card-frame bg-white">
            <h2 class="font-heading text-3xl">Change Password</h2>
            <form method="POST" action="{{ route('customer.account.password.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                <label class="block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Current Password
                    <input type="password" name="current_password" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <label class="block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    New Password
                    <input type="password" name="password" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <label class="block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Confirm New Password
                    <input type="password" name="password_confirmation" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <button type="submit" class="button-dark inline-flex items-center gap-2">
                    <span class="material-symbols-outlined btn-icon">lock_reset</span>
                    Update Password
                </button>
            </form>
        </article>
    </section>
    @endif
</main>
</body>
</html>
