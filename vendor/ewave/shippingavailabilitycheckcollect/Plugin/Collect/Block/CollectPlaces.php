<?php
namespace Ewave\ShippingAvailabilityCheckCollect\Plugin\Collect\Block;

use Ewave\ShippingAvailabilityCheck\Helper\Data;

/**
 * Class CollectPlaces
 * @package Ewave\ShippingAvailabilityCheckCollect\Plugin\Collect\Block
 */
class CollectPlaces
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * CollectPlaces constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Ewave\Collect\Block\CollectPlaces $subject
     * @param int $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetIsPostcodeFieldNeeded(
        \Ewave\Collect\Block\CollectPlaces $subject,
        $result
    ) {
        return $this->helper->isShippingAvailabilityCheckEnable() ? $result : true;
    }
}
