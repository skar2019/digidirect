<?php
namespace Ewave\CmsUpgrade\Model;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class CmsGenerator
 * @package Ewave\CmsUpgrade\Model
 */
class CmsGenerator extends Generator
{
    /**
     * @param AbstractCollection $collection
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript(AbstractCollection $collection)
    {
        $data = $this->_getUpgradeData();
        foreach ($collection as $object) {
            $data['items'][] = $this->_fillData($object);
        }
        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }

        return $this->_result;
    }
}
