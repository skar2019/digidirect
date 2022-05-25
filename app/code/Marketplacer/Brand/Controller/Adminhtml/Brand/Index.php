<?php

namespace Marketplacer\Brand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Layout;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Marketplacer_Brand::brand';

    /**
     * @return ResponseInterface|ResultInterface|Layout
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Brands'));
        return $resultPage;
    }
}
