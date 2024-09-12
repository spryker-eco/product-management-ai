<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Expander;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\Gui\Communication\Form\Type\Select2ComboBoxType;
use SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CategoryIdsProductFormExpander implements CategoryIdsProductFormExpanderInterface
{
    /**
     * @see \Spryker\Zed\ProductManagement\Communication\Form\ProductFormAdd::FIELD_ID_PRODUCT_ABSTRACT
     *
     * @var string
     */
    protected const KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    /**
     * @var \SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProviderInterface $productCategoryAbstractFormDataProvider
     */
    protected ProductCategoryAbstractFormDataProviderInterface $productCategoryAbstractFormDataProvider;

    /**
     * @param \SprykerEco\Zed\ProductManagementAi\Communication\Form\DataProvider\ProductCategoryAbstractFormDataProviderInterface $productCategoryAbstractFormDataProvider
     */
    public function __construct(ProductCategoryAbstractFormDataProviderInterface $productCategoryAbstractFormDataProvider)
    {
        $this->productCategoryAbstractFormDataProvider = $productCategoryAbstractFormDataProvider;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function expand(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $this->addCategoryIdsField($builder);

        return $builder;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    protected function addCategoryIdsField(FormBuilderInterface $builder): FormBuilderInterface
    {
        $builder->add(ProductAbstractTransfer::CATEGORY_IDS, Select2ComboBoxType::class, [
            'label' => 'categories',
            'placeholder' => 'select-category',
            'multiple' => true,
            'required' => false,
            'empty_data' => [],
            'choices' => $this->productCategoryAbstractFormDataProvider->getOptions(),
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $data = $event->getData();

                if (!$data || !isset($data[self::KEY_ID_PRODUCT_ABSTRACT])) {
                    return;
                }

                $data[ProductAbstractTransfer::CATEGORY_IDS] = $this->productCategoryAbstractFormDataProvider
                    ->getData($data[self::KEY_ID_PRODUCT_ABSTRACT]);
                $event->setData($data);
            },
        );

        return $builder;
    }
}
