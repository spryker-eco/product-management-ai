<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Reader;

use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;

class CategoryReader implements CategoryReaderInterface
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
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface $categoryFacade
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface $localeFacade
     */
    public function __construct(
        ProductManagementAiToCategoryFacadeInterface $categoryFacade,
        ProductManagementAiToLocaleFacadeInterface $localeFacade,
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @return array<string, int>
     */
    public function getCategories(): array
    {
        $categoryCollectionTransfer = $this->categoryFacade
            ->getAllCategoryCollection($this->localeFacade->getCurrentLocale());

        $categories = [];
        foreach ($categoryCollectionTransfer->getCategories() as $categoryTransfer) {
            $categories[$categoryTransfer->getName()] = $categoryTransfer->getIdCategoryOrFail();
        }

        return $categories;
    }
}
