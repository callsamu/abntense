/// <reference types="../../cypress/component" />
import ChipInput from './ChipInput.vue'

describe('<ChipInput />', () => {
    it('Allows Insertion', () => {
        const onChangeSpy = cy.spy().as('onChangeSpy');

        cy.mount(ChipInput, {
            props: {
                modelValue: [],
                'onUpdate:modelValue': onChangeSpy
            }
        })

        cy.getByData('input').type('Item');
        cy.getByData('add').click();

        cy.get('@onChangeSpy').should('have.been.calledWith', ['Item']);
    });

    it('Allows Deletion', () => {
        const onChangeSpy = cy.spy().as('onChangeSpy');

        cy.mount(ChipInput, {
            props: {
                modelValue: ["Item 1", "Item 2"],
                'onUpdate:modelValue': onChangeSpy
            }
        })

        cy.getByData('chip-0').find("[data-test='remove']").click();
        cy.get('@onChangeSpy').should('have.been.calledWith', ['Item 2']);
    });

})
