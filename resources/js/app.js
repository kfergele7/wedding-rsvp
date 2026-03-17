import './bootstrap';
import { createApp, h } from 'vue';
import HomePage from './pages/HomePage.vue';
import RsvpPage from './pages/RsvpPage.vue';
import AdminPage from './pages/AdminPage.vue';
import HomeMarketingPage from './pages/marketing/HomeMarketingPage.vue';
import PricingMarketingPage from './pages/marketing/PricingMarketingPage.vue';
import FeaturesMarketingPage from './pages/marketing/FeaturesMarketingPage.vue';
import HowItWorksMarketingPage from './pages/marketing/HowItWorksMarketingPage.vue';
import FaqMarketingPage from './pages/marketing/FaqMarketingPage.vue';

const mountElement = document.getElementById('app');

if (mountElement) {
    const page = window.__APP_PAGE || 'home';
    const payload = window.__APP_PAYLOAD || {};

    const componentMap = {
        home: HomePage,
        rsvp: RsvpPage,
        admin: AdminPage,
        'marketing-home': HomeMarketingPage,
        'marketing-pricing': PricingMarketingPage,
        'marketing-features': FeaturesMarketingPage,
        'marketing-how-it-works': HowItWorksMarketingPage,
        'marketing-faq': FaqMarketingPage,
    };

    const CurrentComponent = componentMap[page] || HomePage;

    createApp({
        render: () => h(CurrentComponent, { payload }),
    }).mount('#app');
}
