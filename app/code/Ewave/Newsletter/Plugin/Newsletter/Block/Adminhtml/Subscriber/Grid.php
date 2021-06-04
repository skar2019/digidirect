<?php

namespace Ewave\Newsletter\Plugin\Newsletter\Block\Adminhtml\Subscriber;

use Ewave\Newsletter\Helper\Config;
use Magento\Backend\Block\Widget\Grid\ColumnFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection;
use Psr\Log\LoggerInterface;

/**
 * Class Grid
 *
 * @package Ewave\Newsletter\Plugin\Newsletter\Block\Adminhtml\Subscriber
 */
class Grid
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var string
     */
    protected $columnSetBlock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $customerResourceModel;

    /**
     * ColumnSet constructor.
     *
     * @param Config $configHelper
     * @param LayoutInterface $layout
     * @param ColumnFactory $columnFactory
     * @param CustomerResourceModel $customerResourceModel
     * @param string $columnSetBlock
     */
    public function __construct(
        Config $configHelper,
        LayoutInterface $layout,
        ColumnFactory $columnFactory,
        CustomerResourceModel $customerResourceModel,
        $columnSetBlock = 'adminhtml.newslettrer.subscriber.grid.columnSet'
    ) {
        $this->configHelper = $configHelper;
        $this->layout = $layout;
        $this->columnFactory = $columnFactory;
        $this->columnSetBlock = $columnSetBlock;
        $this->customerResourceModel = $customerResourceModel;
    }

    /**
     * @see \Magento\Backend\Block\Widget\Grid::getColumnSet
     * @param \Magento\Newsletter\Block\Adminhtml\Subscriber\Grid $subject
     * @param \Magento\Backend\Block\Widget\Grid\ColumnSet|\Magento\Framework\View\Element\Template $result
     * @return \Magento\Backend\Block\Widget\Grid\ColumnSet
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetColumnSet(
        \Magento\Newsletter\Block\Adminhtml\Subscriber\Grid $subject,
        Template $result
    ) {
        if ($subject->hasAdditionalCustomerAttributesColumnsAdded()) {
            return $result;
        }

        $subject->setAdditionalCustomerAttributesColumnsAdded(true);

        $attributes = $this->configHelper->getSubscribersGridAdditionalCustomerAttributes();
        if ($attributes) {
            $collection = $subject->getCollection();
            foreach ($attributes as $attribute) {
                $attribute = $this->getAttribute($attribute);
                if (!$attribute) {
                    continue;
                }

                $this->addColumnByAttributeCode($result, $attribute);
                $this->addAttributeToCollection($collection, $attribute);
            }
        }

        return $result;
    }

    /**
     * @param string $attributeCode
     * @return bool|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected function getAttribute(string $attributeCode)
    {
        return $this->customerResourceModel->getAttribute($attributeCode);
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\ColumnSet|\Magento\Framework\View\Element\Template $columnSetBlock
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addColumnByAttributeCode(Template $columnSetBlock, $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        $blockId = 'adminhtml.newslettrer.subscriber.grid.columnSet.customer.subscriber' . $attributeCode;

        $default = $attribute->getDefaultValue() ?: '----';

        $data = [
            'index' => 'subscriber_' . $attributeCode,
            'header' => $attribute->getFrontendLabel(),
            'type' => 'string',
            'header_css_class' => 'col-' . $attributeCode,
            'column_css_class' => 'col-' . $attributeCode,
            'filter' => 0,
            'default' => $default
        ];

        if ($attribute->getOptions()) {
            $options = [];

            $nonEscapableNbspChar = str_repeat(
                html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8'),
                4
            );

            /** @var \Magento\Customer\Api\Data\OptionInterface $option */
            foreach ($attribute->getOptions() as $option) {
                if (!empty($option->getValue())) {
                    if (is_array($option->getValue())) {
                        foreach ($option->getValue() as $value) {
                            $options[] = [
                                'value' => empty($value['value']) ? '' : $value['value'],
                                'label' => trim(str_replace(
                                    $nonEscapableNbspChar,
                                    '',
                                    $value['label']
                                )) ?: $default,
                            ];
                        }
                    } else {
                        $options[] = [
                            'value' => $option->getValue(),
                            'label' => trim($option->getLabel()) ?: $default,
                        ];
                    }
                }
            }

            if ($options) {
                $data['type'] = 'options';
                $data['options'] = $options;
            }
        }


        /** @var \Magento\Backend\Block\Widget\Grid\Column $column */
        $column = $columnSetBlock->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Grid\Column::class, $blockId)
            ->setData($data);

        $column->setGrid($columnSetBlock->getGrid());

        $columnSetBlock->setChild($blockId, $column);
    }

    /**
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection $collection
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addAttributeToCollection(Collection $collection, $attribute)
    {
        if (!$attribute->getBackendType()) {
            return;
        }

        $attributeCode = $attribute->getAttributeCode();
        $select = $collection->getSelect();
        if ($attribute->getBackendType() == 'static') {
            $select->columns([
                'subscriber_' . $attributeCode => new \Zend_Db_Expr(
                    'IF(customer.' . $attributeCode . ', customer.' . $attributeCode . ', null)'
                )
            ]);
        } else {
            $select->joinLeft(
                [$attributeCode . '_table' => $attribute->getBackend()->getTable()],
                $attributeCode . '_table.entity_id = customer.entity_id'
                . ' AND ' . $attributeCode . '_table.attribute_id = ' . $attribute->getId(),
                [
                    'subscriber_' . $attributeCode => new \Zend_Db_Expr(
                        'IF(' . $attributeCode . '_table.value, ' . $attributeCode . '_table.value, null)'
                    )
                ]
            );
        }
    }
}
