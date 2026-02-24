<template>
    <div class="space-y-2">
        <div class="flex flex-wrap gap-2 border border-soft bg-[#eeeeee] p-2">
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="runCommand('bold')"><strong>B</strong></button>
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="runCommand('italic')"><em>I</em></button>
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="runCommand('insertUnorderedList')">List</button>
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="insertLineBreak">Line Break</button>
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="insertParagraph">Paragraph</button>
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="openLinkModal('link')">Link</button>
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="openLinkModal('button')">Button Link</button>
            <button type="button" class="admin-btn border border-soft px-2 py-1 text-xs" @click="clearFormatting">Clear</button>
        </div>

        <div
            ref="editor"
            class="cms-rich min-h-32 border border-soft bg-white p-3"
            contenteditable="true"
            @input="syncToModel"
            @blur="syncToModel"
        ></div>

        <div v-if="linkModalOpen" class="fixed inset-0 z-[80] bg-black/40 p-4" @click.self="closeLinkModal">
            <div class="mx-auto mt-16 w-full max-w-lg border border-soft bg-white p-5 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <h4 class="font-heading text-2xl">{{ linkModalMode === 'button' ? 'Add Button Link' : 'Add Link' }}</h4>
                    <button
                        type="button"
                        class="admin-btn border border-red-300 px-3 py-2 text-xs uppercase tracking-[0.12em] text-red-700 transition hover:border-red-400 hover:bg-red-50"
                        @click="closeLinkModal"
                    >
                        X Close
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        URL
                        <input v-model="linkUrl" type="url" placeholder="https://example.com" class="mt-1 w-full border border-soft px-3 py-2 normal-case tracking-normal">
                    </label>
                    <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        {{ linkModalMode === 'button' ? 'Button Label' : 'Link Text' }}
                        <input v-model="linkText" type="text" placeholder="Open link" class="mt-1 w-full border border-soft px-3 py-2 normal-case tracking-normal">
                    </label>
                    <p v-if="linkModalError" class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ linkModalError }}</p>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button
                        type="button"
                        class="admin-btn border border-red-300 px-3 py-2 text-xs uppercase tracking-[0.12em] text-red-700 transition hover:border-red-400 hover:bg-red-50"
                        @click="closeLinkModal"
                    >
                        X Close
                    </button>
                    <button type="button" class="admin-btn border border-wedding-band bg-wedding-band px-3 py-2 text-xs uppercase tracking-[0.12em] text-white" @click="insertLinkFromModal">
                        Insert
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
const linkModalOpen = ref(false);
const linkModalMode = ref('link');
const linkUrl = ref('');
const linkText = ref('');
const linkModalError = ref('');

onMounted(() => {
    if (editor.value) {
        editor.value.innerHTML = props.modelValue || '';
    }
});

watch(
    () => props.modelValue,
    (nextValue) => {
        if (!editor.value) {
            return;
        }

        if ((editor.value.innerHTML || '') !== (nextValue || '')) {
            editor.value.innerHTML = nextValue || '';
        }
    }
);

function runCommand(command, value = null) {
    focusEditor();
    document.execCommand(command, false, value);
    syncToModel();
}

function insertLineBreak() {
    runCommand('insertHTML', '<br><br>');
}

function insertParagraph() {
    runCommand('insertHTML', '<p><br></p>');
}

function openLinkModal(mode) {
    linkModalMode.value = mode;
    linkUrl.value = '';
    linkText.value = 'Open link';
    linkModalError.value = '';
    linkModalOpen.value = true;
}

function closeLinkModal() {
    linkModalOpen.value = false;
    linkModalError.value = '';
}

function insertLinkFromModal() {
    const url = linkUrl.value.trim();
    const text = linkText.value.trim() || 'Open link';

    if (!/^https?:\/\//i.test(url)) {
        linkModalError.value = 'Please enter a valid URL starting with http:// or https://';
        return;
    }

    const safeUrl = escapeAttribute(url);
    const safeText = escapeHtml(text);

    if (linkModalMode.value === 'button') {
        runCommand(
            'insertHTML',
            `<a href="${safeUrl}" class="cms-button-link" target="_blank" rel="noopener noreferrer">${safeText}</a>`
        );
    } else {
        runCommand('insertHTML', `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer">${safeText}</a>`);
    }

    closeLinkModal();
}

function clearFormatting() {
    runCommand('removeFormat');
}

function syncToModel() {
    emit('update:modelValue', editor.value?.innerHTML || '');
}

function focusEditor() {
    editor.value?.focus();
}

function escapeHtml(value) {
    return value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function escapeAttribute(value) {
    return value.replaceAll('"', '&quot;');
}
</script>
