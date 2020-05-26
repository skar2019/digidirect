<?php
namespace Ewave\MyStoreWidgetCollect\Plugin\MyStoreWidget\Helper;

use Ewave\Collect\Helper\Data as CollectHelper;
use Ewave\MyStoreWidgetCollect\Helper\Config as ConfigHelper;

class Config
{
    /**
     * @var CollectHelper
     */
    protected $collectHelper;
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param CollectHelper $collectHelper
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        CollectHelper $collectHelper,
        ConfigHelper $configHelper
    ) {
        $this->collectHelper = $collectHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Check if "Full C&C" mode is enabled
     * @return bool
     */
    public function isFullCcEnable()
    {
        return ($this->collectHelper->isCollectEnable() && $this->collectHelper->isFullVariation());
    }

    /**
     * @param \Ewave\MyStoreWidget\Helper\Config $subject
     * @param array $result
     * @return bool
     */
    public function afterIsEnable(
        \Ewave\MyStoreWidget\Helper\Config $subject,
        $result
    ) {
        if (!$this->isFullCcEnable() && $this->configHelper->isDisableFromFullCC()) {
            $result = false;
        }
        return $result;
    }
}
