<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Translator;

use Exception;
use Generated\Shared\Transfer\AiTranslationStructuredTransfer;
use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class Translator implements TranslatorInterface
{
    /**
     * @var \Spryker\Client\AiFoundation\AiFoundationClientInterface
     */
    protected AiFoundationClientInterface $aiFoundationClient;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig
     */
    protected ProductManagementAiConfig $productManagementAiConfig;

    /**
     * @param \Spryker\Client\AiFoundation\AiFoundationClientInterface $aiFoundationClient
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(
        AiFoundationClientInterface $aiFoundationClient,
        ProductManagementAiConfig $productManagementAiConfig
    ) {
        $this->aiFoundationClient = $aiFoundationClient;
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

        try {
            $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);

            return $this->mapPromptResponseToTranslatorResponse(
                $promptResponseTransfer,
                $aiTranslatorRequestTransfer,
            );
        } catch (Exception $exception) {
            return (new AiTranslatorResponseTransfer())
                ->setOriginalText($aiTranslatorRequestTransfer->getTextOrFail())
                ->setSourceLocale($aiTranslatorRequestTransfer->getSourceLocaleOrFail())
                ->setTargetLocale($aiTranslatorRequestTransfer->getTargetLocale())
                ->setIsSuccessful(false)
                ->addError(
                    (new ErrorTransfer())
                        ->setMessage($exception->getMessage()),
                );
        }
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
