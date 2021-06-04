<?php
namespace Ewave\OutOfStockNotif\Plugin\ProductAlert\Model;

use Ewave\OutOfStockNotif\Helper\Data as Helper;

class Observer
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Ewave\OutOfStockNotif\Model\Observer
     */
    protected $observer;

    /**
     * Observer constructor.
     * @param Helper $helper
     * @param \Ewave\OutOfStockNotif\Model\Observer $observer
     */
    public function __construct(
        Helper $helper,
        \Ewave\OutOfStockNotif\Model\Observer $observer
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
