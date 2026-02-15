<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Proposer;

use ArrayObject;
use Exception;
use Generated\Shared\Transfer\CategorySuggestionRequestTransfer;
use Generated\Shared\Transfer\CategorySuggestionResponseTransfer;
use Generated\Shared\Transfer\CategorySuggestionStructuredTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use InvalidArgumentException;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class CategoryProposer implements CategoryProposerInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const string OPERATION_NAME = 'category suggestion';

    /**
     * @var \Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface
     */
    protected AiFoundationFacadeInterface $aiFoundationFacade;

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
     * @param \Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface $aiFoundationFacade
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface $utilEncodingService
     * @param \SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface $categoryReader
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(
        AiFoundationFacadeInterface $aiFoundationFacade,
        ProductManagementAiToUtilEncodingServiceInterface $utilEncodingService,
        CategoryReaderInterface $categoryReader,
        ProductManagementAiConfig $productManagementAiConfig
    ) {
        $this->aiFoundationFacade = $aiFoundationFacade;
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

        $promptRequestTransfer = $this->buildPromptRequest($categorySuggestionRequestTransfer, $categories);

        try {
            $promptResponseTransfer = $this->aiFoundationFacade->prompt($promptRequestTransfer);
        } catch (InvalidArgumentException $exception) {
            $this->logPromptError($exception, $promptRequestTransfer);

            $promptResponseTransfer = $this->createErrorResponse(
                $this->productManagementAiConfig->getErrorCodeAiProviderConfigMissing(),
                sprintf(
                    $this->productManagementAiConfig->getErrorMessageAiProviderConfigMissingTemplate(),
                    static::OPERATION_NAME,
                ),
            );

            return $this->mapPromptResponseToCategorySuggestionResponse(
                $promptResponseTransfer,
                $categorySuggestionResponseTransfer,
            );
            // @phpstan-ignore-next-line catch.neverThrown - AI provider can throw other exceptions
        } catch (Exception $exception) {
            $this->logPromptError($exception, $promptRequestTransfer);

            $promptResponseTransfer = $this->createErrorResponse(
                $this->productManagementAiConfig->getErrorCodeAiProviderRequestError(),
                sprintf(
                    $this->productManagementAiConfig->getErrorMessageAiProviderRequestErrorTemplate(),
                    static::OPERATION_NAME,
                ),
            );

            return $this->mapPromptResponseToCategorySuggestionResponse(
                $promptResponseTransfer,
                $categorySuggestionResponseTransfer,
            );
        }

        if ($promptResponseTransfer->getIsSuccessful() === false) {
            $promptResponseTransfer = $this->handleUnsuccessfulResponse($promptResponseTransfer, $promptRequestTransfer);
        }

        return $this->mapPromptResponseToCategorySuggestionResponse(
            $promptResponseTransfer,
            $categorySuggestionResponseTransfer,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
     * @param array<string, int> $categories
     *
     * @return \Generated\Shared\Transfer\PromptRequestTransfer
     */
    protected function buildPromptRequest(
        CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer,
        array $categories
    ): PromptRequestTransfer {
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

        return $promptRequestTransfer;
    }

    /**
     * @param \Exception $exception
     * @param \Generated\Shared\Transfer\PromptRequestTransfer $promptRequestTransfer
     *
     * @return void
     */
    protected function logPromptError(Exception $exception, PromptRequestTransfer $promptRequestTransfer): void
    {
        $this->getLogger()->error($exception->getMessage(), [
            'exception' => $exception,
            'prompt' => $promptRequestTransfer->toArray(),
        ]);
    }

    /**
     * @param string $errorCode
     * @param string $errorMessage
     *
     * @return \Generated\Shared\Transfer\PromptResponseTransfer
     */
    protected function createErrorResponse(string $errorCode, string $errorMessage): PromptResponseTransfer
    {
        return (new PromptResponseTransfer())
            ->setIsSuccessful(false)
            ->addError(
                (new ErrorTransfer())
                    ->setParameters(['code' => $errorCode])
                    ->setMessage($errorMessage),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PromptResponseTransfer $promptResponseTransfer
     * @param \Generated\Shared\Transfer\PromptRequestTransfer $promptRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PromptResponseTransfer
     */
    protected function handleUnsuccessfulResponse(
        PromptResponseTransfer $promptResponseTransfer,
        PromptRequestTransfer $promptRequestTransfer
    ): PromptResponseTransfer {
        $errors = $promptResponseTransfer->getErrors();

        foreach ($errors as $error) {
            $this->getLogger()->error($error->getMessage() ?? '', [
                'prompt' => $promptRequestTransfer->toArray(),
                'response' => $promptResponseTransfer->toArray(),
            ]);
        }

        $promptResponseTransfer->setErrors(new ArrayObject([
            (new ErrorTransfer())
                ->setParameters(['code' => $this->productManagementAiConfig->getErrorCodeAiProviderRequestError()])
                ->setMessage(sprintf(
                    $this->productManagementAiConfig->getErrorMessageAiProviderRequestErrorTemplate(),
                    static::OPERATION_NAME,
                )),
        ]));

        return $promptResponseTransfer;
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
