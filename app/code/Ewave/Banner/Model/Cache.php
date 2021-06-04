<?php

namespace Ewave\Banner\Model;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\App\Cache\Type\FrontendPool;

/**
 * @api
 * @since 2.1.0
 */
class Cache extends TagScope
{
    const TYPE_IDENTIFIER = 'ewave_banner';
    const CACHE_TAG = 'EWAVE_BANNERS_CACHE';

    /**
     * This parameter is needed to make this cache type dependent on cache state.
     *
     * @var StateInterface
     */
    protected $state;

    /**
     * Cache constructor.
     * @param FrontendPool $cacheFrontendPool
     * @param StateInterface $state
     */
    public function __construct(FrontendPool $cacheFrontendPool, StateInterface $state)
    {
        $this->state = $state;
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }

    /**
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
}
