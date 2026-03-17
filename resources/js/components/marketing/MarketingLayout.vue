<template>
  <div class="min-h-screen bg-wedding-bg text-wedding-black">
    <Navbar />
    <main class="pb-20 pt-8 md:pt-12">
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { nextTick, onMounted } from 'vue';
import Footer from './Footer.vue';
import Navbar from './Navbar.vue';

onMounted(async () => {
  await nextTick();
  const nodes = Array.from(document.querySelectorAll('.mk-reveal'));
  if (!nodes.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.14 });

  nodes.forEach((node) => observer.observe(node));
});
</script>
