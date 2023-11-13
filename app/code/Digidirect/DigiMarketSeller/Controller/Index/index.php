<?php

namespace Digidirect\DigiMarketSeller\Controller\Index;

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
        $audiovisual = $this->getRequest()->getParam('categories_option1');
        $cat_otherfield = $this->getRequest()->getParam('cat_otherfield');

        // Send Mail functionality starts from here 
        $from = "from_email_address@example.com";
        $nameFrom = "From Name";
        $to = "jireh@kayweb.com.au";
        $nameTo = "To Name";
        $body = "
        <div>
            <p>Categories: ".$audiovisual."</p>
            <p>Others: ".$cat_otherfield."</p>
        </div>";

        $email = new \Zend_Mail();
        $email->setSubject("DigiMarketSeller Test"); 
        $email->setBodyHtml($body);     // use it to send html data
        //$email->setBodyText($body);   // use it to send simple text data
        $email->setFrom($from, $nameFrom);
        $email->addTo($to, $nameTo);
        $email->send();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();       
        $data = $objectManager->create('Digidirect\DigiMarketSeller\Model\DigiMarketSeller');
        $data->setData($post);
        $data->save();
        echo "success";
        /* echo "hello";
        exit; */
       
        
        $this->messageManager->addSuccess(__('Form successfully submitted'));
             
    }
}
