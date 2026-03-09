<template>
    <section class="py-20 text-white md:py-24" :style="{ backgroundColor: primaryColor }">
        <div class="site-shell text-center">
            <h2 class="section-heading">{{ content.heading }}</h2>
            <p class="mt-3 font-script text-5xl text-white/75">{{ content.dateAccent }}</p>

            <div class="timeline-items-grid mt-12 grid gap-4">
                <article
                    v-for="(item, index) in content.items"
                    :key="`${item.time || ''}-${item.title || ''}-${index}`"
                    class="border border-white/25 bg-white/5 px-6 py-8 text-left"
                >
                    <p class="font-heading text-xl text-white/70">{{ item.time }}</p>
                    <h3 class="mt-3 font-heading text-2xl uppercase tracking-wide">{{ item.title }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-white/80">{{ item.description }}</p>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    content: {
        type: Object,
        required: true,
    },
    primaryColor: {
        type: String,
        default: '#22363A',
    },
});
</script>

<style scoped>
.timeline-items-grid {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}
</style>
