<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Dependency\Client;

use Generated\Shared\Transfer\OpenAiChatRequestTransfer;
use Generated\Shared\Transfer\OpenAiChatResponseTransfer;

class ProductManagementAiToOpenAiClientBridge implements ProductManagementAiToOpenAiClientInterface
{
    /**
     * @var \SprykerEco\Client\OpenAi\OpenAiClientInterface
     */
    protected $openAiClient;

    /**
     * @param \SprykerEco\Client\OpenAi\OpenAiClientInterface $openAiClient
     */
    public function __construct($openAiClient)
    {
        $this->openAiClient = $openAiClient;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenAiChatRequestTransfer $openAiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OpenAiChatResponseTransfer
     */
    public function chat(OpenAiChatRequestTransfer $openAiRequestTransfer): OpenAiChatResponseTransfer
    {
        return $this->openAiClient->chat($openAiRequestTransfer);
    }
}
