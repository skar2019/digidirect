<?php
namespace Ewave\ProductAttachment\Block\Adminhtml\Product\Edit;

use Ewave\ProductAttachment\Api\AttachmentRepositoryInterface;
use Ewave\ProductAttachment\Block\Adminhtml\Product\ProductTrait;

/**
 * Class AssignAttachments
 * @package Ewave\ProductAttachment\Block\Adminhtml\Product\Edit
 */
class AssignAttachments extends \Magento\Backend\Block\Template
{
    use ProductTrait;
    
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'catalog/product/edit/assign_attachment.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * AssignAttachments constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        AttachmentRepositoryInterface $attachmentRepository,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->attachmentRepository = $attachmentRepository;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Ewave\ProductAttachment\Block\Adminhtml\Product\Edit\Tab\Attachment',
                'product.attachment.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $attachments = $this->attachmentRepository->getProductAttachmentsPosition(
            $this->getProductId(),
            $this->getStoreId()
        );
        if (!empty($attachments)) {
            return $this->jsonEncoder->encode($attachments);
        }
        return '{}';
    }
}
