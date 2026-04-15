@extends('staff.layout', ['title' => 'My Account', 'activeTab' => 'account'])

@section('content')
<section class="grid gap-6 lg:grid-cols-2">
    <article class="card-frame bg-white">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined mt-1 text-wedding-primarygreen">badge</span>
            <div>
                <h2 class="font-heading text-3xl">Staff Profile</h2>
                <p class="mt-2 text-sm text-wedding-muted">Update the name and email address used for your staff login.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('staff.account.profile.update') }}" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <label class="block text-sm font-semibold uppercase tracking-[0.14em] text-wedding-black">
                Full Name
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $staffUser->name) }}"
                    required
                    autocomplete="name"
                    class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base font-normal normal-case tracking-normal text-wedding-black"
                >
            </label>

            <label class="block text-sm font-semibold uppercase tracking-[0.14em] text-wedding-black">
                Email Address
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $staffUser->email) }}"
                    required
                    autocomplete="email"
                    class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base font-normal normal-case tracking-normal text-wedding-black"
                >
            </label>

            <label class="block text-sm font-semibold uppercase tracking-[0.14em] text-wedding-black">
                Current Password
                <input
                    type="password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base font-normal normal-case tracking-normal text-wedding-black"
                >
                <span class="mt-2 block text-xs font-normal normal-case tracking-normal text-wedding-muted">Required before changing staff profile details.</span>
            </label>

            <button type="submit" class="admin-btn admin-btn-success inline-flex items-center gap-2 px-5 py-3 text-xs uppercase tracking-[0.12em]">
                <span class="material-symbols-outlined btn-icon">save</span>
                Save Profile Details
            </button>
        </form>
    </article>

    <article class="card-frame bg-wedding-light">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined mt-1 text-wedding-primarygreen">lock_reset</span>
            <div>
                <h2 class="font-heading text-3xl">Change Password</h2>
                <p class="mt-2 text-sm text-wedding-muted">Choose a strong password for accessing staff tools.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('staff.account.password.update') }}" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <label class="block text-sm font-semibold uppercase tracking-[0.14em] text-wedding-black">
                Current Password
                <input
                    type="password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base font-normal normal-case tracking-normal text-wedding-black"
                >
            </label>

            <label class="block text-sm font-semibold uppercase tracking-[0.14em] text-wedding-black">
                New Password
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base font-normal normal-case tracking-normal text-wedding-black"
                >
            </label>

            <label class="block text-sm font-semibold uppercase tracking-[0.14em] text-wedding-black">
                Confirm New Password
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base font-normal normal-case tracking-normal text-wedding-black"
                >
            </label>

            <button type="submit" class="admin-btn inline-flex items-center gap-2 px-5 py-3 text-xs uppercase tracking-[0.12em]">
                <span class="material-symbols-outlined btn-icon">lock_reset</span>
                Update Password
            </button>
        </form>
    </article>
</section>
@endsection
