import './bootstrap';
import { createApp, h } from 'vue';
import HomePage from './pages/HomePage.vue';
import RsvpPage from './pages/RsvpPage.vue';
import AdminPage from './pages/AdminPage.vue';

const mountElement = document.getElementById('app');

if (mountElement) {
    const page = window.__APP_PAGE || 'home';
    const payload = window.__APP_PAYLOAD || {};

    const componentMap = {
        home: HomePage,
        rsvp: RsvpPage,
        admin: AdminPage,
    };

    const CurrentComponent = componentMap[page] || HomePage;

    createApp({
        render: () => h(CurrentComponent, { payload }),
    }).mount('#app');
}
