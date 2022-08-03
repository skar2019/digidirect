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
        $this->helper->toDisableProducts();

    }
    
    public function toEnable()
    {
        $test = 0;
        $this->helper->toEnableProducts($test);

    }
}
