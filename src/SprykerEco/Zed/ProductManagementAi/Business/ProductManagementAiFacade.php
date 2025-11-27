<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiBusinessFactory getFactory()
 */
class ProductManagementAiFacade extends AbstractFacade implements ProductManagementAiFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $productName
     * @param string $description
     *
     * @return array<string, int>
     */
    public function proposeCategorySuggestions(string $productName, string $description): array
    {
        return $this->getFactory()
            ->createCategoryProposer()
            ->proposeCategorySuggestions($productName, $description);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $imageUrl
     * @param string $targetLocale
     *
     * @return \Generated\Shared\Transfer\PromptResponseTransfer
     */
    public function generateImageAltText(string $imageUrl, string $targetLocale): PromptResponseTransfer
    {
        return $this->getFactory()
            ->createImageAltTextGenerator()
            ->generateImageAltText($imageUrl, $targetLocale);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    public function translate(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): AiTranslatorResponseTransfer
    {
        return $this->getFactory()
            ->createTranslator()
            ->translate($aiTranslatorRequestTransfer);
    }
}
