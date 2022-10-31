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
        
        // Get your post values
        $firstname = "Jireh";
        $lastname = "Sy";
        
        $fname = $this->getRequest()->getParam('name');
        $email = $this->getRequest()->getParam('email');

        // Send Mail functionality starts from here 
        $from = "from_email_address@example.com";
        $nameFrom = "From Name";
        $to = "dev4@digidirect.com.au";
        $nameTo = "To Name";
        $body = "
        <div>
        <b>".$fname."</b>
        <i>".$email."</i>
        </div>";

        $email = new \Zend_Mail();
        $email->setSubject("Cform Test"); 
        $email->setBodyHtml($body);     // use it to send html data
        //$email->setBodyText($body);   // use it to send simple text data
        $email->setFrom($from, $nameFrom);
        $email->addTo($to, $nameTo);
        $email->send();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();       
        $data = $objectManager->create('Digidirect\Cform\Model\Cform');
        $data->setData($post);
        $data->save();
        echo "success";
        /* echo "hello";
        exit; */


       
        
        $this->messageManager->addSuccess(__('Form successfully submitted'));
             
    }
}
