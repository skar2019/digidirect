<?php
namespace Digidirect\Pronto\Cron;

use Psr\Log\LoggerInterface;
use Digidirect\CustomOptions\Helper\CronCustomOption;

class CreateOptions
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
        
        try {
            $this->helper->saveCustomOption(); //use date today as parameter
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}