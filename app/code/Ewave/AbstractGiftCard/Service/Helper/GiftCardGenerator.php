<?php

namespace Ewave\AbstractGiftCard\Service\Helper;

use Ewave\AbstractGiftCard\Model\ServiceInterface;
use Magento\GiftCardAccount\Model\Giftcardaccount;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;

class GiftCardGenerator
{
    /**
     * @var \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface
     */
    protected $_abstractGiftCardEntityRepository;

    /**
     * @var \Ewave\AbstractGiftCard\Model\AbstractGiftCardEntityFactory
     */
    protected $_abstractGiftCardEntityFactory;

    /**
     * @var \Magento\GiftCardAccount\Model\GiftcardaccountFactory
     */
    protected $_giftCardAccountFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Ewave\AbstractGiftCard\Api\AbstractGiftCardLoggerInterface
     */
    protected $_logger;

    /**
     * GiftCardGenerator constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository
     * @param \Ewave\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Ewave\AbstractGiftCard\Api\AbstractGiftCardLoggerInterface $logger
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\GiftCardAccount\Model\GiftcardaccountFactory $giftcardaccountFactory,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardEntityRepositoryInterface $abstractGiftCardEntityRepository,
        \Ewave\AbstractGiftCard\Model\AbstractGiftCardEntityFactory $abstractGiftCardEntityFactory,
        \Magento\Framework\Registry $registry,
        \Ewave\AbstractGiftCard\Api\AbstractGiftCardLoggerInterface $logger
    ) {
        $this->_storeManager = $storeManager;
        $this->_abstractGiftCardEntityRepository = $abstractGiftCardEntityRepository;
        $this->_abstractGiftCardEntityFactory = $abstractGiftCardEntityFactory;
        $this->_giftCardAccountFactory = $giftcardaccountFactory;
        $this->_registry = $registry;
        $this->_logger = $logger;
    }

    /**
     * @param ServiceInterface $service
     * @param DataObject $giftCardDataObject
     * @return Giftcardaccount
     */
    public function initGiftCardAccount(
        ServiceInterface $service,
        DataObject $giftCardDataObject
    ) {
        // init model and set data
        $giftCardAccount = $this->_initGiftCardAccount($service, $giftCardDataObject);
        $giftCardAccount->addData($giftCardDataObject->getData());
        if (!$giftCardAccount->getId()) {
            $giftCardAccount->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
                ->setStatus(Giftcardaccount::STATUS_ENABLED);
        }
        $this->_saveGiftCardAccount($service, $giftCardAccount);

        return $giftCardAccount;
    }

    /**
     * @param ServiceInterface $service
     * @param DataObject $giftCardDataObject
     * @return Giftcardaccount
     */
    protected function _initGiftCardAccount(
        ServiceInterface $service,
        DataObject $giftCardDataObject
    ) {
        try {
            $giftCardServiceData = $this->_abstractGiftCardEntityRepository
                ->getGiftCardDataByServiceAndCode($service->getCode(), $giftCardDataObject->getCode());
            if ($giftCardServiceData->getId()) {
                return $giftCardServiceData->getGiftCardAccount();
            }
            // @codingStandardsIgnoreStart
        } catch (NoSuchEntityException $e) {
            //skip
        }
        // @codingStandardsIgnoreEnd
        return $this->_giftCardAccountFactory->create();
    }

    /**
     * @param ServiceInterface $service
     * @param Giftcardaccount $giftCardAccount
     * @return $this
     * @throws \Exception
     */
    protected function _saveGiftCardAccount(
        ServiceInterface $service,
        Giftcardaccount $giftCardAccount
    ) {
        if ($this->_registry->registry('current_giftcardaccount')) {
            $this->_registry->unregister('current_giftcardaccount');
        }
        $this->_registry->register('current_giftcardaccount', $giftCardAccount);
        try {
            $giftCardAccount->save();
            if (!$giftCardAccount->getAbstractGiftCardEntity()) {
                $abstractGiftCardEntity = $service->getAbstractGiftCardEntity()->setData([
                    'service_code'          => $service->getCode(),
                    'giftcard_account_id'   => $giftCardAccount->getId(),
                    'code'                  => $giftCardAccount->getCode(),
                    'pin'                   => $giftCardAccount->getPin()
                ]);
                $this->_abstractGiftCardEntityRepository->save($abstractGiftCardEntity);
                $giftCardAccount->setAbstractGiftCardEntity($abstractGiftCardEntity);
                $service->setAbstractGiftCardEntity($abstractGiftCardEntity);
            }
        } catch (\Exception $e) {
            $this->_logger->debug(['exception' => $e->getMessage()]);
            throw $e;
        }
        $service->setGiftCardAccount($giftCardAccount);
        return $this;
    }
}
