<?php
namespace Digidirect\Pronto\Controller\Index;

use Digidirect\Pronto\Helper\ProntoOrder;

class UpdateOrders extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
                ProntoOrder $helper)
	{
                $this->helper = $helper;
		return parent::__construct($context);
	}

	public function execute()
	{
            
            $this->helper->GetProntoOrders();
	}
}
