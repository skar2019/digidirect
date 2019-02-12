<?php
namespace Ewave\ProductAttachment\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Ewave\ProductAttachment\Api\AttachmentRepositoryInterface;

/**
 * Class Delete
 * @package Ewave\ProductAttachment\Controller\Adminhtml\Index
 */
class Delete extends Action
{
    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param AttachmentRepositoryInterface $attachmentRepository
     */
    public function __construct(
        Action\Context $context,
        AttachmentRepositoryInterface $attachmentRepository
    ) {
        parent::__construct($context);
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->attachmentRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the attachment item.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a attachment item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if Is allowed to delete
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_ProductAttachment::product_attachment_items_delete');
    }
}
