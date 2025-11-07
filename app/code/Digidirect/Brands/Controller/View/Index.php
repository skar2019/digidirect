<?php
namespace Digidirect\Brands\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $brandUrl = $this->getRequest()->getParam('brand');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(ucwords(str_replace('-', ' ', $brandUrl)));
        return $resultPage;
    }
}
