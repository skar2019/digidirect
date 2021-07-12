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
            $orderId = 0;
            $date = 0;
            if(isset($_GET["p"])){
                $orderId = $_GET["p"];
            }
            if(isset($_GET["date"])){
                $date = $_GET["date"];
            }
            echo "Pronto Send Order - ".$orderId." - ".$date."<br />";
            $this->helper->orderPostTec($orderId, $date);
            exit;
	}
}