<?php

namespace Ewave\Feed\Block\Adminhtml\Dynamic;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

abstract class AbstractEditForm extends WidgetForm
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Form constructor.
     * @param Context $context
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        FormFactory $formFactory,
        Registry $registry,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $model = $this->getModel();

        $fieldset = $form->addFieldset('general_information', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField($model->getIdFieldName(), 'hidden', [
                'name' => $model->getIdFieldName(),
                'value' => $model->getId(),
            ]);
        }

        $fieldset->addField('name', 'text', [
            'label' => __('Name'),
            'required' => true,
            'name' => 'name',
            'value' => $model->getName(),
        ]);

        $validateCodeClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            $model::CODE_MAX_LENGTH
        );

        $fieldset->addField('code', 'text', [
            'label' => __('Code'),
            'required' => true,
            'name' => 'code',
            'value' => $model->getCode(),
            'class' => $validateCodeClass,
            'note' => __(
                'Make sure you don\'t use spaces or more than %1 symbols.',
                $model::CODE_MAX_LENGTH
            ),
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return \Ewave\Feed\Model\Dynamic\AbstractModel
     */
    public function getModel()
    {
        return $this->registry->registry('current_model');
    }
}
