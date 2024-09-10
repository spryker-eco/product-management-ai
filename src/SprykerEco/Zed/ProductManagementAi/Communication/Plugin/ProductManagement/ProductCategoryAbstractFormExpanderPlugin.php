<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\ProductManagementAi\Communication\Plugin\ProductManagement;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Orm\Zed\Category\Persistence\Map\SpyCategoryAttributeTableMap;
use Orm\Zed\Category\Persistence\Map\SpyCategoryNodeTableMap;
use Orm\Zed\Category\Persistence\Map\SpyCategoryTableMap;
use Orm\Zed\Category\Persistence\SpyCategoryQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Gui\Communication\Form\Type\Select2ComboBoxType;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductManagementExtension\Dependency\Plugin\ProductAbstractFormExpanderPluginInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @method \SprykerEco\Zed\ProductManagementAi\Communication\ProductManagementAiCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\ProductManagementAi\ProductManagementAiConfig getConfig()
 * @method \SprykerEco\Zed\ProductManagementAi\Business\ProductManagementAiFacadeInterface getFacade()
 */
class ProductCategoryAbstractFormExpanderPlugin extends AbstractPlugin implements ProductAbstractFormExpanderPluginInterface
{
    /**
     * @see \Spryker\Zed\ProductManagement\Communication\Form\ProductFormAdd::FIELD_ID_PRODUCT_ABSTRACT
     *
     * @var string
     */
    protected const KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    /**
     * {@inheritDoc}
     * - Expands ProductAbstract form with categoryIds field.
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
        $builder->add(ProductAbstractTransfer::CATEGORY_IDS, Select2ComboBoxType::class, [
            'label' => 'categories',
            'placeholder' => 'select-category',
            'multiple' => true,
            'required' => false,
            'empty_data' => [],
            'choices' => $this->getFactory()->createProductCategoryAbstractFormDataProvider()->getOptions(),
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $data = $event->getData();

                if (!$data || !isset($data[self::KEY_ID_PRODUCT_ABSTRACT])) {
                    return;
                }

                $data[ProductAbstractTransfer::CATEGORY_IDS] = $this->getProductCategoryIds($data[self::KEY_ID_PRODUCT_ABSTRACT]);
                $event->setData($data);
            },
        );

        return $builder;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    protected function getProductCategoryIds(int $idProductAbstract): array
    {
        $currentLocaleId = $this->getFactory()->getLocaleFacade()->getCurrentLocale()->getIdLocale();
        // Use Facade
        $categoryCollection = SpyCategoryQuery::create()
            ->joinAttribute()
            ->innerJoinNode()
            ->addAnd(
                SpyCategoryAttributeTableMap::COL_FK_LOCALE,
                $currentLocaleId,
                Criteria::EQUAL,
            )
            ->withColumn(SpyCategoryTableMap::COL_ID_CATEGORY, 'id_category')
            ->withColumn(SpyCategoryAttributeTableMap::COL_NAME, 'name')
            ->withColumn(SpyCategoryTableMap::COL_CATEGORY_KEY, 'category_key')
            ->withColumn(SpyCategoryNodeTableMap::COL_ID_CATEGORY_NODE, 'id_category_node')
            ->joinSpyProductCategory()
            ->useSpyProductCategoryQuery()
            ->filterByFkProductAbstract($idProductAbstract)
            ->endUse()
            ->select([SpyCategoryTableMap::COL_ID_CATEGORY])
            ->find()
            ->toArray();

        $categoryList = [];
        foreach ($categoryCollection as $category) {
            $categoryList[] = $category[SpyCategoryTableMap::COL_ID_CATEGORY];
        }

        return $categoryList;
    }
}
