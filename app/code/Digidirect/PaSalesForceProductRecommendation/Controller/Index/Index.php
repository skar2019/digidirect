<?php

declare(strict_types=1);

namespace Digidirect\PaSalesForceProductRecommendation\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\RawFactory;

class Index implements ActionInterface {

    /**
     * @var  RawFactory
    */

    protected $resultFactory;

    /**
     * Index constructor
     * 
     * @param RawFactory $resultFactory
    */


    public function __construct(RawFactory $resultFactory) {
        $this->resultFactory = $resultFactory;
    }

    public function execute() {
        // die('Test module');
        return $this->resultFactory->create()->setContents('Test Customer ID');
        

        // $resultPage = $this->resultPageFactory ->create();
        // $blockInstance = $resultPage->getLayout()->getBlock('pa-salesforce-data');

        // return $resultPage;

        echo '<script>alert("This is alert")</script>';
    }
}