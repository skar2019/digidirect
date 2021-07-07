<?php
namespace Digidirect\Pronto\Cron;

use Psr\Log\LoggerInterface;
use Digidirect\Pronto\Helper\Product;

class ProductEnquiry
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
            Product $helper)
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function productSet()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set'));
        $this->helper->productPronto();
        exit;
    }
    
    public function productSet1()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 1'));
        $this->helper->productEnquiryOne();
        exit;
    }
    
    public function productSet2()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set2 '));
        $this->helper->productEnquiryTwo();
        exit;
    }
    
    public function productSet3()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 3'));
        $this->helper->productEnquiryThree();
        exit;
    }
    
    public function productSet4()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 4'));
        $this->helper->productEnquiryFour();
        exit;
    }
    
    public function productSet5()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 5'));
        $this->helper->productEnquiryFive();
        exit;
    }
    
    public function productSet6()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 6'));
        $this->helper->productEnquirySix();
        exit;
    }
    
    public function productSet7()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 7'));
        $this->helper->productEnquirySeven();
        exit;
    }
}