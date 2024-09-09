<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business;

use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;
use Spryker\Zed\Category\Business\CategoryFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use SprykerEco\Client\OpenAi\OpenAiClientInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilder;
use SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilderInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryProposer;
use SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryProposerInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryReader;
use SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryReaderInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Generator\ImageAltTextGenerator;
use SprykerEco\Zed\ProductManagementAi\Business\Generator\ImageAltTextGeneratorInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiDependencyProvider;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 */
class ProductManagementAiBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryReaderInterface
     */
    public function createCategoryReader(): CategoryReaderInterface
    {
        return new CategoryReader(
            $this->getCategoryFacade(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Business\Category\CategoryProposerInterface
     */
    public function createCategoryProposer(): CategoryProposerInterface
    {
        return new CategoryProposer(
            $this->getOpenAiClient(),
            $this->getUtilEncodingService(),
            $this->createCategoryReader(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Business\Generator\ImageAltTextGeneratorInterface
     */
    public function createImageAltTextGenerator(): ImageAltTextGeneratorInterface
    {
        return new ImageAltTextGenerator(
            $this->getOpenAiClient(),
            $this->createPromptBuilder(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilderInterface
     */
    public function createPromptBuilder(): PromptBuilderInterface
    {
        return new PromptBuilder($this->getConfig());
    }

    /**
     * @return \Spryker\Zed\Category\Business\CategoryFacadeInterface
     */
    public function getCategoryFacade(): CategoryFacadeInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::FACADE_CATEGORY);
    }

    /**
     * @return \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    public function getLocaleFacade(): LocaleFacadeInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): UtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Client\OpenAi\OpenAiClientInterface
     */
    public function getOpenAiClient(): OpenAiClientInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::CLIENT_OPEN_AI);
    }
}
