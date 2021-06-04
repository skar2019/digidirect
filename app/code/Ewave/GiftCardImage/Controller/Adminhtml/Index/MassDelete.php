<?php
namespace Ewave\GiftCardImage\Controller\Adminhtml\Index;

use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\GiftCardImageRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * Class MassDelete
 * @package Ewave\GiftCardImage\Controller\Adminhtml\Index
 */
class MassDelete extends Action
{
    /**
     * @var GiftCardImageRepositoryInterface|GiftCardImageRepository
     */
    protected $giftCardImageRepository;

    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var array
     */
    protected $data;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param GiftCardImageRepositoryInterface $giftCardImageRepository
     * @param Filter $filter
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param array $data
     */
    public function __construct(
        Context $context,
        GiftCardImageRepositoryInterface $giftCardImageRepository,
        Filter $filter,
        TypeListInterface $typeList,
        array $data = []
    ) {
        $this->giftCardImageRepository = $giftCardImageRepository;
        $this->filter = $filter;
        $this->typeList = $typeList;
        $this->data = $data;
        parent::__construct($context);
    }

    /**
     * Mass delete options
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('selected', []);
        if (empty($ids)) {
            $collection = $this->filter->getCollection($this->giftCardImageRepository->getCollection());
            $ids = $collection->getColumnValues(GiftCardImageInterface::ID);
        }

        $counter = 0;
        foreach ($ids as $id) {
            try {
                $this->giftCardImageRepository->deleteById($id);
                $counter++;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $counter)
        );

        if ($counter > 0) {
            $this->typeList->invalidate($this->data['invalidate_cache_types'] ?? []);
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
