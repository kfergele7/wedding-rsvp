<template>
  <header :class="['mk-nav', { 'is-scrolled': scrolled }]">
    <div class="mx-auto w-full max-w-[1180px] px-4">
      <div class="flex items-center justify-between gap-4 py-4">
        <a href="/" class="inline-flex items-center">
          <img :src="'/images/brand/logo-dark.svg'" alt="Magic Invitation" class="h-8 w-auto md:h-9" />
        </a>

        <nav class="hidden items-center gap-7 lg:flex">
          <a v-for="link in links" :key="link.href" :href="link.href" class="mk-nav-link">{{ link.label }}</a>
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
          <a href="/login" class="mk-btn mk-btn-secondary">Log in</a>
          <a href="/register" class="mk-btn mk-btn-primary">Start free</a>
        </div>

        <button type="button" class="inline-flex h-10 w-10 items-center justify-center border border-soft bg-white lg:hidden" aria-label="Open menu" @click="mobileOpen = true">
          <span class="material-symbols-outlined">menu</span>
        </button>
      </div>
    </div>

    <div v-if="mobileOpen" class="fixed inset-0 z-[100] bg-black/45 lg:hidden" @click.self="mobileOpen = false">
      <div class="h-full w-full bg-wedding-bg">
        <div class="mx-auto w-full max-w-[1180px] px-4 py-5">
          <div class="flex items-center justify-between border-b border-soft pb-4">
            <img :src="'/images/brand/logo-dark.svg'" alt="Magic Invitation" class="h-8 w-auto" />
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center border border-soft bg-white" aria-label="Close menu" @click="mobileOpen = false">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <nav class="mt-5 grid gap-2">
            <a v-for="link in links" :key="`mobile-${link.href}`" :href="link.href" class="mk-mobile-link">{{ link.label }}</a>
            <a href="/login" class="mk-mobile-link">Log in</a>
            <a href="/register" class="mk-mobile-link">Start free</a>
          </nav>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const mobileOpen = ref(false);
const scrolled = ref(false);

const links = [
  { label: 'Features', href: '/features' },
  { label: 'How it works', href: '/how-it-works' },
  { label: 'Pricing', href: '/pricing' },
  { label: 'FAQ', href: '/faq' },
];

const onScroll = () => {
  scrolled.value = window.scrollY > 8;
};

onMounted(() => {
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll);
});
</script>
