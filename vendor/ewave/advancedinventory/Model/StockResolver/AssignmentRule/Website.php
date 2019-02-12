<?php
namespace Ewave\AdvancedInventory\Model\StockResolver\AssignmentRule;

use Ewave\AdvancedInventory\Api\AssignmentRuleInterface;
use Ewave\AdvancedInventory\Helper\Config as ConfigHelper;

class Website implements AssignmentRuleInterface
{
    /**
     * @var \Ewave\AdvancedInventory\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Ewave\AdvancedInventory\Helper\Config $configHelper
     */
    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockId($scopeId = null)
    {
        return $this->configHelper->getWebsiteStock($scopeId);
    }
}
