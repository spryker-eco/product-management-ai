import { AiProductManagement } from './ai-product-management';

export class AiCategorySuggestion extends AiProductManagement {
    names = ['description', 'name'];
    trigger = '.js-ai-category-trigger';

    preparePayload(trigger) {
        const fieldSelector = trigger.getAttribute('data-product-info-field');
        const dataFields = document.querySelectorAll(fieldSelector);

        this.data = {};

        for (const { name, value } of dataFields) {
            const descriptionPart = this.names.find((part) => name.includes(`[${part}]`));

            if (!descriptionPart || !value) {
                continue;
            }

            this.data[`product_${descriptionPart}`] ??= value;

            if (Object.keys(this.data).length === this.names.length) {
                return;
            }
        }
    }

    async processAiAction() {
        if (Object.keys(this.data).length !== this.names.length) {
            this.modal.classList.add(this.states.empty);
            this.modal.classList.remove(this.states.loading);

            return;
        }

        const select = this.modal.querySelector('.js-ai-category-select');

        select.replaceChildren();

        try {
            const { categories } = await (await fetch(this.url, {
                method: 'POST',
                body: new URLSearchParams(this.data),
            })).json();
            const fragment = document.createDocumentFragment();

            for (const [text, id] of Object.entries(categories)) {
                fragment.append(new Option(text, id, true, true));
                this.result ??= [];
                this.result.push(String(id));
            }

            select.append(fragment);
        } finally {
            this.modal.classList.remove(this.states.loading);
            select.dispatchEvent(new Event('change'));
        }
    }

    onApply() {
        for (const option of this.fieldSelector.options) {
            option.selected = this.result.includes(option.value);
        }

        this.fieldSelector.dispatchEvent(new Event('change'));
    };
}
