<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Builder;

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
                'text' => $this->productManagementAiConfig->getImageAltTextPrompt($targetLocale),
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
