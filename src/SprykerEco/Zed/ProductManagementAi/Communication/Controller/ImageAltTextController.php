<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
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

        if (!$imageUrl || !$targetLocale) {
            return $this->getErrorJsonResponse('ImageUrl and/or target locale are missing from request.');
        }

        $openAiChatResponseTransfer = $this->getFacade()->generateImageAltText($imageUrl, $targetLocale);
        if (!$openAiChatResponseTransfer->getIsSuccessful()) {
            return $this->getErrorJsonResponse($openAiChatResponseTransfer->getMessage());
        }

        return $this->jsonResponse([
            'altText' => $this->getFacade()
                ->generateImageAltText($imageUrl, $targetLocale)
                ->getMessageOrFail(),
        ]);
    }

    /**
     * @param string|null $errorMessage
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getErrorJsonResponse(?string $errorMessage): JsonResponse
    {
        return $this->jsonResponse(
            [
                'error' => 'Bad request',
                'message' => $errorMessage,
            ],
            static::STATUS_CODE_BAD_REQUEST,
        );
    }
}
