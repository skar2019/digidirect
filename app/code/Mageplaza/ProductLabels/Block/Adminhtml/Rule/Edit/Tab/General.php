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

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\System\Store;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Model\Rule;

/**
 * Class General
 * @package Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'Mageplaza_ProductLabels::rule/general.phtml';

    /**
     * @var Enabledisable
     */
    protected $_enableDisable;

    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    protected $_objectConverter;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Enabledisable $enableDisable
     * @param Yesno $yesNo
     * @param Store $systemStore
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $enableDisable,
        Yesno $yesNo,
        Store $systemStore,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_enableDisable         = $enableDisable;
        $this->_yesNo                 = $yesNo;
        $this->systemStore            = $systemStore;
        $this->_groupRepository       = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter       = $objectConverter;
        $this->_helperData            = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Rule $rule */
        $rule = $this->_coreRegistry->registry('mageplaza_productlabels_rule');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $form->setFieldNameSuffix('rule');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('General Information'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('enabled', 'select', [
            'name'   => 'enabled',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->_enableDisable->toOptionArray()
        ]);
        if (!$rule->hasData('enabled')) {
            $rule->setEnabled(1);
        }

        $fieldset->addField('state', 'note', [
            'label' => __('State'),
            'text'  => $this->_helperData->getState($rule)
        ]);

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name'   => 'store_ids',
                'label'  => __('Store Views'),
                'title'  => __('Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$rule->hasData('store_ids')) {
                $rule->setStoreIds(0);
            }
        }

        $customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems();
        $fieldset->addField('customer_group_ids', 'multiselect', [
            'name'     => 'customer_group_ids[]',
            'label'    => __('Customer Group(s)'),
            'title'    => __('Customer Group(s)'),
            'values'   => $this->_objectConverter->toOptionArray($customerGroups, 'id', 'code'),
            'required' => true,
            'note'     => __('Select customer group(s) to display the label')
        ]);

        $dateFormat = 'M/d/yyyy';
        $fieldset->addField('from_date', 'date', [
            'name'         => 'from_date',
            'label'        => __('From Date'),
            'title'        => __('From'),
            'input_format' => DateTime::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat,
            'timezone'     => false
        ]);
        $fieldset->addField('to_date', 'date', [
            'name'         => 'to_date',
            'label'        => __('To Date'),
            'title'        => __('To'),
            'input_format' => DateTime::DATE_INTERNAL_FORMAT,
            'date_format'  => $dateFormat,
            'timezone'     => false
        ]);

        $fieldset->addField('stop_process', 'select', [
            'name'   => 'stop_process',
            'label'  => __('Stop further processing'),
            'title'  => __('Stop further processing'),
            'values' => $this->_yesNo->toOptionArray()
        ]);

        $fieldset->addField('priority', 'text', [
            'name'  => 'priority',
            'label' => __('Priority'),
            'title' => __('Priority'),
            'class' => 'validate-digits'
        ]);

        $form->setValues($rule->getData());
        $this->setForm($form);

        return parent::_prepareForm();
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
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }
}
