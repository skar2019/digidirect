<?php
namespace Ewave\Security\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Action;

class AccessObserver implements ObserverInterface
{
    /**
     * @var \Ewave\Security\Model\SecurityFactory
     */
    protected $_securityFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * AccessObserver constructor.
     * @param \Ewave\Security\Model\SecurityFactory $securityFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Ewave\Security\Model\SecurityFactory $securityFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->_securityFactory = $securityFactory;
        $this->_storeManager = $storeManager;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Access to the admin panel by ip
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getEvent()->getRequest();

        $clientIp = $request->getClientIp();

        if ($this->_securityFactory->get('Ewave\Security\Model\Security')->checkRangeIp($clientIp)) {
            return $this;
        }

        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getEvent()->getControllerAction();
        $controller->getResponse()->setRedirect(
            $this->_storeManager->getStore()->getUrl('', ['_direct' => 'noroute', '_nosid' => true])
        );
        $this->_actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        return $this;
    }
}
