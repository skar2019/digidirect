<?php

namespace Ewave\AbstractGiftCard\Api;

/**
 * Interface AbstractGiftCardAccountManagementInterface
 * @api
 */
interface AbstractGiftCardAccountManagementInterface
{
    /**
     * @param int $cartId
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return float
     */
    public function checkAbstractGiftCard(
        $cartId,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
    );

    /**
     * @param int $cartId
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
     * @return mixed
     */
    public function addAbstractGiftCard(
        $cartId,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $giftCardAccountData
    );
}
