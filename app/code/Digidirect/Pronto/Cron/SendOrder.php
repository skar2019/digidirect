<?php
namespace Digidirect\Pronto\Cron;

use Psr\Log\LoggerInterface;
use Digidirect\Pronto\Helper\Order;

class SendOrder
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
            LoggerInterface $logger,
            Order $helper)
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function execute()
    {
        
        try {
            $this->helper->sendOrder(); //use date today as parameter
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}