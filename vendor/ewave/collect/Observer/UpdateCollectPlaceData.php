<?php

namespace Ewave\Collect\Observer;

use Ewave\Collect\Helper\Data as CollectHelper;
use Magento\Framework\Event\ObserverInterface;

class UpdateCollectPlaceData implements ObserverInterface
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * CollectHelper
     *
     * @var \Ewave\Collect\Helper\Data
     */
    protected $_collectHelper;

    /**
     * QuoteItemHandler
     *
     * @var \Ewave\Collect\Model\QuoteItemHandler
     */
    protected $_quoteItemHandler;

    /**
     * SetCollectPlaceData constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param CollectHelper $collectHelper
     * @param \Ewave\Collect\Model\QuoteItemHandler $quoteItemHandler
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Ewave\Collect\Helper\Data $collectHelper,
        \Ewave\Collect\Model\QuoteItemHandler $quoteItemHandler
    ) {
        $this->_request = $requestInterface;
        $this->_collectHelper = $collectHelper;
        $this->_quoteItemHandler = $quoteItemHandler;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return  void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_collectHelper->isCollectEnable()) {
            $this->_quoteItemHandler->changeQuoteItemCollectType(
                $observer->getQuoteItem(),
                $this->_request->getParam('delivery_type'),
                $this->_request->getParam('collect_place_id'),
                $this->_request->getParam('collect_place_storage_name')
            );
        }
    }
}
