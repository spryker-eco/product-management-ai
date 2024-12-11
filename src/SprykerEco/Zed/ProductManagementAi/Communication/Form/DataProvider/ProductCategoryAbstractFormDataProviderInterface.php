<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
