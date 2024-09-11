<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Reader;

interface CategoryReaderInterface
{
    /**
     * @return array<string, int>
     */
    public function getCategories(): array;
}
