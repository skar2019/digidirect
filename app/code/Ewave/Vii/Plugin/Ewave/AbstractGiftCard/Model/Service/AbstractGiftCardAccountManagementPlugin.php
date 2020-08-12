<?php

namespace Ewave\Vii\Plugin\Ewave\AbstractGiftCard\Model\Service;

use Ewave\AbstractGiftCard\Model\Service\AbstractGiftCardAccountManagement;
use Ewave\Vii\Service\Config\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\GiftCardAccount\Model\Giftcardaccount;

/**
 * Class AbstractGiftCardAccountManagementPlugin
 * @package Ewave\Vii\Plugin\Ewave\AbstractGiftCard\Model\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractGiftCardAccountManagementPlugin extends AbstractGiftCardAccountManagement
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $giftCAHelper;

    /**
     * @var \Ewave\Vii\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * AbstractGiftCardAccountManagementPlugin constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardHelper
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardAccountFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ewave\AbstractGiftCard\Helper\Data $helper
     * @param \Magento\GiftCardAccount\Helper\Data $giftCAHelper
     * @param Config $config
     * @param \Ewave\Vii\Helper\Data $helperData
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\GiftCardAccount\Helper\Data $giftCardHelper,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftCardAccountFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ewave\AbstractGiftCard\Helper\Data $helper,
        \Magento\GiftCardAccount\Helper\Data $giftCAHelper,
        Config $config,
        \Ewave\Vii\Helper\Data $helperData,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($quoteRepository, $giftCardHelper, $giftCardAccountFactory, $storeManager, $helper);
        $this->config = $config;
        $this->giftCAHelper = $giftCAHelper;
        $this->helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Ewave\AbstractGiftCard\Model\Service\AbstractGiftCardAccountManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundAddAbstractGiftCard(
        $subject,
        \Closure $proceed,
        $cartId,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
    ) {
        if (!$this->config->isActive()) {
            return $proceed($cartId, $giftCardAccountData);
        }

        if (!$this->checkoutSession->getQuoteId()) {
            throw new LocalizedException(__('We can\'t apply this gift card.'));
        }

        $serviceInstance = $this->_initService($giftCardAccountData);
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->_quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        $cards = $this->giftCAHelper->getCards($quote);
        if (!empty($cards) && $entity = $serviceInstance->getAbstractGiftCardEntity()) {
            foreach ($cards as $card) {
                if ($card[Giftcardaccount::CODE] == $entity->getCode()) {
                    throw new LocalizedException(
                        __('This gift card account is already in the quote.')
                    );
                }
            }
        }

        if ($serviceInstance->canHold()) {
            try {
                $serviceInstance->setStore($quote->getStoreId());
                $serviceInstance->validate()->hold($quote->getBaseGrandTotal());
                if ($this->helperData->isCustomerAsGuest($quote)) {
                    $this->helperData->saveVisitorData($quote);
                }
            } catch (LocalizedException $e) {
                $availableBalance = $serviceInstance->getAvailableBalance();
                if (!$availableBalance) {
                    throw new LocalizedException(__($e->getMessage()));
                }
                $serviceInstance->validate()->hold($availableBalance);
                if ($this->helperData->isCustomerAsGuest($quote)) {
                    $this->helperData->saveVisitorData($quote);
                }
            }
        }

        if (!$serviceInstance->getGiftCardAccount()) {
            throw new LocalizedException(
                __('Something went wrong with processing a Gift Card. Please try again later')
            );
        }
        try {
            $serviceInstance->getGiftCardAccount()->addToCart(true, $quote);
        } catch (\Exception $e) {
            if ($serviceInstance->getLastToken() && $serviceInstance->canCancel()) {
                $serviceInstance->setStore($quote->getStoreId());
                $serviceInstance->validate()->cancel($e->getMessage(), $serviceInstance->getLastToken());
            }
            throw new CouldNotSaveException(__('Could not add gift card code'));
        }
        return true;
    }
}
