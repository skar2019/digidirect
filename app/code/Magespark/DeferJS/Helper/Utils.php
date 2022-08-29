<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark_CopyCmsPageBlock
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

namespace MageSpark\DeferJS\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;
use \Magento\Store\Model\ScopeInterface;

class Utils
{
    const DEFER_JS_GENERAL_ACTIVE = 'deferjs/general/active';
    const DEFER_JS_CONFIG_CONTROLLER = 'deferjs/config/controller';
    const DEFER_JS_CONFIG_PATH = 'deferjs/config/path';
    const DEFER_JS_CONFIG_IN_BODY = 'deferjs/config/in_body';
    const DEFER_JS_CONFIG_IFRAME = 'deferjs/config/iframe';
    const DEFER_JS_CONFIG_SHOW_PATH = 'deferjs/config/show_path';
    const DEFER_JS_CONFIG_HOME_PAGE = 'deferjs/config/home_page';
    const DEFER_JS_HOME_PAGE_PATH = 'cms_index_index';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Json
     */
    private $json;

    /**
     * Data constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $json
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $json
    ) {
        $this->json        = $json;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if defer is enabled for the page
     *
     * @param Http $request
     * @return bool
     */
    public function isEnabled(Http $request)
    {
        $active = $this->scopeConfig->getValue(self::DEFER_JS_GENERAL_ACTIVE,
            ScopeInterface::SCOPE_STORE);
        if ($active !== '1') {
            return false;
        }

        /** check if home page is excluded from deferJs */
        $activeHome = $this->scopeConfig->getValue(self::DEFER_JS_CONFIG_HOME_PAGE,
            ScopeInterface::SCOPE_STORE);
        if ($activeHome === '1' && $request->getFullActionName() === self::DEFER_JS_HOME_PAGE_PATH) {
            return false;
        }

        /** check controller full action name with regex expression */
        if ($this->regexMatch(
            $this->scopeConfig->getValue(self::DEFER_JS_CONFIG_CONTROLLER, ScopeInterface::SCOPE_STORE),
            $request->getFullActionName(),
            1
        )) {
            return false;
        }

        /** check URI path with regex expression */
        if ($this->regexMatch(
            $this->scopeConfig->getValue(self::DEFER_JS_CONFIG_PATH, ScopeInterface::SCOPE_STORE),
            $request->getRequestUri()
        )) {
            return false;
        }

        return true;
    }

    /**
     * @param $regex
     * @param $matchTerm
     * @param null $type
     * @return bool
     */
    public function regexMatch($regex, $matchTerm, $type = null)
    {
        if (!$regex) {
            return false;
        }

        $rules = $this->json->unserialize($regex);
        if (empty($rules)) {
            return false;
        }

        foreach ($rules as $rule) {
            $regex = trim($rule['deferjs'], '#');
            if ($regex === '') {
                continue;
            }
            if ($type === 1) {
                $regexs = explode('_', $regex);
                switch (count($regexs)) {
                    case 1:
                        $regex .= '_index_index';
                        break;
                    case 2:
                        $regex .= '_index';
                        break;
                    default:
                        break;
                }
            }

            $regexp = '#' . $regex . '#';
            if (@preg_match($regexp, $matchTerm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if move Defer Javascript In HTML Body Tag
     *
     * @return bool
     */
    public function inBody()
    {
        $active = $this->scopeConfig->getValue(self::DEFER_JS_CONFIG_IN_BODY,
            ScopeInterface::SCOPE_STORE);
        return !($active !== '1');
    }

    /**
     * Check if defer Iframe is enabled
     *
     * @return bool
     */
    public function isDeferIframe()
    {
        $active = $this->scopeConfig->getValue(self::DEFER_JS_CONFIG_IFRAME,
            ScopeInterface::SCOPE_STORE);
        return !($active !== '1');
    }

    /**
     * return if Show controller path in bottom of page
     *
     * @return bool
     */
    public function isShowControllersPath()
    {
        $active = $this->scopeConfig->getValue(self::DEFER_JS_CONFIG_SHOW_PATH,
            ScopeInterface::SCOPE_STORE);
        return !($active !== '1');
    }
}
