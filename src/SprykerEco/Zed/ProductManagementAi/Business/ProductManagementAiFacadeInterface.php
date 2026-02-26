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

interface ProductManagementAiFacadeInterface
{
    /**
     * Specification:
     * - Proposes category suggestions for a product using AI Foundation.
     * - Uses structured response format for reliable parsing.
     * - Returns suggestions with error information if AI request fails.
     * - Validates that product name and description are provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\CategorySuggestionResponseTransfer
     */
    public function proposeCategorySuggestions(
        CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
    ): CategorySuggestionResponseTransfer;

    /**
     * Specification:
     * - Generates alt text for an image using AI Foundation.
     * - Uses structured response format for reliable parsing.
     * - Returns alt text with error information if AI request fails.
     * - Validates that image URL and target locale are provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ImageAltTextRequestTransfer $imageAltTextRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ImageAltTextResponseTransfer
     */
    public function generateImageAltText(
        ImageAltTextRequestTransfer $imageAltTextRequestTransfer
    ): ImageAltTextResponseTransfer;

    /**
     * Specification:
     * - Translates a text from source locale to target locale.
     * - Text for translation, source and target locales are provided as properties of `AiTranslatorRequest`.
     * - Returns `AiTranslatorResponse`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    public function translate(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): AiTranslatorResponseTransfer;

    /**
     * Specification:
     * - Improves content text by enhancing clarity, grammar, and structure.
     * - Uses AI Foundation structured response format for reliable parsing.
     * - Returns improved text with error information if AI request fails.
     * - Validates that text is provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ContentImproverResponseTransfer
     */
    public function improveContent(ContentImproverRequestTransfer $contentImproverRequestTransfer): ContentImproverResponseTransfer;
}
