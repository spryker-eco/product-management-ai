import { modalTemplates } from "./templates";

let locales = [];
let languageindicators = [];

$(document).ready(function () {
    languageindicators = Array.from(document.querySelectorAll('#tab-content-general .flag'));
    getLocales();
    preInit();
    appendModals();
    textAreaCopyIcon();
    translateInit();
});

const getLocales = () => {
    languageindicators.forEach(indicator => {
        locales.push(formatLocale(indicator.parentElement.innerText.trim('\n').trim()))
    })
}

const formatLocale = (locale) => {
    let localeArray = locale.toLowerCase().split('_');
    localeArray[localeArray.length - 1] = localeArray[localeArray.length - 1].toUpperCase();
    return localeArray.join('_');
}

const appendModals = () => {
    const modalWrapper = document.createElement('div');
    modalWrapper.innerHTML = modalTemplates();
    document.querySelector('body').append(modalWrapper);
}

const preInit = ()=> {
    let inputs = [];

    languageindicators.map(indicator => {
        const boxContent = indicator.closest('.ibox').getElementsByClassName('ibox-content')[0];
        inputs = Array.from(boxContent.querySelectorAll('input, textarea'));

        handleInputs(inputs, indicator.parentElement.innerText.replace(/^\s+|\s+$/g, '').trim());
    })
}

const handleInputs = (inputs, locale)=> {
    inputs.map(input => {
        const currentLocale = formatLocale(locale);
        const inputId = input.getAttribute('id');
        const inputNameArray = inputId.toLowerCase().split(`_${currentLocale}_`);
        const inputName = inputNameArray[inputNameArray.length - 1];

        input.closest('.form-group').classList.add('js-translator-group', 'form-group-locale');
        input.setAttribute('data-locale', currentLocale);
        input.setAttribute('data-translation-key', inputName);
        input.classList.add('name-translation', 'js-translator-from');
    })
}

const translate = async (payload) => {
    const { locale, text, cache } = payload;

    const data = new FormData();

    data.append('text', text);
    data.append('locale', locale);

    if (cache) {
        data.append('invalidate_cache', 1);
    }

    const response = await fetch('/product-management-ai/translate', {
        method: 'POST',
        body: data,
    });

    return response.json();
};

const showTranslator = () => {
    const determinate = document.querySelector('.js-translate-determinate');

    if (!determinate?.classList.contains('cancel')) {
        $('#modal-translator').modal('show');
    }

    determinate?.classList.remove('cancel');
};

const onTranslateClick = async (event) => {
    event.preventDefault();
    const { target } = event;
    const parent = target.closest('.js-translator-group');
    const field = parent.querySelector('.js-translator-from');
    const label = parent.querySelector('label').textContent.trim();
    const to = target.dataset.locale;
    const from = field.dataset.locale;
    const text = field.value;
    const tag = field.tagName.toLowerCase();

    if (!text) {
        parent.classList.add('has-error');
        parent.classList.add('has-error-translator');

        return;
    }

    parent.classList.remove('has-error');
    parent.classList.remove('has-error-translator');

    $('#modal-loading').modal('show');

    const [{ translation }] = await Promise.all([
        translate({ locale: to, text }),
        new Promise((r) => setTimeout(r, 500)),
    ]);
    const typeNode = document.querySelector('.js-translate-type');

    document.querySelector('.js-translate-label').innerHTML = label;
    document.querySelector('.js-translate-to').innerHTML = to;
    typeNode.setAttribute('data-type', tag);
    typeNode.setAttribute('data-copy-name', field.name.replace(from, to));
    typeNode.setAttribute('data-from-value', text);
    document.querySelector(`${tag}.js-translate-field`).value = translation;

    $('#modal-loading').modal('hide');

    showTranslator();
};

const onApply = () => {
    const type = document.querySelector('.js-translate-type').dataset.type;
    const copyName = document.querySelector('.js-translate-type').dataset.copyName;
    const value = document.querySelector(`${type}.js-translate-field`).value;

    document.querySelector(`[name="${copyName}"]`).value = value;
};

const onAgain = async () => {
    $('#modal-translator').modal('hide');

    const text = document.querySelector('.js-translate-type').dataset.fromValue;
    const tag = document.querySelector('.js-translate-type').dataset.type;
    const to = document.querySelector('.js-translate-to').textContent.trim();

    $('#modal-loading').modal('show');
    const { translation } = await translate({ locale: to, text, cache: true });

    document.querySelector(`${tag}.js-translate-field`).value = translation;
    $('#modal-loading').modal('hide');

    showTranslator();
};

const translateInit = () => {
    document.querySelectorAll('.js-translator-link').forEach((link) => {
        link.addEventListener('click', onTranslateClick);
    });

    document.querySelector('.js-translate-apply').addEventListener('click', onApply);

    document.querySelector('.js-translate-determinate')?.addEventListener('click', (event) => {
        event.target.classList.add('cancel');
    });

    document.querySelector('.js-translate-again').addEventListener('click', onAgain);
};

function textAreaCopyIcon() {
    const translationDataAttributeName = 'data-translation-key';
    const textarea = `textarea[${translationDataAttributeName}]`;
    const input = `input[${translationDataAttributeName}]`;
    addTranslationIcon(textarea);
    addTranslationIcon(input);
}

function addTranslationIcon(query) {
    document.querySelectorAll(query).forEach((field) => {
        if(field.getAttribute('type') == 'checkbox'  || !field.dataset.locale) {
            return;
        }

        const parent = field.parentElement;
        const wrapper = document.createElement('div');
        wrapper.classList.add('input-group');
        wrapper.appendChild(field.cloneNode(true));

        wrapper.innerHTML = `
            ${wrapper.innerHTML}
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary" data-style="zoom-in" title="Copy to other languages">
                    <span class="fa fa-copy"></span>
                </button>
            </span>
            ${translatorDropDown(field.dataset.locale).innerHTML}
        `;

        parent.replaceChild(wrapper, field);

        wrapper.querySelector('button').addEventListener('click', (event) => {
            const selector = `textarea[${translationDataAttributeName}="${field.dataset.translationKey}"]`;

            document.querySelectorAll(selector).forEach((_field) => {
                _field.value = event.target.closest('.input-group').querySelector(query).value;
            });

            const icon = event.currentTarget.querySelector('.fa');

            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');

            setTimeout(function () {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
            }, 1000);
        });
    });
}

const translatorDropDown = (boxLocale) => {
    const element = document.createElement('div');
    element.innerHTML = `
        <div class="translator">
            <span class="btn" title="Use AI to assist in editing your products">
                <span class="fa fa-magic"></span>
            </span>

            <ul class="translator__drop">
                <li><span class="translator__text">Translate to:</span></li>

                ${localesList(boxLocale).innerHTML}
            </ul>
        </div>
    `;
    return element;
}

const localesList = (boxLocale)=> {
    let tempList = locales.filter(locale => locale.toLowerCase() !== boxLocale.toLowerCase());
    let localeList = document.createElement('ul');
    localeList.innerHTML = '';

    tempList.map(locale => {
        localeList.innerHTML += `
            <li>
                <a href="#" class="js-translator-link translator__text translator__text--link" data-locale="${locale}">${locale}</a>
            </li>
        `
    })

    return localeList;
}
