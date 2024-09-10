<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\ProductManagementAi\Communication\Expander\ImageAltTextProductFormExpander;
use SprykerEco\Zed\ProductManagementAi\Communication\Expander\ImageAltTextProductFormExpanderInterface;
use SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProvider;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\ProductManagementAiDependencyProvider;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 */
class ProductManagementAiCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProvider
     */
    public function createProductCategoryAbstractFormDataProvider(): ProductCategoryAbstractFormDataProvider
    {
        return new ProductCategoryAbstractFormDataProvider(
            $this->getCategoryFacade(),
            $this->getLocaleFacade(),
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
}
