<?php

namespace Ewave\AbstractGiftCard\Api;

interface AbstractGiftCardEntityRepositoryInterface
{

    /**
     * Loads a specified abstract giftcard entity.
     *
     * @param int $id The abstract giftcard entity ID.
     * @return \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface abstract giftcard entity interface.
     */
    public function get($id);

    /**
     * Performs persist operations for a specified abstract giftcard entity.
     *
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface $entity The abstract giftcard entity ID.
     * @return \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityInterface abstract giftcard entity interface.
     */
    public function save(AbstractGiftCardEntityInterface $entity);

    /**
     * Loads a specified abstract giftcard entity.
     *
     * @param string $serviceCode giftcard service code
     * @param string $giftCardCode code of specified giftcard
     * @return mixed
     */
    public function getGiftCardDataByServiceAndCode($serviceCode, $giftCardCode);

    /**
     * @param \Magento\GiftCardAccount\Model\Giftcardaccount $giftcardaccount
     * @return AbstractGiftCardEntityInterface
     * @throws NoSuchEntityException
     */
    public function loadByGiftCardAccount(\Magento\GiftCardAccount\Model\Giftcardaccount $giftcardaccount);

    /**
     * @param \Magento\Framework\DataObject $entityOrderData
     * @return \Ewave\AbstractGiftCard\Model\ResourceModel\AbstractGiftCardEntity
     */
    public function saveEntityOrderData(\Magento\Framework\DataObject $entityOrderData);
}
