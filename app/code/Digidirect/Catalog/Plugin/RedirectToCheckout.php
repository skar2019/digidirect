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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_url = $url;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->_request = $request;
    }
		
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        //Check if product page
        if ($this->_request->getFullActionName() == 'catalog_product_view') {
            $cartrtnurl=$this->storeManager->getStore()->getBaseUrl()."checkout/";
            if($cartrtnurl != '' && isset($cartrtnurl))
                   {
                        $accUrl = $this->_url->getUrl($cartrtnurl);
                        $this->request->setParam('return_url', $accUrl);
                   }
            return [$productInfo, $requestInfo];
        }
    }
}