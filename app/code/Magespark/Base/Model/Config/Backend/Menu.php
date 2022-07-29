<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */


namespace MageSpark\Base\Model\Config\Backend;

/**
 * Class Menu
 * @package MageSpark\Base\Model\Config\Backend
 */
class Menu extends \Magento\Framework\App\Config\Value implements
    \Magento\Framework\App\Config\Data\ProcessorInterface
{
    /**
     * Validate cache list after save data
     *
     * @return $this
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }

    /**
     * Process config value
     *
     * @param string $value
     * @return string
     */
    public function processValue($value)
    {
        return $value;
    }
}
