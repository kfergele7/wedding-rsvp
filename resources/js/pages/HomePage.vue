<template>
    <div :style="themeVars">
        <HeroSection :content="content.hero" @open-rsvp="openRsvpModal" />

        <main>
            <WelcomeSection :content="content.welcome" />
            <TimelineSection :content="content.timeline" :primary-color="content.theme.primary_color" />
            <StorySection :content="content.story" />
            <DetailsSection :content="content.details" :rsvp-settings="rsvpSettings" :primary-color="content.theme.primary_color" />
            <RsvpCtaSection :content="content.cta" :primary-color="content.theme.primary_color" @open-rsvp="openRsvpModal" />
        </main>

        <footer class="bg-[#0f1b1d] py-[5px] text-white/85">
            <div class="site-shell flex items-center justify-between text-xs uppercase tracking-[0.12em]">
                <span>&copy; Copyright {{ currentYear }}</span>
                <span>
                    Built by
                    <a href="https://elementseven.co/" target="_blank" rel="noopener noreferrer" class="ml-1 text-white underline-offset-2 hover:underline">
                        Element Seven
                    </a>
                </span>
            </div>
        </footer>

        <RsvpModal
            v-if="isRsvpModalOpen"
            :initial-code="rsvpInitialCode"
            :rsvp-settings-payload="rsvpSettings"
            @close="closeRsvpModal"
        />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import HeroSection from '../components/public/HeroSection.vue';
import WelcomeSection from '../components/public/WelcomeSection.vue';
import TimelineSection from '../components/public/TimelineSection.vue';
import StorySection from '../components/public/StorySection.vue';
import DetailsSection from '../components/public/DetailsSection.vue';
import RsvpCtaSection from '../components/public/RsvpCtaSection.vue';
import RsvpModal from '../components/public/RsvpModal.vue';

const props = defineProps({
    payload: {
        type: Object,
        default: () => ({}),
    },
});

const fallbackContent = {
    hero: {
        kicker: "We're Getting Married",
        names: 'Sabrina & Kevin',
        dateLine: 'September 12, 2026',
        locationLine: 'Willow Creek Estate, City, State',
        buttonLabel: 'RSVP Soon',
        image: '/images/wedding/hero-couple.svg',
        imageFocusX: 50,
        imageFocusY: 50,
    },
    welcome: {
        heading: 'Dear Family & Friends',
        letter:
            "We're deeply grateful to celebrate this day with the people who have shaped our lives. This website will share all wedding details as the date gets closer, and we can’t wait to make lasting memories together.",
        signoff: 'Sabrina & Kevin',
        image: '/images/wedding/welcome-couple.svg',
        imageFocusX: 50,
        imageFocusY: 50,
    },
    timeline: {
        heading: 'The Big Day',
        dateAccent: 'September 12, 2026',
        items: [
            { time: '3:30 PM', title: 'Ceremony', description: 'Join us as we say “I do.”' },
            { time: '4:00 PM', title: 'Photos', description: 'Family portraits and couple photos.' },
            { time: '5:00 PM', title: 'Cocktails', description: 'Sip, mingle, and toast with us.' },
            { time: '6:00 PM', title: 'Dinner', description: 'Dinner and dancing to follow.' },
        ],
    },
    story: {
        heading: 'Our Story',
        accent: 'March 2016',
        text: 'Our paths crossed at a mutual friend’s gathering, and what began as a simple hello became years of laughter, support, and adventure. Through every season of life, we have built a home in each other. We are so excited for this next chapter and thankful to share it with everyone we love.',
        image: '/images/wedding/story-couple.svg',
        imageFocusX: 50,
        imageFocusY: 50,
    },
    details: {
        venue: {
            name: 'Willow Creek Estate',
            address: '1024 Garden Lane, City, State 12345',
            blurb: 'Ceremony and reception will both be held onsite. Please arrive 20 minutes early for seating.',
        },
        travel:
            'For guests traveling in, we recommend staying near Downtown City. A room block is reserved at The Magnolia House and Riverfront Hotel through May 20, 2027.',
        faqs: [
            {
                question: 'Is there a dress code?',
                answer: 'Formal attire requested. Think cocktail dresses and suits.',
            },
            {
                question: 'Can I bring a plus one?',
                answer: 'Your invitation indicates the number of seats reserved for your party.',
            },
            {
                question: 'Are children invited?',
                answer: 'We adore your little ones, but this will be an adults-focused evening.',
            },
        ],
        image: '/images/wedding/venue-map.svg',
        imageFocusX: 50,
        imageFocusY: 50,
    },
    cta: {
        title: 'Ready to Celebrate With Us?',
        text: 'RSVP online in just a few moments using your invitation code.',
        buttonLabel: 'Go to RSVP',
    },
    theme: {
        primary_color: '#22363A',
        button_color: '#22363A',
    },
};

const content = computed(() => {
    const incoming = props.payload?.content || {};

    return {
        ...fallbackContent,
        ...incoming,
        hero: { ...fallbackContent.hero, ...(incoming.hero || {}) },
        welcome: { ...fallbackContent.welcome, ...(incoming.welcome || {}) },
        timeline: {
            ...fallbackContent.timeline,
            ...(incoming.timeline || {}),
            items: incoming.timeline?.items || fallbackContent.timeline.items,
        },
        story: { ...fallbackContent.story, ...(incoming.story || {}) },
        details: {
            ...fallbackContent.details,
            ...(incoming.details || {}),
            venue: { ...fallbackContent.details.venue, ...(incoming.details?.venue || {}) },
            faqs: incoming.details?.faqs || fallbackContent.details.faqs,
        },
        cta: { ...fallbackContent.cta, ...(incoming.cta || {}) },
        theme: { ...fallbackContent.theme, ...(incoming.theme || {}) },
    };
});

const themeVars = computed(() => ({
    '--wedding-button-color': content.value.theme.button_color || fallbackContent.theme.button_color,
}));
const isRsvpModalOpen = ref(Boolean(props.payload?.openRsvpModal));
const rsvpInitialCode = ref(props.payload?.rsvpCode || '');
const currentYear = new Date().getFullYear();

const fallbackRsvpSettings = {
    meal_mode: 'options',
    menu_heading: 'Wedding Menu',
    menu_intro: 'We cannot wait to share a beautiful meal with you.',
    set_menu_description: 'A chef-curated set menu will be served for all attending guests.',
    menu_note_title: 'Dining Notes',
    menu_note_text: '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
    meal_options: [],
    menu_courses: {
        starter: [
            {
                title: 'Heirloom Tomato Tart',
                description: 'Roasted heirloom tomatoes, whipped feta, and basil oil on crisp puff pastry.',
            },
        ],
        main: [
            {
                title: 'Seabass',
                description: 'Pan seared seabass served on a bed of whipped mash with tender-stem broccoli and a white wine cream sauce.',
            },
        ],
        dessert: [
            {
                title: 'Lemon Posset',
                description: 'Silky lemon posset with shortbread crumble and fresh berries.',
            },
        ],
    },
};

const rsvpSettings = computed(() => {
    const incoming = props.payload?.rsvpSettings || {};
    const mealMode = incoming.meal_mode || (incoming.meal_choices_enabled === false ? 'set_menu' : 'options');

    return {
        ...fallbackRsvpSettings,
        ...incoming,
        meal_mode: mealMode,
        meal_options: incoming.meal_options?.length ? incoming.meal_options : fallbackRsvpSettings.meal_options,
        menu_courses: {
            starter: incoming.menu_courses?.starter || fallbackRsvpSettings.menu_courses.starter,
            main: incoming.menu_courses?.main || fallbackRsvpSettings.menu_courses.main,
            dessert: incoming.menu_courses?.dessert || fallbackRsvpSettings.menu_courses.dessert,
        },
    };
});

function openRsvpModal() {
    isRsvpModalOpen.value = true;
}

function closeRsvpModal() {
    isRsvpModalOpen.value = false;
}
</script>
