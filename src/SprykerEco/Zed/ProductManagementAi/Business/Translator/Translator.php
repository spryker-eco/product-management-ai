<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Translator;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Generated\Shared\Transfer\AiTranslatorResponseTransfer;
use Generated\Shared\Transfer\OpenAiChatRequestTransfer;
use Generated\Shared\Transfer\OpenAiChatResponseTransfer;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class Translator implements TranslatorInterface
{
    /**
     * @var string
     */
    protected const INVALID_TRANSLATION_MESSAGE = 'Unable to translate provided text.';

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface
     */
    protected ProductManagementAiToOpenAiClientInterface $openAiClient;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig
     */
    protected ProductManagementAiConfig $productManagementAiConfig;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface $openAiClient
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(
        ProductManagementAiToOpenAiClientInterface $openAiClient,
        ProductManagementAiConfig $productManagementAiConfig
    ) {
        $this->openAiClient = $openAiClient;
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
        $openAiChatRequestTransfer = (new OpenAiChatRequestTransfer())
            ->setMessage($this->buildTranslationRequestPrompt($aiTranslatorRequestTransfer));
        $openAiChatResponse = $this->openAiClient->chat($openAiChatRequestTransfer);

        return $this->createTranslatorResponse(
            $aiTranslatorRequestTransfer,
            $openAiChatResponse,
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
     * @param \Generated\Shared\Transfer\OpenAiChatResponseTransfer $openAiChatResponseTransfer
     *
     * @return \Generated\Shared\Transfer\AiTranslatorResponseTransfer
     */
    protected function createTranslatorResponse(
        AiTranslatorRequestTransfer $aiTranslatorRequestTransfer,
        OpenAiChatResponseTransfer $openAiChatResponseTransfer
    ): AiTranslatorResponseTransfer {
        $aiTranslatorResponseTransfer = (new AiTranslatorResponseTransfer())
            ->setOriginalText($aiTranslatorRequestTransfer->getTextOrFail())
            ->setSourceLocale($aiTranslatorRequestTransfer->getSourceLocaleOrFail())
            ->setTargetLocale($aiTranslatorRequestTransfer->getTargetLocale());

        if (!$openAiChatResponseTransfer->getIsSuccessful() || !$openAiChatResponseTransfer->getMessage()) {
            return $aiTranslatorResponseTransfer->setTranslation(static::INVALID_TRANSLATION_MESSAGE);
        }

        return $aiTranslatorResponseTransfer->setTranslation($openAiChatResponseTransfer->getMessage());
    }
}
