<?php
namespace Digidirect\OutOfStockNotif\Plugin\ProductAlert\Model;

use Digidirect\OutOfStockNotif\Helper\Data as Helper;

class Observer
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Digidirect\OutOfStockNotif\Model\Observer
     */
    protected $observer;

    /**
     * Observer constructor.
     * @param Helper $helper
     * @param \Digidirect\OutOfStockNotif\Model\Observer $observer
     */
    public function __construct(
        Helper $helper,
        \Digidirect\OutOfStockNotif\Model\Observer $observer
    ) {
        $this->helper = $helper;
        $this->observer = $observer;
    }

    /**
     * Rug observer for guests
     * @return void
     */
    public function beforeProcess()
    {
        if ($this->helper->isEnabledForGuest()) {
            $this->observer->processGuestStock();
        }
    }
}
