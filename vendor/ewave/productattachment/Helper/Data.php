<?php
namespace Ewave\ProductAttachment\Helper;

/**
 * Class Data
 * @package Ewave\ProductSorting\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ALLOWED_EXTENSIONS = 'ewave_product_attachment/general/product_attachment_formats';

    /**
     * @return bool
     */
    public function getAllowedExtensions()
    {
        return $this->scopeConfig->getValue(self::ALLOWED_EXTENSIONS);
    }
    
    /**
     * Returns the formatted size
     *
     * @param  integer $size
     * @return string
     */
    public function toByteString($size)
    {
        $sizes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        for ($i=0; $size >= 1024 && $i < 9; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $sizes[$i];
    }

    /**
     * @param string $ext
     * @return string
     */
    public function formatFileExt($ext)
    {
        return strtoupper($ext);
    }
}
