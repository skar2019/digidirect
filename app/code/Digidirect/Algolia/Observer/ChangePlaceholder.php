<?php

namespace Digidirect\Algolia\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ChangePlaceholder implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $transport = $observer->getEvent()->getData('configuration');
        $config = $transport->getData();

        // Change the placeholder
        $config['translations']['placeholder'] = __('Search our Endless Aisle');

        $transport->setData($config);
    }
}
