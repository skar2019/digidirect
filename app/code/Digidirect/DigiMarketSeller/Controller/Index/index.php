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

        // page1
        $audiovisual = $this->getRequest()->getParam('categories_option1');
        $cameras = $this->getRequest()->getParam('categories_option2');
        $computersMobile = $this->getRequest()->getParam('categories_option3');
        $drones = $this->getRequest()->getParam('categories_option4');
        $fitnessWellbeing = $this->getRequest()->getParam('categories_option5');
        $homeOffice = $this->getRequest()->getParam('categories_option6');
        $inCar = $this->getRequest()->getParam('categories_option7');
        $instrument = $this->getRequest()->getParam('categories_option8');
        $lenses = $this->getRequest()->getParam('categories_option9');
        $lightingStudio = $this->getRequest()->getParam('categories_option10');
        $optics = $this->getRequest()->getParam('categories_option11');
        $petTech = $this->getRequest()->getParam('categories_option12');
        $photoAccessories = $this->getRequest()->getParam('categories_option13');
        $proVideo = $this->getRequest()->getParam('categories_option14');
        $scootersBikeBoards = $this->getRequest()->getParam('categories_option15');
        $seasonal = $this->getRequest()->getParam('categories_option16');
        $smartHome = $this->getRequest()->getParam('categories_option17');
        $toys = $this->getRequest()->getParam('categories_option18');
        $squareWearable = $this->getRequest()->getParam('categories_option19');
        $squareWhiteGoods = $this->getRequest()->getParam('categories_option20');
        $cat_otherfield = $this->getRequest()->getParam('cat_otherfield');

        $email = "test@gmail.com";
        $firstname = "Jireh";
        $lastname = "Capao";

        // page2
        $salutename = $this->getRequest()->getParam('salutename');
  

        // Send Mail functionality starts from here 
        $from = $email;
        $nameFrom = $firstname." ".$lastname;
        $to = "jireh@kayweb.com.au";
        $nameTo = "Digidirect";
        $body = "
        <div>
            <p>Categories: 
            ".$audiovisual." ".$cameras." 
            </p>
            <p>Others: ".$cat_otherfield."</p>
            <p>Title: ".$salutename."</p>
        </div>";

        $email = new \Zend_Mail();
        $email->setSubject("DigiMarketSeller Form"); 
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
