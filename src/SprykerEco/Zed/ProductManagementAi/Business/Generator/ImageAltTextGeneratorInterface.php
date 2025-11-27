<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Generator;

use Generated\Shared\Transfer\PromptResponseTransfer;

interface ImageAltTextGeneratorInterface
{
    /**
     * @param string $imageUrl
     * @param string $targetLocale
     *
     * @return \Generated\Shared\Transfer\PromptResponseTransfer
     */
    public function generateImageAltText(string $imageUrl, string $targetLocale): PromptResponseTransfer;
}
