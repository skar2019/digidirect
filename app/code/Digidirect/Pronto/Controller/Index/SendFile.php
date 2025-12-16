<?php
namespace Digidirect\Pronto\Controller\Index;

use Digidirect\Pronto\Helper\Sftpwisersender;

class SendFile extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Sftpwisersender $helper)
    {
        $this->helper = $helper;
        return parent::__construct($context);
    }

    public function execute()
    {

        $this->helper->sendFile();
    }
}
