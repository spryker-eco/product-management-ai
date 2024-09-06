require('../scss/main.scss');
require('select2');

$(document).ready(() => {
    const wrapper = document.querySelector('.js-ai-category-selection');
    const openAiCategoryModalButton = wrapper.querySelector('.js-ai-category-selection__btn');
    appendModals();
    aiCategoriesInit(openAiCategoryModalButton);
});

const appendModals = () => {
    const modalWrapper = document.createElement('div');
    modalWrapper.innerHTML = modalTemplates();
    document.querySelector('body').append(modalWrapper);
};

const aiCategoriesInit = (openAiCategoryModalButton) => {
    openAiCategoryModalButton?.addEventListener('click', onOpenAiCategoryModalButtonClick);

    document.querySelector('.js-ai-category-apply')?.addEventListener('click', onApply);

    document.querySelector('.js-ai-category-again')?.addEventListener('click', onAgain);
};

const onApply = () => {
    const aiSelectedCategoriesDropdown = $('#sugessted-categories');
    const categorySelect = $('.js-ai-category-selection select');
    categorySelect.val(aiSelectedCategoriesDropdown.val());
    categorySelect.trigger('change.select2');
    $('#ai-category-modal-loading').modal('hide');
    $('#ai-category-modal').modal('hide');
};

const onAgain = () => {
    $('#ai-category-modal').modal('hide');
    onOpenAiCategoryModalButtonClick();
};

const onOpenAiCategoryModalButtonClick = async (event) => {
    $('#ai-category-modal-loading').modal('show');

    const productDataWrappers = Array.from(document.querySelectorAll('.js-product-meta-data'));
    let isDataExists = false;
    const data = new URLSearchParams();

    for (const wrapper of productDataWrappers) {
        const productNameInput = wrapper.querySelector('.js-product-name');
        const productDescriptionInput = wrapper.querySelector('.js-product-description');
        const productNameInputValue = productNameInput.value;
        const productDescriptionInputValue = productDescriptionInput.value;

        if (productNameInputValue && productDescriptionInputValue) {
            data.append('product_name', productNameInputValue);
            data.append('product_description', productDescriptionInputValue);
            isDataExists = true;

            break;
        }
    }
    if (isDataExists) {
        await sendData(data);
    } else {
        $('#ai-category-modal-loading').modal('hide');
        $('#ai-category-modal').modal('show');
    }
};

const fillModalWithSuggestedCategories = (suggestedCategories) => {
    const modalBody = document.querySelector('.js-modal-body');
    const categories = suggestedCategories.categories;
    const categoriesDropdown = document.createElement('select');
    categoriesDropdown.setAttribute('multiple', true);
    categoriesDropdown.id = 'sugessted-categories';
    for (const option of Object.keys(categories)) {
        const optionElement = document.createElement('option');
        optionElement.value = categories[option];
        optionElement.textContent = option;
        optionElement.selected = true;
        categoriesDropdown.append(optionElement);
    }
    modalBody.innerHTML = '';
    modalBody.append(categoriesDropdown);
    $(categoriesDropdown).select2({
        dropdownParent: $('.js-modal-body'),
    });
    $('#ai-category-modal-loading').modal('hide');
    $('#ai-category-modal').modal('show');
};

const sendData = async (payload) => {
    if (!payload) {
        return;
    }

    const response = await fetch('/product-management-ai/category-suggestion', {
        method: 'POST',
        body: payload,
    })
        .then((response) => response.json())
        .then((data) => {
            return fillModalWithSuggestedCategories(data);
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
        <div id="ai-category-modal-loading" class="modal modal--secondary fade" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">AI assistant</h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal-loading">
                            ${spinnerTemplate()}
                            <span>Looking for categories</span>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <div id="ai-category-modal" class="modal modal--secondary fade" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">AI assistant</h4>
                    </div>
                    <div class="modal-body js-modal-body">
                        Please fill in the product name and description for at least one locale.
                    </div>
                    <div class="modal-footer">
                        <div class="modal-actions">
                            <button type="button" class="js-ai-category-again btn btn-outline btn-view">Try again</button>
                            <button type="button" class="js-ai-category-apply btn" data-dismiss="modal">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
};
