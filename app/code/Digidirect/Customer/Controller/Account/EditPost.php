<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Digidirect\Customer\Controller\Account;

use Magento\Framework\App\ObjectManager;
/**
 * Class EditPost
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends \Magento\Customer\Controller\Account\EditPost 
{   
    public function execute()
    {      
        if(isset($_POST["qff_action"])){
            $data = array();
            
            $action = $_POST["qff_action"];
            $data["result"] = $this->verifyQffDetails($action);

            echo json_encode($data);
            exit;
        }
        else{
//            $qff_number = $this->getRequest()->getParam('qff_number');
//            $qff_lastname = $this->getRequest()->getParam('qff_lastname');
//
//            if($qff_number != "" && $qff_lastname != ""){
//                $customerId = $this->session->getCustomerId();
//                $customer = $this->customerRepository->getById($customerId);
//
//                $customer->setCustomAttribute('qff_number', $qff_number);
//                $customer->setCustomAttribute('qff_lastname', $qff_lastname);
//
//                $this->customerRepository->save($customer);
//            }
//            
  //$message = 'Validation unsuccessful.';
//              $this->_messageManager->addError($message);
             
//          }
//		return $resultRedirect;
            
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
       $resultRedirect = $this->resultRedirectFactory->create();
//         $this->messageManager->addSuccess(__('Validation unsuccessful'));
       
         $validationResult = $this->verifyQffDetails($action);
          if ($validationResult ){
               
                $this->messageManager->addSuccess(__('Validation Succesful'));
                 return $resultRedirect->setPath('customer/account');
            }else{
                 $this->messageManager->addError(__('Validation Unsuccesful'));
                 return $resultRedirect->setPath('customer/account/edit/?a=link');

            }
       
            
            //return parent::execute();
        }
    }
    
    public function verifyQffDetails($action)
    {
        $serviceUrl = "https://api.services-stg.qantasloyalty.com/api/validation/members";
        $status = false;
        
        if(isset($_POST["qff_number"]) && !empty($_POST["qff_number"]) &&
           isset($_POST["qff_lastname"]) && !empty($_POST["qff_lastname"])){
            $qff_number = $_POST["qff_number"];
            $qff_lastname = $_POST["qff_lastname"];
        
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $serviceUrl,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_POSTFIELDS => "{\r\n \"memberId\" : \"$qff_number\",\r\n \"criteria\" : {\"surname\" : \"$qff_lastname\"}\r\n}\r\n",
              CURLOPT_HTTPHEADER => array(
                "authorization: Basic ZGlnaURpcmVjdDpzWUNsdm8wUjZsTGdFODg1"
              ),
            ));

            $initial_response = curl_exec($curl);

            curl_close($curl);

            $status = false;
            $response = json_decode($initial_response);
            if(!empty($response->status))
            {
                if($response->status == "ACTIVE")
                {
                    $status = true;
                    if($action === "update"){
                        $customer = $this->customerRepository->getById($this->session->getCustomerId());

                        if($qff_number != "" && $qff_lastname != ""){
                            $customerId = $this->session->getCustomerId();
                            $customer = $this->customerRepository->getById($customerId);

                            $customer->setCustomAttribute('qff_number', $qff_number);
                            $customer->setCustomAttribute('qff_lastname', $qff_lastname);

                            $this->customerRepository->save($customer);
                        }
                    }
                }
            }
        }
        
        return $status;
    }
}
