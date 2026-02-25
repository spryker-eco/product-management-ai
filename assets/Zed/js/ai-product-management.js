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
    errorHolder = null;

    init() {
        document.addEventListener('click', (event) => {
            const targetElement = event.target.closest(this.triggerSelector);
            if (!targetElement) {
                return;
            }

            const modifiedEvent = { ...event, currentTarget: targetElement };
            this.onTriggerClick(modifiedEvent);
        });
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
        this.errorHolder = this.modal.querySelector('.js-ai-product-management-modal__error');
        this.cleanError();
        this.fieldElement = trigger.parentElement.querySelector(`${trigger.getAttribute('data-field-selector')}`);
        this.url = trigger.dataset.url;

        this.refreshElements();

        this.modal.classList.add(this.states.loading);

        this.preparePayload(trigger);
        this.processAiAction();
    }

    cleanError() {
        this.errorHolder.innerText = '';
    }

    onAgain() {
        this.modal.classList.add(this.states.loading);
        this.processAiAction();
        this.cleanError();
    }

    onError(error) {
        this.errorHolder.innerText = error;
        this.modal.classList.remove(this.states.loading);
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
