<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductManagementAiConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const OPEN_AI_GPT4O_MINI_MODEL = 'gpt-4o-mini';

    /**
     * @var string
     */
    protected const DEFAULT_TRANSLATION_SOURCE_LOCALE = 'en_US';

    /**
     * @var string
     */
    protected const AI_TRANSLATION_PROMPT_TEMPLATE = 'Support me in translating the following text `%s` from %s locale to %s locale(s) for an online shop, ensuring native speaker fluency.
        Generate accurate and contextually fitting translations to enhance the user experience.
        The texts to be translated may contain URLs, URL paths, HTML, unicode characters or some word enclosed by the character "%%", please don\'t translate them.
        IMPORTANT: ONLY RETURN THE TRANSLATED TEXT AND NOTHING ELSE.';

    /**
     * @var string
     */
    protected const PRODUCT_CATEGORY_SUGGESTION_PROMPT_TEMPLATE = 'Based on the provided product name and description, suggest the most fitting product categories from the existing categories list for optimal placement in an e-commerce store.
        Product name: %s
        Product description: %s
        Existing categories:
        %s

        Provide only the suggested categories as your output as key and value';

    /**
     * @api
     *
     * @param string $locale
     *
     * @return string
     */
    public function getImageAltTextPrompt(string $locale): string
    {
        return sprintf(
            'Describe the most important characteristics of the main object you can identify in the image e.g. manufacturer, model, color, part number or any identification number that help me to define the HTML alt text for best SEO using the language from locale %s. Your output must be ony a list of the product attributes. Remove the attribute names and keep only the values from your output and provide them in a single line.',
            $locale,
        );
    }

    /**
     * @api
     *
     * @return string
     */
    public function getOpenAiGpt4oMiniModel(): string
    {
        return static::OPEN_AI_GPT4O_MINI_MODEL;
    }

    /**
     * Specification:
     * - Returns the default source locale for translator.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultTranslationSourceLocale(): string
    {
        return static::DEFAULT_TRANSLATION_SOURCE_LOCALE;
    }

    /**
     * Specification:
     * - Returns prompt template for AI translation.
     *
     * @api
     *
     * @return string
     */
    public function getAiTranslationPromptTemplate(): string
    {
        return static::AI_TRANSLATION_PROMPT_TEMPLATE;
    }

    /**
     * Specification:
     * - Returns prompt template for AI product category suggestion.
     *
     * @api
     *
     * @return string
     */
    public function getProductCategorySuggestionPromptTemplate(): string
    {
        return static::PRODUCT_CATEGORY_SUGGESTION_PROMPT_TEMPLATE;
    }
}
