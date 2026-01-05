<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Proposer;

use Exception;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class CategoryProposer implements CategoryProposerInterface
{
    use LoggerTrait;

    /**
     * @var \Spryker\Client\AiFoundation\AiFoundationClientInterface
     */
    protected AiFoundationClientInterface $aiFoundationClient;

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
     * @param \Spryker\Client\AiFoundation\AiFoundationClientInterface $aiFoundationClient
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface $utilEncodingService
     * @param \SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface $categoryReader
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(
        AiFoundationClientInterface $aiFoundationClient,
        ProductManagementAiToUtilEncodingServiceInterface $utilEncodingService,
        CategoryReaderInterface $categoryReader,
        ProductManagementAiConfig $productManagementAiConfig
    ) {
        $this->aiFoundationClient = $aiFoundationClient;
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

        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setPromptMessage(
                (new PromptMessageTransfer())
                    ->setContent($this->generatePrompt($productName, $description, $categories)),
            );

        try {
            $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);
        } catch (Exception $exception) {
            $this->getLogger()->critical($exception->getMessage(), $exception->getTrace());

            return [];
        }

        $proposedCategories = $this->utilEncodingService->decodeJson(
            $promptResponseTransfer->getMessage()->getContent(),
            true,
        );

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
