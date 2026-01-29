<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Proposer;

use Exception;
use Generated\Shared\Transfer\CategorySuggestionRequestTransfer;
use Generated\Shared\Transfer\CategorySuggestionResponseTransfer;
use Generated\Shared\Transfer\CategorySuggestionStructuredTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class CategoryProposer implements CategoryProposerInterface
{
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
     * @param \Generated\Shared\Transfer\CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\CategorySuggestionResponseTransfer
     */
    public function proposeCategorySuggestions(
        CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
    ): CategorySuggestionResponseTransfer {
        $categorySuggestionResponseTransfer = new CategorySuggestionResponseTransfer();

        $categories = $this->categoryReader->getCategories();
        if (!count($categories)) {
            return $categorySuggestionResponseTransfer->setIsSuccessful(true);
        }

        $promptContent = $this->generatePrompt(
            $categorySuggestionRequestTransfer->getProductNameOrFail(),
            $categorySuggestionRequestTransfer->getProductDescriptionOrFail(),
            $categories,
        );

        $structuredSchema = new CategorySuggestionStructuredTransfer();

        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setPromptMessage(
                (new PromptMessageTransfer())->setContent($promptContent),
            )
            ->setStructuredMessage($structuredSchema)
            ->setMaxRetries(3);

        $aiConfigurationName = $this->productManagementAiConfig->getCategorySuggestionAiConfigurationName();
        if ($aiConfigurationName !== null) {
            $promptRequestTransfer->setAiConfigurationName($aiConfigurationName);
        }

        try {
            $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);

            return $this->mapPromptResponseToCategorySuggestionResponse(
                $promptResponseTransfer,
                $categorySuggestionResponseTransfer,
            );
        } catch (Exception $exception) {
            return $categorySuggestionResponseTransfer
                ->setIsSuccessful(false)
                ->addError(
                    (new ErrorTransfer())
                        ->setMessage($exception->getMessage()),
                );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PromptResponseTransfer $promptResponseTransfer
     * @param \Generated\Shared\Transfer\CategorySuggestionResponseTransfer $categorySuggestionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CategorySuggestionResponseTransfer
     */
    protected function mapPromptResponseToCategorySuggestionResponse(
        PromptResponseTransfer $promptResponseTransfer,
        CategorySuggestionResponseTransfer $categorySuggestionResponseTransfer
    ): CategorySuggestionResponseTransfer {
        $categorySuggestionResponseTransfer->setIsSuccessful($promptResponseTransfer->getIsSuccessful());

        foreach ($promptResponseTransfer->getErrors() as $errorTransfer) {
            $categorySuggestionResponseTransfer->addError($errorTransfer);
        }

        if (!$promptResponseTransfer->getIsSuccessful()) {
            return $categorySuggestionResponseTransfer;
        }

        $structuredMessage = $promptResponseTransfer->getStructuredMessage();
        if ($structuredMessage instanceof CategorySuggestionStructuredTransfer) {
            foreach ($structuredMessage->getCategories() as $categorySuggestionItemTransfer) {
                $categorySuggestionResponseTransfer->addSuggestion($categorySuggestionItemTransfer);
            }
        }

        return $categorySuggestionResponseTransfer;
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
