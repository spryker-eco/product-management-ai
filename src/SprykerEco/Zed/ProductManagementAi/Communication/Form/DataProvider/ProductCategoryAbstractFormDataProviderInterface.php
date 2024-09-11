<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider;

interface ProductCategoryAbstractFormDataProviderInterface
{
    /**
     * @return array<string, int>
     */
    public function getOptions(): array;

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getData(int $idProductAbstract): array;
}
