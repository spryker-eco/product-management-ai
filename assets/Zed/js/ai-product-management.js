export class AiProductManagement {
    constructor() {
        this.onApply = this.onApply.bind(this);
        this.onAgain = this.onAgain.bind(this);
    }

    states = {
        loading: 'is-loading',
        empty: 'is-empty',
    }

    data = null;
    modal = null;
    fieldSelector = null;
    trigger = null;
    result = null;

    init() {
        document.querySelectorAll(this.trigger).forEach((trigger) => {
            trigger.addEventListener('click', this.onTriggerClick.bind(this));
        })
    }

    onTriggerClick(event) {
        const trigger = event.currentTarget;

        this.modal = document.getElementById(trigger.getAttribute('popovertarget'));
        this.fieldSelector = trigger.parentElement.querySelector(`${trigger.getAttribute('data-field-selector')}`);

        this.refreshElements();

        this.modal.classList.add(this.states.loading);

        this.preparePayload(trigger);
        this.processAiAction();
    }

    preparePayload(fieldSelector) {
        throw new Error('Method `preparePayload` at AiProductManagement class is not implemented.');
    }

    processAiAction() {
        throw new Error('Method `processAiAction` at AiProductManagement class is not implemented.');
    }

    refreshElements() {
        this.data = null;
        this.result = null;
        this.modal.classList.remove(this.states.loading, this.states.empty);
        this.modal.querySelector('.js-ai-product-management-apply').removeEventListener('click', this.onApply);
        this.modal.querySelector('.js-ai-product-management-again').removeEventListener('click', this.onAgain);

        this.modal.querySelector('.js-ai-product-management-apply').addEventListener('click', this.onApply);
        this.modal.querySelector('.js-ai-product-management-again').addEventListener('click', this.onAgain);
    }

    onAgain() {
        this.modal.classList.add(this.states.loading);
        this.processAiAction();
    };
}
