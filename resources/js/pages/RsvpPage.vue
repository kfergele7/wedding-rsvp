<template>
    <div class="min-h-screen bg-wedding-bg py-16" :style="themeVars">
        <div class="site-shell">
            <header class="mb-10 text-center">
                <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Wedding RSVP</p>
                <h1 class="mt-3 font-heading text-5xl">RSVP</h1>
                <p class="mx-auto mt-4 max-w-2xl text-wedding-muted">
                    Enter your RSVP code from the invitation to view and update your party response.
                </p>
            </header>

            <section class="card-frame mx-auto w-full max-w-3xl bg-white">
                <form class="flex flex-col gap-4 md:flex-row" @submit.prevent="lookupCode">
                    <input
                        v-model="codeInput"
                        type="text"
                        placeholder="Enter RSVP code"
                        class="w-full border border-soft bg-white px-4 py-3 text-lg uppercase tracking-[0.1em]"
                    >
                    <button class="button-dark whitespace-nowrap" type="submit" :disabled="loadingLookup">
                        {{ loadingLookup ? 'Checking...' : 'Find Invitation' }}
                    </button>
                </form>

                <p v-if="lookupError" class="mt-4 text-sm text-red-700">{{ lookupError }}</p>
            </section>

            <section v-if="party" class="card-frame mx-auto mt-8 w-full max-w-3xl bg-white">
                <h2 class="font-heading text-4xl">Welcome, {{ party.display_name }}</h2>
                <p class="mt-2 text-wedding-muted">
                    Code: <span class="font-medium uppercase">{{ party.code }}</span>
                    · Max Guests: {{ party.max_guests }}
                </p>

                <div class="mt-8 space-y-6">
                    <div>
                        <p class="mb-2 text-sm uppercase tracking-[0.15em] text-wedding-muted">Attending?</p>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="border px-4 py-2"
                                :class="form.status === 'attending' ? 'border-wedding-band bg-wedding-band text-white' : 'border-soft'"
                                @click="form.status = 'attending'"
                            >
                                Yes
                            </button>
                            <button
                                type="button"
                                class="border px-4 py-2"
                                :class="form.status === 'not_attending' ? 'border-wedding-band bg-wedding-band text-white' : 'border-soft'"
                                @click="form.status = 'not_attending'"
                            >
                                No
                            </button>
                        </div>
                    </div>

                    <div v-if="form.status === 'attending'">
                        <label class="text-sm uppercase tracking-[0.15em] text-wedding-muted">Which guests are attending?</label>
                        <div class="mt-3 grid gap-3">
                            <label
                                v-for="guest in party.guests"
                                :key="guest.id"
                                class="flex cursor-pointer items-center justify-between gap-3 border px-4 py-3 transition"
                                :class="form.attending_guest_ids.includes(guest.id) ? 'border-wedding-band bg-wedding-band/5' : 'border-soft bg-white'"
                            >
                                <div>
                                    <p class="font-medium text-wedding-text">{{ formatGuestName(guest) }}</p>
                                    <p v-if="guest.is_child" class="mt-1 text-xs uppercase tracking-[0.12em] text-wedding-muted">Child guest</p>
                                </div>
                                <input
                                    :checked="form.attending_guest_ids.includes(guest.id)"
                                    type="checkbox"
                                    class="h-4 w-4"
                                    @change="toggleAttendingGuest(guest.id)"
                                >
                            </label>
                        </div>
                    </div>

                    <div v-if="mealChoicesEnabled && form.status === 'attending' && selectedGuests.length > 0" class="space-y-4">
                        <p class="text-sm uppercase tracking-[0.15em] text-wedding-muted">Meal choices</p>
                        <div v-for="(meal, index) in form.meal_choices" :key="meal.guest_id || index" class="space-y-3 border border-soft bg-wedding-bg p-4">
                            <div class="w-full border border-soft bg-white px-4 py-3 font-medium text-wedding-text">
                                {{ meal.guest_name }}
                            </div>
                            <div class="grid gap-3 md:grid-cols-2">
                                <div v-for="course in mealSectionsForGuest(meal.guest_id)" :key="`${meal.guest_id}-${course.id}`">
                                    <label class="text-sm uppercase tracking-[0.15em] text-wedding-muted">{{ course.name }}</label>
                                    <select v-model="meal.selections[course.id]" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                                        <option disabled value="">Select {{ course.name }}</option>
                                        <option v-for="item in course.items" :key="`${course.id}-${item.title}`" :value="item.title">{{ item.title }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="!mealChoicesEnabled && form.status === 'attending'"
                        class="cms-rich rounded border border-soft bg-wedding-bg p-4 text-sm text-wedding-muted"
                        v-html="rsvpSettings.set_menu_description"
                    ></div>

                    <div v-if="form.status === 'attending'">
                        <label class="text-sm uppercase tracking-[0.15em] text-wedding-muted">Dietary requirements</label>
                        <textarea
                            v-model="form.dietary_restrictions"
                            rows="3"
                            class="mt-2 w-full border border-soft bg-white px-4 py-3"
                            placeholder="Optional"
                        ></textarea>
                    </div>

                    <div>
                        <label class="text-sm uppercase tracking-[0.15em] text-wedding-muted">Message to the couple</label>
                        <textarea
                            v-model="form.message"
                            rows="4"
                            class="mt-2 w-full border border-soft bg-white px-4 py-3"
                            placeholder="Optional"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <button class="button-dark" type="button" @click="saveRsvp" :disabled="savingRsvp">
                        {{ savingRsvp ? 'Saving...' : 'Save RSVP' }}
                    </button>
                    <a class="inline-flex items-center border border-soft px-6 py-3 text-sm uppercase tracking-[0.15em]" href="/">
                        Back
                    </a>
                </div>

                <p v-if="saveError" class="mt-4 text-sm text-red-700">{{ saveError }}</p>
                <p v-if="saveSuccess" class="mt-4 text-sm text-emerald-700">{{ saveSuccess }}</p>

                <div v-if="party.rsvp" class="mt-8 border border-soft bg-wedding-bg p-5">
                    <h3 class="font-heading text-2xl">Current Confirmation</h3>
                    <p class="mt-2 text-wedding-muted">
                        Status:
                        <span class="font-medium">{{ party.rsvp.status === 'attending' ? 'Attending' : 'Not Attending' }}</span>
                        · Attending Count: {{ party.rsvp.attending_count }}
                    </p>
                    <p v-if="party.rsvp.attending_guest_names?.length" class="mt-2 text-sm text-wedding-muted">
                        Attending guests: {{ party.rsvp.attending_guest_names.join(', ') }}
                    </p>
                    <p v-if="party.rsvp.updated_at" class="mt-1 text-sm text-wedding-muted">Updated: {{ party.rsvp.updated_at }}</p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';

const props = defineProps({
    payload: {
        type: Object,
        default: () => ({}),
    },
});

const rsvpSettings = ref({
    meal_mode: props.payload?.rsvpSettings?.meal_mode || (props.payload?.mealChoicesEnabled === false ? 'set_menu' : 'options'),
    menu_heading: props.payload?.rsvpSettings?.menu_heading || 'Wedding Menu',
    menu_intro: props.payload?.rsvpSettings?.menu_intro || 'We cannot wait to share a beautiful meal with you.',
    set_menu_description: props.payload?.rsvpSettings?.set_menu_description || 'A chef-curated set menu will be served for all attending guests.',
    kids_menu_enabled: Boolean(props.payload?.rsvpSettings?.kids_menu_enabled),
    menu_courses: props.payload?.rsvpSettings?.menu_courses || [],
    kids_menu_items: props.payload?.rsvpSettings?.kids_menu_items || [],
    meal_options: props.payload?.rsvpSettings?.meal_options?.length
        ? props.payload.rsvpSettings.meal_options
        : (props.payload?.mealOptions || ['Chicken', 'Beef', 'Veg', 'Vegan']),
});
const mealOptions = ref(rsvpSettings.value.meal_options);
const mealChoicesEnabled = computed(() => rsvpSettings.value.meal_mode === 'options');
const mealCourses = ref(props.payload?.mealCourses || []);
const kidsMenuItems = ref(props.payload?.kidsMenuItems || []);
const saveEndpoint = ref('');
const codeInput = ref((props.payload?.code || '').toUpperCase());
const party = ref(null);
const theme = computed(() => props.payload?.content?.theme || {});
const paletteColours = computed(() => normalizePaletteColours(theme.value.palette_colours));
const paletteSlug = computed(() => normalizePaletteSlug(theme.value.palette));
const buttonColor = computed(() => paletteSlug.value === 'champagne_silk' ? paletteColours.value.secondary : paletteColours.value.primary);
const buttonHoverColor = computed(() => paletteSlug.value === 'champagne_silk' ? paletteColours.value.primary : paletteColours.value.secondary);
const themeVars = computed(() => ({
    '--wedding-button-color': buttonColor.value,
    '--wedding-button-hover-color': buttonHoverColor.value,
    '--wedding-button-text-color': isLightColour(buttonColor.value) ? '#0F1B1D' : '#FFFFFF',
    '--wedding-button-hover-text-color': isLightColour(buttonHoverColor.value) ? '#0F1B1D' : '#FFFFFF',
}));

const loadingLookup = ref(false);
const savingRsvp = ref(false);
const lookupError = ref('');
const saveError = ref('');
const saveSuccess = ref('');

const form = reactive({
    status: '',
    attending_count: 0,
    attending_guest_ids: [],
    meal_choices: [],
    dietary_restrictions: '',
    message: '',
});

function normalizePaletteColours(colours) {
    return {
        primary: normalizeHexColour(colours?.primary, '#22363A'),
        secondary: normalizeHexColour(colours?.secondary, '#466369'),
    };
}

function normalizePaletteSlug(palette) {
    return (palette || 'magic_classic').toString().trim().toLowerCase().replace(/[\s-]+/g, '_');
}

function normalizeHexColour(colour, fallback) {
    const normalized = String(colour || '').trim().toUpperCase();
    return /^#[0-9A-F]{6}$/.test(normalized) ? normalized : fallback;
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

const availableCourseSections = computed(() => mealCourses.value.filter((course) => Array.isArray(course.items) && course.items.length > 0));
const hasKidsMenu = computed(() => rsvpSettings.value.kids_menu_enabled && kidsMenuItems.value.length > 0);
const selectedGuests = computed(() => {
    if (!party.value?.guests?.length) {
        return [];
    }

    return party.value.guests.filter((guest) => form.attending_guest_ids.includes(guest.id));
});

watch(
    () => [...form.attending_guest_ids],
    () => {
        form.attending_count = form.attending_guest_ids.length;

        if (form.status !== 'attending' || !mealChoicesEnabled.value) {
            form.meal_choices = [];
            return;
        }

        ensureSelectionsShape();
    }
);

watch(
    () => form.status,
    (status) => {
        if (status === 'not_attending') {
            form.attending_count = 0;
            form.attending_guest_ids = [];
            form.meal_choices = [];
            form.dietary_restrictions = '';
        } else if (!status) {
            form.attending_count = 0;
            form.attending_guest_ids = [];
            form.meal_choices = [];
            form.dietary_restrictions = '';
        }
    }
);

onMounted(() => {
    if (codeInput.value) {
        lookupCode();
    }
});

async function lookupCode() {
    lookupError.value = '';
    saveError.value = '';
    saveSuccess.value = '';
    loadingLookup.value = true;

    try {
        const response = await window.axios.post('/rsvp/lookup', {
            code: codeInput.value,
        });

        party.value = response.data.party;
        saveEndpoint.value = response.data.saveUrl || '';
        mealCourses.value = response.data.mealCourses || [];
        kidsMenuItems.value = response.data.kidsMenuItems || [];
        if (response.data.rsvpSettings) {
            const incoming = response.data.rsvpSettings;
            rsvpSettings.value = {
                ...rsvpSettings.value,
                ...incoming,
                meal_mode: incoming.meal_mode || (incoming.meal_choices_enabled === false ? 'set_menu' : 'options'),
                meal_options: incoming.meal_options?.length ? incoming.meal_options : rsvpSettings.value.meal_options,
                kids_menu_enabled: Boolean(incoming.kids_menu_enabled),
                menu_courses: incoming.menu_courses || response.data.mealCourses || [],
                kids_menu_items: incoming.kids_menu_items || response.data.kidsMenuItems || [],
            };
            mealOptions.value = rsvpSettings.value.meal_options;
        } else {
            mealOptions.value = response.data.mealOptions || mealOptions.value;
            rsvpSettings.value.meal_mode = (response.data.mealChoicesEnabled ?? mealChoicesEnabled.value) ? 'options' : 'set_menu';
            rsvpSettings.value.meal_options = mealOptions.value;
        }

        hydrateFormFromExistingRsvp();
    } catch (error) {
        party.value = null;
        lookupError.value = error.response?.data?.message || 'Unable to find that RSVP code.';
    } finally {
        loadingLookup.value = false;
    }
}

function hydrateFormFromExistingRsvp() {
    const existing = party.value?.rsvp;

    if (!existing) {
        form.status = '';
        form.attending_count = 0;
        form.attending_guest_ids = [];
        form.meal_choices = [];
        form.dietary_restrictions = '';
        form.message = '';
        return;
    }

    form.status = existing.status;
    form.attending_guest_ids = normalizeAttendingGuestIds(existing);
    form.attending_count = form.attending_guest_ids.length;
    form.meal_choices = normalizeSavedMealChoices(existing.meal_choices || []);
    form.dietary_restrictions = existing.dietary_restrictions || '';
    form.message = existing.message || '';
}

async function saveRsvp() {
    if (!party.value) {
        return;
    }

    if (!form.status) {
        saveError.value = 'Please select whether your party is attending.';
        return;
    }

    if (form.status === 'attending' && form.attending_guest_ids.length === 0) {
        saveError.value = 'Please select which guests are attending.';
        return;
    }

    saveError.value = '';
    saveSuccess.value = '';
    savingRsvp.value = true;

    try {
        const response = await window.axios.post(saveEndpoint.value || `/rsvp/${party.value.code}`, {
            status: form.status,
            attending_count: form.attending_guest_ids.length,
            attending_guest_ids: form.attending_guest_ids,
            meal_choices: mealChoicesEnabled.value && form.status === 'attending' ? buildMealChoicesPayload() : [],
            dietary_restrictions: form.status === 'attending' ? form.dietary_restrictions : '',
            message: form.message,
        });

        party.value = response.data.party;
        saveSuccess.value = response.data.message || 'RSVP saved.';
        hydrateFormFromExistingRsvp();
    } catch (error) {
        saveError.value = error.response?.data?.message || 'Unable to save RSVP right now.';
    } finally {
        savingRsvp.value = false;
    }
}

function mealSectionsForGuest(guestId) {
    const guest = party.value?.guests?.find((item) => item.id === guestId) || null;
    if (guest?.is_child && hasKidsMenu.value) {
        return [
            {
                id: 'kids-menu',
                name: 'Kids Menu',
                items: kidsMenuItems.value,
            },
        ];
    }

    return availableCourseSections.value;
}

function normalizeAttendingGuestIds(existing) {
    const validGuestIds = (party.value?.guests || []).map((guest) => guest.id);
    const savedIds = Array.isArray(existing?.attending_guest_ids)
        ? existing.attending_guest_ids.filter((id) => validGuestIds.includes(id))
        : [];

    if (savedIds.length) {
        return savedIds;
    }

    const namedChoices = Array.isArray(existing?.meal_choices)
        ? existing.meal_choices
            .map((choice) => {
                if (choice?.guest_id && validGuestIds.includes(choice.guest_id)) {
                    return choice.guest_id;
                }

                const match = (party.value?.guests || []).find((guest) => formatGuestName(guest) === choice?.guest_name);
                return match?.id || null;
            })
            .filter(Boolean)
        : [];

    if (namedChoices.length) {
        return [...new Set(namedChoices)];
    }

    return (party.value?.guests || []).slice(0, existing?.attending_count || 0).map((guest) => guest.id);
}

function normalizeSavedMealChoices(items) {
    const existingByGuestId = new Map(
        Array.isArray(items)
            ? items
                .map((item) => [item?.guest_id || null, item])
                .filter(([guestId]) => guestId)
            : []
    );

    const existingByGuestName = new Map(
        Array.isArray(items)
            ? items
                .map((item) => [item?.guest_name || null, item])
                .filter(([guestName]) => guestName)
            : []
    );

    return selectedGuests.value.map((guest) => {
        const fullName = formatGuestName(guest);
        const existing = existingByGuestId.get(guest.id) || existingByGuestName.get(fullName);

        return {
            guest_id: guest.id,
            guest_name: existing?.guest_name || fullName,
            meal: existing?.meal || '',
            selections: existing?.selections && typeof existing.selections === 'object' ? { ...existing.selections } : {},
        };
    });
}

function ensureSelectionsShape() {
    form.meal_choices = normalizeSavedMealChoices(form.meal_choices);
    form.meal_choices.forEach((choice) => {
        const validIds = mealSectionsForGuest(choice.guest_id).map((section) => section.id);
        choice.selections = Object.fromEntries(
            Object.entries(choice.selections || {}).filter(([key]) => validIds.includes(key))
        );
    });
}

function buildMealSummary(choice) {
    const sections = mealSectionsForGuest(choice.guest_id);
    return sections
        .map((section) => {
            const value = choice.selections?.[section.id];
            return value ? `${section.name}: ${value}` : null;
        })
        .filter(Boolean)
        .join(' | ');
}

function buildMealChoicesPayload() {
    return form.meal_choices.map((choice) => ({
        guest_id: choice.guest_id,
        guest_name: choice.guest_name,
        meal: buildMealSummary(choice).slice(0, 60),
        selections: choice.selections || {},
    }));
}

function toggleAttendingGuest(guestId) {
    if (form.attending_guest_ids.includes(guestId)) {
        form.attending_guest_ids = form.attending_guest_ids.filter((id) => id !== guestId);
        return;
    }

    form.attending_guest_ids = [...form.attending_guest_ids, guestId];
}

function formatGuestName(guest) {
    return `${guest?.first_name || ''} ${guest?.last_name || ''}`.trim() || 'Unnamed guest';
}
</script>
