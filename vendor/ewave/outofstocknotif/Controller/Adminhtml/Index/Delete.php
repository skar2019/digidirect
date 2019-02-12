<?php
namespace Ewave\OutOfStockNotif\Controller\Adminhtml\Index;

use Magento\ProductAlert\Model\StockFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Delete extends Action
{
    /**
     * @var \Magento\ProductAlert\Model\StockFactory
     */
    protected $stockFactory;

    /**
     * Delete constructor.
     * @param Context $context
     * @param \Magento\ProductAlert\Model\StockFactory $stockFactory
     */
    public function __construct(
        Context $context,
        StockFactory $stockFactory
    ) {
        $this->stockFactory = $stockFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        try {
            /** @var \Magento\ProductAlert\Model\Stock $stockModel */
            $stockModel = $this->stockFactory->create();
            $stockModel->getResource()->load($stockModel, $id);
            if ($stockModel->getId()) {
                $stockModel->getResource()->delete($stockModel);
                $this->messageManager->addSuccessMessage(__('Stock alert has been deleted.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if index action is allowed
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_OutOfStockNotif::out_of_stock_delete');
    }
}
