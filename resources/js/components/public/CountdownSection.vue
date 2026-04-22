<template>
    <section v-if="countdownReady" class="bg-[#22363A] py-20 md:py-24">
        <div class="site-shell">
            <div class="border border-white/20 bg-[#466369] px-8 py-10 text-center text-white shadow-soft md:px-10 md:py-12">
                <p class="text-xs uppercase tracking-[0.22em] text-white/70">Countdown</p>
                <h2 class="mt-3 font-heading text-4xl md:text-5xl">Until The Wedding Begins</h2>
                <p class="mt-4 text-white/80">
                    {{ countdownIntro }}
                </p>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <article
                        v-for="item in countdownItems"
                        :key="item.label"
                        class="rounded border border-white/20 bg-[#22363A] px-6 py-8 shadow-soft"
                    >
                        <p class="font-heading text-5xl md:text-6xl">{{ item.value }}</p>
                        <p class="mt-3 text-xs uppercase tracking-[0.22em] text-white/70">{{ item.label }}</p>
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
});

const now = ref(Date.now());
let timer = null;

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
</script>
