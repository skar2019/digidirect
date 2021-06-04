<?php
namespace Ewave\OutOfStockNotif\Controller\Unsubscribe;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory;

class StockAll extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $stockCollectionFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $stockCollectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $stockCollectionFactory
    ) {
        $this->stockCollectionFactory = $stockCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$email) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        try {
            /** @var \Magento\ProductAlert\Model\ResourceModel\Stock\Collection $collection */
            $collection = $this->stockCollectionFactory->create()
                ->addFieldToFilter('email', $email);

            foreach ($collection as $item) {
                $item->delete();
            }

            $this->messageManager->addSuccessMessage(__('You will no longer receive stock alerts.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t update the alert subscription right now.'));
        }

        $resultRedirect->setUrl('/');
        return $resultRedirect;
    }
}
