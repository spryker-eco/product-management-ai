require('../scss/main.scss');

document.addEventListener('DOMContentLoaded', () => {
    new AiCategorySuggestion().init();
});

class AiCategorySuggestion {
    names = ['description', 'name'];
    states = {
        loading: 'is-loading',
        empty: 'is-empty',
    }
    data = null;
    modal = null;
    fieldSelector = null;

    constructor() {
        this.onApply = this.onApply.bind(this);
        this.onAgain = this.onAgain.bind(this);
    }

    init() {
        document.querySelectorAll('.js-ai-category-trigger').forEach((trigger) => {
            trigger.addEventListener('click', this.onTriggerClick.bind(this));
        })
    }

    onTriggerClick(event) {
        const trigger = event.currentTarget;

        this.modal = document.getElementById(trigger.getAttribute('popovertarget'));
        this.fieldSelector = trigger.parentElement.querySelector(`${trigger.getAttribute('data-field-selector')}`);

        this.refreshElements();

        this.modal.classList.add(this.states.loading);

        this.preparePayload(trigger.getAttribute('data-product-info-field'));

        if (this.data.size !== this.names.length) {
            this.modal.classList.add(this.states.empty);
            this.modal.classList.remove(this.states.loading);

            return;
        }

        this.getSuggestionCategories();
    }

    refreshElements() {
        this.modal.classList.remove(this.states.loading, this.states.empty);
        this.modal.querySelector('.js-ai-category-apply').removeEventListener('click', this.onApply);
        this.modal.querySelector('.js-ai-category-again').removeEventListener('click', this.onAgain);

        this.modal.querySelector('.js-ai-category-apply').addEventListener('click', this.onApply);
        this.modal.querySelector('.js-ai-category-again').addEventListener('click', this.onAgain);
    }

    preparePayload(fieldsSelector) {
        const dataFields = document.querySelectorAll(fieldsSelector);
        const data = {};

        for (const { name, value } of dataFields) {
            const descriptionPart = this.names.find((part) => name.includes(`[${part}]`));

            if (!descriptionPart || !value) {
                continue;
            }

            data[`product_${descriptionPart}`] ??= value;

            if (Object.keys(data).length === this.names.length) {
                break;
            }
        }

        this.data = new URLSearchParams(data);
    }

    async getSuggestionCategories() {
        const select = this.modal.querySelector('.js-ai-category-select');

        select.replaceChildren();

        try {
            const { categories } = await (await fetch('/product-management-ai/category-suggestion', {
                method: 'POST',
                body: this.data,
            })).json();
            const fragment = document.createDocumentFragment();

            for (const [text, id] of Object.entries(categories)) {
                fragment.append(new Option(text, id, true, true));
            }

            select.append(fragment);
        } finally {
            this.modal.classList.remove(this.states.loading);
            select.dispatchEvent(new Event('change'));
        }
    }

    onApply() {
        const { selectedOptions } = this.modal.querySelector('.js-ai-category-select');

        for (const option of this.fieldSelector.options) {
            option.selected = [...selectedOptions].some(_option => _option.value === option.value);
        }

        this.fieldSelector.dispatchEvent(new Event('change'));
    };

    onAgain() {
        this.modal.classList.add(this.states.loading);
        this.getSuggestionCategories();
    };
}
