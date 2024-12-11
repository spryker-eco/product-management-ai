<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 * @method \SprykerEco\Zed\ProductManagementAi\Communication\ProductManagementAiCommunicationFactory getFactory()
 */
class CategorySuggestionController extends AbstractController
{
    /**
     * @var string
     */
    public const PARAM_PRODUCT_NAME = 'product_name';

    /**
     * @var string
     */
    public const PARAM_PRODUCT_DESCRIPTION = 'product_description';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(Request $request): JsonResponse
    {
        $productName = $request->get(static::PARAM_PRODUCT_NAME);
        $description = $request->get(static::PARAM_PRODUCT_DESCRIPTION);

        if (!$productName || !$description) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'categories' => $this->getFacade()->proposeCategorySuggestions($productName, $description),
        ]);
    }
}
