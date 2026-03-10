@extends('staff.layout', ['title' => 'Accounts', 'activeTab' => 'accounts'])

@section('content')
<section class="card-frame bg-white">
    <form method="GET" class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
        <input name="q" value="{{ $filters['q'] }}" class="border border-soft px-4 py-3" placeholder="Search name, slug, or owner email">
        <select name="status" class="border border-soft px-4 py-3">
            <option value="">All Subscription Statuses</option>
            @foreach (['active','gifted','draft','past_due','cancelled','suspended'] as $status)
                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ strtoupper($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="button-dark">Filter</button>
    </form>

    <div class="mt-5 overflow-x-auto">
        <table class="min-w-full border-collapse text-sm">
            <thead>
                <tr class="border-b border-soft text-left uppercase tracking-[0.12em] text-wedding-muted">
                    <th class="py-2 pr-4">Account</th>
                    <th class="py-2 pr-4">Owner</th>
                    <th class="py-2 pr-4">Subscription</th>
                    <th class="py-2 pr-4">Access</th>
                    <th class="py-2 pr-4">Sites</th>
                    <th class="py-2 pr-4">Created</th>
                    <th class="py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    @php $owner = $account->users->firstWhere('role', 'owner') ?? $account->users->first(); @endphp
                    <tr class="border-b border-soft/70">
                        <td class="py-3 pr-4">
                            <p class="font-medium">{{ $account->name }}</p>
                            <p class="text-xs text-wedding-muted">{{ $account->slug }}</p>
                        </td>
                        <td class="py-3 pr-4">{{ $owner?->email ?? '—' }}</td>
                        <td class="py-3 pr-4">{{ $account->status }}</td>
                        <td class="py-3 pr-4">{{ $account->access_status }}</td>
                        <td class="py-3 pr-4">{{ $account->sites->count() }}</td>
                        <td class="py-3 pr-4">{{ $account->created_at?->toDateString() }}</td>
                        <td class="py-3">
                            <a href="{{ route('staff.accounts.show', $account) }}" class="admin-btn border border-soft bg-white px-3 py-2 text-xs uppercase tracking-[0.1em]">View / Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-4 text-wedding-muted">No accounts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $accounts->links() }}</div>
</section>
@endsection
