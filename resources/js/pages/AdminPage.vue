<template>
    <div class="admin-ui min-h-screen bg-wedding-bg">
        <header class="border-b border-soft bg-white/90">
            <div class="site-shell flex flex-col gap-4 py-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Wedding Admin</p>
                    <h1 class="font-heading text-4xl">Content & Guest Management</h1>
                </div>

                <form :action="logoutUrl" method="POST">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <button class="admin-btn button-dark inline-flex px-5 py-3 text-xs uppercase tracking-[0.16em]" type="submit">Logout</button>
                </form>
            </div>

            <nav class="site-shell flex flex-wrap gap-2 pb-6">
                <a
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="admin-btn border px-4 py-2 text-xs uppercase tracking-[0.15em]"
                    :class="section === item.key ? 'border-wedding-band bg-wedding-band text-white' : 'border-soft bg-white'"
                >
                    {{ item.label }}
                </a>
            </nav>
        </header>

        <main class="site-shell py-10">
            <section v-if="section === 'dashboard'" class="grid gap-4 md:grid-cols-3 xl:grid-cols-5">
                <article v-for="card in dashboardCards" :key="card.label" class="card-frame bg-white text-center">
                    <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">{{ card.label }}</p>
                    <p class="mt-2 font-heading text-4xl">{{ card.value }}</p>
                </article>
            </section>

            <section v-if="section === 'content'" class="space-y-8 pb-32">
                <article class="card-frame bg-white">
                    <h2 class="font-heading text-3xl">Content</h2>
                    <p class="mt-2 text-wedding-muted">Update text, imagery, and colors shown on your single-page wedding website.</p>

                    <div v-if="content" class="mt-8 space-y-10">
                        <div class="border border-soft bg-wedding-bg/50 p-5">
                            <h3 class="font-heading text-3xl">Theme Colors</h3>
                            <p class="mt-2 text-sm text-wedding-muted">Primary section color applies to The Big Day and Ready to Celebrate sections. Button color applies to all dark buttons (hero button excluded).</p>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Primary Section Color
                                    <div class="mt-2 flex items-center gap-3">
                                        <input v-model="content.theme.primary_color" type="color" class="h-12 w-16 border border-soft bg-white p-1">
                                        <input v-model="content.theme.primary_color" class="h-12 w-full border border-soft px-4 py-3 uppercase" placeholder="#22363A">
                                    </div>
                                </label>
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Button Color
                                    <div class="mt-2 flex items-center gap-3">
                                        <input v-model="content.theme.button_color" type="color" class="h-12 w-16 border border-soft bg-white p-1">
                                        <input v-model="content.theme.button_color" class="h-12 w-full border border-soft px-4 py-3 uppercase" placeholder="#22363A">
                                    </div>
                                </label>
                            </div>
                            <p class="mt-3 text-xs text-wedding-muted">Use dark colors to maintain white text contrast.</p>
                        </div>

                        <div class="w-full py-10">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <h3 class="font-heading text-3xl">Hero</h3>
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

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Background Image Path
                                <input v-model="content.hero.image" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Hero Image
                                <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="mt-2 w-full border border-soft px-4 py-3" @change="uploadContentImage($event, 'hero.image')">
                            </label>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Focus X: {{ content.hero.imageFocusX }}%
                                <input v-model.number="content.hero.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Hero Focus Y: {{ content.hero.imageFocusY }}%
                                <input v-model.number="content.hero.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="border border-soft bg-white p-3">
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

                        <div class="w-full py-10">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <h3 class="font-heading text-3xl">Welcome</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Heading
                                <input v-model="content.welcome.heading" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Signoff
                                <input v-model="content.welcome.signoff" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                        </div>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Letter</label>
                        <RichTextEditor v-model="content.welcome.letter" />

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Image Path
                                <input v-model="content.welcome.image" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Welcome Image
                                <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="mt-2 w-full border border-soft px-4 py-3" @change="uploadContentImage($event, 'welcome.image')">
                            </label>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Focus X: {{ content.welcome.imageFocusX }}%
                                <input v-model.number="content.welcome.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Welcome Focus Y: {{ content.welcome.imageFocusY }}%
                                <input v-model.number="content.welcome.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="border border-soft bg-white p-3">
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

                        <div class="w-full py-10">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <h3 class="font-heading text-3xl">Our Story</h3>
                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Our Story Heading
                            <input v-model="content.story.heading" class="mt-2 w-full border border-soft px-4 py-3">
                        </label>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Our Story Accent (Date/Subtitle)
                            <input v-model="content.story.accent" class="mt-2 w-full border border-soft px-4 py-3">
                        </label>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Our Story Text</label>
                        <RichTextEditor v-model="content.story.text" />

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Our Story Image Path
                                <input v-model="content.story.image" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Our Story Image
                                <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="mt-2 w-full border border-soft px-4 py-3" @change="uploadContentImage($event, 'story.image')">
                            </label>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Story Focus X: {{ content.story.imageFocusX }}%
                                <input v-model.number="content.story.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Story Focus Y: {{ content.story.imageFocusY }}%
                                <input v-model.number="content.story.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="border border-soft bg-white p-3">
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

                        <div class="w-full py-10">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>

                        <div class="mt-2">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="font-heading text-3xl">Timeline</h3>
                                <button type="button" class="admin-btn border border-wedding-band bg-wedding-band px-3 py-2 text-xs uppercase tracking-[0.12em] text-white" @click="addTimelineItem">Add Item</button>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Timeline Heading
                                    <input v-model="content.timeline.heading" class="mt-2 w-full border border-soft px-4 py-3">
                                </label>
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">The Big Day Accent (Date/Subtitle)
                                    <input v-model="content.timeline.dateAccent" class="mt-2 w-full border border-soft px-4 py-3">
                                </label>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div v-for="(item, index) in content.timeline.items" :key="index" class="grid gap-3 border border-soft p-3 md:grid-cols-3">
                                    <input v-model="item.time" placeholder="Time" class="border border-soft px-3 py-2">
                                    <input v-model="item.title" placeholder="Title" class="border border-soft px-3 py-2">
                                    <div class="flex gap-2">
                                        <input v-model="item.description" placeholder="Description" class="w-full border border-soft px-3 py-2">
                                        <button type="button" class="admin-btn admin-btn-danger px-3" @click="removeTimelineItem(index)">X</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full py-10">
                            <hr class="w-full border-t-2 border-wedding-band">
                        </div>
                        <h3 class="font-heading text-3xl">Venue, Travel, and FAQ</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Name
                                <input v-model="content.details.venue.name" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Address
                                <input v-model="content.details.venue.address" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                        </div>

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Blurb</label>
                        <RichTextEditor v-model="content.details.venue.blurb" />

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Details Image Path
                                <input v-model="content.details.image" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Upload Venue Image
                                <input type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="mt-2 w-full border border-soft px-4 py-3" @change="uploadContentImage($event, 'details.image')">
                            </label>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Focus X: {{ content.details.imageFocusX }}%
                                <input v-model.number="content.details.imageFocusX" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Venue Focus Y: {{ content.details.imageFocusY }}%
                                <input v-model.number="content.details.imageFocusY" type="range" min="0" max="100" class="mt-2 w-full">
                            </label>
                        </div>
                        <div class="border border-soft bg-white p-3">
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

                        <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Travel Information</label>
                        <RichTextEditor v-model="content.details.travel" />

                        <div class="grid gap-4 md:grid-cols-3">
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">RSVP CTA Title
                                <input v-model="content.cta.title" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                            <div>
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">RSVP CTA Text</label>
                                <RichTextEditor v-model="content.cta.text" />
                            </div>
                            <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">RSVP CTA Button Label
                                <input v-model="content.cta.buttonLabel" class="mt-2 w-full border border-soft px-4 py-3">
                            </label>
                        </div>

                        <div class="border border-soft p-4">
                            <h3 class="font-heading text-3xl">Menu & RSVP Settings</h3>
                            <p class="mt-2 text-sm text-wedding-muted">This controls the menu section shown above FAQs and whether RSVP asks guests for meal selections.</p>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Section Heading
                                    <input v-model="rsvpSettings.menu_heading" class="mt-2 w-full border border-soft px-4 py-3" placeholder="Wedding Menu">
                                </label>
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">RSVP Meal Type
                                    <select v-model="rsvpSettings.meal_mode" class="mt-2 w-full border border-soft bg-white px-4 py-3">
                                        <option value="options">Guests choose meal options</option>
                                        <option value="set_menu">Set menu for all guests</option>
                                    </select>
                                </label>
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Intro Text</label>
                                <RichTextEditor v-model="rsvpSettings.menu_intro" />
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Notes Card Title
                                    <input v-model="rsvpSettings.menu_note_title" class="mt-2 w-full border border-soft px-4 py-3" placeholder="Dining Notes">
                                </label>
                                <div>
                                    <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Menu Notes Card Text</label>
                                    <RichTextEditor v-model="rsvpSettings.menu_note_text" />
                                </div>
                            </div>

                            <div class="mt-6 space-y-6">
                                <div class="border border-soft p-4">
                                    <div class="mb-3 flex items-center justify-between">
                                        <h4 class="font-heading text-xl">Starter</h4>
                                        <button type="button" class="admin-btn border border-wedding-band bg-wedding-band px-3 py-2 text-xs uppercase tracking-[0.12em] text-white" @click="addMenuCourseItem('starter')">Add Starter</button>
                                    </div>
                                    <div class="space-y-3">
                                        <div v-for="(item, index) in rsvpSettings.menu_courses.starter" :key="`starter-${index}`" class="grid gap-3 border border-soft p-3">
                                            <input v-model="item.title" class="border border-soft px-3 py-2" placeholder="Dish title">
                                            <input v-model="item.description" class="border border-soft px-3 py-2" placeholder="Dish description">
                                            <div class="flex justify-end">
                                                <button type="button" class="admin-btn admin-btn-danger px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeMenuCourseItem('starter', index)">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border border-soft p-4">
                                    <div class="mb-3 flex items-center justify-between">
                                        <h4 class="font-heading text-xl">Main</h4>
                                        <button type="button" class="admin-btn border border-wedding-band bg-wedding-band px-3 py-2 text-xs uppercase tracking-[0.12em] text-white" @click="addMenuCourseItem('main')">Add Main</button>
                                    </div>
                                    <div class="space-y-3">
                                        <div v-for="(item, index) in rsvpSettings.menu_courses.main" :key="`main-${index}`" class="grid gap-3 border border-soft p-3">
                                            <input v-model="item.title" class="border border-soft px-3 py-2" placeholder="Dish title">
                                            <input v-model="item.description" class="border border-soft px-3 py-2" placeholder="Dish description">
                                            <div class="flex justify-end">
                                                <button type="button" class="admin-btn admin-btn-danger px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeMenuCourseItem('main', index)">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-xs text-wedding-muted">Main dish titles are used automatically as RSVP meal choices when guests choose options.</p>
                                </div>

                                <div class="border border-soft p-4">
                                    <div class="mb-3 flex items-center justify-between">
                                        <h4 class="font-heading text-xl">Dessert</h4>
                                        <button type="button" class="admin-btn border border-wedding-band bg-wedding-band px-3 py-2 text-xs uppercase tracking-[0.12em] text-white" @click="addMenuCourseItem('dessert')">Add Dessert</button>
                                    </div>
                                    <div class="space-y-3">
                                        <div v-for="(item, index) in rsvpSettings.menu_courses.dessert" :key="`dessert-${index}`" class="grid gap-3 border border-soft p-3">
                                            <input v-model="item.title" class="border border-soft px-3 py-2" placeholder="Dish title">
                                            <input v-model="item.description" class="border border-soft px-3 py-2" placeholder="Dish description">
                                            <div class="flex justify-end">
                                                <button type="button" class="admin-btn admin-btn-danger px-3 py-2 text-xs uppercase tracking-[0.12em]" @click="removeMenuCourseItem('dessert', index)">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="rsvpSettings.meal_mode === 'set_menu'" class="mt-4">
                                <label class="block text-sm uppercase tracking-[0.12em] text-wedding-muted">Set Menu Description</label>
                                <RichTextEditor v-model="rsvpSettings.set_menu_description" />
                            </div>
                        </div>

                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="font-heading text-3xl">FAQ Items</h3>
                                <button type="button" class="admin-btn border border-wedding-band bg-wedding-band px-3 py-2 text-xs uppercase tracking-[0.12em] text-white" @click="addFaqItem">Add FAQ</button>
                            </div>

                            <div class="space-y-3">
                                <div v-for="(faq, index) in content.details.faqs" :key="index" class="grid gap-3 border border-soft p-3 md:grid-cols-2">
                                    <input v-model="faq.question" placeholder="Question" class="border border-soft px-3 py-2">
                                    <div class="flex gap-2">
                                        <div class="w-full">
                                            <RichTextEditor v-model="faq.answer" />
                                        </div>
                                        <button type="button" class="admin-btn admin-btn-danger px-3" @click="removeFaqItem(index)">X</button>
                                    </div>
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
                            <a class="admin-btn border border-soft px-4 py-2 text-xs uppercase tracking-[0.12em]" href="/admin/api/parties/export">Export CSV</a>
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
                            <button class="admin-btn h-12 bg-wedding-band px-3 text-xs uppercase tracking-[0.12em] text-white" type="button" @click="generateCodeForCreate">Generate</button>
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
                    <button class="admin-btn button-dark mt-4" type="button" @click="createParty">Create Party</button>
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
                                <button class="admin-btn h-12 bg-wedding-band px-3 text-xs uppercase tracking-[0.12em] text-white" type="button" @click="generateCodeForSelectedParty">Generate</button>
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
                            <button class="admin-btn button-dark" type="button" @click="updateParty">Save Party</button>
                            <button class="admin-btn admin-btn-danger px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="deleteParty">Delete</button>
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
                                    <button class="admin-btn border border-soft px-3 text-xs" type="button" @click="updateGuest(guest)">Save</button>
                                    <button class="admin-btn admin-btn-danger px-3 text-xs" type="button" @click="deleteGuest(guest)">Delete</button>
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
                            <label class="inline-flex h-[42px] items-center gap-2 border border-soft px-3 py-2 text-sm">
                                <input v-model="newGuest.is_child" type="checkbox">
                                Child
                            </label>
                            <button class="admin-btn h-[42px] bg-wedding-band px-3 py-2 text-xs uppercase tracking-[0.12em] text-white" type="button" @click="addGuest">Add Guest</button>
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
                    <a class="admin-btn border border-soft px-4 py-2 text-xs uppercase tracking-[0.12em]" href="/admin/api/rsvps/export">Export RSVP CSV</a>
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
                                class="admin-btn px-3 py-2 text-xs uppercase tracking-[0.12em]"
                                :class="row.rsvp?.status ? 'border border-soft bg-white' : 'button-dark'"
                                type="button"
                                @click="editRsvp(row)"
                            >
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
                    <button class="admin-btn admin-btn-danger px-3 py-2 text-xs uppercase tracking-[0.12em]" type="button" @click="closeRsvpModal">X Close</button>
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
                    <button class="admin-btn admin-btn-danger px-4 py-3 text-xs uppercase tracking-[0.12em]" type="button" @click="closeRsvpModal">X Close</button>
                    <button class="admin-btn button-dark" type="button" @click="saveAdminRsvp">Save RSVP</button>
                </div>

                <p v-if="globalMessage" class="mt-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ globalMessage }}
                </p>
                <p v-if="globalError" class="mt-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ globalError }}
                </p>
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
                        <button class="admin-btn button-dark" type="button" @click="saveContent">Save Content</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref } from 'vue';
import RichTextEditor from '../components/admin/RichTextEditor.vue';

const props = defineProps({
    payload: {
        type: Object,
        default: () => ({}),
    },
});

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const section = ref(props.payload?.adminSection || 'dashboard');
const logoutUrl = props.payload?.logoutUrl || '/admin/logout';
const globalMessage = ref('');
const globalError = ref('');
const lastSavedAt = ref('');
const lastSavedContentSnapshot = ref('');
const lastSavedRsvpSnapshot = ref('');

const stats = ref({
    total_households: 0,
    invited_guests: 0,
    attending: 0,
    not_attending: 0,
    no_response: 0,
});

const content = ref(null);
const rsvpSettings = ref({
    meal_mode: 'options',
    menu_heading: 'Wedding Menu',
    menu_intro: 'We cannot wait to share a beautiful meal with you.',
    set_menu_description: 'A chef-curated set menu will be served for all attending guests.',
    menu_note_title: 'Dining Notes',
    menu_note_text: '<p>If you have dietary requirements, please let us know in the RSVP.</p><p>All tables will include a bottle of red and white wine.</p>',
    meal_options: [],
    menu_courses: {
        starter: [{ title: '', description: '' }],
        main: [{ title: '', description: '' }],
        dessert: [{ title: '', description: '' }],
    },
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
    { key: 'dashboard', label: 'Dashboard', href: '/admin' },
    { key: 'parties', label: 'Households', href: '/admin/parties' },
    { key: 'rsvps', label: 'RSVPs', href: '/admin/rsvps' },
    { key: 'content', label: 'Content', href: '/admin/content' },
];

onMounted(async () => {
    await Promise.all([loadStats(), loadParties(), loadRsvps(), loadContent()]);

    if (!newParty.code) {
        await generateCodeForCreate(false);
    }

    if (parties.value.length > 0) {
        selectedPartyId.value = parties.value[0].id;
    }
});

async function loadStats() {
    try {
        const response = await window.axios.get('/admin/api/dashboard');
        stats.value = response.data;
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load dashboard stats.'));
    }
}

async function loadContent() {
    try {
        const response = await window.axios.get('/admin/api/content');
        content.value = response.data.content;
        ensureImageFocusDefaults();
        lastSavedAt.value = formatDateTime(response.data.last_saved_at);
        const legacyMealChoicesEnabled = response.data.rsvp_settings?.meal_choices_enabled;
        const defaultMealMode = legacyMealChoicesEnabled === false ? 'set_menu' : 'options';
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
            menu_courses: {
                starter: response.data.rsvp_settings?.menu_courses?.starter?.length
                    ? response.data.rsvp_settings.menu_courses.starter
                    : [{ title: '', description: '' }],
                main: response.data.rsvp_settings?.menu_courses?.main?.length
                    ? response.data.rsvp_settings.menu_courses.main
                    : [{ title: '', description: '' }],
                dessert: response.data.rsvp_settings?.menu_courses?.dessert?.length
                    ? response.data.rsvp_settings.menu_courses.dessert
                    : [{ title: '', description: '' }],
            },
        };
        captureSavedSnapshots();
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load content.'));
    }
}

async function loadParties() {
    try {
        const response = await window.axios.get('/admin/api/parties');
        parties.value = response.data.parties;
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not load households.'));
    }
}

async function loadRsvps() {
    try {
        const response = await window.axios.get('/admin/api/rsvps');
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
    content.value.timeline.items.push({ time: '', title: '', description: '' });
}

function removeTimelineItem(index) {
    content.value.timeline.items.splice(index, 1);
}

function addFaqItem() {
    content.value.details.faqs.push({ question: '', answer: '' });
}

function removeFaqItem(index) {
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
        const response = await window.axios.put('/admin/api/content', {
            content: content.value,
            rsvp_settings: rsvpSettings.value,
        });
        if (response.data?.content) {
            content.value = response.data.content;
            ensureImageFocusDefaults();
        }
        if (response.data?.rsvp_settings) {
            rsvpSettings.value = response.data.rsvp_settings;
        }
        lastSavedAt.value = formatDateTime(response.data?.last_saved_at) || new Date().toLocaleString();
        captureSavedSnapshots();
        setMessage(response.data?.message || 'Content updated.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not save content.'));
    }
}

function addMenuCourseItem(courseKey) {
    if (!rsvpSettings.value.menu_courses?.[courseKey]) {
        rsvpSettings.value.menu_courses[courseKey] = [];
    }
    rsvpSettings.value.menu_courses[courseKey].push({ title: '', description: '' });
}

function removeMenuCourseItem(courseKey, index) {
    if (!rsvpSettings.value.menu_courses?.[courseKey]) {
        return;
    }
    rsvpSettings.value.menu_courses[courseKey].splice(index, 1);
    if (rsvpSettings.value.menu_courses[courseKey].length === 0) {
        rsvpSettings.value.menu_courses[courseKey].push({ title: '', description: '' });
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
        const response = await window.axios.post('/admin/api/content/image', formData, {
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
        await window.axios.post('/admin/api/parties', newParty);
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
        await window.axios.put(`/admin/api/parties/${selectedParty.value.id}`, selectedParty.value);
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
        const response = await window.axios.get('/admin/api/parties/generate-code');
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
        const response = await window.axios.get('/admin/api/parties/generate-code');
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

    if (!window.confirm('Delete this household and all guests/RSVP data?')) {
        return;
    }

    try {
        await window.axios.delete(`/admin/api/parties/${selectedParty.value.id}`);
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
        await window.axios.post(`/admin/api/parties/${selectedParty.value.id}/guests`, newGuest);
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
        await window.axios.put(`/admin/api/guests/${guest.id}`, guest);
        setMessage('Guest updated successfully.');
    } catch (error) {
        setError(extractErrorMessage(error, 'Could not update guest.'));
    }
}

async function deleteGuest(guest) {
    clearError();
    if (!window.confirm('Remove this guest?')) {
        return;
    }

    try {
        await window.axios.delete(`/admin/api/guests/${guest.id}`);
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
        await window.axios.put(`/admin/api/rsvps/${editingRsvp.value.party_id}`, rsvpForm);
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
        const response = await window.axios.post('/admin/api/parties/import', formData, {
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
    color: #1e1e1e;
}

.admin-btn {
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

.admin-btn:hover {
    background-color: #8b8b8b !important;
    border-color: #8b8b8b !important;
    color: #ffffff !important;
}

.admin-btn-danger {
    border: 1px solid #b42318;
    background-color: #ffffff;
    color: #b42318;
}

.admin-btn-danger:hover {
    border-color: #912018 !important;
    background-color: #fef2f2 !important;
    color: #912018 !important;
}
</style>
