<template>
  <section class="mk-reveal rounded-3xl border border-soft bg-wedding-light/70 px-6 py-14 md:px-10">
    <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
      <div>
        <p class="mk-kicker">Always polished</p>
        <h2 class="mt-3 font-heading text-4xl leading-tight">A site that always looks polished</h2>
        <p class="mt-4 max-w-xl text-wedding-muted">
          You are not designing from scratch. You are filling a premium template engineered to stay elegant on every screen.
        </p>
        <a href="/demo" class="mk-btn mk-btn-secondary mt-7">See a demo</a>
      </div>

      <div class="relative h-[320px] sm:h-[380px]">
        <article class="absolute left-0 top-12 w-[56%] rotate-[-2deg] overflow-hidden rounded-2xl border border-soft bg-white p-2 shadow-soft">
          <img :src="'/images/marketing/collage-1.jpg'" alt="Invitation preview" class="h-48 w-full rounded-xl object-cover sm:h-56" loading="lazy" />
        </article>
        <article class="absolute right-0 top-0 z-10 w-[52%] rotate-[1.5deg] overflow-hidden rounded-2xl border border-soft bg-white p-2 shadow-soft">
          <img :src="'/images/marketing/collage-2.jpg'" alt="Timeline preview" class="h-48 w-full rounded-xl object-cover sm:h-56" loading="lazy" />
        </article>
        <article class="absolute bottom-0 right-[16%] z-20 w-[52%] rotate-[-1deg] overflow-hidden rounded-2xl border border-soft bg-white p-2 shadow-soft">
          <img :src="'/images/marketing/collage-3.jpg'" alt="RSVP form preview" class="h-48 w-full rounded-xl object-cover sm:h-56" loading="lazy" />
        </article>
      </div>
    </div>
  </section>
</template>
