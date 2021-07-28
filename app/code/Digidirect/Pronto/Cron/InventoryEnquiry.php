<?php
namespace Digidirect\Pronto\Cron;

use Psr\Log\LoggerInterface;
use Digidirect\Pronto\Helper\Inventory;

class InventoryEnquiry
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var Inventory
     */
    protected $helper;
    
    public function __construct(
            LoggerInterface $logger,
            Inventory $helper)
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function execute()
    {
        $this->helper->enquireInventory();

    }
}