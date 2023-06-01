<?php
namespace Digidirect\Pronto\Cron;

use Digidirect\Pronto\Helper\Product;

class ProductEnquiry
{

    /**
     * @var Product
     */
    protected $helper;

    public function __construct(
            Product $helper)
    {
        $this->helper = $helper;
    }

    public function executeSync()
    {
        $this->helper->productSync();
    }
    public function execute()
    {
        return;
        $this->helper->productSync();
    }



    public function productSetSync()
    {
        $this->helper->productSynAgain();

    }
    public function productSet0()
    {
        $this->helper->productPronto(100425,102868);

    }

    public function productSet1()
    {
        $this->helper->productPronto(102868,106281);

    }

    public function productSet2()
    {
        $this->helper->productPronto(106281,112036);

    }

    public function productSet3()
    {
        $this->helper->productPronto(112036,116452);

    }

    public function productSet4()
    {
        $this->helper->productPronto(116452,119145);

    }

    public function productSet5()
    {
        $this->helper->productPronto(119145,120567);

    }

    public function productSet6()
    {
        $this->helper->productPronto(120567,122246);

    }

    public function productSet7()
    {
        $this->helper->productPronto(122246,123956);

    }

    public function productSet8()
    {
        $this->helper->productPronto(123956,125708);

    }

    public function productSet9()
    {
        $this->helper->productPronto(125708,127061);

    }

    public function productSet10()
    {
        $this->helper->productPronto(127061,128259);

    }

    public function productSet11()
    {
        $this->helper->productPronto(128259,129337);

    }

    public function productSet12()
    {
        $this->helper->productPronto(129337,130181);

    }

    public function productSet13()
    {
        $this->helper->productPronto(130181,131007);

    }

    public function productSet14()
    {
        $this->helper->productPronto(131007,131803);

    }

    public function productSet15()
    {
        $this->helper->productPronto(131803,132576);

    }

    public function productSet16()
    {
        $this->helper->productPronto(132576,133381);

    }

    public function productSet17()
    {
        $this->helper->productPronto(133381,134414);

    }

    public function productSet18()
    {
        $this->helper->productPronto(134414,135059);

    }

    public function productSet19()
    {
        $this->helper->productPronto(135059,136090);

    }

    public function productSet20()
    {
        $this->helper->productPronto(136090,136819);

    }

    public function productSet21()
    {

        $this->helper->productPronto(136819,137464);

    }

    public function productSet22()
    {
        $this->helper->productPronto(137464,138136);

    }

    public function productSet23()
    {

        $this->helper->productPronto(138136,138957);

    }

    public function productSet24()
    {

        $this->helper->productPronto(138957,139682);

    }

    public function productSet25()
    {

        $this->helper->productPronto(139682,140356);

    }

    public function productSet26()
    {
        $this->helper->productPronto(140356,141500);

    }

    public function productSet27()
    {

        $this->helper->productPronto(140554);

    }
    //comment for redeploy

}
