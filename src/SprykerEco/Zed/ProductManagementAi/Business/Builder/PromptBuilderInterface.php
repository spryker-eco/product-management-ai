<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Builder;

interface PromptBuilderInterface
{
    /**
     * @param string $imageUrl
     * @param string $targetLocale
     *
     * @return array<array<string, mixed>>
     */
    public function buildImageAltTextPrompt(string $imageUrl, string $targetLocale): array;
}
