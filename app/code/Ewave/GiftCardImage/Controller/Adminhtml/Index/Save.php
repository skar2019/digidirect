<?php
namespace Ewave\GiftCardImage\Controller\Adminhtml\Index;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterfaceFactory;
use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * Class Save
 * @package Ewave\GiftCardImage\Controller\Adminhtml\Index
 */
class Save extends Action
{
    /**
     * @var GiftCardImageInterfaceFactory
     */
    protected $giftCardImageFactory;

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
     * Save constructor.
     * @param Context $context
     * @param GiftCardImageInterfaceFactory $giftCardImageFactory
     * @param GiftCardImageRepositoryInterface $giftCardImageRepository
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param array $data
     */
    public function __construct(
        Context $context,
        GiftCardImageInterfaceFactory $giftCardImageFactory,
        GiftCardImageRepositoryInterface $giftCardImageRepository,
        TypeListInterface $typeList,
        array $data = []
    ) {
        $this->giftCardImageFactory = $giftCardImageFactory;
        $this->giftCardImageRepository = $giftCardImageRepository;
        $this->typeList = $typeList;
        $this->data = $data;
        parent::__construct($context);
    }

    /**
     * Save action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $hasError = false;
            /** @var \Ewave\GiftCardImage\Model\GiftCardImage $giftCardImage */
            $giftCardImage = $this->giftCardImageFactory->create($data);
            $giftCardImage->setData($data);

            try {
                $this->giftCardImageRepository->save($giftCardImage);
                $this->messageManager->addSuccessMessage(__('You saved Gift Card Image'));
                $this->typeList->invalidate($this->data['invalidate_cache_types'] ?? []);
            } catch (\Exception $e) {
                $hasError = true;
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $id = $giftCardImage->getId();
            if (($hasError || $this->getRequest()->getParam('back')) && $id) {
                $params = [
                    'giftcard_image_id' => $id,
                    '_current'  => true
                ];
                return $this->resultRedirectFactory->create()->setPath('*/*/edit', $params);
            }

            if ($hasError) {
                $this->_getSession()->setGiftCardImageData($data);
                return $this->resultRedirectFactory->create()->setPath('*/*/edit');
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * Check if Is allowed to save
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_GiftCardImage::gift_card_image_save');
    }
}
