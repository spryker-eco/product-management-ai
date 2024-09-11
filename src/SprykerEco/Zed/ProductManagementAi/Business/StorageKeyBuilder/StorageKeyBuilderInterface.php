<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\StorageKeyBuilder;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;

interface StorageKeyBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return string
     */
    public function buildStorageKey(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): string;
}
