require('../scss/main.scss') ;
export default class AiRequestBuilder {
    constructor(
        requestUrl = '',
        requestMethod = 'POST',
        requestBody = {},
        targetPopoverSelector = 'data-target-popover',
        sourceFieldSelector = 'data-source-field',
        targetFieldSelector = 'data-target-field',
        requestActionAttribute = 'data-request-action',
        requestUrlAttribute = 'data-request-url',
        requestTargetLocaleAttribute = 'data-request-target-locale',
        errorBlock,
    ) {
        this.requestUrl = requestUrl;
        this.requestMethod = requestMethod;
        this.requestBody = requestBody;
        this.targetPopoverSelector = targetPopoverSelector;
        this.sourceFieldSelector = sourceFieldSelector;
        this.targetFieldSelector = targetFieldSelector;
        this.requestActionAttribute = requestActionAttribute;
        this.requestUrlAttribute = requestUrlAttribute;
        this.requestTargetLocaleAttribute = requestTargetLocaleAttribute;
        this.responseFieldSelector = '#response-field';
        this.responseField = document.querySelector(this.responseFieldSelector) || null;
        this.loadingPopover = document.querySelector('.loading-popover');
        this.responsePopover = document.querySelector('.response-popover');
        this.againBtn = document.querySelector('.js-ai-builder-again') || null;
        this.applyBtn = document.querySelector('.js-ai-builder-apply') || null;
        this.response = '';
        this.sourceFields = [];
        this.currentTargetFieldSelector = '';
        this.errorBlock = document.querySelector('.js-ai-product-management-modal__error-block');
        this.errorBlock.innerHTML = '';
        this.errorBlock.display = 'none';

        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Start AI request
        document.querySelectorAll('[data-ai-start]').forEach(button => {
            button.addEventListener('click', () => {
                this.requestBody = {};
            })
        })

        // Set current locale
        document.querySelectorAll('[data-current-locale]').forEach(button => {
            button.addEventListener('click', (event) => {
                this.requestBody.currentLocale = event.target.dataset.currentLocale || '';
            })
        })

        // Show target popover
        document.querySelectorAll(`[${this.targetPopoverSelector}]`).forEach(button => {
            button.addEventListener('click', (event) => {
                this.showTargetPopover(event);
            })
        })

        // Set request action (translation, description improvement)
        document.querySelectorAll(`[${this.requestActionAttribute}]`).forEach(button => {
            button.addEventListener('click', (event) => {
                this.requestBody.action = event.target.getAttribute(this.requestActionAttribute);
                this.requestUrl = event.target.getAttribute(this.requestUrlAttribute);
            })
        })

        // Set target locale
        document.querySelectorAll(`[${this.requestTargetLocaleAttribute}]`).forEach(button => {
            button.addEventListener('click', (event) => {
                this.requestBody.locale = event.target.getAttribute(this.requestTargetLocaleAttribute);
                this.currentTargetFieldSelector = this.currentTargetFieldSelector.replaceAll(this.requestBody.currentLocale, this.requestBody.locale);
            })
        })

        document.querySelectorAll(`[${this.sourceFieldSelector}]`).forEach(button => {
            button.addEventListener('click', (event) => {
                const sources = event.target.dataset.sourceField.split(',').map(field => field.trim());
                this.sourceFields = [];
                sources.forEach(field => {
                    this.sourceFields.push(document.querySelector(field));
                })

                this.requestBody.text = '';
                this.sourceFields.forEach(field => {
                    this.requestBody.text += (field.value || '');
                })
            })
        })

        document.querySelectorAll(`[${this.targetFieldSelector}]`).forEach(button => {
            button.addEventListener('click', (event) => {
                this.currentTargetFieldSelector = event.target.getAttribute(this.targetFieldSelector);
            })
        })

        document.querySelectorAll('[data-request-ready]').forEach(button => {
            button.addEventListener('click', (event) => {
                if(this.requestBody.action === 'translation') {
                    this.currentTargetFieldSelector = this.currentTargetFieldSelector.replaceAll(this.requestBody.currentLocale, this.requestBody.locale);
                }
                this.sendRequest();
                console.log(this.requestBody);
            })
        })

        document.querySelectorAll('[data-close-popover]').forEach(button => {
            button.addEventListener('click', (event) => {
                this.closePopovers();
                this.requestBody = {};
            })
        })

        this.againBtn.addEventListener('click', (event) => {
            this.sendRequest();
        })

        this.applyBtn.addEventListener('click', (event) => {
            document.querySelector(this.currentTargetFieldSelector).value = this.response;
            this.closePopovers();
        })
    }

    sendRequest() {
        const formData = new FormData();
        formData.append('text', this.requestBody.text);
        formData.append('locale', this.requestBody.locale);

        const requestOptions = {
            method: this.requestMethod,
            body: formData,
        };
        this.closePopovers();
        this.toggleLoadingPopover();
        fetch(this.requestUrl, requestOptions)
        .then(response => {
            return response.json().then(data => ({
                status: response.status,
                body: data
            }));
        })
        .then(data => {
            switch(data.status)  {
                case 400:
                case 422:
                    this.handleError(data.body.errors[0].message);
                    break;
                default:
                    this.handleSuccess(data.body.errors[0])
                    break;
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            this.toggleLoadingPopover(false);
        });
    }

    handleSuccess(data) {
        this.errorBlock.display = 'none';
        this.toggleLoadingPopover(false);
        document.getElementById('original-field').value = this.requestBody.text;

        switch(this.requestBody.action) {
            case 'translation':
                this.response = this.responseField.value = data.translation || '';
                break;
            default:
                this.response = this.responseField.value = data.improvedText || '';
                break;
        }
        this.toggleResponsePopover();
    }

    handleError(message = '') {
        this.errorBlock.innerHTML = message;
        this.errorBlock.display = 'block';
        this.toggleLoadingPopover(false);
        this.toggleResponsePopover();
    }

    toggleLoadingPopover(isVisible = true) {
        isVisible ? this.loadingPopover?.showPopover() : this.loadingPopover?.hidePopover();
    }

    toggleResponsePopover(isVisible = true) {
        isVisible ? this.responsePopover?.showPopover() : this.responsePopover?.hidePopover();
    }

    showTargetPopover(event) {
        this.closePopovers();
        document.querySelector(event.target.dataset.targetPopover)?.showPopover();
    }

    closePopovers() {
        document.querySelectorAll('[popover]').forEach(popover => {
            popover.hidePopover();
        });
    }
}

document.addEventListener('DOMContentLoaded', function(){
    new AiRequestBuilder();
})