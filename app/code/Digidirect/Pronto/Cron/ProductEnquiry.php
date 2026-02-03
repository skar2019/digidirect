<?php
namespace Digidirect\Pronto\Cron;

use Digidirect\Pronto\Helper\Product;
use Psr\Log\LoggerInterface;

class ProductEnquiry
{
    protected $helper;
    protected $logger;
    protected $startTime;

    public function __construct(
        Product $helper,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    public function executeSync()
    {
        $this->startTime = time();
        $this->logger->info('========================================');
        $this->logger->info('Pronto Product Sync CRON Started');
        $this->logger->info('Start time: ' . date('Y-m-d H:i:s'));
        $this->logger->info('Memory at start: ' . $this->formatBytes(memory_get_usage(true)));
        $this->logger->info('========================================');

        try {
            $this->helper->productSync();

            $duration = time() - $this->startTime;
            $this->logger->info('========================================');
            $this->logger->info('Pronto Product Sync COMPLETED');
            $this->logger->info('Duration: ' . $duration . ' seconds');
            $this->logger->info('Memory at end: ' . $this->formatBytes(memory_get_usage(true)));
            $this->logger->info('========================================');

        } catch (\Exception $e) {
            $duration = time() - $this->startTime;
            $this->logger->error('========================================');
            $this->logger->error('Pronto Product Sync FAILED');
            $this->logger->error('Duration: ' . $duration . ' seconds');
            $this->logger->error('Error: ' . $e->getMessage());
            $this->logger->error('File: ' . $e->getFile() . ':' . $e->getLine());
            $this->logger->error('Trace: ' . $e->getTraceAsString());
            $this->logger->error('========================================');
            throw $e;
        }
    }

    private function formatBytes($bytes)
    {
        return round($bytes / 1024 / 1024, 2) . ' MB';
    }

    public function execute()
    {
        return;
    }

    //redeploy
}
