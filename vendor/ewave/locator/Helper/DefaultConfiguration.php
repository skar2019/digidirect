<?php

namespace Ewave\Locator\Helper;

use Ewave\Locator\Model\Source\ClickAction;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Default action configuration
 * @since 1.2.0
 */
class DefaultConfiguration extends AbstractHelper
{
    /**
     * @param null $storeId
     * @return string
     */
    public function getStoreDetailClickAction($storeId = null)
    {
        return ClickAction::ACTION_REDIRECT;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isOpenInPopup($storeId = null)
    {
        return $this->getStoreDetailClickAction($storeId) == ClickAction::ACTION_OPEN_IN_POPUP;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isRedirectAction($storeId = null)
    {
        return $this->getStoreDetailClickAction($storeId) == ClickAction::ACTION_REDIRECT;
    }
}
