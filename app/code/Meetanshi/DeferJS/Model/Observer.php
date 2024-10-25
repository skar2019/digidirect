<?php

namespace Meetanshi\DeferJS\Model;

use Magento\Framework\Event\ObserverInterface;
use Meetanshi\DeferJS\Helper\Data;


class Observer implements ObserverInterface
{
    protected $_helper;
    
    protected $request;

    public function __construct(
        Data $helper,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_helper = $helper;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $action = $this->request->getFullActionName();
        
        if ($action != 'catalog_product_view') {
            if (!$this->_helper->isEnabled())
                return;
            $response = $observer->getEvent()->getData('response');
            if (!$response)
                return;
            $html = $response->getBody();
            if ($html == '')
                return;
            $conditionalJsPattern = '@(?:<script type="text/javascript"|<script)(.*)</script>@msU';
            preg_match_all($conditionalJsPattern, $html, $_matches);
            $_js_if = implode('', $_matches[0]);
            $html = preg_replace($conditionalJsPattern, '', $html);
            $html .= $_js_if;
            $response->setBody($html);
        }
    }
}