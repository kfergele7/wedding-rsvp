<template>
    <section class="section-gap" :class="{ 'bg-[var(--modern-blush)]': isModern }">
        <div class="site-shell grid gap-8 lg:grid-cols-2 lg:items-center" :class="{ 'lg:grid-cols-[0.9fr_1.1fr] lg:gap-20': isModern }">
            <article class="card-frame" :class="isModern ? '!border-transparent !bg-transparent !p-0 text-left !shadow-none' : 'text-center'">
                <h2 class="font-heading text-5xl uppercase tracking-[0.08em]" :class="{ 'text-3xl font-semibold tracking-[0.16em] text-[var(--modern-ink)] md:text-4xl': isModern }">{{ headingPrimary }}</h2>
                <p class="font-script text-5xl text-wedding-muted" :class="{ 'mt-2 !font-sans !text-2xl font-medium uppercase !tracking-[0.18em] !text-[var(--modern-mauve-dark)] md:!text-3xl': isModern }">{{ headingAccent }}</p>
                <div class="cms-rich mt-8 max-w-md leading-relaxed text-wedding-muted" :class="{ 'mx-auto': !isModern, '!text-[var(--modern-mauve-dark)]': isModern }" v-html="content.letter"></div>
                <p class="mt-8 font-heading text-3xl" :class="{ '!font-sans !text-xl uppercase !tracking-[0.18em] !text-[var(--modern-ink)]': isModern }">{{ content.signoff }}</p>
            </article>

            <div class="h-[430px] overflow-hidden border border-soft bg-white p-4 shadow-soft md:h-[520px]" :class="{ 'modern-image-frame modern-image-frame--welcome !border-0 !bg-transparent !p-0 !shadow-none md:h-[560px]': isModern }">
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
    layout: {
        type: String,
        default: 'classic',
    },
});

const isModern = computed(() => props.layout === 'modern');
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
