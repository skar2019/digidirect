<?php

namespace Ewave\ProductCalculator\Ui\DataProvider\FieldGroupCategory\Form\Modifier;

use Ewave\ProductCalculator\Model\Constants;
use Ewave\ProductCalculator\Model\FieldGroupCategory;
use Ewave\ProductCalculator\Model\Source\Status;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class FieldGroups implements ModifierInterface
{
    const FIELD_GROUPS_FIELDSET = 'field_groups';
    const SCOPE_NAME = 'productcalculator_fieldgroupcategory_form.productcalculator_fieldgroupcategory_form';
    const FIELD_GROUPS_GRID_NAME = 'calculator_field_groups_grid';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Status
     */
    private $statusSource;

    /**
     * RelatedFields constructor.
     * @param UrlInterface $urlBuilder
     * @param Registry $coreRegistry
     * @param Status $statusSource
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Registry $coreRegistry,
        Status $statusSource
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $coreRegistry;
        $this->statusSource = $statusSource;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::FIELD_GROUPS_FIELDSET => [
                    'children' => [
                        'button_set' => $this->getButtonSet(
                            __('Add User Input Group')
                        ),
                        'modal' => $this->getModal(
                            __('Add User Input Group')
                        ),
                        static::FIELD_GROUPS_FIELDSET => $this->getGrid(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'additionalClasses' => 'admin__fieldset-section',
                                'label' => __('User Input Groups'),
                                'collapsible' => false,
                                'componentType' => Fieldset::NAME,
                                'sortOrder' => 30,
                                'dataScope' => ''
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var FieldGroupCategory $fieldGroupCategory */
        $fieldGroupCategory = $this->coreRegistry->registry(Constants::CURRENT_FIELD_GROUP_CATEGORY);
        if ($fieldGroupCategory && $fieldGroupCategory->getId()) {
            foreach ($fieldGroupCategory->getFieldGroups() as $fieldGroup) {
                $data[$fieldGroupCategory->getId()]['links'][self::FIELD_GROUPS_FIELDSET][] = $fieldGroup->getData();
            }
        }

        return $data;
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $buttonTitle
     * @return array
     */
    protected function getButtonSet(Phrase $buttonTitle)
    {
        $modalTarget = static::SCOPE_NAME . '.' . static::FIELD_GROUPS_FIELDSET . '.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_add_field_group' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . self::FIELD_GROUPS_GRID_NAME,
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @return array
     */
    protected function getModal(Phrase $title)
    {
        $providerPrefix = self::FIELD_GROUPS_GRID_NAME . '.' . self::FIELD_GROUPS_GRID_NAME;

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => $title,
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . self::FIELD_GROUPS_GRID_NAME,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                self::FIELD_GROUPS_GRID_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => self::FIELD_GROUPS_GRID_NAME,
                                'externalProvider' => $providerPrefix . '_data_source',
                                'selectionsProvider' => $providerPrefix . '.calculator_field_groups_columns.ids',
                                'ns' => self::FIELD_GROUPS_GRID_NAME,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => self::FIELD_GROUPS_GRID_NAME,
                        'map' => [
                            'id' => 'id',
                            'name' => 'name',
                            'status' => 'status',
                            'priority' => 'priority',
                            'label' => 'label',
                            'type' => 'type'
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'name' => $this->getTextColumn('name', false, __('Name'), 10),
            'status' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'component' => 'Magento_Ui/js/form/element/select',
                            'elementTmpl' => 'Ewave_ProductCalculator/dynamic-rows/cells/select',
                            'dataType' => 'text',
                            'dataScope' => 'status',
                            'label' => __('Status'),
                            'options' => $this->statusSource->toOptionArray(),
                            'sortOrder' => 20,
                        ],
                    ],
                ],
            ],
            'priority' => $this->getTextColumn('priority', false, __('Priority'), 30),
            'label' => $this->getTextColumn('label', false, __('Label'), 40),
            'type' => $this->getTextColumn('type', false, __('Type'), 50),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
