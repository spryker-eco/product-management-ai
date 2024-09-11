import { AiProductManagement } from './ai-product-management';

export class AiTranslation extends AiProductManagement {
    triggerSelector = '.js-ai-translation-trigger';
    translationData = {};

    preparePayload(trigger) {
        document.querySelectorAll('.js-ai-translation-preview').forEach((field) => field.classList.remove('is-visible'));

        this.translationData = {
            fromLocale: trigger.getAttribute('data-from-locale'),
            fieldName: trigger.getAttribute('data-field-name'),
        }
        this.fieldElement = document.getElementsByName(`${trigger.getAttribute('data-field-pattern')}[${this.translationData.fieldName}]`);
        this.data = {
            locale: trigger.getAttribute('data-to-locale'),
            text: this.fieldElement[0].value,
        }
    }

    async processAiAction() {
        if (!this.data.text) {
            this.modal.classList.add(this.states.empty);
            this.modal.classList.remove(this.states.loading);

            return;
        }

        const data = new FormData();

        data.append('text', this.data.text);
        data.append('locale', this.data.locale);

        if (this.data.cache) {
            data.append('invalidate_cache', 1);
        }

        const field = this.modal.querySelector(`[name="ai-translation-preview[${this.translationData.fieldName}]"]`);

        this.modal.querySelector('.js-translate-to').innerHTML = this.data.locale;
        field.value = '';
        field.classList.add('is-visible');

        try {
            const [{ translation }] = await (await fetch(this.url, {
                method: 'POST',
                body: data,
            })).json();

            field.value = decodeURI(translation);
            this.data.cache = true;
        } finally {
            this.modal.classList.remove(this.states.loading);
        }
    }

    onApply() {
        const targetPattern = trigger.getAttribute('data-field-pattern').replace(this.translationData.fromLocale, this.data.locale);

        document.getElementsByName(`${targetPattern}[${this.translationData.fieldName}]`).value = this.modal.querySelector(`[name="ai-translation-preview[${this.translationData.fieldName}]"]`).value;
    }
}
