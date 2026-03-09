<template>
    <div class="space-y-2">
        <div class="rich-toolbar flex flex-wrap gap-2 border border-soft p-2" :class="toolbarClass">
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="runCommand('bold')"><strong>B</strong></button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="runCommand('italic')"><em>I</em></button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="runCommand('insertUnorderedList')">List</button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="insertLineBreak">Line Break</button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="insertParagraph">Paragraph</button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="openLinkModal('link')">Link</button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="openLinkModal('button')">Button Link</button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" @click="clearFormatting">Clear</button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" :disabled="!canUndo" @click="runCommand('undo')" aria-label="Undo" title="Undo">
                <span class="material-symbols-outlined format-icon">undo</span>
            </button>
            <button type="button" class="format-btn border border-soft px-2 py-1 text-xs" :class="formatButtonClass" :disabled="!canRedo" @click="runCommand('redo')" aria-label="Redo" title="Redo">
                <span class="material-symbols-outlined format-icon">redo</span>
            </button>
        </div>

        <div
            ref="editor"
            class="cms-rich min-h-32 border border-soft p-3"
            :class="editorClass"
            contenteditable="true"
            @input="syncToModel"
            @blur="syncToModel"
            @keyup="refreshHistoryState"
            @mouseup="refreshHistoryState"
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
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    tone: {
        type: String,
        default: 'secondary',
    },
    surface: {
        type: String,
        default: 'white',
    },
    buttonTone: {
        type: String,
        default: 'offwhite',
    },
});

const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
const linkModalOpen = ref(false);
const linkModalMode = ref('link');
const linkUrl = ref('');
const linkText = ref('');
const linkModalError = ref('');
const toolbarClass = ref('toolbar-secondary');
const editorClass = ref('editor-white');
const formatButtonClass = ref('format-btn-offwhite');
const canUndo = ref(false);
const canRedo = ref(false);

onMounted(() => {
    if (editor.value) {
        editor.value.innerHTML = props.modelValue || '';
    }

    refreshHistoryState();
    document.addEventListener('selectionchange', refreshHistoryState);
});

onBeforeUnmount(() => {
    document.removeEventListener('selectionchange', refreshHistoryState);
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

watch(
    () => props.tone,
    (nextTone) => {
        toolbarClass.value = nextTone === 'primary' ? 'toolbar-primary' : 'toolbar-secondary';
    },
    { immediate: true }
);

watch(
    () => props.surface,
    (nextSurface) => {
        editorClass.value = nextSurface === 'primary' ? 'editor-primary' : 'editor-white';
    },
    { immediate: true }
);

watch(
    () => props.buttonTone,
    (nextTone) => {
        formatButtonClass.value = nextTone === 'primary' ? 'format-btn-primary' : 'format-btn-offwhite';
    },
    { immediate: true }
);

function runCommand(command, value = null) {
    focusEditor();
    document.execCommand(command, false, value);
    syncToModel();
    refreshHistoryState();
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
    refreshHistoryState();
}

function focusEditor() {
    editor.value?.focus();
}

function refreshHistoryState() {
    canUndo.value = document.queryCommandEnabled('undo');
    canRedo.value = document.queryCommandEnabled('redo');
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

<style scoped>
.format-btn {
    color: #0f1b1d;
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.format-btn:hover {
    background: #f7f5f2;
    border-color: #466369;
    color: #0f1b1d;
}

.format-icon {
    font-size: 16px;
    line-height: 1;
}

.format-btn:disabled {
    background: #848484;
    border-color: #848484;
    color: #ffffff;
    cursor: not-allowed;
}

.format-btn-offwhite {
    background: #f7f7f7;
}

.format-btn-primary {
    background: #f7f5f2;
}

.toolbar-secondary {
    background: #f2ece3;
}

.toolbar-primary {
    background: #f7f5f2;
}

.editor-white {
    background: #ffffff;
}

.editor-primary {
    background: #f7f5f2;
}
</style>
