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
        $Cameras = $this->getRequest()->getParam('categories_option2');
        $ComputersMobile = $this->getRequest()->getParam('categories_option3');
        $Drones = $this->getRequest()->getParam('categories_option4');
        $FitnessWellbeing = $this->getRequest()->getParam('categories_option5');
        $HomeOffice = $this->getRequest()->getParam('categories_option6');
        $InCar = $this->getRequest()->getParam('categories_option7');
        $Instrument = $this->getRequest()->getParam('categories_option8');
        $Lenses = $this->getRequest()->getParam('categories_option9');
        $LightingStudio = $this->getRequest()->getParam('categories_option10');
        $Optics = $this->getRequest()->getParam('categories_option11');
        $PetTech = $this->getRequest()->getParam('categories_option12');
        $PhotoAccessories = $this->getRequest()->getParam('categories_option13');
        $ProVideo = $this->getRequest()->getParam('categories_option14');
        $ScootersBikeBoards = $this->getRequest()->getParam('categories_option15');
        $Seasonal = $this->getRequest()->getParam('categories_option16');
        $SmartHome = $this->getRequest()->getParam('categories_option17');
        $Toys = $this->getRequest()->getParam('categories_option18');
        $SquareWearable = $this->getRequest()->getParam('categories_option19');
        $SquareWhiteGoods = $this->getRequest()->getParam('categories_option20');
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
            ".$audiovisual." 
            ".$Cameras." 
            ".$ComputersMobile." 
            ".$Drones." 
            ".$FitnessWellbeing." 
            ".$HomeOffice." 
            ".$InCar." 
            ".$Instrument." 
            ".$Lenses." 
            ".$LightingStudio." 
            ".$Optics." 
            ".$PetTech." 
            ".$PhotoAccessories." 
            ".$ProVideo." 
            ".$ScootersBikeBoards." 
            ".$Seasonal." 
            ".$SmartHome." 
            ".$Toys." 
            ".$SquareWearable." 
            ".$SquareWhiteGoods." 
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
