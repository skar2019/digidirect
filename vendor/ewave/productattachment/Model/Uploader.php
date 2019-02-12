<?php
namespace Ewave\ProductAttachment\Model;

/**
 * Class Uploader
 * @package Ewave\ProductAttachment\Model
 */
class Uploader extends \Magento\Framework\File\Uploader
{
    /**
     * Init upload
     *
     * @param string|array $fileId
     * @throws \Exception
     */
    public function __construct($fileId)
    {
        try {
            parent::__construct($fileId);
        } catch (\Exception $e) {
            if ($this->_file['error'] == UPLOAD_ERR_INI_SIZE) {
                throw new \Exception(
                    __('Attention. File is too large.  The max file size is %1.', ini_get('upload_max_filesize'))
                );
            }
            throw new \Exception('The file was not uploaded.', $e->getCode());
        }
    }
    
    /**
     * Validate file before save
     *
     * @return void
     * @throws \Exception
     */
    protected function _validateFile()
    {
        if ($this->_fileExists === false) {
            return;
        }

        if (!$this->checkAllowedExtension($this->getFileExtension())) {
            throw new \Exception('Attention. We don\'t recognize or support this file extension type.');
        }

        foreach ($this->_validateCallbacks as $params) {
            if (is_object($params['object'])
                && method_exists($params['object'], $params['method'])
                && is_callable([$params['object'], $params['method']])
            ) {
                $params['object']->{$params['method']}($this->_file['tmp_name']);
            }
        }
    }
}
