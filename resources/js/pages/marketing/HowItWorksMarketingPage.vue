<template>
  <MarketingLayout>
    <div class="mx-auto w-full max-w-[1180px] space-y-14 px-4 md:space-y-20">
      <section class="mk-reveal">
        <p class="mk-kicker">How it works</p>
        <h1 class="mt-3 font-heading text-5xl md:text-6xl">From first draft to live RSVPs in three steps</h1>
      </section>

      <Steps />

      <SplitBand />

      <section class="mk-reveal rounded-3xl border border-soft bg-white px-6 py-12 shadow-soft md:px-10">
        <h2 class="font-heading text-4xl">Invite code and QR explained</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
          <article class="rounded-2xl border border-soft bg-wedding-offwhite/70 p-5">
            <p class="mk-kicker">1</p>
            <h3 class="mt-2 font-heading text-2xl">Create parties</h3>
            <p class="mt-2 text-sm text-wedding-muted">Each household gets its own invite code.</p>
          </article>
          <article class="rounded-2xl border border-soft bg-wedding-offwhite/70 p-5">
            <p class="mk-kicker">2</p>
            <h3 class="mt-2 font-heading text-2xl">Share your QR</h3>
            <p class="mt-2 text-sm text-wedding-muted">Guests scan and go straight to your RSVP flow.</p>
          </article>
          <article class="rounded-2xl border border-soft bg-wedding-offwhite/70 p-5">
            <p class="mk-kicker">3</p>
            <h3 class="mt-2 font-heading text-2xl">Collect responses</h3>
            <p class="mt-2 text-sm text-wedding-muted">Track attendance, meal choices, and notes in one place.</p>
          </article>
        </div>
      </section>
    </div>
  </MarketingLayout>
</template>

<script setup>
import MarketingLayout from '../../components/marketing/MarketingLayout.vue';
import SplitBand from '../../components/marketing/SplitBand.vue';
import Steps from '../../components/marketing/Steps.vue';
</script>
