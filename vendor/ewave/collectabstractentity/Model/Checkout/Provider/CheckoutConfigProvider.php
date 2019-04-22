<?php

namespace Ewave\CollectAbstractEntity\Model\Checkout\Provider;

/**
 * Class CheckoutConfigProvider
 *
 * @package Ewave\CollectAbstractEntity\Model\Checkout\Provider
 */
class CheckoutConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const PREFILL_SHIPPING_FIELDS = 'collect_prefill_shipping_fields';

    /**
     * @var \Ewave\CollectAbstractEntity\Helper\Config
     */
    protected $configHelper;

    /**
     * CheckoutConfigProvider constructor.
     *
     * @param \Ewave\CollectAbstractEntity\Helper\Config $configHelper
     */
    public function __construct(\Ewave\CollectAbstractEntity\Helper\Config $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'quoteData' => [
                self::PREFILL_SHIPPING_FIELDS => array_keys(
                    $this->configHelper->getPrefillShippingAddressFieldsMatrix()
                )
            ]
        ];
    }
}
