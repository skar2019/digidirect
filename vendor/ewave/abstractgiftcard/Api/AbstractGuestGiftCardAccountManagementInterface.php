<?php

namespace Ewave\AbstractGiftCard\Api;

/**
 * Interface GuestGiftCardAccountManagementInterface
 * @api
 */
interface AbstractGuestGiftCardAccountManagementInterface
{
    /**
     * @param string $cartId
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
     * @return bool
     */
    public function addAbstractGiftCard(
        $cartId,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
    );

    /**
     * @param string $cartId
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return float
     */
    public function checkAbstractGiftCard(
        $cartId,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
    );
}
