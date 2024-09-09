<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerEco\Zed\ProductManagementAi;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductManagementAiConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const OPEN_AI_GPT4O_MINI_MODEL = 'gpt-4o-mini';

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
}
