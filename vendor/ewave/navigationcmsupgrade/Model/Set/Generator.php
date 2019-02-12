<?php
namespace Ewave\NavigationCMSUpgrade\Model\Set;

use Ewave\CmsUpgrade\Model\Generator as CMSUpgradeGenerator;

/**
 * Class Generator
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Menu
 */
class Generator extends CMSUpgradeGenerator
{
    /**
     * @param \Ewave\Navigation\Model\ResourceModel\Set\Collection $collection
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript($collection)
    {
        $items = [];

        foreach ($collection as $key => $item) {
            $items[$key] = $item->getData();
        }
        $data = $this->_getUpgradeData();
        $data['items'][] = $items;
        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }
        return $this->_result;
    }
}
