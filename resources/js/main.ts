import { createApp } from 'vue';
import App from './App.vue';

const el = document.getElementById('skills-app');

if (el) {
    createApp(App).mount('#skills-app');
}