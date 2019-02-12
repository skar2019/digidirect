<?php
namespace Ewave\ProductAttachment\Controller\Adminhtml\Index;

use Ewave\ProductAttachment\Model\AttachmentFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ewave\ProductAttachment\Model\AttachmentRepository;
use Ewave\ProductAttachment\Model\Registry\Constants;

/**
 * Class Edit
 * @package Ewave\ProductAttachment\Controller\Adminhtml\Index
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param AttachmentRepository $attachmentRepository
     * @param AttachmentFactory $attachmentFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        AttachmentRepository $attachmentRepository,
        AttachmentFactory $attachmentFactory
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->registry             = $registry;
        $this->attachmentFactory      = $attachmentFactory;
        $this->attachmentRepository   = $attachmentRepository;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $request = $this->getRequest();
        $id = (int)$request->getParam('id', 0);
        $storeId = $this->getRequest()->getParam('store', 0);
        $this->registry->register(Constants::CURRENT_STORE_ID, $storeId);
        /**
         * @var $model \Ewave\ProductAttachment\Model\AttachmentRepository
         */
        $model = $this->attachmentFactory->create();

        if ($id) {
            $model = $this->attachmentRepository->getByIdWithAttributes($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This attachment doesn\'t exist'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $model->setStoreId($storeId);
        $this->registry->register(Constants::CURRENT_ATTACHMENT_ITEM, $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ewave_ProductAttachment::product_attachment')
            ->addBreadcrumb(__('Ewave Attachment'), __('Ewave Attachment'))
            ->addBreadcrumb(
                $id ? __('Edit Attachment') : __('New Attachment'),
                $id ? __('Edit Attachment') : __('New Attachment')
            );
        $resultPage->getConfig()->getTitle()->prepend(__('Attachment Management'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Attachment'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_ProductAttachment::product_attachment_items_save');
    }
}
