<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Translator;

use Exception;
use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class Translator implements TranslatorInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const INVALID_TRANSLATION_MESSAGE = 'Unable to translate provided text.';

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
        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setPromptMessage(
                (new PromptMessageTransfer())
                    ->setContent($this->buildTranslationRequestPrompt($aiTranslatorRequestTransfer)),
            );

        try {
            $promptResponse = $this->aiFoundationClient->prompt($promptRequestTransfer);
        } catch (Exception $exception) {
            $this->getLogger()->critical($exception->getMessage(), $exception->getTrace());

            return $this->createInvalidTranslatorResponse($aiTranslatorRequestTransfer);
        }

        return $this->createTranslatorResponse(
            $aiTranslatorRequestTransfer,
            $promptResponse,
        );
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
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     * @param \Generated\Shared\Transfer\PromptResponseTransfer $promptResponseTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    protected function createTranslatorResponse(
        AiTranslatorRequestTransfer $aiTranslatorRequestTransfer,
        PromptResponseTransfer $promptResponseTransfer
    ): AiTranslatorResponseTransfer {
        $aiTranslatorResponseTransfer = $this->createBaseTranslatorResponse($aiTranslatorRequestTransfer);

        return $aiTranslatorResponseTransfer->setTranslation($promptResponseTransfer->getMessage()->getContent());
    }

    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    protected function createInvalidTranslatorResponse(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): AiTranslatorResponseTransfer
    {
        $aiTranslatorResponseTransfer = $this->createBaseTranslatorResponse($aiTranslatorRequestTransfer);

        return $aiTranslatorResponseTransfer->setTranslation(static::INVALID_TRANSLATION_MESSAGE);
    }

    /**
     * @param \Generated\Shared\Transfer\AiTranslatorRequestTransfer $aiTranslatorRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    protected function createBaseTranslatorResponse(AiTranslatorRequestTransfer $aiTranslatorRequestTransfer): AiTranslatorResponseTransfer
    {
        return (new AiTranslatorResponseTransfer())
            ->setOriginalText($aiTranslatorRequestTransfer->getTextOrFail())
            ->setSourceLocale($aiTranslatorRequestTransfer->getSourceLocaleOrFail())
            ->setTargetLocale($aiTranslatorRequestTransfer->getTargetLocale());
    }
}
