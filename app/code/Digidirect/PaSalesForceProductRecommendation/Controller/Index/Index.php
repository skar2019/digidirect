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

       <p id="input3"></p>
  <input type="text" id="input4">

<script type="text/javascript">

  const sample = localStorage.getItem("configData-60007039-e927-ec11-aaf7-061f6a8be99c");


var testget = document.getElementById("input4");
testget.value = JSON.stringify(sample.c);

var magento_pa_id = document.getElementById("input4").value;

localStorage.setItem("Magento_PA_Id", magento_pa_id); 

// alert(testdata);

 alert(sample);


console.log(JSON.stringify(sample))

</script>
        

        ');
    }
}