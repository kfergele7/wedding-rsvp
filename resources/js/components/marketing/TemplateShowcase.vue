<template>
  <section class="mk-reveal rounded-3xl border border-soft bg-white px-6 py-14 shadow-soft md:px-10">
    <h2 class="font-heading text-4xl">One premium template, designed so you cannot break it.</h2>
    <p class="mt-4 text-wedding-muted">Customise the details, not the layout.</p>

    <div class="mt-8 grid gap-4 md:grid-cols-3">
      <article v-for="tile in tiles" :key="tile.title" class="overflow-hidden rounded-2xl border border-soft bg-wedding-offwhite p-2">
        <img :src="tile.image" :alt="tile.alt" class="h-56 w-full rounded-xl object-cover" loading="lazy" />
        <p class="mt-3 text-xs uppercase tracking-[0.14em] text-wedding-muted">{{ tile.title }}</p>
      </article>
    </div>
  </section>
</template>

<script setup>
const tiles = [
  {
    title: 'Intro',
    image: '/images/marketing/template-1.jpg',
    alt: 'Template intro section',
  },
  {
    title: 'Timeline',
    image: '/images/marketing/template-2.jpg',
    alt: 'Template timeline section',
  },
  {
    title: 'Details',
    image: '/images/marketing/template-3.jpg',
    alt: 'Template details section',
  },
];
</script>
