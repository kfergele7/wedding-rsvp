<template>
    <div class="admin-ui min-h-screen bg-wedding-bg">
        <header class="border-b border-soft bg-white/90">
            <div class="site-shell py-6">
                <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Wedding Admin</p>
                <h1 class="font-heading text-4xl">Content & Guest Management</h1>
            </div>
        </header>

        <div class="admin-sticky-nav sticky top-0 z-40 border-y border-soft bg-white/95 backdrop-blur">
            <div class="site-shell flex flex-wrap items-center justify-between gap-3 py-3">
                <nav class="flex flex-wrap gap-2">
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

                <div class="flex flex-wrap items-center gap-2 border-l border-soft pl-4">
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

        <main class="site-shell py-10">
            <section v-if="section === 'dashboard'" class="grid gap-4 md:grid-cols-3 xl:grid-cols-5">
                <article v-for="card in dashboardCards" :key="card.label" class="card-frame bg-white text-center">
                    <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">{{ card.label }}</p>
                    <p class="mt-2 font-heading text-4xl">{{ card.value }}</p>
                </article>
            </section>

            <section v-if="section === 'content'" class="space-y-8 pb-32">
                <article class="card-frame bg-white">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-heading text-3xl">Content</h2>
                    </div>
                    <p class="mt-2 text-wedding-muted">Update text, imagery, and colours shown on your single-page wedding website.</p>

                    <div v-if="content" class="mt-8 space-y-6">
                        <div class="content-section-block content-section-even">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">1</span>
                                <span class="font-heading text-3xl">Theme Colours</span>
                            </h3>
                            <p class="mt-2 text-sm text-wedding-muted">Primary section colour applies to The Big Day and Ready to Celebrate sections. Button colour applies to all dark buttons (hero button excluded).</p>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Primary Section Colour
                                    <div class="mt-2 flex items-center gap-3">
                                        <input v-model="content.theme.primary_color" type="color" class="h-12 w-16 border border-soft bg-white p-1">
                                        <input v-model="content.theme.primary_color" class="h-12 w-full border border-soft px-4 py-3 uppercase" placeholder="#22363A">
                                    </div>
                                </label>
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Button Colour
                                    <div class="mt-2 flex items-center gap-3">
                                        <input v-model="content.theme.button_color" type="color" class="h-12 w-16 border border-soft bg-white p-1">
                                        <input v-model="content.theme.button_color" class="h-12 w-full border border-soft px-4 py-3 uppercase" placeholder="#22363A">
                                    </div>
                                </label>
                            </div>
                            <p class="mt-3 text-xs font-semibold text-wedding-muted">Use dark colours to maintain white text contrast.</p>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <div class="content-section-block content-section-odd">
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
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Focus X: {{ content.hero.imageFocusX }}%
                                <input v-model.number="content.hero.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Focus Y: {{ content.hero.imageFocusY }}%
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
                        <div class="content-section-block content-section-even">
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
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Focus X: {{ content.welcome.imageFocusX }}%
                                <input v-model.number="content.welcome.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Focus Y: {{ content.welcome.imageFocusY }}%
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
                        <div class="content-section-block content-section-odd mt-2">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <h3 class="section-heading-with-badge">
                                    <span class="section-step-badge">4</span>
                                    <span class="font-heading text-3xl">Timeline</span>
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
                                {{ isTimelineAtMax ? 'Maximum of 5 timeline items reached.' : 'Minimum of 2 timeline items is required.' }}
                            </p>

                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="(item, index) in content.timeline.items"
                                    :id="`timeline-item-${index}`"
                                    :key="index"
                                    class="grid gap-3 border border-soft bg-wedding-bg p-3 md:grid-cols-[1fr_1fr_3fr_auto]"
                                    @dragover.prevent="onDragOver('timeline', index)"
                                    @drop="onDrop('timeline', index)"
                                >
                                    <input v-model="item.time" placeholder="Time" class="border border-soft bg-white px-3 py-2">
                                    <input v-model="item.title" placeholder="Title" class="border border-soft bg-white px-3 py-2">
                                    <input v-model="item.description" placeholder="Description" class="w-full border border-soft bg-white px-3 py-2">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            class="admin-btn drag-handle inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                            draggable="true"
                                            @dragstart="onDragStart('timeline', index)"
                                            @dragend="onDragEnd"
                                        >
                                            <span class="material-symbols-outlined btn-icon">drag_indicator</span>
                                            Move
                                        </button>
                                        <button type="button" class="admin-btn admin-btn-danger inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]" :disabled="isTimelineAtMin" @click="removeTimelineItem(index)">
                                            <span class="material-symbols-outlined btn-icon">{{ isTimelineAtMin ? 'block' : 'close' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <div class="content-section-block content-section-even">
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
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Story Focus X: {{ content.story.imageFocusX }}%
                                <input v-model.number="content.story.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Story Focus Y: {{ content.story.imageFocusY }}%
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
                        <div class="content-section-block content-section-odd">
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

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Blurb</label>
                        <RichTextEditor v-model="content.details.venue.blurb" class="mt-2" tone="secondary" />

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Venue Image
                            <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="file-input-field mt-2 w-full border border-soft bg-white px-4 py-3" @change="uploadContentImage($event, 'details.image')">
                        </label>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Focus X: {{ content.details.imageFocusX }}%
                                <input v-model.number="content.details.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Focus Y: {{ content.details.imageFocusY }}%
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

                        <div class="content-section-block content-section-even">
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

                        <div class="content-section-block content-section-odd">
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
                                        <option value="options">Guests choose meal options</option>
                                    </select>
                                </label>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Intro Text</label>
                                <RichTextEditor v-model="rsvpSettings.menu_intro" class="mt-2" tone="secondary" />
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-3">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted md:col-span-1">Menu Notes Card Title
                                    <input v-model="rsvpSettings.menu_note_title" class="mt-2 w-full border border-soft bg-white px-4 py-3" placeholder="Dining Notes">
                                </label>
                                <div class="md:col-span-2">
                                    <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Notes Card Text</label>
                                    <RichTextEditor v-model="rsvpSettings.menu_note_text" class="mt-2" tone="secondary" />
                                </div>
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
                                    @dragover.prevent="onDragOver('courses', courseIndex)"
                                    @drop="onDrop('courses', courseIndex)"
                                >
                                    <div class="grid gap-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Course Name</label>
                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    class="admin-btn drag-handle inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                    draggable="true"
                                                    @dragstart="onDragStart('courses', courseIndex)"
                                                    @dragend="onDragEnd"
                                                >
                                                    <span class="material-symbols-outlined btn-icon">drag_indicator</span>
                                                    Move
                                                </button>
                                                <button type="button" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeMenuCourse(courseIndex)">
                                                    <span class="material-symbols-outlined btn-icon">close</span>
                                                    Remove Course
                                                </button>
                                            </div>
                                        </div>
                                        <input v-model="course.name" class="w-full border border-soft bg-white px-4 py-3" placeholder="Starter">
                                    </div>

                                    <div class="mb-4 mt-5 flex items-center justify-between">
                                        <h4 class="font-heading text-xl">{{ course.name || `Course ${courseIndex + 1}` }}</h4>
                                        <button
                                            type="button"
                                            class="admin-btn admin-btn-success inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                            :disabled="!canAddMenuOptions"
                                            @click="addMenuCourseItem(courseIndex)"
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
                                    <p v-if="courseIndex === 1" class="mt-3 text-xs text-wedding-muted">
                                        Main-like course titles are used automatically as RSVP meal choices when guests choose options.
                                    </p>
                                </div>
                            </div>

                            <p v-if="!canAddMenuOptions" class="mt-4 text-xs text-wedding-muted">
                                Set menu allows one option per course. Switch to "Guests choose meal options" to add extra options within a course.
                            </p>

                            <div v-if="rsvpSettings.meal_mode === 'set_menu'" class="mt-4">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Set Menu Description</label>
                                <RichTextEditor v-model="rsvpSettings.set_menu_description" class="mt-2" tone="secondary" />
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div class="content-section-block content-section-even">
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
                                    class="rounded border border-soft bg-wedding-bg p-3"
                                    @dragover.prevent="onDragOver('faqs', index)"
                                    @drop="onDrop('faqs', index)"
                                >
                                    <div class="grid gap-3 md:grid-cols-3">
                                        <input v-model="faq.question" placeholder="Question" class="border border-soft bg-white px-3 py-2 md:col-span-1">
                                        <div class="md:col-span-2">
                                            <RichTextEditor v-model="faq.answer" class="mt-2" tone="secondary" surface="white" button-tone="primary" />
                                        </div>
                                    </div>
                                    <div class="mt-3 flex justify-end">
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="admin-btn drag-handle inline-flex items-center justify-center px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                                draggable="true"
                                                @dragstart="onDragStart('faqs', index)"
                                                @dragend="onDragEnd"
                                            >
                                                <span class="material-symbols-outlined btn-icon">drag_indicator</span>
                                                Move
                                            </button>
                                            <button type="button" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeFaqItem(index)">
                                                <span class="material-symbols-outlined btn-icon">close</span>
                                                Remove FAQ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-8">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div class="content-section-block content-section-odd">
                            <h3 class="section-heading-with-badge">
                                <span class="section-step-badge">10</span>
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

            <section v-if="section === 'parties'" class="grid items-start gap-8 xl:grid-cols-[1.1fr_0.9fr]">
                <article class="card-frame bg-white">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-heading text-3xl">Households</h2>
                        <div class="flex flex-wrap gap-2">
                            <a class="admin-btn border border-soft px-4 py-2 text-xs uppercase tracking-[0.12em]" :href="partiesExportUrl">Export CSV</a>
                            <label class="admin-btn border border-soft px-4 py-2 text-xs uppercase tracking-[0.12em]">
                                Import CSV
                                <input type="file" class="hidden" accept=".csv" @change="importParties">
                            </label>
                        </div>
                    </div>

                    <h3 class="font-heading text-2xl">Create Household</h3>
                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Display Name</label>
                            <input v-model="newParty.display_name" placeholder="Display name" class="w-full border border-soft px-4 py-3">
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
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Max Guests</label>
                            <input v-model.number="newParty.max_guests" type="number" min="1" max="20" placeholder="Max guests" class="w-full border border-soft px-4 py-3">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Notes</label>
                            <input v-model="newParty.notes" placeholder="Notes" class="w-full border border-soft px-4 py-3">
                        </div>
                    </div>
                    <button class="admin-btn admin-btn-success mt-4 inline-flex items-center gap-2" type="button" @click="createParty">
                        <span class="material-symbols-outlined btn-icon">add</span>
                        Create Household
                    </button>
                    <p v-if="globalMessage" class="mt-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ globalMessage }}
                    </p>
                    <p v-if="globalError" class="mt-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ globalError }}
                    </p>

                    <hr class="my-6 border-t-2 border-wedding-band">

                    <div class="mb-3 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                        <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                            Search Households
                            <input
                                v-model="partySearchTerm"
                                type="text"
                                placeholder="Search by party name, code, or guest"
                                class="mt-1 w-full border border-soft px-4 py-3 normal-case tracking-normal text-wedding-text"
                            >
                        </label>
                        <p class="text-sm text-wedding-muted">Showing {{ filteredParties.length }} of {{ parties.length }}</p>
                    </div>

                    <div class="max-h-[620px] overflow-x-auto overflow-y-auto border border-soft/60">
                        <table class="min-w-full text-left text-sm">
                            <thead class="sticky top-0 bg-white">
                                <tr class="border-b border-soft text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                    <th class="px-3 py-2">Party</th>
                                    <th class="px-3 py-2">Code</th>
                                    <th class="px-3 py-2">Guests</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="partyItem in filteredParties"
                                    :key="partyItem.id"
                                    class="cursor-pointer border-b border-soft/60"
                                    :class="selectedParty?.id === partyItem.id ? 'bg-wedding-bg' : ''"
                                    @click="selectParty(partyItem.id)"
                                >
                                    <td class="px-3 py-2">{{ partyItem.display_name }}</td>
                                    <td class="px-3 py-2 uppercase">{{ partyItem.code }}</td>
                                    <td class="px-3 py-2">{{ partyItem.guests.length }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="card-frame bg-white">
                    <template v-if="selectedParty">
                        <h3 class="font-heading text-3xl">Edit {{ selectedParty.display_name }}</h3>
                        <div class="mt-4 grid gap-3">
                            <div>
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Display Name</label>
                                <input v-model="selectedParty.display_name" class="w-full border border-soft px-4 py-3">
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
                            <div>
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Notes</label>
                                <textarea v-model="selectedParty.notes" rows="3" class="w-full border border-soft px-4 py-3" placeholder="Notes"></textarea>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button class="admin-btn inline-flex items-center gap-2" type="button" @click="updateParty">
                                <span class="material-symbols-outlined btn-icon">save</span>
                                Save Party
                            </button>
                            <button class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="deleteParty">
                                <span class="material-symbols-outlined btn-icon">close</span>
                                Remove Household
                            </button>
                        </div>

                        <h4 class="mt-8 font-heading text-2xl">Guests</h4>
                        <div class="mt-3 space-y-2">
                            <div v-for="guest in selectedParty.guests" :key="guest.id" class="grid gap-2 border border-soft p-3 md:grid-cols-[1fr_1fr_auto_auto]">
                                <input v-model="guest.first_name" class="border border-soft px-3 py-2">
                                <input v-model="guest.last_name" class="border border-soft px-3 py-2">
                                <label class="inline-flex items-center gap-2 px-2 text-sm">
                                    <input v-model="guest.is_child" type="checkbox">
                                    Child
                                </label>
                                <div class="flex gap-2">
                                    <button class="admin-btn inline-flex items-center gap-1 px-3 text-xs" type="button" @click="updateGuest(guest)">
                                        <span class="material-symbols-outlined btn-icon">save</span>
                                        Save
                                    </button>
                                    <button class="admin-btn admin-btn-danger inline-flex items-center gap-1 px-3 text-xs" type="button" @click="deleteGuest(guest)">
                                        <span class="material-symbols-outlined btn-icon">close</span>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid items-end gap-2 md:grid-cols-4">
                            <div>
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">First Name</label>
                                <input v-model="newGuest.first_name" placeholder="First name" class="w-full border border-soft px-3 py-2">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs uppercase tracking-[0.12em] text-wedding-muted">Last Name</label>
                                <input v-model="newGuest.last_name" placeholder="Last name" class="w-full border border-soft px-3 py-2">
                            </div>
                            <label class="inline-flex h-12 items-center gap-2 border border-soft bg-white px-3 py-2 text-sm">
                                <input v-model="newGuest.is_child" type="checkbox">
                                Child
                            </label>
                            <button class="admin-btn admin-btn-success h-12 inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]" type="button" @click="addGuest">
                                <span class="material-symbols-outlined btn-icon">person_add</span>
                                Add Guest
                            </button>
                        </div>
                        <p v-if="globalMessage" class="mt-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ globalMessage }}
                        </p>
                        <p v-if="globalError" class="mt-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ globalError }}
                        </p>
                    </template>
                    <p v-else class="text-wedding-muted">Select a household to edit party and guests.</p>
                </article>
            </section>

            <section v-if="section === 'rsvps'" class="card-frame bg-white">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-heading text-3xl">RSVP Responses</h2>
                    <a class="admin-btn border border-soft px-4 py-2 text-xs uppercase tracking-[0.12em]" :href="rsvpsExportUrl">Export RSVP CSV</a>
                </div>

                <div class="mb-4 grid gap-3 md:grid-cols-3">
                    <label class="text-xs uppercase tracking-[0.12em] text-wedding-muted md:col-span-3">
                        Search RSVP Responses
                        <input
                            v-model="rsvpSearchTerm"
                            type="text"
                            placeholder="Search by household name or code"
                            class="mt-1 w-full border border-soft bg-white px-3 py-2 text-sm normal-case tracking-normal text-wedding-text"
                        >
                    </label>
                    <label class="text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        Response Filter
                        <select v-model="rsvpResponseFilter" class="mt-1 w-full border border-soft bg-white px-3 py-2 text-sm normal-case tracking-normal text-wedding-text">
                            <option value="all">All Households</option>
                            <option value="responded">Responded</option>
                            <option value="no_response">No Response</option>
                        </select>
                    </label>
                    <label class="text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        Status Filter
                        <select v-model="rsvpStatusFilter" class="mt-1 w-full border border-soft bg-white px-3 py-2 text-sm normal-case tracking-normal text-wedding-text">
                            <option value="all">All Statuses</option>
                            <option value="attending">Attending</option>
                            <option value="not_attending">Not Attending</option>
                        </select>
                    </label>
                    <div class="flex items-end text-sm text-wedding-muted">
                        Showing {{ filteredRsvpRows.length }} of {{ rsvpRows.length }} households
                    </div>
                </div>

                <div class="space-y-3">
                    <article v-for="row in filteredRsvpRows" :key="row.party_id" class="border border-soft p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="font-heading text-2xl">{{ row.party_name }}</h3>
                                <p class="text-sm text-wedding-muted uppercase">Code: {{ row.code }}</p>
                            </div>

                            <button
                                class="admin-btn inline-flex items-center gap-2 px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                :class="!row.rsvp?.status ? 'admin-btn-success' : ''"
                                type="button"
                                @click="editRsvp(row)"
                            >
                                <span class="material-symbols-outlined btn-icon">{{ row.rsvp?.status ? 'edit' : 'add' }}</span>
                                {{ row.rsvp?.status ? 'Edit RSVP' : 'Add RSVP' }}
                            </button>
                        </div>

                        <p class="mt-2 text-wedding-muted">
                            Status:
                            <span
                                class="ml-1 inline-flex items-center rounded-full border px-2 py-1 text-xs font-medium uppercase tracking-[0.08em]"
                                :class="{
                                    'border-emerald-200 bg-emerald-50 text-emerald-700': row.rsvp?.status === 'attending',
                                    'border-red-200 bg-red-50 text-red-700': row.rsvp?.status === 'not_attending',
                                    'border-amber-200 bg-amber-50 text-amber-700': !row.rsvp?.status,
                                }"
                            >
                                {{ row.rsvp ? formatStatus(row.rsvp.status) : 'No Response' }}
                            </span>
                            <span class="ml-2">· Attending: {{ row.rsvp?.attending_count || 0 }} / {{ row.max_guests }}</span>
                        </p>
                    </article>
                    <p v-if="filteredRsvpRows.length === 0" class="border border-soft bg-wedding-bg px-4 py-3 text-sm text-wedding-muted">
                        No households match the selected filters.
                    </p>
                </div>

            </section>
        </main>

        <div v-if="editingRsvp" class="fixed inset-0 z-[70] bg-black/40 p-4 md:p-8" @click.self="closeRsvpModal">
            <div class="mx-auto mt-6 w-full max-w-3xl border border-soft bg-white p-6 shadow-soft md:mt-16 md:p-8">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="font-heading text-3xl">{{ editingRsvp.rsvp?.status ? 'Update RSVP' : 'Add RSVP' }} · {{ editingRsvp.party_name }}</h3>
                    <button class="admin-btn admin-btn-danger inline-flex items-center gap-1 px-3 py-2 text-xs uppercase tracking-[0.12em]" type="button" @click="closeRsvpModal">
                        <span class="material-symbols-outlined btn-icon">close</span>
                        Close
                    </button>
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

        <div v-if="confirmModal.open" class="fixed inset-0 z-[90] bg-black/40 p-4" @click.self="closeConfirmModal(false)">
            <div class="mx-auto mt-20 w-full max-w-lg border border-soft bg-white p-6 shadow-soft">
                <h3 class="font-heading text-3xl">{{ confirmModal.title }}</h3>
                <p class="mt-3 text-wedding-muted">{{ confirmModal.message }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="admin-btn inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]" @click="closeConfirmModal(false)">
                        <span class="material-symbols-outlined btn-icon">close</span>
                        Cancel
                    </button>
                    <button type="button" class="admin-btn admin-btn-danger inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]" @click="closeConfirmModal(true)">
                        <span class="material-symbols-outlined btn-icon">close</span>
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        <div v-if="noticeModal.open" class="fixed inset-0 z-[85] bg-black/40 p-4" @click.self="closeNoticeModal">
            <div class="mx-auto mt-24 w-full max-w-lg border border-soft bg-white p-6 shadow-soft">
                <h3 class="font-heading text-3xl">{{ noticeModal.title }}</h3>
                <p class="mt-3 text-wedding-muted">{{ noticeModal.message }}</p>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="admin-btn inline-flex items-center gap-2 px-4 py-2 text-xs uppercase tracking-[0.12em]" @click="closeNoticeModal">
                        <span class="material-symbols-outlined btn-icon">check</span>
                        OK
                    </button>
                </div>
            </div>
        </div>

        <div v-if="section === 'content'" class="fixed bottom-0 left-0 right-0 z-50">
            <div class="w-full border-t border-soft bg-white/95 px-4 py-3 shadow-soft backdrop-blur md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-4">
                        <p class="text-sm text-wedding-muted">
                            <span class="uppercase tracking-[0.12em]">Last Saved:</span>
                            <span class="ml-2">{{ lastSavedAt || 'Not yet saved' }}</span>
                        </p>
                        <span class="hidden h-5 w-px bg-soft md:inline-block"></span>
                        <p class="text-sm">
                            <span class="uppercase tracking-[0.12em] text-wedding-muted">Current Changes:</span>
                            <span class="ml-2 font-medium" :class="hasUnsavedChanges ? 'text-red-700' : 'text-emerald-700'">
                                {{ hasUnsavedChanges ? 'Unsaved' : 'Saved' }}
                            </span>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <p v-if="globalMessage" class="rounded border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700">
                            {{ globalMessage }}
                        </p>
                        <p v-if="globalError" class="rounded border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
                            {{ globalError }}
                        </p>
                        <a
                            v-if="previewUrl"
                            :href="previewUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="admin-btn inline-flex items-center justify-center gap-2 border px-8 py-4 text-xs uppercase tracking-[0.2em]"
                        >
                            <span class="material-symbols-outlined btn-icon">visibility</span>
                            Open Preview
                        </a>
                        <button class="admin-btn button-dark admin-btn-success inline-flex items-center gap-2" type="button" @click="saveContent"><span class="material-symbols-outlined btn-icon">save</span>Save Content</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue';
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
const adminBaseUrl = props.payload?.adminBaseUrl || '/admin';
const apiBaseUrl = props.payload?.adminApiBaseUrl || '/admin/api';
const previewUrl = props.payload?.previewUrl || '';
const globalMessage = ref('');
const globalError = ref('');
const lastSavedAt = ref('');
const lastSavedContentSnapshot = ref('');
const lastSavedRsvpSnapshot = ref('');
const timelineMinItems = 2;
const timelineMaxItems = 5;
const confirmResolve = ref(null);
const confirmModal = reactive({
    open: false,
    title: '',
    message: '',
});
const noticeModal = reactive({
    open: false,
    title: '',
    message: '',
});
const dragState = reactive({
    type: '',
    fromIndex: -1,
});

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
    menu_courses: defaultMenuCourses.map((course) => ({
        ...course,
        items: course.items.map((item) => ({ ...item })),
    })),
});
const parties = ref([]);
const partySearchTerm = ref('');
const selectedPartyId = ref(null);
const rsvpRows = ref([]);
const rsvpResponseFilter = ref('all');
const rsvpStatusFilter = ref('all');
const rsvpSearchTerm = ref('');
const editingRsvp = ref(null);

const rsvpForm = reactive({
    status: 'attending',
    attending_count: 0,
    meal_choices: [],
    dietary_restrictions: '',
    message: '',
});

const newParty = reactive({
    display_name: '',
    code: '',
    max_guests: 2,
    notes: '',
});

const newGuest = reactive({
    first_name: '',
    last_name: '',
    is_child: false,
});

const selectedParty = computed(() => parties.value.find((party) => party.id === selectedPartyId.value) || null);
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
const hasUnsavedChanges = computed(() => {
    if (!content.value) {
        return false;
    }

    return serialize(content.value) !== lastSavedContentSnapshot.value
        || serialize(rsvpSettings.value) !== lastSavedRsvpSnapshot.value;
});
const isTimelineAtMax = computed(() => (content.value?.timeline?.items?.length || 0) >= timelineMaxItems);
const isTimelineAtMin = computed(() => (content.value?.timeline?.items?.length || 0) <= timelineMinItems);
const canAddMenuOptions = computed(() => rsvpSettings.value?.meal_mode === 'options');
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

        if (rsvpResponseFilter.value === 'responded' && !row.rsvp?.status) {
            return false;
        }
        if (rsvpResponseFilter.value === 'no_response' && row.rsvp?.status) {
            return false;
        }
        if (rsvpStatusFilter.value !== 'all') {
            if (!row.rsvp?.status) {
                return false;
            }
            if (row.rsvp.status !== rsvpStatusFilter.value) {
                return false;
            }
        }
        return true;
    }),
);

const dashboardCards = computed(() => [
    { label: 'Total Households', value: stats.value.total_households },
    { label: 'Invited Guests', value: stats.value.invited_guests },
    { label: 'Attending', value: stats.value.attending },
    { label: 'Not Attending', value: stats.value.not_attending },
    { label: 'No Response', value: stats.value.no_response },
]);

const navItems = [
    { key: 'dashboard', label: 'Dashboard', href: adminBaseUrl, icon: 'dashboard' },
    { key: 'parties', label: 'Households', href: `${adminBaseUrl}/parties`, icon: 'groups' },
    { key: 'rsvps', label: 'RSVPs', href: `${adminBaseUrl}/rsvps`, icon: 'event_note' },
    { key: 'content', label: 'Content', href: `${adminBaseUrl}/content`, icon: 'edit_note' },
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
        content.value = response.data.content;
        ensureImageFocusDefaults();
        ensureSectionVisibilityDefaults();
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
        parties.value = response.data.parties;
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load households.'));
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

function formatStatus(status) {
    return status === 'attending' ? 'Attending' : 'Not Attending';
}

function addTimelineItem() {
    if (!content.value) {
        return;
    }
    if (isTimelineAtMax.value) {
        openNoticeModal('Timeline limit reached', 'You can only have a maximum of 5 timeline items in this section.');
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

async function saveContent() {
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
        await nextTick();
    }

    clearError();
    if (!content.value?.hero?.names?.trim()) {
        setError('Couple names are required.');
        return;
    }

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
        if (response.data?.rsvp_settings) {
            rsvpSettings.value = {
                ...response.data.rsvp_settings,
                menu_courses: normalizeMenuCourses(response.data.rsvp_settings?.menu_courses),
            };
        }
        lastSavedAt.value = formatDateTime(response.data?.last_saved_at) || new Date().toLocaleString();
        captureSavedSnapshots();
        setMessage(response.data?.message || 'Content updated.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not save content.'));
    }
}

function addMenuCourse() {
    if (!Array.isArray(rsvpSettings.value.menu_courses)) {
        rsvpSettings.value.menu_courses = [];
    }

    rsvpSettings.value.menu_courses.push({
        id: `course-${Date.now()}`,
        name: `Course ${rsvpSettings.value.menu_courses.length + 1}`,
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

function addMenuCourseItem(courseIndex) {
    if (!canAddMenuOptions.value) {
        openNoticeModal(
            'Set menu active',
            'Switch to "Guests choose meal options" to add multiple options within a course.'
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

    if (!content.value.theme.primary_color) {
        content.value.theme.primary_color = '#22363A';
    }

    if (!content.value.theme.button_color) {
        content.value.theme.button_color = '#22363A';
    }

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

        content.value = response.data.content;
        ensureImageFocusDefaults();
        setMessage(response.data.message || 'Image updated.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not upload image.'));
    } finally {
        event.target.value = '';
    }
}

async function createParty() {
    clearError();

    if (!newParty.display_name || newParty.display_name.trim() === '') {
        setError('Party name is required.');
        return;
    }

    if (newParty.max_guests < 1 || newParty.max_guests > 20) {
        setError('Max guests must be between 1 and 20.');
        return;
    }

    if (newParty.code?.trim() && !/^[A-Za-z]{3,10}$/.test(newParty.code.trim())) {
        setError('RSVP code must be 3-10 letters.');
        return;
    }

    try {
        await window.axios.post(`${apiBaseUrl}/parties`, newParty);
        newParty.display_name = '';
        newParty.max_guests = 2;
        newParty.notes = '';
        await generateCodeForCreate(false);
        await loadParties();
        await loadStats();
        setMessage('Household created successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not create household.'));
    }
}

async function updateParty() {
    clearError();
    if (!selectedParty.value) {
        setError('Select a household to update.');
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
        setMessage('Household updated successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not update household.'));
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
        setError('Select a household to generate a code.');
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
        setError('Select a household to delete.');
        return;
    }

    const confirmed = await openConfirmModal(
        'Remove Household',
        'Are you sure you want to remove this household and all guests/RSVP data?'
    );
    if (!confirmed) {
        return;
    }

    try {
        await window.axios.delete(`${apiBaseUrl}/parties/${selectedParty.value.id}`);
        selectedPartyId.value = null;
        await Promise.all([loadParties(), loadStats(), loadRsvps()]);
        if (parties.value.length > 0) {
            selectedPartyId.value = parties.value[0].id;
        }
        setMessage('Household deleted successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not delete household.'));
    }
}

async function addGuest() {
    clearError();
    if (!selectedParty.value) {
        setError('Select a household before adding a guest.');
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
        await Promise.all([loadParties(), loadStats()]);
        setMessage('Guest added successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not add guest.'));
    }
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

async function deleteGuest(guest) {
    clearError();
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

function openConfirmModal(title, message) {
    confirmModal.title = title;
    confirmModal.message = message;
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

function openNoticeModal(title, message) {
    noticeModal.title = title;
    noticeModal.message = message;
    noticeModal.open = true;
}

function closeNoticeModal() {
    noticeModal.open = false;
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

function onDragStart(type, index) {
    dragState.type = type;
    dragState.fromIndex = index;
}

function onDragOver(type, index) {
    if (dragState.type !== type || dragState.fromIndex === index) {
        return;
    }
}

function onDrop(type, toIndex) {
    if (dragState.type !== type || dragState.fromIndex < 0 || dragState.fromIndex === toIndex) {
        onDragEnd();
        return;
    }

    if (type === 'timeline' && content.value?.timeline?.items) {
        moveInArray(content.value.timeline.items, dragState.fromIndex, toIndex);
    }

    if (type === 'courses' && Array.isArray(rsvpSettings.value?.menu_courses)) {
        moveInArray(rsvpSettings.value.menu_courses, dragState.fromIndex, toIndex);
    }

    if (type === 'faqs' && content.value?.details?.faqs) {
        moveInArray(content.value.details.faqs, dragState.fromIndex, toIndex);
    }

    onDragEnd();
}

function onDragEnd() {
    dragState.type = '';
    dragState.fromIndex = -1;
}

function moveInArray(list, fromIndex, toIndex) {
    if (!Array.isArray(list) || fromIndex < 0 || toIndex < 0 || fromIndex >= list.length || toIndex >= list.length) {
        return;
    }

    const [moved] = list.splice(fromIndex, 1);
    list.splice(toIndex, 0, moved);
}

function setMessage(message) {
    globalMessage.value = message;
    clearError();
    window.setTimeout(() => {
        globalMessage.value = '';
    }, 3000);
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

function captureSavedSnapshots() {
    lastSavedContentSnapshot.value = serialize(content.value);
    lastSavedRsvpSnapshot.value = serialize(rsvpSettings.value);
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

.admin-btn-danger-solid {
    border: 1px solid #e66363 !important;
    background-color: #e66363 !important;
    color: #ffffff !important;
}

.admin-btn-danger-solid:hover {
    border-color: #b93f3f !important;
    background-color: #b93f3f !important;
    color: #ffffff !important;
}

.admin-btn:disabled,
.admin-btn-danger:disabled,
.admin-btn-danger-solid:disabled,
.admin-btn-success:disabled,
.admin-btn-outline:disabled {
    border-color: #848484 !important;
    background-color: #848484 !important;
    color: #ffffff !important;
    box-shadow: none !important;
    cursor: not-allowed !important;
    opacity: 1 !important;
}

.content-section-block {
    border: 1px solid #22363a;
    padding: 1.5rem;
}

.content-section-block label {
    line-height: 1.4;
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

.drag-handle {
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
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
