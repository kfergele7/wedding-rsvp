@extends('marketing.layout', ['title' => 'Pricing'])

@section('content')
<section class="mx-auto max-w-4xl card-frame bg-white text-center">
    <p class="text-xs uppercase tracking-[0.2em] text-wedding-muted">Pricing</p>
    <h1 class="mt-4 font-heading text-6xl">One plan. Everything you need.</h1>
    <p class="mt-4 text-wedding-muted">Built for couples who want a polished wedding website and practical RSVP operations.</p>

    <div class="mx-auto mt-8 max-w-2xl rounded border border-soft p-8 text-left">
        <h2 class="font-heading text-4xl">{{ $plan['name'] }}</h2>
        <p class="mt-2 text-2xl">{{ $plan['price'] }}{{ $plan['interval'] }}</p>
        <p class="mt-2 text-sm text-wedding-muted">Cancel anytime. No long-term contract.</p>

        <ul class="mt-5 space-y-2 text-sm text-wedding-text">
            @foreach ($plan['includes'] as $item)
                <li>• {{ $item }}</li>
            @endforeach
        </ul>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('register') }}" class="button-dark">Create Account</a>
            <a href="{{ route('login') }}" class="admin-btn border border-soft bg-white px-6 py-4 text-xs uppercase tracking-[0.14em]">Login</a>
        </div>
    </div>
</section>
@endsection
