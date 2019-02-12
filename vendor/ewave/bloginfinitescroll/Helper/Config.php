<?php
namespace Ewave\BlogInfiniteScroll\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * Contains all infinite scroll add-on configurations
 */
class Config extends AbstractHelper
{
    const EWAVE_BLOG_INFINITE_CATEGORY_ENABLED = 'ewave_blog/infinite_scroll/enable_category_listing';
    const EWAVE_BLOG_INFINITE_POST_ENABLED = 'ewave_blog/infinite_scroll/enable_post_listing';
    const EWAVE_BLOG_INFINITE_POST_LIMIT = 'ewave_blog/general/postonlist';

    const TYPE_POST = 'post';
    const TYPE_CATEGORY = 'category';

    /**
     * @param string $type
     * @return bool
     */
    public function isEnabled($type)
    {
        if ($type == self::TYPE_POST) {
            return $this->isPostEnabled();
        } elseif ($type == self::TYPE_CATEGORY) {
            return $this->isCategoryEnabled();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCategoryEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::EWAVE_BLOG_INFINITE_CATEGORY_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return bool
     */
    public function isPostEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::EWAVE_BLOG_INFINITE_POST_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getActionType()
    {
        return \Ewave\InfiniteScroll\Model\Config\Source\Action::LOAD_ACTION_CLICK;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->getConfigValue(self::EWAVE_BLOG_INFINITE_POST_LIMIT);
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORES);
    }
}
