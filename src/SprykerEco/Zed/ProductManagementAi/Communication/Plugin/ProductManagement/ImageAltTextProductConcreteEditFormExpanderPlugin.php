<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Plugin\ProductManagement;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductManagementExtension\Dependency\Plugin\ProductConcreteEditFormExpanderPluginInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 * @method \SprykerEco\Zed\ProductManagementAi\Communication\ProductManagementAiCommunicationFactory getFactory()
 */
class ImageAltTextProductConcreteEditFormExpanderPlugin extends AbstractPlugin implements ProductConcreteEditFormExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Expands ProductConcrete edit form with image alt text field.
     *
     * @api
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->getFactory()
            ->createImageAltTextProductFormExpander()
            ->expand($builder, $options);
    }
}
