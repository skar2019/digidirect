<?php

namespace Digidirect\Navigation\Model\Frontend;

/**
 * Introduced to be able to generate cache key depends on for example customer segment, b2b segment etc
 * @since 1.3.0
 */
interface CacheKeyGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
