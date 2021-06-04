<?php
namespace Ewave\ProductAttachment\Controller\Adminhtml\Index;

use Ewave\ProductAttachment\Api\AttachmentRepositoryInterface;
use Ewave\ProductAttachment\Model\AttachmentFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

/**
 * Class Save
 * @package Ewave\ProductAttachment\Controller\Adminhtml\Index
 */
class Save extends Action
{
    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var array
     */
    protected $data;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param AttachmentFactory $attachmentFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param array $data
     */
    public function __construct(
        Action\Context $context,
        AttachmentRepositoryInterface $attachmentRepository,
        AttachmentFactory $attachmentFactory,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        array $data = []
    ) {
        $this->attachmentRepository = $attachmentRepository;
        $this->attachmentFactory = $attachmentFactory;
        $this->typeList = $typeList;
        $this->data = $data;
        parent::__construct($context);
    }

    /**
     * Save Menu Item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id', 0);
        $params = $this->getRequest()->getPostValue();
        if ($params) {
            try {
                $attachmentModel = $this->attachmentFactory->create();
                if (!empty($id)) {
                    $attachmentModel = $this->attachmentRepository->getById($id);
                } else {
                    $params['entity_id'] = null;
                }

                if (!$attachmentModel->getId() && $id) {
                    throw new LocalizedException(__('This attachment item no longer exists.'));
                }

                $attachmentModel->setData($params);

                $this->_eventManager->dispatch(
                    'product_attachment_prepare_save',
                    ['attachmentModel' => $attachmentModel, 'request' => $this->getRequest()]
                );

                $this->attachmentRepository->save($attachmentModel);

                $this->messageManager->addSuccessMessage(__('You saved attachment item'));

                $this->typeList->invalidate($this->data['invalidate_cache_types'] ?? []);

                if ($this->getRequest()->getParam('back')) {
                    $redirectParams = ['id' => $attachmentModel->getId()];
                    if (!empty($attachmentModel->getStoreId())) {
                        $redirectParams['store'] = $attachmentModel->getStoreId();
                    }
                    return $resultRedirect->setPath('*/*/edit', $redirectParams);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check permissions for this action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_ProductAttachment::product_attachment_items_save');
    }
}
