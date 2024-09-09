<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Generator;

use Generated\Shared\Transfer\OpenAiChatRequestTransfer;
use Generated\Shared\Transfer\OpenAiChatResponseTransfer;
use SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilderInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig;

class ImageAltTextGenerator implements ImageAltTextGeneratorInterface
{
    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface
     */
    protected ProductManagementAiToOpenAiClientInterface $openAiClient;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilderInterface
     */
    protected PromptBuilderInterface $promptBuilder;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig
     */
    protected ProductManagementAiConfig $productManagementAiConfig;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface $openAiClient
     * @param \SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilderInterface $promptBuilder
     * @param \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig $productManagementAiConfig
     */
    public function __construct(
        ProductManagementAiToOpenAiClientInterface $openAiClient,
        PromptBuilderInterface $promptBuilder,
        ProductManagementAiConfig $productManagementAiConfig
    ) {
        $this->openAiClient = $openAiClient;
        $this->promptBuilder = $promptBuilder;
        $this->productManagementAiConfig = $productManagementAiConfig;
    }

    /**
     * @param string $imageUrl
     * @param string $targetLocale
     *
     * @return \Generated\Shared\Transfer\OpenAiChatResponseTransfer
     */
    public function generateImageAltText(string $imageUrl, string $targetLocale): OpenAiChatResponseTransfer
    {
        $openAiChatRequestTransfer = (new OpenAiChatRequestTransfer())->setPromptData(
            $this->promptBuilder->buildImageAltTextPrompt($imageUrl, $targetLocale),
        )->setModel($this->productManagementAiConfig->getOpenAiGpt4oMiniModel());

        return $this->openAiClient->chat($openAiChatRequestTransfer);
    }
}
