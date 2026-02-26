<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Improver;

use Generated\Shared\Transfer\ContentImproverRequestTransfer;
use Generated\Shared\Transfer\ContentImproverResponseTransfer;

interface ContentImproverInterface
{
    /**
     * @param \Generated\Shared\Transfer\ContentImproverRequestTransfer $contentImproverRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ContentImproverResponseTransfer
     */
    public function improveContent(
        ContentImproverRequestTransfer $contentImproverRequestTransfer
    ): ContentImproverResponseTransfer;
}
