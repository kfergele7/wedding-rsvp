<template>
  <section class="mk-reveal py-4">
    <div class="grid gap-10 lg:grid-cols-[1.02fr_0.98fr] lg:items-center">
      <div class="overflow-hidden rounded-3xl border border-soft bg-white p-3 shadow-soft">
        <img :src="'/images/marketing/rsvp-flow.jpg'" alt="RSVP journey preview" class="h-[340px] w-full rounded-2xl object-cover md:h-[420px]" loading="lazy" />
      </div>

      <div>
        <p class="mk-kicker">RSVP wedge</p>
        <h2 class="mt-3 font-heading text-4xl leading-tight">The simplest RSVP for guests, especially older relatives.</h2>
        <ul class="mt-6 space-y-3 text-sm text-wedding-black md:text-base">
          <li class="mk-bullet"><span class="material-symbols-outlined">check_circle</span>Invite code only</li>
          <li class="mk-bullet"><span class="material-symbols-outlined">check_circle</span>QR code per household</li>
          <li class="mk-bullet"><span class="material-symbols-outlined">check_circle</span>No guest accounts</li>
          <li class="mk-bullet"><span class="material-symbols-outlined">check_circle</span>Manual RSVP entry for phone call-ins</li>
        </ul>

        <div class="mt-7 rounded-2xl border border-soft bg-wedding-offwhite p-5">
          <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">RSVP flow</p>
          <div class="mt-4 grid gap-2 md:grid-cols-3">
            <div class="rounded-xl border border-soft bg-white p-4 text-center text-xs uppercase tracking-[0.1em]">1. Scan QR</div>
            <div class="rounded-xl border border-soft bg-white p-4 text-center text-xs uppercase tracking-[0.1em]">2. Enter code</div>
            <div class="rounded-xl border border-soft bg-white p-4 text-center text-xs uppercase tracking-[0.1em]">3. Submit RSVP</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
