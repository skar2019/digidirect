<?php

namespace Digidirect\DigiClubMember\Observer;
   
use Magento\Framework\View\Page\Config;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;


class CheckLogin implements ObserverInterface
{
    protected Config $config;

    public function __construct(
        Config $config
    ){
        $this->config = $config;
    }

    public function execute(EventObserver $observer){
        $name = $observer->getFullActionName();
        if($name == "customer_account_login") {
            $this->config->addBodyClass("digiclub-redirect");
        }
    }
}