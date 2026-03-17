<template>
  <section class="mk-reveal">
    <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
      <div>
        <p class="mk-kicker">Wedding website and RSVP platform</p>
        <h1 class="mt-4 font-heading text-4xl leading-tight md:text-6xl">
          Your wedding website and RSVPs, done beautifully.
        </h1>
        <p class="mt-5 max-w-2xl text-base leading-relaxed text-wedding-muted md:text-lg">
          One elegant template. Update everything in minutes with simple forms.
          Subscribe only when you are ready to go live.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
          <a href="/register" class="mk-btn mk-btn-primary">Start free</a>
          <a href="/demo" class="mk-btn mk-btn-secondary">View demo</a>
        </div>
        <p class="mt-4 text-sm text-wedding-muted">No card required to start. Cancel anytime.</p>
      </div>

      <div class="relative">
        <div class="overflow-hidden rounded-[20px] border border-soft bg-white p-3 shadow-soft">
          <img :src="'/images/marketing/hero.jpg'" alt="Magic Invitation preview" class="h-[360px] w-full rounded-2xl object-cover md:h-[500px]" fetchpriority="high" />
        </div>

        <div class="absolute -left-2 top-8 rounded-full border border-soft bg-white px-4 py-2 text-[11px] uppercase tracking-[0.14em] shadow-soft md:-left-4">
          Template
        </div>
        <div class="absolute -right-2 bottom-24 rounded-full border border-soft bg-white px-4 py-2 text-[11px] uppercase tracking-[0.14em] shadow-soft md:-right-4">
          RSVP code
        </div>
        <div class="absolute left-1/2 top-[calc(100%-12px)] -translate-x-1/2 rounded-full border border-soft bg-wedding-light px-4 py-2 text-[11px] uppercase tracking-[0.14em] shadow-soft">
          Guest list
        </div>
      </div>
    </div>
  </section>
</template>
