<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Proposer;

use Generated\Shared\Transfer\CategorySuggestionRequestTransfer;
use Generated\Shared\Transfer\CategorySuggestionResponseTransfer;

interface CategoryProposerInterface
{
    /**
     * @param \Generated\Shared\Transfer\CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\CategorySuggestionResponseTransfer
     */
    public function proposeCategorySuggestions(
        CategorySuggestionRequestTransfer $categorySuggestionRequestTransfer
    ): CategorySuggestionResponseTransfer;
}
