<template>
    <div :style="themeVars">
        <div v-if="previewBanner" ref="previewBannerRef" class="fixed inset-x-0 top-0 z-50 border-b border-[#466369] bg-[#F2ECE3]">
            <div class="site-shell flex flex-wrap items-center justify-between gap-3 py-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#22363a]">{{ previewBanner.title }}</p>
                    <p class="mt-1 text-sm text-[#22363a]">{{ previewBanner.message }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        v-if="previewBanner.accountUrl"
                        :href="previewBanner.accountUrl"
                        class="button-primary preview-account-button px-4 py-2"
                    >
                        {{ previewBanner.accountLabel || 'Account' }}
                    </a>
                    <a
                        v-if="previewBanner.mode === 'customer'"
                        :href="previewBanner.subscribeUrl || '/app'"
                        class="button-dark px-4 py-2"
                    >
                        {{ previewBanner.subscribeLabel || 'Subscribe now' }}
                    </a>
                </div>
            </div>
        </div>
        <div v-if="previewBanner" :style="{ height: `${previewBannerHeight}px` }"></div>

        <HeroSection :content="content.hero" @open-rsvp="openRsvpModal" />

        <main>
            <WelcomeSection v-if="sectionVisibility.welcome" :content="content.welcome" />
            <TimelineSection v-if="sectionVisibility.timeline" :content="content.timeline" :primary-color="content.theme.primary_color" />
            <StorySection v-if="sectionVisibility.story" :content="content.story" />
            <DetailsSection
                :content="content.details"
                :rsvp-settings="rsvpSettings"
                :primary-color="content.theme.primary_color"
                :section-visibility="sectionVisibility"
            />
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
            :public-slug="payload.publicSlug || ''"
            :rsvp-settings-payload="rsvpSettings"
            @close="closeRsvpModal"
        />
    </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
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
        heading: 'Wedding Timeline',
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
    section_visibility: {
        welcome: true,
        story: true,
        timeline: true,
        venue: true,
        travel: true,
        menu: true,
        faqs: true,
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
        section_visibility: { ...fallbackContent.section_visibility, ...(incoming.section_visibility || {}) },
    };
});

const sectionVisibility = computed(() => content.value.section_visibility || fallbackContent.section_visibility);
const previewBanner = computed(() => props.payload?.previewBanner || null);
const previewBannerRef = ref(null);
const previewBannerHeight = ref(0);

const themeVars = computed(() => ({
    '--wedding-button-color': content.value.theme.button_color || fallbackContent.theme.button_color,
}));
const isRsvpModalOpen = ref(Boolean(props.payload?.openRsvpModal));
const rsvpInitialCode = ref(props.payload?.rsvpCode || '');
const currentYear = new Date().getFullYear();

function updatePreviewBannerHeight() {
    previewBannerHeight.value = previewBannerRef.value?.offsetHeight || 0;
}

onMounted(() => {
    updatePreviewBannerHeight();
    window.addEventListener('resize', updatePreviewBannerHeight);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updatePreviewBannerHeight);
});

watch(previewBanner, async () => {
    await nextTick();
    updatePreviewBannerHeight();
});

const fallbackRsvpSettings = {
    meal_mode: 'options',
    menu_heading: 'Wedding Menu',
    menu_intro: 'We cannot wait to share a beautiful meal with you.',
    set_menu_description: 'A chef-curated set menu will be served for all attending guests.',
    menu_note_title: 'Dining Notes',
    menu_note_text: '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
    meal_options: [],
    menu_courses: [
        {
            id: 'starter',
            name: 'Starter',
            items: [
                {
                    title: 'Heirloom Tomato Tart',
                    description: 'Roasted heirloom tomatoes, whipped feta, and basil oil on crisp puff pastry.',
                },
            ],
        },
        {
            id: 'main',
            name: 'Main',
            items: [
                {
                    title: 'Seabass',
                    description: 'Pan seared seabass served on a bed of whipped mash with tender-stem broccoli and a white wine cream sauce.',
                },
            ],
        },
        {
            id: 'dessert',
            name: 'Dessert',
            items: [
                {
                    title: 'Lemon Posset',
                    description: 'Silky lemon posset with shortbread crumble and fresh berries.',
                },
            ],
        },
    ],
};

const rsvpSettings = computed(() => {
    const incoming = props.payload?.rsvpSettings || {};
    const mealMode = incoming.meal_mode || (incoming.meal_choices_enabled === false ? 'set_menu' : 'options');

    return {
        ...fallbackRsvpSettings,
        ...incoming,
        meal_mode: mealMode,
        meal_options: incoming.meal_options?.length ? incoming.meal_options : fallbackRsvpSettings.meal_options,
        menu_courses: normalizeMenuCourses(incoming.menu_courses),
    };
});

function openRsvpModal() {
    isRsvpModalOpen.value = true;
}

function closeRsvpModal() {
    isRsvpModalOpen.value = false;
}

function normalizeMenuCourses(courses) {
    const defaultNameKeys = new Set(['starter', 'main', 'dessert']);
    const seenDefaultKeys = new Set();
    const seenIds = new Set();

    if (Array.isArray(courses) && courses.length > 0) {
        const normalized = courses
            .map((course, index) => {
                const rawId = (course.id || '').toString().trim().toLowerCase();
                const name = (course.name || '').toString().trim();
                const nameKey = name.toLowerCase();
                const defaultKey = defaultNameKeys.has(rawId) ? rawId : (defaultNameKeys.has(nameKey) ? nameKey : null);
                const id = rawId || `course-${index}`;

                return {
                    id,
                    name: name || (defaultKey ? defaultKey.charAt(0).toUpperCase() + defaultKey.slice(1) : `Course ${index + 1}`),
                    defaultKey,
                    items: Array.isArray(course.items) ? course.items : [],
                };
            })
            .filter((course) => {
                if (course.defaultKey) {
                    if (seenDefaultKeys.has(course.defaultKey)) {
                        return false;
                    }
                    seenDefaultKeys.add(course.defaultKey);
                }

                if (seenIds.has(course.id)) {
                    return false;
                }
                seenIds.add(course.id);
                return true;
            })
            .map(({ defaultKey, ...course }) => course);

        if (normalized.length > 0) {
            return normalized;
        }
    }

    if (courses && typeof courses === 'object') {
        const legacyOrder = ['starter', 'main', 'dessert'];
        return legacyOrder.map((key) => ({
            id: key,
            name: key.charAt(0).toUpperCase() + key.slice(1),
            items: Array.isArray(courses[key]) ? courses[key] : [],
        }));
    }

    return fallbackRsvpSettings.menu_courses;
}
</script>
