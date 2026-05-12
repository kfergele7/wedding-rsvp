<template>
    <section v-if="isModern" class="bg-[var(--modern-blush)] py-20 md:py-28">
        <div class="site-shell space-y-24 md:space-y-32">
            <div v-if="showVenueTravelBlock" class="grid items-center gap-12 lg:grid-cols-[0.92fr_1.08fr] lg:gap-20">
                <div class="space-y-10">
                    <article v-if="sectionVisibility.venue" class="rounded-[1.75rem] border border-[rgba(143,115,123,0.16)] bg-[rgba(250,247,243,0.62)] p-8 md:p-10">
                        <p class="text-xs uppercase tracking-[0.24em] text-[var(--modern-mauve-dark)]">Venue</p>
                        <h2 class="mt-4 text-3xl font-semibold uppercase tracking-[0.14em] text-[var(--modern-ink)]">Venue & Details</h2>
                        <p class="mt-6 text-2xl font-semibold text-[var(--modern-mauve-dark)]">{{ content.venue.name }}</p>
                        <p class="mt-3 text-sm uppercase tracking-[0.18em] text-[var(--modern-taupe)]">{{ content.venue.address }}</p>
                        <div class="cms-rich mt-6 leading-relaxed text-[var(--modern-mauve-dark)]" v-html="content.venue.blurb"></div>
                    </article>

                    <article v-if="sectionVisibility.travel" class="rounded-[1.75rem] border border-[rgba(143,115,123,0.16)] bg-white/55 p-8 md:p-10">
                        <p class="text-xs uppercase tracking-[0.24em] text-[var(--modern-mauve-dark)]">Guest guide</p>
                        <h3 class="mt-4 text-2xl font-semibold uppercase tracking-[0.14em] text-[var(--modern-ink)]">Travel & Accommodation</h3>
                        <div class="cms-rich mt-5 leading-relaxed text-[var(--modern-mauve-dark)]" v-html="content.travel"></div>
                    </article>
                </div>

                <figure class="modern-image-frame modern-image-frame--details overflow-hidden">
                    <img :src="content.image" alt="Venue walkway" class="h-[520px] w-full object-cover md:h-[680px]" :style="{ objectPosition: imagePosition }" />
                </figure>
            </div>

            <section v-if="sectionVisibility.menu" class="rounded-[2rem] border border-[rgba(143,115,123,0.18)] bg-[var(--modern-cream)] p-8 md:p-12">
                <div class="max-w-3xl text-left">
                    <p class="text-xs uppercase tracking-[0.24em] text-[var(--modern-mauve-dark)]">Dining</p>
                    <h3 class="section-heading mt-4">{{ rsvpSettings.menu_heading }}</h3>
                    <div class="cms-rich mt-5 leading-relaxed text-[var(--modern-mauve-dark)]" v-html="rsvpSettings.menu_intro"></div>
                </div>

                <div class="mt-12 divide-y divide-[rgba(143,115,123,0.18)]">
                    <article
                        v-for="course in menuCourseSections"
                        :key="course.key"
                        class="py-8 first:pt-0 last:pb-0"
                    >
                        <p class="text-xs uppercase tracking-[0.24em] text-[var(--modern-taupe)]">{{ panelHeading({ type: 'course', label: course.label, items: course.items }) }}</p>
                        <div class="mt-5 grid gap-6 md:grid-cols-2">
                            <article v-for="(item, index) in course.items" :key="`${course.key}-${item.title}-${index}`" class="border-l border-[rgba(143,115,123,0.24)] pl-5">
                                <p class="text-xl font-semibold text-[var(--modern-ink)]">{{ item.title }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-[var(--modern-mauve-dark)]">{{ item.description }}</p>
                            </article>
                        </div>
                    </article>
                </div>

                <div v-if="kidsMenuItems.length > 0" class="mt-10 border-t border-[rgba(143,115,123,0.18)] pt-8">
                    <p class="text-xs uppercase tracking-[0.24em] text-[var(--modern-mauve-dark)]">Kids Menu</p>
                    <div class="mt-5 grid gap-6 md:grid-cols-2">
                        <article v-for="(item, index) in kidsMenuItems" :key="`kids-menu-${index}-${item.title}`" class="border-l border-[rgba(143,115,123,0.24)] pl-5">
                            <p class="text-xl font-semibold text-[var(--modern-ink)]">{{ item.title }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-[var(--modern-mauve-dark)]">{{ item.description }}</p>
                        </article>
                    </div>
                </div>

                <article class="mt-10 rounded-[1.5rem] border border-[rgba(143,115,123,0.18)] bg-white/65 p-6 md:p-8">
                    <p class="text-xs uppercase tracking-[0.24em] text-[var(--modern-taupe)]">Additional Information</p>
                    <h4 class="mt-3 text-xl font-semibold text-[var(--modern-ink)]">{{ rsvpSettings.menu_note_title || 'Dining Notes' }}</h4>
                    <div class="cms-rich mt-3 text-sm leading-relaxed text-[var(--modern-mauve-dark)]" v-html="rsvpSettings.menu_note_text"></div>
                </article>
            </section>

            <section v-if="sectionVisibility.faqs" class="rounded-[2rem] border border-[rgba(143,115,123,0.26)] bg-white/85 p-8 shadow-[0_18px_46px_rgba(49,40,42,0.06)] md:p-12">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs uppercase tracking-[0.24em] text-[var(--modern-mauve-dark)]">Good to know</p>
                    <h3 class="section-heading mt-4">FAQ</h3>
                </div>
                <div class="mt-10 grid gap-5 md:grid-cols-2">
                    <article v-for="faq in content.faqs" :key="faq.question" class="rounded-[1.5rem] border border-[rgba(143,115,123,0.16)] bg-white/80 p-6">
                        <h4 class="text-lg font-semibold text-[var(--modern-ink)]">{{ faq.question }}</h4>
                        <div class="cms-rich mt-3 leading-relaxed text-[var(--modern-mauve-dark)]" v-html="faq.answer"></div>
                    </article>
                </div>
            </section>
        </div>
    </section>

    <section v-else class="py-20 md:py-28" style="background-color: var(--wedding-soft-background);">
        <div class="site-shell space-y-20 md:space-y-28" :class="{ 'md:space-y-32': isModern }">
            <div v-if="showVenueTravelBlock" class="grid items-stretch gap-10 lg:grid-cols-[1.1fr_0.9fr]" :class="{ 'lg:grid-cols-[0.95fr_1.05fr] lg:gap-20': isModern }">
                <div class="space-y-8">
                    <article v-if="sectionVisibility.venue" class="card-frame" :class="{ '!border-transparent !bg-transparent !p-0 !shadow-none': isModern }">
                        <h2 class="font-heading text-4xl">Venue & Details</h2>
                        <p class="mt-4 font-heading text-2xl">{{ content.venue.name }}</p>
                        <p class="mt-2 text-sm uppercase tracking-[0.18em] text-wedding-muted">{{ content.venue.address }}</p>
                        <div class="cms-rich mt-5 leading-relaxed text-wedding-muted" v-html="content.venue.blurb"></div>
                    </article>

                    <article v-if="sectionVisibility.travel" class="card-frame" :class="{ '!border-transparent !bg-transparent !p-0 !shadow-none': isModern }">
                        <h3 class="font-heading text-3xl">Travel & Accommodation</h3>
                        <div class="cms-rich mt-5 leading-relaxed text-wedding-muted" v-html="content.travel"></div>
                    </article>
                </div>

                <div class="h-full overflow-hidden border border-soft bg-white p-4 shadow-soft" :class="{ '!border-0 !bg-transparent !p-0 !shadow-none min-h-[520px]': isModern }">
                    <img :src="content.image" alt="Venue walkway" class="h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
                </div>
            </div>

            <div v-if="sectionVisibility.menu" class="p-8 shadow-soft md:p-10" :class="{ 'md:p-12': isModern }" :style="{ backgroundColor: primaryColor, border: `1px solid ${panelBorderColor}` }">
                <h3 class="font-heading text-3xl" :style="{ color: textColor }">{{ rsvpSettings.menu_heading }}</h3>
                <div class="cms-rich mt-4 leading-relaxed" :style="{ color: softTextColor }" v-html="rsvpSettings.menu_intro"></div>

                <div class="mt-8 grid gap-5 md:grid-cols-2">
                    <article
                        v-for="panel in menuPanels"
                        :key="panel.key"
                        class="h-full p-6"
                        :style="panelCardStyle(panel)"
                    >
                        <h4 class="text-xs font-semibold uppercase tracking-[0.22em]" :style="{ color: mutedTextColor }">{{ panelHeading(panel) }}</h4>
                        <div v-if="panel.type === 'course'" class="mt-4 space-y-4">
                            <div v-for="(item, index) in panel.items" :key="`${panel.key}-${item.title}`">
                                <p class="font-heading text-2xl leading-tight" :style="{ color: textColor }">{{ item.title }}</p>
                                <p class="mt-2 text-sm leading-relaxed" :style="{ color: softTextColor }">{{ item.description }}</p>
                                <hr
                                    v-if="showOptionDividers(panel, index)"
                                    class="mt-4 border-t"
                                    :style="{ borderColor: dividerColor }"
                                >
                            </div>
                        </div>
                        <div v-else class="mt-4">
                            <p class="font-heading text-2xl leading-tight" :style="{ color: textColor }">{{ rsvpSettings.menu_note_title || 'Dining Notes' }}</p>
                            <div class="cms-rich mt-2 text-sm leading-relaxed" :style="{ color: softTextColor }" v-html="rsvpSettings.menu_note_text"></div>
                        </div>
                    </article>
                </div>

                <div v-if="kidsMenuItems.length > 0" class="mt-8 border-t pt-8" :style="{ borderColor: dividerColor }">
                    <h4 class="font-heading text-3xl" :style="{ color: textColor }">Kids Menu</h4>
                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <article
                            v-for="(item, index) in kidsMenuItems"
                            :key="`kids-menu-${index}-${item.title}`"
                            class="h-full p-6"
                            :style="{ border: `1px solid ${panelBorderColor}`, backgroundColor: panelBackgroundColor }"
                        >
                            <p class="font-heading text-2xl leading-tight" :style="{ color: textColor }">{{ item.title }}</p>
                            <p class="mt-2 text-sm leading-relaxed" :style="{ color: softTextColor }">{{ item.description }}</p>
                        </article>
                    </div>
                </div>
            </div>

            <div v-if="sectionVisibility.faqs" class="card-frame" :class="{ 'bg-white/75 md:p-12': isModern }">
                <h3 class="font-heading text-3xl">FAQ</h3>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div v-for="faq in content.faqs" :key="faq.question" class="border border-soft bg-white/80 p-5">
                        <h4 class="font-heading text-2xl">{{ faq.question }}</h4>
                        <div class="cms-rich mt-2 leading-relaxed text-wedding-muted" v-html="faq.answer"></div>
                    </div>
                </div>
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
    rsvpSettings: {
        type: Object,
        required: true,
    },
    primaryColor: {
        type: String,
        default: '#22363A',
    },
    sectionVisibility: {
        type: Object,
        default: () => ({
            venue: true,
            travel: true,
            menu: true,
            faqs: true,
        }),
    },
    layout: {
        type: String,
        default: 'classic',
    },
});

const isModern = computed(() => props.layout === 'modern');
const imagePosition = computed(() => `${props.content.imageFocusX ?? 50}% ${props.content.imageFocusY ?? 50}%`);
const showVenueTravelBlock = computed(() => props.sectionVisibility.venue || props.sectionVisibility.travel);
const hasLightBackground = computed(() => isLightColour(props.primaryColor));
const textColor = computed(() => (hasLightBackground.value ? '#0F1B1D' : '#FFFFFF'));
const mutedTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.72)' : 'rgba(255, 255, 255, 0.75)'));
const softTextColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.82)' : 'rgba(255, 255, 255, 0.8)'));
const panelBorderColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.16)' : 'rgba(255, 255, 255, 0.25)'));
const panelBackgroundColor = computed(() => (hasLightBackground.value ? 'rgba(255, 255, 255, 0.68)' : 'rgba(255, 255, 255, 0.1)'));
const notesBorderColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.22)' : 'rgba(255, 255, 255, 0.36)'));
const notesBackgroundColor = computed(() => (hasLightBackground.value ? '#F7F7F7' : 'rgba(247, 247, 247, 0.18)'));
const dividerColor = computed(() => (hasLightBackground.value ? 'rgba(15, 27, 29, 0.18)' : 'rgba(255, 255, 255, 0.25)'));
const menuCourseSections = computed(() => {
    const courses = props.rsvpSettings.menu_courses || [];

    if (Array.isArray(courses)) {
        return courses
            .map((course, index) => ({
                key: course.id || `course-${index}`,
                label: course.name || `Course ${index + 1}`,
                items: Array.isArray(course.items) ? course.items : [],
            }))
            .filter((course) => course.items.length > 0);
    }

    return ['starter', 'main', 'dessert']
        .map((key) => ({
            key,
            label: key.charAt(0).toUpperCase() + key.slice(1),
            items: Array.isArray(courses[key]) ? courses[key] : [],
        }))
        .filter((course) => course.items.length > 0);
});

const menuPanels = computed(() => {
    const courses = menuCourseSections.value.map((course) => ({
        key: course.key,
        label: course.label,
        type: 'course',
        items: course.items,
    }));

    return [
        ...courses,
        {
            key: 'notes',
            label: 'Additional Information',
            type: 'notes',
            items: [],
        },
    ];
});

const kidsMenuItems = computed(() => {
    if (!props.rsvpSettings.kids_menu_enabled) {
        return [];
    }

    return (Array.isArray(props.rsvpSettings.kids_menu_items) ? props.rsvpSettings.kids_menu_items : []).filter((item) => item?.title);
});

function panelHeading(panel) {
    if (panel.type !== 'course') {
        return panel.label;
    }

    if (props.rsvpSettings.meal_mode === 'options' && Array.isArray(panel.items) && panel.items.length > 1) {
        return `${panel.label} Options`;
    }

    return panel.label;
}

function showOptionDividers(panel, index) {
    return panel.type === 'course'
        && props.rsvpSettings.meal_mode === 'options'
        && index < panel.items.length - 1;
}

function panelCardStyle(panel) {
    if (panel.type === 'notes') {
        return {
            border: `1px solid ${notesBorderColor.value}`,
            backgroundColor: notesBackgroundColor.value,
        };
    }

    return {
        border: `1px solid ${panelBorderColor.value}`,
        backgroundColor: panelBackgroundColor.value,
    };
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
