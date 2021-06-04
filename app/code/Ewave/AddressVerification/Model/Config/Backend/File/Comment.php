<?php
namespace Ewave\AddressVerification\Model\Config\Backend\File;

/**
 * Class Comment
 * @package Ewave\AddressVerification\Model\Config\Backend\File
 */
class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    /**
     * @param string $elementValue
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCommentText($elementValue)
    {
        return __('Max allowed file size is ') . ini_get('upload_max_filesize');
    }
}
