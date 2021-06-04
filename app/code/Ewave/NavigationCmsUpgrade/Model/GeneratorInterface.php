<?php
namespace Ewave\NavigationCMSUpgrade\Model;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Interface GeneratorInterface
 * @package Ewave\NavigationCMSUpgrade\Model
 */
interface GeneratorInterface
{
    /**
     * @param AbstractCollection $collection
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript(AbstractCollection $collection);
}
