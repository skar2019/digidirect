<?php

namespace Ewave\CmsUpgrade\Model;

/**
 * @method \Ewave\CmsUpgrade\Model\ResourceModel\DataVersion _getResource()
 * 
 * Class DataVersion
 * @package Ewave\CmsUpgrade\Model
 */
class DataVersion extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize DataVersion model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ewave\CmsUpgrade\Model\ResourceModel\DataVersion');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->_getResource()->loadDataVersion();
    }

    /**
     * @param string $version
     * @param string $fileName
     * @return int
     */
    public function updateVersion($version, $fileName = null)
    {
        return $this->_getResource()->updateDataVersion($version, $fileName);
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function installFile($fileName)
    {
        return $this->_getResource()->installFile($fileName);
    }

    /**
     * @param string $fileName
     * @return bool
     */
    public function isFileInstalled($fileName)
    {
        return $this->_getResource()->isFileInstalled($fileName);
    }
}
