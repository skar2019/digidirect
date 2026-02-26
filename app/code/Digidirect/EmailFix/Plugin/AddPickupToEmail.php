<?php

namespace Digidirect\EmailFix\Plugin;

use Magento\Framework\App\ObjectManager;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order;

class AddPickupToEmail
{
    protected $sourceRepository;

    public function __construct(
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceRepository = $sourceRepository;
    }

    public function beforeSetTemplateVars(
        \Magento\Sales\Model\Order\Email\Container\Template $subject,
        array $templateVars
    ) {
        if (!isset($templateVars['order'])) {
            return [$templateVars];
        }

        /** @var Order $order */
        $order = $templateVars['order'];

        $pickupLocationCode = $order->getData('pickup_location_code');

        if ($pickupLocationCode) {
            try {
                $source = $this->sourceRepository->get($pickupLocationCode);

                $templateVars['pickup_location_name'] = $source->getName();

                $templateVars['pickup_location_address'] =
                    $source->getStreet() . '<br>' .
                    $source->getCity() . ', ' .
                    $source->getRegion() . ' ' .
                    $source->getPostcode() . '<br>' .
                    $source->getCountryId();

            } catch (\Exception $e) {
                $templateVars['pickup_location_name'] = '';
                $templateVars['pickup_location_address'] = '';
            }
        }

        return [$templateVars];
    }
}