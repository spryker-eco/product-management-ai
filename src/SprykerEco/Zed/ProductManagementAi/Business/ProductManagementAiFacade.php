<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use Generated\Shared\Transfer\CategorySuggestionRequestTransfer;
use Generated\Shared\Transfer\CategorySuggestionResponseTransfer;
use Generated\Shared\Transfer\ContentImproverRequestTransfer;
use Generated\Shared\Transfer\ContentImproverResponseTransfer;
use Generated\Shared\Transfer\ImageAltTextRequestTransfer;
use Generated\Shared\Transfer\ImageAltTextResponseTransfer;
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
     * @param \Generated\Shared\Transfer\CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\CategorySuggestionResponseTransfer
     */
    public function proposeCategorySuggestions(
        CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
    ): CategorySuggestionResponseTransfer {
        return $this->getFactory()
            ->createCategoryProposer()
            ->proposeCategorySuggestions($categorySuggestionRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ImageAltTextRequestTransfer $imageAltTextRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ImageAltTextResponseTransfer
     */
    public function generateImageAltText(
        ImageAltTextRequestTransfer $imageAltTextRequestTransfer
    ): ImageAltTextResponseTransfer {
        return $this->getFactory()
            ->createImageAltTextGenerator()
            ->generateImageAltText($imageAltTextRequestTransfer);
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

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ContentImproverResponseTransfer
     */
    public function improveContent(ContentImproverRequestTransfer $contentImproverRequestTransfer): ContentImproverResponseTransfer
    {
        return $this->getFactory()
            ->createContentImprover()
            ->improveContent($contentImproverRequestTransfer);
    }
}
