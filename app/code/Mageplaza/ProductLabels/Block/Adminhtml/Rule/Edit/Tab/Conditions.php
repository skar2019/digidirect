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

namespace Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as MagentoCondition;
use Mageplaza\ProductLabels\Model\Rule;

/**
 * Class Conditions
 * @package Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Tab
 */
class Conditions extends Generic implements TabInterface
{
    /**
     * @var MagentoCondition
     */
    protected $_conditions;

    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * Conditions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param MagentoCondition $conditions
     * @param Fieldset $rendererFieldset
     * @param Yesno $yesNo
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        MagentoCondition $conditions,
        Fieldset $rendererFieldset,
        Yesno $yesNo,
        array $data = []
    ) {
        $this->_conditions       = $conditions;
        $this->_rendererFieldset = $rendererFieldset;
        $this->_yesNo            = $yesNo;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Rule $rule */
        $rule   = $this->_coreRegistry->registry('mageplaza_productlabels_rule');
        $ruleId = $this->getRequest()->getParam('id');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');
        $form->setFieldNameSuffix('rule');

        $renderer = $this->_rendererFieldset->setTemplate('Mageplaza_ProductLabels::rule/conditions.phtml')
            ->setNewChildUrl($this->getUrl('mpproductlabels/condition/newConditionHtml/form/rule_conditions_fieldset'));

        if ($rule->getId()) {
            $renderer->setAjaxUrl($this->getUrl('mpproductlabels/grid/products', [
                'id'       => $ruleId,
                'form_key' => $this->formKey->getFormKey(),
                'loadGrid' => 1
            ]));
        }

        $fieldset = $form->addFieldset('conditions_fieldset', [
            'legend' => __('Product Labels Rules (Leave blank to apply to all products).')
        ])->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', [
            'name'  => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
        ])->setRule($rule)->setRenderer($this->_conditions);

        $fieldset->addField('bestseller', 'select', [
            'name'   => 'bestseller',
            'label'  => __('Best Seller'),
            'title'  => __('Best Seller'),
            'values' => $this->_yesNo->toOptionArray()
        ]);

        $fieldset->addField('new', 'select', [
            'name'   => 'new',
            'label'  => __('New Products'),
            'title'  => __('New Products'),
            'values' => $this->_yesNo->toOptionArray(),
            'note' => __('If Yes, display labels with all products that meet the condition and is new product')
        ]);

        $fieldset->addField('on_sale', 'select', [
            'name'   => 'on_sale',
            'label'  => __('On Sale Products'),
            'title'  => __('On Sale Products'),
            'values' => $this->_yesNo->toOptionArray(),
            'note' => __('If Yes, the label will be displayed with all products that satisfy condition and are on a discount or Special Price')
        ]);

        $fieldset->addField('limit', 'text', [
            'name'  => 'limit',
            'label' => __('Limit'),
            'title' => __('Limit'),
            'class' => 'validate-number validate-digits'
        ]);

        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $this->setConditionFormName($rule->getConditions(), 'rule_conditions_fieldset');
        $form->addValues($rule->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Where To Show');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
