<?php
namespace Digidirect\Pronto\Controller\Index;

//use Digidirect\Pronto\Helper\Inventory;
use Digidirect\Pronto\Helper\Product;

class ProductEnquiry extends \Magento\Framework\App\Action\Action
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
		echo "Pronto Full <br />";
                $this->helper->fullEnquiry();
		exit;
	}
}