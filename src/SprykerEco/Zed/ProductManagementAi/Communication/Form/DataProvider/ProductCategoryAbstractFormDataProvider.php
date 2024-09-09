<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider;

use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToCategoryFacadeInterface;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;

class ProductCategoryAbstractFormDataProvider
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
        ProductManagementAiToLocaleFacadeInterface $localeFacade
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
            $formOptions[$categoryTransfer->getNameOrFail()] = $categoryTransfer->getIdCategoryOrFail();
        }

        return $formOptions;
    }
}
