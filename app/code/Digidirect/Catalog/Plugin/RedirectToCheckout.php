<?php
namespace Digidirect\Catalog\Plugin;

class RedirectToCheckout 
{
    protected $_url;
    protected $request;
    protected $helperdata;
    protected $storeManager;
	
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_url = $url;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }
		
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        //Check if product page
        //if ($this->request->getFullActionName() == 'catalog_product_view') {
        
        $isCustom = $subject->getRequest()->getParam('is_custom');
        
        if ($isCustom) {
            $cartrtnurl=$this->storeManager->getStore()->getBaseUrl()."checkout/";
        } else {
            $cartrtnurl=$this->storeManager->getStore()->getBaseUrl();
        }
        
        if($cartrtnurl != '' && isset($cartrtnurl))
        {
             $accUrl = $this->_url->getUrl($cartrtnurl);
             $this->request->setParam('return_url', $accUrl);
        }
        
        return [$productInfo, $requestInfo];
        //}
    }
}