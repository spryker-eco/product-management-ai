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
 * @method \SprykerEco\Zed\ProductManagementAi\Communication\ProductManagementAiCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 */
class ImageAltTextController extends AbstractController
{
    /**
     * @var string
     */
    protected const PARAM_IMAGE_URL = 'imageUrl';

    /**
     * @var string
     */
    protected const PARAM_LOCALE = 'locale';

    /**
     * @var string
     */
    protected const PARAM_INVALIDATE_CACHE = 'invalidate_cache';

    /**
     * @var int
     */
    protected const STATUS_CODE_BAD_REQUEST = 400;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request): Response
    {
        $imageUrl = $request->get(static::PARAM_IMAGE_URL);
        $targetLocale = $request->get(static::PARAM_LOCALE);
        $response = new JsonResponse();

        if (!$imageUrl || !$targetLocale) {
            return $response->setData([
                'error' => 'Bad request',
                'message' => 'ImageUrl and/or target locale are missing from request.',
            ])->setStatusCode(static::STATUS_CODE_BAD_REQUEST);
        }

        return $response->setData([
            'altText' => $this->getFacade()
                ->generateImageAltText($imageUrl, $targetLocale)
                ->getMessageOrFail(),
        ]);
    }
}
