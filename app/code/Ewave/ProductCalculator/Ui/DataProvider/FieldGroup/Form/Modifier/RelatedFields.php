<?php

namespace Ewave\ProductCalculator\Ui\DataProvider\FieldGroup\Form\Modifier;

use Ewave\ProductCalculator\Model\Constants;
use Ewave\ProductCalculator\Model\FieldGroup;
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

class RelatedFields implements ModifierInterface
{
    const RELATED_FIELDS_FIELDSET = 'related_fields';
    const SCOPE_NAME = 'productcalculator_fieldgroup_form.productcalculator_fieldgroup_form';

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
                static::RELATED_FIELDS_FIELDSET => [
                    'children' => [
                        'button_set' => $this->getButtonSet(
                            __('Add User Input Fields'),
                            __('New User Input Fields')
                        ),
                        'modal' => $this->getModal(
                            __('Add User Input Fields')
                        ),
                        static::RELATED_FIELDS_FIELDSET => $this->getGrid(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'additionalClasses' => 'admin__fieldset-section',
                                'label' => __('Fields'),
                                'collapsible' => false,
                                'componentType' => Fieldset::NAME,
                                'sortOrder' => 10,
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
        /** @var FieldGroup $fieldGroup */
        $fieldGroup = $this->coreRegistry->registry(Constants::CURRENT_FIELD_GROUP);
        if ($fieldGroup && $fieldGroup->getId()) {
            foreach ($fieldGroup->getRelatedFields() as $field) {
                $data[$fieldGroup->getId()]['links']['related_fields'][] = $field->getData();
            }
        }

        return $data;
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $buttonTitle
     * @param Phrase $button2Title
     * @return array
     */
    protected function getButtonSet(Phrase $buttonTitle, Phrase $button2Title)
    {
        $modalTarget = static::SCOPE_NAME . '.' . static::RELATED_FIELDS_FIELDSET . '.modal';

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
                'button_add_fields' => [
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
                                        'targetName' => $modalTarget . '.fieldgroup_related_fields_grid',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
                'button_add_new_fields' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Ewave_ProductCalculator/js/form/button',
                                'title' => $button2Title,
                                'href' => $this->urlBuilder->getUrl(
                                    'ewave_productcalculator/field/new'
                                ),
                                'actions' => [],
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
     * @param string $scope
     * @return array
     */
    protected function getModal(Phrase $title)
    {
        $listingTarget = 'fieldgroup_related_fields_grid';

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
                                    'text' => __('Add Selected Fields'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
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
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget .
                                    '.fieldgroup_related_fields_columns.ids',
                                'ns' => $listingTarget,
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
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid()
    {
        $dataProvider = 'fieldgroup_related_fields_grid';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'dndConfig' => [
                            'enabled' => false,
                        ],
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
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'id',
                            'name' => 'name',
                            'status' => 'status',
                            'priority' => 'priority',
                            'label' => 'label',
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
