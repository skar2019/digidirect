<?php

namespace Ewave\Banner\Block\Adminhtml\Banner\Edit\Tab;

use Ewave\Banner\Model\Attributes\NavigationTitle;
use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Ewave\Banner\Model\Attributes\TargetType\Config as TargetTypeConfig;
use Ewave\Banner\Model\Attributes\TargetLink;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Ewave\Banner\Source\Categories;
use Magento\Banner\Model\Banner as BannerModel;

class Properties extends BackendTemplate
{
    const CHOOSER_CLASS = \Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser::class;

    /**
     * @var \Ewave\Banner\Model\Attributes\TargetType\Config
     */
    protected $targetTypeConfig;

    /**
     * @var \Ewave\Banner\Model\Attributes\TargetLink
     */
    protected $targetTypeAttribute;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \Ewave\Banner\Source\Categories
     */
    protected $categories;

    /**
     * @var NavigationTitle
     */
    protected $navigationTitleAttribute;

    /**
     * @var string
     */
    protected $skipWidgetClass;

    /**
     * Properties constructor.
     *
     * @param TemplateContext $context
     * @param TargetTypeConfig $config
     * @param TargetLink $targetType
     * @param WysiwygConfig $wysiwygConfig
     * @param Categories $categories
     * @param NavigationTitle $navigationTitle
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        TargetTypeConfig $config,
        TargetLink $targetType,
        WysiwygConfig $wysiwygConfig,
        Categories $categories,
        NavigationTitle $navigationTitle,
        $skipWidgetClass,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->targetTypeConfig = $config;
        $this->targetTypeAttribute = $targetType;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->categories = $categories;
        $this->navigationTitleAttribute = $navigationTitle;
        $this->skipWidgetClass = $skipWidgetClass;
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\DataObject
     */
    protected function _getWysiwygConfig($form)
    {
        return $this->wysiwygConfig->getConfig(
            ['tab_id' => $form->getTabId(), 'skip_widgets' => [$this->skipWidgetClass]]
        );
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @param BannerModel $bannerModel
     * @param \Magento\Backend\Block\Widget\Form\Element\Dependence $afterForm
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function addTargetTypeFields(
        \Magento\Framework\Data\Form $form,
        BannerModel $bannerModel,
        \Magento\Backend\Block\Widget\Form\Element\Dependence $afterForm
    ) {
        $prefix = $form->getHtmlIdPrefix();

        $baseFieldset = $form->getElement('base_fieldset');

        $baseFieldset->addField(
            'navigation_title_type',
            'select',
            [
                'label' => __('Navigation Type'),
                'title' => __('Navigation Type'),
                'options' => $this->navigationTitleAttribute->getNavigationTypes(),
                'name' => 'navigation_title_type',
            ]
        );

        $baseFieldset->addField(
            'navigation_title',
            'editor',
            [
                'name' => 'navigation_title',
                'label' => __('Navigation Title'),
                'title' => __('Navigation Title'),
                'required' => false,
                'wysiwyg' => true,
                'config' => $this->_getWysiwygConfig($form),
            ]
        );

        $baseFieldset->addField(
            'navigation_image',
            'image',
            [
                'name' => 'navigation_image',
                'label' => __('Navigation Image'),
                'title' => __('Navigation Image'),
            ]
        );

        $fieldset = $form->addFieldset(
            'entity_link_options',
            ['legend' => __('Link Options'), 'class' => 'ewave_banner_link_options']
        );

        $targetTypeField = $fieldset->addField(
            'target_type',
            'select',
            [
                'name' => 'target_type',
                'label' => __('Select Link Type'),
                'title' => __('Select Link Type'),
                'required' => false,
                'options' => $this->targetTypeConfig->toOptionArray(),
            ]
        );

        $productField = $fieldset->addField(
            'ewave_grid_widget_product_id',
            'label',
            [
                'name' => 'product_id',
                'label' => __('Select Product'),
                'title' => __('Select Product'),
                'required' => true,
                'class' => 'widget-option',
                'value' => $this->targetTypeAttribute->getBackendAttributeForEdit($bannerModel, 'product_id'),
            ]
        );

        $data = [
            'button' => [
                'open' => __('Select product...'),
            ],
            'type' => static::CHOOSER_CLASS,
        ];

        $widget = $this->_layout->createBlock(static::CHOOSER_CLASS, 'product_chooser_banner', ['data' => $data]);

        if ($widget instanceof \Magento\Framework\DataObject) {
            $widget->setConfig([$data])
                ->setFieldsetId($fieldset->getId())
                ->prepareElementHtml($productField);
        }

        $categoryField = $fieldset->addField(
            'category_id',
            'select',
            [
                'name' => 'category_id',
                'label' => __('Select Category'),
                'title' => __('Select Category'),
                'required' => true,
                'class' => 'widget-option',
                'value' => $this->targetTypeAttribute->getBackendAttributeForEdit($bannerModel, 'category_id'),
                'values' => $this->categories->toOptionArray(),
            ]
        );

        $customLinkField = $fieldset->addField(
            'custom_link',
            'text',
            [
                'name' => 'custom_link',
                'label' => __('Custom Link'),
                'title' => __('Custom Link'),
                'required' => true,
                'class' => 'widget-option',
                'value' => $this->targetTypeAttribute->getBackendAttributeForEdit($bannerModel, 'custom_link'),
                'note' => __(
                    'The field allow fully qualified URLs that end with \'/\' (slash) e.g. http://example.com/'
                ),
            ]
        );

        $afterForm
            ->addFieldMap($prefix . $categoryField->getId(), $categoryField->getName())
            ->addFieldMap($prefix . $targetTypeField->getId(), $targetTypeField->getName())
            ->addFieldMap($prefix . $customLinkField->getId(), $customLinkField->getName())
            ->addFieldMap($productField->getId(), $productField->getName())
            ->addFieldMap($prefix . 'navigation_title_type', 'navigation_title_type')
            ->addFieldMap($prefix . 'navigation_image', 'navigation_image')
            ->addFieldDependence(
                'navigation_image',
                'navigation_title_type',
                NavigationTitle::NAVIGATION_TYPE_TITLE_IMAGE
            )
            ->addFieldDependence($categoryField->getName(), $targetTypeField->getName(), 'category')
            ->addFieldDependence($customLinkField->getName(), $targetTypeField->getName(), 'custom_link')
            ->addFieldDependence($productField->getName(), $targetTypeField->getName(), 'product');

        return $this;
    }
}
