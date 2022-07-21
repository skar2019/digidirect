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


namespace MageSpark\Base\Plugin\Backend\Model\Menu;

use Magento\Backend\Model\Menu\Item as NativeItem;
use MageSpark\Base\Helper\Module;

/**
 * Class Item
 *
 * @package MageSpark\Base\Plugin\Backend\Model\Menu
 */
class Item
{
    /**
     * Decleare constant variables
     */
    const BASE_MARKETPLACE = 'MageSpark_Base::marketplace';
    const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=main_menu_to_user_guide';
    const MARKET_URL = 'https://www.magespark.com/magento-2-extensions.html?utm_source=extension&utm_medium=backend&utm_campaign=main_menu_to_catalog';

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * Item constructor.
     *
     * @param Module $moduleHelper
     */
    public function __construct(
        Module $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
    }

    /**
     *
     * @param NativeItem $subject
     * @param $url
     *
     * @return string
     */
    public function afterGetUrl(NativeItem $subject, $url)
    {
        $id = $subject->getId();
        if ($id == self::BASE_MARKETPLACE) {
            $url = self::MARKET_URL;
        }

        /* we can't add guide link into item object - find link again */
        if (strpos($id, '::menuguide') !== false
            && strpos($id, 'MageSpark') !== false
        ) {
            $moduleCode = explode('::', $subject->getId());
            $moduleCode = $moduleCode[0];
            $moduleInfo = $this->moduleHelper->getFeedModuleData($moduleCode);
            if (isset($moduleInfo['guide']) && $moduleInfo['guide']) {
                $url = $moduleInfo['guide'];
                $seoLink = self::SEO_PARAMS;
                if (strpos($url, '?') !== false) {
                    $seoLink = str_replace('?', '&', $seoLink);
                }
                $url .= $seoLink;
            } else {
                $url = '';
            }
        }

        return $url;
    }
}
