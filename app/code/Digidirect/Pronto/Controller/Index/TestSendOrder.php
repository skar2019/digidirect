<?php
namespace Digidirect\Pronto\Controller\Index;

use Digidirect\Pronto\Helper\TestPronto;

class TestSendOrder extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
                TestPronto $helper)
	{
                $this->helper = $helper;
		return parent::__construct($context);
	}

	public function execute()
	{
            //echo "Pronto Send Order <br />";
            $this->helper->orderPostTec();
            exit;
	}
}