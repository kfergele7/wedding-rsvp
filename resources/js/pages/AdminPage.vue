<template>
    <div class="admin-ui min-h-screen bg-wedding-bg">
        <header class="border-b border-soft bg-white/90">
            <div class="admin-shell py-6">
                <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Wedding Admin</p>
                <h1 class="font-heading text-2xl md:text-4xl">Content & Guest Management</h1>
            </div>
        </header>

        <div class="admin-sticky-nav sticky top-0 z-40 border-y border-soft bg-white/95 backdrop-blur">
            <div class="admin-shell flex items-center justify-between gap-3 py-3">
                <button
                    type="button"
                    class="admin-btn inline-flex w-full items-center justify-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.14em] xl:hidden"
                    @click="mobileNavOpen = true"
                >
                    <span class="material-symbols-outlined btn-icon">menu</span>
                    Menu
                </button>

                <nav class="hidden flex-wrap gap-2 xl:flex">
                    <a
                        v-for="item in navItems"
                        :key="item.key"
                        :href="item.href"
                        class="admin-btn inline-flex items-center gap-2 border px-4 py-3 text-xs uppercase tracking-[0.15em]"
                        :class="section === item.key ? 'admin-btn-active' : ''"
                    >
                        <span class="material-symbols-outlined btn-icon">{{ item.icon }}</span>
                        {{ item.label }}
                    </a>
                </nav>

                <div class="hidden flex-wrap items-center gap-2 border-l border-soft pl-4 xl:flex">
                    <a
                        v-if="accountUrl"
                        :href="accountUrl"
                        class="admin-btn inline-flex items-center gap-2 border px-4 py-3 text-xs uppercase tracking-[0.14em]"
                    >
                        <span class="material-symbols-outlined btn-icon">person</span>
                        My Account
                    </a>
                    <form :action="logoutUrl" method="POST">
                        <input type="hidden" name="_token" :value="csrfToken">
                        <button class="admin-btn admin-btn-danger-solid inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.16em]" type="submit"><span class="material-symbols-outlined btn-icon">logout</span>Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <main class="admin-main-shell pb-10 pt-4 md:py-10">
            <section v-if="section === 'dashboard'" class="space-y-6">
                <article class="content-section-block content-section-odd">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.18em] text-wedding-muted">Guided setup checklist</p>
                            <h2 class="mt-2 font-heading text-3xl">Launch your wedding website with confidence</h2>
                            <p class="mt-3 max-w-3xl text-wedding-muted">Follow these steps in order. Magic Invitation will tick each one off as your website, guest list, emails, publishing, and RSVP testing are ready.</p>
                        </div>
                        <div class="min-w-[190px] border border-soft bg-white p-4 text-center">
                            <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">Progress</p>
                            <p class="mt-1 font-heading text-4xl">{{ completedSetupCount }}/{{ setupChecklistItems.length }}</p>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-wedding-bg">
                                <div class="h-full rounded-full bg-wedding-success transition-all" :style="{ width: `${setupCompletionPercentage}%` }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-7 space-y-3">
                        <article
                            v-for="(item, index) in setupChecklistItems"
                            :key="item.key"
                            class="border px-4 py-4 transition md:grid md:grid-cols-[minmax(210px,0.7fr)_minmax(0,1fr)_210px] md:items-center md:gap-4"
                            :class="[
                                item.completed ? 'border-wedding-success/40 bg-emerald-50/40' : 'border-soft bg-white',
                                item.isNext ? 'ring-2 ring-wedding-band/20' : '',
                            ]"
                        >
                            <div class="flex items-center gap-8">
                                <span
                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full"
                                    :class="item.completed ? 'bg-wedding-success text-white' : 'border border-soft bg-white text-wedding-muted'"
                                >
                                    <span class="material-symbols-outlined" style="font-size: 14px;">{{ item.completed ? 'check' : 'horizontal_rule' }}</span>
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">Step {{ index + 1 }}:</p>
                                        <span class="text-xs uppercase tracking-[0.14em]" :class="item.completed ? 'text-wedding-successdark' : item.isNext ? 'text-wedding-band' : 'text-wedding-muted'">
                                        {{ item.completed ? 'Complete' : item.isNext ? 'Next' : 'Pending' }}
                                        </span>
                                    </div>
                                    <h3 class="mt-1 font-heading text-2xl leading-tight">{{ item.title }}</h3>
                                </div>
                            </div>
                            <div class="mt-4 min-w-0 md:mt-0">
                                <p class="truncate text-sm text-wedding-muted" :title="item.description">{{ item.description }}</p>
                                <p class="mt-2 truncate text-sm font-medium" :class="item.completed ? 'text-wedding-successdark' : 'text-red-700'" :title="item.statusText">
                                    {{ item.statusText }}
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <a :href="item.href" class="admin-btn setup-checklist-btn inline-flex w-full items-center justify-center gap-2 whitespace-nowrap px-4 py-3 text-center text-xs uppercase tracking-[0.12em]" :class="item.completed ? 'admin-tool-btn' : ''" @click="handleSetupChecklistClick($event, item)">
                                    <span class="material-symbols-outlined btn-icon">{{ item.ctaIcon }}</span>
                                    {{ item.ctaLabel }}
                                </a>
                            </div>
                        </article>
                    </div>
                </article>

                <div class="grid gap-6 xl:grid-cols-[1.35fr_0.9fr]">
                    <article id="site-information-section" class="content-section-block content-section-even scroll-mt-28">
                        <h2 class="font-heading text-3xl">Site Information</h2>

                        <p class="mt-4 text-sm uppercase tracking-[0.14em] text-wedding-muted">Public URL</p>
                        <p class="mt-1 text-lg">
                            <a :href="previewUrl" target="_blank" rel="noopener noreferrer" class="text-wedding-band underline decoration-wedding-band/50 underline-offset-4 hover:decoration-wedding-band">
                                {{ previewUrl }}
                            </a>
                        </p>
                        <div class="site-tools-wrap mt-4">
                            <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Site tools</p>
                            <div class="site-tools-grid mt-3">
                            <a :href="previewUrl" target="_blank" rel="noopener noreferrer" class="admin-tool-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]">
                                <span class="material-symbols-outlined btn-icon">visibility</span>
                                Preview Site
                            </a>
                            <button type="button" class="admin-tool-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" @click="copyPublicUrl">
                                <span class="material-symbols-outlined btn-icon">{{ copyLinkCopied ? 'check' : 'content_copy' }}</span>
                                {{ copyLinkCopied ? 'Link Copied' : 'Copy Link' }}
                            </button>
                            <button type="button" class="admin-tool-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" @click="sharePublicUrl">
                                <span class="material-symbols-outlined btn-icon">share</span>
                                Share
                            </button>
                            <button type="button" class="admin-tool-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" @click="openQrModal">
                                <span class="material-symbols-outlined btn-icon">qr_code_2</span>
                                Generate QR Code
                            </button>
                            </div>
                        </div>

                        <p class="mt-8 text-sm uppercase tracking-[0.14em] text-wedding-muted">Site Visibility</p>
                        <p class="mt-1 text-lg">{{ sitePublished ? 'Published' : 'Draft' }}</p>

                        <button type="button" class="admin-btn admin-btn-success mt-6 inline-flex items-center gap-2 px-6 py-4 text-sm uppercase tracking-[0.14em]" @click="toggleSitePublished">
                            <span class="material-symbols-outlined btn-icon">{{ sitePublished ? 'visibility_off' : 'publish' }}</span>
                            {{ sitePublished ? 'Move To Draft' : 'Publish Site' }}
                        </button>
                    </article>

                    <article class="content-section-block content-section-even">
                        <p class="text-xs uppercase tracking-[0.18em] text-wedding-muted">Quick overview</p>
                        <h2 class="mt-2 font-heading text-3xl">Your current numbers</h2>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <article v-for="card in dashboardCards" :key="card.label" class="dashboard-stat-card">
                                <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">{{ card.label }}</p>
                                <p class="mt-3 font-heading text-4xl">{{ card.value }}</p>
                            </article>
                        </div>
                    </article>
                </div>
            </section>

            <section v-if="section === 'content'" class="space-y-8 pb-32">
                <article class="card-frame bg-white">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-heading text-3xl">Create your website</h2>
                    </div>
                    <p class="mt-2 text-wedding-muted">
                        Update text, imagery, and your selected colour palette shown on your single-page wedding website. Use the info icons beside each section title for guidance and examples.
                        If you would like to see a completed demo version of the website to reference, then
                        <button type="button" class="inline underline decoration-wedding-band underline-offset-2" @click="openDemoChoiceModal">click here</button>.
                    </p>

                    <div v-if="content" class="mt-8 space-y-6">
                        <div id="website-theme-section" class="content-section-block content-section-odd">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">1</span>
                                <span class="font-heading text-3xl">Website Title and Theme Selection</span>
                            </h3>
                            <label class="mt-3 block text-sm uppercase tracking-[0.12em] text-wedding-muted">
                                Website Title
                                <input v-model="siteTitle" class="mt-2 w-full border border-soft bg-white px-4 py-3" placeholder="e.g. Kyle & Nicole's Wedding">
                            </label>

                            <div class="mt-8">
                                <p class="text-sm uppercase tracking-[0.12em] text-wedding-muted">Theme/template</p>
                                <p class="mt-2 text-sm text-wedding-muted">Choose the structure of your wedding website. Your colour palette is selected separately below.</p>
                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <article
                                        v-for="themeOption in themeOptions"
                                        :key="themeOption.layout"
                                        class="flex h-full flex-col justify-between border p-5 transition"
                                        :class="selectedThemeLayout === themeOption.layout ? 'border-wedding-band bg-white shadow-soft' : 'border-soft bg-[#F7F7F7] hover:border-wedding-band'"
                                    >
                                        <div>
                                            <span class="flex items-center justify-between gap-3">
                                                <span class="font-heading text-2xl text-wedding-text">{{ themeOption.name }}</span>
                                                <span v-if="selectedThemeLayout === themeOption.layout" class="material-symbols-outlined text-[20px] text-wedding-success">check_circle</span>
                                            </span>
                                            <span class="mt-3 block text-sm leading-relaxed text-wedding-muted">{{ themeOption.description }}</span>
                                        </div>
                                        <div class="mt-5 flex flex-wrap gap-2">
                                            <button
                                                type="button"
                                                class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                                                :class="selectedThemeLayout === themeOption.layout ? 'admin-btn-success' : ''"
                                                @click="selectThemeLayout(themeOption.layout)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">check</span>
                                                {{ selectedThemeLayout === themeOption.layout ? `${themeOption.name} selected` : `Select ${themeOption.name}` }}
                                            </button>
                                            <a
                                                :href="themePreviewUrl(themeOption.layout)"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="admin-btn border border-soft bg-white px-4 py-3 text-xs uppercase tracking-[0.12em]"
                                            >
                                                Preview
                                            </a>
                                        </div>
                                    </article>
                                </div>

                                <div class="mt-8 border-t border-soft pt-8">
                                    <p class="text-sm uppercase tracking-[0.12em] text-wedding-muted">Colour palette</p>
                                    <p class="mt-2 text-sm text-wedding-muted">Choose a professionally designed palette. It controls section backgrounds, buttons, hover colours, accents, borders and contrast-safe text.</p>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                        <article
                                            v-for="palette in colourPaletteOptions"
                                            :key="palette.slug"
                                            class="flex h-full flex-col justify-between border p-4 transition"
                                            :class="selectedColourPaletteSlug === palette.slug ? 'border-wedding-band bg-white shadow-soft' : 'border-soft bg-[#F7F7F7] hover:border-wedding-band'"
                                        >
                                            <div>
                                                <span class="flex items-start justify-between gap-3">
                                                    <span>
                                                        <span class="block font-heading text-xl text-wedding-text">{{ palette.name }}</span>
                                                        <span class="mt-2 block text-sm leading-relaxed text-wedding-muted">{{ palette.mood }}</span>
                                                    </span>
                                                    <span v-if="selectedColourPaletteSlug === palette.slug" class="material-symbols-outlined text-[20px] text-wedding-success">check_circle</span>
                                                </span>
                                                <div class="mt-4 flex flex-nowrap items-center gap-1.5" :aria-label="`${palette.name} colour swatches`">
                                                    <span
                                                        v-for="colour in paletteSwatches(palette)"
                                                        :key="`${palette.slug}-${colour}`"
                                                        class="theme-swatch-button pointer-events-none"
                                                        :style="{ backgroundColor: colour }"
                                                    ></span>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="admin-btn mt-5 inline-flex items-center justify-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                                                :class="selectedColourPaletteSlug === palette.slug ? 'admin-btn-success' : ''"
                                                @click="selectColourPalette(palette.slug)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">palette</span>
                                                {{ selectedColourPaletteSlug === palette.slug ? 'Selected' : 'Select Palette' }}
                                            </button>
                                        </article>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <div class="content-section-block content-section-even">
                        <h3 class="section-heading-with-badge">
                            <span class="section-step-badge">2</span>
                            <span class="font-heading text-3xl">Hero</span>
                        </h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Couple Names
                                <input v-model="content.hero.names" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Kicker
                                <input v-model="content.hero.kicker" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Wedding Date
                                <input v-model="content.hero.dateLine" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Location Line
                                <input v-model="content.hero.locationLine" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                        </div>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero RSVP Button Label
                            <input v-model="content.hero.buttonLabel" class="mt-2 w-full border border-soft px-4 py-3">
                        </label>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Hero Image
                            <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="file-input-field mt-2 w-full border border-soft bg-white px-4 py-3" @change="uploadContentImage($event, 'hero.image')">
                        </label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Image Horizontal Focus Point: {{ content.hero.imageFocusX }}%
                                <input v-model.number="content.hero.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Image Vertical Focus Point: {{ content.hero.imageFocusY }}%
                                <input v-model.number="content.hero.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="mt-[30px] border border-soft bg-white p-3">
                            <p class="mb-2 text-xs uppercase tracking-[0.12em] text-wedding-muted">Hero Image Preview</p>
                            <div class="mx-auto w-full max-w-5xl overflow-hidden border border-soft aspect-[16/7]">
                                <img
                                    :src="content.hero.image"
                                    alt="Hero preview"
                                    class="h-full w-full object-cover"
                                    :style="{ objectPosition: `${content.hero.imageFocusX ?? 50}% ${content.hero.imageFocusY ?? 50}%` }"
                                >
                            </div>
                        </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <div class="content-section-block content-section-odd">
                        <div class="mb-3">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">3</span>
                                <span class="font-heading text-3xl">Welcome</span>
                            </h3>
                        </div>
                        <div class="section-toggle-row">
                            <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('welcome') }" @click="toggleSectionVisibility('welcome')">
                                <span class="section-toggle-thumb">
                                    <span v-if="isSectionVisible('welcome')" class="material-symbols-outlined">check</span>
                                </span>
                            </button>
                            <span class="section-toggle-note">Show or hide this section.</span>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Heading
                                <input v-model="content.welcome.heading" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Signoff
                                <input v-model="content.welcome.signoff" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                        </div>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Letter</label>
                        <RichTextEditor v-model="content.welcome.letter" class="mt-2" tone="primary" />

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Welcome Image
                            <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="file-input-field mt-2 w-full border border-soft bg-white px-4 py-3" @change="uploadContentImage($event, 'welcome.image')">
                        </label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Image Horizontal Focus Point: {{ content.welcome.imageFocusX }}%
                                <input v-model.number="content.welcome.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Image Vertical Focus Point: {{ content.welcome.imageFocusY }}%
                                <input v-model.number="content.welcome.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="mt-[30px] border border-soft bg-white p-3">
                            <p class="mb-2 text-xs uppercase tracking-[0.12em] text-wedding-muted">Welcome Image Preview</p>
                            <div class="mx-auto w-full max-w-sm overflow-hidden border border-soft aspect-[4/5]">
                                <img
                                    :src="content.welcome.image"
                                    alt="Welcome preview"
                                    class="h-full w-full object-cover"
                                    :style="{ objectPosition: `${content.welcome.imageFocusX ?? 50}% ${content.welcome.imageFocusY ?? 50}%` }"
                                >
                            </div>
                        </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <div class="content-section-block content-section-even mt-2">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <h3 class="section-heading-with-badge">
                                    <span class="section-step-badge">4</span>
                                    <span class="font-heading text-3xl">Wedding Timeline</span>
                                </h3>
                                <button type="button" class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" :disabled="isTimelineAtMax" @click="addTimelineItem">
                                    <span class="material-symbols-outlined btn-icon">add</span>
                                    Add Time
                                </button>
                            </div>
                            <div class="section-toggle-row">
                                <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('timeline') }" @click="toggleSectionVisibility('timeline')">
                                    <span class="section-toggle-thumb">
                                        <span v-if="isSectionVisible('timeline')" class="material-symbols-outlined">check</span>
                                    </span>
                                </button>
                                <span class="section-toggle-note">Show or hide this section.</span>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Timeline Heading
                                    <input v-model="content.timeline.heading" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                                </label>
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Timeline Accent
                                    <input v-model="content.timeline.dateAccent" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                                </label>
                            </div>

                            <p v-if="isTimelineAtMax || isTimelineAtMin" class="mt-3 text-sm text-wedding-danger">
                                {{ isTimelineAtMax ? 'Maximum of 8 timeline items reached.' : 'Minimum of 3 timeline items is required.' }}
                            </p>

                            <div class="mt-4 space-y-3">
                                <div class="timeline-grid hidden border border-soft bg-wedding-secondary-light py-2 text-xs uppercase tracking-[0.12em] text-wedding-black md:grid">
                                    <span class="px-3">Time</span>
                                    <span class="px-3">Event</span>
                                    <span class="px-3">Description</span>
                                    <span class="text-center">Reorder</span>
                                    <span class="text-center">Delete</span>
                                </div>
                                <div
                                    v-for="(item, index) in content.timeline.items"
                                    :id="`timeline-item-${index}`"
                                    :key="index"
                                    class="timeline-grid grid border border-soft bg-wedding-bg py-3 md:items-center"
                                >
                                    <div class="px-3">
                                        <input v-model="item.time" placeholder="Time" class="w-full border border-soft bg-white px-3 py-2">
                                    </div>
                                    <div class="px-3">
                                        <input v-model="item.title" placeholder="Title" class="w-full border border-soft bg-white px-3 py-2">
                                    </div>
                                    <div class="px-3">
                                        <input v-model="item.description" placeholder="Description" class="w-full border border-soft bg-white px-3 py-2">
                                    </div>
                                    <div class="px-3">
                                        <div class="grid grid-cols-2 gap-2">
                                            <button
                                                type="button"
                                                class="admin-btn inline-flex w-full items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                :disabled="index === 0"
                                                title="Move up"
                                                @click="moveTimelineItem(index, -1)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">arrow_upward</span>
                                            </button>
                                            <button
                                                type="button"
                                                class="admin-btn inline-flex w-full items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                :disabled="index === content.timeline.items.length - 1"
                                                title="Move down"
                                                @click="moveTimelineItem(index, 1)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">arrow_downward</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="px-3">
                                        <button type="button" class="admin-btn admin-btn-danger inline-flex w-full items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]" :disabled="isTimelineAtMin" @click="removeTimelineItem(index)">
                                            <span class="material-symbols-outlined btn-icon">{{ isTimelineAtMin ? 'block' : 'close' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <div class="content-section-block content-section-odd">
                        <div class="mb-3">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">5</span>
                                <span class="font-heading text-3xl">Our Story</span>
                            </h3>
                        </div>
                        <div class="section-toggle-row">
                            <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('story') }" @click="toggleSectionVisibility('story')">
                                <span class="section-toggle-thumb">
                                    <span v-if="isSectionVisible('story')" class="material-symbols-outlined">check</span>
                                </span>
                            </button>
                            <span class="section-toggle-note">Show or hide this section.</span>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Our Story Heading
                                <input v-model="content.story.heading" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                            </label>

                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Our Story Accent (Date/Subtitle)
                                <input v-model="content.story.accent" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                            </label>
                        </div>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Our Story Text</label>
                        <RichTextEditor v-model="content.story.text" class="mt-2" tone="primary" />

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Our Story Image
                            <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="file-input-field mt-2 w-full border border-soft bg-white px-4 py-3" @change="uploadContentImage($event, 'story.image')">
                        </label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Story Image Horizontal Focus Point: {{ content.story.imageFocusX }}%
                                <input v-model.number="content.story.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Story Image Vertical Focus Point: {{ content.story.imageFocusY }}%
                                <input v-model.number="content.story.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="mt-[30px] border border-soft bg-white p-3">
                            <p class="mb-2 text-xs uppercase tracking-[0.12em] text-wedding-muted">Story Image Preview</p>
                            <div class="mx-auto w-full max-w-sm overflow-hidden border border-soft aspect-[4/5]">
                                <img
                                    :src="content.story.image"
                                    alt="Story preview"
                                    class="h-full w-full object-cover"
                                    :style="{ objectPosition: `${content.story.imageFocusX ?? 50}% ${content.story.imageFocusY ?? 50}%` }"
                                >
                            </div>
                        </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <div class="content-section-block content-section-even">
                        <div class="mb-3">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">6</span>
                                <span class="font-heading text-3xl">Venue</span>
                            </h3>
                        </div>
                        <div class="section-toggle-row">
                            <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('venue') }" @click="toggleSectionVisibility('venue')">
                                <span class="section-toggle-thumb">
                                    <span v-if="isSectionVisible('venue')" class="material-symbols-outlined">check</span>
                                </span>
                            </button>
                            <span class="section-toggle-note">Show or hide this section.</span>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Name
                                <input v-model="content.details.venue.name" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Address
                                <input v-model="content.details.venue.address" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                            </label>
                        </div>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Information</label>
                        <RichTextEditor v-model="content.details.venue.blurb" class="mt-2" tone="secondary" />

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Venue Image
                            <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="file-input-field mt-2 w-full border border-soft bg-white px-4 py-3" @change="uploadContentImage($event, 'details.image')">
                        </label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Image Horizontal Focus Point: {{ content.details.imageFocusX }}%
                                <input v-model.number="content.details.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Image Vertical Focus Point: {{ content.details.imageFocusY }}%
                                <input v-model.number="content.details.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="mt-[30px] border border-soft bg-white p-3">
                            <p class="mb-2 text-xs uppercase tracking-[0.12em] text-wedding-muted">Venue Image Preview</p>
                            <div class="mx-auto w-full max-w-sm overflow-hidden border border-soft aspect-[3/5]">
                                <img
                                    :src="content.details.image"
                                    alt="Venue preview"
                                    class="h-full w-full object-cover"
                                    :style="{ objectPosition: `${content.details.imageFocusX ?? 50}% ${content.details.imageFocusY ?? 50}%` }"
                                >
                            </div>
                        </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div class="content-section-block content-section-odd">
                            <div class="mb-3">
                                <h3 class="section-heading-with-badge">
                                    <span class="section-step-badge">7</span>
                                    <span class="font-heading text-3xl">Travel</span>
                                </h3>
                            </div>
                            <div class="section-toggle-row">
                                <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('travel') }" @click="toggleSectionVisibility('travel')">
                                    <span class="section-toggle-thumb">
                                        <span v-if="isSectionVisible('travel')" class="material-symbols-outlined">check</span>
                                    </span>
                                </button>
                                <span class="section-toggle-note">Show or hide this section.</span>
                            </div>

                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Travel Information</label>
                            <RichTextEditor v-model="content.details.travel" class="mt-2" tone="primary" />
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div id="menu-settings-section" class="content-section-block content-section-even">
                            <div class="mb-3">
                                <h3 class="section-heading-with-badge">
                                    <span class="section-step-badge">8</span>
                                    <span class="font-heading text-3xl">Menu & RSVP Settings</span>
                                </h3>
                            </div>
                            <div class="section-toggle-row">
                                <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('menu') }" @click="toggleSectionVisibility('menu')">
                                    <span class="section-toggle-thumb">
                                        <span v-if="isSectionVisible('menu')" class="material-symbols-outlined">check</span>
                                    </span>
                                </button>
                                <span class="section-toggle-note">Show or hide this section.</span>
                            </div>
                            <p class="mt-2 text-sm text-wedding-muted">This controls the menu section shown above FAQs and whether RSVP asks guests for meal selections.</p>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Section Heading
                                    <input v-model="rsvpSettings.menu_heading" class="mt-2 w-full border border-soft bg-white px-4 py-3" placeholder="Wedding Menu">
                                </label>
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Select your RSVP Meal Type
                                    <select v-model="rsvpSettings.meal_mode" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                                        <option value="set_menu">Set menu for all guests</option>
                                        <option value="options">Guests choose their own</option>
                                    </select>
                                </label>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Intro Text</label>
                                <RichTextEditor v-model="rsvpSettings.menu_intro" class="mt-2" tone="secondary" />
                            </div>

                            <div class="menu-courses-divider">
                                <hr class="w-full border-t border-wedding-band/70">
                            </div>

                            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <h4 class="font-heading text-2xl">Build Your Menu</h4>
                                <button type="button" class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="addMenuCourse">
                                    <span class="material-symbols-outlined btn-icon">add</span>
                                    Add Course
                                </button>
                            </div>

                            <div class="mt-4 space-y-6">
                                <div
                                    v-for="(course, courseIndex) in rsvpSettings.menu_courses"
                                    :id="`menu-course-${courseIndex}`"
                                    :key="course.id || `course-${courseIndex}`"
                                    class="border border-soft bg-wedding-bg p-4"
                                >
                                    <div class="grid gap-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Course Name</label>
                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    class="admin-btn inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                    :disabled="courseIndex === 0"
                                                    title="Move up"
                                                    @click="moveMenuCourse(courseIndex, -1)"
                                                >
                                                    <span class="material-symbols-outlined btn-icon">arrow_upward</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="admin-btn inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                    :disabled="courseIndex === rsvpSettings.menu_courses.length - 1"
                                                    title="Move down"
                                                    @click="moveMenuCourse(courseIndex, 1)"
                                                >
                                                    <span class="material-symbols-outlined btn-icon">arrow_downward</span>
                                                </button>
                                                <button type="button" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeMenuCourse(courseIndex)">
                                                    <span class="material-symbols-outlined btn-icon">close</span>
                                                    Remove Course
                                                </button>
                                            </div>
                                        </div>
                                        <input
                                            v-model="course.name"
                                            class="w-full border border-soft bg-white px-4 py-3"
                                            placeholder="e.g. Starter, Main or Dessert"
                                        >
                                    </div>

                                    <div class="mb-4 mt-5 flex items-center justify-between">
                                        <h4 class="font-heading text-xl">{{ course.name || `Course ${courseIndex + 1}` }}</h4>
                                        <button
                                            type="button"
                                            class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                            @click="addMenuCourseItem(courseIndex, canAddMenuOptions)"
                                        >
                                            <span class="material-symbols-outlined btn-icon">add</span>
                                            Add {{ course.name || 'Course' }}
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <div
                                            v-for="(item, itemIndex) in course.items"
                                            :id="`menu-course-${courseIndex}-item-${itemIndex}`"
                                            :key="`${course.id || courseIndex}-${itemIndex}`"
                                            class="grid gap-3 border border-soft bg-white p-3"
                                        >
                                            <input v-model="item.title" class="border border-soft bg-white px-3 py-2" placeholder="Dish title">
                                            <input v-model="item.description" class="border border-soft bg-white px-3 py-2" placeholder="Dish description">
                                            <div class="flex justify-end">
                                                <button type="button" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeMenuCourseItem(courseIndex, itemIndex)">
                                                    <span class="material-symbols-outlined btn-icon">close</span>
                                                    Remove {{ course.name || 'Course' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p v-if="!canAddMenuOptions" class="mt-4 text-xs text-wedding-muted">
                                Set menu allows one option per course. Switch to "Guests choose their own" to add extra options within a course.
                            </p>

                            <div v-if="rsvpSettings.meal_mode === 'set_menu'" class="mt-4">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Set Menu Description</label>
                                <RichTextEditor v-model="rsvpSettings.set_menu_description" class="mt-2" tone="secondary" />
                            </div>

                            <div class="menu-courses-divider">
                                <hr class="w-full border-t border-wedding-band/70">
                            </div>

                            <div class="space-y-4">
                                <div class="flex flex-wrap items-center gap-4">
                                    <h4 class="font-heading text-2xl">Do you need to add a kids menu?</h4>
                                    <button type="button" class="section-toggle" :class="{ 'is-active': rsvpSettings.kids_menu_enabled }" @click="rsvpSettings.kids_menu_enabled = !rsvpSettings.kids_menu_enabled">
                                        <span class="section-toggle-thumb">
                                            <span v-if="rsvpSettings.kids_menu_enabled" class="material-symbols-outlined">check</span>
                                        </span>
                                    </button>
                                    <span class="section-toggle-note">Turn this on only if children should have separate meal choices.</span>
                                </div>

                                <div v-if="rsvpSettings.kids_menu_enabled">
                                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                        <h4 class="font-heading text-2xl">Kids Menu</h4>
                                        <button
                                            type="button"
                                            class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                            @click="addKidsMenuItem"
                                        >
                                            <span class="material-symbols-outlined btn-icon">add</span>
                                            Add Kids Menu Item
                                        </button>
                                    </div>

                                    <div v-if="rsvpSettings.kids_menu_items.length === 0" class="rounded border border-soft bg-wedding-bg px-4 py-3 text-sm text-wedding-muted">
                                        Add a kids menu only if you want children to have a separate meal choice.
                                    </div>

                                    <div class="space-y-3">
                                        <div
                                            v-for="(item, itemIndex) in rsvpSettings.kids_menu_items"
                                            :id="`kids-menu-item-${itemIndex}`"
                                            :key="`kids-menu-${itemIndex}`"
                                            class="grid gap-3 border border-soft bg-white p-3"
                                        >
                                            <input v-model="item.title" class="border border-soft bg-white px-3 py-2" placeholder="Kids dish title">
                                            <input v-model="item.description" class="border border-soft bg-white px-3 py-2" placeholder="Kids dish description">
                                            <div class="flex justify-end">
                                                <button type="button" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeKidsMenuItem(itemIndex)">
                                                    <span class="material-symbols-outlined btn-icon">close</span>
                                                    Remove Kids Menu Item
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="rounded border border-soft bg-wedding-bg px-4 py-3 text-sm text-wedding-muted">
                                    Leave this off if children will eat from the main wedding menu or if children are not attending the wedding.
                                </div>
                            </div>

                            <div class="menu-courses-divider">
                                <hr class="w-full border-t border-wedding-band/70">
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-3">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted md:col-span-1">Menu Notes Card Title
                                    <input v-model="rsvpSettings.menu_note_title" class="mt-2 w-full border border-soft bg-white px-4 py-3" placeholder="Dining Notes">
                                </label>
                                <div class="md:col-span-2">
                                    <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Notes Card Text</label>
                                    <RichTextEditor v-model="rsvpSettings.menu_note_text" class="mt-2" tone="secondary" />
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div class="content-section-block content-section-odd">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="section-heading-with-badge">
                                    <span class="section-step-badge">9</span>
                                    <span class="font-heading text-3xl">FAQ Items</span>
                                </h3>
                                <button type="button" class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="addFaqItem">
                                    <span class="material-symbols-outlined btn-icon">add</span>
                                    Add FAQ
                                </button>
                            </div>
                            <div class="section-toggle-row">
                                <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('faqs') }" @click="toggleSectionVisibility('faqs')">
                                    <span class="section-toggle-thumb">
                                        <span v-if="isSectionVisible('faqs')" class="material-symbols-outlined">check</span>
                                    </span>
                                </button>
                                <span class="section-toggle-note">Show or hide this section.</span>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="(faq, index) in content.details.faqs"
                                    :id="`faq-item-${index}`"
                                    :key="index"
                                    class="rounded border border-soft bg-wedding-bg p-5"
                                >
                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">FAQ Item {{ index + 1 }}</p>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="admin-btn inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                :disabled="index === 0"
                                                title="Move up"
                                                @click="moveFaqItem(index, -1)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">arrow_upward</span>
                                            </button>
                                            <button
                                                type="button"
                                                class="admin-btn inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                :disabled="index === content.details.faqs.length - 1"
                                                title="Move down"
                                                @click="moveFaqItem(index, 1)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">arrow_downward</span>
                                            </button>
                                            <button type="button" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeFaqItem(index)">
                                                <span class="material-symbols-outlined btn-icon">close</span>
                                                Remove FAQ
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid gap-4">
                                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">
                                            Question
                                            <input
                                                v-model="faq.question"
                                                placeholder="e.g. Is there parking at the venue?"
                                                class="mt-2 w-full border border-soft bg-white px-4 py-3 normal-case tracking-normal text-wedding-text"
                                            >
                                        </label>

                                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">
                                            Answer
                                            <textarea
                                                v-model="faq.answer"
                                                rows="5"
                                                placeholder="e.g. Yes, there is free onsite parking available for all guests."
                                                class="mt-2 w-full border border-soft bg-white px-4 py-3 normal-case tracking-normal text-wedding-text"
                                            ></textarea>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div id="countdown-section" class="content-section-block content-section-even">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">10</span>
                                <span class="font-heading text-3xl">Countdown Timer</span>
                            </h3>
                            <div class="section-toggle-row">
                                <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('countdown') }" @click="toggleSectionVisibility('countdown')">
                                    <span class="section-toggle-thumb">
                                        <span v-if="isSectionVisible('countdown')" class="material-symbols-outlined">check</span>
                                    </span>
                                </button>
                                <span class="section-toggle-note">Show or hide this section.</span>
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">
                                    Ceremony Date &amp; Time
                                    <input
                                        v-model="content.countdown.targetDateTime"
                                        type="datetime-local"
                                        class="mt-2 w-full border border-soft bg-white px-4 py-3 normal-case tracking-normal text-wedding-text"
                                    >
                                </label>
                                <div class="rounded border border-soft bg-white px-4 py-4 text-sm text-wedding-muted">
                                    <p class="font-medium text-wedding-text">What this controls</p>
                                    <p class="mt-2">
                                        The countdown timer on your website will count down to this ceremony date and time.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div id="gallery-section" class="content-section-block content-section-odd">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                <h3 class="section-heading-with-badge">
                                    <span class="section-step-badge">11</span>
                                    <span class="font-heading text-3xl">Photo Gallery</span>
                                </h3>
                                <button
                                    type="button"
                                    class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                    :disabled="isGalleryAtMax"
                                    @click="addGalleryImage"
                                >
                                    <span class="material-symbols-outlined btn-icon">add</span>
                                    Add Image
                                </button>
                            </div>
                            <div class="section-toggle-row">
                                <button type="button" class="section-toggle" :class="{ 'is-active': isSectionVisible('gallery') }" @click="toggleSectionVisibility('gallery')">
                                    <span class="section-toggle-thumb">
                                        <span v-if="isSectionVisible('gallery')" class="material-symbols-outlined">check</span>
                                    </span>
                                </button>
                                <span class="section-toggle-note">Show or hide this section.</span>
                            </div>

                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Photo Gallery Heading
                                <input v-model="content.gallery.heading" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                            </label>

                            <p class="mt-3 text-sm text-wedding-muted">
                                Add between 2 and 8 images. The gallery automatically adapts the row layout so the images span the full width.
                            </p>
                            <p v-if="galleryValidationMessage" class="mt-2 text-sm text-wedding-danger">
                                {{ galleryValidationMessage }}
                            </p>

                            <div class="mt-5 grid gap-5 md:grid-cols-2">
                                <div
                                    v-for="(item, index) in content.gallery.items"
                                    :id="`gallery-item-${index}`"
                                    :key="`gallery-item-${index}`"
                                    class="border border-soft bg-wedding-bg p-4"
                                >
                                    <div class="mb-5 flex items-center justify-between gap-3">
                                        <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">Gallery Image {{ index + 1 }}</p>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="admin-btn inline-flex h-8 w-8 items-center justify-center p-0"
                                                :disabled="index === 0"
                                                title="Move image left"
                                                @click="moveGalleryImage(index, -1)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">chevron_left</span>
                                            </button>
                                            <button
                                                type="button"
                                                class="admin-btn inline-flex h-8 w-8 items-center justify-center p-0"
                                                :disabled="index === content.gallery.items.length - 1"
                                                title="Move image right"
                                                @click="moveGalleryImage(index, 1)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">chevron_right</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid items-stretch gap-[18px] sm:grid-cols-[0.7fr_1.3fr]">
                                        <label class="gallery-action-label admin-btn admin-btn-success flex h-12 cursor-pointer items-center justify-center gap-2 px-2 text-xs uppercase tracking-[0.12em] leading-none">
                                            <span class="material-symbols-outlined btn-icon">upload</span>
                                            Upload
                                            <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="hidden" @change="uploadContentImage($event, `gallery.items.${index}.image`)">
                                        </label>
                                        <button type="button" class="admin-btn flex h-12 items-center justify-center gap-2 whitespace-nowrap px-3 text-xs uppercase tracking-[0.12em] leading-none" @click="openImageLibrary(index)">
                                            <span class="material-symbols-outlined btn-icon">photo_library</span>
                                            Select from library
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Image Horizontal Focus Point: {{ item.imageFocusX }}%
                                            <input v-model.number="item.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                                        </label>
                                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Image Vertical Focus Point: {{ item.imageFocusY }}%
                                            <input v-model.number="item.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                                        </label>
                                    </div>

                                    <div class="mt-[30px] overflow-hidden border border-soft bg-white aspect-square">
                                        <img
                                            v-if="item.image"
                                            :src="item.image"
                                            alt="Gallery preview"
                                            class="h-full w-full object-cover"
                                            :style="{ objectPosition: `${item.imageFocusX ?? 50}% ${item.imageFocusY ?? 50}%` }"
                                        >
                                        <div v-else class="flex h-full w-full items-center justify-center px-6 text-center text-sm text-wedding-muted">
                                            Upload an image or select one from your library.
                                        </div>
                                    </div>

                                    <button type="button" class="admin-btn admin-btn-danger mt-4 inline-flex w-full items-center justify-center gap-2 px-3 py-3 text-xs uppercase tracking-[0.12em]" @click="removeGalleryImage(index)">
                                        <span class="material-symbols-outlined btn-icon">close</span>
                                        Remove Image
                                    </button>
                                </div>

                                <button
                                    v-if="!isGalleryAtMax"
                                    type="button"
                                    class="gallery-add-card flex min-h-[18rem] w-full flex-col items-center justify-center border border-dashed border-[#848484]/50 bg-[#F7F7F7] p-6 text-center transition hover:border-[#466369] hover:bg-[#F2ECE3]"
                                    @click="addGalleryImage"
                                >
                                    <span class="material-symbols-outlined text-[#848484]" style="font-size:52px;">add</span>
                                    <span class="mt-3 text-sm font-semibold uppercase tracking-[0.16em] text-[#848484]">Add a picture</span>
                                </button>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div class="content-section-block content-section-even">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">12</span>
                                <span class="font-heading text-3xl">Final RSVP Request</span>
                            </h3>

                            <div class="mt-4 grid gap-4 md:grid-cols-3">
                                <div class="space-y-4 md:col-span-1">
                                    <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">RSVP Request Title
                                        <input v-model="content.cta.title" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                                    </label>
                                    <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">RSVP Request Button Label
                                        <input v-model="content.cta.buttonLabel" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                                    </label>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Final RSVP Request Text</label>
                                    <RichTextEditor v-model="content.cta.text" class="mt-2" tone="primary" />
                                </div>
                            </div>
                        </div>

                    </div>
                </article>
            </section>

            <section v-if="section === 'parties'" class="card-frame bg-white space-y-6 guest-help-scope">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-heading text-3xl">Guest List</h2>
                    <div class="flex flex-wrap gap-2">
                        <a class="admin-btn px-4 py-3 text-xs uppercase tracking-[0.12em]" :href="partiesExportUrl">Export CSV</a>
                        <label class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]">
                            <span class="material-symbols-outlined btn-icon">upload</span>
                            Import CSV
                            <input type="file" class="hidden" accept=".csv" @change="importParties">
                        </label>
                    </div>
                </div>

                <div class="content-section-block content-section-even">
                    <h3 class="font-heading text-2xl">RSVP Email Settings</h3>
                    <p class="mt-2 text-sm text-wedding-muted">Preview the email your guests receive and send yourself a test before contacting your guest list.</p>
                    <div v-if="content" class="mt-4 space-y-4">
                        <div class="grid gap-3 lg:grid-cols-[1fr_220px_1fr]">
                            <div>
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                    RSVP Response Date Deadline
                                    <span class="ml-1 text-[11px] normal-case italic tracking-normal text-wedding-muted">(will be sent to guests)</span>
                                </label>
                                <input
                                    v-model="content.guest_list.responseDeadline"
                                    type="date"
                                    class="w-full border border-soft bg-white px-4 py-3 normal-case tracking-normal text-wedding-text"
                                >
                            </div>
                            <div>
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Preview guest type</label>
                                <select v-model="rsvpEmailPreviewGuestType" class="w-full border border-soft bg-white px-4 py-3 normal-case tracking-normal text-wedding-text">
                                    <option value="day">All Day Guest</option>
                                    <option value="evening">Evening Guest</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Send test to</label>
                                <input
                                    v-model="testRsvpEmailAddress"
                                    type="email"
                                    placeholder="your@email.com"
                                    class="w-full border border-soft bg-white px-4 py-3 normal-case tracking-normal text-wedding-text"
                                >
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 pb-4">
                            <button
                                class="admin-btn admin-btn-success inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                                type="button"
                                @click="saveGuestListEmailSettings"
                            >
                                <span class="material-symbols-outlined btn-icon">save</span>
                                Save RSVP Email Settings
                            </button>
                            <button
                                class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                                type="button"
                                @click="openRsvpEmailPreview"
                            >
                                <span class="material-symbols-outlined btn-icon">preview</span>
                                Preview Email
                            </button>
                            <button
                                class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                                type="button"
                                @click="sendTestRsvpEmail"
                            >
                                <span class="material-symbols-outlined btn-icon">outgoing_mail</span>
                                Send Test Email
                            </button>
                        </div>
                        <div class="border-t border-soft pt-8">
                            <div class="border border-soft bg-white p-4 md:flex md:items-center md:justify-between md:gap-6">
                                <div>
                                    <h4 class="font-heading text-xl">RSVP Reminders</h4>
                                    <a
                                        v-if="!sitePublished"
                                        :href="`${adminBaseUrl}#site-information-section`"
                                        class="mb-4 mt-3 inline-block text-sm font-semibold text-red-700 underline underline-offset-4 hover:text-red-900"
                                        @click="handleSiteInformationLinkClick"
                                    >
                                        Your website must be published before before you can send RSVP emails.
                                    </a>
                                    <p class="mt-1 text-sm text-wedding-muted">
                                        Send a reminder to parties with an email address who have not responded yet.
                                    </p>
                                    <p class="mt-2 text-sm text-wedding-muted">
                                        To send an RSVP email to one party only, use the email icon in the Actions column within
                                        <a href="#your-guests-section" class="font-semibold text-wedding-primary underline underline-offset-4 hover:text-wedding-green" @click.prevent="scrollToYourGuests">Your Guests</a>
                                        below.
                                    </p>
                                </div>
                                <div class="mt-4 md:mt-0 md:text-right">
                                    <p class="mb-2 text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                        Eligible parties: <span class="font-semibold text-wedding-black">{{ reminderEligibleParties.length }}</span>
                                    </p>
                                    <button
                                        class="admin-btn admin-btn-success inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                                        type="button"
                                        :disabled="!canSendRsvpEmails || !hasRsvpDeadline() || reminderEligibleParties.length === 0"
                                        :title="rsvpReminderSendTitle"
                                        @click="openSendRsvpReminderConfirmModal"
                                    >
                                        <span class="material-symbols-outlined btn-icon">notification_important</span>
                                        Send RSVP Reminders
                                    </button>
                                    <p v-if="rsvpReminderBlockedReason" class="mt-2 max-w-xs text-xs normal-case tracking-normal text-red-700 md:ml-auto">
                                        {{ rsvpReminderBlockedReason }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p v-if="rsvpEmailSettingsMessage" class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ rsvpEmailSettingsMessage }}
                        </p>
                        <p v-if="rsvpEmailSettingsError" class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ rsvpEmailSettingsError }}
                        </p>
                    </div>
                </div>

                <div id="create-party-section" class="content-section-block content-section-odd scroll-mt-28">
                    <h3 class="font-heading text-2xl">Create a Party</h3>
                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Party Name</label>
                            <input v-model="newParty.display_name" placeholder="Party name" class="w-full border border-soft px-4 py-3">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Guest Type</label>
                            <select v-model="newParty.guest_type" class="w-full border border-soft bg-white px-4 py-3">
                                <option value="day">All Day Guest</option>
                                <option value="evening">Evening Guest</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                EMAIL
                                <span class="ml-1 text-[11px] normal-case italic tracking-normal text-wedding-muted">(only required if sending via email)</span>
                            </label>
                            <input v-model="newParty.email" type="email" placeholder="party@example.com" class="w-full border border-soft px-4 py-3 normal-case tracking-normal">
                        </div>
                        <div class="flex items-end gap-2">
                            <div class="w-full">
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">RSVP Code</label>
                                <input v-model="newParty.code" placeholder="RSVP Code" class="h-12 w-full border border-soft px-4 py-3 uppercase">
                            </div>
                            <button class="admin-btn h-12 inline-flex items-center gap-2 px-3 text-xs uppercase tracking-[0.12em]" type="button" @click="generateCodeForCreate">
                                <span class="material-symbols-outlined btn-icon">autorenew</span>
                                Generate
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 rounded border border-soft bg-wedding-bg p-4">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                            <h4 class="font-heading text-xl">Guests for this party</h4>
                            <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                Invited seats: <span class="font-semibold text-wedding-black">{{ createPartyInvitedSeats }}</span>
                            </p>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="(guestRow, index) in newPartyGuests"
                                :key="`new-party-guest-${index}`"
                                class="guest-row-grid grid gap-2 border border-soft bg-white p-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(190px,0.75fr)_170px]"
                            >
                                <input v-model="guestRow.first_name" class="guest-row-input guest-name-input border border-soft bg-white px-3 py-2" placeholder="First name">
                                <input v-model="guestRow.last_name" class="guest-row-input guest-name-input border border-soft bg-white px-3 py-2" placeholder="Last name">
                                <label class="guest-option-control guest-row-input">
                                    <input v-model="guestRow.is_child" class="guest-option-checkbox bg-white" type="checkbox">
                                    Is this a Child?
                                </label>
                                <button
                                    type="button"
                                    class="admin-btn admin-btn-danger guest-row-button inline-flex items-center justify-center gap-1 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                    :disabled="newPartyGuests.length <= 1 || index === 0"
                                    :title="index === 0 ? 'The first guest in the party cannot be removed.' : 'Remove guest'"
                                    @click="removeNewPartyGuestRow(index)"
                                >
                                    <span class="material-symbols-outlined btn-icon">{{ newPartyGuests.length <= 1 || index === 0 ? 'block' : 'close' }}</span>
                                    Remove
                                </button>
                            </div>
                        </div>

                    <button class="admin-btn admin-btn-success mt-3 inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" type="button" @click="addNewPartyGuestRow">
                        <span class="material-symbols-outlined btn-icon">person_add</span>
                        Add Guest
                    </button>
                    <button class="admin-btn mt-3 ml-2 inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" type="button" @click="addAnonymousPlusOneRow">
                        <span class="material-symbols-outlined btn-icon">person_add</span>
                        Add Anonymous +1
                    </button>
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Additional notes for this party</label>
                    <input v-model="newParty.notes" placeholder="Additional notes for this party" class="w-full border border-soft px-4 py-3">
                </div>

                    <button class="admin-btn admin-btn-success mt-4 inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="createParty">
                        <span class="material-symbols-outlined btn-icon">add</span>
                        Create this Party
                    </button>
                    <p v-if="createPartyMessage" class="mt-3 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ createPartyMessage }}
                    </p>
                    <p v-if="createPartyError" class="mt-3 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ createPartyError }}
                    </p>
                </div>

                <div id="your-guests-section" class="content-section-block content-section-odd scroll-mt-28">
                    <p v-if="globalMessage" class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ globalMessage }}
                    </p>
                    <p v-if="globalError" class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ globalError }}
                    </p>

                    <div class="mb-4 border-b border-soft pb-3">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="font-heading text-2xl">Your Guests</h3>
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <p class="text-sm text-wedding-muted">Select parties below to action</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                        type="button"
                                        :disabled="!canSendRsvpEmails || selectedEmailableParties.length === 0"
                                        :title="rsvpEmailSendTitle"
                                        @click="openSendRsvpConfirmModal()"
                                    >
                                        <span class="material-symbols-outlined btn-icon">mail</span>
                                        Send Email RSVP
                                    </button>
                                    <button
                                        class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                        type="button"
                                        :disabled="selectedPartyIdsForEmail.length === 0"
                                        @click="deleteSelectedParties"
                                    >
                                        <span class="material-symbols-outlined btn-icon">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                        <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                            Search Parties
                            <input
                                v-model="partySearchTerm"
                                type="text"
                                placeholder="Search by party name, code, or guest"
                                class="mt-1 w-full border border-soft px-4 py-3 normal-case tracking-normal text-wedding-text"
                            >
                        </label>
                        <p class="text-sm text-wedding-muted">Showing {{ filteredParties.length }} of {{ parties.length }}</p>
                    </div>

                    <div class="mb-4 flex flex-wrap items-center gap-5 border border-soft bg-[#F7F7F7] px-4 py-3 text-xs text-wedding-muted">
                        <span class="uppercase tracking-[0.14em] text-wedding-text">RSVP key</span>
                        <span
                            v-for="item in rsvpStatusLegend"
                            :key="item.title"
                            class="inline-flex items-center gap-1"
                            :title="item.title"
                        >
                            <span class="material-symbols-outlined rsvp-status-icon-small" :style="{ color: item.color }">{{ item.icon }}</span>
                            {{ item.label }}
                        </span>
                        <span class="mx-1 text-wedding-muted/70" aria-hidden="true">|</span>
                        <span class="uppercase tracking-[0.14em] text-wedding-text">Guest key</span>
                        <span
                            v-for="item in guestTypeLegend"
                            :key="item.title"
                            class="inline-flex items-center gap-1"
                            :title="item.title"
                        >
                            <span class="material-symbols-outlined rsvp-status-icon-small" :style="{ color: item.color }">{{ item.icon }}</span>
                            {{ item.label }}
                        </span>
                    </div>

                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        <button
                            class="admin-btn inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                            type="button"
                            :disabled="filteredParties.length === 0"
                            @click="toggleSelectAllFilteredParties"
                        >
                            <span class="material-symbols-outlined btn-icon">{{ areAllFilteredPartiesSelected ? 'deselect' : 'select_all' }}</span>
                            {{ areAllFilteredPartiesSelected ? 'Clear selected' : 'Select all' }}
                        </button>
                    </div>

                    <div class="your-guests-table overflow-x-auto border border-soft/60 bg-white">
                        <table class="min-w-full table-fixed text-left text-sm">
                            <thead class="sticky top-0 bg-white">
                                <tr class="border-b border-soft text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                    <th class="w-[6%] px-2 py-2"><span class="sr-only">Select</span></th>
                                    <th class="w-[37%] px-2 py-2">Party</th>
                                    <th class="w-[14%] px-2 py-2">Email</th>
                                    <th class="w-[12%] whitespace-nowrap px-2 py-2 text-center">Email Sent</th>
                                    <th class="w-[7%] px-2 py-2 text-center">Code</th>
                                    <th class="w-[4%] px-1 py-2 text-center">Seats</th>
                                    <th class="w-[20%] px-2 py-2 text-center">
                                        <span class="actions-help-wrap">
                                            Actions
                                            <button
                                                type="button"
                                                class="actions-help-trigger"
                                                aria-label="Explain guest list actions"
                                                title="Explain guest list actions"
                                                @click.stop="actionsHelpOpen = !actionsHelpOpen"
                                            >
                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2" />
                                                    <path d="M12 10.5V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                    <circle cx="12" cy="7.7" r="1.2" fill="currentColor" />
                                                </svg>
                                            </button>
                                            <span class="actions-help-tooltip" :class="{ 'is-open': actionsHelpOpen }">
                                                <span><strong>View:</strong> preview party details.</span>
                                                <span><strong>Edit:</strong> update party and guest details.</span>
                                                <span><strong>Delete:</strong> remove the party.</span>
                                                <span><strong>WhatsApp:</strong> open a pre-filled invite message.</span>
                                                <span><strong>Copy:</strong> copy invite details to paste anywhere.</span>
                                                <span><strong>Email:</strong> send the RSVP request by email.</span>
                                            </span>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(partyItem, partyIndex) in filteredParties"
                                    :key="partyItem.id"
                                    class="border-b border-soft/60"
                                    :class="partyIndex % 2 === 0 ? 'bg-white' : 'bg-[#F7F7F7]'"
                                >
                                    <td class="py-2 pl-[13px] pr-2">
                                    <input
                                        class="h-4 w-4 cursor-pointer align-middle"
                                        type="checkbox"
                                        :checked="selectedPartyIdsForEmail.includes(partyItem.id)"
                                        :title="partyItem.email ? 'Select for email or delete action' : 'Select for delete action (no email set)'"
                                        @change="togglePartyEmailSelection(partyItem)"
                                    >
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="min-w-0">
                                        <p class="inline-flex max-w-full items-center gap-2 font-medium">
                                            <span
                                                class="material-symbols-outlined rsvp-status-icon"
                                                :style="{ color: guestTypeMeta(partyItem).color }"
                                                :title="guestTypeMeta(partyItem).title"
                                                :aria-label="guestTypeMeta(partyItem).title"
                                                role="img"
                                            >
                                                {{ guestTypeMeta(partyItem).icon }}
                                            </span>
                                            <span class="truncate">{{ partyItem.display_name }}</span>
                                            <span
                                                class="material-symbols-outlined rsvp-status-icon"
                                                :style="{ color: rsvpStatusMeta(partyItem).color }"
                                                :title="rsvpStatusMeta(partyItem).title"
                                                :aria-label="rsvpStatusMeta(partyItem).title"
                                                role="img"
                                            >
                                                {{ rsvpStatusMeta(partyItem).icon }}
                                            </span>
                                        </p>
                                        <p
                                            v-if="partyItem.guests.length"
                                            class="mt-1 block max-w-full truncate text-xs text-wedding-muted"
                                            :title="guestSummary(partyItem)"
                                        >
                                            Guests: {{ truncateText(guestSummary(partyItem), 42) }}
                                        </p>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 normal-case tracking-normal text-wedding-muted">
                                        <span
                                            class="inline-block max-w-full truncate align-middle"
                                            :title="partyItem.email || ''"
                                        >
                                            {{ partyItem.email ? truncateText(partyItem.email, 24) : '—' }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button
                                            v-if="partyItem.rsvp_email_sent"
                                            type="button"
                                            class="admin-btn admin-btn-success inline-flex items-center gap-1 px-2 py-1 text-xs normal-case tracking-normal"
                                            @click="openPartyEmailHistory(partyItem.id, partyItem.display_name)"
                                        >
                                            Yes
                                            <span class="material-symbols-outlined btn-icon">visibility</span>
                                        </button>
                                        <span v-else class="text-red-700">No</span>
                                    </td>
                                    <td class="px-2 py-2 text-center uppercase">{{ partyItem.code }}</td>
                                    <td class="px-1 py-2 text-center">{{ partyItem.max_guests }}</td>
                                    <td class="px-2 py-2">
                                        <div class="mx-auto grid w-fit grid-cols-3 gap-1.5">
                                            <button class="admin-btn admin-btn-view inline-flex h-10 w-10 items-center justify-center p-0 text-xs" type="button" title="View" @click="openViewPartyModal(partyItem.id)">
                                                <span class="material-symbols-outlined btn-icon">visibility</span>
                                            </button>
                                            <button class="admin-btn inline-flex h-10 w-10 items-center justify-center p-0 text-xs" type="button" title="Edit" @click="openEditPartyModal(partyItem.id)">
                                                <span class="material-symbols-outlined btn-icon">edit</span>
                                            </button>
                                            <button class="admin-btn admin-btn-danger inline-flex h-10 w-10 items-center justify-center p-0 text-xs" type="button" title="Delete" @click="deletePartyById(partyItem.id, partyItem.display_name)">
                                                <span class="material-symbols-outlined btn-icon">delete</span>
                                            </button>
                                            <button
                                                class="admin-btn admin-btn-copy inline-flex h-10 w-10 items-center justify-center p-0 text-xs"
                                                :class="{ 'admin-btn-copy-copied': copiedInvitationPartyId === partyItem.id }"
                                                type="button"
                                                :disabled="!sitePublished"
                                                :title="copiedInvitationPartyId === partyItem.id ? 'Invitation details copied' : (sitePublished ? 'Copy invitation details' : 'Publish your website before copying invitation details')"
                                                @click="copyInvitationDetails(partyItem)"
                                            >
                                                <span class="material-symbols-outlined btn-icon">{{ copiedInvitationPartyId === partyItem.id ? 'check' : 'content_copy' }}</span>
                                            </button>
                                            <span class="inline-flex" :class="{ 'disabled-action-tooltip': !partyItem.email || !canSendRsvpEmails }" :title="emailActionTitle(partyItem.email, 'Email RSVP')">
                                                <button class="admin-btn admin-btn-email inline-flex h-10 w-10 items-center justify-center p-0 text-xs" type="button" :disabled="!partyItem.email || !canSendRsvpEmails" @click="openSendRsvpConfirmModal([partyItem.id])">
                                                    <span class="material-symbols-outlined btn-icon">mail</span>
                                                </button>
                                            </span>
                                            <button
                                                class="admin-btn admin-btn-whatsapp inline-flex h-10 w-10 items-center justify-center p-0 text-xs"
                                                type="button"
                                                :disabled="!sitePublished"
                                                :title="sitePublished ? 'Share RSVP via WhatsApp' : 'Publish your website before sharing RSVP links by WhatsApp'"
                                                @click="openWhatsappInvite(partyItem)"
                                            >
                                                <svg class="action-svg-icon" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path d="M20 11.7a7.8 7.8 0 0 1-11.6 6.8L4 20l1.5-4.2A7.8 7.8 0 1 1 20 11.7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M8.9 8.2c.2-.4.4-.5.7-.5h.5c.2 0 .4.1.5.4l.7 1.6c.1.3 0 .5-.1.7l-.4.5c.6 1.1 1.5 2 2.7 2.6l.5-.4c.2-.2.4-.2.7-.1l1.5.7c.3.1.4.3.4.6v.5c0 .4-.2.6-.5.8-.5.3-1.1.4-1.8.2-3-.7-5.4-3.1-6.1-6.1-.2-.6-.1-1.2.2-1.5Z" fill="currentColor" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <div v-if="viewPartyModalOpen && selectedParty" class="fixed inset-0 z-[79] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closeViewPartyModal">
                <div class="guest-help-scope mx-auto my-4 flex w-full max-w-3xl flex-col border border-soft bg-white p-6 shadow-soft md:my-12 md:max-h-[calc(100vh-6rem)] md:overflow-y-auto md:p-8">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-3xl">Party Details</h3>
                            <p class="mt-2 text-sm text-wedding-muted">{{ selectedParty.display_name }}</p>
                        </div>
                        <button class="modal-close-x" type="button" aria-label="Close party details modal" title="Close" @click="closeViewPartyModal">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded border border-soft bg-[#F7F7F7] p-4">
                            <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Party Name</p>
                            <p class="mt-1 text-base text-wedding-text">{{ selectedParty.display_name }}</p>
                        </div>
                        <div class="rounded border border-soft bg-[#F7F7F7] p-4">
                            <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Guest Type</p>
                            <p class="mt-1 text-base text-wedding-text">{{ selectedParty.guest_type_label || (selectedParty.guest_type === 'evening' ? 'Evening Guest' : 'All Day Guest') }}</p>
                        </div>
                        <div class="rounded border border-soft bg-[#F7F7F7] p-4">
                            <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Email</p>
                            <p class="mt-1 break-all text-base normal-case tracking-normal text-wedding-text">{{ selectedParty.email || 'No email address has been entered for this party' }}</p>
                        </div>
                        <div class="rounded border border-soft bg-[#F7F7F7] p-4">
                            <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">RSVP Code</p>
                            <p class="mt-1 text-base uppercase text-wedding-text">{{ selectedParty.code }}</p>
                        </div>
                        <div class="rounded border border-soft bg-[#F7F7F7] p-4">
                            <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Seats</p>
                            <p class="mt-1 text-base text-wedding-text">{{ selectedParty.max_guests }}</p>
                        </div>
                        <div class="rounded border border-soft bg-[#F7F7F7] p-4">
                            <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Email RSVP Request Sent</p>
                            <p class="mt-1 text-base text-wedding-text">
                                <template v-if="selectedParty.rsvp_email_sent">
                                    Yes, email sent at {{ formatDateTime(selectedParty.rsvp_email_sent_at) }}
                                </template>
                                <template v-else>
                                    No
                                </template>
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 rounded border border-soft bg-[#F7F7F7] p-4">
                        <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Guests</p>
                        <div class="mt-3 space-y-2">
                            <div
                                v-for="(guest, index) in selectedParty.guests"
                                :key="`view-guest-${guest.id}`"
                                class="flex items-center justify-between gap-3 rounded border border-soft bg-white px-4 py-3"
                            >
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-wedding-text">{{ formatGuestName(guest) }}</p>
                                    <p class="mt-1 text-xs text-wedding-muted">
                                        {{ index === 0 ? 'Lead guest' : 'Additional guest' }}<span v-if="guest.is_child"> · Child guest</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedParty.notes" class="mt-5 rounded border border-soft bg-[#F7F7F7] p-4">
                        <p class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Additional Notes</p>
                        <p class="mt-2 text-sm text-wedding-text">{{ selectedParty.notes }}</p>
                    </div>

                    <div class="mt-6 flex flex-wrap justify-end gap-3">
                        <button
                            class="admin-btn admin-btn-whatsapp inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                            type="button"
                            :disabled="!sitePublished"
                            :title="sitePublished ? 'Share RSVP via WhatsApp' : 'Publish your website before sharing RSVP links by WhatsApp'"
                            @click="openWhatsappInvite(selectedParty)"
                        >
                            <svg class="action-svg-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 11.7a7.8 7.8 0 0 1-11.6 6.8L4 20l1.5-4.2A7.8 7.8 0 1 1 20 11.7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.9 8.2c.2-.4.4-.5.7-.5h.5c.2 0 .4.1.5.4l.7 1.6c.1.3 0 .5-.1.7l-.4.5c.6 1.1 1.5 2 2.7 2.6l.5-.4c.2-.2.4-.2.7-.1l1.5.7c.3.1.4.3.4.6v.5c0 .4-.2.6-.5.8-.5.3-1.1.4-1.8.2-3-.7-5.4-3.1-6.1-6.1-.2-.6-.1-1.2.2-1.5Z" fill="currentColor" />
                            </svg>
                            WhatsApp RSVP
                        </button>
                        <button class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="openEditFromViewModal">
                            <span class="material-symbols-outlined btn-icon">edit</span>
                            Edit this Party
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="editPartyModalOpen && selectedParty" class="fixed inset-0 z-[80] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closeEditPartyModal">
                <div class="guest-help-scope mx-auto my-4 flex w-full max-w-4xl flex-col border border-soft bg-white p-6 shadow-soft md:my-12 md:max-h-[calc(100vh-6rem)] md:p-8">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-heading text-3xl">Edit Party · {{ selectedParty.display_name }}</h3>
                        <button class="modal-close-x" type="button" aria-label="Close edit party modal" title="Close" @click="closeEditPartyModal">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="party-modal-scroll mt-4 min-h-0 flex-1 overflow-y-scroll pr-3">
                    <p class="mt-3 text-sm text-wedding-muted">
                        RSVP request sent:
                        <button
                            v-if="selectedParty.rsvp_email_sent"
                            type="button"
                            class="admin-btn admin-btn-success ml-1 inline-flex items-center gap-1 px-2 py-1 text-xs normal-case tracking-normal"
                            @click="openPartyEmailHistory(selectedParty.id, selectedParty.display_name)"
                        >
                            Yes · {{ formatDateTime(selectedParty.rsvp_email_sent_at) }}
                            <span class="material-symbols-outlined btn-icon">visibility</span>
                        </button>
                        <span v-else class="ml-1 text-red-700">No</span>
                    </p>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Party Name</label>
                            <input v-model="selectedParty.display_name" class="w-full border border-soft px-4 py-3">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Guest Type</label>
                            <select v-model="selectedParty.guest_type" class="w-full border border-soft bg-white px-4 py-3">
                                <option value="day">All Day Guest</option>
                                <option value="evening">Evening Guest</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                EMAIL
                                <span class="ml-1 text-[11px] normal-case italic tracking-normal text-wedding-muted">(only required if sending via email)</span>
                            </label>
                            <input v-model="selectedParty.email" type="email" class="w-full border border-soft px-4 py-3 normal-case tracking-normal">
                        </div>
                        <div class="flex items-end gap-2">
                            <div class="w-full">
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">RSVP Code</label>
                                <input v-model="selectedParty.code" placeholder="RSVP Code" class="h-12 w-full border border-soft px-4 py-3 uppercase">
                            </div>
                            <button class="admin-btn h-12 inline-flex items-center gap-2 px-3 text-xs uppercase tracking-[0.12em]" type="button" @click="generateCodeForSelectedParty">
                                <span class="material-symbols-outlined btn-icon">autorenew</span>
                                Generate
                            </button>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Max Guests</label>
                            <input v-model.number="selectedParty.max_guests" type="number" min="1" max="20" class="w-full border border-soft px-4 py-3">
                        </div>
                        <div class="md:col-span-2 mt-1">
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Notes</label>
                            <textarea v-model="selectedParty.notes" rows="3" class="w-full border border-soft px-4 py-3" placeholder="Notes"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="updateParty">
                            <span class="material-symbols-outlined btn-icon">save</span>
                            Save Party
                        </button>
                        <button class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="deleteParty">
                            <span class="material-symbols-outlined btn-icon">close</span>
                            Remove Party
                        </button>
                    </div>

                    <h4 class="mt-8 font-heading text-2xl">Guests</h4>
                    <div class="mt-3 space-y-2">
                        <div v-for="(guest, index) in selectedParty.guests" :key="guest.id" class="guest-row-grid grid gap-2 border border-soft p-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(190px,0.75fr)_170px]">
                            <input v-model="guest.first_name" class="guest-row-input guest-name-input border border-soft bg-white px-3 py-2">
                            <input v-model="guest.last_name" class="guest-row-input guest-name-input border border-soft bg-white px-3 py-2">
                            <label class="guest-option-control guest-row-input">
                                <input v-model="guest.is_child" class="guest-option-checkbox bg-white" type="checkbox">
                                Is this a Child?
                            </label>
                            <div class="flex gap-2">
                                <button class="admin-btn guest-row-button inline-flex items-center gap-1 px-3 py-2 text-xs" type="button" @click="updateGuest(guest)">
                                    <span class="material-symbols-outlined btn-icon">save</span>
                                    Save
                                </button>
                                <button
                                    class="admin-btn admin-btn-danger guest-row-button inline-flex items-center gap-1 px-3 py-2 text-xs"
                                    type="button"
                                    :disabled="index === 0"
                                    :title="index === 0 ? 'The first guest in the party cannot be removed.' : 'Remove guest'"
                                    @click="deleteGuest(guest, index)"
                                >
                                    <span class="material-symbols-outlined btn-icon">{{ index === 0 ? 'block' : 'close' }}</span>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid items-end gap-2 pb-6 md:grid-cols-4">
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">First Name</label>
                            <input v-model="newGuest.first_name" placeholder="First name" class="w-full border border-soft bg-white px-3 py-2">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Last Name</label>
                            <input v-model="newGuest.last_name" placeholder="Last name" class="w-full border border-soft bg-white px-3 py-2">
                        </div>
                        <label class="guest-option-control guest-row-input">
                            <input v-model="newGuest.is_child" class="guest-option-checkbox bg-white" type="checkbox">
                            Is this a Child?
                        </label>
                        <button class="admin-btn admin-btn-success h-12 inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" type="button" @click="addGuest">
                            <span class="material-symbols-outlined btn-icon">person_add</span>
                            Add Guest
                        </button>
                        <button class="admin-btn h-12 inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" type="button" @click="addAnonymousPlusOneGuest">
                            <span class="material-symbols-outlined btn-icon">person_add</span>
                            Add Anonymous +1
                        </button>
                    </div>
                    </div>
                </div>
            </div>

            <div v-if="sendRsvpConfirmModal.open" class="fixed inset-0 z-[82] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closeSendRsvpConfirmModal">
                <div class="mx-auto my-4 w-full max-w-2xl border border-soft bg-white p-6 shadow-soft md:my-12">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-heading text-3xl">Send Email RSVP Request</h3>
                        <button class="modal-close-x" type="button" aria-label="Close send RSVP confirmation" title="Close" @click="closeSendRsvpConfirmModal">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <p class="mt-3 text-wedding-muted">Are you sure you want to send an RSVP request to the selected parties below?</p>
                    <div class="mt-4 max-h-64 overflow-y-auto border border-soft bg-wedding-bg p-3">
                        <ul class="space-y-2 text-sm">
                            <li v-for="party in selectedEmailableParties" :key="party.id" class="border border-soft bg-white px-3 py-2">
                                <p class="font-medium">{{ party.display_name }}</p>
                                <p class="text-xs text-wedding-muted normal-case tracking-normal">{{ party.email }}</p>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-5 flex justify-end gap-3">
                        <button class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="closeSendRsvpConfirmModal">
                            <span class="material-symbols-outlined btn-icon">close</span>
                            Cancel
                        </button>
                        <button class="admin-btn admin-btn-success inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="sendRsvpEmailsToSelected">
                            <span class="material-symbols-outlined btn-icon">send</span>
                            Send
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="sendRsvpReminderConfirmModal.open" class="fixed inset-0 z-[82] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closeSendRsvpReminderConfirmModal">
                <div class="mx-auto my-4 w-full max-w-2xl border border-soft bg-white p-6 shadow-soft md:my-12">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-3xl">Send RSVP Reminders</h3>
                            <p class="mt-2 text-sm text-wedding-muted">
                                These reminders will only go to parties with an email address and no RSVP response yet.
                            </p>
                        </div>
                        <button class="modal-close-x" type="button" aria-label="Close RSVP reminder confirmation" title="Close" @click="closeSendRsvpReminderConfirmModal">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="mt-4 max-h-64 overflow-y-auto border border-soft bg-wedding-bg p-3">
                        <ul class="space-y-2 text-sm">
                            <li v-for="party in reminderEligibleParties" :key="party.id" class="border border-soft bg-white px-3 py-2">
                                <p class="font-medium">{{ party.display_name }}</p>
                                <p class="text-xs text-wedding-muted normal-case tracking-normal">{{ party.email }}</p>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-5 flex justify-end gap-3">
                        <button class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="closeSendRsvpReminderConfirmModal">
                            <span class="material-symbols-outlined btn-icon">close</span>
                            Cancel
                        </button>
                        <button class="admin-btn admin-btn-success inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="sendRsvpReminderEmails">
                            <span class="material-symbols-outlined btn-icon">send</span>
                            Send Reminders
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="rsvpEmailPreviewModal.open" class="fixed inset-0 z-[83] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closeRsvpEmailPreview">
                <div class="mx-auto my-4 flex w-full max-w-4xl flex-col border border-soft bg-white p-6 shadow-soft md:my-12 md:max-h-[calc(100vh-6rem)]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-3xl">RSVP Email Preview</h3>
                            <p class="mt-2 text-sm text-wedding-muted">This preview uses sample guest details and your current RSVP email settings.</p>
                        </div>
                        <button class="modal-close-x" type="button" aria-label="Close RSVP email preview" title="Close" @click="closeRsvpEmailPreview">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="mt-5 min-h-[420px] overflow-hidden border border-soft bg-wedding-bg">
                        <div v-if="rsvpEmailPreviewModal.loading" class="flex min-h-[420px] items-center justify-center p-8 text-sm text-wedding-muted">
                            Loading email preview...
                        </div>
                        <div v-else-if="rsvpEmailPreviewModal.error" class="p-6 text-sm text-red-700">
                            {{ rsvpEmailPreviewModal.error }}
                        </div>
                        <iframe
                            v-else
                            title="RSVP email preview"
                            class="h-[70vh] min-h-[420px] w-full bg-white"
                            :srcdoc="rsvpEmailPreviewModal.html"
                        ></iframe>
                    </div>
                </div>
            </div>

            <div v-if="emailHistoryModal.open" class="fixed inset-0 z-[83] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closePartyEmailHistory">
                <div class="mx-auto my-4 w-full max-w-2xl border border-soft bg-white p-6 shadow-soft md:my-12">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-heading text-3xl">RSVP Request History · {{ emailHistoryModal.partyName }}</h3>
                        <button class="modal-close-x" type="button" aria-label="Close RSVP request history" title="Close" @click="closePartyEmailHistory">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="mt-4 max-h-72 overflow-y-auto border border-soft bg-wedding-bg p-3">
                        <p v-if="emailHistoryModal.loading" class="text-sm text-wedding-muted">Loading history...</p>
                        <p v-else-if="emailHistoryModal.error" class="text-sm text-red-700">{{ emailHistoryModal.error }}</p>
                        <ul v-else-if="emailHistoryModal.history.length" class="space-y-2">
                            <li v-for="entry in emailHistoryModal.history" :key="entry.id" class="border border-soft bg-white px-3 py-2 text-sm">
                                <p class="font-medium normal-case tracking-normal">{{ entry.type_label || 'Invite' }} · {{ entry.sent_to_email }}</p>
                                <p class="text-xs text-wedding-muted">{{ formatDateTime(entry.sent_at) }}</p>
                            </li>
                        </ul>
                        <p v-else class="text-sm text-wedding-muted">No RSVP email history found for this party.</p>
                    </div>
                </div>
            </div>

            <section v-if="section === 'rsvps'" class="card-frame bg-white">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-heading text-3xl">RSVP Responses</h2>
                    <a class="admin-btn border border-soft px-4 py-2 text-xs uppercase tracking-[0.12em]" :href="rsvpsExportUrl">Export RSVP CSV</a>
                </div>

                <div class="mb-4 grid gap-3 md:grid-cols-4">
                    <label class="text-xs uppercase tracking-[0.12em] text-wedding-muted md:col-span-4">
                        Search RSVP Responses
                        <input
                            v-model="rsvpSearchTerm"
                            type="text"
                            placeholder="Search by guest list name or code"
                            class="mt-1 w-full border border-soft bg-white px-3 py-2 text-sm normal-case tracking-normal text-wedding-text"
                        >
                    </label>
                    <label class="text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        Response Status Filter
                        <select v-model="rsvpStatusFilter" class="mt-1 w-full border border-soft bg-white px-3 py-2 text-sm normal-case tracking-normal text-wedding-text">
                            <option value="all">All Statuses</option>
                            <option value="attending">Attending</option>
                            <option value="not_attending">Not Attending</option>
                            <option value="no_response">No Response</option>
                        </select>
                    </label>
                    <label class="text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        Guest Type Filter
                        <select v-model="rsvpGuestTypeFilter" class="mt-1 w-full border border-soft bg-white px-3 py-2 text-sm normal-case tracking-normal text-wedding-text">
                            <option value="all">All Guest Types</option>
                            <option value="day">All Day Guests</option>
                            <option value="evening">Evening Guests</option>
                        </select>
                    </label>
                    <div class="flex items-end text-sm text-wedding-muted">
                        Showing {{ filteredRsvpRows.length }} of {{ rsvpRows.length }} guest lists
                    </div>
                </div>

                <div class="space-y-3">
                    <article
                        v-for="(row, rowIndex) in filteredRsvpRows"
                        :key="row.party_id"
                        class="border border-soft p-4"
                        :class="rowIndex % 2 === 0 ? 'bg-white' : 'bg-[#F7F7F7]'"
                    >
                        <div class="grid gap-4 md:grid-cols-[minmax(0,0.95fr)_minmax(0,1.65fr)_auto] md:items-center">
                            <div class="min-w-0 space-y-2.5">
                                <h3 class="inline-flex items-center gap-2 font-heading text-2xl">
                                    <span class="truncate">{{ row.party_name }}</span>
                                </h3>
                                <p class="text-sm text-wedding-muted uppercase">Code: {{ row.code }}</p>
                                <p class="inline-flex items-center gap-1 text-sm text-wedding-muted">
                                    <span
                                        class="material-symbols-outlined rsvp-status-icon-small"
                                        :style="{ color: guestTypeMeta(row).color }"
                                        :title="guestTypeMeta(row).title"
                                        :aria-label="guestTypeMeta(row).title"
                                        role="img"
                                    >
                                        {{ guestTypeMeta(row).icon }}
                                    </span>
                                    {{ guestTypeMeta(row).label.toLowerCase() }}
                                </p>
                            </div>

                            <div class="space-y-2 border-soft text-sm text-wedding-muted md:border-l md:pl-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Status</span>
                                    <span
                                        class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-medium uppercase tracking-[0.08em]"
                                        :class="{
                                            'border-emerald-200 bg-emerald-50 text-emerald-700': row.rsvp?.status === 'attending',
                                            'border-red-200 bg-red-50 text-red-700': row.rsvp?.status === 'not_attending',
                                            'border-[#D79A2B] bg-[#D79A2B]/10 text-[#D79A2B]': !row.rsvp?.status,
                                        }"
                                    >
                                        {{ row.rsvp ? formatStatus(row.rsvp.status) : 'No Response' }}
                                    </span>
                                    <span>Attending: {{ row.rsvp?.attending_count || 0 }} / {{ row.max_guests }}</span>
                                </div>

                                <p v-if="row.rsvp?.attending_guest_names?.length" class="max-w-full truncate" :title="row.rsvp.attending_guest_names.join(', ')">
                                    <span class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Attending guests</span>
                                    <span class="ml-2">{{ row.rsvp.attending_guest_names.join(', ') }}</span>
                                </p>
                                <p v-else class="text-wedding-muted">
                                    <span class="text-xs uppercase tracking-[0.12em] text-wedding-muted">Attending guests</span>
                                    <span class="ml-2">None selected yet</span>
                                </p>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs uppercase tracking-[0.12em] text-wedding-muted">RSVP request sent</span>
                                    <template v-if="row.rsvp_email_sent">
                                        <span class="text-emerald-700">Yes, email sent at {{ formatDate(row.rsvp_email_sent_at) }}</span>
                                        <span class="text-wedding-muted">-</span>
                                        <button
                                            type="button"
                                            class="border-0 bg-transparent p-0 text-sm font-medium text-wedding-band underline decoration-wedding-band/50 underline-offset-4 transition hover:text-wedding-primarygreen hover:decoration-wedding-primarygreen"
                                            title="View RSVP email history"
                                            @click="openPartyEmailHistory(row.party_id, row.party_name)"
                                        >
                                            View RSVP History
                                        </button>
                                    </template>
                                    <span v-else-if="row.email" class="text-red-700">No</span>
                                    <span v-else class="text-wedding-muted">No email address entered</span>
                                </div>
                            </div>

                            <div class="grid w-fit grid-cols-4 gap-1.5 md:grid-cols-2 md:justify-self-end">
                                <span class="inline-flex" :class="{ 'disabled-action-tooltip': !row.rsvp }" :title="viewResponseActionTitle(row)">
                                    <button
                                        class="admin-btn admin-btn-view inline-flex h-10 w-10 items-center justify-center p-0 text-xs"
                                        type="button"
                                        :disabled="!row.rsvp"
                                        @click="openRsvpResponseModal(row)"
                                    >
                                        <span class="material-symbols-outlined btn-icon">visibility</span>
                                    </button>
                                </span>
                                <button
                                    class="admin-btn inline-flex h-10 w-10 items-center justify-center p-0 text-xs"
                                    :class="!row.rsvp?.status ? 'admin-btn-success' : ''"
                                    type="button"
                                    :title="row.rsvp?.status ? 'Edit RSVP' : 'Manually RSVP'"
                                    @click="editRsvp(row)"
                                >
                                    <span class="material-symbols-outlined btn-icon">{{ row.rsvp?.status ? 'edit' : 'add' }}</span>
                                </button>
                                <span class="inline-flex" :class="{ 'disabled-action-tooltip': !row.email || !canSendRsvpEmails }" :title="emailActionTitle(row.email, 'Email Guest')">
                                    <button
                                        class="admin-btn admin-btn-success inline-flex h-10 w-10 items-center justify-center p-0 text-xs"
                                        type="button"
                                        :disabled="!row.email || !canSendRsvpEmails"
                                        @click="openSendRsvpConfirmModal([row.party_id])"
                                    >
                                        <span class="material-symbols-outlined btn-icon">mail</span>
                                    </button>
                                </span>
                                <button
                                    class="admin-btn admin-btn-danger inline-flex h-10 w-10 items-center justify-center p-0 text-xs"
                                    type="button"
                                    title="Delete Guest"
                                    @click="deletePartyById(row.party_id, row.party_name)"
                                >
                                    <span class="material-symbols-outlined btn-icon">delete</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <p v-if="filteredRsvpRows.length === 0" class="border border-soft bg-wedding-bg px-4 py-3 text-sm text-wedding-muted">
                        No guest lists match the selected filters.
                    </p>
                </div>

            </section>
        </main>

        <div v-if="selectedRsvpResponse" class="fixed inset-0 z-[72] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closeRsvpResponseModal">
            <div class="mx-auto my-4 w-full max-w-3xl border border-soft bg-white p-6 shadow-soft md:my-16 md:p-8">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-wedding-muted">RSVP Response</p>
                        <h3 class="mt-1 font-heading text-3xl">{{ selectedRsvpResponse.party_name }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-wedding-muted">
                            <span class="inline-flex items-center gap-1">
                                <span
                                    class="material-symbols-outlined rsvp-status-icon-small"
                                    :style="{ color: guestTypeMeta(selectedRsvpResponse).color }"
                                    :title="guestTypeMeta(selectedRsvpResponse).title"
                                    aria-hidden="true"
                                >
                                    {{ guestTypeMeta(selectedRsvpResponse).icon }}
                                </span>
                                {{ selectedRsvpResponse.guest_type_label || (selectedRsvpResponse.guest_type === 'evening' ? 'Evening Guest' : 'All Day Guest') }}
                            </span>
                            <span>Code: {{ selectedRsvpResponse.code }}</span>
                        </div>
                    </div>
                    <button class="modal-close-x" type="button" aria-label="Close RSVP response details" title="Close" @click="closeRsvpResponseModal">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-3">
                    <article class="border border-soft bg-wedding-bg p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Status</p>
                        <p class="mt-2 text-lg font-medium">{{ selectedRsvpResponse.rsvp ? formatStatus(selectedRsvpResponse.rsvp.status) : 'No Response' }}</p>
                    </article>
                    <article class="border border-soft bg-wedding-bg p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Attending</p>
                        <p class="mt-2 text-lg font-medium">{{ selectedRsvpResponse.rsvp?.attending_count || 0 }} / {{ selectedRsvpResponse.max_guests }}</p>
                    </article>
                    <article class="border border-soft bg-wedding-bg p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Submitted</p>
                        <p class="mt-2 text-sm text-wedding-text">{{ selectedRsvpResponse.rsvp?.updated_at ? formatDateTime(selectedRsvpResponse.rsvp.updated_at) : 'Not submitted yet' }}</p>
                    </article>
                </div>

                <div class="mt-5 space-y-4">
                    <article class="border border-soft bg-[#F7F7F7] p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Attending Guests</p>
                        <p v-if="selectedRsvpResponse.rsvp?.attending_guest_names?.length" class="mt-2 text-wedding-text">
                            {{ selectedRsvpResponse.rsvp.attending_guest_names.join(', ') }}
                        </p>
                        <p v-else class="mt-2 text-wedding-muted">No attending guests have been selected.</p>
                    </article>

                    <article class="border border-soft bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Dietary Requirements</p>
                        <p v-if="selectedRsvpResponse.rsvp?.dietary_restrictions" class="mt-2 whitespace-pre-line text-wedding-text">
                            {{ selectedRsvpResponse.rsvp.dietary_restrictions }}
                        </p>
                        <p v-else class="mt-2 text-wedding-muted">No dietary requirements provided.</p>
                    </article>

                    <article class="border border-soft bg-[#F7F7F7] p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Message To The Couple</p>
                        <p v-if="selectedRsvpResponse.rsvp?.message" class="mt-2 whitespace-pre-line text-wedding-text">
                            {{ selectedRsvpResponse.rsvp.message }}
                        </p>
                        <p v-else class="mt-2 text-wedding-muted">No message provided.</p>
                    </article>

                    <article class="border border-soft bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Meal Choices</p>
                        <div v-if="rsvpMealChoiceRows(selectedRsvpResponse).length" class="mt-3 space-y-3">
                            <div v-for="(choice, choiceIndex) in rsvpMealChoiceRows(selectedRsvpResponse)" :key="`${choice.guest}-${choiceIndex}`" class="border border-soft bg-wedding-bg p-3">
                                <p class="font-medium">{{ choice.guest }}</p>
                                <p class="mt-1 text-sm text-wedding-muted">{{ choice.summary }}</p>
                                <ul v-if="choice.selections.length" class="mt-2 space-y-1 text-sm text-wedding-muted">
                                    <li v-for="selection in choice.selections" :key="selection">{{ selection }}</li>
                                </ul>
                            </div>
                        </div>
                        <p v-else class="mt-2 text-wedding-muted">No meal choices recorded.</p>
                    </article>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <button class="admin-btn border border-soft bg-white px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="editRsvp(selectedRsvpResponse); closeRsvpResponseModal();">
                        Edit RSVP
                    </button>
                    <button class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="closeRsvpResponseModal">
                        <span class="material-symbols-outlined btn-icon">close</span>
                        Close
                    </button>
                </div>
            </div>
        </div>

        <div v-if="editingRsvp" class="fixed inset-0 z-[70] overflow-y-auto bg-black/40 p-4 md:p-8" @click.self="closeRsvpModal">
            <div class="mx-auto my-4 w-full max-w-3xl border border-soft bg-white p-6 shadow-soft md:my-16 md:p-8">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="font-heading text-3xl">{{ editingRsvp.rsvp?.status ? 'Update RSVP' : 'Manually RSVP' }} · {{ editingRsvp.party_name }}</h3>
                    <button class="modal-close-x" type="button" aria-label="Close manual RSVP modal" title="Close" @click="closeRsvpModal">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-wedding-muted">
                    <span>Email RSVP request sent:</span>
                    <template v-if="editingRsvp.rsvp_email_sent">
                        <span class="text-emerald-700">Yes, email sent at {{ formatDateTime(editingRsvp.rsvp_email_sent_at) }}</span>
                        <button
                            type="button"
                            class="border-0 bg-transparent p-0 text-sm font-medium text-wedding-band underline decoration-wedding-band/50 underline-offset-4 transition hover:text-wedding-primarygreen hover:decoration-wedding-primarygreen"
                            title="View RSVP email history"
                            @click="openPartyEmailHistory(editingRsvp.party_id, editingRsvp.party_name)"
                        >
                            View RSVP History
                        </button>
                    </template>
                    <button
                        v-else-if="editingRsvp.email"
                        type="button"
                        class="border-0 bg-transparent p-0 text-sm font-medium text-wedding-band underline decoration-wedding-band/50 underline-offset-4 transition hover:text-wedding-primarygreen hover:decoration-wedding-primarygreen"
                        :class="{ 'cursor-not-allowed opacity-60': !canSendRsvpEmails }"
                        :disabled="!canSendRsvpEmails"
                        :title="rsvpEmailSendTitle"
                        @click="openSendRsvpConfirmModal([editingRsvp.party_id])"
                    >
                        Send email now
                    </button>
                    <span v-else class="text-wedding-muted">No email address has been entered for this guest</span>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    <select v-model="rsvpForm.status" class="border border-soft px-4 py-3">
                        <option value="attending">Attending</option>
                        <option value="not_attending">Not Attending</option>
                    </select>
                    <input v-model.number="rsvpForm.attending_count" type="number" min="0" :max="editingRsvp.max_guests" class="border border-soft px-4 py-3">
                </div>

                <label class="mt-4 block text-sm uppercase tracking-[0.12em] text-wedding-muted">Dietary Restrictions
                    <textarea v-model="rsvpForm.dietary_restrictions" rows="2" class="mt-2 w-full border border-soft px-3 py-2"></textarea>
                </label>

                <label class="mt-4 block text-sm uppercase tracking-[0.12em] text-wedding-muted">Message
                    <textarea v-model="rsvpForm.message" rows="2" class="mt-2 w-full border border-soft px-3 py-2"></textarea>
                </label>

                <div class="mt-5 flex flex-wrap justify-end gap-3">
                    <button class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="closeRsvpModal">
                        <span class="material-symbols-outlined btn-icon">close</span>
                        Close
                    </button>
                    <button class="admin-btn inline-flex items-center gap-2" type="button" @click="saveAdminRsvp">
                        <span class="material-symbols-outlined btn-icon">save</span>
                        Save RSVP
                    </button>
                </div>

                <p v-if="globalMessage" class="mt-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ globalMessage }}
                </p>
                <p v-if="globalError" class="mt-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ globalError }}
                </p>
            </div>
        </div>

        <div v-if="qrModalOpen" class="fixed inset-0 z-[88] overflow-y-auto bg-black/50 p-4 md:p-8" @click.self="closeQrModal">
            <div class="mx-auto my-4 w-full max-w-xl border border-soft bg-wedding-bg p-6 shadow-soft md:my-16">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="font-heading text-3xl">Share QR Code</h3>
                    <button type="button" class="modal-close-x" aria-label="Close QR code modal" title="Close" @click="closeQrModal">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <p class="mt-2 text-sm text-wedding-muted">Download, share digitally, or print your invitation QR code.</p>

                <div class="mt-5 flex justify-center border border-soft bg-white p-6">
                    <img :src="qrImageUrl" alt="Public URL QR code" class="h-72 w-72 border border-soft object-contain">
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <button
                        type="button"
                        class="admin-tool-btn inline-flex items-center justify-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]"
                        @click="downloadQrImage('png')"
                    >
                        <span class="material-symbols-outlined btn-icon">download</span>
                        Download PNG
                    </button>
                    <button type="button" class="admin-tool-btn inline-flex items-center justify-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" @click="downloadQrImage('jpg')">
                        <span class="material-symbols-outlined btn-icon">image</span>
                        Download JPG
                    </button>
                    <button type="button" class="admin-tool-btn inline-flex items-center justify-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" @click="shareQrImage">
                        <span class="material-symbols-outlined btn-icon">share</span>
                        Share Image
                    </button>
                    <button type="button" class="admin-tool-btn inline-flex items-center justify-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" @click="printQrImage">
                        <span class="material-symbols-outlined btn-icon">print</span>
                        Print
                    </button>
                </div>
            </div>
        </div>

        <div v-if="imageLibraryModalOpen" class="fixed inset-0 z-[89] overflow-y-auto bg-black/50 p-4 md:p-8" @click.self="closeImageLibrary">
            <div class="mx-auto my-4 w-full max-w-4xl border border-soft bg-white p-6 shadow-soft md:my-16">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-heading text-3xl">Select from your library</h3>
                        <p class="mt-2 text-sm text-wedding-muted">Choose from images already uploaded to this wedding website.</p>
                    </div>
                    <button type="button" class="modal-close-x" aria-label="Close image library" title="Close" @click="closeImageLibrary">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div v-if="imageLibrary.length" class="mt-6 grid max-h-[60vh] gap-4 overflow-y-auto sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <button
                        v-for="imagePath in imageLibrary"
                        :key="imagePath"
                        type="button"
                        class="group aspect-square overflow-hidden border border-soft bg-wedding-bg p-2 transition hover:border-wedding-band"
                        @click="selectGalleryImageFromLibrary(imagePath)"
                    >
                        <img :src="imagePath" alt="Uploaded library item" class="h-full w-full object-cover transition group-hover:scale-[1.03]">
                    </button>
                </div>
                <p v-else class="mt-6 border border-soft bg-wedding-bg px-4 py-3 text-sm text-wedding-muted">
                    No uploaded images are available yet. Upload an image first, then it will appear here for reuse.
                </p>
            </div>
        </div>

        <div v-if="confirmModal.open" class="fixed inset-0 z-[90] overflow-y-auto bg-black/40 p-4" @click.self="closeConfirmModal(false)">
            <div class="mx-auto my-4 w-full max-w-lg border border-soft bg-white p-6 shadow-soft md:my-20">
                <h3 class="font-heading text-3xl">{{ confirmModal.title }}</h3>
                <p class="mt-3 text-wedding-muted">{{ confirmModal.message }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="admin-btn inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]" @click="closeConfirmModal(false)">
                        <span class="material-symbols-outlined btn-icon">close</span>
                        Cancel
                    </button>
                    <button type="button" class="admin-btn inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]" :class="confirmModal.confirmClass" @click="closeConfirmModal(true)">
                        <span class="material-symbols-outlined btn-icon">{{ confirmModal.confirmIcon }}</span>
                        {{ confirmModal.confirmLabel }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="noticeModal.open" class="fixed inset-0 z-[85] overflow-y-auto bg-black/40 p-4" @click.self="closeNoticeModal">
            <div class="mx-auto my-4 w-full max-w-lg border border-soft bg-white p-6 shadow-soft md:my-24">
                <h3 class="font-heading text-3xl">{{ noticeModal.title }}</h3>
                <p class="mt-3 text-wedding-muted">{{ noticeModal.message }}</p>
                <p v-if="noticeModal.note" class="mt-2 text-sm italic text-wedding-muted">{{ noticeModal.note }}</p>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="admin-btn inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]" @click="closeNoticeModal">
                        <span class="material-symbols-outlined btn-icon">check</span>
                        OK
                    </button>
                </div>
            </div>
        </div>

        <div v-if="demoChoiceModalOpen" class="fixed inset-0 z-[86] overflow-y-auto bg-black/40 p-4" @click.self="closeDemoChoiceModal">
            <div class="mx-auto my-4 w-full max-w-2xl border border-soft bg-white p-6 shadow-soft md:my-24 md:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-wedding-muted">Demo preview</p>
                        <h3 class="mt-2 font-heading text-3xl">Choose a demo layout</h3>
                        <p class="mt-3 text-wedding-muted">Preview the same completed demo content in either available website style.</p>
                    </div>
                    <button type="button" class="modal-close-x" aria-label="Close demo layout chooser" title="Close" @click="closeDemoChoiceModal">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <button
                        type="button"
                        class="border border-soft bg-[#F7F7F7] p-5 text-left transition hover:border-wedding-band hover:bg-white"
                        @click="openDemoPreview('classic')"
                    >
                        <span class="material-symbols-outlined text-wedding-band">auto_stories</span>
                        <span class="mt-3 block font-heading text-2xl">Classic demo</span>
                        <span class="mt-2 block text-sm leading-relaxed text-wedding-muted">Elegant, timeless, image-led wedding website layout.</span>
                    </button>
                    <button
                        type="button"
                        class="border border-soft bg-[#F7F7F7] p-5 text-left transition hover:border-wedding-band hover:bg-white"
                        @click="openDemoPreview('modern')"
                    >
                        <span class="material-symbols-outlined text-wedding-band">interests</span>
                        <span class="mt-3 block font-heading text-2xl">Modern demo</span>
                        <span class="mt-2 block text-sm leading-relaxed text-wedding-muted">Rounded imagery, softer shapes, and a more contemporary editorial feel.</span>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="section === 'content'" class="fixed bottom-0 left-0 right-0 z-50">
            <div class="w-full border-t border-soft bg-white/95 px-4 py-3 shadow-soft backdrop-blur md:px-8">
                <div class="flex flex-col items-center justify-center gap-3 text-center md:flex-row md:justify-between md:text-left">
                    <div class="flex flex-col items-center gap-2 md:flex-row md:items-center md:gap-4">
                        <p class="text-sm">
                            <span class="uppercase tracking-[0.12em] text-wedding-muted">Current Changes:</span>
                            <span class="ml-2 font-medium" :class="hasUnsavedChanges ? 'text-red-700' : 'text-emerald-700'">
                                {{ hasUnsavedChanges ? 'Unsaved' : 'Saved' }}
                            </span>
                        </p>
                    </div>
                    <div class="flex w-full flex-col gap-2 md:w-auto md:flex-row md:items-center md:justify-end md:gap-3">
                        <div class="flex flex-wrap items-center justify-center gap-2 md:justify-end">
                            <p v-if="globalMessage" class="rounded border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                                {{ globalMessage }}
                            </p>
                            <p v-if="globalError" class="rounded border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
                                {{ globalError }}
                            </p>
                        </div>
                        <div class="grid w-full grid-cols-2 gap-2 md:flex md:w-auto md:justify-end md:gap-3">
                            <button class="admin-btn admin-btn-success inline-flex items-center justify-center gap-1 px-4 py-3 text-[11px] uppercase tracking-[0.14em] md:gap-2 md:px-8 md:py-4 md:text-xs md:tracking-[0.2em]" type="button" @click="saveContent(false)">
                                <span class="material-symbols-outlined btn-icon">save</span>
                                Save
                            </button>
                            <button
                                v-if="previewUrl"
                                class="admin-btn inline-flex items-center justify-center gap-1 px-3 py-3 text-[11px] uppercase tracking-[0.14em] md:gap-2 md:px-8 md:py-4 md:text-xs md:tracking-[0.2em]"
                                type="button"
                                @click="saveContent(true)"
                            >
                                <span class="material-symbols-outlined btn-icon">visibility</span>
                                Save and Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="mobileNavOpen" class="fixed inset-0 z-[100] bg-black/50 p-0 xl:hidden" @click.self="mobileNavOpen = false">
            <div class="h-full w-full bg-white p-6">
                <div class="flex items-center justify-between border-b border-soft pb-4">
                    <h2 class="font-heading text-3xl">Menu</h2>
                    <button
                        type="button"
                        class="modal-close-x"
                        aria-label="Close menu"
                        title="Close"
                        @click="mobileNavOpen = false"
                    >
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <nav class="mt-5 grid gap-2">
                    <a
                        v-for="item in navItems"
                        :key="`mobile-${item.key}`"
                        :href="item.href"
                        class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.15em]"
                        :class="section === item.key ? 'admin-btn-active' : ''"
                    >
                        <span class="material-symbols-outlined btn-icon">{{ item.icon }}</span>
                        {{ item.label }}
                    </a>
                </nav>

                <div class="mt-5 border-t border-soft pt-5">
                    <a
                        v-if="accountUrl"
                        :href="accountUrl"
                        class="admin-btn inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.14em]"
                    >
                        <span class="material-symbols-outlined btn-icon">person</span>
                        My Account
                    </a>
                    <form :action="logoutUrl" method="POST" class="mt-2">
                        <input type="hidden" name="_token" :value="csrfToken">
                        <button class="admin-btn admin-btn-danger-solid inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.16em]" type="submit">
                            <span class="material-symbols-outlined btn-icon">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import RichTextEditor from '../components/admin/RichTextEditor.vue';

const props = defineProps({
    payload: {
        type: Object,
        default: () => ({}),
    },
});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const section = ref(props.payload?.adminSection || 'dashboard');
const logoutUrl = props.payload?.logoutUrl || '/logout';
const accountUrl = props.payload?.accountUrl || '';
const billingUrl = props.payload?.billingUrl || accountUrl || '';
const currentUserEmail = props.payload?.currentUserEmail || '';
const adminBaseUrl = props.payload?.adminBaseUrl || '/admin';
const apiBaseUrl = props.payload?.adminApiBaseUrl || '/admin/api';
const previewUrl = props.payload?.previewUrl || '';
const siteSettingsUpdateUrl = props.payload?.siteSettingsUpdateUrl || '';
const sitePublishUrl = props.payload?.sitePublishUrl || '';
const fieldHelpTexts = (props.payload?.fieldHelpTexts && typeof props.payload.fieldHelpTexts === 'object')
    ? props.payload.fieldHelpTexts
    : {};
const siteTitle = ref(props.payload?.siteTitle || '');
const hasPaidAccess = ref(Boolean(props.payload?.hasPaidAccess ?? true));
const sitePublished = ref(Boolean(props.payload?.sitePublished ?? false));
const globalMessage = ref('');
const globalError = ref('');
const createPartyMessage = ref('');
const createPartyError = ref('');
const lastSavedAt = ref('');
const lastSavedContentSnapshot = ref('');
const lastSavedRsvpSnapshot = ref('');
const lastSavedSiteTitle = ref(siteTitle.value);
const timelineMinItems = 3;
const timelineMaxItems = 8;
const confirmResolve = ref(null);
const confirmModal = reactive({
    open: false,
    title: '',
    message: '',
    confirmClass: 'admin-btn-danger',
    confirmIcon: 'close',
    confirmLabel: 'Confirm',
});
const noticeModal = reactive({
    open: false,
    title: '',
    message: '',
    note: '',
    scrollTarget: '',
});
const demoChoiceModalOpen = ref(false);
const qrModalOpen = ref(false);
const copyLinkCopied = ref(false);
const copiedInvitationPartyId = ref(null);
const actionsHelpOpen = ref(false);
let copyLinkResetTimer = null;
let copiedInvitationResetTimer = null;
const mobileNavOpen = ref(false);
const imageLibrary = ref([]);
const imageLibraryModalOpen = ref(false);
const selectedGalleryLibraryIndex = ref(null);
const themeOptions = [
    {
        layout: 'classic',
        name: 'Classic',
        description: 'Large image hero, centred headings, and the current polished wedding layout.',
    },
    {
        layout: 'modern',
        name: 'Modern',
        description: 'A refined, modern layout with soft mauve tones, rounded imagery, clean image bands, and calmer spacing.',
    },
];
const fallbackColourPalettes = [
    {
        slug: 'magic_classic',
        name: 'Magic Classic',
        mood: 'Elegant, premium, timeless',
        colours: {
            primary: '#22363A',
            secondary: '#466369',
            dark: '#0F1B1D',
            soft_background: '#F7F5F2',
            light: '#FFFFFF',
        },
    },
];
const colourPalettes = ref(fallbackColourPalettes);

const stats = ref({
    total_households: 0,
    invited_guests: 0,
    attending: 0,
    not_attending: 0,
    no_response: 0,
});

const content = ref(null);
const defaultMenuCourses = [
    { id: 'starter', name: 'Starter', items: [{ title: '', description: '' }] },
    { id: 'main', name: 'Main', items: [{ title: '', description: '' }] },
    { id: 'dessert', name: 'Dessert', items: [{ title: '', description: '' }] },
];

const rsvpSettings = ref({
    meal_mode: 'set_menu',
    menu_heading: 'Wedding Menu',
    menu_intro: 'We cannot wait to share a beautiful meal with you.',
    set_menu_description: 'A chef-curated set menu will be served for all attending guests.',
    menu_note_title: 'Dining Notes',
    menu_note_text: '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
    meal_options: [],
    kids_menu_enabled: false,
    kids_menu_items: [],
    menu_courses: defaultMenuCourses.map((course) => ({
        ...course,
        items: course.items.map((item) => ({ ...item })),
    })),
});
const parties = ref([]);
const partySearchTerm = ref('');
const selectedPartyId = ref(null);
const editPartyModalOpen = ref(false);
const viewPartyModalOpen = ref(false);
const selectedPartyIdsForEmail = ref([]);
const sendRsvpConfirmModal = reactive({
    open: false,
});
const sendRsvpReminderConfirmModal = reactive({
    open: false,
});
const emailHistoryModal = reactive({
    open: false,
    partyId: null,
    partyName: '',
    history: [],
    loading: false,
    error: '',
});
const rsvpEmailPreviewGuestType = ref('day');
const testRsvpEmailAddress = ref(currentUserEmail);
const rsvpEmailSettingsMessage = ref('');
const rsvpEmailSettingsError = ref('');
const rsvpEmailPreviewModal = reactive({
    open: false,
    loading: false,
    html: '',
    error: '',
});
const rsvpRows = ref([]);
const rsvpStatusFilter = ref('all');
const rsvpGuestTypeFilter = ref('all');
const rsvpSearchTerm = ref('');
const editingRsvp = ref(null);
const selectedRsvpResponse = ref(null);

const rsvpForm = reactive({
    status: 'attending',
    attending_count: 0,
    meal_choices: [],
    dietary_restrictions: '',
    message: '',
});

const newParty = reactive({
    display_name: '',
    guest_type: 'day',
    email: '',
    code: '',
    max_guests: 1,
    notes: '',
});
const newPartyGuests = ref([createEmptyGuestRow()]);

const newGuest = reactive({
    first_name: '',
    last_name: '',
    is_child: false,
    allow_plus_one: false,
});

const selectedParty = computed(() => parties.value.find((party) => party.id === selectedPartyId.value) || null);
const selectedEmailableParties = computed(() =>
    parties.value.filter((party) => selectedPartyIdsForEmail.value.includes(party.id) && Boolean((party.email || '').trim()))
);
const reminderEligibleParties = computed(() =>
    parties.value.filter((party) => Boolean((party.email || '').trim()) && !party.rsvp?.status)
);
const createPartyInvitedSeats = computed(() => {
    const guests = newPartyGuests.value || [];
    return guests.length;
});
const filteredParties = computed(() => {
    const term = partySearchTerm.value.trim().toLowerCase();
    if (!term) {
        return parties.value;
    }

    return parties.value.filter((party) => {
        const partyName = (party.display_name || '').toLowerCase();
        const partyCode = (party.code || '').toLowerCase();
        const guestNames = (party.guests || [])
            .map((guest) => `${guest.first_name || ''} ${guest.last_name || ''}`.trim().toLowerCase())
            .join(' ');

        return partyName.includes(term) || partyCode.includes(term) || guestNames.includes(term);
    });
});
const areAllFilteredPartiesSelected = computed(() =>
    filteredParties.value.length > 0
    && filteredParties.value.every((party) => selectedPartyIdsForEmail.value.includes(party.id))
);
const hasUnsavedChanges = computed(() => {
    if (!content.value) {
        return false;
    }

    return serialize(content.value) !== lastSavedContentSnapshot.value
        || serialize(rsvpSettings.value) !== lastSavedRsvpSnapshot.value
        || siteTitle.value !== lastSavedSiteTitle.value;
});
const selectedThemeLayout = computed(() => normalizeThemeLayout(content.value?.theme?.layout));
const colourPaletteOptions = computed(() => colourPalettes.value.length ? colourPalettes.value : fallbackColourPalettes);
const selectedColourPaletteSlug = computed(() => normalizeColourPalette(content.value?.theme?.palette));
const isTimelineAtMax = computed(() => (content.value?.timeline?.items?.length || 0) >= timelineMaxItems);
const isTimelineAtMin = computed(() => (content.value?.timeline?.items?.length || 0) <= timelineMinItems);
const galleryImageCount = computed(() => {
    const items = content.value?.gallery?.items;
    return Array.isArray(items) ? items.filter((item) => item?.image).length : 0;
});
const isGalleryAtMax = computed(() => (content.value?.gallery?.items?.length || 0) >= 8);
const galleryValidationMessage = computed(() => {
    if (!isSectionVisible('gallery')) {
        return '';
    }

    if (galleryImageCount.value === 1) {
        return 'Please upload at least 2 images, or hide this section.';
    }

    return '';
});
const canAddMenuOptions = computed(() => rsvpSettings.value?.meal_mode === 'options');
const qrImageUrl = computed(() =>
    `https://api.qrserver.com/v1/create-qr-code/?size=1200x1200&format=png&data=${encodeURIComponent(previewUrl)}`
);
const qrDownloadName = computed(() => {
    try {
        const pathname = new URL(previewUrl).pathname || '';
        const maybeSlug = pathname.split('/').filter(Boolean).pop() || 'wedding';
        return maybeSlug.replace(/[^a-z0-9-_]/gi, '-').toLowerCase() || 'wedding';
    } catch {
        return 'wedding';
    }
});
const filteredRsvpRows = computed(() =>
    rsvpRows.value.filter((row) => {
        const searchTerm = rsvpSearchTerm.value.trim().toLowerCase();
        if (searchTerm) {
            const partyName = (row.party_name || '').toLowerCase();
            const code = (row.code || '').toLowerCase();
            if (!partyName.includes(searchTerm) && !code.includes(searchTerm)) {
                return false;
            }
        }

        if (rsvpStatusFilter.value !== 'all') {
            if (rsvpStatusFilter.value === 'no_response') {
                return !row.rsvp?.status;
            }
            if (!row.rsvp?.status) {
                return false;
            }
            if (row.rsvp.status !== rsvpStatusFilter.value) {
                return false;
            }
        }

        if (rsvpGuestTypeFilter.value !== 'all' && (row.guest_type || 'day') !== rsvpGuestTypeFilter.value) {
            return false;
        }

        return true;
    }),
);

const dashboardCards = computed(() => [
    { label: 'Total Parties', value: stats.value.total_households },
    { label: 'Invited Guests', value: stats.value.invited_guests },
    { label: 'Attending', value: stats.value.attending },
    { label: 'Not Attending', value: stats.value.not_attending },
    { label: 'No Response', value: stats.value.no_response },
]);
const setupChecklistItems = computed(() => {
    const websiteComplete = hasSavedWebsiteContent();
    const guestListComplete = parties.value.length > 0 && parties.value.some((party) => (party.guests || []).length > 0);
    const emailReady = hasRsvpDeadline() && parties.value.some((party) => Boolean((party.email || '').trim()));
    const subscriptionReady = hasPaidAccess.value;
    const published = sitePublished.value;
    const rsvpChecked = rsvpRows.value.some((row) => Boolean(row.rsvp?.status));

    const items = [
        {
            key: 'website',
            title: 'Create your website',
            description: 'Add names, date, venue, and website details.',
            statusText: websiteComplete ? 'Website content has been saved.' : 'Add wedding details and save.',
            href: `${adminBaseUrl}/content`,
            icon: 'draw',
            ctaIcon: 'draw',
            ctaLabel: websiteComplete ? 'Review site' : 'Create site',
            completed: websiteComplete,
        },
        {
            key: 'guest-list',
            title: 'Build your guest list',
            description: 'Create parties, named guests, and seats.',
            statusText: guestListComplete ? `${parties.value.length} ${parties.value.length === 1 ? 'party' : 'parties'} added.` : 'Add one party with a named guest.',
            href: `${adminBaseUrl}/parties#create-party-section`,
            icon: 'groups',
            ctaIcon: 'groups',
            ctaLabel: guestListComplete ? 'Guest list' : 'Add guests',
            completed: guestListComplete,
        },
        {
            key: 'emails',
            title: 'Prepare RSVP emails',
            description: 'Set the RSVP deadline and guest emails.',
            statusText: emailReady ? 'RSVP email settings are ready.' : 'Set a deadline and guest email.',
            href: `${adminBaseUrl}/parties`,
            icon: 'forward_to_inbox',
            ctaIcon: 'mail',
            ctaLabel: 'Email setup',
            completed: emailReady,
        },
        {
            key: 'subscription',
            title: 'Subscribe to go live',
            description: 'Subscribe before publishing or sending emails.',
            statusText: subscriptionReady ? 'Account can publish and send emails.' : 'Subscribe to publish and send emails.',
            href: billingUrl || adminBaseUrl,
            icon: 'workspace_premium',
            ctaIcon: subscriptionReady ? 'verified' : 'credit_card',
            ctaLabel: 'View plan',
            completed: subscriptionReady,
        },
        {
            key: 'publish',
            title: 'Publish your site',
            description: 'Make the website live when ready.',
            statusText: published ? 'Website is published.' : 'Website is still in draft.',
            href: '#site-information-section',
            scrollTarget: 'site-information-section',
            icon: 'publish',
            ctaIcon: published ? 'visibility' : 'publish',
            ctaLabel: published ? 'Site tools' : 'Publish site',
            completed: published,
        },
        {
            key: 'rsvp-check',
            title: 'Check RSVP flow',
            description: 'Confirm the guest RSVP journey works.',
            statusText: rsvpChecked ? 'An RSVP response is recorded.' : 'Submit a test RSVP or add one.',
            href: `${adminBaseUrl}/rsvps`,
            icon: 'fact_check',
            ctaIcon: 'event_note',
            ctaLabel: rsvpChecked ? 'Responses' : 'Check RSVP',
            completed: rsvpChecked,
        },
    ];

    const nextIncompleteIndex = items.findIndex((item) => !item.completed);

    return items.map((item, index) => ({
        ...item,
        isNext: index === nextIncompleteIndex,
    }));
});
const completedSetupCount = computed(() => setupChecklistItems.value.filter((item) => item.completed).length);
const setupCompletionPercentage = computed(() => {
    if (setupChecklistItems.value.length === 0) {
        return 0;
    }

    return Math.round((completedSetupCount.value / setupChecklistItems.value.length) * 100);
});
const canSendRsvpEmails = computed(() => hasPaidAccess.value && sitePublished.value);
const rsvpEmailSendTitle = computed(() => {
    if (!hasPaidAccess.value) {
        return 'An active subscription is required before sending RSVP emails.';
    }

    if (!sitePublished.value) {
        return 'Publish your website before sending RSVP emails.';
    }

    return 'Email RSVP';
});
const rsvpReminderSendTitle = computed(() => {
    if (!canSendRsvpEmails.value) {
        return rsvpEmailSendTitle.value;
    }

    if (!hasRsvpDeadline()) {
        return 'Set an RSVP response deadline before sending reminders.';
    }

    if (reminderEligibleParties.value.length === 0) {
        return 'No parties with email addresses are waiting for a response.';
    }

    return 'Send RSVP reminders';
});
const rsvpReminderBlockedReason = computed(() =>
    !sitePublished.value || rsvpReminderSendTitle.value === 'Send RSVP reminders' ? '' : rsvpReminderSendTitle.value
);
const rsvpStatusLegend = [
    {
        icon: 'check',
        color: '#21C177',
        label: 'Attending',
        title: 'Guest confirmed attending',
    },
    {
        icon: 'close',
        color: '#E66363',
        label: 'Not attending',
        title: 'Guest confirmed not attending',
    },
    {
        icon: 'horizontal_rule',
        color: '#D79A2B',
        label: 'No response',
        title: 'Guest has not yet responded',
    },
];
const guestTypeLegend = [
    {
        icon: 'sunny',
        color: '#D79A2B',
        label: 'All Day Guest',
        title: 'All Day Guest invitation',
    },
    {
        icon: 'dark_mode',
        color: '#22363A',
        label: 'Evening Guest',
        title: 'Evening guest invitation',
    },
];

const navItems = [
    { key: 'dashboard', label: 'Dashboard', href: adminBaseUrl, icon: 'dashboard' },
    { key: 'content', label: 'Create your website', href: `${adminBaseUrl}/content`, icon: 'edit_note' },
    { key: 'parties', label: 'Guest List', href: `${adminBaseUrl}/parties`, icon: 'groups' },
    { key: 'rsvps', label: 'RSVP Responses', href: `${adminBaseUrl}/rsvps`, icon: 'event_note' },
];
const partiesExportUrl = `${apiBaseUrl}/parties/export`;
const rsvpsExportUrl = `${apiBaseUrl}/rsvps/export`;

onMounted(async () => {
    await Promise.all([loadStats(), loadParties(), loadRsvps(), loadContent()]);

    if (!newParty.code) {
        await generateCodeForCreate(false);
    }

    if (parties.value.length > 0) {
        selectedPartyId.value = parties.value[0].id;
    }

    await nextTick();
    applyFieldHelpAttributes();
    scrollToHashTarget();
});

onBeforeUnmount(() => {
    if (copyLinkResetTimer) {
        clearTimeout(copyLinkResetTimer);
    }
    if (copiedInvitationResetTimer) {
        clearTimeout(copiedInvitationResetTimer);
    }
});

watch(section, async () => {
    await nextTick();
    applyFieldHelpAttributes();
});

watch(
    () => rsvpSettings.value?.meal_mode,
    (nextMode, previousMode) => {
        if (!nextMode || !previousMode || nextMode === previousMode) {
            return;
        }

        if (nextMode === 'set_menu' && hasMultipleOptionsPerCourse()) {
            rsvpSettings.value.meal_mode = 'options';
            openNoticeModal(
                'Cannot switch to set menu yet',
                'Set menu allows only one menu option per course. Please delete additional menu items first.'
            );
        }
    }
);

function hasSavedWebsiteContent() {
    if (!content.value || !lastSavedAt.value) {
        return false;
    }

    return [
        siteTitle.value,
        content.value?.hero?.names,
        content.value?.hero?.dateLine,
        content.value?.hero?.locationLine,
        content.value?.details?.venue?.name,
    ].every((value) => String(value || '').trim().length > 0);
}

function hasRsvpDeadline() {
    return String(content.value?.guest_list?.responseDeadline || '').trim().length > 0;
}

async function loadStats() {
    try {
        const response = await window.axios.get(`${apiBaseUrl}/dashboard`);
        stats.value = response.data;
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load dashboard stats.'));
    }
}

async function loadContent() {
    try {
        const response = await window.axios.get(`${apiBaseUrl}/content`);
        colourPalettes.value = normalizeColourPalettes(response.data.colour_palettes);
        content.value = response.data.content;
        ensureImageFocusDefaults();
        ensureSectionVisibilityDefaults();
        imageLibrary.value = Array.isArray(response.data.image_library) ? response.data.image_library : [];
        lastSavedAt.value = formatDateTime(response.data.last_saved_at);
        const legacyMealChoicesEnabled = response.data.rsvp_settings?.meal_choices_enabled;
        const defaultMealMode = legacyMealChoicesEnabled === true ? 'options' : 'set_menu';
        rsvpSettings.value = {
            meal_mode: response.data.rsvp_settings?.meal_mode || defaultMealMode,
            menu_heading: response.data.rsvp_settings?.menu_heading || 'Wedding Menu',
            menu_intro: response.data.rsvp_settings?.menu_intro || 'We cannot wait to share a beautiful meal with you.',
            set_menu_description: response.data.rsvp_settings?.set_menu_description || 'A chef-curated set menu will be served for all attending guests.',
            menu_note_title: response.data.rsvp_settings?.menu_note_title || 'Dining Notes',
            menu_note_text: response.data.rsvp_settings?.menu_note_text || '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
            meal_options: response.data.rsvp_settings?.meal_options?.length
                ? response.data.rsvp_settings.meal_options
                : [],
            kids_menu_enabled: Boolean(response.data.rsvp_settings?.kids_menu_enabled),
            kids_menu_items: normalizeCourseItems(response.data.rsvp_settings?.kids_menu_items),
            menu_courses: normalizeMenuCourses(response.data.rsvp_settings?.menu_courses),
        };
        captureSavedSnapshots();
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load content.'));
    }
}

async function loadParties() {
    try {
        const response = await window.axios.get(`${apiBaseUrl}/parties`);
        parties.value = (response.data.parties || []).map((party) => ({
            ...party,
            guest_type: party.guest_type || 'day',
            guest_type_label: party.guest_type_label || (party.guest_type === 'evening' ? 'Evening Guest' : 'All Day Guest'),
            guests: (party.guests || []).map((guest) => ({
                ...guest,
                allow_plus_one: Boolean(guest.allow_plus_one),
            })),
        }));
        selectedPartyIdsForEmail.value = selectedPartyIdsForEmail.value.filter((id) =>
            parties.value.some((party) => party.id === id)
        );

        if (selectedPartyId.value && !parties.value.some((party) => party.id === selectedPartyId.value)) {
            selectedPartyId.value = parties.value.length > 0 ? parties.value[0].id : null;
        }
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load parties.'));
    }
}

async function loadRsvps() {
    try {
        const response = await window.axios.get(`${apiBaseUrl}/rsvps`);
        rsvpRows.value = response.data.rsvps;
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load RSVP responses.'));
    }
}

function selectParty(partyId) {
    selectedPartyId.value = partyId;
}

function createEmptyGuestRow() {
    return {
        first_name: '',
        last_name: '',
        is_child: false,
        allow_plus_one: false,
    };
}

function addNewPartyGuestRow() {
    if (newPartyGuests.value.length >= 20) {
        setError('A party can include up to 20 named guests.');
        return;
    }

    newPartyGuests.value.push(createEmptyGuestRow());
}

function addAnonymousPlusOneRow() {
    if (newPartyGuests.value.length >= 20) {
        setError('A party can include up to 20 named guests.');
        return;
    }

    newPartyGuests.value.push({
        first_name: 'Anonymous',
        last_name: '+1',
        is_child: false,
        allow_plus_one: false,
    });
}

function removeNewPartyGuestRow(index) {
    if (newPartyGuests.value.length <= 1 || index === 0) {
        return;
    }
    newPartyGuests.value.splice(index, 1);
}

function openViewPartyModal(partyId) {
    selectedPartyId.value = partyId;
    viewPartyModalOpen.value = true;
    editPartyModalOpen.value = false;
    clearError();
}

function closeViewPartyModal() {
    viewPartyModalOpen.value = false;
}

function openEditPartyModal(partyId) {
    selectedPartyId.value = partyId;
    viewPartyModalOpen.value = false;
    editPartyModalOpen.value = true;
    clearError();
    nextTick(() => applyFieldHelpAttributes());
}

function closeEditPartyModal() {
    editPartyModalOpen.value = false;
}

function openEditFromViewModal() {
    if (!selectedPartyId.value) {
        return;
    }

    viewPartyModalOpen.value = false;
    openEditPartyModal(selectedPartyId.value);
}

function togglePartyEmailSelection(partyItem) {
    if (selectedPartyIdsForEmail.value.includes(partyItem.id)) {
        selectedPartyIdsForEmail.value = selectedPartyIdsForEmail.value.filter((id) => id !== partyItem.id);
        return;
    }

    selectedPartyIdsForEmail.value = [...selectedPartyIdsForEmail.value, partyItem.id];
}

function toggleSelectAllFilteredParties() {
    const visibleIds = filteredParties.value.map((party) => party.id);
    if (visibleIds.length === 0) {
        return;
    }

    if (areAllFilteredPartiesSelected.value) {
        selectedPartyIdsForEmail.value = selectedPartyIdsForEmail.value.filter((id) => !visibleIds.includes(id));
        return;
    }

    const merged = new Set([...selectedPartyIdsForEmail.value, ...visibleIds]);
    selectedPartyIdsForEmail.value = Array.from(merged);
}

async function openPartyEmailHistory(partyId, partyName = '') {
    emailHistoryModal.open = true;
    emailHistoryModal.partyId = partyId;
    emailHistoryModal.partyName = partyName || 'Party';
    emailHistoryModal.history = [];
    emailHistoryModal.error = '';
    emailHistoryModal.loading = true;

    try {
        const response = await window.axios.get(`${apiBaseUrl}/parties/${partyId}/email-history`);
        emailHistoryModal.partyName = response.data?.party_name || emailHistoryModal.partyName;
        emailHistoryModal.history = response.data?.history || [];
    } catch (error) {
        emailHistoryModal.error = extractErrorMessage(error, 'Could not load RSVP email history.');
    } finally {
        emailHistoryModal.loading = false;
    }
}

function closePartyEmailHistory() {
    emailHistoryModal.open = false;
    emailHistoryModal.partyId = null;
    emailHistoryModal.partyName = '';
    emailHistoryModal.history = [];
    emailHistoryModal.loading = false;
    emailHistoryModal.error = '';
}

function openRsvpResponseModal(row) {
    selectedRsvpResponse.value = row;
    clearError();
}

function closeRsvpResponseModal() {
    selectedRsvpResponse.value = null;
}

function openSendRsvpConfirmModal(partyIds = null) {
    clearError();
    if (!canSendRsvpEmails.value) {
        setError(rsvpEmailSendTitle.value);
        return;
    }

    if (Array.isArray(partyIds)) {
        selectedPartyIdsForEmail.value = partyIds;
    }

    if (selectedEmailableParties.value.length === 0) {
        setError('Select at least one party with an email address.');
        return;
    }

    sendRsvpConfirmModal.open = true;
}

function closeSendRsvpConfirmModal() {
    sendRsvpConfirmModal.open = false;
}

function openSendRsvpReminderConfirmModal() {
    clearError();
    clearRsvpEmailSettingsNotice();

    if (!canSendRsvpEmails.value) {
        rsvpEmailSettingsError.value = rsvpEmailSendTitle.value;
        return;
    }

    if (!hasRsvpDeadline()) {
        rsvpEmailSettingsError.value = 'Set an RSVP response deadline before sending reminders.';
        return;
    }

    if (reminderEligibleParties.value.length === 0) {
        rsvpEmailSettingsError.value = 'No parties with email addresses are waiting for a response.';
        return;
    }

    sendRsvpReminderConfirmModal.open = true;
}

function closeSendRsvpReminderConfirmModal() {
    sendRsvpReminderConfirmModal.open = false;
}

function scrollToYourGuests() {
    scrollToElementId('your-guests-section');
}

function handleSetupChecklistClick(event, item) {
    if (!item.scrollTarget) {
        return;
    }

    event.preventDefault();
    scrollToElementId(item.scrollTarget);
}

function handleSiteInformationLinkClick(event) {
    if (section.value !== 'dashboard') {
        return;
    }

    event.preventDefault();
    window.history.replaceState(null, '', '#site-information-section');
    scrollToElementId('site-information-section');
}

function scrollToHashTarget() {
    const hash = window.location.hash.replace('#', '');
    if (!hash) {
        return;
    }

    window.setTimeout(() => scrollToElementId(hash), 75);
}

function scrollToElementId(id) {
    document.getElementById(id)?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
}

function rsvpEmailPreviewPayload() {
    return {
        guest_type: rsvpEmailPreviewGuestType.value,
        response_deadline: content.value?.guest_list?.responseDeadline || null,
    };
}

function clearRsvpEmailSettingsNotice() {
    rsvpEmailSettingsMessage.value = '';
    rsvpEmailSettingsError.value = '';
}

async function openRsvpEmailPreview() {
    clearRsvpEmailSettingsNotice();
    rsvpEmailPreviewModal.open = true;
    rsvpEmailPreviewModal.loading = true;
    rsvpEmailPreviewModal.error = '';
    rsvpEmailPreviewModal.html = '';

    try {
        const response = await window.axios.post(`${apiBaseUrl}/parties/rsvp-email-preview`, rsvpEmailPreviewPayload());
        rsvpEmailPreviewModal.html = response.data?.html || '';
    } catch (error) {
        rsvpEmailPreviewModal.error = extractErrorMessage(error, 'Could not load RSVP email preview.');
    } finally {
        rsvpEmailPreviewModal.loading = false;
    }
}

function closeRsvpEmailPreview() {
    rsvpEmailPreviewModal.open = false;
}

async function sendTestRsvpEmail() {
    clearRsvpEmailSettingsNotice();

    try {
        const response = await window.axios.post(`${apiBaseUrl}/parties/send-test-rsvp-email`, {
            ...rsvpEmailPreviewPayload(),
            email: testRsvpEmailAddress.value,
        });
        rsvpEmailSettingsMessage.value = response.data?.message || 'Test RSVP email sent.';
    } catch (error) {
        rsvpEmailSettingsError.value = extractErrorMessage(error, 'Could not send test RSVP email.');
    }
}

async function copyToClipboard(text, successMessage = 'Link copied to clipboard.', fallbackLabel = 'Copy this link:') {
    try {
        await navigator.clipboard.writeText(text);
        setMessage(successMessage);
        return true;
    } catch (error) {
        window.prompt(fallbackLabel, text);
        return false;
    }
}

async function copyPublicUrl() {
    if (!previewUrl) {
        setError('Public URL is not available.');
        return;
    }
    const copied = await copyToClipboard(previewUrl);
    if (copied) {
        copyLinkCopied.value = true;
        if (copyLinkResetTimer) {
            clearTimeout(copyLinkResetTimer);
        }
        copyLinkResetTimer = setTimeout(() => {
            copyLinkCopied.value = false;
        }, 2500);
    }
}

async function sharePublicUrl() {
    if (!previewUrl) {
        setError('Public URL is not available.');
        return;
    }

    if (navigator.share) {
        try {
            await navigator.share({ title: 'Wedding Website', url: previewUrl });
            setMessage('Share sheet opened.');
            return;
        } catch {
            // fall back to copy
        }
    }

    await copyToClipboard(previewUrl);
}

function openQrModal() {
    if (!previewUrl) {
        setError('Public URL is not available.');
        return;
    }
    qrModalOpen.value = true;
}

function closeQrModal() {
    qrModalOpen.value = false;
}

async function downloadQrImage(format = 'png') {
    if (!previewUrl) {
        setError('Public URL is not available.');
        return;
    }

    try {
        const response = await fetch(qrImageUrl.value);
        const blob = await response.blob();

        if (format === 'jpg') {
            const jpgUrl = await convertImageBlobToJpg(blob);
            triggerQrDownload(jpgUrl, `${qrDownloadName.value}-qr.jpg`);
            URL.revokeObjectURL(jpgUrl);
            return;
        }

        const transparentPngUrl = await convertImageBlobToTransparentPng(blob);
        triggerQrDownload(transparentPngUrl, `${qrDownloadName.value}-qr.png`);
        URL.revokeObjectURL(transparentPngUrl);
    } catch {
        setError(`Could not download the QR code as ${format.toUpperCase()}.`);
    }
}

async function shareQrImage() {
    if (!previewUrl) {
        setError('Public URL is not available.');
        return;
    }

    if (navigator.share && navigator.canShare) {
        try {
            const response = await fetch(qrImageUrl.value);
            const blob = await response.blob();
            const file = new File([blob], `${qrDownloadName.value}-qr.png`, { type: 'image/png' });
            if (navigator.canShare({ files: [file] })) {
                await navigator.share({ title: 'Wedding QR Code', text: previewUrl, files: [file] });
                setMessage('QR code shared.');
                return;
            }
        } catch {
            // fall through
        }
    }

    await copyToClipboard(previewUrl);
}

function triggerQrDownload(fileUrl, fileName) {
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function convertImageBlobToTransparentPng(blob) {
    return convertQrImageBlob(blob, ({ context, canvas }) => {
        context.drawImage(canvas.sourceImage, 0, 0);

        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const pixels = imageData.data;

        for (let index = 0; index < pixels.length; index += 4) {
            const red = pixels[index];
            const green = pixels[index + 1];
            const blue = pixels[index + 2];

            if (red > 245 && green > 245 && blue > 245) {
                pixels[index + 3] = 0;
            }
        }

        context.putImageData(imageData, 0, 0);

        return new Promise((resolve, reject) => {
            canvas.toBlob((pngBlob) => {
                if (!pngBlob) {
                    reject(new Error('Could not create transparent PNG image.'));
                    return;
                }

                resolve(URL.createObjectURL(pngBlob));
            }, 'image/png');
        });
    });
}

function convertImageBlobToJpg(blob) {
    return convertQrImageBlob(blob, ({ context, canvas }) => {
        context.fillStyle = '#FFFFFF';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(canvas.sourceImage, 0, 0);

        return new Promise((resolve, reject) => {
            canvas.toBlob((jpgBlob) => {
                if (!jpgBlob) {
                    reject(new Error('Could not create JPG image.'));
                    return;
                }

                resolve(URL.createObjectURL(jpgBlob));
            }, 'image/jpeg', 0.92);
        });
    });
}

function convertQrImageBlob(blob, renderCanvas) {
    return new Promise((resolve, reject) => {
        const sourceUrl = URL.createObjectURL(blob);
        const image = new Image();
        image.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = image.naturalWidth || image.width;
            canvas.height = image.naturalHeight || image.height;
            canvas.sourceImage = image;

            const context = canvas.getContext('2d');
            if (!context) {
                URL.revokeObjectURL(sourceUrl);
                reject(new Error('Canvas context is not available.'));
                return;
            }

            Promise.resolve(renderCanvas({ context, canvas }))
                .then((objectUrl) => {
                    URL.revokeObjectURL(sourceUrl);
                    resolve(objectUrl);
                })
                .catch((error) => {
                    URL.revokeObjectURL(sourceUrl);
                    reject(error);
                });
        };
        image.onerror = () => {
            URL.revokeObjectURL(sourceUrl);
            reject(new Error('Could not load QR image.'));
        };
        image.src = sourceUrl;
    });
}

function printQrImage() {
    if (!previewUrl) {
        setError('Public URL is not available.');
        return;
    }

    const printWindow = window.open('', '_blank');
    if (!printWindow) {
        setError('Pop-up blocked. Please allow pop-ups and try again.');
        return;
    }

    printWindow.document.write(`
        <html>
        <head><title>Print QR Code</title></head>
        <body style="margin:0;display:flex;align-items:center;justify-content:center;min-height:100vh;">
            <img src="${qrImageUrl.value}" alt="Wedding QR Code" style="width:420px;height:420px;object-fit:contain;" />
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function formatStatus(status) {
    return status === 'attending' ? 'Attending' : 'Not Attending';
}

function rsvpMealChoiceRows(row) {
    const choices = row?.rsvp?.meal_choices;

    if (!Array.isArray(choices)) {
        return [];
    }

    return choices
        .map((choice, index) => {
            const selections = choice?.selections && typeof choice.selections === 'object'
                ? Object.entries(choice.selections)
                    .filter(([, value]) => String(value || '').trim())
                    .map(([course, value]) => `${formatCourseLabel(course)}: ${value}`)
                : [];

            return {
                guest: choice?.guest_name || `Guest ${index + 1}`,
                summary: choice?.meal || selections.join(', ') || 'No meal summary recorded',
                selections,
            };
        })
        .filter((choice) => choice.summary || choice.selections.length);
}

function formatCourseLabel(value) {
    return String(value || '')
        .replace(/[_-]+/g, ' ')
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
}

function rsvpStatusMeta(partyItem) {
    const status = partyItem?.rsvp?.status;

    if (status === 'attending') {
        return {
            icon: 'check',
            color: '#21C177',
            title: 'Guest confirmed attending',
        };
    }

    if (status === 'not_attending') {
        return {
            icon: 'close',
            color: '#E66363',
            title: 'Guest confirmed not attending',
        };
    }

    return {
        icon: 'horizontal_rule',
        color: '#D79A2B',
        title: 'Guest has not yet responded',
    };
}

function guestTypeMeta(partyItem) {
    if ((partyItem?.guest_type || 'day') === 'evening') {
        return {
            icon: 'dark_mode',
            color: '#22363A',
            label: 'Evening Guest',
            title: 'Evening guest invitation',
        };
    }

    return {
        icon: 'sunny',
        color: '#D79A2B',
        label: 'All Day Guest',
        title: 'All Day Guest invitation',
    };
}

function emailActionTitle(email, fallbackTitle = 'Email Guest') {
    return email ? fallbackTitle : "this guest doesn't have an email address";
}

function partyRsvpUrl(partyItem) {
    if (!previewUrl || !partyItem?.code) {
        return '';
    }

    try {
        const url = new URL(previewUrl, window.location.origin);
        url.searchParams.set('code', partyItem.code);
        return url.toString();
    } catch (error) {
        const separator = previewUrl.includes('?') ? '&' : '?';
        return `${previewUrl}${separator}code=${encodeURIComponent(partyItem.code)}`;
    }
}

function responseDeadlineLabel() {
    const deadline = content.value?.guest_list?.responseDeadline;
    return deadline ? formatDate(deadline) : '';
}

function whatsappInviteMessage(partyItem) {
    const partyName = String(partyItem?.display_name || 'there').trim();
    const guestType = guestTypeMeta(partyItem).label;
    const deadline = responseDeadlineLabel();
    const rsvpUrl = partyRsvpUrl(partyItem);
    const weddingName = String(siteTitle.value || 'our wedding').trim();
    const lines = [
        `Hi ${partyName},`,
        '',
        `We would love you to join us for ${weddingName}.`,
        '',
        'Please visit our wedding website to RSVP:',
        rsvpUrl,
        '',
        `Your RSVP code is: ${partyItem?.code || ''}`,
        `You are invited as an ${guestType}.`,
    ];

    if (deadline) {
        lines.push(`Please RSVP by ${deadline}.`);
    }

    return lines.join('\n');
}

function openWhatsappInvite(partyItem) {
    clearError();

    if (!sitePublished.value) {
        setError('Publish your website before sharing RSVP links by WhatsApp.');
        return;
    }

    if (!partyItem?.code || !partyRsvpUrl(partyItem)) {
        setError('This party needs an RSVP code before you can share it on WhatsApp.');
        return;
    }

    const inviteMessage = whatsappInviteMessage(partyItem);
    const copyPromise = copyToClipboard(
        inviteMessage,
        `Full WhatsApp invite copied for ${partyItem.display_name}. If WhatsApp only inserts the link, paste the copied message.`,
        'Copy this WhatsApp invitation:'
    );
    const shareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(inviteMessage)}`;
    window.open(shareUrl, '_blank', 'noopener,noreferrer');

    copyPromise.then((copied) => {
        if (!copied) {
            setMessage(`WhatsApp opened for ${partyItem.display_name}.`);
        }
    });
}

async function copyInvitationDetails(partyItem) {
    clearError();

    if (!sitePublished.value) {
        setError('Publish your website before copying invitation details.');
        return;
    }

    if (!partyItem?.code || !partyRsvpUrl(partyItem)) {
        setError('This party needs an RSVP code before you can copy invitation details.');
        return;
    }

    const copied = await copyToClipboard(
        whatsappInviteMessage(partyItem),
        `Invitation details copied for ${partyItem.display_name}.`,
        'Copy these invitation details:'
    );

    if (copied) {
        copiedInvitationPartyId.value = partyItem.id;
        if (copiedInvitationResetTimer) {
            clearTimeout(copiedInvitationResetTimer);
        }
        copiedInvitationResetTimer = setTimeout(() => {
            copiedInvitationPartyId.value = null;
        }, 2200);
    }
}

function viewResponseActionTitle(row) {
    return row?.rsvp ? 'View Response' : 'no response has been given yet';
}

function formatGuestName(guest) {
    const fullName = `${guest?.first_name || ''} ${guest?.last_name || ''}`.trim();
    return fullName || 'Unnamed guest';
}

function truncateText(value, maxLength) {
    if (!value) {
        return '';
    }

    const text = String(value);

    if (text.length <= maxLength) {
        return text;
    }

    return `${text.slice(0, Math.max(0, maxLength - 3)).trimEnd()}...`;
}

function guestSummary(partyItem) {
    return (partyItem?.guests || []).map((guest) => formatGuestName(guest)).join(', ');
}

function addTimelineItem() {
    if (!content.value) {
        return;
    }
    if (isTimelineAtMax.value) {
        openNoticeModal('Timeline limit reached', 'You can only have a maximum of 8 timeline items in this section.');
        return;
    }

    content.value.timeline.items.push({ time: '', title: '', description: '' });
    const targetIndex = content.value.timeline.items.length - 1;
    scrollToElementById(`timeline-item-${targetIndex}`);
}

async function removeTimelineItem(index) {
    if (isTimelineAtMin.value) {
        return;
    }
    const confirmed = await openConfirmModal('Remove Timeline Item', 'Are you sure you want to remove this timeline item?');
    if (!confirmed) {
        return;
    }
    content.value.timeline.items.splice(index, 1);
}

function addFaqItem() {
    content.value.details.faqs.push({ question: '', answer: '' });
    const targetIndex = content.value.details.faqs.length - 1;
    scrollToElementById(`faq-item-${targetIndex}`);
}

async function removeFaqItem(index) {
    const confirmed = await openConfirmModal('Remove FAQ', 'Are you sure you want to remove this FAQ item?');
    if (!confirmed) {
        return;
    }
    content.value.details.faqs.splice(index, 1);
}

function addGalleryImage() {
    ensureImageFocusDefaults();

    if (isGalleryAtMax.value) {
        openNoticeModal('Gallery limit reached', 'You can only add a maximum of 8 gallery images.');
        return;
    }

    content.value.gallery.items.push({
        image: '',
        imageFocusX: 50,
        imageFocusY: 50,
    });

    const targetIndex = content.value.gallery.items.length - 1;
    scrollToElementById(`gallery-item-${targetIndex}`);
}

async function removeGalleryImage(index) {
    const confirmed = await openConfirmModal('Remove Gallery Image', 'Are you sure you want to remove this gallery image?');
    if (!confirmed) {
        return;
    }

    content.value.gallery.items.splice(index, 1);
}

function moveGalleryImage(index, offset) {
    const items = content.value?.gallery?.items;
    if (!Array.isArray(items)) {
        return;
    }

    moveInArray(items, index, index + offset);
}

function openImageLibrary(index) {
    selectedGalleryLibraryIndex.value = index;
    imageLibraryModalOpen.value = true;
}

function closeImageLibrary() {
    imageLibraryModalOpen.value = false;
    selectedGalleryLibraryIndex.value = null;
}

function selectGalleryImageFromLibrary(imagePath) {
    const index = selectedGalleryLibraryIndex.value;
    if (index === null || !content.value?.gallery?.items?.[index]) {
        closeImageLibrary();
        return;
    }

    content.value.gallery.items[index].image = imagePath;
    if (typeof content.value.gallery.items[index].imageFocusX !== 'number') {
        content.value.gallery.items[index].imageFocusX = 50;
    }
    if (typeof content.value.gallery.items[index].imageFocusY !== 'number') {
        content.value.gallery.items[index].imageFocusY = 50;
    }

    closeImageLibrary();
    scrollToElementById(`gallery-item-${index}`);
}

function themePreviewUrl(layout) {
    if (!previewUrl) {
        return '#';
    }

    try {
        const url = new URL(previewUrl, window.location.origin);
        url.searchParams.set('preview_layout', layout);
        return url.toString();
    } catch {
        const separator = previewUrl.includes('?') ? '&' : '?';
        return `${previewUrl}${separator}preview_layout=${encodeURIComponent(layout)}`;
    }
}

function normalizeThemeLayout(layout) {
    const normalized = (layout || 'classic').toString().trim().toLowerCase();

    if (normalized === 'editorial') {
        return 'modern';
    }

    return ['classic', 'modern'].includes(normalized) ? normalized : 'classic';
}

function selectThemeLayout(layout) {
    if (!content.value.theme || typeof content.value.theme !== 'object') {
        content.value.theme = {};
    }

    content.value.theme.layout = normalizeThemeLayout(layout);
}

function selectColourPalette(slug) {
    if (!content.value.theme || typeof content.value.theme !== 'object') {
        content.value.theme = {};
    }

    content.value.theme.palette = normalizeColourPalette(slug);
    delete content.value.theme.palette_colours;
}

function normalizeColourPalette(slug) {
    const normalized = String(slug || 'magic_classic').trim().toLowerCase().replace(/[\s-]+/g, '_');
    return colourPaletteOptions.value.some((palette) => palette.slug === normalized) ? normalized : 'magic_classic';
}

function normalizeColourPalettes(palettes) {
    if (!Array.isArray(palettes) || palettes.length === 0) {
        return fallbackColourPalettes;
    }

    const normalizedPalettes = palettes
        .map((palette) => ({
            slug: String(palette?.slug || '').trim(),
            name: String(palette?.name || '').trim(),
            mood: String(palette?.mood || '').trim(),
            colours: {
                primary: normalizeHexColour(palette?.colours?.primary, '#22363A'),
                secondary: normalizeHexColour(palette?.colours?.secondary, '#466369'),
                dark: normalizeHexColour(palette?.colours?.dark, '#0F1B1D'),
                soft_background: normalizeHexColour(palette?.colours?.soft_background, '#F7F5F2'),
                light: normalizeHexColour(palette?.colours?.light, '#FFFFFF'),
            },
        }))
        .filter((palette) => palette.slug && palette.name);

    return normalizedPalettes.length ? normalizedPalettes : fallbackColourPalettes;
}

function normalizeHexColour(colour, fallback) {
    const normalized = String(colour || '').trim().toUpperCase();
    return /^#[0-9A-F]{6}$/.test(normalized) ? normalized : fallback;
}

function paletteSwatches(palette) {
    return [
        palette?.colours?.primary,
        palette?.colours?.secondary,
        palette?.colours?.dark,
        palette?.colours?.soft_background,
        palette?.colours?.light,
    ].filter(Boolean);
}

async function saveContent(openPreviewAfterSave = false) {
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
        await nextTick();
    }

    clearError();
    if (!content.value?.hero?.names?.trim()) {
        setError('Couple names are required.');
        return;
    }

    if (isSectionVisible('gallery') && galleryImageCount.value === 1) {
        openNoticeModal(
            'More gallery images needed',
            'Please upload at least 2 images for the photo gallery, or hide this section if you do not want it to appear.',
            '',
            'gallery-section'
        );
        return;
    }

    if (isSectionVisible('countdown') && !content.value?.countdown?.targetDateTime?.trim()) {
        openNoticeModal(
            'Ceremony date and time needed',
            'Please add the ceremony date and time for the countdown section, or hide this section if you do not want it to appear.',
            '',
            'countdown-section'
        );
        return;
    }

    const menuValidationError = validateMenuSettings();
    if (menuValidationError) {
        openNoticeModal('More menu items needed', menuValidationError.message, menuValidationError.note, 'menu-settings-section');
        return;
    }

    try {
        if (siteSettingsUpdateUrl && siteTitle.value.trim()) {
            await window.axios.put(siteSettingsUpdateUrl, {
                title: siteTitle.value.trim(),
            }, {
                headers: {
                    'Accept': 'application/json',
                },
            });
        }

        const response = await window.axios.put(`${apiBaseUrl}/content`, {
            content: content.value,
            rsvp_settings: rsvpSettings.value,
        });
        if (response.data?.content) {
            content.value = response.data.content;
            ensureImageFocusDefaults();
            ensureSectionVisibilityDefaults();
        }
        if (response.data?.colour_palettes) {
            colourPalettes.value = normalizeColourPalettes(response.data.colour_palettes);
        }
        if (response.data?.rsvp_settings) {
            rsvpSettings.value = {
                ...response.data.rsvp_settings,
                menu_courses: normalizeMenuCourses(response.data.rsvp_settings?.menu_courses),
            };
        }
        lastSavedAt.value = formatDateTime(response.data?.last_saved_at) || new Date().toLocaleString();
        captureSavedSnapshots();
        setMessage('Content Saved, make sure to refresh your preview page to see changes made', 7000);
        if (openPreviewAfterSave && previewUrl) {
            window.open(previewUrl, '_blank', 'noopener,noreferrer');
        }
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not save content.'));
    }
}

async function saveGuestListEmailSettings() {
    clearError();
    clearRsvpEmailSettingsNotice();

    try {
        const response = await window.axios.put(`${apiBaseUrl}/content`, {
            content: content.value,
            rsvp_settings: rsvpSettings.value,
        });

        if (response.data?.content) {
            content.value = response.data.content;
            ensureImageFocusDefaults();
            ensureSectionVisibilityDefaults();
        }
        if (response.data?.colour_palettes) {
            colourPalettes.value = normalizeColourPalettes(response.data.colour_palettes);
        }

        if (response.data?.rsvp_settings) {
            rsvpSettings.value = {
                ...response.data.rsvp_settings,
                menu_courses: normalizeMenuCourses(response.data.rsvp_settings?.menu_courses),
            };
        }

        lastSavedAt.value = formatDateTime(response.data?.last_saved_at) || new Date().toLocaleString();
        captureSavedSnapshots();
        rsvpEmailSettingsMessage.value = 'RSVP email settings saved.';
    } catch (error) {
        rsvpEmailSettingsError.value = extractErrorMessage(error, 'Could not save RSVP email settings.');
    }
}

async function toggleSitePublished() {
    clearError();

    if (!sitePublishUrl) {
        setError('Site publishing endpoint is not configured.');
        return;
    }

    try {
        const response = await window.axios.put(sitePublishUrl, {
            is_published: !sitePublished.value,
        }, {
            headers: {
                'Accept': 'application/json',
            },
        });

        sitePublished.value = Boolean(response.data?.is_published ?? !sitePublished.value);
        setMessage(response.data?.message || (sitePublished.value ? 'Site published.' : 'Site moved to draft.'));
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not update site visibility.'));
    }
}

function addMenuCourse() {
    if (!Array.isArray(rsvpSettings.value.menu_courses)) {
        rsvpSettings.value.menu_courses = [];
    }

    rsvpSettings.value.menu_courses.push({
        id: `course-${Date.now()}`,
        name: '',
        items: [{ title: '', description: '' }],
    });
    const courseIndex = rsvpSettings.value.menu_courses.length - 1;
    scrollToElementById(`menu-course-${courseIndex}`);
}

async function removeMenuCourse(courseIndex) {
    if ((rsvpSettings.value.menu_courses || []).length <= 1) {
        openNoticeModal('Cannot remove course', 'At least one menu course is required.');
        return;
    }

    const confirmed = await openConfirmModal('Remove Course', 'Are you sure you want to remove this course?');
    if (!confirmed) {
        return;
    }

    rsvpSettings.value.menu_courses.splice(courseIndex, 1);
}

function addMenuCourseItem(courseIndex, force = false) {
    if (!canAddMenuOptions.value && !force) {
        openNoticeModal(
            'Set menu active',
            'Switch to "Guests choose their own" to add multiple options within a course.'
        );
        return;
    }

    if (!Array.isArray(rsvpSettings.value.menu_courses)) {
        rsvpSettings.value.menu_courses = [];
    }
    if (!rsvpSettings.value.menu_courses[courseIndex]) {
        return;
    }

    if (!Array.isArray(rsvpSettings.value.menu_courses[courseIndex].items)) {
        rsvpSettings.value.menu_courses[courseIndex].items = [];
    }
    rsvpSettings.value.menu_courses[courseIndex].items.push({ title: '', description: '' });
    const itemIndex = rsvpSettings.value.menu_courses[courseIndex].items.length - 1;
    scrollToElementById(`menu-course-${courseIndex}-item-${itemIndex}`);
}

function addKidsMenuItem() {
    if (!Array.isArray(rsvpSettings.value.kids_menu_items)) {
        rsvpSettings.value.kids_menu_items = [];
    }

    rsvpSettings.value.kids_menu_items.push({ title: '', description: '' });
    const itemIndex = rsvpSettings.value.kids_menu_items.length - 1;
    scrollToElementById(`kids-menu-item-${itemIndex}`);
}

async function removeKidsMenuItem(itemIndex) {
    const confirmed = await openConfirmModal('Remove Kids Menu Item', 'Are you sure you want to remove this kids menu item?');
    if (!confirmed) {
        return;
    }

    rsvpSettings.value.kids_menu_items.splice(itemIndex, 1);
}

async function removeMenuCourseItem(courseIndex, itemIndex) {
    const course = rsvpSettings.value.menu_courses?.[courseIndex];
    if (!course || !Array.isArray(course.items)) {
        return;
    }

    const confirmed = await openConfirmModal('Remove Course Item', 'Are you sure you want to remove this menu item?');
    if (!confirmed) {
        return;
    }

    course.items.splice(itemIndex, 1);
    if (course.items.length === 0) {
        course.items.push({ title: '', description: '' });
    }
}

function ensureImageFocusDefaults() {
    if (!content.value) {
        return;
    }

    if (typeof content.value.theme !== 'object' || content.value.theme === null) {
        content.value.theme = {};
    }

    content.value.theme.palette = normalizeColourPalette(content.value.theme.palette);

    if (!content.value.theme.primary_color) {
        content.value.theme.primary_color = '#22363A';
    }

    if (!content.value.theme.button_color) {
        content.value.theme.button_color = '#22363A';
    }

    content.value.theme.layout = normalizeThemeLayout(content.value.theme.layout);

    if (typeof content.value.gallery !== 'object' || content.value.gallery === null) {
        content.value.gallery = {
            heading: "Photo's of us across the years",
            items: [],
        };
    }

    if (!Array.isArray(content.value.gallery.items)) {
        content.value.gallery.items = [];
    }

    if (!content.value.gallery.heading) {
        content.value.gallery.heading = "Photo's of us across the years";
    }

    if (typeof content.value.countdown !== 'object' || content.value.countdown === null) {
        content.value.countdown = {
            targetDateTime: '2026-09-12T15:30',
        };
    }

    if (!content.value.countdown.targetDateTime) {
        content.value.countdown.targetDateTime = '2026-09-12T15:30';
    }

    if (typeof content.value.guest_list !== 'object' || content.value.guest_list === null) {
        content.value.guest_list = {
            responseDeadline: '2026-08-15',
        };
    }

    if (!content.value.guest_list.responseDeadline) {
        content.value.guest_list.responseDeadline = '2026-08-15';
    }

    content.value.gallery.items = content.value.gallery.items.slice(0, 8).map((item) => ({
        image: item?.image || '',
        imageFocusX: typeof item?.imageFocusX === 'number' ? item.imageFocusX : 50,
        imageFocusY: typeof item?.imageFocusY === 'number' ? item.imageFocusY : 50,
    }));

    const defaults = [
        ['hero', 'imageFocusX'],
        ['hero', 'imageFocusY'],
        ['welcome', 'imageFocusX'],
        ['welcome', 'imageFocusY'],
        ['story', 'imageFocusX'],
        ['story', 'imageFocusY'],
        ['details', 'imageFocusX'],
        ['details', 'imageFocusY'],
    ];

    defaults.forEach(([section, key]) => {
        if (typeof content.value?.[section]?.[key] !== 'number') {
            content.value[section][key] = 50;
        }
    });
}

function ensureSectionVisibilityDefaults() {
    if (!content.value) {
        return;
    }

    if (typeof content.value.section_visibility !== 'object' || content.value.section_visibility === null) {
        content.value.section_visibility = {};
    }

    const defaults = {
        welcome: true,
        story: true,
        timeline: true,
        venue: true,
        travel: true,
        menu: true,
        faqs: true,
        countdown: true,
        gallery: true,
    };

    Object.entries(defaults).forEach(([key, defaultValue]) => {
        if (typeof content.value.section_visibility[key] !== 'boolean') {
            content.value.section_visibility[key] = defaultValue;
        }
    });
}

function isSectionVisible(sectionKey) {
    return Boolean(content.value?.section_visibility?.[sectionKey]);
}

function toggleSectionVisibility(sectionKey) {
    ensureSectionVisibilityDefaults();
    content.value.section_visibility[sectionKey] = !Boolean(content.value.section_visibility[sectionKey]);
}

function normalizeMenuCourses(courses) {
    const defaultNameKeys = new Set(['starter', 'main', 'dessert']);
    const seenDefaultKeys = new Set();
    const seenIds = new Set();
    let dynamicIndex = 1;

    if (Array.isArray(courses) && courses.length > 0) {
        const normalized = courses
            .map((course, index) => {
                const rawId = (course.id || '').toString().trim().toLowerCase();
                const name = (course.name || '').toString().trim();
                const nameKey = name.toLowerCase();
                const defaultKey = defaultNameKeys.has(rawId) ? rawId : (defaultNameKeys.has(nameKey) ? nameKey : null);
                const id = rawId || `course-${index + 1}`;

                return {
                    id,
                    name: name || (defaultKey ? defaultKey.charAt(0).toUpperCase() + defaultKey.slice(1) : `Course ${index + 1}`),
                    defaultKey,
                    items: normalizeCourseItems(course.items),
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
        return ['starter', 'main', 'dessert'].map((key) => ({
            id: key,
            name: key.charAt(0).toUpperCase() + key.slice(1),
            items: normalizeCourseItems(courses[key]),
        }));
    }

    return defaultMenuCourses.map((course) => ({
        id: course.id || `course-${dynamicIndex++}`,
        name: course.name,
        items: normalizeCourseItems(course.items.map((item) => ({ ...item }))),
    }));
}

function normalizeCourseItems(items) {
    if (!Array.isArray(items) || items.length === 0) {
        return [{ title: '', description: '' }];
    }

    return items.map((item) => ({
        title: (item?.title || '').toString(),
        description: (item?.description || '').toString(),
    }));
}

function countCompletedMenuItems(items) {
    return (Array.isArray(items) ? items : []).filter((item) => item?.title?.trim()).length;
}

function validateMenuSettings() {
    if (rsvpSettings.value?.meal_mode !== 'options') {
        return null;
    }

    const incompleteCourses = (rsvpSettings.value?.menu_courses || [])
        .filter((course) => countCompletedMenuItems(course?.items) < 2)
        .map((course) => course?.name?.trim() || 'Untitled course');

    if (incompleteCourses.length === 0) {
        return null;
    }

    return {
        message: `Guests choose their own requires at least 2 menu items in each course. Please add more menu items to: ${incompleteCourses.join(', ')}.`,
        note: 'Or if you only have 1 option, change your RSVP meal type to Set.',
    };
}

function hasMultipleOptionsPerCourse() {
    const courses = rsvpSettings.value?.menu_courses;
    if (!Array.isArray(courses)) {
        return false;
    }

    return courses.some((course) => Array.isArray(course?.items) && course.items.length > 1);
}

async function uploadContentImage(event, field) {
    clearError();
    const file = event.target.files?.[0];
    if (!file) {
        return;
    }

    if (!['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'].includes(file.type)) {
        setError('Please upload a JPG, PNG, WEBP, or SVG file.');
        event.target.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('field', field);
    formData.append('image_file', file);

    try {
        const response = await window.axios.post(`${apiBaseUrl}/content/image`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.data?.content) {
            content.value = response.data.content;
        } else if (response.data?.path) {
            setNestedContentValue(field, response.data.path);
        }
        ensureImageFocusDefaults();
        ensureSectionVisibilityDefaults();
        imageLibrary.value = mergeImageLibrary(response.data?.image_library, response.data?.path);
        setMessage(response.data.message || 'Image updated.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not upload image.'));
    } finally {
        event.target.value = '';
    }
}

function setNestedContentValue(path, value) {
    if (!content.value || !path) {
        return;
    }

    const keys = path.split('.');
    let target = content.value;

    keys.slice(0, -1).forEach((key) => {
        if (/^\d+$/.test(key)) {
            key = Number(key);
        }

        if (target[key] === undefined || target[key] === null) {
            target[key] = {};
        }

        target = target[key];
    });

    const finalKey = keys[keys.length - 1];
    target[finalKey] = value;
}

function mergeImageLibrary(serverLibrary, uploadedPath = '') {
    const paths = [
        ...(Array.isArray(imageLibrary.value) ? imageLibrary.value : []),
        ...(Array.isArray(serverLibrary) ? serverLibrary : []),
        uploadedPath,
    ];

    return [...new Set(paths.filter(Boolean))];
}

async function createParty() {
    clearCreatePartyFeedback();

    if (!newParty.display_name || newParty.display_name.trim() === '') {
        setCreatePartyError('Party name is required.');
        return;
    }

    if (newParty.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newParty.email.trim())) {
        setCreatePartyError('Enter a valid email address for the party.');
        return;
    }

    if (!Array.isArray(newPartyGuests.value) || newPartyGuests.value.length === 0) {
        setCreatePartyError('Add at least one guest for this party.');
        return;
    }

    const guestsPayload = newPartyGuests.value.map((guest) => ({
        first_name: guest.first_name?.trim() || '',
        last_name: guest.last_name?.trim() || '',
        is_child: Boolean(guest.is_child),
        allow_plus_one: Boolean(guest.allow_plus_one),
    }));

    const hasIncompleteGuest = guestsPayload.some((guest) => !guest.first_name || !guest.last_name);
    if (hasIncompleteGuest) {
        setCreatePartyError('Every guest row must include first and last name.');
        return;
    }

    const invitedSeats = guestsPayload.length + guestsPayload.filter((guest) => guest.allow_plus_one).length;
    if (invitedSeats < 1 || invitedSeats > 20) {
        setCreatePartyError('Invited seats must be between 1 and 20.');
        return;
    }

    if (newParty.code?.trim() && !/^[A-Za-z]{3,10}$/.test(newParty.code.trim())) {
        setCreatePartyError('RSVP code must be 3-10 letters.');
        return;
    }

    try {
        await window.axios.post(`${apiBaseUrl}/parties`, {
            ...newParty,
            guests: guestsPayload,
            max_guests: invitedSeats,
        });
        newParty.display_name = '';
        newParty.guest_type = 'day';
        newParty.email = '';
        newParty.max_guests = 1;
        newParty.notes = '';
        newPartyGuests.value = [createEmptyGuestRow()];
        await generateCodeForCreate(false);
        await loadParties();
        await loadStats();
        setCreatePartyMessage('Party created successfully.');
    } catch (error) {
        setCreatePartyError(extractErrorMessage(error, 'Could not create party.'));
    }
}

async function updateParty() {
    clearError();
    if (!selectedParty.value) {
        setError('Select a party to update.');
        return;
    }

    if (!selectedParty.value.display_name?.trim()) {
        setError('Party name is required.');
        return;
    }

    if (!selectedParty.value.code?.trim()) {
        setError('RSVP code is required.');
        return;
    }

    if (selectedParty.value.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(selectedParty.value.email.trim())) {
        setError('Enter a valid email address for the party.');
        return;
    }

    if (!/^[A-Za-z]{3,10}$/.test(selectedParty.value.code.trim())) {
        setError('RSVP code must be 3-10 letters.');
        return;
    }

    if (selectedParty.value.max_guests < 1 || selectedParty.value.max_guests > 20) {
        setError('Max guests must be between 1 and 20.');
        return;
    }

    try {
        await window.axios.put(`${apiBaseUrl}/parties/${selectedParty.value.id}`, selectedParty.value);
        await loadParties();
        await loadStats();
        setMessage('Party updated successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not update party.'));
    }
}

async function generateCodeForCreate(showMessage = true) {
    clearError();
    try {
        const response = await window.axios.get(`${apiBaseUrl}/parties/generate-code`);
        newParty.code = response.data.code;
        if (showMessage) {
            setMessage('Unique 4-letter code generated.');
        }
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not generate RSVP code.'));
    }
}

async function generateCodeForSelectedParty() {
    clearError();
    if (!selectedParty.value) {
        setError('Select a party to generate a code.');
        return;
    }

    try {
        const response = await window.axios.get(`${apiBaseUrl}/parties/generate-code`);
        selectedParty.value.code = response.data.code;
        setMessage('Unique 4-letter code generated.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not generate RSVP code.'));
    }
}

async function deleteParty() {
    clearError();
    if (!selectedParty.value) {
        setError('Select a party to delete.');
        return;
    }

    const confirmed = await openConfirmModal(
        'Remove Party',
        'Are you sure you want to remove this party and all guests/RSVP data?'
    );
    if (!confirmed) {
        return;
    }

    try {
        await window.axios.delete(`${apiBaseUrl}/parties/${selectedParty.value.id}`);
        closeEditPartyModal();
        selectedPartyId.value = null;
        await Promise.all([loadParties(), loadStats(), loadRsvps()]);
        if (parties.value.length > 0) {
            selectedPartyId.value = parties.value[0].id;
        }
        selectedPartyIdsForEmail.value = selectedPartyIdsForEmail.value.filter((id) =>
            parties.value.some((party) => party.id === id)
        );
        setMessage('Party deleted successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not delete party.'));
    }
}

async function deletePartyById(partyId, partyName = 'this party') {
    clearError();

    const confirmed = await openConfirmModal(
        'Remove Party',
        `Are you sure you want to remove ${partyName} and all guests/RSVP data?`
    );
    if (!confirmed) {
        return;
    }

    try {
        await window.axios.delete(`${apiBaseUrl}/parties/${partyId}`);
        selectedPartyIdsForEmail.value = selectedPartyIdsForEmail.value.filter((id) => id !== partyId);
        await Promise.all([loadParties(), loadStats(), loadRsvps()]);
        setMessage('Party deleted successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not delete party.'));
    }
}

async function deleteSelectedParties() {
    clearError();
    const selectedIds = [...selectedPartyIdsForEmail.value];
    if (selectedIds.length === 0) {
        setError('Select at least one party to delete.');
        return;
    }

    const selectedNames = parties.value
        .filter((party) => selectedIds.includes(party.id))
        .map((party) => party.display_name)
        .join(', ');

    const confirmed = await openConfirmModal(
        'Delete Selected Parties',
        `Are you sure you want to delete: ${selectedNames}?`
    );
    if (!confirmed) {
        return;
    }

    try {
        await Promise.all(selectedIds.map((partyId) => window.axios.delete(`${apiBaseUrl}/parties/${partyId}`)));
        selectedPartyIdsForEmail.value = [];
        await Promise.all([loadParties(), loadStats(), loadRsvps()]);
        setMessage('Selected parties deleted successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not delete selected parties.'));
    }
}

async function addGuest() {
    clearError();
    if (!selectedParty.value) {
        setError('Select a party before adding a guest.');
        return;
    }

    if (!newGuest.first_name?.trim() || !newGuest.last_name?.trim()) {
        setError('Guest first and last name are required.');
        return;
    }

    try {
        await window.axios.post(`${apiBaseUrl}/parties/${selectedParty.value.id}/guests`, newGuest);
        newGuest.first_name = '';
        newGuest.last_name = '';
        newGuest.is_child = false;
        newGuest.allow_plus_one = false;
        await Promise.all([loadParties(), loadStats()]);
        setMessage('Guest added successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not add guest.'));
    }
}

async function addAnonymousPlusOneGuest() {
    if (!selectedParty.value) {
        setError('Select a party before adding a guest.');
        return;
    }

    newGuest.first_name = 'Anonymous';
    newGuest.last_name = '+1';
    newGuest.is_child = false;
    newGuest.allow_plus_one = false;
    await addGuest();
}

async function updateGuest(guest) {
    clearError();
    if (!guest.first_name?.trim() || !guest.last_name?.trim()) {
        setError('Guest first and last name are required.');
        return;
    }

    try {
        await window.axios.put(`${apiBaseUrl}/guests/${guest.id}`, guest);
        setMessage('Guest updated successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not update guest.'));
    }
}

async function deleteGuest(guest, index = -1) {
    clearError();
    if (index === 0) {
        setError('The first guest in the party cannot be removed.');
        return;
    }
    const confirmed = await openConfirmModal('Remove Guest', 'Are you sure you want to remove this guest?');
    if (!confirmed) {
        return;
    }

    try {
        await window.axios.delete(`${apiBaseUrl}/guests/${guest.id}`);
        await Promise.all([loadParties(), loadStats()]);
        setMessage('Guest removed successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not remove guest.'));
    }
}

function editRsvp(row) {
    editingRsvp.value = row;
    rsvpForm.status = row.rsvp?.status || 'attending';
    rsvpForm.attending_count = row.rsvp?.attending_count || 0;
    rsvpForm.meal_choices = row.rsvp?.meal_choices || [];
    rsvpForm.dietary_restrictions = row.rsvp?.dietary_restrictions || '';
    rsvpForm.message = row.rsvp?.message || '';
    clearError();
}

function closeRsvpModal() {
    editingRsvp.value = null;
}

async function saveAdminRsvp() {
    clearError();
    if (!editingRsvp.value) {
        setError('Choose an RSVP entry to edit first.');
        return;
    }

    if (rsvpForm.attending_count < 0 || rsvpForm.attending_count > editingRsvp.value.max_guests) {
        setError(`Attending count must be between 0 and ${editingRsvp.value.max_guests}.`);
        return;
    }

    try {
        await window.axios.put(`${apiBaseUrl}/rsvps/${editingRsvp.value.party_id}`, rsvpForm);
        await Promise.all([loadRsvps(), loadStats(), loadParties()]);
        closeRsvpModal();
        setMessage('RSVP updated successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not update RSVP.'));
    }
}

async function importParties(event) {
    clearError();
    const file = event.target.files?.[0];
    if (!file) {
        return;
    }

    if (!file.name.toLowerCase().endsWith('.csv')) {
        setError('Please select a valid CSV file.');
        event.target.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('csv_file', file);

    try {
        const response = await window.axios.post(`${apiBaseUrl}/parties/import`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        await Promise.all([loadParties(), loadStats(), loadRsvps()]);
        setMessage(`${response.data.message} ${response.data.created_parties} parties, ${response.data.created_guests} guests.`);
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not import CSV.'));
    } finally {
        event.target.value = '';
    }
}

async function sendRsvpEmailsToSelected() {
    clearError();
    if (!canSendRsvpEmails.value) {
        setError(rsvpEmailSendTitle.value);
        closeSendRsvpConfirmModal();
        return;
    }

    const partyIds = selectedEmailableParties.value.map((party) => party.id);
    if (partyIds.length === 0) {
        setError('Select at least one party with an email address.');
        closeSendRsvpConfirmModal();
        return;
    }

    try {
        const response = await window.axios.post(`${apiBaseUrl}/parties/send-rsvp-emails`, {
            party_ids: partyIds,
        });
        await Promise.all([loadParties(), loadRsvps()]);
        closeSendRsvpConfirmModal();
        selectedPartyIdsForEmail.value = [];
        setMessage(response.data?.message || 'RSVP emails sent successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not send RSVP emails.'));
    }
}

async function sendRsvpReminderEmails() {
    clearError();
    clearRsvpEmailSettingsNotice();

    if (!canSendRsvpEmails.value) {
        rsvpEmailSettingsError.value = rsvpEmailSendTitle.value;
        closeSendRsvpReminderConfirmModal();
        return;
    }

    if (!hasRsvpDeadline()) {
        rsvpEmailSettingsError.value = 'Set an RSVP response deadline before sending reminders.';
        closeSendRsvpReminderConfirmModal();
        return;
    }

    const partyIds = reminderEligibleParties.value.map((party) => party.id);
    if (partyIds.length === 0) {
        rsvpEmailSettingsError.value = 'No parties with email addresses are waiting for a response.';
        closeSendRsvpReminderConfirmModal();
        return;
    }

    try {
        const response = await window.axios.post(`${apiBaseUrl}/parties/send-rsvp-reminders`, {
            party_ids: partyIds,
        });
        await Promise.all([loadParties(), loadRsvps()]);
        closeSendRsvpReminderConfirmModal();
        rsvpEmailSettingsMessage.value = response.data?.message || 'RSVP reminder emails sent.';
    } catch (error) {
        rsvpEmailSettingsError.value = extractErrorMessage(error, 'Could not send RSVP reminder emails.');
    }
}

function openConfirmModal(title, message, options = {}) {
    confirmModal.title = title;
    confirmModal.message = message;
    confirmModal.confirmClass = options.confirmClass || 'admin-btn-danger';
    confirmModal.confirmIcon = options.confirmIcon || 'close';
    confirmModal.confirmLabel = options.confirmLabel || 'Confirm';
    confirmModal.open = true;

    return new Promise((resolve) => {
        confirmResolve.value = resolve;
    });
}

function closeConfirmModal(confirmed) {
    confirmModal.open = false;
    if (typeof confirmResolve.value === 'function') {
        confirmResolve.value(confirmed);
        confirmResolve.value = null;
    }
}

function openNoticeModal(title, message, note = '', scrollTarget = '') {
    noticeModal.title = title;
    noticeModal.message = message;
    noticeModal.note = note;
    noticeModal.scrollTarget = scrollTarget;
    noticeModal.open = true;
}

function closeNoticeModal() {
    const scrollTarget = noticeModal.scrollTarget;
    noticeModal.open = false;
    noticeModal.note = '';
    noticeModal.scrollTarget = '';

    if (scrollTarget) {
        scrollToElementById(scrollTarget);
    }
}

function openDemoChoiceModal() {
    demoChoiceModalOpen.value = true;
}

function closeDemoChoiceModal() {
    demoChoiceModalOpen.value = false;
}

function openDemoPreview(layout) {
    const normalizedLayout = normalizeThemeLayout(layout);
    const url = new URL('/demo', window.location.origin);
    url.searchParams.set('preview_layout', normalizedLayout);
    window.open(url.toString(), '_blank', 'noopener,noreferrer');
    closeDemoChoiceModal();
}

function scrollToElementById(elementId) {
    nextTick(() => {
        window.requestAnimationFrame(() => {
            const element = document.getElementById(elementId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
}

function moveInArray(list, fromIndex, toIndex) {
    if (!Array.isArray(list) || fromIndex < 0 || toIndex < 0 || fromIndex >= list.length || toIndex >= list.length) {
        return;
    }

    const [moved] = list.splice(fromIndex, 1);
    list.splice(toIndex, 0, moved);
}

function moveTimelineItem(index, offset) {
    const items = content.value?.timeline?.items;
    if (!Array.isArray(items)) {
        return;
    }
    const targetIndex = index + offset;
    moveInArray(items, index, targetIndex);
}

function moveMenuCourse(index, offset) {
    const courses = rsvpSettings.value?.menu_courses;
    if (!Array.isArray(courses)) {
        return;
    }
    const targetIndex = index + offset;
    moveInArray(courses, index, targetIndex);
}

function moveFaqItem(index, offset) {
    const faqs = content.value?.details?.faqs;
    if (!Array.isArray(faqs)) {
        return;
    }
    const targetIndex = index + offset;
    moveInArray(faqs, index, targetIndex);
}

function applyFieldHelpAttributes() {
    const labels = document.querySelectorAll('.content-section-block label, .guest-help-scope label');

    labels.forEach((label) => {
        const labelText = extractLabelText(label);
        if (!labelText) {
            return;
        }

        const help = buildFieldHelp(labelText);
        if (!help) {
            return;
        }

        label.classList.add('label-help');
        label.setAttribute('data-help', help);
        ensureLabelHelpHeader(label, labelText, help);
    });
}

function extractLabelText(labelElement) {
    const existingHeader = labelElement.querySelector(':scope > .label-help-header .label-help-title-text');
    if (existingHeader) {
        return (existingHeader.textContent || '').replace(/\s+/g, ' ').trim();
    }

    let text = '';
    labelElement.childNodes.forEach((node) => {
        if (node.nodeType === Node.TEXT_NODE) {
            text += ` ${node.textContent || ''}`;
        }
    });
    return text.replace(/\s+/g, ' ').trim();
}

function ensureLabelHelpHeader(labelElement, labelText, helpText) {
    let header = labelElement.querySelector(':scope > .label-help-header');
    let title = header?.querySelector('.label-help-title-text');
    let icon = header?.querySelector('.label-help-icon');

    if (!header) {
        const leadingTextNodes = [];
        labelElement.childNodes.forEach((node) => {
            if (node.nodeType === Node.TEXT_NODE && node.textContent && node.textContent.trim()) {
                leadingTextNodes.push(node);
            }
        });

        leadingTextNodes.forEach((node) => node.remove());

        header = document.createElement('span');
        header.className = 'label-help-header';

        title = document.createElement('span');
        title.className = 'label-help-title-text';
        title.textContent = labelText;

        icon = document.createElement('span');
        icon.className = 'label-help-icon';
        icon.setAttribute('aria-hidden', 'true');
        icon.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 10.25V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="7.5" r="1.25" fill="currentColor"/></svg>';
        icon.style.display = 'inline-flex';
        icon.style.alignItems = 'center';
        icon.style.justifyContent = 'center';
        icon.style.marginLeft = '8px';
        icon.style.width = '16px';
        icon.style.height = '16px';
        icon.style.lineHeight = '1';
        icon.style.color = '#848484';
        icon.style.verticalAlign = 'middle';
        icon.style.position = 'relative';
        icon.style.top = '-1px';
        icon.style.cursor = 'help';

        header.appendChild(title);
        header.appendChild(icon);
        labelElement.insertBefore(header, labelElement.firstChild);
    }

    if (title) {
        title.textContent = labelText;
    }

    if (icon) {
        icon.className = 'label-help-icon';
        icon.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 10.25V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="7.5" r="1.25" fill="currentColor"/></svg>';
        icon.setAttribute('aria-hidden', 'true');
        icon.style.display = 'inline-flex';
        icon.style.alignItems = 'center';
        icon.style.justifyContent = 'center';
        icon.style.marginLeft = '8px';
        icon.style.width = '16px';
        icon.style.height = '16px';
        icon.style.lineHeight = '1';
        icon.style.color = '#848484';
        icon.style.verticalAlign = 'middle';
        icon.style.position = 'relative';
        icon.style.top = '-1px';
        icon.style.cursor = 'help';
        icon.style.pointerEvents = 'auto';
        icon.style.userSelect = 'none';
        icon.style.zIndex = '1';

        ensureHelpTooltip(icon, helpText);
    }

    header.setAttribute('data-help', helpText);
}

function ensureHelpTooltip(iconElement, helpText) {
    let tooltip = iconElement.querySelector(':scope > .label-help-tooltip');

    if (!tooltip) {
        tooltip = document.createElement('span');
        tooltip.className = 'label-help-tooltip';
        tooltip.style.position = 'absolute';
        tooltip.style.zIndex = '80';
        tooltip.style.display = 'none';
        tooltip.style.width = '320px';
        tooltip.style.maxWidth = '72vw';
        tooltip.style.border = '1px solid rgba(255,255,255,0.2)';
        tooltip.style.borderRadius = '8px';
        tooltip.style.background = '#0f1b1d';
        tooltip.style.color = '#ffffff';
        tooltip.style.padding = '0.55rem 0.7rem';
        tooltip.style.fontSize = '12px';
        tooltip.style.lineHeight = '1.4';
        tooltip.style.fontWeight = '400';
        tooltip.style.letterSpacing = '0';
        tooltip.style.textTransform = 'none';
        tooltip.style.whiteSpace = 'normal';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.boxShadow = '0 8px 18px rgba(15,27,29,0.25)';
        iconElement.appendChild(tooltip);
    }

    tooltip.textContent = helpText;

    const positionTooltip = () => {
        if (window.innerWidth <= 900) {
            tooltip.style.left = '0';
            tooltip.style.top = 'calc(100% + 8px)';
            tooltip.style.transform = 'none';
        } else {
            tooltip.style.left = 'calc(100% + 12px)';
            tooltip.style.top = '50%';
            tooltip.style.transform = 'translateY(-50%)';
        }
    };

    const showTooltip = () => {
        positionTooltip();
        tooltip.style.display = 'block';
    };

    const hideTooltip = () => {
        tooltip.style.display = 'none';
    };

    if (iconElement.dataset.helpBound !== '1') {
        iconElement.addEventListener('mouseenter', showTooltip);
        iconElement.addEventListener('mouseleave', hideTooltip);
        iconElement.dataset.helpBound = '1';
    }
}

function buildFieldHelp(labelText) {
    const lower = labelText.toLowerCase();

    if (lower.includes('colour palette')) return resolveFieldHelpText('theme.colour_palette', 'Choose a professionally designed colour palette. The website automatically handles button, section and text contrast.');
    if (lower.includes('couple names')) return resolveFieldHelpText('hero.couple_names', 'Example: Kyle & Nicole. This displays as the hero headline.');
    if (lower.includes('hero kicker')) return resolveFieldHelpText('hero.kicker', 'Example: We are getting married. Keep this short and warm.');
    if (lower.includes('wedding date')) return resolveFieldHelpText('hero.wedding_date', 'Example: 12 September 2026.');
    if (lower.includes('location line')) return resolveFieldHelpText('hero.location_line', 'Example: Ayrshire, Scotland.');
    if (lower.includes('hero rsvp button label')) return resolveFieldHelpText('hero.rsvp_button_label', 'Example: RSVP Now.');
    if (lower.includes('upload hero image')) return resolveFieldHelpText('hero.upload_image', 'Upload your main hero photo (JPG/PNG/WEBP/SVG).');
    if (lower.includes('hero focus x') || lower.includes('hero side to side') || lower.includes('hero image horizontal focus point')) return resolveFieldHelpText('hero.focus_x', 'Set horizontal focal point. 50% keeps the centre in view.');
    if (lower.includes('hero focus y') || lower.includes('hero up and down') || lower.includes('hero image vertical focus point')) return resolveFieldHelpText('hero.focus_y', 'Set vertical focal point. 50% keeps the centre in view.');
    if (lower.includes('welcome heading')) return resolveFieldHelpText('welcome.heading', 'Example: Dear Family & Friends.');
    if (lower.includes('welcome signoff')) return resolveFieldHelpText('welcome.signoff', 'Example: Kyle & Nicole.');
    if (lower.includes('welcome letter')) return resolveFieldHelpText('welcome.letter', 'Example: We are thrilled to celebrate with you on our wedding day.');
    if (lower.includes('upload welcome image')) return resolveFieldHelpText('welcome.upload_image', 'Upload the image that appears beside the welcome letter.');
    if (lower.includes('welcome focus x') || lower.includes('welcome side to side') || lower.includes('welcome image horizontal focus point')) return resolveFieldHelpText('welcome.focus_x', 'Adjust horizontal crop focus for the welcome image.');
    if (lower.includes('welcome focus y') || lower.includes('welcome up and down') || lower.includes('welcome image vertical focus point')) return resolveFieldHelpText('welcome.focus_y', 'Adjust vertical crop focus for the welcome image.');
    if (lower.includes('timeline heading')) return resolveFieldHelpText('timeline.heading', 'Example: The Big Day.');
    if (lower.includes('timeline accent')) return resolveFieldHelpText('timeline.accent', 'Example: Saturday, 12 September 2026.');
    if (lower === 'time') return resolveFieldHelpText('timeline.time', 'Example: 3:30 PM.');
    if (lower === 'event') return resolveFieldHelpText('timeline.event', 'Example: Ceremony.');
    if (lower === 'description') return resolveFieldHelpText('timeline.description', 'Example: Join us as we say “I do.”');
    if (lower.includes('our story heading')) return resolveFieldHelpText('story.heading', 'Example: Our Story.');
    if (lower.includes('our story accent')) return resolveFieldHelpText('story.accent', 'Example: March 2016.');
    if (lower.includes('our story text')) return resolveFieldHelpText('story.text', 'Share a short story of how you met and your journey together.');
    if (lower.includes('upload our story image')) return resolveFieldHelpText('story.upload_image', 'Upload the photo shown beside your story.');
    if (lower.includes('story focus x') || lower.includes('story side to side') || lower.includes('story image horizontal focus point')) return resolveFieldHelpText('story.focus_x', 'Adjust horizontal crop focus for the story image.');
    if (lower.includes('story focus y') || lower.includes('story up and down') || lower.includes('story image vertical focus point')) return resolveFieldHelpText('story.focus_y', 'Adjust vertical crop focus for the story image.');
    if (lower.includes('venue name')) return resolveFieldHelpText('details.venue_name', 'Example: Lochgreen House Hotel.');
    if (lower.includes('venue address')) return resolveFieldHelpText('details.venue_address', 'Example: Monktonhill Rd, Troon KA10 7EN.');
    if (lower.includes('venue blurb') || lower.includes('venue information')) return resolveFieldHelpText('details.venue_blurb', 'Example: Ceremony and reception are both onsite.');
    if (lower.includes('upload venue image')) return resolveFieldHelpText('details.upload_image', 'Upload the image shown alongside venue/travel details.');
    if (lower.includes('venue focus x') || lower.includes('venue side to side') || lower.includes('venue image horizontal focus point')) return resolveFieldHelpText('details.focus_x', 'Adjust horizontal crop focus for the venue image.');
    if (lower.includes('venue focus y') || lower.includes('venue up and down') || lower.includes('venue image vertical focus point')) return resolveFieldHelpText('details.focus_y', 'Adjust vertical crop focus for the venue image.');
    if (lower.includes('travel information')) return resolveFieldHelpText('details.travel_info', 'Example: Nearby hotels and transport details for out-of-town guests.');
    if (lower.includes('menu section heading')) return resolveFieldHelpText('menu.section_heading', 'Example: Wedding Menu.');
    if (lower.includes('select your rsvp meal type')) return resolveFieldHelpText('menu.meal_type', 'Choose between set menu and guest meal selections.');
    if (lower.includes('menu intro text')) return resolveFieldHelpText('menu.intro_text', 'Short intro above menu cards. Example: We cannot wait to share this meal with you.');
    if (lower.includes('menu notes card title')) return resolveFieldHelpText('menu.notes_title', 'Example: Dining Notes.');
    if (lower.includes('menu notes card text')) return resolveFieldHelpText('menu.notes_text', 'Example: Please include allergies and dietary needs in your RSVP.');
    if (lower.includes('course name')) return resolveFieldHelpText('menu.course_name', 'Example: Starter, Main, Dessert.');
    if (lower.includes('dish title')) return resolveFieldHelpText('menu.dish_title', 'Example: Pan Seared Seabass.');
    if (lower.includes('dish description')) return resolveFieldHelpText('menu.dish_description', 'Example: Served with whipped mash and tender-stem broccoli.');
    if (lower.includes('set menu description')) return resolveFieldHelpText('menu.set_menu_description', 'Shown when set menu mode is enabled. Example: A chef-curated menu will be served.');
    if (lower.includes('question')) return resolveFieldHelpText('faq.question', 'Example: Is there parking at the venue?');
    if (lower.includes('answer')) return resolveFieldHelpText('faq.answer', 'Example: Yes, there is free onsite parking available.');
    if (lower.includes('photo gallery heading')) return resolveFieldHelpText('gallery.heading', 'Example: Photo\'s of us across the years.');
    if (lower.includes('image horizontal focus point')) return resolveFieldHelpText('gallery.focus_x', 'Adjust horizontal crop focus for this gallery image.');
    if (lower.includes('image vertical focus point')) return resolveFieldHelpText('gallery.focus_y', 'Adjust vertical crop focus for this gallery image.');
    if (lower.includes('rsvp request title')) return resolveFieldHelpText('rsvp.title', 'Example: Ready to celebrate with us?');
    if (lower.includes('rsvp request button label')) return resolveFieldHelpText('rsvp.button_label', 'Example: Go to RSVP.');
    if (lower.includes('final rsvp request text')) return resolveFieldHelpText('rsvp.text', 'Example: Please RSVP using your invitation code.');
    if (lower.includes('website title')) return resolveFieldHelpText('site.website_title', 'Example: Kyle & Nicole\'s Wedding. This appears in account/site areas and identifies your wedding site.');
    if (lower.includes('party name')) return resolveFieldHelpText('party.name', 'Example: The Kane Party. This is what you and guests will see.');
    if (lower === 'email' || lower.includes('email (only required if sending via email)') || lower.includes('email address')) {
        return resolveFieldHelpText('party.email', 'Optional. Add an email only if you plan to send digital RSVP invitations.');
    }
    if (lower.includes('rsvp code')) return resolveFieldHelpText('party.rsvp_code', 'Short unique code printed on invitations. Guests use this code to RSVP.');
    if (lower.includes('additional notes for this party')) return resolveFieldHelpText('party.notes', 'Internal notes for your planning only (not shown publicly).');
    if (lower.includes('first name')) return resolveFieldHelpText('guest.first_name', 'Example: Emma. Add each invited person by name.');
    if (lower.includes('last name')) return resolveFieldHelpText('guest.last_name', 'Example: Kane.');
    if (lower.includes('is this a child?')) return resolveFieldHelpText('guest.is_child', 'Turn on if this guest is a child so your guest breakdown stays accurate.');
    if (lower.includes('max guests')) return resolveFieldHelpText('party.max_guests', 'Total seats available for this party. Keep this equal to your intended invite count.');

    return '';
}

function resolveFieldHelpText(key, fallback) {
    const custom = fieldHelpTexts?.[key];
    if (typeof custom === 'string' && custom.trim().length > 0) {
        return custom.trim();
    }

    return fallback;
}

function setMessage(message, durationMs = 3000) {
    globalMessage.value = message;
    clearError();
    window.setTimeout(() => {
        globalMessage.value = '';
    }, durationMs);
}

function setCreatePartyMessage(message, durationMs = 3000) {
    createPartyError.value = '';
    createPartyMessage.value = message;
    window.setTimeout(() => {
        createPartyMessage.value = '';
    }, durationMs);
}

function setCreatePartyError(message) {
    createPartyMessage.value = '';
    createPartyError.value = message;
}

function clearCreatePartyFeedback() {
    createPartyMessage.value = '';
    createPartyError.value = '';
    clearError();
}

function setError(message) {
    globalMessage.value = '';
    globalError.value = message;
}

function clearError() {
    globalError.value = '';
}

function extractErrorMessage(error, fallback) {
    const response = error?.response;

    if (!response) {
        return fallback;
    }

    if (response.data?.message) {
        return response.data.message;
    }

    const validationErrors = response.data?.errors;
    if (validationErrors && typeof validationErrors === 'object') {
        const firstField = Object.keys(validationErrors)[0];
        if (firstField && Array.isArray(validationErrors[firstField]) && validationErrors[firstField][0]) {
            return validationErrors[firstField][0];
        }
    }

    return fallback;
}

function formatDateTime(value) {
    if (!value) {
        return '';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    return parsed.toLocaleString();
}

function formatDate(value) {
    if (!value) {
        return '';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    return parsed.toLocaleDateString();
}

function captureSavedSnapshots() {
    lastSavedContentSnapshot.value = serialize(content.value);
    lastSavedRsvpSnapshot.value = serialize(rsvpSettings.value);
    lastSavedSiteTitle.value = siteTitle.value;
}

function serialize(value) {
    try {
        return JSON.stringify(value ?? {});
    } catch (error) {
        return '';
    }
}
</script>

<style scoped>
.admin-ui label {
    color: #0f1b1d;
    font-weight: 500;
}

.admin-shell {
    max-width: none;
    width: 100%;
    padding-left: 1rem;
    padding-right: 1rem;
}

.admin-main-shell {
    max-width: none;
    width: 100%;
    padding-left: 0;
    padding-right: 0;
}

@media (min-width: 768px) {
    .admin-shell,
    .admin-main-shell {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 1rem;
        padding-right: 1rem;
    }
}

.admin-btn {
    border: 1px solid #22363a;
    background-color: #22363a !important;
    color: #ffffff !important;
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

.admin-btn:hover {
    background-color: #466369 !important;
    border-color: #466369 !important;
    color: #ffffff !important;
}

.admin-btn-active {
    background: #f2ece3 !important;
    color: #0f1b1d !important;
    border-color: #22363a !important;
    border-bottom-width: 2px;
    border-bottom-color: #22363a !important;
    pointer-events: none;
    cursor: default;
}

.admin-btn-active:hover {
    background: #f2ece3 !important;
    border-color: #22363a !important;
    border-bottom-color: #22363a !important;
    color: #0f1b1d !important;
}

.admin-btn-success {
    border-color: #21c177 !important;
    background-color: #21c177 !important;
    color: #ffffff !important;
}

.admin-btn-success:hover {
    border-color: #1aa267 !important;
    background-color: #1aa267 !important;
}

.admin-btn-outline {
    border-color: #22363a !important;
    background-color: #22363a !important;
    color: #ffffff !important;
}

.admin-btn-outline:hover {
    border-color: #466369 !important;
    background-color: #466369 !important;
    color: #ffffff !important;
}

.admin-btn-view {
    border-color: #f2ece3 !important;
    background-color: #f2ece3 !important;
    color: #0f1b1d !important;
}

.admin-btn-view .btn-icon {
    color: #0f1b1d !important;
}

.admin-btn-view:hover {
    border-color: #f7f5f2 !important;
    background-color: #f7f5f2 !important;
    color: #0f1b1d !important;
}

.admin-btn-view:hover .btn-icon {
    color: #0f1b1d !important;
}

.admin-btn-danger {
    border: 1px solid #e66363;
    background-color: #e66363 !important;
    color: #ffffff !important;
}

.admin-btn-danger:hover {
    border-color: #b93f3f !important;
    background-color: #b93f3f !important;
    color: #ffffff !important;
}

.admin-btn-whatsapp {
    border-color: #21c177 !important;
    background-color: #ffffff !important;
    color: #21c177 !important;
}

.admin-btn-whatsapp:hover {
    border-color: #21c177 !important;
    background-color: #f7f7f7 !important;
    color: #21c177 !important;
}

.admin-btn-copy {
    border-color: #21c177 !important;
    background-color: #21c177 !important;
    color: #ffffff !important;
}

.admin-btn-copy:hover {
    border-color: #1aa267 !important;
    background-color: #1aa267 !important;
    color: #ffffff !important;
}

.admin-btn-copy-copied,
.admin-btn-copy-copied:hover {
    border-color: #21c177 !important;
    background-color: #21c177 !important;
    color: #ffffff !important;
    animation: copy-confirm-pulse 0.55s ease;
}

.admin-btn-email {
    border-color: #466369 !important;
    background-color: #466369 !important;
    color: #ffffff !important;
}

.admin-btn-email:hover {
    border-color: #22363a !important;
    background-color: #22363a !important;
    color: #ffffff !important;
}

.action-svg-icon {
    display: block;
    width: 1.25rem;
    height: 1.25rem;
    color: currentColor;
}

.actions-help-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
}

.actions-help-trigger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1rem;
    height: 1rem;
    color: #848484;
    border: 0;
    background: transparent;
    padding: 0;
    cursor: help;
}

.actions-help-trigger svg {
    width: 1rem;
    height: 1rem;
}

.actions-help-tooltip {
    position: absolute;
    z-index: 90;
    top: calc(100% + 0.7rem);
    right: 0;
    display: grid;
    width: min(18rem, 72vw);
    gap: 0.35rem;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 0.5rem;
    background: #0f1b1d;
    color: #ffffff;
    padding: 0.75rem;
    text-align: left;
    text-transform: none;
    letter-spacing: 0;
    font-size: 0.75rem;
    line-height: 1.35;
    font-weight: 400;
    box-shadow: 0 12px 28px rgba(15, 27, 29, 0.22);
    opacity: 0;
    pointer-events: none;
    transform: translateY(-0.25rem);
    transition: opacity 0.16s ease, transform 0.16s ease;
}

.actions-help-tooltip strong {
    color: #f2ece3;
    font-weight: 700;
}

.actions-help-wrap:hover .actions-help-tooltip,
.actions-help-tooltip.is-open {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}

@keyframes copy-confirm-pulse {
    0% {
        transform: scale(1);
    }
    45% {
        transform: scale(1.12);
    }
    100% {
        transform: scale(1);
    }
}

.admin-btn-danger-solid {
    border: 1px solid #e66363 !important;
    background-color: #e66363 !important;
    color: #ffffff !important;
}

.dashboard-stat-card {
    border: 1px solid rgba(15, 27, 29, 0.12);
    background: #ffffff;
    padding: 1.5rem;
    text-align: center;
}

.dashboard-card-footer {
    margin-top: auto;
    min-height: 6rem;
    display: flex;
    align-items: flex-end;
}

.dashboard-card-footer-inner {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.dashboard-card-footer-inner .admin-btn {
    margin-top: 24px;
}

.setup-checklist-btn {
    justify-content: center !important;
}

.site-tools-wrap {
    border-top: 1px solid rgba(15, 27, 29, 0.08);
    margin-top: 1.75rem;
    padding-top: 1.5rem;
}

.site-tools-grid {
    display: grid;
    gap: 0.75rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.admin-tool-btn {
    border: 1px solid rgba(15, 27, 29, 0.12);
    background: #f7f7f7 !important;
    color: #0f1b1d !important;
    box-shadow: none !important;
    justify-content: flex-start;
}

.admin-tool-btn .btn-icon {
    color: #22363a !important;
}

.admin-tool-btn:hover {
    border-color: #22363a !important;
    background: #ffffff !important;
    color: #0f1b1d !important;
}

.admin-tool-btn:hover .btn-icon {
    color: #22363a !important;
}

.admin-btn-danger-solid:hover {
    border-color: #b93f3f !important;
    background-color: #b93f3f !important;
    color: #ffffff !important;
}

.rsvp-status-icon {
    font-size: 18px;
    line-height: 1;
    vertical-align: middle;
}

.rsvp-status-icon-small {
    font-size: 14px;
    line-height: 1;
}

.admin-btn:disabled,
.admin-btn-danger:disabled,
.admin-btn-danger-solid:disabled,
.admin-btn-success:disabled,
.admin-btn-whatsapp:disabled,
.admin-btn-copy:disabled,
.admin-btn-email:disabled,
.admin-btn-outline:disabled {
    border-color: #848484 !important;
    background-color: #848484 !important;
    color: #ffffff !important;
    box-shadow: none !important;
    cursor: not-allowed !important;
    opacity: 1 !important;
}

.admin-btn:disabled .btn-icon,
.admin-btn-view:disabled .btn-icon,
.admin-btn-success:disabled .btn-icon,
.admin-btn-danger:disabled .btn-icon,
.admin-btn-copy:disabled .btn-icon,
.admin-btn-email:disabled .btn-icon {
    color: #ffffff !important;
}

.admin-btn:disabled:hover,
.admin-btn-view:disabled:hover,
.admin-btn-success:disabled:hover,
.admin-btn-danger:disabled:hover,
.admin-btn-whatsapp:disabled:hover,
.admin-btn-copy:disabled:hover,
.admin-btn-email:disabled:hover {
    border-color: #848484 !important;
    background-color: #848484 !important;
    color: #ffffff !important;
}

.disabled-action-tooltip {
    cursor: not-allowed;
}

.disabled-action-tooltip .admin-btn:disabled {
    pointer-events: none;
}

.content-section-block {
    border: 1px solid #22363a;
    padding: 1.5rem;
}

.content-section-block label {
    line-height: 1.4;
}

.content-section-block label.label-help {
    position: relative;
}

.your-guests-table .admin-btn {
    box-shadow: none !important;
}

.content-section-block .label-help-header {
    display: inline-flex;
    align-items: center;
    gap: 0;
    margin-bottom: 0.05rem;
}

.content-section-block .label-help-title-text {
    display: inline-block;
}

.content-section-block .label-help-icon {
    pointer-events: auto;
}

.content-section-block label > input,
.content-section-block label > select,
.content-section-block label > textarea,
.content-section-block label > .flex,
.content-section-block label > .grid,
.content-section-block label > .cms-rich,
.content-section-block label > .admin-rich-editor {
    margin-top: 0.9rem !important;
}

.content-section-block > label:not(:first-child) {
    margin-top: 1.1rem;
}

.content-section-block .grid > label {
    margin-top: 0.85rem;
}

.content-section-block .grid > label.gallery-action-label {
    margin-top: 0 !important;
}

.content-section-even {
    background: #f2ece3;
}

.content-section-odd {
    background: #ffffff;
}

.section-heading-with-badge {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 0.9rem;
}

.section-step-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.7rem;
    height: 1.7rem;
    border-radius: 9999px;
    background: #22363a;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.04em;
}

.section-toggle-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0.35rem 0 1.2rem;
}

.section-toggle-note {
    color: #848484;
    font-size: 0.82rem;
}

.section-toggle {
    position: relative;
    width: 2.6rem;
    height: 1.5rem;
    border-radius: 9999px;
    border: 2px solid #848484;
    background: #f7f7f7;
    transition: border-color 0.2s ease, background-color 0.2s ease;
}

.section-toggle-thumb {
    position: absolute;
    top: 50%;
    left: 0.15rem;
    transform: translateY(-50%);
    width: 1.05rem;
    height: 1.05rem;
    border-radius: 9999px;
    background: #848484;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    transition: left 0.2s ease, background-color 0.2s ease;
}

.section-toggle.is-active {
    border-color: #21c177;
    background: #21c177;
}

.section-toggle.is-active .section-toggle-thumb {
    left: calc(100% - 1.2rem);
    background: #ffffff;
    color: #21c177;
}

.section-toggle .material-symbols-outlined {
    font-size: 14px;
}

.file-input-field {
    background: #ffffff;
}

.file-input-field::file-selector-button {
    margin-right: 0.8rem;
}

.menu-courses-divider {
    margin: 2.6rem 0 2.2rem;
}

.guest-name-input {
    min-width: 0;
}

.guest-row-grid {
    align-items: center;
}

.guest-row-grid > .guest-option-control {
    margin-top: 0 !important;
}

.guest-row-input {
    height: 2.5rem;
    background-color: #ffffff !important;
}

.guest-row-button {
    height: 2.5rem;
    width: 100%;
}

.guest-option-control {
    display: inline-flex;
    align-items: center;
    justify-content: flex-start;
    gap: 0.5rem;
    width: 100%;
    height: 2.5rem;
    border: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 0;
    background: #ffffff;
    padding: 0 0.75rem;
    font-size: 0.82rem;
    font-weight: 500;
    letter-spacing: 0.02em;
    line-height: 1;
    color: #0f1b1d;
    white-space: nowrap;
    text-align: center;
}

.guest-option-checkbox {
    width: 1.1rem;
    height: 1.1rem;
    accent-color: #22363a;
    background-color: #ffffff !important;
    border: 1px solid #848484;
    border-radius: 0.2rem;
    margin: 0;
    flex: 0 0 auto;
}

.guest-row-grid input[type='text'],
.guest-row-grid input[type='email'],
.guest-row-grid input[type='number'] {
    background-color: #ffffff !important;
}

.party-modal-scroll,
.party-modal-guest-list {
    scrollbar-gutter: stable;
}

.party-modal-scroll::-webkit-scrollbar,
.party-modal-guest-list::-webkit-scrollbar {
    width: 12px;
}

.party-modal-scroll::-webkit-scrollbar-track,
.party-modal-guest-list::-webkit-scrollbar-track {
    background: #f2ece3;
    border-left: 1px solid rgba(34, 54, 58, 0.12);
}

.party-modal-scroll::-webkit-scrollbar-thumb,
.party-modal-guest-list::-webkit-scrollbar-thumb {
    background: #466369;
    border: 2px solid #f2ece3;
    border-radius: 9999px;
}

.party-modal-scroll::-webkit-scrollbar-thumb:hover,
.party-modal-guest-list::-webkit-scrollbar-thumb:hover {
    background: #22363a;
}

.party-modal-scroll {
    border-top: 1px solid rgba(34, 54, 58, 0.12);
    padding-top: 0.5rem;
}

.guest-option-control > .guest-option-checkbox {
    margin-top: 0 !important;
}

.theme-swatch-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    width: 1.85rem;
    height: 1.85rem;
    border: 1px solid rgba(15, 27, 29, 0.18);
    border-radius: 9999px;
    box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.4);
    transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
}

.theme-swatch-button:hover,
.theme-swatch-button:focus-visible {
    border-color: #22363a;
    box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.58), 0 8px 18px rgba(15, 27, 29, 0.12);
    outline: none;
    transform: translateY(-1px);
}

.theme-swatch-button.is-selected {
    border-color: #22363a;
    box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #22363a;
}

.timeline-grid {
    gap: 0.75rem;
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .timeline-grid {
        grid-template-columns: 1fr 1fr 3fr 110px 90px;
    }
}

input,
select,
textarea {
    background: #ffffff;
}

select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 20 20' fill='none'%3E%3Cpath d='M5.5 7.5L10 12L14.5 7.5' stroke='%230F1B1D' stroke-width='1.75' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: calc(100% - 15px) center;
    background-size: 16px;
    padding-right: 2.5rem;
}
</style>
