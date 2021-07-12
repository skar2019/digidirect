<?php
namespace Digidirect\Pronto\Controller\Index;

use Digidirect\Pronto\Helper\Shipping;

class TestShipping extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
                Shipping $helper)
	{
                $this->helper = $helper;
		return parent::__construct($context);
	}

	public function execute()
	{
		echo "Pronto Shipping Sync<br />";
                $this->helper->getShipping();
		exit;
	}
}