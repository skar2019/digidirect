<?php
namespace Ewave\ProductAttachment\Ui\DataProvider\Attachment\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Attachment
 * @package Ewave\ProductAttachment\Ui\DataProvider\Attachment\Form
 */
class Attachment implements ModifierInterface
{
    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $meta['general']['children']['file'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => 'string',
                        'source' => 'attachment',
                        'label' => __('File'),
                        'visible' => true,
                        'formElement' => 'fileUploader',
                        'componentType' => 'fileUploader',
                        'notice' => __('Max allowed file size %1', ini_get('upload_max_filesize')),
                        'previewTmpl' => __('Ewave_ProductAttachment/file-preview'),
                        'elementTmpl' => __('ui/form/element/uploader/uploader'),
                        'sortOrder' => 40,
                        'scopeLabel' => '[GLOBAL]',
                        'uploaderConfig' => ['url' => 'ewave_product_attachment/index/upload'],
                        'validation' => ['required-entry' => true]
                    ]
                ]
            ]
        ];
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
