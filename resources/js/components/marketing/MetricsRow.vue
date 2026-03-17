<template>
  <section class="mk-reveal mt-6 grid gap-4 md:grid-cols-3">
    <article v-for="item in metrics" :key="item.title" class="rounded-2xl border border-soft bg-white px-6 py-6 text-center">
      <p class="font-heading text-4xl">{{ item.title }}</p>
      <p class="mt-2 text-xs uppercase tracking-[0.14em] text-wedding-muted">{{ item.subtitle }}</p>
    </article>
  </section>
</template>

<script setup>
const metrics = [
  { title: '10 min', subtitle: 'Setup for first draft' },
  { title: 'Invite-code-only', subtitle: 'Privacy-first RSVP flow' },
  { title: 'CSV ready', subtitle: 'Export guest data anytime' },
];
</script>
