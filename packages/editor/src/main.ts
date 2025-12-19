import { createApp } from 'vue'
import './style.css'
import '../lib/editor.css'
import App from './App.vue'
import UI from '@monorepo/ui';

const app = UI.install(createApp(App));
app.mount('#app')
