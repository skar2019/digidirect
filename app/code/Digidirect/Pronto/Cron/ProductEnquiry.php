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
        $this->logger->info('Pronto Product Sync syncing set 0');
        $this->helper->productPronto(0);
        exit;
    }
    
    public function productSet1()
    {
        $this->logger->info('Pronto Product Sync syncing set 1');
        $this->helper->productPronto(106156);
        exit;
    }
    
    public function productSet2()
    {
        $this->logger->info('Pronto Product Sync syncing set2 ');
        $this->helper->productPronto(111623);
        exit;
    }
    
    public function productSet3()
    {
        $this->logger->info('Pronto Product Sync syncing set 3');
        $this->helper->productPronto(114760);
        exit;
    }
    
    public function productSet4()
    {
        $this->logger->info('Pronto Product Sync syncing set 4');
        $this->helper->productPronto(118807);
        exit;
    }
    
    public function productSet5()
    {
        $this->logger->info('Pronto Product Sync syncing set 5');
        $this->helper->productPronto(120409);
        exit;
    }
    
    public function productSet6()
    {
        $this->logger->info('Pronto Product Sync syncing set 6');
        $this->helper->productPronto(121979);
        exit;
    }
    
    public function productSet7()
    {
        $this->logger->info('Pronto Product Sync syncing set 7');
        $this->helper->productPronto(123432);
        exit;
    }
    
    public function productSet8()
    {
        $this->logger->info('Pronto Product Sync syncing set 8');
        $this->helper->productPronto(125174);
        exit;
    }
    
    public function productSet9()
    {
        $this->logger->info('Pronto Product Sync syncing set 9');
        $this->helper->productPronto(126304);
        exit;
    }
    
    public function productSet10()
    {
        $this->logger->info('Pronto Product Sync syncing set 10');
        $this->helper->productPronto(127636);
        exit;
    }
    
    public function productSet11()
    {
        $this->logger->info('Pronto Product Sync syncing set 11');
        $this->helper->productPronto(128787);
        exit;
    }
    
    public function productSet12()
    {
        $this->logger->info('Pronto Product Sync syncing set 12');
        $this->helper->productPronto(129611);
        exit;
    }
    
    public function productSet13()
    {
        $this->logger->info('Pronto Product Sync syncing set 13');
        $this->helper->productPronto(130469);
        exit;
    }
    
    public function productSet14()
    {
        $this->logger->info('Pronto Product Sync syncing set 14');
        $this->helper->productPronto(131168);
        exit;
    }
    
    public function productSet15()
    {
        $this->logger->info('Pronto Product Sync syncing set 15');
        $this->helper->productPronto(131928);
        exit;
    }
    
    public function productSet16()
    {
        $this->logger->info('Pronto Product Sync syncing set 16');
        $this->helper->productPronto(132662);
        exit;
    }
    
    public function productSet17()
    {
        $this->logger->info('Pronto Product Sync syncing set 17');
        $this->helper->productPronto(133412);
        exit;
    }
    
    public function productSet18()
    {
        $this->logger->info('Pronto Product Sync syncing set 18');
        $this->helper->productPronto(134410);
        exit;
    }
    
    public function productSet19()
    {
        $this->logger->info('Pronto Product Sync syncing set 19');
        $this->helper->productPronto(135035);
        exit;
    }
    
    public function productSet20()
    {
        $this->logger->info('Pronto Product Sync syncing set 20');
        $this->helper->productPronto(135924);
        exit;
    }
    
    public function productSet21()
    {
        $this->logger->info('Pronto Product Sync syncing set 21');
        $this->helper->productPronto(136697);
        exit;
    }
    
    public function productSet22()
    {
        $this->logger->info('Pronto Product Sync syncing set 22');
        $this->helper->productPronto(137346);
        exit;
    }
    
    public function productSet23()
    {
        $this->logger->info('Pronto Product Sync syncing set 23');
        $this->helper->productPronto(137951);
        exit;
    }
    
    public function productSet24()
    {
        $this->logger->info('Pronto Product Sync syncing set 24');
        $this->helper->productPronto(138630);
        exit;
    }

}