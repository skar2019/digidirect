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

    public function execute()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set'));
        $this->helper->productPronto();
        exit;
    }
    
    public function productSet1()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 1'));
        $this->helper->productProntoOne();
        exit;
    }
    
    public function productSet2()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set2 '));
        $this->helper->productProntoTwo();
        exit;
    }
    
    public function productSet3()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 3'));
        $this->helper->productProntoThree();
        exit;
    }
    
    public function productSet4()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 4'));
        $this->helper->productProntoFour();
        exit;
    }
    
    public function productSet5()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 5'));
        $this->helper->productProntoFive();
        exit;
    }
    
    public function productSet6()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 6'));
        $this->helper->productProntoSix();
        exit;
    }
    
    public function productSet7()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 7'));
        $this->helper->productProntoSeven();
        exit;
    }
    
    public function productSet8()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 8'));
        $this->helper->productProntoEight();
        exit;
    }
    
    public function productSet9()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 9'));
        $this->helper->productProntoNine();
        exit;
    }
    
    public function productSet10()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 10'));
        $this->helper->productProntoTen();
        exit;
    }
    
    public function productSet11()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 11'));
        $this->helper->productProntoEleven();
        exit;
    }
    
    public function productSet12()
    {
        $this->logger->info('Pronto Product Sync', array('info' => 'syncing set 12'));
        $this->helper->productProntoTwelve();
        exit;
    }
}