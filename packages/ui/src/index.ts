import { App } from 'vue'
import * as components from './components'
import PrimeVue from 'primevue/config';

function install (app: App): App {
  for (const key in components) {
    // @ts-expect-error
    app.component(key, components[key])
  }

  app.use(PrimeVue, {
    unstyled: true
  });

  return app;
}

export default { install }

export * from './components'
