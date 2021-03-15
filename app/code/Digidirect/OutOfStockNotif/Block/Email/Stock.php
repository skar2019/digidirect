<?php
namespace Digidirect\OutOfStockNotif\Block\Email;

class Stock extends \Magento\ProductAlert\Block\Email\Stock
{
    /**
     * @var string
     */
    protected $_template = 'Magento_ProductAlert::email/stock.phtml';

    /**
     * Retrieve unsubscribe url for product
     *
     * @param int $productId
     * @return string
     */
    public function getProductUnsubscribeUrl($productId)
    {
        $params = $this->_getUrlParams();
        $params['product'] = $productId;
        $params['email'] = $this->getEmail();
        return $this->getUrl('outofstocknotif/unsubscribe/stock', $params);
    }

    /**
     * Retrieve unsubscribe url for all products
     *
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        $params = $this->_getUrlParams();
        $params['email'] = $this->getEmail();
        return $this->getUrl('outofstocknotif/unsubscribe/stockAll', $params);
    }
}
