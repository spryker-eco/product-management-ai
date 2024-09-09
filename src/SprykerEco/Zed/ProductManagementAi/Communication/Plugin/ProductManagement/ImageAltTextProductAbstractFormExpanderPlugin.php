<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Plugin\ProductManagement;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductManagementExtension\Dependency\Plugin\ProductAbstractFormExpanderPluginInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 * @method \SprykerEco\Zed\ProductManagementAi\Communication\ProductManagementAiCommunicationFactory getFactory()
 */
class ImageAltTextProductAbstractFormExpanderPlugin extends AbstractPlugin implements ProductAbstractFormExpanderPluginInterface
{
    /**
     * @var string
     */
    protected const SUBFORM_IMAGE_SET_PREFIX = 'image_set_';

    /**
     * @var string
     */
    public const FIELD_ALT_TEXT = 'alt_text';

    /**
     * @var string
     */
    protected const SUBFORM_IMAGE_COLLECTION = 'product_images';

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    protected function getLocalizedImageSetFormName(LocaleTransfer $localeTransfer): string
    {
        return static::SUBFORM_IMAGE_SET_PREFIX . $localeTransfer->getLocaleName();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function expand(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder = $this->addAltTextField($builder);

        return $builder;
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
                $data = $event->getData();

                $localeCollection = $this->getFactory()->getLocaleFacade()->getLocaleCollection();
                $localeCollection[] = (new LocaleTransfer())->setLocaleName('default');

                foreach ($localeCollection as $localeTransfer) {
                    $imageSetCollection = $form->get($this->getLocalizedImageSetFormName($localeTransfer));

                    foreach ($imageSetCollection as $imageSet) {
                        $imageCollection = $imageSet->get(static::SUBFORM_IMAGE_COLLECTION);

                        foreach ($imageCollection as $imageField) {
                            $imageField->add(static::FIELD_ALT_TEXT, TextType::class, [
                                'label' => 'Alt Text',
                                'required' => false,
                                'constraints' => [
                                    new Length([
                                        'min' => 0,
                                        'max' => 2048,
                                    ]),
                                ],
                            ]);
                        }
                    }
                }
            },
        );

        return $builder;
    }
}
