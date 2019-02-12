<?php

// @codingStandardsIgnoreFile

namespace Ewave\AdvancedInventory\Ui\DataProvider\AbstractEntity\Form\Modifier;

use Magento\Ui\Component\Form;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Ewave\AbstractEntity\Ui\DataProvider\Form\Modifier\AbstractModifier;
use Ewave\AdvancedInventory\Helper\Config as Helper;

/**
 * Class Relation
 * @package Ewave\AbstractEntity\Ui\DataProvider\Form\Modifier
 */
class AdvancedInventory extends AbstractModifier
{
    const GROUP_NAME = 'advanced_inventory';
    const DATA_SCOPE = 'ewave_abstractentity_advancedinventory';
    const SORT_ORDER = 150;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Relation constructor.
     * @param Registry $registry
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param Helper $helper
     */
    public function __construct(
        Registry $registry,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        Helper $helper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        parent::__construct($registry, $request);
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->getCurrentEntity()->getEntityId()
            || !in_array($this->getCurrentEntity()->getAttributeSetId(), $this->helper->getAbstractEntities())
        ) {
            return $meta;
        }

        $meta[self::GROUP_NAME] = [
            'children' => [
                self::DATA_SCOPE => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => self::DATA_SCOPE,
                                'externalProvider' => 'ewave_abstractentity_advancedinventory.ewave_abstractentity_advancedinventory_data_source',
                                'ns' => self::DATA_SCOPE,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'entityId' => '${ $.provider }:data.entity.current_entity_id',
                                    'attributeSetId' =>
                                        '${ $.provider }:data.attribute_set.current_attribute_set_id'
                                ],
                                'exports' => [
                                    'entityId' => '${ $.externalProvider }:params.current_entity_id',
                                    'attributeSetId' =>
                                        '${ $.externalProvider }:params.current_attribute_set_id'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Advanced Inventory Stock Items'),
                        'collapsible' => true,
                        'opened' => true,
                        'componentType' => Form\Fieldset::NAME,
                        'sortOrder' => self::SORT_ORDER,
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $entityId = $this->getCurrentEntity()->getEntityId();
        $setId = $this->getCurrentEntity()->getAttributeSetId();

        $data[$entityId]['entity']['current_entity_id'] = $entityId;
        $data[$entityId]['attribute_set']['current_attribute_set_id'] = $setId;

        return $data;
    }
}
