<template>
    <section class="section-gap story-section" :class="{ 'bg-[var(--modern-blush)]': isModern }">
        <div class="site-shell grid gap-8 lg:grid-cols-2 lg:items-center" :class="{ 'lg:grid-cols-[1fr_0.9fr] lg:gap-20': isModern }">
            <div class="h-[430px] overflow-hidden border border-soft bg-white p-4 shadow-soft md:h-[520px]" :class="{ 'modern-image-frame modern-image-frame--story !border-0 !bg-transparent !p-0 !shadow-none md:h-[540px]': isModern }">
                <img :src="content.image" alt="Couple closeup" class="h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
            </div>

            <article class="card-frame" :class="{ '!border-transparent !bg-transparent !p-0 !shadow-none': isModern }">
                <h2 class="section-heading">{{ content.heading }}</h2>
                <p class="mt-3 font-script text-5xl text-wedding-muted" :class="{ '!font-sans !text-sm uppercase !tracking-[0.22em] !text-[var(--modern-mauve-dark)]': isModern }">{{ content.accent }}</p>
                <div class="cms-rich mt-8 max-w-xl leading-relaxed text-wedding-muted" :class="{ '!text-[var(--modern-mauve-dark)]': isModern }" v-html="content.text"></div>
            </article>
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
const imagePosition = computed(() => `${props.content.imageFocusX ?? 50}% ${props.content.imageFocusY ?? 50}%`);
</script>
