<?php

/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Digidirect\Qantas\Controller\Account;

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

    /**
     * @var dir
     */
    private $directoryList;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\CustomerExtractor $customerExtractor,
        dir $directoryList,
        ?\Magento\Framework\Escaper $escaper = null,
        ?\Magento\Customer\Model\AddressRegistry $addressRegistry = null,
        ?filesys $filesystem = null,
        ?\Magento\Customer\Api\SessionCleanerInterface $sessionCleaner = null,
        ?\Magento\Customer\Model\AccountConfirmation $accountConfirmation = null,
        ?\Magento\Customer\Model\Url $customerUrl = null
    ) {
        $this->directoryList = $directoryList;
        parent::__construct(
            $context,
            $customerSession,
            $accountManagement,
            $customerRepository,
            $formKeyValidator,
            $customerExtractor,
            $escaper,
            $addressRegistry,
            $filesystem,
            $sessionCleaner,
            $accountConfirmation,
            $customerUrl
        );
    }

    public function execute() {

        /**
         * For AJAX Validation
         */
        if (isset($_POST["qff_action"])) {
            $data = array();
            $data["result"] = false;
            $action = $_POST["qff_action"];
            
            $data["result"] = $this->verifyQffDetails($action);
            if ($data["result"] == true) {
                $qff_number = $_POST["qff_number"];
                $qff_lastname = $_POST["qff_lastname"];
                $this->session->start();
                $customerId = $this->session->getCustomerId();
                $customer = $this->customerRepository->getById($customerId);

                $customer->setCustomAttribute('qff_number', $qff_number);
                $customer->setCustomAttribute('qff_lastname', $qff_lastname);

                $this->customerRepository->save($customer);
            }

            echo json_encode($data);
            exit;
        } else {
            return parent::execute();
        }
    }

    public function verifyQffDetails($action) {
        $serviceUrl = "https://api.services.qantasloyalty.com/api/validation/members";
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

            $fileDirectoryPath = $this->directoryList->getPath('var');


            $filePath = $fileDirectoryPath . '/ProntoApi/';
            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }

            $handle = fopen($filePath . 'logs.txt', 'a');

            fwrite($handle, $initial_response);

            fclose($handle);


            if (!empty($response->status)) {
                if ($response->status == "ACTIVE") {
                    $status = true;
                }
            }
        }

        return $status;
    }

}
