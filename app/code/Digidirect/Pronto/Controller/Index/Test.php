<?php
namespace Digidirect\Pronto\Controller\Index;

use Digidirect\Pronto\Helper\DisableProduct;

class Test extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        DisableProduct $helper)
	{
                $this->helper = $helper;
		return parent::__construct($context);
	}

	public function execute()
	{
            $test = 0;
            $disable = 0;
            
            if(isset($_GET["test"])){
                $test = $_GET["test"];
            }
            if(isset($_GET["disable"])){
                $test = $_GET["disable"];
            }
            
            if($disable)
            {
                $this->helper->toDisableProducts($test);
            }
            else 
            {
                $this->helper->toEnableProducts($test);
            }
            
    }
}
