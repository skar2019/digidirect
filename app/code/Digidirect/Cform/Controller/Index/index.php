<?php

namespace Digidirect\Cform\Controller\Index;

use Magento\Framework\App\Action\Action;


class Index extends Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }   
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();       
        $data = $objectManager->create('Digidirect\Cform\Model\Cform');
        $data->setData($post);
        $data->save();
        /* echo "hello";
        exit; */

        print_r($post);
        print_r($data);
        
        $this->messageManager->addSuccess(__('Form successfully submitted'));
             
    }
}
