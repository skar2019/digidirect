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
        return $this->resultFactory->create()->setContents('

          <p id="retrievepa_id"></p>
          <input type="text" id="retrievepa_id">

          <script type="text/javascript">

              const loadconfigdata = localStorage.getItem("configData-60007039-e927-ec11-aaf7-061f6a8be99c");


              var getpaID = document.getElementById("retrievepa_id");
              getpaID.value = JSON.stringify(loadconfigdata.c);

              var magento_pa_id = document.getElementById("retrievepa_id").;

              localStorage.setItem("Magento_PA_Id", magento_pa_id); 

              alert(getpaID);

              alert(loadconfigdata);


              console.log(JSON.stringify(loadconfigdata))
              console.log(JSON.stringify(loadconfigdata.c))

          </script>
        

        ');
    }
}