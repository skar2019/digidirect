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

  const sample = {"rec":{"r":[{"nwt":[],"id":"3b6feb9b-ef8c-ed11-aae9-02dca44cceec","pt":42,"pn":"patest","pv":"test32","p":"/photo-accessories","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"efbc95c7-ef8c-ed11-aae9-02dca44cceec","pt":41,"pn":"patest","pv":"test32","p":"/lighting-studio","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"f90239b1-ef8c-ed11-aae9-02dca44cceec","pt":40,"pn":"patest","pv":"test32","p":"/computers-mobile","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"fcab2387-ef8c-ed11-aae9-02dca44cceec","pt":38,"pn":"patest","pv":"test32","p":"/audio-visual","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"3dc7da70-ef8c-ed11-aae9-02dca44cceec","pt":37,"pn":"patest","pv":"test32","p":"/lenses/mirrorless-lenses","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"10705c71-166b-ed11-aae9-02dca44cceec","pt":36,"pn":"patest","pv":"test32","p":"/cameras/**","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"33a78d44-3719-ed11-aae9-02dca44cceec","pt":35,"pn":"patest","pv":"test32","p":"/find**","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"51bc87ec-dd0e-ed11-aae9-02dca44cceec","pt":33,"pn":"","pv":"","p":"/find**","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"13af2931-bfdc-ec11-aae9-02dca44cceec","pt":31,"pn":"patest","pv":"test32","p":"/","s":[],"c":[],"tre":false,"trs":[],"trw":[{"rc":".recommended-for-you-column .product-tab-navigation"},{"rc":".you-may-also-like .you-may-also-like-inner"},{"rc":" #maincontent .magezon-builder .mgz-element.mgz-element-column.right-side-banner-upper-wrapper .mgz-element-inner.uh4leb4-s"},{"rc":"#maincontent .magezon-builder .mgz-element.mgz-element-column.right-side-banner-upper-wrapper .mgz-element-inner"},{"rc":"#maincontent .magezon-builder .mgz-element.mgz-element-row.full_width_row .mgz-element.mgz-element-row.product-tab-tablet-wrapper.mgz-hidden-xl.mgz-hidden-sm.mgz-hidden-xs.full_width_row .mgz-tabs-nav"},{"rc":"#maincontent .magezon-builder .mgz-element.mgz-element-row.full_width_row .mgz-element.mgz-element-row.product-tab-tablet-wrapper.mgz-hidden-xl.mgz-hidden-sm.mgz-hidden-xs.full_width_row "}]},{"nwt":[],"id":"73a8e8be-f240-ec11-aae9-02dca44cceec","pt":30,"pn":"patest","pv":"test32","p":"/**","s":[],"c":[],"tre":false,"trs":[],"trw":[]},{"nwt":[],"id":"63a2d2dc-773b-ec11-aae9-02dca44cceec","pt":21,"pn":"","pv":"","p":"/","s":[],"c":[],"tre":true,"trs":[],"trw":[{"rc":".recommended-for-you-column .product-tab-navigation"},{"rc":".you-may-also-like .you-may-also-like-inner"},{"rc":" #maincontent .magezon-builder .mgz-element.mgz-element-column.right-side-banner-upper-wrapper .mgz-element-inner.uh4leb4-s"},{"rc":"#maincontent .magezon-builder .mgz-element.mgz-element-column.right-side-banner-upper-wrapper .mgz-element-inner"},{"rc":"#maincontent .magezon-builder .mgz-element.mgz-element-row.full_width_row .mgz-element.mgz-element-row.product-tab-tablet-wrapper.mgz-hidden-xl.mgz-hidden-sm.mgz-hidden-xs.full_width_row .mgz-tabs-nav"},{"rc":"#maincontent .magezon-builder .mgz-element.mgz-element-row.full_width_row .mgz-element.mgz-element-row.product-tab-tablet-wrapper.mgz-hidden-xl.mgz-hidden-sm.mgz-hidden-xs.full_width_row "}]},{"nwt":[],"id":"2d4f22d6-da0e-ed11-aae9-02dca44cceec","pt":3,"pn":"","pv":"","p":"/**","s":[],"c":[],"tre":false,"trs":[],"trw":[]}],"cc":1,"iero":true},"vs":{"ie":false,"r":[]},"ga":{"ie":false,"id":"","ds":[],"ms":[]},"c":"3c0e2c68-9f8d-47c5-bf88-d041aeef2bdb","s":{"id":"c810adfe-a4fc-4be1-8997-e49c8b953b0c","d":1800000},"wm":1,"sc":"ddr","pro":{"cc":1,"r":[{"p":"/photo-accessories","s":[],"c":[]},{"p":"/lighting-studio","s":[],"c":[]},{"p":"/computers-mobile","s":[],"c":[]},{"p":"/audio-visual","s":[],"c":[]},{"p":"/lenses/mirrorless-lenses","s":[],"c":[]},{"p":"/cameras/**","s":[],"c":[]},{"p":"/find**","s":[],"c":[]},{"p":"/find**","s":[],"c":[]},{"p":"/","s":[],"c":[]},{"p":"/","s":[],"c":[]},{"p":"/**","s":[],"c":[]},{"p":"/","s":[],"c":[]},{"p":"/**","s":[],"c":[]},{"p":"/","s":[],"c":[]},{"p":"/catalogsearch/result/**","s":[],"c":[]},{"p":"/**","s":[],"c":[]},{"p":"/**","s":[],"c":[]}]},"a":{"l":1,"h":2.7},"tv":"v1.0.11.1-5986c40","expireDate":1678239788691,"apiVersion":"2.7"}


var testget = document.getElementById("input4");
testget.value = JSON.stringify(sample.c);

var magento_pa_id = document.getElementById("input4").value;

localStorage.setItem("Magento_PA_Id", magento_pa_id); 

// alert(testdata);


console.log(JSON.stringify(sample))

</script>

        

        ');
    }
}