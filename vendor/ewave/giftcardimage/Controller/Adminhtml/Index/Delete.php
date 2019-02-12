<?php
namespace Ewave\GiftCardImage\Controller\Adminhtml\Index;

use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Cache\TypeListInterface;

class Delete extends Action
{
    /**
     * @var GiftCardImageRepositoryInterface
     */
    protected $giftCardImageRepository;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var array
     */
    protected $data;

    /**
     * Delete constructor.
     * @param Context $context
     * @param GiftCardImageRepositoryInterface $giftCardImageRepository
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param array $data
     */
    public function __construct(
        Context $context,
        GiftCardImageRepositoryInterface $giftCardImageRepository,
        TypeListInterface $typeList,
        array $data = []
    ) {
        $this->giftCardImageRepository = $giftCardImageRepository;
        $this->typeList = $typeList;
        $this->data = $data;
        parent::__construct($context);
    }

    /**
     * Delete giftcard image
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('giftcard_image_id');
        try {
            $this->giftCardImageRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('Gift Card Image has been deleted.'));
            $this->typeList->invalidate($this->data['invalidate_cache_types'] ?? []);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if Is allowed to delete
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_GiftCardImage::gift_card_image_delete');
    }
}
