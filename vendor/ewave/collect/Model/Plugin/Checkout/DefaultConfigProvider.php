<?php

namespace Ewave\Collect\Model\Plugin\Checkout;

use Ewave\Collect\Helper\Config\Address as AddressCollectHelper;
use Ewave\Collect\Helper\Data as CollectHelper;
use Ewave\Collect\Plugin\Quote\Model\Quote;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DefaultConfigProvider
{
    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * StorageHandler
     *
     * @var \Ewave\Collect\Model\StorageHandler
     */
    protected $storageHandler;

    /**
     * @var CollectHelper
     */
    protected $collectHelper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Ewave\Collect\Helper\Config\Address
     */
    protected $addressCollectHelper;

    /**
     * DefaultConfigProvider constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Ewave\Collect\Model\StorageHandler $storageHandler
     * @param CollectHelper $collectHelper
     * @param \Ewave\Collect\Helper\Config\Address $addressCollectHelper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Ewave\Collect\Model\StorageHandler $storageHandler,
        CollectHelper $collectHelper,
        AddressCollectHelper $addressCollectHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->storageHandler = $storageHandler;
        $this->collectHelper = $collectHelper;
        $this->addressCollectHelper = $addressCollectHelper;
    }

    /**
     * AfterGetConfig
     *
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param [] $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        $result
    ) {
        if ($this->collectHelper->isCollectEnable()) {
            if (isset($result['quoteData'])) {
                $collectItems = true;
                $quoteId = $this->checkoutSession->getQuote()->getId();

                $result['quoteData']['collect_items'] = $this->collectHelper->isCollectItems($quoteId);
                $result['quoteData']['delivery_items'] = $this->collectHelper->isDeliveryItems($quoteId);

                $quote = $this->checkoutSession->getQuote();

                if (empty($result['paymentMethods'])) {
                    $paymentMethods = [];
                    if ($quote->getIsVirtual() || $collectItems) {
                        foreach ($this->paymentMethodManagement->getList($quote->getId()) as $paymentMethod) {
                            $paymentMethods[] = [
                                'code'  => $paymentMethod->getCode(),
                                'title' => $paymentMethod->getTitle()
                            ];
                        }
                    }
                    $result['paymentMethods'] = $paymentMethods;
                }
            }
            $result['quoteData']['collect_places'] = $this->getCollectPlaceInformation();

            $singleVariation = $this->collectHelper->isSingleVariation();
            $singleCartVariation = $this->collectHelper->isSingleCartVariation();
            $result['quoteData']['is_single_collect_variation'] = $singleVariation;
            $result['quoteData']['is_single_cart_collect_variation'] = $singleCartVariation;
            $result['quoteData']['is_collect_enable_on_checkout'] = $this->collectHelper->isCollectEnableOnCheckout();
            $result['quoteData']['collect_default_address'] = $singleVariation ? $this->getCollectDefaultAddress() : [];
            $result['quoteData']['deliver_instead_url'] = $this->collectHelper->getDeliverInsteadUrl();
            $result['quoteData']['change_place_url'] = $this->collectHelper->getChangePlaceUrl();
            $result['quoteData']['get_places_url'] = $this->collectHelper->getPlacesUrl();
            $result['quoteData']['distance_list'] = explode(',', $this->collectHelper->getDefaultDistanceRange());
        }

        return $result;
    }

    /**
     * Get collect place information
     *
     * @return []
     */
    protected function getCollectPlaceInformation()
    {
        $collectPlaces = [];
        $quoteItems = $this->checkoutSession->getQuote()->getAllVisibleItems();

        if (empty($quoteItems)) {
            return [];
        }

        $findPlaces = [];
        foreach ($quoteItems as $quoteItem) {
            if (!$quoteItem->getCollectPlaceId() || !$quoteItem->getCollectPlaceStorageName()) {
                continue;
            }

            /** @var \Magento\Quote\Model\Quote\Item $quoteItem * */
            $key = implode('__', [$quoteItem->getCollectPlaceId(), $quoteItem->getCollectPlaceStorageName()]);
            if (!isset($findPlaces[$key])) {
                $findPlaces[$key]['collect_place'] = $this->storageHandler->getCollectPlaceById(
                    $quoteItem->getCollectPlaceId(),
                    $quoteItem->getCollectPlaceStorageName()
                );
            }
            /** @var $collectPlace \Ewave\Collect\Api\Data\CollectPlaceInterface */
            $collectPlace = $findPlaces[$key]['collect_place'];
            if ($collectPlace instanceof \Ewave\Collect\Api\Data\CollectPlaceInterface) {
                $collectPlaces[$quoteItem->getItemId()]['item_id'] = $quoteItem->getItemId();
                $collectPlaces[$quoteItem->getItemId()]['item_name'] = $quoteItem->getName();
                $collectPlaces[$quoteItem->getItemId()]['collect_place_name'] = $collectPlace->getName();
                $collectPlaces[$quoteItem->getItemId()]['collect_place_address'] = $collectPlace->getAddress();
            }
        }

        return $collectPlaces;
    }

    /**
     * Get default collect address
     *
     * @return string[]
     */
    protected function getCollectDefaultAddress()
    {
        $addresses = $this->addressCollectHelper->getDefaultAddressArray();
        return $addresses;
    }
}
