<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Builder;

use Locale;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class PromptBuilder implements PromptBuilderInterface
{
    /**
     * @var \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig
     */
    protected ProductManagementAiConfig $productManagementAiConfig;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(ProductManagementAiConfig $productManagementAiConfig)
    {
        $this->productManagementAiConfig = $productManagementAiConfig;
    }

    /**
     * @param string $imageUrl
     * @param string $targetLocale
     *
     * @return array<array<string, mixed>>
     */
    public function buildImageAltTextPrompt(string $imageUrl, string $targetLocale): array
    {
        return [
            [
                'type' => 'text',
                'text' => $this->productManagementAiConfig->getImageAltTextPrompt(Locale::getDisplayLanguage($targetLocale, 'en_US')),
            ],
            [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $imageUrl,
                ],
            ],
        ];
    }
}
