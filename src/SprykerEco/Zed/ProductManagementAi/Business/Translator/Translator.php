<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Translator;

use ArrayObject;
use Exception;
use Generated\Shared\Transfer\AiTranslationStructuredTransfer;
use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use InvalidArgumentException;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class Translator implements TranslatorInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const string OPERATION_NAME = 'translation';

    /**
     * @var \Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface
     */
    protected AiFoundationFacadeInterface $aiFoundationFacade;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig
     */
    protected ProductManagementAiConfig $productManagementAiConfig;

    /**
     * @param \Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface $aiFoundationFacade
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(
        AiFoundationFacadeInterface $aiFoundationFacade,
        ProductManagementAiConfig $productManagementAiConfig
    ) {
        $this->aiFoundationFacade = $aiFoundationFacade;
        $this->productManagementAiConfig = $productManagementAiConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    public function translate(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): AiTranslatorResponseTransfer
    {
        $aiTranslatorRequestTransfer = $this->normalizeSourceLocale($aiTranslatorRequestTransfer);

        $promptRequestTransfer = $this->buildPromptRequest($aiTranslatorRequestTransfer);

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

            return $this->mapPromptResponseToTranslatorResponse(
                $promptResponseTransfer,
                $aiTranslatorRequestTransfer,
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

            return $this->mapPromptResponseToTranslatorResponse(
                $promptResponseTransfer,
                $aiTranslatorRequestTransfer,
            );
        }

        if ($promptResponseTransfer->getIsSuccessful() === false) {
            $promptResponseTransfer = $this->handleUnsuccessfulResponse($promptResponseTransfer, $promptRequestTransfer);
        }

        return $this->mapPromptResponseToTranslatorResponse(
            $promptResponseTransfer,
            $aiTranslatorRequestTransfer,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PromptRequestTransfer
     */
    protected function buildPromptRequest(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): PromptRequestTransfer
    {
        $promptContent = $this->buildTranslationRequestPrompt($aiTranslatorRequestTransfer);
        $structuredSchema = new AiTranslationStructuredTransfer();

        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setPromptMessage(
                (new PromptMessageTransfer())->setContent($promptContent),
            )
            ->setStructuredMessage($structuredSchema)
            ->setMaxRetries(3);

        $aiConfigurationName = $this->productManagementAiConfig->getTranslationAiConfigurationName();
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
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorRequestTransfer
     */
    protected function normalizeSourceLocale(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): AiTranslatorRequestTransfer
    {
        $sourceLocale = $aiTranslatorRequestTransfer->getSourceLocale() ?? $this->productManagementAiConfig->getDefaultTranslationSourceLocale();
        $aiTranslatorRequestTransfer->setSourceLocale($sourceLocale);

        return $aiTranslatorRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return string
     */
    protected function buildTranslationRequestPrompt(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): string
    {
        return sprintf(
            $this->productManagementAiConfig->getAiTranslationPromptTemplate(),
            $aiTranslatorRequestTransfer->getTextOrFail(),
            $aiTranslatorRequestTransfer->getSourceLocaleOrFail(),
            $aiTranslatorRequestTransfer->getTargetLocaleOrFail(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PromptResponseTransfer $promptResponseTransfer
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    protected function mapPromptResponseToTranslatorResponse(
        PromptResponseTransfer $promptResponseTransfer,
        AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
    ): AiTranslatorResponseTransfer {
        $aiTranslatorResponseTransfer = (new AiTranslatorResponseTransfer())
            ->setOriginalText($aiTranslatorRequestTransfer->getTextOrFail())
            ->setSourceLocale($aiTranslatorRequestTransfer->getSourceLocaleOrFail())
            ->setTargetLocale($aiTranslatorRequestTransfer->getTargetLocale())
            ->setIsSuccessful($promptResponseTransfer->getIsSuccessful());

        foreach ($promptResponseTransfer->getErrors() as $errorTransfer) {
            $aiTranslatorResponseTransfer->addError($errorTransfer);
        }

        if (!$promptResponseTransfer->getIsSuccessful()) {
            return $aiTranslatorResponseTransfer;
        }

        $structuredMessage = $promptResponseTransfer->getStructuredMessage();
        if ($structuredMessage instanceof AiTranslationStructuredTransfer) {
            $aiTranslatorResponseTransfer->setTranslation($structuredMessage->getTranslation());
        }

        return $aiTranslatorResponseTransfer;
    }
}
