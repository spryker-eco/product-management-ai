<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Generator;

use Exception;
use Generated\Shared\Transfer\AttachmentTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use Spryker\Client\AiFoundation\AiFoundationClientInterface;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class ImageAltTextGenerator implements ImageAltTextGeneratorInterface
{
    use LoggerTrait;

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
     * @param string $imageUrl
     * @param string $targetLocale
     *
     * @return \Generated\Shared\Transfer\PromptResponseTransfer
     */
    public function generateImageAltText(string $imageUrl, string $targetLocale): PromptResponseTransfer
    {
        $promptRequestTransfer = (new PromptRequestTransfer())
            ->setPromptMessage(
                (new PromptMessageTransfer())
                    ->setContent($this->productManagementAiConfig->getImageAltTextPrompt($targetLocale))
                    ->addAttachment((new AttachmentTransfer())
                        ->setType(AiFoundationConstants::ATTACHMENT_TYPE_IMAGE)
                        ->setContentType(AiFoundationConstants::ATTACHMENT_CONTENT_TYPE_URL)
                        ->setType(AiFoundationConstants::ATTACHMENT_TYPE_IMAGE)
                        ->setContent($imageUrl)),
            );

        try {
            $promptResponseTransfer = $this->aiFoundationClient->prompt($promptRequestTransfer);
        } catch (Exception $exception) {
            $this->getLogger()->critical($exception->getMessage(), $exception->getTrace());

            return (new PromptResponseTransfer())
                ->setMessage(
                    (new PromptMessageTransfer())
                        ->setContent(''),
                );
        }

        return $promptResponseTransfer;
    }
}
