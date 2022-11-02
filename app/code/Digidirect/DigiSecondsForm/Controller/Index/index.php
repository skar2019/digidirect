<?php

namespace Digidirect\DigiSecondsForm\Controller\Index;

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
        
        // Get post values
        $firstname = $this->getRequest()->getParam('firstname');
        $lastname = $this->getRequest()->getParam('lastname');
        $phone = $this->getRequest()->getParam('phone');
        $email = $this->getRequest()->getParam('email');
        $brands = $this->getRequest()->getParam('brands');
        $productName = $this->getRequest()->getParam('productName');
        $purchaseYear = $this->getRequest()->getParam('purchaseYear');
        $notes = $this->getRequest()->getParam('notes');
        $askingPrice = $this->getRequest()->getParam('askingPrice');

        // Send Mail functionality starts from here 
        $from = $email;
        $nameFrom = "From Name";
        $to = "dev4@digidirect.com.au";
        $nameTo = "To Name";
        $body = "
        <div>
            <li>".$firstname."</li>
            <li>".$lastname."</li>
            <li>".$phone."</li>
            <li>".$email."</li>
            <li>".$brands."</li>
            <li>".$productName."</li>
            <li>".$purchaseYear."</li>
            <li>".$notes."</li>
            <li>".$askingPrice."</li>
        </div>";

        $email = new \Zend_Mail();
        $email->setSubject("DigiSecondsform Test"); 
        $email->setBodyHtml($body);     // use it to send html data
        //$email->setBodyText($body);   // use it to send simple text data
        $email->setFrom($from, $nameFrom);
        $email->addTo($to, $nameTo);
        $email->send();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();       
        $data = $objectManager->create('Digidirect\DigiSecondsForm\Model\DigiSecondsForm');
        $data->setData($post);
        $data->save();
        echo "success";
        /* echo "hello";
        exit; */


       
        
        $this->messageManager->addSuccess(__('Form successfully submitted'));
             
    }
}
