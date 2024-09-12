<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Proposer;

use Generated\Shared\Transfer\OpenAiChatRequestTransfer;
use SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class CategoryProposer implements CategoryProposerInterface
{
    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface
     */
    protected ProductManagementAiToOpenAiClientInterface $openAiClient;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface
     */
    protected ProductManagementAiToUtilEncodingServiceInterface $utilEncodingService;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface
     */
    protected CategoryReaderInterface $categoryReader;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig
     */
    protected ProductManagementAiConfig $productManagementAiConfig;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface $openAiClient
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface $utilEncodingService
     * @param \SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface $categoryReader
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(
        ProductManagementAiToOpenAiClientInterface $openAiClient,
        ProductManagementAiToUtilEncodingServiceInterface $utilEncodingService,
        CategoryReaderInterface $categoryReader,
        ProductManagementAiConfig $productManagementAiConfig
    ) {
        $this->openAiClient = $openAiClient;
        $this->utilEncodingService = $utilEncodingService;
        $this->categoryReader = $categoryReader;
        $this->productManagementAiConfig = $productManagementAiConfig;
    }

    /**
     * @param string $productName
     * @param string $description
     *
     * @return array<string, int>
     */
    public function proposeCategorySuggestions(string $productName, string $description): array
    {
        $categories = $this->categoryReader->getCategories();
        if (!count($categories)) {
            return [];
        }

        $openAiChatRequestTransfer = (new OpenAiChatRequestTransfer())
            ->setMessage($this->generatePrompt($productName, $description, $categories));

        $openAiChatResponseTransfer = $this->openAiClient->chat($openAiChatRequestTransfer);

        if (!$openAiChatResponseTransfer->getMessage() || !$openAiChatResponseTransfer->getIsSuccessful()) {
            return [];
        }

        $proposedCategories = $this->utilEncodingService->decodeJson($openAiChatResponseTransfer->getMessage(), true);

        return is_array($proposedCategories) ? $proposedCategories : [];
    }

    /**
     * @param string $productName
     * @param string $description
     * @param array<string, int> $categories
     *
     * @return string
     */
    protected function generatePrompt(string $productName, string $description, array $categories): string
    {
        $categories = $this->utilEncodingService->encodeJson($categories);

        return sprintf(
            $this->productManagementAiConfig->getProductCategorySuggestionPromptTemplate(),
            $productName,
            $description,
            $categories,
        );
    }
}
