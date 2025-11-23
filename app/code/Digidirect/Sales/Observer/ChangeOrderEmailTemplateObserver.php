<?php
namespace Digidirect\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class ChangeOrderEmailTemplateObserver implements ObserverInterface
{
    /**
     * Force Click & Collect orders to use a different email template
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\DataObject $transport */
        $transport = $observer->getData('transportObject');

        if (!$transport) {
            return;
        }

        $order = $transport->getData('order');
        if (!$order instanceof Order) {
            return;
        }

        /*$shippingMethod = $order->getShippingMethod();
        if (strpos(strtolower($shippingMethod), 'clickandcollect') !== false) {
            // Force template ID
            $identityContainer = $observer->getData('sender')->getIdentityContainer();
            $identityContainer->setTemplateId(69);
        }*/
        $identityContainer = $observer->getData('sender')->getIdentityContainer();
        $identityContainer->setTemplateId(69);
    }
}
