<?php
namespace Ewave\SEO\Model;

/**
 * Interface MetadataInterface
 * @package Ewave\SEO\Model
 */
interface MetadataInterface
{
    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function setMetadata(\Magento\Framework\Model\AbstractModel $object);

    /**
     * @return bool
     */
    public function isEnabled();
}
