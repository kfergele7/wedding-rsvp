<template>
  <MarketingLayout>
    <div class="mx-auto w-full max-w-[1180px] space-y-14 px-4 md:space-y-20">
      <section class="mk-reveal">
        <p class="mk-kicker">Pricing</p>
        <h1 class="mt-3 font-heading text-5xl md:text-6xl">Free to build. Pay when ready to publish.</h1>
        <p class="mt-4 max-w-3xl text-wedding-muted">No card required to start. Build your site and guest list first, then subscribe when you want to go live.</p>
      </section>

      <section class="mk-reveal grid gap-5 lg:grid-cols-2">
        <article class="rounded-3xl border border-soft bg-white p-8 shadow-soft">
          <p class="mk-kicker">Free</p>
          <h2 class="mt-3 font-heading text-4xl">£0</h2>
          <ul class="mt-6 space-y-3 text-sm text-wedding-black">
            <li v-for="line in freeIncludes" :key="line" class="mk-bullet"><span class="material-symbols-outlined">check_circle</span>{{ line }}</li>
          </ul>
          <a href="/register" class="mk-btn mk-btn-secondary mt-7">Start free</a>
        </article>

        <article class="rounded-3xl border border-soft bg-wedding-light/55 p-8 shadow-soft">
          <p class="mk-kicker">Paid</p>
          <h2 class="mt-3 font-heading text-4xl">{{ plan.name }}</h2>
          <p class="mt-2 text-3xl">{{ plan.price }}<span class="text-base text-wedding-muted">{{ plan.interval }}</span></p>
          <ul class="mt-6 space-y-3 text-sm text-wedding-black">
            <li v-for="line in plan.includes" :key="line" class="mk-bullet"><span class="material-symbols-outlined">check_circle</span>{{ line }}</li>
          </ul>
          <div class="mt-7 flex flex-wrap gap-3">
            <a href="/register" class="mk-btn mk-btn-primary">Start free</a>
            <a href="/login" class="mk-btn mk-btn-secondary">Subscribe to publish</a>
          </div>
        </article>
      </section>

      <section class="mk-reveal rounded-3xl border border-soft bg-white px-8 py-10 shadow-soft">
        <h3 class="font-heading text-3xl">What paid unlocks</h3>
        <div class="mt-5 grid gap-3 md:grid-cols-2">
          <article v-for="line in paidIncludes" :key="line" class="rounded-2xl border border-soft bg-wedding-offwhite/70 px-5 py-4 text-sm">
            {{ line }}
          </article>
        </div>
        <p class="mt-6 text-sm text-wedding-muted">Cancel anytime. Your site stays live until the end of your current billing period.</p>
      </section>
    </div>
  </MarketingLayout>
</template>

<script setup>
import MarketingLayout from '../../components/marketing/MarketingLayout.vue';

const props = defineProps({
  payload: {
    type: Object,
    default: () => ({}),
  },
});

const pricing = props.payload?.pricing || {};
const plan = pricing.plan || { name: 'Magic Invitation Pro', price: '£19', interval: '/month', includes: [] };
const freeIncludes = pricing.freeIncludes || [];
const paidIncludes = pricing.paidIncludes || [];
</script>
