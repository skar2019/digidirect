<?php
namespace Digidirect\ReadytoPickup\Cron;

use Psr\Log\LoggerInterface;
use Digidirect\ReadytoPickup\Helper\ReadytoPickup;

class SendReadytoPickupEmail
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
            Order $helper)
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function execute()
    {
        $this->helper->sendReadytoPickupEmail();

    }
    
}