<template>
    <div :class="['public-site', `public-layout-${selectedLayout}`]" :style="themeVars">
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

        <HeroSection :content="content.hero" :layout="selectedLayout" @open-rsvp="openRsvpModal" />

        <main :style="{ backgroundColor: activePalette.soft_background }">
            <WelcomeSection v-if="effectiveSectionVisibility.welcome" :content="content.welcome" :layout="selectedLayout" />
            <TimelineSection v-if="effectiveSectionVisibility.timeline" :content="content.timeline" :primary-color="effectivePrimaryColor" :layout="selectedLayout" />
            <StorySection v-if="effectiveSectionVisibility.story" :content="content.story" :layout="selectedLayout" />
            <DetailsSection
                :content="content.details"
                :rsvp-settings="rsvpSettings"
                :primary-color="effectivePrimaryColor"
                :section-visibility="effectiveSectionVisibility"
                :layout="selectedLayout"
            />
            <CountdownSection
                v-if="effectiveSectionVisibility.countdown"
                :target-date-time="content.countdown.targetDateTime"
                :primary-color="effectivePrimaryColor"
                :layout="selectedLayout"
            />
            <GallerySection v-if="effectiveSectionVisibility.gallery" :content="content.gallery" :layout="selectedLayout" />
            <RsvpCtaSection :content="content.cta" :primary-color="effectivePrimaryColor" :layout="selectedLayout" @open-rsvp="openRsvpModal" />
        </main>

        <div :style="{ backgroundColor: activePalette.dark }">
            <footer class="w-full py-[5px] text-white/85">
                <div class="site-shell flex items-center justify-center text-center text-[10px] uppercase tracking-[0.12em]">
                    <span>&copy; Copyright Magic Invitation {{ currentYear }}</span>
                </div>
            </footer>
        </div>

        <RsvpModal
            v-if="isRsvpModalOpen"
            :initial-code="rsvpInitialCode"
            :public-slug="payload.publicSlug || ''"
            :rsvp-settings-payload="rsvpSettings"
            @party-resolved="handleResolvedParty"
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
import CountdownSection from '../components/public/CountdownSection.vue';
import GallerySection from '../components/public/GallerySection.vue';
import RsvpCtaSection from '../components/public/RsvpCtaSection.vue';
import RsvpModal from '../components/public/RsvpModal.vue';

const props = defineProps({
    payload: {
        type: Object,
        default: () => ({}),
    },
});

const fallbackPaletteColours = {
    primary: '#22363A',
    secondary: '#466369',
    dark: '#0F1B1D',
    soft_background: '#F7F5F2',
    light: '#FFFFFF',
};

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
    countdown: {
        targetDateTime: '2026-09-12T15:30',
    },
    gallery: {
        heading: "Photo's of us across the years",
        items: [],
    },
    cta: {
        title: 'Ready to Celebrate With Us?',
        text: 'RSVP online in just a few moments using your invitation code.',
        buttonLabel: 'Go to RSVP',
    },
    theme: {
        palette: 'magic_classic',
        palette_colours: fallbackPaletteColours,
        primary_color: '#22363A',
        button_color: '#22363A',
        layout: 'classic',
    },
    section_visibility: {
        welcome: true,
        story: true,
        timeline: true,
        venue: true,
        travel: true,
        menu: true,
        faqs: true,
        countdown: true,
        gallery: true,
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
        countdown: { ...fallbackContent.countdown, ...(incoming.countdown || {}) },
        gallery: {
            ...fallbackContent.gallery,
            ...(incoming.gallery || {}),
            items: incoming.gallery?.items || fallbackContent.gallery.items,
        },
        cta: { ...fallbackContent.cta, ...(incoming.cta || {}) },
        theme: { ...fallbackContent.theme, ...(incoming.theme || {}) },
        section_visibility: { ...fallbackContent.section_visibility, ...(incoming.section_visibility || {}) },
    };
});

const sectionVisibility = computed(() => content.value.section_visibility || fallbackContent.section_visibility);
const resolvedGuestParty = ref(null);
const resolvedGuestType = computed(() => resolvedGuestParty.value?.guest_type || null);
const effectiveSectionVisibility = computed(() => {
    const baseVisibility = { ...sectionVisibility.value };

    if (resolvedGuestType.value !== 'evening') {
        return baseVisibility;
    }

    return {
        ...baseVisibility,
        welcome: false,
        story: false,
        timeline: false,
        menu: false,
        gallery: false,
    };
});
const previewBanner = computed(() => props.payload?.previewBanner || null);
const previewBannerRef = ref(null);
const previewBannerHeight = ref(0);
const previewLayout = computed(() => {
    if (typeof window === 'undefined') {
        return null;
    }

    const requestedLayout = new URLSearchParams(window.location.search).get('preview_layout');

    if (!requestedLayout) {
        return null;
    }

    const layout = normalizeLayout(requestedLayout);

    return ['classic', 'modern'].includes(layout) ? layout : null;
});
const selectedLayout = computed(() => {
    if (previewLayout.value) {
        return previewLayout.value;
    }

    const layout = normalizeLayout(content.value.theme.layout || fallbackContent.theme.layout || 'classic');

    return ['classic', 'modern'].includes(layout) ? layout : 'classic';
});

const activePalette = computed(() => normalizePaletteColours(content.value.theme.palette_colours));
const activePaletteSlug = computed(() => normalizePaletteSlug(content.value.theme.palette));
const usesGoldenButtons = computed(() => activePaletteSlug.value === 'champagne_silk');
const effectivePrimaryColor = computed(() => activePalette.value.primary);
const effectiveButtonColor = computed(() => usesGoldenButtons.value ? activePalette.value.secondary : activePalette.value.primary);
const effectiveButtonHoverColor = computed(() => usesGoldenButtons.value ? activePalette.value.primary : activePalette.value.secondary);

const themeVars = computed(() => {
    const buttonColor = effectiveButtonColor.value;
    const hoverColor = effectiveButtonHoverColor.value;

    return {
        '--wedding-palette-primary': activePalette.value.primary,
        '--wedding-palette-secondary': activePalette.value.secondary,
        '--wedding-palette-dark': activePalette.value.dark,
        '--wedding-soft-background': activePalette.value.soft_background,
        '--wedding-palette-light': activePalette.value.light,
        '--wedding-card-background': activePalette.value.light,
        '--wedding-card-border-color': withAlpha(activePalette.value.secondary, 0.34),
        '--modern-mauve': activePalette.value.primary,
        '--modern-mauve-dark': activePalette.value.secondary,
        '--modern-cream': activePalette.value.soft_background,
        '--modern-blush': washedOutColour(activePalette.value.soft_background, 0.18),
        '--modern-blush-deep': washedOutColour(activePalette.value.secondary, 0.42),
        '--modern-taupe': activePalette.value.secondary,
        '--modern-ink': activePalette.value.dark,
        '--modern-shape-primary': withAlpha(activePalette.value.secondary, 0.2),
        '--modern-shape-secondary': withAlpha(activePalette.value.primary, 0.14),
        '--modern-shape-tertiary': withAlpha(activePalette.value.dark, 0.08),
        '--wedding-button-color': buttonColor,
        '--wedding-button-hover-color': hoverColor,
        '--wedding-button-text-color': isLightColour(buttonColor) ? '#0F1B1D' : '#FFFFFF',
        '--wedding-button-hover-text-color': isLightColour(hoverColor) ? '#0F1B1D' : '#FFFFFF',
    };
});
const isRsvpModalOpen = ref(Boolean(props.payload?.openRsvpModal));
const rsvpInitialCode = ref(props.payload?.rsvpCode || '');
const currentYear = new Date().getFullYear();

function updatePreviewBannerHeight() {
    previewBannerHeight.value = previewBannerRef.value?.offsetHeight || 0;
}

onMounted(() => {
    updateDocumentTitle();
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

watch(
    () => [props.payload?.siteTitle, content.value.hero?.names],
    () => updateDocumentTitle(),
);

function updateDocumentTitle() {
    const title = (props.payload?.siteTitle || content.value.hero?.names || 'Magic Invitation').toString().trim();

    document.title = title || 'Magic Invitation';
}

function normalizeLayout(layout) {
    const normalized = (layout || 'classic').toString().trim().toLowerCase();

    if (normalized === 'editorial') {
        return 'modern';
    }

    return normalized;
}

function normalizePaletteSlug(palette) {
    return (palette || 'magic_classic').toString().trim().toLowerCase().replace(/[\s-]+/g, '_');
}

const fallbackRsvpSettings = {
    meal_mode: 'options',
    menu_heading: 'Wedding Menu',
    menu_intro: 'We cannot wait to share a beautiful meal with you.',
    set_menu_description: 'A chef-curated set menu will be served for all attending guests.',
    menu_note_title: 'Dining Notes',
    menu_note_text: '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
    meal_options: [],
    kids_menu_enabled: false,
    kids_menu_items: [],
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
        kids_menu_enabled: Boolean(incoming.kids_menu_enabled),
        kids_menu_items: Array.isArray(incoming.kids_menu_items) ? incoming.kids_menu_items : fallbackRsvpSettings.kids_menu_items,
        menu_courses: normalizeMenuCourses(incoming.menu_courses),
    };
});

function openRsvpModal() {
    isRsvpModalOpen.value = true;
}

function closeRsvpModal() {
    isRsvpModalOpen.value = false;
}

function handleResolvedParty(resolvedParty) {
    resolvedGuestParty.value = resolvedParty || null;
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

function normalizePaletteColours(colours) {
    return {
        primary: normalizeHexColour(colours?.primary, fallbackPaletteColours.primary),
        secondary: normalizeHexColour(colours?.secondary, fallbackPaletteColours.secondary),
        dark: normalizeHexColour(colours?.dark, fallbackPaletteColours.dark),
        soft_background: normalizeHexColour(colours?.soft_background, fallbackPaletteColours.soft_background),
        light: normalizeHexColour(colours?.light, fallbackPaletteColours.light),
    };
}

function normalizeHexColour(colour, fallback) {
    const normalized = String(colour || '').trim().toUpperCase();

    return /^#[0-9A-F]{6}$/.test(normalized) ? normalized : fallback;
}

function washedOutColour(hex, mixWithWhite = 0.24) {
    const normalized = (hex || '').replace('#', '').trim();

    if (!/^[0-9a-fA-F]{6}$/.test(normalized)) {
        return '#466369';
    }

    const red = parseInt(normalized.slice(0, 2), 16);
    const green = parseInt(normalized.slice(2, 4), 16);
    const blue = parseInt(normalized.slice(4, 6), 16);

    const washedRed = Math.round(red + ((255 - red) * mixWithWhite));
    const washedGreen = Math.round(green + ((255 - green) * mixWithWhite));
    const washedBlue = Math.round(blue + ((255 - blue) * mixWithWhite));

    return `#${[washedRed, washedGreen, washedBlue]
        .map((channel) => channel.toString(16).padStart(2, '0'))
        .join('')}`;
}

function withAlpha(hex, alpha = 0.18) {
    const normalized = (hex || '').replace('#', '').trim();

    if (!/^[0-9a-fA-F]{6}$/.test(normalized)) {
        return `rgba(70, 99, 105, ${alpha})`;
    }

    const red = parseInt(normalized.slice(0, 2), 16);
    const green = parseInt(normalized.slice(2, 4), 16);
    const blue = parseInt(normalized.slice(4, 6), 16);

    return `rgba(${red}, ${green}, ${blue}, ${alpha})`;
}

function isLightColour(hex) {
    const normalized = (hex || '').replace('#', '').trim();

    if (!/^[0-9a-fA-F]{6}$/.test(normalized)) {
        return false;
    }

    const red = parseInt(normalized.slice(0, 2), 16);
    const green = parseInt(normalized.slice(2, 4), 16);
    const blue = parseInt(normalized.slice(4, 6), 16);
    const luminance = (0.299 * red) + (0.587 * green) + (0.114 * blue);

    return luminance > 160;
}
</script>
