<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
