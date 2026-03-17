<template>
  <section class="mk-reveal rounded-3xl border border-soft bg-wedding-light/55 px-6 py-14 md:px-10">
    <div class="grid gap-8 md:grid-cols-[1.1fr_0.9fr] md:items-center">
      <div>
        <h2 class="font-heading text-5xl leading-tight">Ready to launch your wedding website?</h2>
        <p class="mt-4 max-w-xl text-wedding-muted">
          Build everything first, publish when ready, and keep RSVPs simple for every guest.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
          <a href="/register" class="mk-btn mk-btn-primary">Start free</a>
          <a href="/pricing" class="mk-btn mk-btn-secondary">View pricing</a>
        </div>
      </div>
      <div class="overflow-hidden rounded-2xl border border-soft bg-white p-3 shadow-soft">
        <img :src="'/images/marketing/template-3.jpg'" alt="Couple celebrating" class="h-72 w-full rounded-xl object-cover" loading="lazy" />
      </div>
    </div>
  </section>
</template>
