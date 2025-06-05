import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import {createPersistedState} from 'pinia-plugin-persistedstate';
import Toast from "vue-toastification";
import AppContainer from './components/AppContainer.vue';

const app = createApp({});

const pinia = createPinia();
pinia.use(createPersistedState({
    storage: localStorage,
    key: id => `quotelikepro__${id}`,
}));
app.use(pinia);
app.use(Toast, {});

app.component('app-container', AppContainer);

app.mount('#app');

