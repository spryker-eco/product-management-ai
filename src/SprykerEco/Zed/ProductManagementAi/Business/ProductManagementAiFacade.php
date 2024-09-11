<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use Generated\Shared\Transfer\OpenAiChatResponseTransfer;
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
     * @return \Generated\Shared\Transfer\OpenAiChatResponseTransfer
     */
    public function generateImageAltText(string $imageUrl, string $targetLocale): OpenAiChatResponseTransfer
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
            ->getTranslator()
            ->translate($aiTranslatorRequestTransfer);
    }
}
