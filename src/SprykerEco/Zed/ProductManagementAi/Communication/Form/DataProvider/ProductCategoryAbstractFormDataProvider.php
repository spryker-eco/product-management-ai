<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider;

use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface;

class ProductCategoryAbstractFormDataProvider implements ProductCategoryAbstractFormDataProviderInterface
{
    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface
     */
    protected ProductManagementAiToCategoryFacadeInterface $categoryFacade;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface
     */
    protected ProductManagementAiToLocaleFacadeInterface $localeFacade;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface
     */
    protected ProductManagementAiToProductCategoryFacadeInterface $productCategoryFacade;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface $categoryFacade
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface $localeFacade
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface $productCategoryFacade
     */
    public function __construct(
        ProductManagementAiToCategoryFacadeInterface $categoryFacade,
        ProductManagementAiToLocaleFacadeInterface $localeFacade,
        ProductManagementAiToProductCategoryFacadeInterface $productCategoryFacade
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->localeFacade = $localeFacade;
        $this->productCategoryFacade = $productCategoryFacade;
    }

    /**
     * @return array<string, int>
     */
    public function getOptions(): array
    {
        $formOptions = [];
        $categoryCollectionTransfer = $this->categoryFacade->getAllCategoryCollection($this->localeFacade->getCurrentLocale());

        /** @var \Generated\Shared\Transfer\CategoryTransfer $categoryTransfer */
        foreach ($categoryCollectionTransfer->getCategories() as $categoryTransfer) {
            $formOptions[$categoryTransfer->getNameOrFail()] = $categoryTransfer->getIdCategoryOrFail();
        }

        return $formOptions;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getData(int $idProductAbstract): array
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();
        $categoryCollectionTransfer = $this->productCategoryFacade->getCategoryTransferCollectionByIdProductAbstract(
            $idProductAbstract,
            $localeTransfer,
        );
        $categoryIds = [];
        foreach ($categoryCollectionTransfer->getCategories() as $categoryTransfer) {
            $categoryIds[] = $categoryTransfer->getIdCategory();
        }

        return $categoryIds;
    }
}
