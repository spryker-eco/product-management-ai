<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Plugin\Product;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Product\Dependency\Plugin\ProductAbstractPluginUpdateInterface;

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
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function update(ProductAbstractTransfer $productAbstractTransfer): ProductAbstractTransfer
    {
        return $this->getFactory()->createProductCategoryHandler()->updateProductCategories($productAbstractTransfer);
    }
}
