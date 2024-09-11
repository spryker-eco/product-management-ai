<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\ProductManagementAi\ProductManagementAiConstants;

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
     * @api
     *
     * @param string $locale
     *
     * @return string
     */
    public function getImageAltTextPrompt(string $locale): string
    {
        return sprintf(
            'Describe the most important characteristics of the main object you can identify in the image e.g. manufacturer, model, color, part number or any identification number that help me to define the HTML alt text for best SEO using the language %s. Your output must be ony a list of the product attributes. Remove the attribute names and keep only the values from your output and provide them in a single line.',
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
     * - Returns the default source locale for translator.
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
     * @api
     *
     * @return bool
     */
    public function isTranslatorCacheEnabled(): bool
    {
        return $this->get(ProductManagementAiConstants::ENABLE_TRANSLATION_CACHE, false);
    }
}
