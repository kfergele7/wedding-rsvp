<template>
    <section class="py-20 md:py-24" :class="isModern ? 'bg-[var(--modern-mauve)] py-24 md:py-32' : 'bg-[#22363A]'">
        <div class="site-shell">
            <div class="p-8 text-center text-white shadow-soft md:p-10" :class="{ 'rounded-[2rem] border-white/25 bg-[rgba(250,247,243,0.2)] md:p-12': isModern }" :style="ctaPanelStyle">
                <p v-if="isModern" class="text-xs uppercase tracking-[0.24em] text-white/75">RSVP</p>
                <h2 class="section-heading" :class="{ 'modern-cta-heading mt-4': isModern }">{{ content.title }}</h2>
                <div class="cms-rich mx-auto mt-4 max-w-2xl leading-relaxed" :style="{ color: softTextColor }" v-html="content.text"></div>
                <a href="/rsvp" class="footer-rsvp-button button-dark mt-8" @click.prevent="$emit('open-rsvp')">{{ content.buttonLabel }}</a>
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
defineEmits(['open-rsvp']);

const isModern = computed(() => props.layout === 'modern');
const hasLightBackground = computed(() => isLightColour(props.primaryColor));
const textColor = computed(() => (hasLightBackground.value ? '#0F1B1D' : '#FFFFFF'));
const softTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.82)' : 'rgba(255, 255, 255, 0.8)'));
const panelBorderColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.16)' : 'rgba(255, 255, 255, 0.25)'));
const ctaPanelStyle = computed(() => {
    if (isModern.value) {
        return {
            border: '1px solid rgba(255, 255, 255, 0.25)',
            backgroundColor: 'rgba(250, 247, 243, 0.2)',
        };
    }

    return {
        border: `1px solid ${panelBorderColor.value}`,
        backgroundColor: '#466369',
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
.footer-rsvp-button:hover {
    border-color: #0F1B1D;
    background-color: #0F1B1D;
}
</style>
