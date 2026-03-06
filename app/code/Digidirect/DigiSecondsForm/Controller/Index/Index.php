<?php

namespace Digidirect\DigiSecondsForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Mail\Template\TransportBuilder;
use Digidirect\DigiSecondsForm\Model\DigiSecondsFormFactory;

class Index extends Action
{
    /** @var TransportBuilder */
    protected $transportBuilder;

    /** @var DigiSecondsFormFactory */
    private $digiSecondsFormFactory;

    /* @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param DigiSecondsFormFactory $digiSecondsFormFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        TransportBuilder $transportBuilder,
        DigiSecondsFormFactory $digiSecondsFormFactory
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->digiSecondsFormFactory  = $digiSecondsFormFactory;
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
        $nameFrom = $firstname." ".$lastname;
        $to = array("geoff.n@digidirect.com.au","paul@digidirect.com.au","dev4@digidirect.com.au");
        $bcc = "orders@kayweb.com.au";
        $nameTo = "Digidirect";
        $body = "
        <div>
            <p>FullName: ".$firstname." ".$lastname."</p>
            <p>Phone: ".$phone."</p>
            <p>Email: ".$email."</p>
            <p>Brands: ".$brands."</p>
            <p>Product Name: ".$productName."</p>
            <p>Purchase Year: ".$purchaseYear."</p>
            <p>Notes: ".$notes."</p>
            <p>Asking Price: ".$askingPrice."</p>
        </div>";

        $email = new \Zend_Mail();
        $email->setSubject("DigiSeconds Form"); 
        $email->setBodyHtml($body);     // use it to send html data
        //$email->setBodyText($body);   // use it to send simple text data
        $email->setFrom($from, $nameFrom);
        $email->addTo($to, $nameTo);
        $email->addBcc($bcc);
        $email->send();


        $saveData = [
            'name' => trim((string)$firstname . ' ' . (string)$lastname),
            'email' => (string)$email,
            'telephone' => (string)$phone,
            'comment' => (string)$notes
        ];

        $data = $this->digiSecondsFormFactory->create();
        $data->setData($saveData);
        $data->save();

//        echo "success";
        /* echo "hello";
        exit; */
        
        $this->messageManager->addSuccess(__('Form successfully submitted'));
             
    }
}
