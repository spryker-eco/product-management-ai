<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider;

use Spryker\Zed\Category\Business\CategoryFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;

class ProductCategoryAbstractFormDataProvider
{
    /**
     * @var \Spryker\Zed\Category\Business\CategoryFacadeInterface
     */
    protected CategoryFacadeInterface $categoryFacade;

    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected LocaleFacadeInterface $localeFacade;

    /**
     * @param \Spryker\Zed\Category\Business\CategoryFacadeInterface $categoryFacade
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     */
    public function __construct(
        CategoryFacadeInterface $categoryFacade,
        LocaleFacadeInterface $localeFacade
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->localeFacade = $localeFacade;
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
            $formOptions[$categoryTransfer->getName()] = $categoryTransfer->getIdCategory();
        }

        return $formOptions;
    }
}
