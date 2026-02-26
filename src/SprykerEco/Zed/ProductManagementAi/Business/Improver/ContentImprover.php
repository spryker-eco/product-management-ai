<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Improver;

use ArrayObject;
use Exception;
use Generated\Shared\Transfer\ContentImproverRequestTransfer;
use Generated\Shared\Transfer\ContentImproverResponseTransfer;
use Generated\Shared\Transfer\ContentImproverStructuredTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\PromptMessageTransfer;
use Generated\Shared\Transfer\PromptRequestTransfer;
use Generated\Shared\Transfer\PromptResponseTransfer;
use InvalidArgumentException;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\AiFoundation\Business\AiFoundationFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class ContentImprover implements ContentImproverInterface
{
    use LoggerTrait;

    /**
     * @var string
     */
    protected const string OPERATION_NAME = 'content improvement';

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
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ContentImproverResponseTransfer
     */
    public function improveContent(
        ContentImproverRequestTransfer $contentImproverRequestTransfer
    ): ContentImproverResponseTransfer {
        $promptRequestTransfer = $this->buildPromptRequest($contentImproverRequestTransfer);

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

            return $this->mapPromptResponseToContentImproverResponse(
                $promptResponseTransfer,
                $contentImproverRequestTransfer,
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

            return $this->mapPromptResponseToContentImproverResponse(
                $promptResponseTransfer,
                $contentImproverRequestTransfer,
            );
        }

        if ($promptResponseTransfer->getIsSuccessful() === false) {
            $promptResponseTransfer = $this->handleUnsuccessfulResponse($promptResponseTransfer, $promptRequestTransfer);
        }

        return $this->mapPromptResponseToContentImproverResponse(
            $promptResponseTransfer,
            $contentImproverRequestTransfer,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PromptRequestTransfer
     */
    protected function buildPromptRequest(ContentImproverRequestTransfer $contentImproverRequestTransfer): PromptRequestTransfer
    {
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
