<?php
namespace Digidirect\RemoveCategory\Controller\Index;

//use Digidirect\Pronto\Helper\Inventory;
use Digidirect\RemoveCategory\Helper\Product;

class Remove extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
                Product $helper)
	{
                $this->helper = $helper;
		return parent::__construct($context);
	}

	public function execute()
	{
                if(isset($_GET["id"])){
                    $id = $_GET["id"];
                    $this->helper->removeCategory($id);
                    exit;
                }
	}
}