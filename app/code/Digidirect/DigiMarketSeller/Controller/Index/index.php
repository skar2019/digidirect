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


        // $product->getData();
        // $brandlist = $this->getBrand();
        
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

        // page2
        $salutename = $this->getRequest()->getParam('salutename');
        $firstname = $this->getRequest()->getParam('firstname');
        $lastname = $this->getRequest()->getParam('lastname');
        $email = $this->getRequest()->getParam('email');
        $job = $this->getRequest()->getParam('job');
        $business = $this->getRequest()->getParam('business');
        $address = $this->getRequest()->getParam('address');
        $abn = $this->getRequest()->getParam('abn');
        $employees = $this->getRequest()->getParam('employees');
        $contact = $this->getRequest()->getParam('contact');
        $website = $this->getRequest()->getParam('website');

        // page3
        $brands = $this->getRequest()->getParam('brands');
        $current_sell = $this->getRequest()->getParam('current_sell');
        $method = $this->getRequest()->getParam('method');
        $platform_use = $this->getRequest()->getParam('platform_use');
        $head_office = $this->getRequest()->getParam('head_office');
        $warehouse = $this->getRequest()->getParam('warehouse');
        $annual_sale = $this->getRequest()->getParam('annual_sale');
        $intending = $this->getRequest()->getParam('intending');



  

        // Send Mail functionality starts from here 
        $from = $email;
        $nameFrom = $firstname." ".$lastname;
        $to = "jireh@kayweb.com.au";
        $nameTo = "Digidirect";
        $body = "
        <div>
            <p><b>Categories:</b> 
            ".$audiovisual." 
            ".$cameras." 
            ".$computersMobile." 
            ".$drones." 
            ".$fitnessWellbeing." 
            ".$homeOffice." 
            ".$inCar." 
            ".$instrument." 
            ".$lenses." 
            ".$lightingStudio." 
            ".$optics." 
            ".$petTech." 
            ".$photoAccessories." 
            ".$proVideo." 
            ".$scootersBikeBoards." 
            ".$seasonal." 
            ".$smartHome." 
            ".$toys." 
            ".$squareWearable." 
            ".$squareWhiteGoods." 
            ".$cat_otherfield."
            </p>

            <p><b>Title:</b> ".$salutename."</p>
            <p><b>FullName:</b> ".$firstname." ".$lastname."</p>
            <p><b>Email:</b> ".$email."</p>
            <p><b>Job:</b> ".$job."</p>
            <p><b>Business:</b> ".$business."</p>
            <p><b>Address:</b> ".$address."</p>
            <p><b>ABN:</b> ".$abn."</p>
            <p><b>Employees No.:</b> ".$employees."</p>
            <p><b>Contact:</b> ".$contact."</p>
            <p><b>Website:</b> ".$website."</p>

            <p><b>Brands:</b> ".$brands."</p>
            <p><b>Currently sell on other marketplaces (including abroad):</b> ".$current_sell."</p>
            <p><b>Prefered method of integration with digiDirect:</b> ".$method."</p>
            <p><b>POS and eCommerce platforms:</b> ".$platform_use."</p> 
            <p><b>Head Office Location:</b> ".$head_office."</p> 
            <p><b>Warehouse Location Shipping Product from:</b> ".$warehouse."</p>
            <p><b>Estimate Annual Sale (AUD):</b> ".$annual_sale."</p>
            <p><b>Intending Product to list an digiDirect:</b> ".$intending."</p>

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
