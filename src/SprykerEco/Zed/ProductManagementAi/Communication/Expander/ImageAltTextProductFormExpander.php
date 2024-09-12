<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Expander;

use Generated\Shared\Transfer\LocaleTransfer;
use SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;

class ImageAltTextProductFormExpander implements ImageAltTextProductFormExpanderInterface
{
    /**
     * @var string
     */
    protected const SUBFORM_IMAGE_SET_PREFIX = 'image_set_';

    /**
     * @var string
     */
    protected const FIELD_ALT_TEXT = 'alt_text';

    /**
     * @var string
     */
    protected const SUBFORM_IMAGE_COLLECTION = 'product_images';

    /**
     * @var string
     */
    protected const TEMPLATE_PATH = '@SprykerEco:ProductManagementAi/ProductManagement/_partials/image-alt-text.twig';

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface
     */
    protected ProductManagementAiToLocaleFacadeInterface $localeFacade;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Dependency\Facade\ProductManagementAiToLocaleFacadeInterface $localeFacade
     */
    public function __construct(ProductManagementAiToLocaleFacadeInterface $localeFacade)
    {
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function expand(FormBuilderInterface $builder, array $options): void
    {
        $this->addAltTextField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    protected function addAltTextField(FormBuilderInterface $builder): FormBuilderInterface
    {
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event): void {
                $form = $event->getForm();

                $localeCollection = $this->localeFacade->getLocaleCollection();
                $localeCollection[] = (new LocaleTransfer())->setLocaleName('default');

                foreach ($localeCollection as $localeTransfer) {
                    $localizedImageSetFormName = $this->getLocalizedImageSetFormName($localeTransfer);
                    if (!$form->has($localizedImageSetFormName)) {
                        continue;
                    }

                    $imageSetCollection = $form->get($localizedImageSetFormName);
                    foreach ($imageSetCollection as $imageSet) {
                        $imageCollection = $imageSet->get(static::SUBFORM_IMAGE_COLLECTION);

                        foreach ($imageCollection as $imageField) {
                            $imageField->add(static::FIELD_ALT_TEXT, TextType::class, [
                                'label' => 'Alt Text',
                                'required' => false,
                                'constraints' => [
                                    new Length([
                                        'min' => 0,
                                        'max' => 255,
                                    ]),
                                ],
                                'sanitize_xss' => true,
                                'attr' => [
                                    'template_path' => static::TEMPLATE_PATH,
                                ],
                            ]);
                        }
                    }
                }
            },
        );

        return $builder;
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    protected function getLocalizedImageSetFormName(LocaleTransfer $localeTransfer): string
    {
        return static::SUBFORM_IMAGE_SET_PREFIX . $localeTransfer->getLocaleName();
    }
}
