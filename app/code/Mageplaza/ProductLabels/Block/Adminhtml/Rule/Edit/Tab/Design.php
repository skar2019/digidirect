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
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\ProductLabels\Helper\Data as HelperData;
use Mageplaza\ProductLabels\Helper\Image;
use Mageplaza\ProductLabels\Model\Config\Source\Font;
use Mageplaza\ProductLabels\Model\Config\Source\Template;
use Mageplaza\ProductLabels\Model\Rule;

/**
 * Class Design
 * @package Mageplaza\ProductLabels\Block\Adminhtml\Rule\Edit\Tab
 */
class Design extends Generic implements TabInterface
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'Mageplaza_ProductLabels::rule/design.phtml';

    /**
     * @var Enabledisable
     */
    protected $_enableDisable;

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
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Template
     */
    protected $_templateOptions;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var Font
     */
    protected $_font;

    /**
     * Design constructor.
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
     * @param Image $imageHelper
     * @param Template $templateOptions
     * @param HelperData $helperData
     * @param Font $font
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
        Image $imageHelper,
        Template $templateOptions,
        HelperData $helperData,
        Font $font,
        array $data = []
    ) {
        $this->_enableDisable         = $enableDisable;
        $this->_yesNo                 = $yesNo;
        $this->systemStore            = $systemStore;
        $this->_groupRepository       = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter       = $objectConverter;
        $this->imageHelper            = $imageHelper;
        $this->_templateOptions       = $templateOptions;
        $this->_helperData            = $helperData;
        $this->_font                  = $font;

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

        $fieldset = $form->addFieldset('product_base_fieldset', [
            'legend' => __('Product Page'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('label_template', 'select', [
            'name'               => 'label_template',
            'label'              => __('Template'),
            'title'              => __('Template'),
            'after_element_html' => '<a id="load-label-template" class="btn">' . __('Load') . '</a>',
            'values'             => $this->_templateOptions->toOptionArray(),
            'note'               => __('Select a template then click Load')
        ]);

        $fieldset->addField('label_image', \Mageplaza\Core\Block\Adminhtml\Renderer\Image::class, [
            'name'  => 'label_image',
            'label' => __('Image'),
            'title' => __('Image'),
            'path'  => $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_PRODUCT_LABEL)
        ]);

        $fieldset->addField('prod_image_size', 'note', [
            'name'               => 'label_image_size',
            'label'              => __('Image Size'),
            'title'              => __('Image Size'),
            'after_element_html' => '
                    <div class="input-img-width">
                        <input id="label_image_width" type="text"><br>
                        <span class="note">' . __('px (width)') . '</span>
                    </div>                   
                    <div class="input-img-height">
                        <input id="label_image_height" type="text"><br>
                        <span class="note">' . __('px (height)') . '</span>
                    </div>                  
                ',
        ]);

        $fieldset->addField('label', 'text', [
            'name'  => 'label',
            'label' => __('Label'),
            'title' => __('Label'),
        ])->setAfterElementHtml('
            <div class="admin__field-tooltip tooltip">
                <div class="admin__field-tooltip-action action-help mpproductlabel-tooltip" title="Variables:
- {{discount}}
- {{discount_percent}}
- {{current_price}}
- {{attribute_code}}">                 
                </div>             
            </div>
        ');

        $fieldset->addField('label_font', 'select', [
            'name'   => 'label_font',
            'label'  => __('Font family'),
            'title'  => __('Font family'),
            'values' => $this->_font->toOptionArray(),
            'note'   => __('Supports Google Fonts')
        ]);

        $fieldset->addField('label_font_size', 'text', [
            'name'  => 'label_font_size',
            'label' => __('Font Size'),
            'title' => __('Font Size'),
            'class' => 'validate-number',
            'note'  => __('px. The font size of the label')
        ]);
        if (!$rule->hasData('label_font_size')) {
            $rule->setLabelFontSize(14);
        }

        $fieldset->addField('label_color', 'text', [
            'name'  => 'label_color',
            'label' => __('Label color'),
            'title' => __('Label color'),
            'class' => 'jscolor {hash:true,refine:false}'
        ]);
        if (!$rule->hasData('label_color')) {
            $rule->setLabelColor('#000000');
        }

        $fieldset->addField('label_css', 'textarea', [
            'name'  => 'label_css',
            'label' => __('Custom CSS'),
            'title' => __('Custom CSS'),
            'note'  => __('Ex: #design-labels {background-color: #000000}')
        ]);

        $fieldset->addField('label_position', 'hidden', [
            'name' => 'label_position',
        ]);

        $fieldset->addField('preview', 'note', [
            'name'               => 'preview',
            'label'              => __('Select position'),
            'title'              => __('Select position'),
            'after_element_html' => '
                <div id="mpproductlabels-design-filed">               
                <img src="' . $this->getPlaceholderImage() .
                '" id="mpproductlabels-product-img" class="drop_zone" alt="default"/>
                <div id="design-labels" data-id="label" class="draggable" >
                    <img src="" id="design-label-image" alt="label"/>
                    <span id="design-label-text"></span>
                </div>
                <style></style>
                </div>
                <div id="label-position-grid">
                    <div class="squares position-top-left" data-pos="tl"></div>
                    <div class="squares position-top-center" data-pos="tc"></div>
                    <div class="squares position-top-right" data-pos="tr"></div>
                    <div class="squares position-center-left" data-pos="cl"></div>
                    <div class="squares position-center-center" data-pos="cc"></div>
                    <div class="squares position-center-right" data-pos="cr"></div>
                    <div class="squares position-bottom-left" data-pos="bl"></div>
                    <div class="squares position-bottom-center" data-pos="bc"></div>
                    <div class="squares position-bottom-right" data-pos="br"></div>
                    <span class="note">' . __('Select a position for the label or drag & drop the image') . '</span>
                </div>
                ',
        ]);

        $fieldset->addField('label_position_grid', 'hidden', [
            'name' => 'label_position_grid',
        ]);

        $listFieldset = $form->addFieldset('list_base_fieldset', [
            'legend' => __('Product Listing Design'),
            'class'  => 'fieldset-wide'
        ]);

        $listFieldset->addField('same', 'select', [
            'name'   => 'same',
            'label'  => __('Same design with product page'),
            'title'  => __('Same design with product page'),
            'values' => $this->_yesNo->toOptionArray()
        ]);
        if (!$rule->hasData('same')) {
            $rule->setSame(1);
        }

        $listFieldset->addField('list_template', 'select', [
            'name'               => 'list_template',
            'label'              => __('Template'),
            'title'              => __('Template'),
            'after_element_html' => '<a id="load-list-template" class="btn">' . __('Load') . '</a>',
            'values'             => $this->_templateOptions->toOptionArray(),
            'note'               => __('Select a template then click Load')
        ]);

        $path = $rule->getSame() ? Image::TEMPLATE_MEDIA_PRODUCT_LABEL : Image::TEMPLATE_MEDIA_LISTING_LABEL;

        $listFieldset->addField('list_image', \Mageplaza\Core\Block\Adminhtml\Renderer\Image::class, [
            'name'  => 'list_image',
            'label' => __('Image'),
            'title' => __('Image'),
            'path'  => $this->imageHelper->getBaseMediaPath($path)
        ]);

        $listFieldset->addField('list_image_size', 'note', [
            'name'               => 'list_image_size',
            'label'              => __('Image Size'),
            'title'              => __('Image Size'),
            'after_element_html' => '
                    <div class="input-img-width">
                        <input id="list_image_width" type="text"><br>
                        <span class="note">' . __('px (width)') . '</span>
                    </div>               
                    <div class="input-img-height">
                        <input id="list_image_height" type="text"><br>
                        <span class="note">' . __('px (height)') . '</span>
                    </div> 
                ',
        ]);

        $listFieldset->addField('list_label', 'text', [
            'name'  => 'list_label',
            'label' => __('Label'),
            'title' => __('Label'),
        ])->setAfterElementHtml('
            <div class="admin__field-tooltip tooltip">
                <div class="admin__field-tooltip-action action-help mpproductlabel-tooltip" title="Variables:
- {{discount}}
- {{discount_percent}}
- {{current_price}}
- {{attribute_code}}">                 
                </div>             
            </div>
        ');

        $listFieldset->addField('list_font', 'select', [
            'name'   => 'list_font',
            'label'  => __('Font family'),
            'title'  => __('Font family'),
            'values' => $this->_font->toOptionArray(),
            'note'   => __('Supports Google Fonts')
        ]);

        $listFieldset->addField('list_font_size', 'text', [
            'name'  => 'list_font_size',
            'label' => __('Font Size'),
            'title' => __('Font Size'),
            'class' => 'validate-number',
            'note'  => __('px. The font size of the label')
        ]);
        if (!$rule->hasData('list_font_size')) {
            $rule->setListFontSize(14);
        }

        $listFieldset->addField('list_color', 'text', [
            'name'  => 'list_color',
            'label' => __('Label color'),
            'title' => __('Label color'),
            'class' => 'jscolor {hash:true,refine:false}'
        ]);
        if (!$rule->hasData('list_color')) {
            $rule->setListColor('#000000');
        }

        $listFieldset->addField('list_css', 'textarea', [
            'name'  => 'list_css',
            'label' => __('Custom CSS'),
            'title' => __('Custom CSS'),
            'note'  => __('Ex: #design-labels-list {background-color: #000000}')
        ]);

        $listFieldset->addField('list_position', 'hidden', [
            'name' => 'list_position',
        ]);

        $listFieldset->addField('list_preview', 'note', [
            'name'               => 'list_preview',
            'label'              => __('Select position'),
            'title'              => __('Select position'),
            'after_element_html' => '
                <div id="mpproductlabels-design-filed-list">               
                <img src="' . $this->getPlaceholderImage() .
                '" id="mpproductlabels-product-img-list" class="drop_zone" alt="default"/>
                <div id="design-labels-list" data-id="label" class="draggable-list" >
                    <img src="" id="design-label-image-list" alt="label"/>
                    <span id="design-label-text-list"></span>
                </div>
                <style></style>
                </div>
                <div id="list-position-grid">
                    <div class="squares-list position-top-left" data-pos="tl"></div>
                    <div class="squares-list position-top-center" data-pos="tc"></div>
                    <div class="squares-list position-top-right" data-pos="tr"></div>
                    <div class="squares-list position-center-left" data-pos="cl"></div>
                    <div class="squares-list position-center-center" data-pos="cc"></div>
                    <div class="squares-list position-center-right" data-pos="cr"></div>
                    <div class="squares-list position-bottom-left" data-pos="bl"></div>
                    <div class="squares-list position-bottom-center" data-pos="bc"></div>
                    <div class="squares-list position-bottom-right" data-pos="br"></div>
                    <span class="note">' . __('Select a position for the label or drag & drop the image') . '</span>
                </div>
                ',
        ]);

        $listFieldset->addField('list_position_grid', 'hidden', [
            'name' => 'list_position_grid',
        ]);

        $form->setValues($rule->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function getViewFilePath()
    {
        return trim($this->getViewFileUrl('Mageplaza_ProductLabels::images/template/'), '/');
    }

    /**
     * Get url default image magento
     *
     * @return string
     */
    public function getPlaceholderImage()
    {
        return $this->getViewFileUrl('Magento_Catalog::images/product/placeholder/image.jpg');
    }

    /**
     * Get Rule
     *
     * @return mixed
     */
    public function getRule()
    {
        return $this->_coreRegistry->registry('mageplaza_productlabels_rule');
    }

    /**
     * data default preview field
     *
     * @return array|mixed
     */
    public function getDesignFields()
    {
        $design = $this->getRule()->getProductDesign();

        $designFields = [
            'label' => [
                'top'         => 0,
                'left'        => 0,
                'width'       => 0,
                'height'      => 0,
                'percentTop'  => 0,
                'percentLeft' => 0
            ]
        ];

        if (is_string($design) && $design) {
            $designFields = HelperData::jsonDecode($design);
        }

        return $designFields;
    }

    /**
     * Encode data default preview field
     *
     * @return string
     */
    public function getDesignFieldsEncode()
    {
        return $this->escapeHtml(HelperData::jsonEncode($this->getDesignFields()));
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
        return __('Label Design');
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
