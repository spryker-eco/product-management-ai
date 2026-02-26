import { AiProductManagement } from './ai-product-management';

export class AiCategorySuggestion extends AiProductManagement {
    names = ['description', 'name'];
    triggerSelector = '.js-ai-category-trigger';

    preparePayload(trigger) {
        const fieldElement = trigger.getAttribute('data-product-info-field');
        const dataFields = document.querySelectorAll(fieldElement);

        this.data = {};

        for (const { name, value } of dataFields) {
            const informationalPart = this.names.find((part) => name.includes(`[${part}]`));

            if (!informationalPart || !value) {
                continue;
            }

            this.data[`product_${informationalPart}`] ??= value;

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
            const response = await fetch(this.url, {
                method: 'POST',
                body: new URLSearchParams(this.data),
            });


            const responseData = await response.json();
        
            if (!response.ok) {
                this.onError(responseData.errors[0].message);
                return;
            }
        
            const { categories } = responseData;
            const fragment = document.createDocumentFragment();
        
            for (const [text, id] of Object.entries(categories)) {
                fragment.append(new Option(text, id, true, true));
            }
        
            select.append(fragment);
        
        } catch (e) {
            console.error(e);
            
        } finally {
            this.modal.classList.remove(this.states.loading);
            select.dispatchEvent(new Event('change'));
        }
    }

    onApply() {
        const { selectedOptions } = this.modal.querySelector('.js-ai-category-select');

        for (const option of this.fieldElement.options) {
            option.selected = [...selectedOptions].some(_option => _option.value === option.value);
        }

        this.fieldElement.dispatchEvent(new Event('change'));
    };
}
