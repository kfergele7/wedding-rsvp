<template>
  <section class="mk-reveal rounded-3xl border border-soft bg-white px-6 py-14 shadow-soft md:px-10">
    <h2 class="font-heading text-4xl">Trusted by couples and planners</h2>
    <div class="mt-8 grid gap-4 md:grid-cols-3">
      <article v-for="item in testimonials" :key="item.name" class="rounded-2xl border border-soft bg-wedding-offwhite/70 p-6">
        <p class="text-sm leading-relaxed text-wedding-black">“{{ item.quote }}”</p>
        <p class="mt-4 text-xs uppercase tracking-[0.14em] text-wedding-muted">{{ item.name }}</p>
        <p class="mt-1 text-xs text-wedding-muted">{{ item.role }}</p>
      </article>
    </div>
  </section>
</template>

<script setup>
const testimonials = [
  {
    quote: 'Guests actually completed their RSVPs without us chasing everyone individually.',
    name: 'Isla & Nathan',
    role: 'Bride & Groom',
  },
  {
    quote: 'The household view and manual call-in updates made planning far less stressful.',
    name: 'Harriet Cole',
    role: 'Wedding Planner',
  },
  {
    quote: 'We launched in one evening and it still looked premium from day one.',
    name: 'Leah & Marcus',
    role: 'Bride & Groom',
  },
];
</script>
