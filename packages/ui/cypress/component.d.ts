/// <reference types="cypress" />
import cypress from 'cypress'
import { mount } from 'cypress/vue'

declare global {
  namespace Cypress {
    interface Chainable {
      mount: typeof mount
    }
  }
}
