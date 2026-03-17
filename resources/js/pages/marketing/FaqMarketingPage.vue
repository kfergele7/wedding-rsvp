<template>
  <MarketingLayout>
    <div class="mx-auto w-full max-w-[1180px] space-y-14 px-4 md:space-y-20">
      <section class="mk-reveal">
        <p class="mk-kicker">FAQ</p>
        <h1 class="mt-3 font-heading text-5xl md:text-6xl">Everything couples ask before publishing</h1>
      </section>

      <section class="mk-reveal space-y-8">
        <div v-for="group in groups" :key="group.title" class="rounded-3xl border border-soft bg-white px-6 py-10 shadow-soft md:px-10">
          <h2 class="font-heading text-3xl">{{ group.title }}</h2>
          <div class="mt-4 space-y-3">
            <details v-for="(item, index) in group.items" :key="item.q" :open="index === 0" class="group rounded-2xl border border-soft bg-wedding-offwhite/60 p-5 open:bg-wedding-offwhite">
              <summary class="flex cursor-pointer list-none items-start justify-between gap-3 text-left">
                <span class="font-heading text-2xl text-wedding-black">{{ item.q }}</span>
                <span class="material-symbols-outlined text-wedding-band transition group-open:rotate-45">add</span>
              </summary>
              <p class="mt-3 text-sm leading-relaxed text-wedding-muted">{{ item.a }}</p>
            </details>
          </div>
        </div>
      </section>

      <FinalCTA />
    </div>
  </MarketingLayout>
</template>

<script setup>
import FinalCTA from '../../components/marketing/FinalCTA.vue';
import MarketingLayout from '../../components/marketing/MarketingLayout.vue';

const props = defineProps({
  payload: {
    type: Object,
    default: () => ({}),
  },
});

const groups = props.payload?.faqGroups || [];
</script>
