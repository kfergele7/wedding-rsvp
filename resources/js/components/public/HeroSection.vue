<template>
    <section v-if="isModern" class="relative overflow-hidden bg-[var(--modern-cream)] py-12 md:py-16">
        <div class="site-shell">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-[11px] uppercase tracking-[0.28em] text-[var(--modern-mauve-dark)]">{{ content.kicker }}</p>
                <h1 class="mt-3 text-4xl font-semibold uppercase tracking-[0.16em] text-[var(--modern-ink)] md:text-6xl">{{ content.names }}</h1>
                <p class="mt-3 text-xs uppercase tracking-[0.22em] text-[var(--modern-taupe)]">{{ content.dateLine }}</p>
            </div>

            <figure class="modern-image-frame modern-image-frame--hero mx-auto mt-12 max-w-5xl overflow-hidden border border-[color:var(--wedding-card-border-color)] bg-[var(--wedding-card-background)] p-3 shadow-soft md:mt-14 md:p-4">
                <img :src="content.image" alt="Couple in garden" class="aspect-[16/10] h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
            </figure>

            <article class="mx-auto mt-10 max-w-2xl text-center">
                <p class="text-sm uppercase tracking-[0.2em] text-[var(--modern-mauve-dark)]">{{ content.locationLine }}</p>
                <a href="/rsvp" class="button-dark mt-7 px-10 py-5 text-sm" @click.prevent="$emit('open-rsvp')">{{ content.buttonLabel }}</a>
            </article>
        </div>
    </section>

    <section v-else class="relative min-h-[84vh] overflow-hidden text-white">
        <img :src="content.image" alt="Couple in garden" class="absolute inset-0 h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
        <div class="absolute inset-0 bg-black/45"></div>

        <div class="site-shell relative flex min-h-[84vh] items-center justify-center py-24 text-center">
            <div class="mx-auto w-full max-w-6xl">
                <p class="mb-10 text-sm uppercase tracking-luxe">{{ content.kicker }}</p>
                <h1 class="font-heading text-7xl leading-tight md:text-9xl md:whitespace-nowrap">{{ content.names }}</h1>
                <p class="mt-10 font-heading text-3xl md:text-4xl">{{ content.dateLine }}</p>
                <p class="mt-3 text-sm uppercase tracking-[0.2em] text-white/80">{{ content.locationLine }}</p>
                <a href="/rsvp" class="button-primary mt-12 px-10 py-5 text-sm" @click.prevent="$emit('open-rsvp')">{{ content.buttonLabel }}</a>
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
defineEmits(['open-rsvp']);

const isModern = computed(() => props.layout === 'modern');
const imagePosition = computed(() => `${props.content.imageFocusX ?? 50}% ${props.content.imageFocusY ?? 50}%`);
</script>
