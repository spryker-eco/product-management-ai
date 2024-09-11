<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\ProductManagementAi\Communication\Expander\CategoryIdsProductFormExpander;
use SprykerEco\Zed\ProductManagementAi\Communication\Expander\CategoryIdsProductFormExpanderInterface;
use SprykerEco\Zed\ProductManagementAi\Communication\Expander\ImageAltTextProductFormExpander;
use SprykerEco\Zed\ProductManagementAi\Communication\Expander\ImageAltTextProductFormExpanderInterface;
use SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProvider;
use SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProviderInterface;
use SprykerEco\Zed\ProductManagementAi\Communication\Handler\ProductCategoryHandler;
use SprykerEco\Zed\ProductManagementAi\Communication\Handler\ProductCategoryHandlerInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiDependencyProvider;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 */
class ProductManagementAiCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProviderInterface
     */
    public function createProductCategoryAbstractFormDataProvider(): ProductCategoryAbstractFormDataProviderInterface
    {
        return new ProductCategoryAbstractFormDataProvider(
            $this->getCategoryFacade(),
            $this->getLocaleFacade(),
            $this->getProductCategoryFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Communication\Expander\ImageAltTextProductFormExpanderInterface
     */
    public function createImageAltTextProductFormExpander(): ImageAltTextProductFormExpanderInterface
    {
        return new ImageAltTextProductFormExpander(
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Communication\Handler\ProductCategoryHandlerInterface
     */
    public function createProductCategoryHandler(): ProductCategoryHandlerInterface
    {
        return new ProductCategoryHandler(
            $this->getProductCategoryFacade(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Communication\Expander\CategoryIdsProductFormExpanderInterface
     */
    public function createCategoryIdsProductFormExpander(): CategoryIdsProductFormExpanderInterface
    {
        return new CategoryIdsProductFormExpander($this->createProductCategoryAbstractFormDataProvider());
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
     * @return \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface
     */
    public function getProductCategoryFacade(): ProductManagementAiToProductCategoryFacadeInterface
    {
        return $this->getProvidedDependency(ProductManagementAiDependencyProvider::FACADE_PRODUCT_CATEGORY);
    }
}
