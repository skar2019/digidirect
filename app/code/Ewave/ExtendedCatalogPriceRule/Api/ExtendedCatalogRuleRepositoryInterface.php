<?php
namespace Ewave\ExtendedCatalogPriceRule\Api;

use Ewave\ExtendedCatalogPriceRule\Api\Data\ExtendedCatalogRuleInterface;

/**
 * Interface ExtendedCatalogRuleRepositoryInterface
 * @package Ewave\ExtendedCatalogPriceRule\Api
 */
interface ExtendedCatalogRuleRepositoryInterface
{
    /**
     * @param int $id Extended Catalog Rule entity ID
     * @return ExtendedCatalogRuleInterface
     */
    public function get($id);

    /**
     * @param ExtendedCatalogRuleInterface $rule
     * @return ExtendedCatalogRuleInterface
     */
    public function save(ExtendedCatalogRuleInterface $rule);

    /**
     * @param ExtendedCatalogRuleInterface $rule
     * @return bool
     */
    public function delete(ExtendedCatalogRuleInterface $rule);
}
