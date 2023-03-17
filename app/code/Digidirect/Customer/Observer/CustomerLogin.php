<?php

namespace Vendor\Module\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        echo "Customer LoggedIn";
        $customer = $observer->getEvent()->getCustomer();
        echo $customer->getName(); //Get customer name
        exit;
    }
}