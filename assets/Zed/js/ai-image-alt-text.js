import { AiProductManagement } from './ai-product-management';

export class AiImageAltText extends AiProductManagement {
    triggerSelector = '.js-ai-alt-image-trigger';

    preparePayload(trigger) {
        const inputLocale = this.fieldElement.name.split('[')[1].split(']')[0].replace('image_set_', '');
        const patterns = JSON.parse(trigger.getAttribute('data-product-field-pattern'));

        this.data = {
            locale: inputLocale === 'default' ? 'en_US' : inputLocale,
        };

        for (const pattern of patterns) {
            const { value } = document.querySelector(`[name="${pattern.replace('%locale%', this.data.locale)}"]`);

            if (value) {
                this.data.imageUrl = value;

                return;
            }
        }
    }

    async processAiAction() {
        if (!this.data.imageUrl) {
            this.modal.classList.add(this.states.empty);
            this.modal.classList.remove(this.states.loading);

            return;
        }

        const input = this.modal.querySelector('.js-ai-alt-text-input');

        input.value = '';

        try {
            const { altText } = await (await fetch(this.url, {
                method: 'POST',
                body: new URLSearchParams(this.data),
            })).json();

            input.value = decodeURI(altText);
        } finally {
            this.modal.classList.remove(this.states.loading);
        }
    }

    onApply() {
        this.fieldElement.value = this.modal.querySelector('.js-ai-alt-text-input').value;
    };
}
