<?php
namespace Ewave\ProductOverlay\Model\Rule;

use Ewave\ProductOverlay\Model\Overlays;

class CatalogRuleOverlays implements ProcessorInterface
{
    const OVERLAY_ENTITY_TYPE = 'overlay';
    const OVERLAY_IDS = 'overlay_ids';

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Ewave\ProductOverlay\Model\ResourceModel\Rule\CatalogRule
     */
    protected $resource;

    /**
     * @var array
     */
    protected $productByRuleId = [];

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\Collection
     */
    protected $activeRulesCache;

    /**
     * CatalogRuleOverlays constructor.
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Ewave\ProductOverlay\Model\ResourceModel\Rule\CatalogRule $resource
     */
    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Ewave\ProductOverlay\Model\ResourceModel\Rule\CatalogRule $resource
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->resource = $resource;
    }

    /**
     * @param Overlays $overlay
     * @return bool|null
     */
    public function isApplicable(Overlays $overlay)
    {
        /**
         * @var $product \Magento\Catalog\Model\Product
         */
        $product = $overlay->getProduct();
        if (!$product || !$overlay->getId()) {
            return false;
        }

        /** Ignore checks if an overlay is not assigned to catalog rules or staging updates */
        if (!$this->isCatalogRuleOverlay($overlay->getId())) {
            return null;
        }

        $ruleIds = $this->getCatalogPriceRulesIds($overlay->getId());
        if (empty($ruleIds)) {
            return false;
        }

        if (empty($this->productByRuleId[$product->getId()])) {
            $this->productByRuleId = $this->resource->getPriceRuleProductsByRule($ruleIds);
        }

        foreach ($ruleIds as $item) {
            if (isset($this->productByRuleId[$product->getId()])
                && in_array($item, $this->productByRuleId[$product->getId()])
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $overlayId
     * @return array
     */
    protected function getCatalogPriceRulesIds($overlayId)
    {
        return $this->getActiveRuleIds(
            $this->storeManager->getStore()->getWebsiteId(),
            $this->customerSession->getCustomerGroupId(),
            $overlayId
        );
    }

    /**
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $overlayId
     * @return array
     */
    protected function getActiveRuleIds($websiteId, $customerGroupId, $overlayId)
    {
        if (null === $this->activeRulesCache) {
            $activeRules = $this->ruleCollectionFactory->create()
                ->addFieldToSelect('rule_id')
                ->addWebsiteFilter($websiteId)
                ->addCustomerGroupFilter($customerGroupId)
                ->addFieldToFilter('is_active', 1);

            $activeRules->getSelect()->join(
                ['overlay' => $activeRules->getTable('ewave_product_overlay_catalogrule')],
                'main_table.row_id = overlay.row_id',
                'overlay_id'
            );

            $this->activeRulesCache = $activeRules->getData();
        }

        $ruleIds = [];
        foreach ($this->activeRulesCache as $rule) {
            if ($rule['overlay_id'] == $overlayId) {
                $ruleIds[] = $rule['rule_id'];
            }
        }

        return $ruleIds;
    }

    /**
     * Check if an overlay is assigned to catalog rules or staging updates
     *
     * @param int $overlayId
     * @return int
     */
    protected function isCatalogRuleOverlay($overlayId)
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName('ewave_product_overlay_catalogrule'), 'COUNT(*)')
            ->where('overlay_id = ?', $overlayId);

        return (int)$connection->fetchOne($select);
    }
}
