<?php
namespace Digidirect\Pronto\Cron;

use Digidirect\Pronto\Helper\ProductEntHelper;

class MwaveData
{


    /**
     * @var Inventory
     */
    protected $helper;

    public function __construct(
        ProductEntHelper $helper)
    {
        $this->helper = $helper;
    }
    public function execute()
    {
        $this->helper->mwaveData();
    }



}
