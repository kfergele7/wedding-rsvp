<template>
  <MarketingLayout>
    <div class="mx-auto w-full max-w-[1180px] space-y-14 px-4 md:space-y-20">
      <section class="mk-reveal">
        <p class="mk-kicker">Features</p>
        <h1 class="mt-3 font-heading text-5xl md:text-6xl">Built for real wedding RSVP operations</h1>
        <p class="mt-4 max-w-3xl text-wedding-muted">One elegant template with practical workflows for guest management, privacy-first RSVPs, and planning exports.</p>
      </section>

      <FeatureGroups />

      <section class="mk-reveal rounded-3xl border border-soft bg-white px-6 py-12 shadow-soft md:px-10">
        <div class="grid gap-8 md:grid-cols-[1fr_1fr] md:items-center">
          <div>
            <h2 class="font-heading text-4xl">For older guests</h2>
            <p class="mt-4 text-wedding-muted">Guests can RSVP with a simple code and no account sign-up. If needed, you can record responses manually over the phone.</p>
          </div>
          <div class="overflow-hidden rounded-2xl border border-soft bg-wedding-offwhite p-2">
            <img :src="'/images/marketing/rsvp-flow.jpg'" alt="RSVP support for older guests" class="h-64 w-full rounded-xl object-cover" loading="lazy" />
          </div>
        </div>
      </section>

      <section class="mk-reveal rounded-3xl border border-soft bg-wedding-light/55 px-6 py-12 md:px-10">
        <div class="grid gap-8 md:grid-cols-[1fr_1fr] md:items-center">
          <div class="overflow-hidden rounded-2xl border border-soft bg-white p-2">
            <img :src="'/images/marketing/template-3.jpg'" alt="Privacy-first controls" class="h-64 w-full rounded-xl object-cover" loading="lazy" />
          </div>
          <div>
            <h2 class="font-heading text-4xl">Privacy-first RSVPs</h2>
            <p class="mt-4 text-wedding-muted">Invite-code access, site publish controls, and tenant-scoped data handling keep guest responses private and controlled.</p>
          </div>
        </div>
      </section>
    </div>
  </MarketingLayout>
</template>

<script setup>
import FeatureGroups from '../../components/marketing/FeatureGroups.vue';
import MarketingLayout from '../../components/marketing/MarketingLayout.vue';
</script>
