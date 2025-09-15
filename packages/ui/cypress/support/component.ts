/// <reference types="../component" />
import './commands'
import '@/styles/app.css'
import { mount } from 'cypress/vue'
import PrimeVue from 'primevue/config'


Cypress.Commands.add('mount', (component, options = {}) => {
    // @ts-ignore
    options.global = options.global || {}
    // @ts-ignore
    options.global.plugins = options.global.plugins || []

    // @ts-ignore
    options.global.plugins.push({
        // @ts-ignore
        install(app) {
            app.use(PrimeVue, {
                unstyled: true
            });
        }
    })

    console.log(options);

    // @ts-ignore
    return mount(component, options);
});

Cypress.Commands.add('getByData', (name) => {
    return cy.get(`[data-test="${name}"]`);
});
