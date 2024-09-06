<?php

namespace Digidirect\ParticularAudienceAPI\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * CheckoutCartAddObserver
 */
class CheckoutCartAddObserver implements ObserverInterface {
    
    protected $_layout;
    
    protected $_storeManager;
    
    protected $_request;
    
    protected $logger;
    
    /**
     * __construct
     *
     * @param \Magento\Store\Model\StoreManagerInterface storeManager
     * @param \Magento\Framework\View\LayoutInterface layout
     * @param \Magento\Framework\App\RequestInterface request
     * @param \Magento\Framework\Serialize\SerializerInterface serializer
     *
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }
    
    /**
     * execute
     *
     * @param EventObserver observer
     *
     * @return void
     */
    public function execute(EventObserver $observer) {
        
        //$this->logger->info("CheckoutCartAddObserver");
        $postValue = $this->_request->getParams();
        $item = $observer->getQuoteItem();
        
        $refId = [];
        $refId[] = ['label' => 'refId', 'value' => $item->getProductId()];
        $item->addOption([
            'product_id' => $item->getProductId(),
            'code' => 'additional_options',
            'value' => $this->serializer->serialize($refId),
        ]);

        if (isset($postValue['route_id']) && $postValue['route_id']) {
            $this->logger->info("route_id: " . $postValue['route_id']);
            $routeId = [];
            $routeId[] = ['label' => 'route_id', 'value' => $postValue['route_id']];
            if (count($routeId) > 0) {
                $item->addOption([
                    'product_id' => $item->getProductId(),
                    'code' => 'additional_options',
                    'value' => $this->serializer->serialize($routeId),
                ]);
            }
        }
        
        if (isset($postValue['widget_id']) && $postValue['widget_id']) {
            $this->logger->info("widget_id: " . $postValue['widget_id']);
            $widgetId = [];
            $widgetId[] = ['label' => 'widgetId', 'value' => $postValue['widget_id']];
            if (count($widgetId) > 0) {
                $item->addOption([
                    'product_id' => $item->getProductId(),
                    'code' => 'additional_options',
                    'value' => $this->serializer->serialize($widgetId),
                ]);
            }
        }
    }
}