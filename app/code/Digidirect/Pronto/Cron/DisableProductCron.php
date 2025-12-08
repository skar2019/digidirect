<?php
namespace Digidirect\Pronto\Cron;

use Digidirect\Pronto\Helper\DisableProduct;

class DisableProductCron
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DisableProduct
     */
    protected $helper;

    public function __construct(
        DisableProduct $helper)
    {

        $this->helper = $helper;
    }

    public function execute()
    {
        exit;
        $test = 0;
        $this->helper->toDisableProducts($test);//toDisableProducts($test);

    }

    public function toDisable()
    {
        $test = 0;
        $this->helper->toDisableProducts($test);//toDisableProducts($test);

    }

    public function toEnable()
    {
        $test = 0;
        $this->helper->toEnableProducts($test);

    }
}
