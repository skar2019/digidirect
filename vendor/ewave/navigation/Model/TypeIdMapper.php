<?php

namespace Ewave\Navigation\Model;

use Ewave\Navigation\Component\Json;
use Magento\Framework\App\CacheInterface;

/**
 * Due architecture fail we store type_id in database but configSwitcher is not dynamic.
 * When we delete and add same menu type it db id and switcher id will not match
 * This class is responsible for mapping
 *
 * @api
 * @since 1.3.0
 */
class TypeIdMapper
{
    const CACHE_KEY = 'ewave_navigation_type_mapping';

    const EXPECTED = 'expected';
    const REAL = 'real';

    /**
     * @var array
     */
    protected $expectedByReal = [];

    /**
     * @var array
     */
    protected $realByExpected = [];

    /**
     * @var array
     */
    protected $expectedConfiguration = [];

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var NotFilteredTypes
     */
    protected $typeCollection;

    /**
     * @var Json
     */
    protected $jsonComponent;

    /**
     * TypeIdMapper constructor.
     * @param CacheInterface $cache
     * @param NotFilteredTypes $notFilteredTypes
     * @param Json $jsonComponent
     */
    public function __construct(
        CacheInterface $cache,
        NotFilteredTypes $notFilteredTypes,
        Json $jsonComponent
    ) {
        $this->jsonComponent = $jsonComponent;
        $this->typeCollection = $notFilteredTypes;
        $this->cache = $cache;
    }

    /**
     * @param int $expectedId
     * @return mixed
     */
    public function getDbIdByExpectedId($expectedId)
    {
        if (empty($this->realByExpected[$expectedId])) {
            $this->prepareMapping();
        }

        return $this->realByExpected[$expectedId] ?? $expectedId;
    }

    /**
     * @param int $dbId
     * @return mixed
     */
    public function getExpectedIdByDbId($dbId)
    {
        if (empty($this->expectedByReal[$dbId])) {
            $this->prepareMapping();
        }

        return $this->expectedByReal[$dbId] ?? $dbId;
    }

    /**
     * @return void
     */
    protected function prepareMapping()
    {
        $mappingFromCache = $this->cache->load(self::CACHE_KEY);
        if (false === $mappingFromCache) {

            $collection = $this->typeCollection->getCollection();
            /**
             * @var $item Type
             */
            foreach ($collection as $item) {
                if (empty($this->expectedConfiguration)) {
                    $this->expectedConfiguration = $item->getConfiguration();
                }

                $expectedId = $this->expectedConfiguration[$item->getMenuTypeCode()] ?? null;
                if (!$expectedId) {
                    $expectedId = $item->getId();
                }

                $this->expectedByReal[$item->getId()] = $expectedId;
                $this->realByExpected[$expectedId] = $item->getId();
            }

            $this->cache->save(
                $this->jsonComponent->encode([
                    self::EXPECTED => $this->expectedByReal,
                    self::REAL => $this->realByExpected
                ]),
                self::CACHE_KEY,
                [MenuCache::CACHE_TAG]
            );
        } else {
            $mappingFromCache = $this->jsonComponent->decode($mappingFromCache);
            $this->realByExpected = $mappingFromCache[self::REAL] ?? [];
            $this->expectedByReal = $mappingFromCache[self::EXPECTED] ?? [];
        }
    }
}
