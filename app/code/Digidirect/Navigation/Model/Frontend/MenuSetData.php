<?php

namespace Digidirect\Navigation\Model\Frontend;

use Digidirect\Navigation\Api\MenuRepositoryInterface;
use Digidirect\Navigation\Component\Json;
use Digidirect\Navigation\Model\MenuCache;
use Digidirect\Navigation\Model\UrlComparator;
use Digidirect\Navigation\Model\UrlComparatorFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\UrlInterface;
use Digidirect\Navigation\Model\ResourceModel\Menu\Collection;

/**
 * Class MenuSetData
 * Introduced after menu set widget has been developed.
 * It is needed to avoid inheritance and use composition.
 *
 * @since 1.3.0
 * @api
 *
 * This class after testing on widgets will be re-used by default class (Composition instead of inheritance)
 */
class MenuSetData
{
    const CHILDREN_KEY = 'children';

    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @var CacheKeyGeneratorInterface
     */
    protected $cacheKeyGenerator;

    /**
     * @var MenuCache
     */
    protected $menuCache;

    /**
     * @var UrlComparator|null
     */
    protected $urlComparator;

    /**
     * @var UrlComparatorFactory
     */
    protected $urlComparatorFactory;

    /**
     * @var null|string
     */
    protected $currentUrl = null;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected $menuBySetContext = [];

    /**
     * @var array
     */
    protected $identitiesBySetContext = [];

    /**
     * @var Json
     */
    protected $jsonComponent;

    /**
     * MenuSetData constructor.
     * @param MenuRepositoryInterface $menuRepository
     * @param CacheKeyGeneratorInterface $cacheKeyGenerator
     * @param MenuCache $cache
     * @param UrlComparatorFactory $comparatorFactory
     * @param UrlInterface $url
     * @param ManagerInterface $manager
     * @param Json $jsonComponent
     */
    public function __construct(
        MenuRepositoryInterface $menuRepository,
        CacheKeyGeneratorInterface $cacheKeyGenerator,
        MenuCache $cache,
        UrlComparatorFactory $comparatorFactory,
        UrlInterface $url,
        ManagerInterface $manager,
        Json $jsonComponent
    ) {
        $this->jsonComponent = $jsonComponent;
        $this->eventManager = $manager;
        $this->urlBuilder = $url;
        $this->urlComparatorFactory = $comparatorFactory;
        $this->menuCache = $cache;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
        $this->menuRepository = $menuRepository;
    }

    /**
     * @param CustomerSetInterface $customerSet
     * @return DataObject
     */
    public function getSetByCode(CustomerSetInterface $customerSet)
    {
        $cacheKey = $this->getExternalCacheKey($customerSet);

        if (isset($this->menuBySetContext[$cacheKey])) {
            return $this->menuBySetContext[$cacheKey];
        }

        /**
         * Get saved menu set array from cache and if it exists change active links
         */
        $menuInCache = $this->menuCache->load($cacheKey);
        if ($menuInCache) {
            $menuInCache = $this->jsonComponent->decode($menuInCache);
            $menuInCache = $this->modifyExistingMenu($menuInCache, $cacheKey);
            $this->menuBySetContext[$cacheKey] = new DataObject($menuInCache);
            return $this->menuBySetContext[$cacheKey];
        }
        $collection = $this->getFromRepository($customerSet);
        $this->menuBySetContext[$cacheKey] = $this->makeOutputArray($collection, $customerSet);
        return $this->menuBySetContext[$cacheKey];
    }

    /**
     * @param \Digidirect\Navigation\Model\ResourceModel\Menu\Collection $collection
     * @param CustomerSetInterface $customerSet
     * @return \Magento\Framework\DataObject
     */
    protected function makeOutputArray(Collection $collection, CustomerSetInterface $customerSet)
    {
        $globalId = 0;
        $menuByIdArray = [
            $globalId => [
                'title' => 'Global(highest level), not shown in store',
                'identifier' => 'menu-node' . $globalId,
                self::CHILDREN_KEY => [],
            ],
        ];
        $cantRenderItems = [];
        foreach ($collection as $menuItem) {
            /**
             * @var $menuItem \Digidirect\Navigation\Model\Menu
             */
            if (!isset($menuByIdArray[$menuItem->getParentMenuItemId()])
                || in_array($menuItem->getId(), $cantRenderItems) || !$menuItem->isAvailable()
            ) {
                $cantRenderItems[] = $menuItem->getId();
                continue;
            }

            $this->identitiesBySetContext[$this->getExternalCacheKey($customerSet)][]
                = MenuCache::CACHE_TAG . '_' . $menuItem->getId();
            $parentItemId = $menuItem->getParentMenuItemId();
            foreach ([$menuItem->getId(), $parentItemId] as $menuItemId) {
                if (!isset($menuByIdArray[$menuItemId])) {
                    $menuByIdArray[$menuItemId] = ['value' => $menuItemId];
                }
            }

            $menuByIdArray[$menuItem->getId()] = $this->getMenuItemAsArray($menuItem);

            $menuByIdArray[$parentItemId][self::CHILDREN_KEY][] = &$menuByIdArray[$menuItem->getId()];
        }

        $menuArray = $menuByIdArray[$globalId][self::CHILDREN_KEY];

        /**
         * Save external cache to avoid extra db queries
         */
        $this->menuCache->save(
            $this->jsonComponent->encode($menuArray),
            $this->getExternalCacheKey($customerSet),
            [$this->getExternalCacheKey($customerSet), MenuCache::CACHE_TAG]
        );

        $menuObject = new DataObject($menuArray);

        $this->eventManager->dispatch(
            'digidirect_navigation_prepare_menu_after',
            [
                'set_id' => $customerSet->getSetCode(),
                'menu' => $menuObject,
            ]
        );

        return $menuObject;
    }

    /**
     * Prepare menu item data
     *
     * @param \Digidirect\Navigation\Model\Menu $menuItem
     * @return array
     */
    protected function getMenuItemAsArray($menuItem)
    {
        $menuItemData = $menuItem->getMenuData();
        $url = $menuItem->getUrl();
        $menuItemData['url'] = $url;
        $menuItemData['is_active'] = $menuItem->isActive($this->getCurrentUrl(), $url);
        return $menuItemData;
    }

    /**
     * @param CustomerSetInterface $customerSet
     * @return \Digidirect\Navigation\Model\ResourceModel\Menu\Collection
     */
    public function getFromRepository(CustomerSetInterface $customerSet)
    {
        return $this->menuRepository->getListBySetId($customerSet->getSetCode(), $customerSet->isLoggedIn());
    }

    /**
     * Change active attribute for each link
     *
     * @param array $menu
     * @param string|null $cacheKey
     * @return array
     */
    protected function modifyExistingMenu(array $menu, $cacheKey = null)
    {
        foreach ($menu as $key => $item) {
            $children = $item[self::CHILDREN_KEY] ?? [];
            if ($cacheKey) {
                $this->identitiesBySetContext[$cacheKey][] = MenuCache::CACHE_TAG . '_' . ($item['entity_id'] ?? null);
            }
            $link = $item['url'] ?? '';
            $menu[$key]['is_active'] = $this->getUrlComparator()->isActive(
                $this->getCurrentUrl(),
                $link
            );
            if (!empty($children)) {
                $menu[$key][self::CHILDREN_KEY] = $this->modifyExistingMenu($children);
            } else {
                $menu[$key][self::CHILDREN_KEY] = [];
            }
        }
        return $menu;
    }

    /**
     * @param CustomerSetInterface $customerSet
     * @return string
     */
    public function getExternalCacheKey(CustomerSetInterface $customerSet)
    {
        return $customerSet->getSetCode() .
            $customerSet->getStoreCode() .
            $customerSet->isLoggedIn() .
            $this->cacheKeyGenerator->generate();
    }

    /**
     * @return UrlComparator|null
     */
    protected function getUrlComparator()
    {
        if (null === $this->urlComparator) {
            $this->urlComparator = $this->urlComparatorFactory->create();
        }
        return $this->urlComparator;
    }

    /**
     * @return string
     */
    protected function getCurrentUrl()
    {
        if (null === $this->currentUrl) {
            $this->currentUrl = $this->urlBuilder->getCurrentUrl();
        }

        return $this->currentUrl;
    }

    /**
     * @param CustomerSetInterface $customerSet
     * @return array|mixed
     */
    public function getIdentities(CustomerSetInterface $customerSet)
    {
        return $this->identitiesBySetContext[$this->getExternalCacheKey($customerSet)] ?? [];
    }
}
