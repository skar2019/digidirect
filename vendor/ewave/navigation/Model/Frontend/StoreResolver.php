<?php

namespace Ewave\Navigation\Model\Frontend;

use Magento\Store\Model\StoreManagerInterface;

/**
 * @api
 * Store id and code resolver
 */
class StoreResolver
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var null
     */
    protected $storeCode = null;

    /**
     * @var null
     */
    protected $storeId = null;

    /**
     * StoreResolver constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        if (null === $this->storeId) {
            $this->storeId = $this->storeManager->getStore()->getId();
        }

        return $this->storeId;
    }

    /**
     * @return null|string
     */
    public function getStoreCode()
    {
        if (null === $this->storeCode) {
            $this->storeCode = $this->storeManager->getStore()->getCode();
        }

        return $this->storeCode;
    }
}
