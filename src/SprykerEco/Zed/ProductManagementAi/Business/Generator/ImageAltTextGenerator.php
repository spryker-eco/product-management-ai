<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Generator;

use ArrayObject;
use Exception;
use Generated\Shared\Transfer\AttachmentTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\ImageAltTextRequestTransfer;
use Generated\Shared\Transfer\ImageAltTextResponseTransfer;
use Generated\Shared\Transfer\ImageAltTextStructuredTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use InvalidArgumentException;
use Spryker\Shared\AiFoundation\AiFoundationConstants;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class ImageAltTextGenerator implements ImageAltTextGeneratorInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const string OPERATION_NAME = 'image alt text generation';

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
     * @param \Generated\Shared\Transfer\ImageAltTextRequestTransfer $imageAltTextRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ImageAltTextResponseTransfer
     */
    public function generateImageAltText(
        ImageAltTextRequestTransfer $imageAltTextRequestTransfer
    ): ImageAltTextResponseTransfer {
        $promptRequestTransfer = $this->buildPromptRequest($imageAltTextRequestTransfer);

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

            return $this->mapPromptResponseToImageAltTextResponse(
                $promptResponseTransfer,
                new ImageAltTextResponseTransfer(),
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

            return $this->mapPromptResponseToImageAltTextResponse(
                $promptResponseTransfer,
                new ImageAltTextResponseTransfer(),
            );
        }

        if ($promptResponseTransfer->getIsSuccessful() === false) {
            $promptResponseTransfer = $this->handleUnsuccessfulResponse($promptResponseTransfer, $promptRequestTransfer);
        }

        return $this->mapPromptResponseToImageAltTextResponse(
            $promptResponseTransfer,
            new ImageAltTextResponseTransfer(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ImageAltTextRequestTransfer $imageAltTextRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PromptRequestTransfer
     */
    protected function buildPromptRequest(ImageAltTextRequestTransfer $imageAltTextRequestTransfer): PromptRequestTransfer
    {
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
