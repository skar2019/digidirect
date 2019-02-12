<?php
namespace Ewave\AdvancedInventory\Model;

use Ewave\AdvancedInventory\Api\StockResolverInterface;
use Ewave\AdvancedInventory\Api\AssignmentRuleInterface;
use Ewave\AdvancedInventory\Helper\Config as ConfigHelper;
use Magento\CatalogInventory\Model\Stock;

class StockResolver implements StockResolverInterface
{
    /**
     * @var \Ewave\AdvancedInventory\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Ewave\AdvancedInventory\Helper\Config
     */
    protected $assignmentRules;

    /**
     * @var int
     */
    protected $currentStockId;

    /**
     * @param \Ewave\AdvancedInventory\Helper\Config $configHelper
     * @param array $assignmentRules
     */
    public function __construct(
        ConfigHelper $configHelper,
        array $assignmentRules = []
    ) {
        $this->configHelper = $configHelper;
        $this->assignmentRules = $assignmentRules;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStockId($scopeId = null)
    {
        if ($this->currentStockId === null) {
            $this->currentStockId = Stock::DEFAULT_STOCK_ID;
            if ($this->configHelper->isEnabled()) {
                $assignmentRule = $this->configHelper->getOrderAssignmentRule();
                if (!empty($this->assignmentRules[$assignmentRule])) {
                    $rule = $this->assignmentRules[$assignmentRule];
                    if ($rule instanceof AssignmentRuleInterface && $stockId = $rule->getStockId($scopeId)) {
                        $this->currentStockId = $stockId;
                    }
                }
            }
        }
        return $this->currentStockId;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedProductTypes()
    {
        return $this->configHelper->getAllowedProductTypes();
    }
}
