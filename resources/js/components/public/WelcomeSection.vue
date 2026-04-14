<template>
    <section class="section-gap">
        <div class="site-shell grid gap-8 lg:grid-cols-2 lg:items-center">
            <article class="card-frame text-center">
                <h2 class="font-heading text-5xl uppercase tracking-[0.08em]">{{ headingPrimary }}</h2>
                <p class="font-script text-5xl text-wedding-muted">{{ headingAccent }}</p>
                <div class="cms-rich mx-auto mt-8 max-w-md leading-relaxed text-wedding-muted" v-html="content.letter"></div>
                <p class="mt-8 font-heading text-3xl">{{ content.signoff }}</p>
            </article>

            <div class="h-[430px] overflow-hidden border border-soft bg-white p-4 shadow-soft md:h-[520px]">
                <img :src="content.image" alt="Engagement portrait" class="h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
});

const headingPrimary = computed(() => {
    const full = props.content?.heading || 'Dear Family & Friends';
    const parts = full.split(' ');
    return parts[0] || 'Dear';
});

const headingAccent = computed(() => {
    const full = props.content?.heading || 'Dear Family & Friends';
    const parts = full.split(' ');
    return parts.slice(1).join(' ') || 'Family & Friends';
});

const imagePosition = computed(() => `${props.content.imageFocusX ?? 50}% ${props.content.imageFocusY ?? 50}%`);
</script>
