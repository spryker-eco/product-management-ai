<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Controller;

use Generated\Shared\Transfer\AiTranslatorRequestTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\Communication\ProductManagementAiCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 */
class TranslateController extends AbstractController
{
    /**
     * @var string
     */
    protected const PARAM_TEXT = 'text';

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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(Request $request): JsonResponse
    {
        $text = $request->get(static::PARAM_TEXT);
        $targetLocale = $request->get(static::PARAM_LOCALE);

        if (!$text || !$targetLocale) {
            return $this->jsonResponse(
                [
                    'error' => 'Bad request',
                    'message' => 'Text and/or target locale are missing from request.',
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $translatorRequestTransfer = (new AiTranslatorRequestTransfer())
            ->setTargetLocale($targetLocale)
            ->setText($text)
            ->setInvalidateCache((bool)$request->get(static::PARAM_INVALIDATE_CACHE, false));
        $translationResponseTransfer = $this->getFacade()->translate($translatorRequestTransfer);

        return $this->jsonResponse([
            'locale' => $translationResponseTransfer->getTargetLocaleOrFail(),
            'translation' => $translationResponseTransfer->getTranslationOrFail(),
        ]);
    }
}
