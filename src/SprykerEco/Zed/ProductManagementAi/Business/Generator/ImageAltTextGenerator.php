<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Generator;

use Exception;
use Generated\Shared\Transfer\AttachmentTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\ImageAltTextRequestTransfer;
use Generated\Shared\Transfer\ImageAltTextResponseTransfer;
use Generated\Shared\Transfer\ImageAltTextStructuredTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class ImageAltTextGenerator implements ImageAltTextGeneratorInterface
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
     * @param \Generated\Shared\Transfer\ImageAltTextRequestTransfer $imageAltTextRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ImageAltTextResponseTransfer
     */
    public function generateImageAltText(
        ImageAltTextRequestTransfer $imageAltTextRequestTransfer
    ): ImageAltTextResponseTransfer {
        $promptContent = $this->productManagementAiConfig->getImageAltTextPrompt(
            $imageAltTextRequestTransfer->getTargetLocaleOrFail(),
        );

        $structuredSchema = new ImageAltTextStructuredTransfer();

        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setPromptMessage(
                (new PromptMessageTransfer())
                    ->setContent($promptContent)
                    ->addAttachment(
                        (new AttachmentTransfer())
                            ->setType(AiFoundationConstants::ATTACHMENT_TYPE_IMAGE)
                            ->setContentType(AiFoundationConstants::ATTACHMENT_CONTENT_TYPE_URL)
                            ->setContent($imageAltTextRequestTransfer->getImageUrlOrFail()),
                    ),
            )
            ->setStructuredMessage($structuredSchema)
            ->setMaxRetries(3);

        $aiConfigurationName = $this->productManagementAiConfig->getImageAltTextAiConfigurationName();
        if ($aiConfigurationName !== null) {
            $promptRequestTransfer->setAiConfigurationName($aiConfigurationName);
        }

        try {
            $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);

            return $this->mapPromptResponseToImageAltTextResponse(
                $promptResponseTransfer,
                new ImageAltTextResponseTransfer(),
            );
        } catch (Exception $exception) {
            return (new ImageAltTextResponseTransfer())
                ->setIsSuccessful(false)
                ->addError(
                    (new ErrorTransfer())
                        ->setMessage($exception->getMessage()),
                );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PromptResponseTransfer $promptResponseTransfer
     * @param \Generated\Shared\Transfer\ImageAltTextResponseTransfer $imageAltTextResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ImageAltTextResponseTransfer
     */
    protected function mapPromptResponseToImageAltTextResponse(
        PromptResponseTransfer $promptResponseTransfer,
        ImageAltTextResponseTransfer $imageAltTextResponseTransfer
    ): ImageAltTextResponseTransfer {
        $imageAltTextResponseTransfer->setIsSuccessful($promptResponseTransfer->getIsSuccessful());

        foreach ($promptResponseTransfer->getErrors() as $errorTransfer) {
            $imageAltTextResponseTransfer->addError($errorTransfer);
        }

        if (!$promptResponseTransfer->getIsSuccessful()) {
            return $imageAltTextResponseTransfer;
        }

        $structuredMessage = $promptResponseTransfer->getStructuredMessage();
        if ($structuredMessage instanceof ImageAltTextStructuredTransfer) {
            $imageAltTextResponseTransfer->setAltText($structuredMessage->getAltText());
        }

        return $imageAltTextResponseTransfer;
    }
}
