<?php
namespace Ewave\AISales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Grid;

class GridRefresh implements ObserverInterface
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @param Grid $grid
     */
    public function __construct(
        Grid $grid
    ) {
        $this->grid = $grid;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $this->grid->refreshBySchedule();
    }
}
