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

        // $product->getData();
        // $brandlist = $this->getBrand();
        
        $post = $this->getRequest()->getPostValue();

        // Get your post values
        $firstname = $this->getRequest()->getParam('firstname');
        $lastname = $this->getRequest()->getParam('lastname');
        $email = $this->getRequest()->getParam('email');
        $message = $this->getRequest()->getParam('message');
        // $attachedfiles = $this->getRequest()->getParam('attachedfiles');

        // attachment
        $file = $_FILES["attachedfiles"]["tmp_name"];
        $filename = $_FILES["attachedfiles"]["name"];
        $attachment = chunk_split(base64_encode(file_get_contents($file)));

        // Send Mail functionality starts from here 
        $from = $email;
        $nameFrom = $firstname." ".$lastname;
        $to = "jireh@kayweb.com.au";
        // $to = array("jireh@kayweb.com.au","digimarket@digidirect.com.au");
        $nameTo = "Digidirect";
        $uploadfile = $attachment;
        
        $body = "
        <div>
            <p><b>FullName:</b> ".$firstname." ".$lastname."</p>
            <p><b>Message:</b> ".$message."</p>
            
        </div>";

        $email = new \Zend_Mail();
        $email->setSubject("DigiMarketSeller Form"); 
        $email->setBodyHtml($body, $uploadfile);     // use it to send html data
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
