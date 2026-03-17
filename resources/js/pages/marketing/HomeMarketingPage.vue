<template>
  <MarketingLayout>
    <div class="mx-auto w-full max-w-[1180px] space-y-24 px-4 md:space-y-28">
      <Hero />
      <CollageBand />
      <SplitBand />
      <Testimonials />
      <MetricsRow />
      <Steps />
      <FeatureGroups />
      <TemplateShowcase />
      <PricingBand />
      <FAQSection :items="faqItems" />
      <FinalCTA />
    </div>
  </MarketingLayout>
</template>

<script setup>
import CollageBand from '../../components/marketing/CollageBand.vue';
import FAQSection from '../../components/marketing/FAQSection.vue';
import FeatureGroups from '../../components/marketing/FeatureGroups.vue';
import FinalCTA from '../../components/marketing/FinalCTA.vue';
import Hero from '../../components/marketing/Hero.vue';
import MarketingLayout from '../../components/marketing/MarketingLayout.vue';
import MetricsRow from '../../components/marketing/MetricsRow.vue';
import PricingBand from '../../components/marketing/PricingBand.vue';
import SplitBand from '../../components/marketing/SplitBand.vue';
import Steps from '../../components/marketing/Steps.vue';
import TemplateShowcase from '../../components/marketing/TemplateShowcase.vue';
import Testimonials from '../../components/marketing/Testimonials.vue';

const props = defineProps({
  payload: {
    type: Object,
    default: () => ({}),
  },
});

const faqItems = props.payload?.home?.faqItems || [];
</script>
