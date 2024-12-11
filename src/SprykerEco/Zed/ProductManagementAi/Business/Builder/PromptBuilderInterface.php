<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
