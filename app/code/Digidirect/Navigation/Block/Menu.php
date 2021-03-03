<?php

namespace Digidirect\Navigation\Block;

use Digidirect\Navigation\Api\MenuRepositoryInterface;
use Digidirect\Navigation\Component\Json;
use Digidirect\Navigation\Model\Frontend\CacheKeyGeneratorInterface;
use Digidirect\Navigation\Model\Frontend\StoreResolver;
use Digidirect\Navigation\Model\MenuCache;
use Digidirect\Navigation\Model\UrlComparator;
use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * The main entry point on frontend
 * By default it returns object json decode
 * But it is possible to do it without json encoding/decoding
 *
 * TODO: refactor to use \Digidirect\Navigation\Model\Frontend\MenuSetData as it's done in widget
 */
class Menu extends Template implements DataObject\IdentityInterface, MenuBlockInterface
{
    const CHILDREN_KEY = 'children';

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * @var null|UrlComparator
     */
    protected $urlComparator;

    /**
     * @var null|string
     */
    protected $currentUrl = null;

    /**
     * @var MenuCache
     */
    protected $menuCache;

    /**
     * @var null|string
     */
    protected $currentStoreCode;

    /**
     * @var CacheKeyGeneratorInterface
     * @since 1.3.0
     */
    protected $cacheKeyGenerator;

    /**
     * @var array
     */
    protected $identities = [];

    /**
     * @var StoreResolver
     */
    protected $storeResolver;

    /**
     * @var Json
     */
    protected $jsonComponent;

    /**
     * Menu constructor.
     * @param Template\Context $context
     * @param HttpContext $httpContext
     * @param MenuRepositoryInterface $menuRepository
     * @param UrlComparator $comparator
     * @param MenuCache $menuCache
     * @param CacheKeyGeneratorInterface $cacheKeyGenerator
     * @param StoreResolver $storeResolver
     * @param Json $jsonComponent
     * @param string $template
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HttpContext $httpContext,
        MenuRepositoryInterface $menuRepository,
        UrlComparator $comparator,
        MenuCache $menuCache,
        CacheKeyGeneratorInterface $cacheKeyGenerator,
        StoreResolver $storeResolver,
        Json $jsonComponent,
        $template = 'menu.phtml',
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonComponent = $jsonComponent;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
        $this->storeResolver = $storeResolver;
        $this->urlComparator = $comparator;
        $this->_template = $template;
        $this->httpContext = $httpContext;
        $this->menuRepository = $menuRepository;
        $this->menuCache = $menuCache;
    }

    /**
     * Get menu JSON string
     *
     * @return string
     */
    public function getMenuJson()
    {
        return $this->_getMenuCollection()->toJson();
    }

    /**
     * Get cache key info
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCacheKeyInfo()
    {
        return [
            'BLOCK_TPL',
            $this->getCurrentStoreCode(),
            $this->getTemplateFile(),
            'base_url' => $this->getBaseUrl(),
            'template' => $this->getTemplate(),
            'is_logged_in' => $this->_isLoggedIn(),
            'set_code' => $this->getData('set_code'),
            'url' => $this->getCurrentUrl(),
        ];
    }

    /**
     * @return null|string
     */
    protected function getCurrentStoreCode()
    {
        return $this->storeResolver->getStoreCode();
    }

    /**
     * @return bool
     */
    protected function _isLoggedIn()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH) == true;
    }

    /**
     * Get menu collection prepared data
     *
     * @return DataObject
     */
    protected function _getMenuCollection()
    {
        if (!$setCode = $this->getData('set_code')) {
            return new DataObject([]);
        }

        /**
         * Get saved menu set array from cache and if it exists change active links
         */
        $menuInCache = $this->menuCache->load($this->getExternalCacheKey());
        if ($menuInCache) {
            $menuInCache = $this->jsonComponent->decode($menuInCache);
            $menuInCache = $this->modifyExistingMenu($menuInCache);
            return new DataObject($menuInCache);
        }

        $collection = $this->menuRepository->getListBySetId($setCode, $this->_isLoggedIn());
        return $this->_makeOutputArray($collection);
    }

    /**
     * @return bool|int|null
     */
    public function getCacheLifetime()
    {
        $cacheLifeTime = parent::getCacheLifetime();
        if (!$cacheLifeTime) {
            $cacheLifeTime = 86400;
        }
        return $cacheLifeTime;
    }

    /**
     * Check if category in menu is disabled. Suitable for all menu items types
     *
     * @param \Digidirect\Navigation\Model\Menu $menuItem
     * @return bool
     * @deprecated Use menu item method isAvailable directly
     */
    protected function _canRender($menuItem)
    {
        return $menuItem->isAvailable();
    }

    /**
     * Change active attribute for each link
     *
     * @param array $menu
     * @return array
     */
    protected function modifyExistingMenu(array $menu)
    {
        foreach ($menu as $key => $item) {
            $children = $item[self::CHILDREN_KEY] ?? [];
            $this->identities[] = MenuCache::CACHE_TAG . '_' . ($item['entity_id'] ?? null);
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
     * @return UrlComparator|null
     */
    protected function getUrlComparator()
    {
        return $this->urlComparator;
    }

    /**
     * @param \Digidirect\Navigation\Model\ResourceModel\Menu\Collection $collection
     * @return \Magento\Framework\DataObject
     */
    protected function _makeOutputArray($collection)
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
            if (!isset($menuByIdArray[$menuItem->getParentMenuItemId()])
                || in_array($menuItem->getId(), $cantRenderItems) || !$this->_canRender($menuItem)
            ) {
                $cantRenderItems[] = $menuItem->getId();
                continue;
            }

            $this->identities[] = MenuCache::CACHE_TAG . '_' . $menuItem->getId();
            $parentItemId = $menuItem->getParentMenuItemId();
            foreach ([$menuItem->getId(), $parentItemId] as $menuItemId) {
                if (!isset($menuByIdArray[$menuItemId])) {
                    $menuByIdArray[$menuItemId] = ['value' => $menuItemId];
                }
            }

            $menuByIdArray[$menuItem->getId()] = $this->_getMenuItemAsArray($menuItem);

            $menuByIdArray[$parentItemId][self::CHILDREN_KEY][] = &$menuByIdArray[$menuItem->getId()];
        }

        $menuArray = $menuByIdArray[$globalId][self::CHILDREN_KEY];

        /**
         * Save external cache to avoid extra db queries
         */
        $this->menuCache->save(
            $this->jsonComponent->encode($menuArray),
            $this->getExternalCacheKey(),
            [$this->getExternalCacheKey(), MenuCache::CACHE_TAG],
            $this->getCacheLifetime()
        );

        $menuObject = new DataObject($menuArray);

        $this->_eventManager->dispatch(
            'digidirect_navigation_prepare_menu_after',
            [
                'set_id' => $this->getData('set_code'),
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
    protected function _getMenuItemAsArray($menuItem)
    {
        $menuItemData = $menuItem->getMenuData();
        $url = $menuItem->getUrl();
        $menuItemData['url'] = $url;
        $menuItemData['is_active'] = $menuItem->isActive($this->getCurrentUrl(), $url);
        return $menuItemData;
    }

    /**
     * @return string
     */
    public function getMenuVar()
    {
        return preg_replace('/-@|\./', '', $this->getNameInLayout());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * Method is public for making cache key dynamic.
     * If you want different customer segments/groups/b2b entities to see different menu items
     * just add plugin in add-on
     *
     * @return string
     */
    public function getExternalCacheKey()
    {
        return $this->getData('set_code') .
            $this->getCurrentStoreCode() .
            $this->_isLoggedIn() .
            $this->cacheKeyGenerator->generate();
    }

    /**
     * @return string
     */
    protected function getCurrentUrl()
    {
        if (null === $this->currentUrl) {
            $this->currentUrl = $this->_urlBuilder->getCurrentUrl();
        }

        return $this->currentUrl;
    }

    /**
     * Added to simplify template(avoid using objects syntax)
     *
     * @return string
     */
    public function getMenuJsonArray()
    {
        return $this->jsonComponent->encode($this->_getMenuCollection()->toArray());
    }

    /**
     * @return mixed
     */
    public function getMenuAsArray()
    {
        return $this->_getMenuCollection()->toArray();
    }

    /**
     * @return string
     */
    public function getMenuJsonObject()
    {
        return $this->getMenuJson();
    }

    /**
     * Can be used if needed in template
     *
     * @param string $url
     * @param null $param
     * @return mixed
     */
    public function parseUrl($url, $param = null)
    {
        return $this->getUrlComparator()->getParseUrlResultByUrl($url, $param);
    }
}
