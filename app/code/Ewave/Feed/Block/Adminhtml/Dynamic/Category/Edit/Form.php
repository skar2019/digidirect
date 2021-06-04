<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic\Category\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Ewave\Feed\Block\Adminhtml\Dynamic\AbstractEditForm;
use Ewave\Feed\Block\Adminhtml\Dynamic\Category\Edit\Renderer\Mapping;

class Form extends AbstractEditForm
{
    /**
     * @var Mapping
     */
    protected $mappingElement;

    /**
     * Form constructor.
     * @param Context $context
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Mapping $mappingElement
     * @param array $data
     */
    public function __construct(
        Context $context,
        FormFactory $formFactory,
        Registry $registry,
        Mapping $mappingElement,
        array $data = []
    ) {
        $this->mappingElement = $mappingElement;

        parent::__construct($context, $formFactory, $registry, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $return = parent::_prepareForm();

        $form = $this->getForm();
        $fieldset = $form->getElement('general_information');

        $form->getElement('code')->setData('label', __('Mapping Code'));

        $fieldset->addElement($this->mappingElement);

        return $return;
    }
}
