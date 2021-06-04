<?php

namespace Ewave\Banner\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Ewave\Banner\Model\ResourceModel\AttributesShared;

/**
 * @api
 * @since 2.0.4
 *
 * Customer segment fetching has been moved to separate class as it is used in 2 places
 */
class CustomerSegment
{
    const CUSTOMER_SEGMENT = 'customer_segment';
    const REGISTRY_KEY = 'segment_customer';

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AttributesShared
     */
    protected $attributesShared;

    /**
     * CustomerSegment constructor.
     *
     * @param Context $context
     * @param Session $session
     * @param Registry $registry
     * @param StoreManagerInterface $manager
     * @param AttributesShared $attributesShared
     */
    public function __construct(
        Context $context,
        Session $session,
        Registry $registry,
        StoreManagerInterface $manager,
        AttributesShared $attributesShared
    ) {
        $this->attributesShared = $attributesShared;
        $this->storeManager = $manager;
        $this->customerSession = $session;
        $this->httpContext = $context;
    }

    /**
     * @return array|mixed|null
     */
    public function getCustomerSegment()
    {
        try {
            $customerSegment = $this->httpContext->getValue(self::CUSTOMER_SEGMENT);
            /** @var \Magento\Customer\Model\Session $customerSession */
            $result = [];
            $customerId = $this->customerSession->getCustomerId();
            $websiteId = $this->storeManager->getWebsite()->getId();
            if (!$customerId) {
                $allSegmentIds = $this->customerSession->getCustomerSegmentIds();
                if (is_array($allSegmentIds) && isset($allSegmentIds[$websiteId])) {
                    $result = $allSegmentIds[$websiteId];
                }
            } else {
                $result = $customerSegment;
            }
        } catch (\Throwable $exception) {
            $result = [];
        }

        return $result;
    }

    /**
     * @param mixed $customerSegment
     * @return string
     */
    public function normalizeCustomerSegmentToString($customerSegment)
    {
        return $this->normalizeMixedData($customerSegment);
    }

    /**
     * @param mixed $mixedData
     * @return string
     */
    protected function normalizeMixedData($mixedData)
    {
        return is_object($mixedData) ? $this->normalizeObject($mixedData) :
            is_array($mixedData) ? $this->normalizeArray($mixedData) :
                $mixedData;
    }

    /**
     * @param array $array
     * @return string
     */
    protected function normalizeArray(array $array)
    {
        $string = '';
        foreach ($array as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $string .= '_cs_' . $value;
        }
        return $string;
    }

    /**
     * @param object $object
     * @return string
     */
    protected function normalizeObject(object $object)
    {
        try {
            return spl_object_hash($object);
        } catch (\Throwable $exception) {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getDbBannerSegments()
    {
        /**
         * Wrapped in try/catch due compatibility
         */
        try {
            $attributesResource = $this->attributesShared->get();
            $select = $attributesResource->getConnection()->select()
                ->from($attributesResource->getTable('magento_banner_customersegment'), ['*']);
            return $attributesResource->getConnection()->fetchAll($select);
        } catch (\Throwable $exception) {
            return [];
        }
    }
}
