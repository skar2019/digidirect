<?php

namespace Digidirect\MarketplacerProducts\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute() {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set("Buy & Sell Pre-Owned Photography Gear at digiMarket now!");
        $resultPage->getConfig()->setDescription("Discover digiMarket for buying and selling pre-owned camera gear. Great deals on quality equipment. Explore digiDirect's marketplace today!");
        return $resultPage;
    }
}
