<?php
namespace Digidirect\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;

class SetOverlayFlag implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Session $customerSession
     */
    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->customerSession->setShowLoginOverlay(true);
    }
}
