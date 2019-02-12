<?php

namespace Ewave\Feed\Controller\Adminhtml\Report;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class View extends Action
{
    /**
     * {@inheritdoc}
     * @param \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage
     * @return \Magento\Backend\Model\View\Result\Page\Interceptor
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Ewave_Utilities::ewave');
        $resultPage->getConfig()->getTitle()->prepend(__('Shopping Feeds'));
        $resultPage->getConfig()->getTitle()->prepend(__('Reports'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage);

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_Feed::feed_report');
    }
}
