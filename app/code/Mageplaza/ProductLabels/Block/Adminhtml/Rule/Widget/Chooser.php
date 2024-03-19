<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Block\Adminhtml\Rule\Widget;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as WidgetChooser;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;
use Mageplaza\ProductLabels\Model\RuleFactory;

/**
 * Class Chooser
 * @package Mageplaza\ProductLabels\Block\Adminhtml\Rule\Widget
 */
class Chooser extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * Chooser constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param RuleFactory $ruleFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        RuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->ruleFactory        = $ruleFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @throws FileSystemException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['enabled' => '1']);
    }

    /**
     * @param AbstractElement $element
     *
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId    = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('mpproductlabels/rule_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()->createBlock(
            WidgetChooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        $chooser->setLabel(__('Not Selected'));

        if ($element->getValue()) {
            $rule = $this->ruleFactory->create()->load($element->getValue());
            $chooser->setLabel($rule->getName());
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $labelJsObject = $this->getId();
        $js            = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML;
                ' .
            $labelJsObject .
            '.setElementValue(blockId);
                ' .
            $labelJsObject .
            '.setElementLabel(blockTitle);
                ' .
            $labelJsObject .
            '.close();
            }
        ';

        return $js;
    }

    /**
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_collectionFactory->create());

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'rule_id',
            ['header' => __('ID'), 'align' => 'right', 'index' => 'rule_id', 'width' => 50]
        );

        $this->addColumn('name', ['header' => __('Name'), 'align' => 'left', 'index' => 'name']);

        $this->addColumn(
            'enabled',
            [
                'header'  => __('Status'),
                'index'   => 'enabled',
                'type'    => 'options',
                'options' => [0 => __('Disabled'), 1 => __('Enabled')]
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('mpproductlabels/rule_widget/chooser', ['_current' => true]);
    }
}
