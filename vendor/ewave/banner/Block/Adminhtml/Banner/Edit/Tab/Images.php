<?php

namespace Ewave\Banner\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Images extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * Element factory
     *
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * Images constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param [] $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        array $data = []
    ) {
        $this->elementFactory = $factoryElement;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Set tab visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Set tab visible
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Images and Videos');
    }

    /**
     * Get tab tite
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Images and Videos');
    }

    /**
     * Prepare form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('banner_images_gallery_');
        $form->setDataObject($this->_coreRegistry->registry('current_banner'));
        $element = $this->elementFactory->create(
            'Ewave\Banner\Block\Adminhtml\Helper\Form\Gallery\Content',
            ['name' => 'banner_images']
        );

        $fieldSet = $form->addFieldset(
            'default_fieldset',
            [
                'class' => 'banner-images-gallery',
            ]
        );

        $fieldSet->addElement($element);

        $form->setValues($form->getDataObject()->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
