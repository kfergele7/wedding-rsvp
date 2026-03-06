<template>
    <section class="pb-20 md:pb-28">
        <div class="site-shell space-y-8">
            <div v-if="showVenueTravelBlock" class="grid items-stretch gap-10 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="space-y-8">
                    <article v-if="sectionVisibility.venue" class="card-frame">
                        <h2 class="font-heading text-4xl">Venue & Details</h2>
                        <p class="mt-4 font-heading text-2xl">{{ content.venue.name }}</p>
                        <p class="mt-2 text-sm uppercase tracking-[0.18em] text-wedding-muted">{{ content.venue.address }}</p>
                        <div class="cms-rich mt-5 leading-relaxed text-wedding-muted" v-html="content.venue.blurb"></div>
                    </article>

                    <article v-if="sectionVisibility.travel" class="card-frame">
                        <h3 class="font-heading text-3xl">Travel & Accommodations</h3>
                        <div class="cms-rich mt-5 leading-relaxed text-wedding-muted" v-html="content.travel"></div>
                    </article>
                </div>

                <div class="h-full overflow-hidden border border-soft bg-white p-4 shadow-soft">
                    <img :src="content.image" alt="Venue walkway" class="h-full w-full object-cover" :style="{ objectPosition: imagePosition }" />
                </div>
            </div>

            <div v-if="sectionVisibility.menu" class="border border-white/20 p-8 shadow-soft md:p-10" :style="{ backgroundColor: primaryColor }">
                <h3 class="font-heading text-3xl text-white">{{ rsvpSettings.menu_heading }}</h3>
                <div class="cms-rich mt-4 leading-relaxed text-white/80" v-html="rsvpSettings.menu_intro"></div>

                <div class="mt-8 grid gap-5 md:grid-cols-2">
                    <article
                        v-for="panel in menuPanels"
                        :key="panel.key"
                        class="h-full border border-white/25 bg-white/10 p-6"
                    >
                        <h4 class="text-xs font-semibold uppercase tracking-[0.22em] text-white/75">{{ panelHeading(panel) }}</h4>
                        <div v-if="panel.type === 'course'" class="mt-4 space-y-4">
                            <div v-for="(item, index) in panel.items" :key="`${panel.key}-${item.title}`">
                                <p class="font-heading text-2xl leading-tight text-white">{{ item.title }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-white/80">{{ item.description }}</p>
                                <hr
                                    v-if="showOptionDividers(panel, index)"
                                    class="mt-4 border-t border-white/30"
                                >
                            </div>
                        </div>
                        <div v-else class="cms-rich mt-4 leading-relaxed text-white/80" v-html="rsvpSettings.menu_note_text"></div>
                    </article>
                </div>

                <div
                    v-if="rsvpSettings.meal_mode === 'set_menu'"
                    class="cms-rich mt-6 border border-white/25 bg-white/10 p-5 leading-relaxed text-white/80"
                    v-html="rsvpSettings.set_menu_description"
                ></div>
            </div>

            <div v-if="sectionVisibility.faqs" class="card-frame">
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
});

const imagePosition = computed(() => `${props.content.imageFocusX ?? 50}% ${props.content.imageFocusY ?? 50}%`);
const showVenueTravelBlock = computed(() => props.sectionVisibility.venue || props.sectionVisibility.travel);
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
    const coursePanels = menuCourseSections.value.map((course) => ({
        key: course.key,
        label: course.label,
        type: 'course',
        items: course.items,
    }));

    return [
        ...coursePanels,
        {
            key: 'notes',
            label: props.rsvpSettings.menu_note_title || 'Dining Notes',
            type: 'notes',
            items: [],
        },
    ];
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
</script>
