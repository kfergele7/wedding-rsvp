<template>
    <section class="bg-[#22363A] py-20 md:py-24">
        <div class="site-shell">
            <div class="p-8 text-center text-white shadow-soft md:p-10" :style="{ border: `1px solid ${panelBorderColor}`, backgroundColor: '#466369' }">
                <h2 class="section-heading">{{ content.title }}</h2>
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
});
defineEmits(['open-rsvp']);

const hasLightBackground = computed(() => isLightColour(props.primaryColor));
const textColor = computed(() => (hasLightBackground.value ? '#0F1B1D' : '#FFFFFF'));
const softTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.82)' : 'rgba(255, 255, 255, 0.8)'));
const panelBorderColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.16)' : 'rgba(255, 255, 255, 0.25)'));

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
