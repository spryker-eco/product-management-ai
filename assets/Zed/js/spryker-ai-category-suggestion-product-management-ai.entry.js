import('../scss/main.scss');
import { AiCategorySuggestion } from './ai-category-suggestion';
import { AiImageAltText } from './ai-image-alt-text';
import { AiTranslation } from './ai-translation';

document.addEventListener('DOMContentLoaded', () => {
    new AiCategorySuggestion().init();
    new AiImageAltText().init();
    new AiTranslation().init();
});
