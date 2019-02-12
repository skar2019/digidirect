<?php
namespace Ewave\AbstractEntity\Controller\Adminhtml\Attribute;

use Ewave\AbstractEntity\Controller\Adminhtml\Attribute as AttributeController;
use Magento\Eav\Model\Entity\Attribute\Source\Table as SourceTable;
use Magento\Framework\Exception\LocalizedException;

class Save extends AttributeController
{
    /**
     * Save attribute action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($this->getRequest()->isPost() && $data) {
            $attributeObject = $this->_initAttribute();
            try {
                $data = $this->_attributeHelper->filterPostData($data);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if (isset($data['attribute_id'])) {
                    $this->_redirect('*/*/edit', ['_current' => true]);
                } else {
                    $this->_redirect('*/*/new', ['_current' => true]);
                }
                return;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->messageManager->addErrorMessage(__('You cannot edit this attribute.'));
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code'] = $attributeObject->getAttributeCode();
                $data['frontend_input'] = $attributeObject->getFrontendInput();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
                $data['is_system'] = $attributeObject->getIsSystem();
            } else {
                if ($sourceModel = trim($data['source_model'])) {
                    $data['source_model'] = $sourceModel;
                    $data['backend_model'] = '';
                } else {
                    $data['source_model'] = $this->_attributeHelper->getAttributeSourceModelByInputType(
                        $data['frontend_input']
                    );
                    $data['backend_model'] = $this->_attributeHelper->getAttributeBackendModelByInputType(
                        $data['frontend_input']
                    );
                }
                $data['backend_type'] = $this->_attributeHelper->getAttributeBackendTypeByInputType(
                    $data['frontend_input']
                );

                if ($data['frontend_input'] == 'image') {
                    $data['backend_model'] = \Ewave\Store\Model\Store\Attribute\Backend\Image::class;
                }

                $data['entity_type_id'] = $this->_getEntityType()->getEntityTypeId();
                $data['is_user_defined'] = 1;
                $data['is_system'] = 0;

                // add set and group info
                $data['attribute_set_id'] = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = $this->_objectManager->create(
                    \Magento\Eav\Model\Entity\Attribute\Set::class
                )->getDefaultGroupId(
                    $data['attribute_set_id']
                );
            }

            $defaultValueField = $this->_attributeHelper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            $data['validate_rules'] = $this->_attributeHelper->getAttributeValidateRules(
                $data['frontend_input'],
                $data
            );

            $attributeObject->addData($data);

            if ($attributeObject->getData('source_model') == SourceTable::class) {
                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                    foreach ($useDefaults as $key) {
                        $attributeObject->setData($key, null);
                    }
                }

                $attributeObject->setCanManageOptionLabels(true);
            }

            try {
                $attributeObject->save();

                $this->messageManager->addSuccessMessage(__('You saved the attribute.'));
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect(
                        '*/*/edit',
                        ['attribute_id' => $attributeObject->getId(), '_current' => true]
                    );
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', ['_current' => true]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the attribute.')
                );
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', ['_current' => true]);
                return;
            }
        }
        $this->_redirect('*/*/');
        return;
    }
}
