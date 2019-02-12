<?php
namespace Ewave\Navigation\Model;

class Cache
{
    /**
     * Application config object
     *
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\PageCache\Model\Cache\Type
     */
    private $fullPageCache;

    /**
     * Cache constructor.
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\PageCache\Model\Cache\Type $cache
     */
    public function __construct(\Magento\PageCache\Model\Config $config, \Magento\PageCache\Model\Cache\Type $cache)
    {
        $this->config = $config;
        $this->fullPageCache = $cache;
    }

    /**
     * If fpc is enabled and tag exist
     *
     * @param array $tags
     * @return void
     */
    public function execute(array $tags = [])
    {
        if (empty($tags)) {
            return;
        }
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN && $this->config->isEnabled()) {
            $this->fullPageCache->clean(
                \Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                array_merge(array_unique($tags), [\Ewave\Navigation\Model\Menu::CACHE_TAG])
            );
        }
    }
}
