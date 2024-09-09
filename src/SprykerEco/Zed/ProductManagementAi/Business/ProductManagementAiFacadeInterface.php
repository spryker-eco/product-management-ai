<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business;

use Generated\Shared\Transfer\OpenAiChatResponseTransfer;

interface ProductManagementAiFacadeInterface
{
    /**
     * Specification:
     * - propose category suggestions based on product name, description and existing categories
     *
     * @api
     *
     * @param string $productName
     * @param string $description
     *
     * @return array<string, int>
     */
    public function proposeCategorySuggestions(string $productName, string $description): array;

    /**
     * Specification:
     *  - Generates alt text for an image using AI.
     *
     * @api
     *
     * @param string $imageUrl
     * @param string $targetLocale
     *
     * @return \Generated\Shared\Transfer\OpenAiChatResponseTransfer
     */
    public function generateImageAltText(string $imageUrl, string $targetLocale): OpenAiChatResponseTransfer;
}
