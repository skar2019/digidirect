<?php
namespace Ewave\ProductAttachment\Block\Adminhtml\Product;

use Ewave\ProductAttachment\Model\Registry\Constants;

/**
 * Class ProductTrait
 * @package Ewave\ProductAttachment\Block\Adminhtml\Product
 */
trait ProductTrait
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry(Constants::CURRENT_PRODUCT);
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->registry->registry(Constants::CURRENT_STORE);
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }
}
