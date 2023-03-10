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

        <input type="text" id="customerpaId">

        <script type="text/javascript">

        // live configData configData-dbd6c84f-2332-ec11-aae9-02dca44cceec
        //staging configData configData-60007039-e927-ec11-aaf7-061f6a8be99c
        const get_PaId = localStorage.getItem("configData-60007039-e927-ec11-aaf7-061f6a8be99c");

          var retrievepa = document.getElementById("customerpaId");
          retrievepa.value = JSON.stringify(get_PaId.c);

          var magento_pa_id = document.getElementById("customerpaId").value;

          localStorage.setItem("Magento_PA_Id", magento_pa_id); 

          alert(get_PaId);


          console.log(JSON.stringify(get_PaId))

        </script>

        

        ');
    }
}