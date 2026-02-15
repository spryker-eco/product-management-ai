<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Controller;

use ArrayObject;
use Generated\Shared\Transfer\ImageAltTextRequestTransfer;
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(Request $request): JsonResponse
    {
        $imageUrl = $request->get(static::PARAM_IMAGE_URL);
        $targetLocale = $request->get(static::PARAM_LOCALE);

        if (!$imageUrl || !$targetLocale) {
            return $this->jsonResponse(
                [
                    'error' => 'Bad request',
                    'message' => 'ImageUrl and/or target locale are missing from request.',
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $imageAltTextRequestTransfer = (new ImageAltTextRequestTransfer())
            ->setImageUrl($imageUrl)
            ->setTargetLocale($targetLocale);

        $imageAltTextResponseTransfer = $this->getFacade()
            ->generateImageAltText($imageAltTextRequestTransfer);

        if (!$imageAltTextResponseTransfer->getIsSuccessful()) {
            return $this->jsonResponse(
                ['errors' => $this->formatErrors($imageAltTextResponseTransfer->getErrors())],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return $this->jsonResponse([
            'altText' => $imageAltTextResponseTransfer->getAltTextOrFail(),
        ]);
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ErrorTransfer> $errors
     *
     * @return array<int, array<string, string>>
     */
    protected function formatErrors(ArrayObject $errors): array
    {
        $formatted = [];
        foreach ($errors as $errorTransfer) {
            $formatted[] = [
                'message' => $errorTransfer->getMessageOrFail(),
                'code' => $errorTransfer->getParameters()['code'] ?? null,
            ];
        }

        return $formatted;
    }
}
