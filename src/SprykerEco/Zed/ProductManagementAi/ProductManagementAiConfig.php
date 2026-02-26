<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductManagementAiConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const DEFAULT_TRANSLATION_SOURCE_LOCALE = 'en_US';

    /**
     * @var string
     */
    protected const AI_TRANSLATION_PROMPT_TEMPLATE = 'Support me in translating the following text `%s` from %s locale to %s locale(s) for an online shop, ensuring native speaker fluency.
        Generate accurate and contextually fitting translations to enhance the user experience.
        The texts to be translated may contain URLs, URL paths, HTML, unicode characters or some word enclosed by the character "%%", please don\'t translate them.';

    /**
     * @var string
     */
    protected const PRODUCT_CATEGORY_SUGGESTION_PROMPT_TEMPLATE = 'Based on the provided product name and description, suggest the most fitting product categories from the existing categories list for optimal placement in an e-commerce store.
        Product name: %s
        Product description: %s
        Existing categories in format {"categoryName": "categoryId", ...}:
        %s';

    /**
     * @var string
     */
    protected const CONTENT_IMPROVER_PROMPT_TEMPLATE = 'Improve the following text for an e-commerce product by enhancing clarity, grammar, structure, and readability while maintaining the original meaning and tone.
        Make it more professional and engaging for potential customers.
        Text to improve: %s';

    /**
     * @var string
     */
    protected const string ERROR_CODE_AI_PROVIDER_CONFIG_MISSING = 'AI_PROVIDER_CONFIG_MISSING';

    /**
     * @var string
     */
    protected const string ERROR_CODE_AI_PROVIDER_REQUEST_ERROR = 'AI_PROVIDER_REQUEST_ERROR';

    /**
     * @var string
     */
    protected const string ERROR_MESSAGE_AI_PROVIDER_CONFIG_MISSING_TEMPLATE = 'AI %s is not available because the AI provider is not configured.';

    /**
     * @var string
     */
    protected const string ERROR_MESSAGE_AI_PROVIDER_REQUEST_ERROR_TEMPLATE = 'AI %s is not available because an error occurred while trying to reach out to the AI provider.';

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
            'Describe the most important characteristics of the main object you can identify in the image e.g. manufacturer, model, color, part number or any identification number that help me to define the HTML alt text for best SEO using the language from locale %s.',
            $locale,
        );
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

    /**
     * Specification:
     * - Returns AI configuration name for category suggestions defined in \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATIONS.
     * - Returns null to use default AI configuration.
     * - If null is returned, the default AI configuration will be used \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATION_DEFAULT
     *
     * @api
     *
     * @return string|null
     */
    public function getCategorySuggestionAiConfigurationName(): ?string
    {
        return null;
    }

    /**
     * Specification:
     * - Returns AI configuration name for translation defined in \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATIONS.
     * - Returns null to use default AI configuration.
     * - If null is returned, the default AI configuration will be used \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATION_DEFAULT
     *
     * @api
     *
     * @return string|null
     */
    public function getTranslationAiConfigurationName(): ?string
    {
        return null;
    }

    /**
     * Specification:
     * - Returns AI configuration name for image alt text generation defined in \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATIONS.
     * - Returns null to use default AI configuration.
     * - If null is returned, the default AI configuration will be used \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATION_DEFAULT
     *
     * @api
     *
     * @return string|null
     */
    public function getImageAltTextAiConfigurationName(): ?string
    {
        return null;
    }

    /**
     * Specification:
     * - Returns prompt template for AI content improvement.
     *
     * @api
     *
     * @return string
     */
    public function getContentImproverPromptTemplate(): string
    {
        return static::CONTENT_IMPROVER_PROMPT_TEMPLATE;
    }

    /**
     * Specification:
     * - Returns AI configuration name for content improvement defined in \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATIONS.
     * - Returns null to use default AI configuration.
     * - If null is returned, the default AI configuration will be used \Spryker\Shared\AiFoundation\AiFoundationConstants::AI_CONFIGURATION_DEFAULT
     *
     * @api
     *
     * @return string|null
     */
    public function getContentImproverAiConfigurationName(): ?string
    {
        return null;
    }

    /**
     * Specification:
     * - Returns error code for missing AI provider configuration.
     *
     * @api
     *
     * @return string
     */
    public function getErrorCodeAiProviderConfigMissing(): string
    {
        return static::ERROR_CODE_AI_PROVIDER_CONFIG_MISSING;
    }

    /**
     * Specification:
     * - Returns error code for AI provider request errors.
     *
     * @api
     *
     * @return string
     */
    public function getErrorCodeAiProviderRequestError(): string
    {
        return static::ERROR_CODE_AI_PROVIDER_REQUEST_ERROR;
    }

    /**
     * Specification:
     * - Returns error message template for missing AI provider configuration.
     * - Use sprintf with operation name to generate the final message.
     *
     * @api
     *
     * @return string
     */
    public function getErrorMessageAiProviderConfigMissingTemplate(): string
    {
        return static::ERROR_MESSAGE_AI_PROVIDER_CONFIG_MISSING_TEMPLATE;
    }

    /**
     * Specification:
     * - Returns error message template for AI provider request errors.
     * - Use sprintf with operation name to generate the final message.
     *
     * @api
     *
     * @return string
     */
    public function getErrorMessageAiProviderRequestErrorTemplate(): string
    {
        return static::ERROR_MESSAGE_AI_PROVIDER_REQUEST_ERROR_TEMPLATE;
    }
}
