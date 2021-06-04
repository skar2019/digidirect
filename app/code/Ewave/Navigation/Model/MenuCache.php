<?php

namespace Ewave\Navigation\Model;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\App\Cache\Type\FrontendPool;

/**
 * @api
 * @since 1.3.0
 */
class MenuCache extends TagScope
{
    const TYPE_IDENTIFIER = 'ewave_navigation';
    const CACHE_TAG = 'EWAVE_NAVIGATION_GENERAL_CACHE_TAG';

    /**
     * This parameter is needed to make this cache type dependent on cache state.
     *
     * @var StateInterface
     */
    protected $state;

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(FrontendPool $cacheFrontendPool, StateInterface $state)
    {
        $this->state = $state;
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }

    /**
     * Saves cache only if it is enabled
     *
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param null $lifeTime
     * @return bool
     */
    public function save($data, $identifier, array $tags = [], $lifeTime = null)
    {
        if (!$this->state->isEnabled(self::TYPE_IDENTIFIER)) {
            return false;
        }
        return parent::save($data, $identifier, $tags, $lifeTime);
    }

    /**
     * Loads cache independently on its status
     *
     * @param string $identifier
     * @return bool|string
     */
    public function load($identifier)
    {
        return parent::load($identifier);
    }
}
