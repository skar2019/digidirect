<?php

namespace Ewave\Collect\Model\Plugin\Checkout;

use Ewave\Collect\Helper\Storage\Data;

class GuestShippingInformationManagement
{
    /**
     * @var \Ewave\Collect\Helper\Data
     */
    private $collectHelper;

    /**
     * @var \Ewave\Collect\Helper\Config\Address
     */
    private $addressHelper;

    /**
     * GuestShippingInformationManagement constructor.
     *
     * @param \Ewave\Collect\Helper\Data $collectHelper
     * @param \Ewave\Collect\Helper\Config\Address $addressHelper
     */
    public function __construct(
        \Ewave\Collect\Helper\Data $collectHelper,
        \Ewave\Collect\Helper\Config\Address $addressHelper
    ) {
        $this->collectHelper = $collectHelper;
        $this->addressHelper = $addressHelper;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param string $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSaveAddressInformation(
        $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        if ($this->collectHelper->isCollectEnable()
            && $this->collectHelper->isSingleVariation()
            && $this->collectHelper->isCollectCarrierCode($addressInformation->getShippingCarrierCode())
        ) {
            $shippingAddress = $addressInformation->getShippingAddress();
            $this->addressHelper->applyDummyAddress($shippingAddress);
        }

        return [$cartId, $addressInformation];
    }
}
