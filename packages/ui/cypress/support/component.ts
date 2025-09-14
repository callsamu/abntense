/// <reference types="../component" />
import './commands'
import '@/styles/app.css'
import { mount } from 'cypress/vue'
import PrimeVue from 'primevue/config'


Cypress.Commands.add('mount', (component, ...args) => {
    // @ts-ignore
    args.global = args.global || {}
    // @ts-ignore
    args.global.plugins = args.global.plugins || []
    // @ts-ignore
    args.global.plugins.push({
        // @ts-ignore
        install(app) {
            app.use(PrimeVue, {
                unstyled: true
            });
        }
    })

    // @ts-ignore
    return mount(component, args);
});
