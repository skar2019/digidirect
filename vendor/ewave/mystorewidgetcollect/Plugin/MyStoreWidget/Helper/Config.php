<?php
namespace Ewave\MyStoreWidgetCollect\Plugin\MyStoreWidget\Helper;

use Ewave\Collect\Helper\Data as CollectHelper;

class Config
{
    /**
     * @var CollectHelper
     */
    protected $collectHelper;

    /**
     * @param CollectHelper $collectHelper
     */
    public function __construct(
        CollectHelper $collectHelper
    ) {
        $this->collectHelper = $collectHelper;
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
        if (!$this->isFullCcEnable()) {
            $result = false;
        }
        return $result;
    }
}
