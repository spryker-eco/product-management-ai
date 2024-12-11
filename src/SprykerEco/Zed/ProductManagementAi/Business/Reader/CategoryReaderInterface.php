<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Reader;

interface CategoryReaderInterface
{
    /**
     * @return array<string, int>
     */
    public function getCategories(): array;
}
