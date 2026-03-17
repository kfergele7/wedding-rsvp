<template>
  <footer class="mt-10 border-t border-soft bg-white/80">
    <div class="mx-auto grid w-full max-w-[1180px] gap-10 px-4 py-12 md:grid-cols-4">
      <div>
        <img :src="'/images/brand/logo-dark.svg'" alt="Magic Invitation" class="h-8 w-auto" />
        <p class="mt-4 text-sm leading-relaxed text-wedding-muted">
          Privacy-first wedding RSVPs with one beautiful template you edit via simple forms.
        </p>
      </div>
      <div>
        <p class="mk-foot-head">Product</p>
        <ul class="mt-4 space-y-2 text-sm text-wedding-black">
          <li><a href="/features" class="hover:text-wedding-band">Features</a></li>
          <li><a href="/how-it-works" class="hover:text-wedding-band">How it works</a></li>
          <li><a href="/pricing" class="hover:text-wedding-band">Pricing</a></li>
          <li><a href="/faq" class="hover:text-wedding-band">FAQ</a></li>
        </ul>
      </div>
      <div>
        <p class="mk-foot-head">Company</p>
        <ul class="mt-4 space-y-2 text-sm text-wedding-black">
          <li><a href="/register" class="hover:text-wedding-band">Start free</a></li>
          <li><a href="/login" class="hover:text-wedding-band">Log in</a></li>
          <li><a href="mailto:support@magicinvitation.app" class="hover:text-wedding-band">support@magicinvitation.app</a></li>
        </ul>
      </div>
      <div>
        <p class="mk-foot-head">Legal</p>
        <ul class="mt-4 space-y-2 text-sm text-wedding-black">
          <li><a href="#" class="hover:text-wedding-band">Terms</a></li>
          <li><a href="#" class="hover:text-wedding-band">Privacy</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-soft bg-[#0f1b1d] py-2">
      <div class="mx-auto flex w-full max-w-[1180px] items-center justify-between px-4 text-[11px] tracking-[0.12em] text-white/75">
        <p>Privacy-first by design.</p>
        <p>© {{ new Date().getFullYear() }} Magic Invitation</p>
      </div>
    </div>
  </footer>
</template>
