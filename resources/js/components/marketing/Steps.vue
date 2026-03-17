<template>
  <section class="mk-reveal py-2">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <h2 class="font-heading text-4xl">How it works</h2>
      <a href="/how-it-works" class="mk-btn mk-btn-secondary">See full walkthrough</a>
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-3">
      <article v-for="step in steps" :key="step.number" class="rounded-2xl border border-soft bg-white p-6 shadow-soft">
        <p class="font-heading text-5xl text-wedding-band">{{ step.number }}</p>
        <h3 class="mt-3 font-heading text-3xl">{{ step.title }}</h3>
        <p class="mt-2 text-sm text-wedding-muted">{{ step.copy }}</p>
      </article>
    </div>

    <div class="mt-7 overflow-hidden rounded-3xl border border-soft bg-white p-3 shadow-soft">
      <img :src="'/images/marketing/template-2.jpg'" alt="How it works visual" class="h-64 w-full rounded-2xl object-cover md:h-72" loading="lazy" />
    </div>
  </section>
</template>

<script setup>
const steps = [
  {
    number: '01',
    title: 'Build',
    copy: 'Create your website and guest list in draft mode for free.',
  },
  {
    number: '02',
    title: 'Invite',
    copy: 'Share your URL and household invite codes via QR or messages.',
  },
  {
    number: '03',
    title: 'Publish',
    copy: 'Subscribe only when ready, then collect and manage RSVPs live.',
  },
];
</script>
