<?php

namespace Digidirect\Navigation\Model\Frontend;

/**
 * Introduced to be able to generate cache key depends on for example customer segment, b2b segment etc
 *
 * To use it you need to configure pool
 *
 * [
 *     'customer_segment' => [
 *         'disabled' => true,
 *         'object' => new SomeObject()
 *    ],
 *     'vepe' => [
 *         'object' => new Vepe()
 *     ]
 * ]
 *
 * @since 1.3.0
 */
class CacheKeyGenerator implements CacheKeyGeneratorInterface
{
    const DISABLED = 'disabled';
    const OBJECT = 'object';

    /**
     * @var array
     */
    protected $pool;

    /**
     * @var CacheKeyGeneratorInterface[]
     */
    protected $preparedGenerators = [];

    /**
     * CacheKeyGenerator constructor.
     * @param array $pool
     */
    public function __construct(array $pool = [])
    {
        $this->pool = $pool;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $cacheString = '';
        if (empty($this->preparedGenerators)) {
            if (!empty($this->pool)) {
                foreach ($this->pool as $key => $generatorConfiguration) {
                    $disabled = $generatorConfiguration[self::DISABLED] ?? false;
                    $object = $generatorConfiguration[self::OBJECT] ?? false;
                    if (!$disabled && $object && $object instanceof CacheKeyGeneratorInterface) {
                        $this->preparedGenerators[] = $object;
                    }
                }
            }
        }

        foreach ($this->preparedGenerators as $generator) {
            $cacheString .= $generator->generate();
        }

        return $cacheString;
    }
}
