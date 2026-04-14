<template>
    <section class="py-20 md:py-24" :style="{ backgroundColor: primaryColor, color: textColor }">
        <div class="site-shell text-center">
            <h2 class="section-heading">{{ content.heading }}</h2>
            <p class="mt-3 font-script text-5xl" :style="{ color: mutedTextColor }">{{ content.dateAccent }}</p>

            <div class="timeline-items-grid mt-12 grid gap-4" :style="timelineGridStyle">
                <article
                    v-for="(item, index) in content.items"
                    :key="`${item.time || ''}-${item.title || ''}-${index}`"
                    class="px-5 py-7 text-left md:px-4 lg:px-5"
                    :style="{ border: `1px solid ${panelBorderColor}`, backgroundColor: panelBackgroundColor }"
                >
                    <p class="font-heading text-xl" :style="{ color: mutedTextColor }">{{ item.time }}</p>
                    <h3 class="mt-3 font-heading text-2xl uppercase tracking-wide">{{ item.title }}</h3>
                    <p class="mt-3 text-sm leading-relaxed" :style="{ color: softTextColor }">{{ item.description }}</p>
                </article>
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
    primaryColor: {
        type: String,
        default: '#22363A',
    },
});

const hasLightBackground = computed(() => isLightColour(props.primaryColor));
const textColor = computed(() => (hasLightBackground.value ? '#0F1B1D' : '#FFFFFF'));
const mutedTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.72)' : 'rgba(255, 255, 255, 0.75)'));
const softTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.82)' : 'rgba(255, 255, 255, 0.8)'));
const panelBorderColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.16)' : 'rgba(255, 255, 255, 0.25)'));
const panelBackgroundColor = computed(() => (hasLightBackground.value ? 'rgba(255, 255, 255, 0.55)' : 'rgba(255, 255, 255, 0.05)'));
const timelineGridStyle = computed(() => {
    const itemCount = Math.min(Math.max(props.content?.items?.length || 1, 1), 5);

    return {
        '--timeline-desktop-columns': `repeat(${itemCount}, minmax(0, 1fr))`,
    };
});

function isLightColour(hex) {
    const normalized = (hex || '').replace('#', '').trim();
    if (!/^[0-9a-fA-F]{6}$/.test(normalized)) {
        return false;
    }

    const r = parseInt(normalized.slice(0, 2), 16);
    const g = parseInt(normalized.slice(2, 4), 16);
    const b = parseInt(normalized.slice(4, 6), 16);
    const luminance = (0.299 * r) + (0.587 * g) + (0.114 * b);

    return luminance > 160;
}
</script>

<style scoped>
.timeline-items-grid {
    grid-template-columns: 1fr;
}

@media (min-width: 640px) {
    .timeline-items-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .timeline-items-grid {
        grid-template-columns: var(--timeline-desktop-columns);
    }
}
</style>
