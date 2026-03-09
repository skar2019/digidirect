<?php

namespace Digidirect\CollaborateForm\Controller\Index;

use Digidirect\CollaborateForm\Model\CformFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Mail\Template\TransportBuilder;


class Index extends Action
{
    /**
     * @var CformFactory
     */
    private $cformFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        TransportBuilder $transportBuilder,
        CformFactory $cformFactory
        
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->cformFactory = $cformFactory;
    }   
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        
        // Get post values
        $firstname = $this->getRequest()->getParam('firstname');
        $lastname = $this->getRequest()->getParam('lastname');
        $email = $this->getRequest()->getParam('email');
        $notes = $this->getRequest()->getParam('notes');
        $socialmedia_link = $this->getRequest()->getParam('socialmedia_link');

        // Send Mail functionality starts from here 
        $from = $email;
        $nameFrom = $firstname." ".$lastname;
        $to = array("jireh.c@digidirect.com.au","community@digidirect.com.au");
        // $bcc = "orders@kayweb.com.au";
        $nameTo = "Digidirect";
        $body = "
        <div>
            <p>FullName: ".$firstname." ".$lastname."</p>
            <p>Email: ".$email."</p>
            <p>Notes: ".$notes."</p>
            <p>Social Media Link: ".$socialmedia_link."</p>
        </div>";

        $email = new \Zend_Mail();
        $email->setSubject("Collaborate Program Form"); 
        $email->setBodyHtml($body);     // use it to send html data
        //$email->setBodyText($body);   // use it to send simple text data
        $email->setFrom($from, $nameFrom);
        $email->addTo($to, $nameTo);
        // $email->addBcc($bcc);
        $email->send();

        $data = $this->cformFactory->create();
        $data->setData($post);
        $data->save();
//        echo "success";
        /* echo "hello";
        exit; */

        $this->messageManager->addSuccess(__('Form successfully submitted'));
        
             
             
    }
}
