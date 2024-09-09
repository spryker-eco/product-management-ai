<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Category;

use Generated\Shared\Transfer\OpenAiChatRequestTransfer;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface;

class CategoryProposer implements CategoryProposerInterface
{
    /**
     * @var string
     */
    protected const PRODUCT_CATEGORY_SUGGESTION_PROMPT = 'Based on the provided product name and description, suggest the most fitting product categories from the existing categories list for optimal placement in an e-commerce store.
        Product name: %s
        Product description: %s
        Existing categories:
        %s

        Provide only the suggested categories as your output as key and value';

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface
     */
    protected ProductManagementAiToOpenAiClientInterface $openAiClient;

    /**
     * @var \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    protected UtilEncodingServiceInterface $utilEncodingService;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryReaderInterface
     */
    protected CategoryReaderInterface $categoryReader;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface $openAiClient
     * @param \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface $utilEncodingService
     * @param \SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryReaderInterface $categoryReader
     */
    public function __construct(
        ProductManagementAiToOpenAiClientInterface $openAiClient,
        UtilEncodingServiceInterface $utilEncodingService,
        CategoryReaderInterface $categoryReader
    ) {
        $this->openAiClient = $openAiClient;
        $this->utilEncodingService = $utilEncodingService;
        $this->categoryReader = $categoryReader;
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

        if (!$openAiChatResponseTransfer->getMessage()) {
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

        return sprintf(static::PRODUCT_CATEGORY_SUGGESTION_PROMPT, $productName, $description, $categories);
    }
}
