<template>
    <section class="py-20 text-white md:py-24" :style="{ backgroundColor: primaryColor }">
        <div class="site-shell">
            <div class="border border-white/25 bg-white/5 p-8 text-center shadow-soft md:p-10">
                <h2 class="section-heading">{{ content.title }}</h2>
                <div class="cms-rich mx-auto mt-4 max-w-2xl leading-relaxed text-white/80" v-html="content.text"></div>
                <a href="/rsvp" class="button-dark mt-8" @click.prevent="$emit('open-rsvp')">{{ content.buttonLabel }}</a>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
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
</script>
