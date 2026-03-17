<template>
  <section class="mk-reveal">
    <h2 class="font-heading text-4xl">Everything needed to launch and manage RSVPs</h2>
    <div class="mt-8 grid gap-5 xl:grid-cols-3">
      <article v-for="group in groups" :key="group.title" class="rounded-3xl border border-soft bg-white p-6 shadow-soft">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-soft bg-wedding-offwhite text-wedding-band">
            <span class="material-symbols-outlined" style="font-size: 18px">{{ group.icon }}</span>
          </span>
          <h3 class="font-heading text-3xl">{{ group.title }}</h3>
        </div>

        <ul class="mt-5 space-y-3">
          <li v-for="item in group.items" :key="item" class="mk-bullet">
            <span class="material-symbols-outlined">check_circle</span>
            <span>{{ item }}</span>
          </li>
        </ul>

        <div class="mt-6 overflow-hidden rounded-2xl border border-soft bg-wedding-offwhite p-2">
          <img :src="group.image" :alt="`${group.title} feature preview`" class="h-40 w-full rounded-xl object-cover" loading="lazy" />
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
const groups = [
  {
    title: 'Build',
    icon: 'edit_square',
    image: '/images/marketing/template-1.jpg',
    items: [
      'Form-based editor for names, dates, story, details, and FAQs',
      'Always-on-brand template with clean visual rhythm',
      'Customise details without breaking layout quality',
    ],
  },
  {
    title: 'Invite',
    icon: 'qr_code_2',
    image: '/images/marketing/collage-2.jpg',
    items: [
      'QR and invite-code access per household',
      'No guest accounts required to RSVP',
      'Privacy-first controls with draft and publish states',
    ],
  },
  {
    title: 'Manage',
    icon: 'groups',
    image: '/images/marketing/collage-3.jpg',
    items: [
      'Household guest lists with meal and dietary details',
      'Manual RSVP entry for phone call-ins',
      'CSV exports for seating and venue planning',
    ],
  },
];
</script>
