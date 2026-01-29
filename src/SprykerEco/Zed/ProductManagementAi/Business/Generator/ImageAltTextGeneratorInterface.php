<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Business\Generator;

use Generated\Shared\Transfer\ImageAltTextRequestTransfer;
use Generated\Shared\Transfer\ImageAltTextResponseTransfer;

interface ImageAltTextGeneratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\ImageAltTextRequestTransfer $imageAltTextRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ImageAltTextResponseTransfer
     */
    public function generateImageAltText(
        ImageAltTextRequestTransfer $imageAltTextRequestTransfer
    ): ImageAltTextResponseTransfer;
}
