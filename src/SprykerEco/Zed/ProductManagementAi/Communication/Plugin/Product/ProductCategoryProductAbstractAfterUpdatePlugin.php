<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerEco\Zed\ProductManagementAi\Communication\Plugin\Product;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Orm\Zed\ProductCategory\Persistence\Map\SpyProductCategoryTableMap;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Product\Dependency\Plugin\ProductAbstractPluginUpdateInterface;
use Spryker\Zed\ProductCategory\Business\ProductCategoryFacade;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\Communication\ProductManagementAiCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 */
class ProductCategoryProductAbstractAfterUpdatePlugin extends AbstractPlugin implements ProductAbstractPluginUpdateInterface
{
    /**
     * {@inheritDoc}
     * - Executed on "after" event when an abstract product is updated.
     * - Persists product categories.
     *
     * @api
     *
     * @see \Spryker\Zed\Product\ProductDependencyProvider
     *
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function update(ProductAbstractTransfer $productAbstractTransfer): ProductAbstractTransfer
    {
        $productCategoryFacade = new ProductCategoryFacade();
        $assignedCategoryIds = $this->getProductCategoryIds($productAbstractTransfer->getIdProductAbstract());
        $categoryIdsToAssign = $productAbstractTransfer->getCategoryIds();
        $assignedCategoryIdsToSave = array_diff($categoryIdsToAssign, $assignedCategoryIds);
        $assignedCategoryIdsToDelete = array_diff($assignedCategoryIds, $categoryIdsToAssign);
        foreach ($assignedCategoryIdsToSave as $idCategory) {
            $productCategoryFacade->createProductCategoryMappings($idCategory, [$productAbstractTransfer->getIdProductAbstract()]);
        }

        foreach ($assignedCategoryIdsToDelete as $idCategory) {
            $productCategoryFacade->removeProductCategoryMappings($idCategory, [$productAbstractTransfer->getIdProductAbstract()]);
        }

        return $productAbstractTransfer;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    protected function getProductCategoryIds(int $idProductAbstract): array
    {
        return SpyProductCategoryQuery::create()
            ->filterByFkProductAbstract($idProductAbstract)
            ->select(SpyProductCategoryTableMap::COL_FK_CATEGORY)
            ->find()
            ->toArray();
    }
}
