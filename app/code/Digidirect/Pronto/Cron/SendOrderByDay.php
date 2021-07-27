<?php
namespace Digidirect\Pronto\Cron;

use Psr\Log\LoggerInterface;
use Digidirect\Pronto\Helper\Order;

class SendOrderByDay
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
        $this->helper->orderPostByDay();

    }

}
