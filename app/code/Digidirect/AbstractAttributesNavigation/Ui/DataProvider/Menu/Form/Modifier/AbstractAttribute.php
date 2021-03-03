<?php
namespace Digidirect\AbstractAttributesNavigation\Ui\DataProvider\Menu\Form\Modifier;

use Digidirect\AbstractAttributes\Api\AbstractAttributeRepositoryInterface;
use Digidirect\AbstractAttributes\Api\OptionRepositoryInterface;
use Digidirect\Navigation\Ui\DataProvider\Menu\Form\Modifier\MenuModifier;
use Magento\Framework\Registry;
use Digidirect\AbstractAttributes\Model\ResourceModel\Option\Grid\CollectionFactory as OptionsCollectionFactory;

/**
 * Class AbstractAttribute
 * @package Digidirect\AbstractAttributesNavigation\Ui\DataProvider\Menu\Form\Modifier
 */
class AbstractAttribute extends MenuModifier
{
    /**
     * @var AbstractAttributeRepositoryInterface
     */
    protected $abstractAttributeRepository;

    /**
     * @var OptionRepositoryInterface
     */
    protected $abstractAttributeOptionRepository;

    /**
     * AbstractAttribute constructor.
     * @param Registry $registry
     * @param OptionRepositoryInterface $optionRepository
     * @param AbstractAttributeRepositoryInterface $abstractAttributeRepository
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        OptionRepositoryInterface $optionRepository,
        AbstractAttributeRepositoryInterface $abstractAttributeRepository,
        array $data = []
    ) {
        parent::__construct($registry, $data);
        $this->abstractAttributeRepository = $abstractAttributeRepository;
        $this->abstractAttributeOptionRepository = $optionRepository;
    }

    /**
     * Modify data - option_ids from string to array
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $currentMenuItem = $this->_getCurrentMenuItem();
        $defaultData = $currentMenuItem->getDataDefault();
        $storeData = $currentMenuItem->getData();

        $storeData['option_ids'] = isset($storeData['option_ids']) ? explode(',', $storeData['option_ids']) : [];
        $defaultData['option_ids'] = isset($defaultData['option_ids']) ? explode(',', $defaultData['option_ids']) : [];

        $currentMenuItem->setDataDefault($defaultData);
        $currentMenuItem->setData($storeData);

        return $data;
    }

    /**
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $meta[self::MENU_ITEM_INFORMATION_DATASCOPE]['children']['option_ids'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Attribute Options'),
                        'formElement' => 'select',
                        'componentType' => 'field',
                        'component' => 'Digidirect_AbstractAttributesNavigation/js/component/abstract-attributes-multiselect-dropdown',
                        'filterOptions' => true,
                        'chipsEnabled' => true,
                        'disableLabel' => true,
                        'levelsVisibility' => '1',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        'options' => $this->getAbstractAttributesOptions(),
                        'scopeLabel' => __('[Store View]'),
                        'config' => [
                            'dataScope' => self::MENU_ITEM_INFORMATION_DATASCOPE,
                            'sortOrder' => 40,
                        ]
                    ],
                ],
            ]
        ];
        return $meta;
    }

    /**
     * @return []
     */
    protected function getAbstractAttributesOptions()
    {
        $attributes = $this->abstractAttributeRepository->getAbstractAttributes();
        $defaultParent = 0;
        $menuById = [
            $defaultParent => [
                'value' => 'No parent',
                'optgroup' => null
            ]
        ];

        foreach ($attributes as $attribute) {
            $options = $this->abstractAttributeOptionRepository->getAttributeOptions($attribute->getAttributeId());
            foreach ($options as $option) {
                foreach ([$option->getOptionId(), $defaultParent] as $menuId) {
                    if (!isset($menuById[$menuId])) {
                        $menuById[$menuId] = ['value' => $menuId];
                    }
                }

                $menuById[$option->getOptionId()]['is_active'] = $option->getOptionIds();
                $menuById[$option->getOptionId()]['label'] = $option->getLabel();
                $menuById[$defaultParent]['optgroup'][] = &$menuById[$option->getOptionId()];

            }

        }

        return  $menuById[$defaultParent]['optgroup'];
    }
}
