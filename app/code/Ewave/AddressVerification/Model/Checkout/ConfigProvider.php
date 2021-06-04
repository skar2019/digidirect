<?php
namespace Ewave\AddressVerification\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class ConfigProvider
 * @package Ewave\AddressVerification\Model\Checkout
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Ewave\AddressVerification\Helper\Aupost
     */
    protected $autocompleteHelper;

    /**
     * ConfigProvider constructor.
     * @param \Ewave\AddressVerification\Helper\Autocomplete $autocompleteHelper
     */
    public function __construct(
        \Ewave\AddressVerification\Helper\Autocomplete $autocompleteHelper
    ) {
        $this->autocompleteHelper = $autocompleteHelper;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return ['aupost_enabled' => $this->autocompleteHelper->isAuPostEnabled() ? 'on' : 'off'];
    }
}
