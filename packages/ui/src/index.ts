import { App } from 'vue'
import * as components from './components'
import PrimeVue from 'primevue/config';

function install (app: App) {
  for (const key in components) {
    // @ts-expect-error
    app.component(key, components[key])
  }

  app.use(PrimeVue, {
    unstyled: true
  });
}

export default { install }

export * from './components'
