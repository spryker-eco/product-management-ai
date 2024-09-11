<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Handler;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface;

class ProductCategoryHandler implements ProductCategoryHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface
     */
    protected ProductManagementAiToProductCategoryFacadeInterface $productCategoryFacade;

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface
     */
    protected ProductManagementAiToLocaleFacadeInterface $localeFacade;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToProductCategoryFacadeInterface $productCategoryFacade
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface $localeFacade
     */
    public function __construct(
        ProductManagementAiToProductCategoryFacadeInterface $productCategoryFacade,
        ProductManagementAiToLocaleFacadeInterface $localeFacade
    ) {
        $this->productCategoryFacade = $productCategoryFacade;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function updateProductCategories(ProductAbstractTransfer $productAbstractTransfer): ProductAbstractTransfer
    {
        $assignedCategoryIds = $this->getCategoryIdsByIdProductAbstract($productAbstractTransfer->getIdProductAbstract());
        $categoryIdsToAssign = $productAbstractTransfer->getCategoryIds();
        $assignedCategoryIdsToSave = array_diff($categoryIdsToAssign, $assignedCategoryIds);
        $assignedCategoryIdsToDelete = array_diff($assignedCategoryIds, $categoryIdsToAssign);
        foreach ($assignedCategoryIdsToSave as $idCategory) {
            $this->productCategoryFacade->createProductCategoryMappings(
                $idCategory,
                [$productAbstractTransfer->getIdProductAbstract()],
            );
        }

        foreach ($assignedCategoryIdsToDelete as $idCategory) {
            $this->productCategoryFacade->removeProductCategoryMappings(
                $idCategory,
                [$productAbstractTransfer->getIdProductAbstract()],
            );
        }

        return $productAbstractTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function createProductCategories(ProductAbstractTransfer $productAbstractTransfer): ProductAbstractTransfer
    {
        foreach ($productAbstractTransfer->getCategoryIds() as $idCategory) {
            $this->productCategoryFacade->createProductCategoryMappings(
                $idCategory,
                [$productAbstractTransfer->getIdProductAbstract()],
            );
        }

        return $productAbstractTransfer;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    protected function getCategoryIdsByIdProductAbstract(int $idProductAbstract): array
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
