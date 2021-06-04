<?php

namespace Ewave\Banner\Preference\Magento\Banner\Block\Adminhtml\Banner\Edit\Tab;

use \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content as TabContent;
use Magento\Banner\Model\Banner as BannerModel;

class Content extends TabContent
{
    /**
     * Create Store default content field
     *
     * @see parent::createStoreDefaultContentField
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param BannerModel $model
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    protected function createStoreDefaultContentField($fieldset, $model, $form)
    {
        $storeContents = $this->getStoreContents();
        return $fieldset->addField(
            'store_default_content',
            'editor',
            [
                'name' => 'store_contents[0]',
                'value' => isset($storeContents[0]) ? $storeContents[0] : '',
                'disabled' => $this->isDisabled($model),
                'config' => $this->_getWysiwygConfig(),
                'wysiwyg' => true,
                'container_id' => 'store_default_content',
                'after_element_html' => $this->getAfterHtml($model, $form),
            ]
        );
    }

    /**
     * Create fieldset that provides ability to change content per store view
     *
     * @see parent::_createStoresContentFieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param BannerModel $model
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function _createStoresContentFieldset($form, $model)
    {
        $storeContents = $this->_coreRegistry->registry('current_banner')->getStoreContents();
        $fieldset = $form->addFieldset(
            'scopes_fieldset',
            ['legend' => __('Store View Specific Content'), 'class' => 'store-scope']
        );
        $renderer = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset::class
        );
        $fieldset->setRenderer($renderer);
        $this->_getWysiwygConfig()->setUseContainer(true);
        foreach ($this->_storeManager->getWebsites() as $website) {
            $fieldset->addField(
                "w_{$website->getId()}_label",
                'note',
                ['label' => $website->getName(), 'fieldset_html_class' => 'website']
            );
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField(
                    "sg_{$group->getId()}_label",
                    'note',
                    ['label' => $group->getName(), 'fieldset_html_class' => 'store-group']
                );
                foreach ($stores as $store) {
                    $storeContent = isset($storeContents[$store->getId()]) ? $storeContents[$store->getId()] : '';
                    $contentFieldId = 's_' . $store->getId() . '_content';
                    $wysiwygConfig = clone $this->_getWysiwygConfig();
                    $afterHtml = '<script>require(["prototype"], function () {' .
                        ("if ($('" . $form->getHtmlIdPrefix() . "store_0_content_use').checked) {" .
                            "$('" . $form->getHtmlIdPrefix() . "store_" . $store->getId() . "_content_use" .
                            "').disabled = true;" .
                            "$('" . $form->getHtmlIdPrefix() . "store_" . $store->getId() . "_content_use" .
                            "').checked = false;" .
                            "} else {" .
                            "$('" . $form->getHtmlIdPrefix() . "store_" . $store->getId() . "_content_use" .
                            "').disabled = false;}") .
                        '});</script>';
                    $afterEditorHtml = '<script>require(["prototype"], function () {' .
                        ("if ('" . !$storeContent . "') {" .
                            "if ($('" . $form->getHtmlIdPrefix() . "store_0_content_use').checked) {" .
                            "$('" . $contentFieldId . "').show();" .
                            "} else {" .
                            "$('" . $contentFieldId . "').hide();" .
                            "$('" . $form->getHtmlIdPrefix() . $contentFieldId . "').disabled = true;" .
                            "}" .
                            "} else if ('" . (bool)$model->getIsReadonly() . "') {" .
                            "$('buttons" . $form->getHtmlIdPrefix() . $contentFieldId . "').hide();" .
                            "}") .
                        '});</script>';
                    $fieldset->addField(
                        'store_' . $store->getId() . '_content_use',
                        'checkbox',
                        [
                            'name' => 'store_contents_not_use[' . $store->getId() . ']',
                            'required' => false,
                            'label' => $store->getName(),
                            'value' => $store->getId(),
                            'fieldset_html_class' => 'store',
                            'disabled' => (bool)$model->getIsReadonly(),
                            'onclick' => "\$('{$contentFieldId}').toggle(); \$('" .
                                $form->getHtmlIdPrefix() .
                                $contentFieldId .
                                "').disabled = !$('" .
                                $form->getHtmlIdPrefix() .
                                $contentFieldId .
                                "').disabled;",
                            'checked' => $storeContent ? false : true,
                            'after_element_html' => $afterHtml .
                                '<span>' . __('Use Default') . '</span>',
                            'class' => 'banner-content-checkbox',
                        ]
                    );

                    $fieldset->addField(
                        $contentFieldId,
                        'editor',
                        [
                            'name' => 'store_contents[' . $store->getId() . ']',
                            'required' => false,
                            'disabled' => (bool)$model->getIsReadonly(),
                            'value' => $storeContent,
                            'container_id' => $contentFieldId,
                            'config' => $wysiwygConfig,
                            'wysiwyg' => true,
                            'after_element_html' => $afterEditorHtml,
                        ]
                    );
                }
            }
        }
        return $fieldset;
    }
}
