<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Translator;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use SprykerEco\Zed\ProductManagementAi\Business\Translator\StorageKeyBuilder\StorageKeyBuilderInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToStorageClientInterface;

class TranslatorCacheAware implements TranslatorInterface
{
    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Business\Translator\TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToStorageClientInterface
     */
    protected ProductManagementAiToStorageClientInterface $storageClient;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Business\Translator\StorageKeyBuilder\StorageKeyBuilderInterface
     */
    protected StorageKeyBuilderInterface $storageKeyBuilder;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Business\Translator\TranslatorInterface $translator
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToStorageClientInterface $storageClient
     * @param \SprykerEco\Zed\ProductManagementAi\Business\Translator\StorageKeyBuilder\StorageKeyBuilderInterface $storageKeyBuilder
     */
    public function __construct(
        TranslatorInterface $translator,
        ProductManagementAiToStorageClientInterface $storageClient,
        StorageKeyBuilderInterface $storageKeyBuilder
    ) {
        $this->translator = $translator;
        $this->storageClient = $storageClient;
        $this->storageKeyBuilder = $storageKeyBuilder;
    }

    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    public function translate(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): AiTranslatorResponseTransfer
    {
        $cacheKey = $this->storageKeyBuilder->buildStorageKey($aiTranslatorRequestTransfer);
        if (!$aiTranslatorRequestTransfer->getInvalidateCache()) {
            $cachedData = $this->storageClient->get($cacheKey);

            if ($cachedData !== null) {
                return (new AiTranslatorResponseTransfer())->fromArray($cachedData);
            }
        }

        $aiTranslatorResponseTransfer = $this->translator->translate($aiTranslatorRequestTransfer);
        $this->storageClient->set($cacheKey, json_encode($aiTranslatorResponseTransfer->toArray()));

        return $aiTranslatorResponseTransfer;
    }
}
