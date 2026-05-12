<template>
    <section v-if="isModern" class="py-20 md:py-28" :style="sectionStyle">
        <div class="site-shell grid gap-12 lg:grid-cols-[0.38fr_0.62fr] lg:gap-20">
            <div class="lg:sticky lg:top-24 lg:self-start">
                <p class="text-xs uppercase tracking-[0.24em]" :style="{ color: mutedTextColor }">The day</p>
                <h2 class="section-heading mt-4" :style="{ color: textColor }">{{ content.heading }}</h2>
                <p class="mt-5 text-sm uppercase tracking-[0.22em]" :style="{ color: mutedTextColor }">{{ content.dateAccent }}</p>
            </div>

            <div class="relative space-y-8 md:space-y-10">
                <span class="absolute left-[1.15rem] top-2 h-[calc(100%-1rem)] w-px" :style="{ backgroundColor: timelineLineColor }"></span>
                <article
                    v-for="(item, index) in content.items"
                    :key="`${item.time || ''}-${item.title || ''}-${index}`"
                    class="relative grid gap-5 pl-14 md:grid-cols-[9rem_1fr] md:gap-8"
                >
                    <span class="absolute left-0 top-1 flex h-9 w-9 items-center justify-center rounded-full border text-[13px] font-semibold" :style="timelineMarkerStyle">
                        {{ index + 1 }}
                    </span>
                    <p class="text-sm uppercase tracking-[0.2em]" :style="{ color: mutedTextColor }">{{ item.time }}</p>
                    <div class="rounded-[1.5rem] p-6" :style="timelinePanelStyle">
                        <h3 class="text-lg font-semibold uppercase tracking-[0.18em]" :style="{ color: textColor }">{{ item.title }}</h3>
                        <p class="mt-3 text-sm leading-relaxed" :style="{ color: softTextColor }">{{ item.description }}</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section v-else class="py-20 md:py-24" :style="sectionStyle">
        <div class="site-shell grid gap-12 lg:grid-cols-[0.38fr_0.62fr] lg:gap-20">
            <div class="text-center lg:sticky lg:top-24 lg:self-start lg:text-left">
                <p class="text-xs uppercase tracking-[0.24em]" :style="{ color: mutedTextColor }">The day</p>
                <h2 class="mt-4 font-heading text-5xl leading-tight md:text-6xl">{{ content.heading }}</h2>
                <p class="mt-5 font-script text-5xl" :style="{ color: mutedTextColor }">{{ content.dateAccent }}</p>
            </div>

            <div class="relative space-y-8 md:space-y-10">
                <span class="absolute left-[1.15rem] top-2 h-[calc(100%-1rem)] w-px" :style="{ backgroundColor: timelineLineColor }"></span>
                <article
                    v-for="(item, index) in content.items"
                    :key="`${item.time || ''}-${item.title || ''}-${index}`"
                    class="relative grid gap-5 pl-14 md:grid-cols-[9rem_1fr] md:gap-8"
                >
                    <span class="absolute left-0 top-1 flex h-9 w-9 items-center justify-center rounded-full border text-[13px] font-semibold" :style="timelineMarkerStyle">
                        {{ index + 1 }}
                    </span>
                    <p class="font-heading text-xl" :style="{ color: mutedTextColor }">{{ item.time }}</p>
                    <div class="p-6 shadow-soft" :style="timelinePanelStyle">
                        <h3 class="font-heading text-2xl uppercase tracking-wide">{{ item.title }}</h3>
                        <p class="mt-3 text-sm leading-relaxed" :style="{ color: softTextColor }">{{ item.description }}</p>
                    </div>
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
    layout: {
        type: String,
        default: 'classic',
    },
});

const isModern = computed(() => props.layout === 'modern');
const hasLightBackground = computed(() => isLightColour(props.primaryColor));
const textColor = computed(() => (hasLightBackground.value ? '#0F1B1D' : '#FFFFFF'));
const mutedTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.72)' : 'rgba(255, 255, 255, 0.75)'));
const softTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.78)' : 'rgba(255, 255, 255, 0.8)'));
const panelBorderColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.16)' : 'rgba(255, 255, 255, 0.25)'));
const panelBackgroundColor = computed(() => (hasLightBackground.value ? 'rgba(255, 255, 255, 0.55)' : 'rgba(255, 255, 255, 0.1)'));
const timelineLineColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.22)' : 'rgba(255, 255, 255, 0.28)'));
const sectionStyle = computed(() => ({
    backgroundColor: props.primaryColor,
    color: textColor.value,
}));
const timelinePanelStyle = computed(() => ({
    border: `1px solid ${panelBorderColor.value}`,
    backgroundColor: panelBackgroundColor.value,
}));
const timelineMarkerStyle = computed(() => ({
    borderColor: panelBorderColor.value,
    backgroundColor: hasLightBackground.value ? 'rgba(255, 255, 255, 0.65)' : 'rgba(255, 255, 255, 0.12)',
    color: hasLightBackground.value ? '#0F1B1D' : '#FFFFFF',
}));

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
