<?php
namespace Ewave\ProductCalculator\Ui\DataProvider\Field\Form\Modifier;

use Magento\Framework\Registry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Ewave\ProductCalculator\Model\Constants;
use Ewave\ProductCalculator\Model\Source\Category;
use Ewave\ProductCalculator\Api\Data\CalculatorInterface;

/**
 * Class Categories
 * @package Ewave\ProductCalculator\Ui\DataProvider\Field\Form\Modifier
 */
class Categories implements ModifierInterface
{
    /**
     * @var Category
     */
    protected $categorySource;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Categories constructor.
     *
     * @param Category $categorySource
     * @param Registry $coreRegistry
     */
    public function __construct(
        Category $categorySource,
        Registry $coreRegistry
    ) {
        $this->categorySource = $categorySource;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        foreach ($data as $id => $item) {
            if (!is_array($item['categories_to_show'])) {
                $data[$id]['categories_to_show'] = explode(',', $item['categories_to_show']);
            }
        }
        return $data;
    }

    /**
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        /** @var CalculatorInterface $field**/
        $calculator = $this->coreRegistry->registry(Constants::CURRENT_CALCULATOR);
        if ($calculator && $calculator->getSeparateProductsByCategories()) {
            $meta['General']['children'] = [
                'categories_to_show' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Categories to Show'),
                                'dataType' => 'text',
                                'formElement' => 'select',
                                'componentType' => 'field',
                                'component' => 'Magento_Ui/js/form/element/ui-select',
                                'selectType' => 'optgroup',
                                'filterOptions' => false,
                                'chipsEnabled' => true,
                                'disableLabel' => true,
                                'levelsVisibility' => '1',
                                'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                'dataScope' => 'categories_to_show',
                                'multiple' => true,
                                'sortOrder' => 60,
                                'options' => $this->categorySource->toOptionArray(),

                            ]
                        ],
                    ]
                ]
            ];
        }

        return $meta;
    }
}
