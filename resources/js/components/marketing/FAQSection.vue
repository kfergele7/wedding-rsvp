<template>
  <section class="mk-reveal rounded-3xl border border-soft bg-white px-6 py-14 shadow-soft md:px-10">
    <div class="grid gap-8 lg:grid-cols-[0.75fr_1.25fr]">
      <aside>
        <p class="mk-kicker">Questions?</p>
        <h2 class="mt-3 font-heading text-4xl">Answers before you launch</h2>
        <p class="mt-4 text-wedding-muted">If you need help, contact us at support@magicinvitation.app.</p>
      </aside>

      <div class="space-y-3">
        <details v-for="(item, index) in items" :key="item.q" :open="index === 0" class="group rounded-2xl border border-soft bg-wedding-offwhite/60 p-5 open:bg-wedding-offwhite">
          <summary class="flex cursor-pointer list-none items-start justify-between gap-3 text-left">
            <span class="font-heading text-2xl text-wedding-black">{{ item.q }}</span>
            <span class="material-symbols-outlined text-wedding-band transition group-open:rotate-45">add</span>
          </summary>
          <p class="mt-3 text-sm leading-relaxed text-wedding-muted">{{ item.a }}</p>
        </details>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => [],
  },
});
</script>
