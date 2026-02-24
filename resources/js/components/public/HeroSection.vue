<template>
    <section class="relative min-h-[84vh] overflow-hidden text-white">
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
});
defineEmits(['open-rsvp']);

const imagePosition = computed(() => `${props.content.imageFocusX ?? 50}% ${props.content.imageFocusY ?? 50}%`);
</script>
