require('../scss/ai-image-alt-text.scss');

let currentAltTextInput;
let currentAiAltTextButton;
$(document).ready(() => {
    const wrapper = document.querySelector('.js-product-images-container');
    wrapper.addEventListener('click', (event) => {
        const target = event.target.closest('.js-ai-image-alt-text__btn');

        if (!target) {
            return;
        }

        currentAiAltTextButton = target;
        onOpenAiImageAltTextModalButtonClick(event);
    });

    appendModals();
    aiAltTextModalButtonsInit();
});

const appendModals = () => {
    const modalWrapper = document.createElement('div');
    modalWrapper.innerHTML = modalTemplates();
    document.querySelector('body').append(modalWrapper);
};

const aiAltTextModalButtonsInit = () => {
    document.querySelector('.js-ai-alt-text-apply')?.addEventListener('click', onApply);
    document.querySelector('.js-ai-alt-text-again')?.addEventListener('click', onAgain);
};

const onApply = () => {
    const aiAltTextInput = document.getElementById('alt-text-input');
    currentAltTextInput.value = aiAltTextInput.value;
    $('#ai-image-alt-text-modal-loading').modal('hide');
    $('#ai-image-alt-text-modal').modal('hide');
};

const onAgain = () => {
    $('#ai-image-alt-text-modal').modal('hide');
    onOpenAiImageAltTextModalButtonClick();
};

const onOpenAiImageAltTextModalButtonClick = async (event) => {
    $('#ai-image-alt-text-modal-loading').modal('show');
    const target = event?.target || currentAiAltTextButton;
    const parentImageCollection = target.closest('.js-image-collection');
    const imageUrlField = parentImageCollection.querySelector('.js-image-url-small-field');
    const ibox = parentImageCollection.closest('.ibox');
    let locale = ibox.querySelector('.ibox-title h5').innerText;
    locale = locale.toLowerCase() === 'default' ? 'en_US' : locale;
    currentAltTextInput = parentImageCollection.querySelector('.js-image-alt-text-field');

    if (!imageUrlField.value) {
        return;
    }

    await sendData(locale, imageUrlField.value);
};

const fillModalWithSuggestedAltText = (data) => {
    const modalBody = document.querySelector('.js-alt-text-modal-body');
    const input = document.createElement('input');

    input.setAttribute('type', 'text');
    input.setAttribute('class', 'form-control');
    input.id = 'alt-text-input';
    input.value = decodeURI(data.altText);
    modalBody.innerHTML = '';
    modalBody.append(input);
    $('#ai-image-alt-text-modal-loading').modal('hide');
    $('#ai-image-alt-text-modal').modal('show');
};

const sendData = async (locale, imageUrl) => {
    const response = await fetch(
        `/product-image-alt-text-ai-generator/generate?locale=${locale}&imageUrl=${imageUrl}`,
        {
            method: 'POST',
        },
    )
        .then((response) => response.json())
        .then((data) => {
            return fillModalWithSuggestedAltText(data);
        });
};

const spinnerTemplate = () => {
    return `
    <span class="spinner">
        <span class="spinner__inner spinner__inner--top"></span>
        <span class="spinner__inner spinner__inner--bottom"></span>
    </span>
    `;
};
const modalTemplates = () => {
    return `
        <div id="ai-image-alt-text-modal-loading" class="modal modal--secondary fade" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">AI assistant</h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal-loading">
                            ${spinnerTemplate()}
                            <span>Looking for image alt text</span>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <div id="ai-image-alt-text-modal" class="modal modal--secondary fade" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">AI assistant</h4>
                    </div>
                    <div class="modal-body js-alt-text-modal-body">
                    </div>
                    <div class="modal-footer">
                        <div class="modal-actions">
                            <button type="button" class="js-ai-alt-text-again btn btn-outline btn-view">Try again</button>
                            <button type="button" class="js-ai-alt-text-apply btn" data-dismiss="modal">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
};
