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
     * @var Product
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

    }

    public function productSet0()
    {
        $this->logger->info('Pronto Product Sync syncing set 0');
        $this->helper->productPronto(100425,102868);

    }

    public function productSet1()
    {
        $this->logger->info('Pronto Product Sync syncing set 1');
        $this->helper->productPronto(102868,106281);

    }

    public function productSet2()
    {
        $this->logger->info('Pronto Product Sync syncing set2 ');
        $this->helper->productPronto(106281,112036);

    }

    public function productSet3()
    {
        $this->logger->info('Pronto Product Sync syncing set 3');
        $this->helper->productPronto(112036,116452);

    }

    public function productSet4()
    {
        $this->logger->info('Pronto Product Sync syncing set 4');
        $this->helper->productPronto(116452,119145);

    }

    public function productSet5()
    {
        $this->logger->info('Pronto Product Sync syncing set 5');
        $this->helper->productPronto(119145,120567);

    }

    public function productSet6()
    {
        $this->logger->info('Pronto Product Sync syncing set 6');
        $this->helper->productPronto(120567,122246);

    }

    public function productSet7()
    {
        $this->logger->info('Pronto Product Sync syncing set 7');
        $this->helper->productPronto(122246,123956);

    }

    public function productSet8()
    {
        $this->logger->info('Pronto Product Sync syncing set 8');
        $this->helper->productPronto(123956,125708);

    }

    public function productSet9()
    {
        $this->logger->info('Pronto Product Sync syncing set 9');
        $this->helper->productPronto(125708,127061);

    }

    public function productSet10()
    {
        $this->logger->info('Pronto Product Sync syncing set 10');
        $this->helper->productPronto(127061,128259);

    }

    public function productSet11()
    {
        $this->logger->info('Pronto Product Sync syncing set 11');
        $this->helper->productPronto(128259,129337);

    }

    public function productSet12()
    {
        $this->logger->info('Pronto Product Sync syncing set 12');
        $this->helper->productPronto(129337,130181);

    }

    public function productSet13()
    {
        $this->logger->info('Pronto Product Sync syncing set 13');
        $this->helper->productPronto(130181,131007);

    }

    public function productSet14()
    {
        $this->logger->info('Pronto Product Sync syncing set 14');
        $this->helper->productPronto(131007,131803);

    }

    public function productSet15()
    {
        $this->logger->info('Pronto Product Sync syncing set 15');
        $this->helper->productPronto(131803,132576);

    }

    public function productSet16()
    {
        $this->logger->info('Pronto Product Sync syncing set 16');
        $this->helper->productPronto(132576,133381);

    }

    public function productSet17()
    {
        $this->logger->info('Pronto Product Sync syncing set 17');
        $this->helper->productPronto(133381,134414);

    }

    public function productSet18()
    {
        $this->logger->info('Pronto Product Sync syncing set 18');
        $this->helper->productPronto(134414,135059);

    }

    public function productSet19()
    {
        $this->logger->info('Pronto Product Sync syncing set 19');
        $this->helper->productPronto(135059,136090);

    }

    public function productSet20()
    {
        $this->logger->info('Pronto Product Sync syncing set 20');
        $this->helper->productPronto(136090,136819);

    }

    public function productSet21()
    {
        $this->logger->info('Pronto Product Sync syncing set 21');
        $this->helper->productPronto(136819,137464);

    }

    public function productSet22()
    {
        $this->logger->info('Pronto Product Sync syncing set 22');
        $this->helper->productPronto(137464,138136);

    }

    public function productSet23()
    {
        $this->logger->info('Pronto Product Sync syncing set 23');
        $this->helper->productPronto(138136,138957);

    }

    public function productSet24()
    {
        $this->logger->info('Pronto Product Sync syncing set 24');
        $this->helper->productPronto(138957,139682);

    }

    public function productSet25()
    {
        $this->logger->info('Pronto Product Sync syncing set 25');
        $this->helper->productPronto(139682,140356);

    }

    public function productSet26()
    {
        $this->logger->info('Pronto Product Sync syncing set 26');
        $this->helper->productPronto(140356,141500);

    }

    public function productSet27()
    {
        $this->logger->info('Pronto Product Sync syncing set 27');
        $this->helper->productPronto(140554);

    }


}
