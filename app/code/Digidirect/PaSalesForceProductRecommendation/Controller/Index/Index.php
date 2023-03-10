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

        const get_PaId = localStorage.getItem("configData-dbd6c84f-2332-ec11-aae9-02dca44cceec");

          var cID = document.getElementById("customerpaId");
          var retrievepa = cID.value = JSON.stringify(get_PaId.c);

          var magento_pa_id = document.getElementById("customerpaId").value;

          localStorage.setItem("Magento_PA_Id", magento_pa_id); 

          alert(retrievepa);


          console.log(JSON.stringify(get_PaId))

        </script>

        

        ');
    }
}