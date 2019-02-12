<?php
namespace Ewave\Collect\Block;

use Ewave\Collect\Api\ApplyCollectPlaceInterface;
use Ewave\Collect\Api\Data\CollectPlaceInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class CollectPlacesSingleCart
 * @package Ewave\Collect\Block
 */
class CollectPlacesSingleCart extends CollectPlaces
{
    /**
     * @var ApplyCollectPlaceInterface
     */
    protected $applyCollectPlaceApi;

    /**
     * CollectPlacesSingleCart constructor.
     * @param \Ewave\Collect\Helper\Data $collectHelper
     * @param \Ewave\Collect\Model\StorageHandler $storageHandler
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ApplyCollectPlaceInterface $applyCollectPlaceApi
     * @param array $data
     */
    public function __construct(
        \Ewave\Collect\Helper\Data $collectHelper,
        \Ewave\Collect\Model\StorageHandler $storageHandler,
        \Magento\Checkout\Model\Session $checkoutSession,
        Context $context,
        \Magento\Framework\Registry $registry,
        ApplyCollectPlaceInterface $applyCollectPlaceApi,
        array $data = []
    ) {
        parent::__construct($collectHelper, $storageHandler, $checkoutSession, $context, $registry, $data);
        $this->applyCollectPlaceApi = $applyCollectPlaceApi;
    }

    /**
     * @return bool
     */
    public function showCollect()
    {
        return $this->getCollectHelper()->isCollectEnable()
            && $this->getCollectHelper()->isSingleCartVariation();
    }

    /**
     * @return CollectPlaceInterface|bool
     */
    public function getPresetCollectPlace()
    {
        return $this->applyCollectPlaceApi->getSelectedSingleCollectPlace();
    }

    /**
     * @todo Remove if doesn't need
     * @param int $distance
     * @param string $postcode
     * @return array
     */
    public function getCollectPlaces($distance, $postcode)
    {
        $skus = [];
        $quoteItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
        foreach ($quoteItems as $quoteItem) {
            $skus[] = $quoteItem->getSku();
        }

        $places = $this->_storageHandler->getPlacesByData($skus, $postcode, $distance);
        return $places;
    }
}
