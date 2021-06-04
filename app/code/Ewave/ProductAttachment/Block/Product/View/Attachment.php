<?php
namespace Ewave\ProductAttachment\Block\Product\View;

use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;
use Ewave\ProductAttachment\Model\Registry\Constants;

/**
 * Class Attachment
 * @package Ewave\ProductAttachment\Block\Product\View
 */
class Attachment extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ewave\ProductAttachment\Helper\Data
     */
    protected $helper;

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
     * Attachment constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Ewave\ProductAttachment\Helper\Data $helper
     * @param \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface $attachmentRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ewave\ProductAttachment\Helper\Data $helper,
        \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface $attachmentRepository,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
        $this->attachmentRepository = $attachmentRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return \Ewave\ProductAttachment\Model\ResourceModel\Attachment\Collection
     */
    public function getAttachments()
    {
        return $this->attachmentRepository->getProductAttachments(
            $this->getProduct()->getId(),
            $this->_storeManager->getStore()->getId(),
            AttachmentModel::STATUS_ACTIVE
        );
    }

    /**
     * @param string $size
     * @return string
     */
    public function getFileSize($size)
    {
        return $this->helper->toByteString($size);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getFileFormat($format)
    {
        return $this->helper->formatFileExt($format);
    }
    
    /**
     * @param int $attachmentId
     * @return string
     */
    public function getDownloadLink($attachmentId)
    {
        return $this->getUrl('product-attachment/download/index', ['id' => $attachmentId]);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry(Constants::CURRENT_PRODUCT);
    }

    /**
     * @param AttachmentModel $attachment
     * @return bool
     */
    public function canBeShown(AttachmentModel $attachment)
    {
        return $attachment->isActive() && $attachment->isExists() && $this->allowForCurrentStore($attachment);
    }

    /**
     * @param AttachmentModel $attachment
     * @return bool
     */
    protected function allowForCurrentStore(AttachmentModel $attachment)
    {
        if ($attachment->getAttachedStore() !== null
            && $attachment->getAttachedStore() == AttachmentModel::NOT_ATTACHED_FLAG) {
            return false;
        }
        return $attachment->getAttachedStore() || $attachment->getAttachedAll();
    }
}
