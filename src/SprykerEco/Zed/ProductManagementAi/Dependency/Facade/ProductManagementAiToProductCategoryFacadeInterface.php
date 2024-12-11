<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Dependency\Facade;

use Generated\Shared\Transfer\CategoryCollectionTransfer;
use Generated\Shared\Transfer\LocaleTransfer;

interface ProductManagementAiToProductCategoryFacadeInterface
{
    /**
     * @param int $idCategory
     * @param array<int> $productIdsToAssign
     *
     * @return void
     */
    public function createProductCategoryMappings(int $idCategory, array $productIdsToAssign): void;

    /**
     * @param int $idCategory
     * @param array<int> $productIdsToUnAssign
     *
     * @return void
     */
    public function removeProductCategoryMappings(int $idCategory, array $productIdsToUnAssign): void;

    /**
     * @param int $idProductAbstract
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\CategoryCollectionTransfer
     */
    public function getCategoryTransferCollectionByIdProductAbstract(
        int $idProductAbstract,
        LocaleTransfer $localeTransfer
    ): CategoryCollectionTransfer;
}
