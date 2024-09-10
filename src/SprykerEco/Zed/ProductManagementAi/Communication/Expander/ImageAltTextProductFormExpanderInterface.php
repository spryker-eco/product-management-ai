<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Expander;

use Symfony\Component\Form\FormBuilderInterface;

interface ImageAltTextProductFormExpanderInterface
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function expand(FormBuilderInterface $builder, array $options): void;
}
