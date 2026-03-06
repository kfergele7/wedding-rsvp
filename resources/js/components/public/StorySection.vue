<template>
    <section class="section-gap">
        <div class="site-shell grid gap-8 lg:grid-cols-2 lg:items-center">
            <div class="h-[430px] overflow-hidden border border-soft bg-white p-4 shadow-soft md:h-[520px]">
                <img :src="content.image" alt="Couple closeup" class="h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
            </div>

            <article class="card-frame">
                <h2 class="section-heading">{{ content.heading }}</h2>
                <p class="mt-1 font-script text-5xl text-wedding-muted">{{ content.accent }}</p>
                <div class="cms-rich mt-8 max-w-xl leading-relaxed text-wedding-muted" v-html="content.text"></div>
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
});

const imagePosition = computed(() => `${props.content.imageFocusX ?? 50}% ${props.content.imageFocusY ?? 50}%`);
</script>
