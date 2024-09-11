<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilder;
use SprykerEco\Zed\ProductManagementAi\Business\Builder\PromptBuilderInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Generator\ImageAltTextGenerator;
use SprykerEco\Zed\ProductManagementAi\Business\Generator\ImageAltTextGeneratorInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Proposer\CategoryProposer;
use SprykerEco\Zed\ProductManagementAi\Business\Proposer\CategoryProposerInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReader;
use SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface;
use SprykerEco\Zed\ProductManagementAi\Business\StorageKeyBuilder\StorageKeyBuilder;
use SprykerEco\Zed\ProductManagementAi\Business\StorageKeyBuilder\StorageKeyBuilderInterface;
use SprykerEco\Zed\ProductManagementAi\Business\Translator\Translator;
use SprykerEco\Zed\ProductManagementAi\Business\Translator\TranslatorInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToStorageClientInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiDependencyProvider;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 */
class ProductManagementAiBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Business\Reader\CategoryReaderInterface
     */
    public function createCategoryReader(): CategoryReaderInterface
    {
        return new CategoryReader(
            $this->getCategoryFacade(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Business\Proposer\CategoryProposerInterface
     */
    public function createCategoryProposer(): CategoryProposerInterface
    {
        return new CategoryProposer(
            $this->getOpenAiClient(),
            $this->getUtilEncodingService(),
            $this->createCategoryReader(),
            $this->getConfig(),
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
     * @return \SprykerEco\Zed\ProductManagementAi\Business\Translator\TranslatorInterface
     */
    public function createTranslator(): TranslatorInterface
    {
        return new Translator(
            $this->getOpenAiClient(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Business\StorageKeyBuilder\StorageKeyBuilderInterface
     */
    public function createStorageKeyBuilder(): StorageKeyBuilderInterface
    {
        return new StorageKeyBuilder();
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface
     */
    public function getCategoryFacade(): ProductManagementAiToCategoryFacadeInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::FACADE_CATEGORY);
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface
     */
    public function getLocaleFacade(): ProductManagementAiToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Dependency\Service\ProductManagementAiToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): ProductManagementAiToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToOpenAiClientInterface
     */
    public function getOpenAiClient(): ProductManagementAiToOpenAiClientInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::CLIENT_OPEN_AI);
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Dependency\Client\ProductManagementAiToStorageClientInterface
     */
    public function getStorageClient(): ProductManagementAiToStorageClientInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::CLIENT_STORAGE);
    }
}
