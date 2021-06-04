<?php
namespace Ewave\ExtendedShippingRates\Block\Adminhtml\ExtendedShippingRates\Carrier\Edit\Button;

use Magento\Framework\Registry;
use Ewave\ExtendedShippingRates\Model\Carrier;
use Ewave\ExtendedShippingRates\Model\CarrierFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * @var CarrierFactory
     */
    protected $carrierFactory;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param CarrierFactory $carrierFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CarrierFactory $carrierFactory
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * Get carrier
     *
     * @return \Ewave\ExtendedShippingRates\Model\Carrier
     */
    public function getCarrier()
    {
        $carrier = $this->registry->registry(Carrier::CURRENT_CARRIER);
        if (!$carrier) {
            $carrier = $this->carrierFactory->create();
        }

        return $carrier;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}
