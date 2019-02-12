<?php
namespace Ewave\QuickView\Observer;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class AddQuickViewResponse
 * @package Ewave\QuickView\Observer
 */
class AddQuickViewResponse implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Ewave\QuickView\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ewave\QuickView\Helper\ResponseHandler
     */
    protected $_responseHandler;

    /**
     * AddQuickViewResponse constructor.
     * @param \Ewave\QuickView\Helper\Data $helper
     * @param \Ewave\QuickView\Helper\ResponseHandler $_responseHandler
     */
    public function __construct(
        \Ewave\QuickView\Helper\Data $helper,
        \Ewave\QuickView\Helper\ResponseHandler $_responseHandler
    ) {
        $this->_helper = $helper;
        $this->_responseHandler = $_responseHandler;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controllerAction */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($this->_helper->isModuleEnabled() && $controllerAction->getRequest()->getParam('quickview')) {
            $response = $controllerAction->getResponse();
            $this->_responseHandler->sendResponse($response);

            /** @todo eliminate usage of exit statement */
            exit;
        }
    }
}
