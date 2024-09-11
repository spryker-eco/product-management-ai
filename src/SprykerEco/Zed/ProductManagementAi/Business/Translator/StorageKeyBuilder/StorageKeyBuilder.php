<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Translator\StorageKeyBuilder;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;

class StorageKeyBuilder implements StorageKeyBuilderInterface
{
    /**
     * @var string
     */
    protected const KEY_PATTERN = 'ai_translator:%s:%s';

    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return string
     */
    public function buildStorageKey(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): string
    {
        return sprintf(
            static::KEY_PATTERN,
            md5($aiTranslatorRequestTransfer->getTextOrFail()),
            $aiTranslatorRequestTransfer->getTargetLocaleOrFail(),
        );
    }
}
