<?php
declare(strict_types=1);

namespace Digidirect\Collect\Plugin\Checkout;

use Digidirect\Collect\Model\Carrier\Collectcarrier;
use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Quote\Model\Quote\Address\RateFactory as AddressRateFactory;

class ForceCollectAddressOnPlaceOrder
{
    private CartRepositoryInterface $cartRepository;
    private AddressRateFactory $addressRateFactory;
    private MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        AddressRateFactory $addressRateFactory,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
    ) {
        $this->cartRepository = $cartRepository;
        $this->addressRateFactory = $addressRateFactory;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
    }

    // Guest flow
    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagement $subject,
        string $cartId, // masked quote ID
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
    ): array {
        $realQuoteId = (int)$this->maskedQuoteIdToQuoteId->execute($cartId);
        $this->ensureCollectShippingAddress($realQuoteId);
        return [$cartId, $paymentMethod];
    }

    private function ensureCollectShippingAddress(int $quoteId): void
    {
        $quote = $this->cartRepository->get($quoteId);

        $shippingAddress = $quote->getShippingAddress();
        $method = $shippingAddress ? $shippingAddress->getShippingMethod() : null;

        // If shipping method isn’t collect_collect, nothing to do.
        if ($method !== Collectcarrier::COLLECT_SHIPPING_METHOD) {
            return;
        }

        // Normalize address with required fields
        $this->applyRequiredFieldsForCollect($quote);

        // Re-set shipping method and ensure rate exists
        $shippingAddress->setShippingMethod(Collectcarrier::COLLECT_SHIPPING_METHOD);
        $rate = $shippingAddress->getShippingRateByCode(Collectcarrier::COLLECT_SHIPPING_METHOD);
        if (!$rate) {
            $rate = $this->createAddressRateForCollect();
            $shippingAddress->addShippingRate($rate);
        }
        $shippingAddress->setShippingDescription($rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle());

        // Persist changes
        $this->cartRepository->save($quote);
    }

    private function applyRequiredFieldsForCollect(QuoteEntity $quote): void
    {
        $shipping = $quote->getShippingAddress();
        $billing  = $quote->getBillingAddress();

        // Helpers to pick values from shipping, then billing, then fallback
        $pick = static function ($shipGetter, $billGetter, $fallback = null) use ($shipping, $billing) {
            $val = $shipping && method_exists($shipping, $shipGetter) ? $shipping->{$shipGetter}() : null;
            if ($val === null || $val === '' || $val === [] || $val === 0) {
                $val = $billing && method_exists($billing, $billGetter) ? $billing->{$billGetter}() : $fallback;
            }
            return ($val === null || $val === '' || $val === []) ? $fallback : $val;
        };

        $firstname = $pick('getFirstname', 'getFirstname', 'Store');
        $lastname  = $pick('getLastname',  'getLastname',  'Pickup');
        $telephone = $pick('getTelephone', 'getTelephone', '0000000000');

        $street = $shipping ? $shipping->getStreet() : null;
        if (empty($street) || !is_array($street) || count(array_filter($street)) === 0) {
            $street = $billing ? $billing->getStreet() : null;
        }
        if (empty($street) || !is_array($street) || count(array_filter($street)) === 0) {
            $street = ['Store Pickup'];
        }

        $city      = $pick('getCity',      'getCity',      'Store Pickup');
        $postcode  = $pick('getPostcode',  'getPostcode',  '0000');
        $countryId = $pick('getCountryId', 'getCountryId', 'AU');
        $region    = $pick('getRegion',    'getRegion',    null);
        $regionId  = $pick('getRegionId',  'getRegionId',  null);

        $shipping->setFirstname($firstname);
        $shipping->setLastname($lastname);
        $shipping->setTelephone($telephone);
        $shipping->setStreet($street);
        $shipping->setCity($city);
        $shipping->setPostcode($postcode);
        $shipping->setCountryId($countryId);

        if ($region !== null && $region !== '') {
            $shipping->setRegion($region);
        }
        if ($regionId) {
            $shipping->setRegionId($regionId);
        }

        $shipping->setSaveInAddressBook(0);
        $shipping->setCollectShippingRates(true);
    }

    private function createAddressRateForCollect(): \Magento\Quote\Model\Quote\Address\Rate
    {
        return $this->addressRateFactory->create()
            ->setCode(Collectcarrier::COLLECT_SHIPPING_METHOD)
            ->setCarrier(Collectcarrier::COLLECT_CARRIER_CODE)
            ->setCarrierTitle('Pick Up in Store - Click and Collect')
            ->setMethod(Collectcarrier::COLLECT_CARRIER_CODE)
            ->setMethodTitle('Store Pickup');
    }
}
