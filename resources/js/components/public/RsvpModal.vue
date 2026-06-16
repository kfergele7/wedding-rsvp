<template>
    <div class="fixed inset-0 z-[90] bg-black/50 p-4 md:p-8" @click.self="$emit('close')">
        <div class="mx-auto mt-4 max-h-[92vh] w-full max-w-4xl overflow-y-auto border border-soft bg-wedding-bg p-6 shadow-soft md:mt-8 md:p-8">
            <div v-if="!submittedConfirmation" class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Wedding RSVP</p>
                    <h2 class="mt-2 font-heading text-4xl">RSVP</h2>
                    <p class="mt-3 text-wedding-muted">Enter your invitation code to view and update your response.</p>
                </div>
                <button
                    class="modal-close-x"
                    type="button"
                    aria-label="Close RSVP modal"
                    title="Close"
                    @click="$emit('close')"
                >
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <section v-if="submittedConfirmation" class="card-frame mt-2 bg-white text-center md:mt-4">
                <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Thank you</p>
                <h2 class="mt-3 font-heading text-4xl">Your RSVP has been submitted</h2>
                <p class="mx-auto mt-4 max-w-2xl text-wedding-muted">
                    Thank you for sending your response. This window will close in a moment, and you can reopen it any time if you need to review your RSVP again.
                </p>
            </section>

            <template v-else>
            <section class="card-frame mt-6 bg-white">
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

            <section v-if="party" class="card-frame mt-6 bg-white">
                <h3 class="font-heading text-3xl">Welcome, {{ party.display_name }}</h3>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs uppercase tracking-[0.14em]"
                        :style="guestTypeBadgeStyle"
                    >
                        <span class="material-symbols-outlined text-base">{{ guestTypeMeta.icon }}</span>
                        {{ guestTypeMeta.label }}
                    </span>
                    <p class="text-sm text-wedding-muted">{{ guestTypeMeta.description }}</p>
                </div>
                <p class="mt-3 text-wedding-muted">
                    Code: <span class="font-medium uppercase">{{ party.code }}</span>
                    · Max Guests: {{ party.max_guests }}
                </p>
                <div
                    v-if="eveningArrivalTimeLabel"
                    class="mt-4 border border-soft bg-wedding-bg p-4"
                >
                    <p class="text-xs uppercase tracking-[0.18em] text-wedding-muted">Evening arrival</p>
                    <p class="mt-2 font-heading text-2xl text-wedding-text">{{ eveningArrivalTimeLabel }}</p>
                    <p v-if="eveningArrivalSentence" class="mt-2 text-sm text-wedding-muted">{{ eveningArrivalSentence }}</p>
                </div>

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

                    <div v-if="effectiveMealChoicesEnabled && form.status === 'attending' && selectedGuests.length > 0" class="space-y-4">
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
                        v-if="showSetMenuNotice"
                        class="cms-rich rounded border border-soft bg-wedding-bg p-4 text-sm text-wedding-muted"
                        v-html="rsvpSettings.set_menu_description"
                    ></div>

                    <div
                        v-if="showEveningGuestNotice"
                        class="rounded border border-soft bg-wedding-bg p-4 text-sm text-wedding-muted"
                    >
                        Evening guests do not need to choose a meal here. Please let us know any dietary requirements below.
                    </div>

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
                    <button class="button-success" type="button" @click="saveRsvp" :disabled="savingRsvp">
                        {{ savingRsvp ? 'Saving...' : 'Save RSVP' }}
                    </button>
                    <button
                        class="admin-btn admin-btn-danger px-6 py-3 text-sm uppercase tracking-[0.15em]"
                        type="button"
                        @click="$emit('close')"
                    >
                        X Close
                    </button>
                </div>

                <p v-if="saveError" class="mt-4 text-sm text-red-700">{{ saveError }}</p>
                <p v-if="saveSuccess" class="mt-4 text-sm text-emerald-700">{{ saveSuccess }}</p>

                <div v-if="party.rsvp" class="mt-8 border border-soft bg-wedding-bg p-5">
                    <h4 class="font-heading text-2xl">Current Confirmation</h4>
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
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';

const props = defineProps({
    initialCode: {
        type: String,
        default: '',
    },
    publicSlug: {
        type: String,
        default: '',
    },
    rsvpSettingsPayload: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close', 'party-resolved']);

const rsvpSettings = ref({
    meal_mode: props.rsvpSettingsPayload?.meal_mode || 'options',
    set_menu_description: props.rsvpSettingsPayload?.set_menu_description || 'A chef-curated set menu will be served for all attending guests.',
    meal_options: props.rsvpSettingsPayload?.meal_options || ['Chicken', 'Beef', 'Veg', 'Vegan'],
    kids_menu_enabled: Boolean(props.rsvpSettingsPayload?.kids_menu_enabled),
    menu_courses: props.rsvpSettingsPayload?.menu_courses || [],
    kids_menu_items: props.rsvpSettingsPayload?.kids_menu_items || [],
});
const mealOptions = ref(rsvpSettings.value.meal_options);
const mealChoicesEnabled = computed(() => rsvpSettings.value.meal_mode === 'options');
const effectiveMealChoicesEnabled = ref(mealChoicesEnabled.value);
const mealCourses = ref([]);
const kidsMenuItems = ref([]);
const saveEndpoint = ref('');
const lookupEndpoint = computed(() => {
    if (props.publicSlug) {
        return `/${props.publicSlug}/rsvp/lookup`;
    }
    return '/rsvp/lookup';
});
const codeInput = ref((props.initialCode || '').toUpperCase());
const party = ref(null);

const loadingLookup = ref(false);
const savingRsvp = ref(false);
const lookupError = ref('');
const saveError = ref('');
const saveSuccess = ref('');
const submittedConfirmation = ref(false);
let closeAfterSubmitTimer = null;

const form = reactive({
    status: '',
    attending_count: 0,
    attending_guest_ids: [],
    meal_choices: [],
    dietary_restrictions: '',
    message: '',
});

const availableCourseSections = computed(() => mealCourses.value.filter((course) => Array.isArray(course.items) && course.items.length > 0));
const hasKidsMenu = computed(() => rsvpSettings.value.kids_menu_enabled && kidsMenuItems.value.length > 0);
const isEveningGuest = computed(() => party.value?.guest_type === 'evening');
const eveningArrivalTimeLabel = computed(() => isEveningGuest.value ? (party.value?.evening_arrival_time_label || '') : '');
const eveningArrivalSentence = computed(() => isEveningGuest.value ? (party.value?.evening_arrival_sentence || '') : '');
const showSetMenuNotice = computed(() => !effectiveMealChoicesEnabled.value && form.status === 'attending' && !isEveningGuest.value);
const showEveningGuestNotice = computed(() => form.status === 'attending' && isEveningGuest.value);
const selectedGuests = computed(() => {
    if (!party.value?.guests?.length) {
        return [];
    }

    return party.value.guests.filter((guest) => form.attending_guest_ids.includes(guest.id));
});
const guestTypeMeta = computed(() => {
    if (isEveningGuest.value) {
        return {
            icon: 'dark_mode',
            label: 'Evening Guest',
            description: 'You are invited to the evening celebration.',
        };
    }

    return {
        icon: 'sunny',
        label: 'All Day Guest',
        description: 'You are invited to join us for the full day.',
    };
});
const guestTypeBadgeStyle = computed(() => {
    if (isEveningGuest.value) {
        return {
            color: '#22363A',
            borderColor: 'rgba(34, 54, 58, 0.24)',
            backgroundColor: 'rgba(34, 54, 58, 0.08)',
        };
    }

    return {
        color: '#D79A2B',
        borderColor: 'rgba(215, 154, 43, 0.24)',
        backgroundColor: 'rgba(215, 154, 43, 0.08)',
    };
});

watch(
    () => [...form.attending_guest_ids],
    () => {
        form.attending_count = form.attending_guest_ids.length;

        if (form.status !== 'attending' || !effectiveMealChoicesEnabled.value) {
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

onBeforeUnmount(() => {
    if (closeAfterSubmitTimer) {
        clearTimeout(closeAfterSubmitTimer);
    }
});

async function lookupCode() {
    lookupError.value = '';
    saveError.value = '';
    saveSuccess.value = '';
    submittedConfirmation.value = false;
    loadingLookup.value = true;

    try {
        const response = await window.axios.post(lookupEndpoint.value, {
            code: codeInput.value,
        });

        party.value = response.data.party;
        saveEndpoint.value = response.data.saveUrl || '';
        mealCourses.value = response.data.mealCourses || [];
        kidsMenuItems.value = response.data.kidsMenuItems || [];
        effectiveMealChoicesEnabled.value = Boolean(response.data.mealChoicesEnabled);
        if (response.data.rsvpSettings) {
            rsvpSettings.value = {
                ...rsvpSettings.value,
                ...response.data.rsvpSettings,
                meal_mode: response.data.rsvpSettings.meal_mode || 'options',
                meal_options: response.data.rsvpSettings.meal_options || rsvpSettings.value.meal_options,
                kids_menu_enabled: Boolean(response.data.rsvpSettings.kids_menu_enabled),
                menu_courses: response.data.rsvpSettings.menu_courses || response.data.mealCourses || [],
                kids_menu_items: response.data.rsvpSettings.kids_menu_items || response.data.kidsMenuItems || [],
            };
        }
        mealOptions.value = response.data.mealOptions || rsvpSettings.value.meal_options;
        hydrateFormFromExistingRsvp();
        emit('party-resolved', party.value);
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
        const fallbackSaveEndpoint = props.publicSlug
            ? `/${props.publicSlug}/rsvp/${party.value.code}`
            : `/rsvp/${party.value.code}`;

        const response = await window.axios.post(saveEndpoint.value || fallbackSaveEndpoint, {
            status: form.status,
            attending_count: form.attending_guest_ids.length,
            attending_guest_ids: form.attending_guest_ids,
            meal_choices: effectiveMealChoicesEnabled.value && form.status === 'attending' ? buildMealChoicesPayload() : [],
            dietary_restrictions: form.status === 'attending' ? form.dietary_restrictions : '',
            message: form.message,
        });

        party.value = response.data.party;
        saveSuccess.value = response.data.message || 'RSVP saved.';
        hydrateFormFromExistingRsvp();
        emit('party-resolved', party.value);
        submittedConfirmation.value = true;
        if (closeAfterSubmitTimer) {
            clearTimeout(closeAfterSubmitTimer);
        }
        closeAfterSubmitTimer = window.setTimeout(() => {
            emit('close');
        }, 10000);
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
