<?php
namespace Ewave\ProductAttachment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ewave\ProductAttachment\Api\AttachmentRepositoryInterface;

/**
 * Class SaveProductAttachments
 * @package Ewave\ProductAttachment\Observer
 */
class SaveProductAttachments implements ObserverInterface
{
    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * SaveProductAttachments constructor.
     * @param AttachmentRepositoryInterface $attachmentRepository
     */
    public function __construct(AttachmentRepositoryInterface $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Controller\Adminhtml\Product\Save $controller */
        $controller = $observer->getController();
        $attachments = (string)$controller->getRequest()->getParam('product_attachments');
        $unchecked = $controller->getRequest()->getParam('unchecked_product_attachments');
        $store = $controller->getRequest()->getParam('store');
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();
        $this->attachmentRepository->updateProductRelation($product->getId(), $store, $attachments, $unchecked);
    }
}
