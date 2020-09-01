<?php

/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Digidirect\Customer\Controller\Account;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList as dir;
use Magento\Framework\Filesystem as filesys;
use Magento\Framework\File\Csv as csv;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Class EditPost
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditPost extends \Magento\Customer\Controller\Account\EditPost {

    public function execute() {

        /**
         * For AJAX Validation
         */
        if (isset($_POST["qff_action"])) {
            $data = array();

            $data["result"] = false;

            $action = $_POST["qff_action"];
            $data["result"] = $this->verifyQffDetails($action);

            echo json_encode($data);
            exit;
        } else {

            /*
             * Post Validation
             * 1. Confirm member credentials
             * 
             * if successful, save credentials
             * 
             * else
             * show error prompt, if in case credentials is present, delete.
             */

            $qff_number = $this->getRequest()->getParam('qff_number');
            $qff_lastname = $this->getRequest()->getParam('qff_lastname');

            if ($qff_number != "" && $qff_lastname != "") {
                $resultRedirect = $this->resultRedirectFactory->create();
                $validationResult = false;
                $action = ""; //Initialized action

                $validationResult = $this->verifyQffDetails($action);
                if ($validationResult) {
                    $customerId = $this->session->getCustomerId();
                    $customer = $this->customerRepository->getById($customerId);

                    $customer->setCustomAttribute('qff_number', $qff_number);
                    $customer->setCustomAttribute('qff_lastname', $qff_lastname);

                    $this->customerRepository->save($customer);

                    return parent::execute();
                } else {

                    $this->messageManager->addError(__('Qantas Fequent Fyler details are invalid.'));

                    $this->session->start();

                    $this->session->setCustomerFormData($this->getRequest()->getPostValue());

                    $resultRedirect->setPath('*/*/edit/?a=link');
                    return $resultRedirect;
                }
            } else {
                //Pre caution in case if credentials is present. Delete QFF data.
                $customerId = $this->session->getCustomerId();
                $customer = $this->customerRepository->getById($customerId);

                $customer->setCustomAttribute('qff_number', "");
                $customer->setCustomAttribute('qff_lastname', "");

                return parent::execute();
            }
        }
    }

    function errorMessage() {
        $error = $this->messageManager->addError(__('Qantas Fequent Flyer details are invalid.'));
        return $error;
    }

    public function verifyQffDetails($action) {
        $serviceUrl = "https://api.services-stg.qantasloyalty.com/api/validation/members";
        $status = false;

        if (isset($_POST["qff_number"]) && !empty($_POST["qff_number"]) &&
                isset($_POST["qff_lastname"]) && !empty($_POST["qff_lastname"])) {
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


            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $path= $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');

            $fileDirectoryPath = $path->getPath('var');


            $filePath = $fileDirectoryPath . '/ProntoApi/';
            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }

            $handle = fopen($filePath . 'logs.txt', 'w');

            fwrite($handle, $initial_response);

            fclose($handle);





            if (!empty($response->status)) {
                if ($response->status == "ACTIVE") {
                    $status = true;
                    if ($action === "update") {
                        $customer = $this->customerRepository->getById($this->session->getCustomerId());

                        if ($qff_number != "" && $qff_lastname != "") {
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
