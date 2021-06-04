<?php
namespace Ewave\OutOfStockNotif\Controller\Adminhtml\Index;

use Ewave\OutOfStockNotif\Model\ResourceModel\Stock\Grid\CollectionFactory;
use Magento\ProductAlert\Model\StockFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;

class MassDelete extends Action
{
    /**
     * @var \Magento\ProductAlert\Model\StockFactory
     */
    protected $stockFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassActions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param StockFactory $stockFactory
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        StockFactory $stockFactory,
        CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        $this->stockFactory = $stockFactory;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
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
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $ids = $collection->getColumnValues('alert_stock_id');
        }

        $counter = 0;
        foreach ($ids as $id) {
            /** @var \Magento\ProductAlert\Model\Stock $stockModel */
            $stockModel = $this->stockFactory->create();
            $stockModel->getResource()->load($stockModel, $id);
            if ($stockModel->getId()) {
                $stockModel->getResource()->delete($stockModel);
                $counter++;
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $counter)
        );

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
        return $this->_authorization->isAllowed('Ewave_OutOfStockNotif::out_of_stock_delete');
    }
}
