<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic\Attribute\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Ewave\Feed\Block\Adminhtml\Dynamic\AbstractEditForm;
use Ewave\Feed\Block\Adminhtml\Dynamic\Attribute\Edit\Renderer\Conditions;

class Form extends AbstractEditForm
{
    /**
     * @var Conditions
     */
    protected $conditionsElement;

    /**
     * Form constructor.
     * @param Context $context
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Conditions $conditionsElement
     * @param array $data
     */
    public function __construct(
        Context $context,
        FormFactory $formFactory,
        Registry $registry,
        Conditions $conditionsElement,
        array $data = []
    ) {
        $this->conditionsElement = $conditionsElement;

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

        $form->getElement('code')->setData('label', __('Attribute Code'));

        $fieldset->addElement($this->conditionsElement);

        return $return;
    }
}
