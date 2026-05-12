<template>
    <section v-if="countdownReady" class="py-20 md:py-24" :class="isModern ? 'py-24 md:py-32' : ''" :style="sectionStyle">
        <div class="site-shell">
            <div class="border px-8 py-10 text-center shadow-soft md:px-10 md:py-12" :class="isModern ? 'rounded-[2rem]' : ''" :style="panelStyle">
                <p class="text-xs uppercase tracking-[0.22em]" :style="{ color: mutedTextColor }">Countdown</p>
                <h2 class="mt-3 font-heading text-4xl md:text-5xl" :class="{ '!font-sans font-semibold uppercase tracking-[0.14em]': isModern }">Until The Wedding Begins</h2>
                <p class="mt-4" :style="{ color: softTextColor }">
                    {{ countdownIntro }}
                </p>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <article
                        v-for="item in countdownItems"
                        :key="item.label"
                        class="border px-6 py-8 shadow-soft"
                        :class="isModern ? 'rounded-[1.5rem]' : 'rounded'"
                        :style="countdownCardStyle"
                    >
                        <p class="font-heading text-5xl md:text-6xl" :class="{ '!font-sans font-semibold': isModern }">{{ item.value }}</p>
                        <p class="mt-3 text-xs uppercase tracking-[0.22em]" :style="{ color: mutedTextColor }">{{ item.label }}</p>
                    </article>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    targetDateTime: {
        type: String,
        default: '',
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

const now = ref(Date.now());
let timer = null;

const isModern = computed(() => props.layout === 'modern');
const hasLightBackground = computed(() => isLightColour(props.primaryColor));
const textColor = computed(() => (hasLightBackground.value ? '#0F1B1D' : '#FFFFFF'));
const mutedTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.62)' : 'rgba(255, 255, 255, 0.75)'));
const softTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.78)' : 'rgba(255, 255, 255, 0.85)'));
const panelSurface = computed(() => (hasLightBackground.value ? 'rgba(255, 255, 255, 0.5)' : 'rgba(255, 255, 255, 0.12)'));
const cardSurface = computed(() => (hasLightBackground.value ? 'rgba(255, 255, 255, 0.64)' : 'rgba(15, 27, 29, 0.16)'));
const borderColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.14)' : 'rgba(255, 255, 255, 0.25)'));
const sectionStyle = computed(() => ({
    backgroundColor: props.primaryColor,
    color: textColor.value,
}));
const panelStyle = computed(() => ({
    borderColor: borderColor.value,
    backgroundColor: panelSurface.value,
    color: textColor.value,
}));
const countdownCardStyle = computed(() => ({
    borderColor: borderColor.value,
    backgroundColor: cardSurface.value,
}));
const targetDate = computed(() => parseTargetDate(props.targetDateTime));
const countdownReady = computed(() => targetDate.value instanceof Date && !Number.isNaN(targetDate.value.getTime()));

const differenceMs = computed(() => {
    if (!countdownReady.value) {
        return 0;
    }

    return Math.max(targetDate.value.getTime() - now.value, 0);
});

const countdownParts = computed(() => {
    const totalMinutes = Math.floor(differenceMs.value / (1000 * 60));
    const days = Math.floor(totalMinutes / (60 * 24));
    const hours = Math.floor((totalMinutes % (60 * 24)) / 60);
    const minutes = totalMinutes % 60;

    return { days, hours, minutes };
});

const countdownItems = computed(() => ([
    { label: 'Days', value: padNumber(countdownParts.value.days) },
    { label: 'Hours', value: padNumber(countdownParts.value.hours) },
    { label: 'Minutes', value: padNumber(countdownParts.value.minutes) },
]));

const countdownIntro = computed(() => {
    if (!countdownReady.value) {
        return '';
    }

    if (differenceMs.value === 0) {
        return 'The wedding day has arrived.';
    }

    return `Only ${countdownParts.value.days} days, ${countdownParts.value.hours} hours, and ${countdownParts.value.minutes} minutes to go.`;
});

onMounted(() => {
    timer = window.setInterval(() => {
        now.value = Date.now();
    }, 30000);
});

onBeforeUnmount(() => {
    if (timer) {
        window.clearInterval(timer);
    }
});

function parseTargetDate(targetDateTime) {
    const trimmedDateTime = (targetDateTime || '').trim();
    if (!trimmedDateTime) {
        return null;
    }

    const parsedDateTime = new Date(trimmedDateTime);
    if (!Number.isNaN(parsedDateTime.getTime())) {
        return parsedDateTime;
    }

    const normalisedDateTime = trimmedDateTime.replace(' ', 'T');
    const parsedNormalisedDateTime = new Date(normalisedDateTime);
    if (!Number.isNaN(parsedNormalisedDateTime.getTime())) {
        return parsedNormalisedDateTime;
    }

    return null;
}

function padNumber(value) {
    return String(Math.max(0, value)).padStart(2, '0');
}

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
