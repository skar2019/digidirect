<?php
namespace Digidirect\Pronto\Controller\Index;

use Digidirect\Pronto\Helper\Inventory;

class Test extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
                Inventory $helper)
	{
                $this->helper = $helper;
		return parent::__construct($context);
	}

	public function execute()
	{
		echo "Hello Pronto <br />";
                $this->helper->enquireInventoryTest();
		exit;
	}
}