<template>
    <div class="fixed inset-0 z-[90] bg-black/50 p-4 md:p-8" @click.self="$emit('close')">
        <div class="mx-auto mt-4 max-h-[92vh] w-full max-w-4xl overflow-y-auto border border-soft bg-wedding-bg p-6 shadow-soft md:mt-8 md:p-8">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Wedding RSVP</p>
                    <h2 class="mt-2 font-heading text-4xl">RSVP</h2>
                    <p class="mt-3 text-wedding-muted">Enter your invitation code to view and update your response.</p>
                </div>
                <button
                    class="admin-btn border border-red-300 px-3 py-2 text-xs uppercase tracking-[0.12em] text-red-700 transition hover:border-red-400 hover:bg-red-50"
                    type="button"
                    @click="$emit('close')"
                >
                    X Close
                </button>
            </div>

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
                        <label class="text-sm uppercase tracking-[0.15em] text-wedding-muted">How many attending?</label>
                        <select v-model.number="form.attending_count" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                            <option v-for="count in countOptions" :key="count" :value="count">{{ count }}</option>
                        </select>
                    </div>

                    <div v-if="mealChoicesEnabled && form.status === 'attending' && form.attending_count > 0" class="space-y-3">
                        <p class="text-sm uppercase tracking-[0.15em] text-wedding-muted">Main course choices</p>
                        <div v-for="(meal, index) in form.meal_choices" :key="index" class="grid gap-3 md:grid-cols-2">
                            <input
                                v-model="meal.guest_name"
                                type="text"
                                class="border border-soft bg-white px-4 py-3"
                                :placeholder="`Guest ${index + 1} name`"
                            >
                            <select v-model="meal.meal" class="border border-soft bg-white px-4 py-3">
                                <option disabled value="">Select meal</option>
                                <option v-for="mealOption in mealOptions" :key="mealOption" :value="mealOption">{{ mealOption }}</option>
                            </select>
                        </div>
                    </div>

                    <div
                        v-if="!mealChoicesEnabled && form.status === 'attending'"
                        class="cms-rich rounded border border-soft bg-wedding-bg p-4 text-sm text-wedding-muted"
                        v-html="rsvpSettings.set_menu_description"
                    ></div>

                    <div>
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
                    <button
                        class="admin-btn border border-red-300 px-6 py-3 text-sm uppercase tracking-[0.15em] text-red-700 transition hover:border-red-400 hover:bg-red-50"
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
                    <p v-if="party.rsvp.updated_at" class="mt-1 text-sm text-wedding-muted">Updated: {{ party.rsvp.updated_at }}</p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';

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

defineEmits(['close']);

const rsvpSettings = ref({
    meal_mode: props.rsvpSettingsPayload?.meal_mode || 'options',
    set_menu_description: props.rsvpSettingsPayload?.set_menu_description || 'A chef-curated set menu will be served for all attending guests.',
    meal_options: props.rsvpSettingsPayload?.meal_options || ['Chicken', 'Beef', 'Veg', 'Vegan'],
});
const mealOptions = ref(rsvpSettings.value.meal_options);
const mealChoicesEnabled = computed(() => rsvpSettings.value.meal_mode === 'options');
const lookupEndpoint = computed(() => {
    if (props.publicSlug) {
        return `/w/${props.publicSlug}/rsvp/lookup`;
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

const form = reactive({
    status: '',
    attending_count: 0,
    meal_choices: [],
    dietary_restrictions: '',
    message: '',
});

const countOptions = computed(() => {
    const maxGuests = party.value?.max_guests || 0;
    return Array.from({ length: maxGuests + 1 }, (_, i) => i);
});

watch(
    () => form.attending_count,
    (count) => {
        if (form.status !== 'attending' || !mealChoicesEnabled.value) {
            form.meal_choices = [];
            return;
        }

        const current = form.meal_choices;
        const next = [];

        for (let index = 0; index < count; index += 1) {
            const existing = current[index];
            const guest = party.value?.guests?.[index];

            next.push({
                guest_name: existing?.guest_name || (guest ? `${guest.first_name} ${guest.last_name}` : ''),
                meal: existing?.meal || '',
            });
        }

        form.meal_choices = next;
    }
);

watch(
    () => form.status,
    (status) => {
        if (status === 'not_attending') {
            form.attending_count = 0;
            form.meal_choices = [];
        } else if (status === 'attending' && form.attending_count === 0) {
            form.attending_count = Math.min(1, party.value?.max_guests || 1);
        } else if (!status) {
            form.attending_count = 0;
            form.meal_choices = [];
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
        const response = await window.axios.post(lookupEndpoint.value, {
            code: codeInput.value,
        });

        party.value = response.data.party;
        if (response.data.rsvpSettings) {
            rsvpSettings.value = {
                ...rsvpSettings.value,
                ...response.data.rsvpSettings,
                meal_mode: response.data.rsvpSettings.meal_mode || 'options',
                meal_options: response.data.rsvpSettings.meal_options || rsvpSettings.value.meal_options,
            };
        }
        mealOptions.value = response.data.mealOptions || rsvpSettings.value.meal_options;
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
        form.meal_choices = [];
        form.dietary_restrictions = '';
        form.message = '';
        return;
    }

    form.status = existing.status;
    form.attending_count = existing.attending_count;
    form.meal_choices = existing.meal_choices || [];
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

    saveError.value = '';
    saveSuccess.value = '';
    savingRsvp.value = true;

    try {
        const saveEndpoint = props.publicSlug
            ? `/w/${props.publicSlug}/rsvp/${party.value.code}`
            : `/rsvp/${party.value.code}`;

        const response = await window.axios.post(saveEndpoint, {
            status: form.status,
            attending_count: form.attending_count,
            meal_choices: mealChoicesEnabled.value && form.status === 'attending' ? form.meal_choices : [],
            dietary_restrictions: form.dietary_restrictions,
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
</script>
