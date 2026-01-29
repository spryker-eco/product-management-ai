<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Improver;

use Exception;
use Generated\Shared\Transfer\ContentImproverRequestTransfer;
use Generated\Shared\Transfer\ContentImproverResponseTransfer;
use Generated\Shared\Transfer\ContentImproverStructuredTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class ContentImprover implements ContentImproverInterface
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
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ContentImproverResponseTransfer
     */
    public function improveContent(
        ContentImproverRequestTransfer $contentImproverRequestTransfer
    ): ContentImproverResponseTransfer {
        $promptContent = $this->buildContentImproverPrompt($contentImproverRequestTransfer);
        $structuredSchema = new ContentImproverStructuredTransfer();

        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setPromptMessage(
                (new PromptMessageTransfer())->setContent($promptContent),
            )
            ->setStructuredMessage($structuredSchema)
            ->setMaxRetries(3);

        $aiConfigurationName = $this->productManagementAiConfig->getContentImproverAiConfigurationName();
        if ($aiConfigurationName !== null) {
            $promptRequestTransfer->setAiConfigurationName($aiConfigurationName);
        }

        try {
            $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);

            return $this->mapPromptResponseToContentImproverResponse(
                $promptResponseTransfer,
                $contentImproverRequestTransfer,
            );
        } catch (Exception $exception) {
            return (new ContentImproverResponseTransfer())
                ->setOriginalText($contentImproverRequestTransfer->getTextOrFail())
                ->setIsSuccessful(false)
                ->addError(
                    (new ErrorTransfer())
                        ->setMessage($exception->getMessage()),
                );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return string
     */
    protected function buildContentImproverPrompt(ContentImproverRequestTransfer $contentImproverRequestTransfer): string
    {
        return sprintf(
            $this->productManagementAiConfig->getContentImproverPromptTemplate(),
            $contentImproverRequestTransfer->getTextOrFail(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PromptResponseTransfer $promptResponseTransfer
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ContentImproverResponseTransfer
     */
    protected function mapPromptResponseToContentImproverResponse(
        PromptResponseTransfer $promptResponseTransfer,
        ContentImproverRequestTransfer $contentImproverRequestTransfer
    ): ContentImproverResponseTransfer {
        $contentImproverResponseTransfer = (new ContentImproverResponseTransfer())
            ->setOriginalText($contentImproverRequestTransfer->getTextOrFail())
            ->setIsSuccessful($promptResponseTransfer->getIsSuccessful());

        foreach ($promptResponseTransfer->getErrors() as $errorTransfer) {
            $contentImproverResponseTransfer->addError($errorTransfer);
        }

        if (!$promptResponseTransfer->getIsSuccessful()) {
            return $contentImproverResponseTransfer;
        }

        $structuredMessage = $promptResponseTransfer->getStructuredMessage();
        if ($structuredMessage instanceof ContentImproverStructuredTransfer) {
            $contentImproverResponseTransfer->setImprovedText($structuredMessage->getImprovedText());
        }

        return $contentImproverResponseTransfer;
    }
}
