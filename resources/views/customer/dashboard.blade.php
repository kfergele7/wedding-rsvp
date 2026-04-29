<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Dashboard | Wedding RSVP</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css'])
    <style>
        .cust-shell { max-width: 1200px; margin: 0 auto; padding-left: 1rem; padding-right: 1rem; }
        .cust-top { border-bottom: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.92); }
        .cust-nav { position: sticky; top: 0; z-index: 40; border-top: 1px solid rgba(0,0,0,0.12); border-bottom: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.95); backdrop-filter: blur(6px); }
        .cust-tab { border: 1px solid #22363a; background: #22363a; color: #fff; }
        .cust-tab:hover { background: #466369; border-color: #466369; color: #fff; }
        .cust-tab-active { background: #F2ECE3; color: #0f1b1d; border-color: #22363a; border-bottom-width: 2px; pointer-events: none; }
        .cust-block { border: 1px solid #22363a; padding: 1.5rem; }
        .cust-even { background: #F2ECE3; }
        .cust-odd { background: #FFFFFF; }
        .cust-field-label { color: #0f1b1d; font-weight: 500; }
        .cust-logout { border: 1px solid #e66363 !important; background: #e66363 !important; color: #fff !important; }
        .cust-logout:hover { border-color: #b93f3f !important; background: #b93f3f !important; color: #fff !important; }
    </style>
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="pb-12">
    <header class="cust-top">
        <div class="cust-shell py-6">
            <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Customer Dashboard</p>
            <h1 class="font-heading text-4xl">{{ $accountName }}</h1>
        </div>
    </header>

    <div class="cust-nav">
        <div class="cust-shell flex flex-wrap items-center justify-between gap-3 py-3">
            <nav class="flex flex-wrap gap-2">
                <a href="{{ route('customer.dashboard') }}" class="cust-tab inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em] {{ $activeTab === 'overview' ? 'cust-tab-active' : '' }}">
                    <span class="material-symbols-outlined btn-icon">dashboard</span>
                    Overview
                </a>
                <a href="{{ route('customer.dashboard', ['tab' => 'account']) }}" class="cust-tab inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em] {{ $activeTab === 'account' ? 'cust-tab-active' : '' }}">
                    <span class="material-symbols-outlined btn-icon">person</span>
                    My Account
                </a>
                <a href="{{ route('customer.admin.dashboard') }}" class="cust-tab inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]">
                    <span class="material-symbols-outlined btn-icon">edit_note</span>
                    Manage Wedding
                </a>
            </nav>

            <div class="flex items-center gap-2 border-l border-soft pl-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-btn cust-logout inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"><span class="material-symbols-outlined btn-icon">logout</span>Logout</button>
                </form>
            </div>
        </div>
    </div>

    <div class="cust-shell py-10">

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
    <section class="card-frame mt-8 bg-white">
        <h2 class="font-heading text-3xl">Create your website and manage your invites now</h2>
        <p class="mt-3 text-wedding-muted">Manage content, guest lists, and RSVPs in your tenant-scoped customer admin.</p>
        <div class="mt-5">
            <a href="{{ route('customer.admin.dashboard') }}" class="button-dark inline-flex items-center gap-2"><span class="material-symbols-outlined btn-icon">edit_note</span>get started now</a>
        </div>
    </section>

    <section class="mt-8 grid gap-5 md:grid-cols-2">
        <article id="billing-section" class="card-frame scroll-mt-28 bg-white">
            <h2 class="font-heading text-3xl">Billing</h2>
            <p class="mt-4 text-sm uppercase tracking-[0.14em] text-wedding-muted">Subscription Status</p>
            <p class="mt-1 text-lg {{ in_array($billing['status'], ['active', 'gifted'], true) ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $billing['status_label'] }}
            </p>

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
                        <button type="submit" class="admin-btn admin-btn-danger inline-flex items-center justify-center gap-2 px-8 py-4 text-xs font-medium uppercase tracking-[0.2em]"><span class="material-symbols-outlined btn-icon">event_busy</span>Cancel At Period End</button>
                    </form>
                @endif
            </div>
        </article>

        <article class="card-frame bg-white">
            <h2 class="font-heading text-3xl">Quick Stats</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex items-center justify-between border-b border-soft pb-2">
                    <dt>Guest Lists</dt>
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
        </article>
    </section>
    @endif

    @if ($activeTab === 'account')
    <section class="mt-8 grid gap-5 md:grid-cols-2">
        <article class="cust-block cust-even">
            <h2 class="font-heading text-3xl">Profile Details</h2>
            <form method="POST" action="{{ route('customer.account.profile.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                <label class="cust-field-label block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Full Name
                    <input type="text" name="name" value="{{ old('name', $currentUser->name) }}" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <label class="cust-field-label block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Email Address
                    <input type="email" name="email" value="{{ old('email', $currentUser->email) }}" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <button type="submit" class="admin-btn admin-btn-success inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]">
                    <span class="material-symbols-outlined btn-icon">save</span>
                    Save Profile
                </button>
            </form>
        </article>

        <article class="cust-block cust-odd">
            <h2 class="font-heading text-3xl">Change Password</h2>
            <form method="POST" action="{{ route('customer.account.password.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                <label class="cust-field-label block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Current Password
                    <input type="password" name="current_password" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <label class="cust-field-label block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    New Password
                    <input type="password" name="password" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <label class="cust-field-label block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Confirm New Password
                    <input type="password" name="password_confirmation" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>

                <button type="submit" class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]">
                    <span class="material-symbols-outlined btn-icon">lock_reset</span>
                    Update Password
                </button>
            </form>
        </article>
    </section>

    <section class="mt-6">
        <article class="cust-block bg-white">
            <h2 class="font-heading text-3xl text-red-700">Delete Account</h2>
            <p class="mt-3 text-sm text-wedding-muted">
                This action is permanent. Deleting your account will remove your wedding website, guest list, RSVP responses, settings, and related account data.
                This cannot be undone.
            </p>

            <form id="deleteAccountForm" method="POST" action="{{ route('customer.account.destroy') }}" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')

                <label class="cust-field-label block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Current Password
                    <input
                        type="password"
                        name="current_password"
                        required
                        class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base"
                        autocomplete="current-password"
                    >
                </label>

                <label class="cust-field-label block text-sm uppercase tracking-[0.14em] text-wedding-muted">
                    Confirm Current Password
                    <input
                        type="password"
                        name="current_password_confirmation"
                        required
                        class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base"
                        autocomplete="current-password"
                    >
                </label>

                <label class="inline-flex items-center gap-2 text-sm text-wedding-text">
                    <input type="checkbox" name="confirm_data_deletion" value="1" required>
                    I understand all my website and guest data will be permanently deleted.
                </label>

                <div class="pt-2">
                    <button
                        type="button"
                        id="openDeleteAccountConfirm"
                        class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                    >
                        <span class="material-symbols-outlined btn-icon">delete_forever</span>
                        Delete Account Permanently
                    </button>
                </div>
            </form>
        </article>
    </section>
    @endif
    </div>
</main>

<div id="qrModal" class="fixed inset-0 z-[80] hidden bg-black/50 p-4 md:p-8">
    <div class="mx-auto mt-8 w-full max-w-xl border border-soft bg-white p-6 shadow-soft md:mt-16">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-heading text-3xl">Share QR Code</h3>
            <button type="button" id="qrCloseBtn" class="admin-btn admin-btn-danger inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]">X Close</button>
        </div>
        <p class="mt-2 text-sm text-wedding-muted">Download, share digitally, or print your invitation QR code.</p>

        <div class="mt-5 flex justify-center">
            <img id="qrImage" src="" alt="Public URL QR code" class="h-72 w-72 border border-soft object-contain">
        </div>

        <div class="mt-5 flex flex-wrap justify-center gap-2">
            <a id="qrDownloadBtn" href="#" download class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em] transition hover:border-wedding-disabled hover:bg-wedding-disabled hover:text-white">
                <span class="material-symbols-outlined btn-icon">download</span>
                Download
            </a>
            <button type="button" id="qrShareBtn" class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em] transition hover:border-wedding-disabled hover:bg-wedding-disabled hover:text-white">
                <span class="material-symbols-outlined btn-icon">share</span>
                Share Image
            </button>
            <button type="button" id="qrPrintBtn" class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em] transition hover:border-wedding-disabled hover:bg-wedding-disabled hover:text-white">
                <span class="material-symbols-outlined btn-icon">print</span>
                Print
            </button>
        </div>
    </div>
</div>

<div id="deleteAccountConfirmModal" class="fixed inset-0 z-[95] hidden bg-black/50 p-4 md:p-8">
    <div class="mx-auto mt-20 w-full max-w-lg border border-soft bg-white p-6 shadow-soft">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-heading text-3xl">Confirm Account Deletion</h3>
        </div>
        <p class="mt-4 text-wedding-muted">I confirm this action is irreversible and cannot be recovered.</p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" id="deleteAccountCancelBtn" class="admin-btn inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]">
                <span class="material-symbols-outlined btn-icon">close</span>
                Cancel
            </button>
            <button type="button" id="deleteAccountConfirmBtn" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]">
                <span class="material-symbols-outlined btn-icon">delete_forever</span>
                Yes, Delete Account Permanently
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const copyButtons = document.querySelectorAll('[data-copy-link]');
        const shareButtons = document.querySelectorAll('[data-share-link]');
        const qrButtons = document.querySelectorAll('[data-open-qr]');

        const qrModal = document.getElementById('qrModal');
        const qrImage = document.getElementById('qrImage');
        const qrDownloadBtn = document.getElementById('qrDownloadBtn');
        const qrShareBtn = document.getElementById('qrShareBtn');
        const qrPrintBtn = document.getElementById('qrPrintBtn');
        const qrCloseBtn = document.getElementById('qrCloseBtn');
        const deleteAccountForm = document.getElementById('deleteAccountForm');
        const openDeleteAccountConfirm = document.getElementById('openDeleteAccountConfirm');
        const deleteAccountConfirmModal = document.getElementById('deleteAccountConfirmModal');
        const deleteAccountCancelBtn = document.getElementById('deleteAccountCancelBtn');
        const deleteAccountConfirmBtn = document.getElementById('deleteAccountConfirmBtn');

        let activeQrUrl = '';
        let activeQrImageUrl = '';

        async function copyToClipboard(url) {
            try {
                await navigator.clipboard.writeText(url);
                window.alert('Link copied to clipboard.');
            } catch (error) {
                window.prompt('Copy this link:', url);
            }
        }

        copyButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const url = button.getAttribute('data-copy-link');
                if (!url) return;
                await copyToClipboard(url);
            });
        });

        shareButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const url = button.getAttribute('data-share-link');
                if (!url) return;

                if (navigator.share) {
                    try {
                        await navigator.share({ title: 'Wedding Website', url });
                        return;
                    } catch (error) {
                        // fall through to clipboard
                    }
                }

                await copyToClipboard(url);
            });
        });

        function closeQrModal() {
            qrModal?.classList.add('hidden');
        }

        function openQrModal(publicUrl, siteSlug) {
            if (!qrModal || !qrImage || !qrDownloadBtn) return;

            activeQrUrl = publicUrl;
            activeQrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=1200x1200&format=png&data=${encodeURIComponent(publicUrl)}`;
            qrImage.src = activeQrImageUrl;
            qrDownloadBtn.href = activeQrImageUrl;
            qrDownloadBtn.setAttribute('download', `${siteSlug || 'wedding'}-qr.png`);
            qrModal.classList.remove('hidden');
        }

        qrButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const url = button.getAttribute('data-open-qr');
                const siteSlug = button.getAttribute('data-site-slug') || 'wedding';
                if (!url) return;
                openQrModal(url, siteSlug);
            });
        });

        qrCloseBtn?.addEventListener('click', closeQrModal);
        qrModal?.addEventListener('click', (event) => {
            if (event.target === qrModal) {
                closeQrModal();
            }
        });

        qrShareBtn?.addEventListener('click', async () => {
            if (!activeQrImageUrl) return;

            if (navigator.share && navigator.canShare) {
                try {
                    const response = await fetch(activeQrImageUrl);
                    const blob = await response.blob();
                    const file = new File([blob], 'wedding-qr.png', { type: 'image/png' });
                    if (navigator.canShare({ files: [file] })) {
                        await navigator.share({ title: 'Wedding QR Code', text: activeQrUrl, files: [file] });
                        return;
                    }
                } catch (error) {
                    // fallback below
                }
            }

            await copyToClipboard(activeQrUrl);
        });

        qrPrintBtn?.addEventListener('click', () => {
            if (!activeQrImageUrl) return;
            const printWindow = window.open('', '_blank');
            if (!printWindow) return;
            printWindow.document.write(`
                <html>
                <head><title>Print QR Code</title></head>
                <body style="margin:0;display:flex;align-items:center;justify-content:center;min-height:100vh;">
                    <img src="${activeQrImageUrl}" alt="Wedding QR Code" style="width:420px;height:420px;object-fit:contain;" />
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });

        function closeDeleteAccountModal() {
            deleteAccountConfirmModal?.classList.add('hidden');
        }

        openDeleteAccountConfirm?.addEventListener('click', () => {
            if (!deleteAccountForm) return;
            if (!deleteAccountForm.reportValidity()) {
                return;
            }
            deleteAccountConfirmModal?.classList.remove('hidden');
        });

        deleteAccountCancelBtn?.addEventListener('click', closeDeleteAccountModal);
        deleteAccountConfirmModal?.addEventListener('click', (event) => {
            if (event.target === deleteAccountConfirmModal) {
                closeDeleteAccountModal();
            }
        });

        deleteAccountConfirmBtn?.addEventListener('click', () => {
            if (!deleteAccountForm) return;
            deleteAccountConfirmBtn.disabled = true;
            deleteAccountForm.submit();
        });
    })();
</script>
</body>
</html>
