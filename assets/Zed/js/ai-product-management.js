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
    fieldElement = null;
    triggerSelector = null;
    url = null;

    init() {
        document.querySelectorAll(this.triggerSelector).forEach((trigger) => {
            trigger.addEventListener('click', this.onTriggerClick.bind(this));
        })
    }

    refreshElements() {
        this.data = null;
        this.modal.classList.remove(this.states.loading, this.states.empty);
        this.modal.querySelector('.js-ai-product-management-apply').removeEventListener('click', this.onApply);
        this.modal.querySelector('.js-ai-product-management-again').removeEventListener('click', this.onAgain);

        this.modal.querySelector('.js-ai-product-management-apply').addEventListener('click', this.onApply);
        this.modal.querySelector('.js-ai-product-management-again').addEventListener('click', this.onAgain);
    }

    onTriggerClick(event) {
        const trigger = event.currentTarget;

        this.modal = document.getElementById(trigger.getAttribute('popovertarget'));
        this.fieldElement = trigger.parentElement.querySelector(`${trigger.getAttribute('data-field-selector')}`);
        this.url = trigger.dataset.url;

        this.refreshElements();

        this.modal.classList.add(this.states.loading);

        this.preparePayload(trigger);
        this.processAiAction();
    }

    onAgain() {
        this.modal.classList.add(this.states.loading);
        this.processAiAction();
    }

    preparePayload() {
        throw new Error('Method `preparePayload` at AiProductManagement class is not implemented.');
    }

    processAiAction() {
        throw new Error('Method `processAiAction` at AiProductManagement class is not implemented.');
    }

    onApply() {
        throw new Error('Method `onApply` at AiProductManagement class is not implemented.');
    }
}
