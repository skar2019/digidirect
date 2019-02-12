<?php

namespace Ewave\Navigation\Model;

use Ewave\Navigation\Api\Data\MenuItemInformationInterface;
use Ewave\Navigation\Api\Data\MenuItemInterface;
use Ewave\Navigation\Model\Menu\CustomOptions;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Ewave\Navigation\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Ewave\Navigation\Model\ResourceModel\Menu as MenuResourceModel;

/**
 * @property \Ewave\Navigation\Model\ResourceModel\Menu $_resource
 * TODO: create RoleResolver class with isAllowed = true. Move admingws dependency to add on
 * TODO: it will be available to use the same code in community and enterprise versions
 */
class Menu extends AbstractModel implements IdentityInterface, MenuItemInterface, MenuItemInformationInterface
{
    const MENU_ITEM_LEVEL = 'level';
    const MENU_ITEM_STATUS = 'status';
    const MENU_ITEM_IS_LOGGED_IN = 'is_logged_in';
    const MENU_ITEM_POSITION = 'position';
    const MENU_ITEM_PARENT_MENU_ITEM_ID = 'parent_menu_item_id';
    const MENU_ITEM_ID = 'id';
    const SORT_ORDER_ASC = 'ASC';
    const MENU_ITEM_STATUS_ENABLED = 1;
    const MENU_ITEM_FOR_LOGGED_IN = 1;
    const MENU_ITEM_FOR_NOT_LOGGED_IN = 0;
    const CACHE_TAG = 'ewave_navigation_menu_item';
    const MENU_ITEM_FOR_ALL_USERS = 2;
    const CUSTOM_OPTION_CLASS_NAME = 'class';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Default data
     *
     * @var []
     */
    protected $dataDefault = [];

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $typeModel = [];

    /**
     * @var Menu\Type\Pool
     */
    protected $pool;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var UrlComparator
     */
    protected $urlComparator;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var null|CustomOptions
     */
    protected $customOptionsProcessor;

    /**
     * Menu constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Menu\Type\Pool $pool
     * @param FilterProvider $filterProvider
     * @param MenuResourceModel $resource
     * @param Acl|null $acl
     * @param UrlComparator|null $urlComparator
     * @param CustomOptions|null $customOptions
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        CategoryCollectionFactory $categoryCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Menu\Type\Pool $pool,
        FilterProvider $filterProvider,
        MenuResourceModel $resource,
        Acl $acl = null,
        UrlComparator $urlComparator = null,
        CustomOptions $customOptions = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->pool = $pool;
        $this->filterProvider = $filterProvider;

        /**
         * It is unknown if add-ons extend this class or not. BC code
         */
        if (null === $urlComparator) {
            $urlComparator = $this->createInstance(UrlComparator::class);
        }

        if (null === $acl) {
            $acl = $this->createInstance(Acl::class);
        }

        if (null === $customOptions) {
            $customOptions = $this->createInstance(CustomOptions::class);
        }

        $this->acl = $acl;
        $this->urlComparator = $urlComparator;
        $this->customOptionsProcessor = $customOptions;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ewave\Navigation\Model\ResourceModel\Menu');
    }

    /**
     * @param array $data
     * @return void
     */
    public function addDataDefault(array $data)
    {
        $this->dataDefault = array_replace($this->dataDefault, $data);
    }

    /**
     * @return []
     */
    public function getDataDefault()
    {
        return $this->dataDefault;
    }

    /**
     * Validate data
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param int $store
     * @param int $id
     * @return []
     */
    public function validate($request, $store, $id)
    {
        $errors = $this->_resource->validate($request, $store, $id);
        $errors = array_merge_recursive($errors, $this->isValidLink($request->getParam('link', null)));
        return $errors;
    }

    /**
     * find class attribute
     *
     * @return string
     */
    protected function getFrontendClass()
    {
        $customOptions = $this->unserializeCustomOptions();
        $classString = '';
        foreach ($customOptions as $key => $optionConfig) {
            if (!isset($optionConfig['option_name'])) {
                continue;
            }
            if (trim($optionConfig['option_name']) == self::CUSTOM_OPTION_CLASS_NAME) {
                $classString .= $optionConfig['value'] . ' ';
            }
        }

        return $classString;
    }

    /**
     * @param string $optionKey
     * @return mixed|null
     */
    public function getCustomOption($optionKey)
    {
        $customOptions = $this->unserializeCustomOptions();
        if (is_array($customOptions)) {
            return $customOptions[$optionKey] ?? null;
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getStyle()
    {
        $customOptions = $this->unserializeCustomOptions();
        $styles = [];
        foreach ($customOptions as $key => $optionConfig) {
            if (!isset($optionConfig['option_name'])) {
                continue;
            }
            if (trim($optionConfig['option_name']) != self::CUSTOM_OPTION_CLASS_NAME) {
                $styles[$key] = trim($optionConfig['option_name']) . ':' . $optionConfig['value'];
            }
        }

        return implode('; ', $styles);
    }

    /**
     *
     *
     * @return []
     */
    public function unserializeCustomOptions()
    {
        if (!$this->getCustomOptions()) {
            return [];
        }

        return $this->customOptionsProcessor->unserialize($this->getCustomOptions());
    }

    /**
     * Serialize options
     *
     * @return string
     */
    public function serializeCustomOptions()
    {
        $customOptions = $this->getCustomOptions() ?? [];
        $customOptionsSerialized = $this->customOptionsProcessor->serialize($customOptions);
        $this->setCustomOptions($customOptionsSerialized);
        return $this;
    }

    /**
     * Get menu item data by type
     *
     * @return array
     */
    public function getMenuData()
    {
        $content = $this->getContent() ? $this->filterProvider->getPageFilter()->filter($this->getContent()) : '';
        $data = [
            'title' => $this->getTitle(),
            'identifier' => 'menu-node' . $this->getId(),
            'custom_options' => $this->unserializeCustomOptions(),
            'position' => $this->getPosition(),
            'menu_item_type' => $this->getTypeCode(),
            'content' => $content,
            'frontend_class' => $this->getFrontendClass(),
            'style' => $this->getStyle(),
            'entity_id' => $this->getId()
        ];
        $data = array_merge($data, $this->_getTypeInstance()->setItem($this)->getMenuData());
        return $data;
    }

    /**
     * Gwt children IDs
     *
     * @param int $storeId
     * @return []
     */
    public function hasChildren($storeId)
    {
        return $this->_resource->getChildrenIds($this, $storeId);
    }

    /**
     * Get all active categories
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getActiveCategories()
    {
        $categories = [];

        /**
         * @var $collection CategoryCollection
         */
        $collection = $this->categoryCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => true]);
        foreach ($collection as $category) {
            $categories[$category->getId()] = $category;
        }
        return $categories;
    }

    /**
     * @return \Ewave\Navigation\Model\Menu\Type\MenuDataInterface
     */
    protected function _getTypeInstance()
    {
        if (!isset($this->typeModel[$this->getTypeCode()])) {
            $instanceString = $this->_data['type_instance'][$this->getTypeCode()];
            $instance = $this->pool->get($instanceString);
            if (!($instance instanceof \Ewave\Navigation\Model\Menu\Type\MenuDataInterface)) {
                throw new \InvalidArgumentException(
                    __('Menu type object must implement \Ewave\Navigation\Model\Menu\Type\MenuDataInterface')
                );
            }
            $this->typeModel[$this->getTypeCode()] = $instance;
        }
        return $this->typeModel[$this->getTypeCode()];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_getTypeInstance()->setItem($this)->getUrl();
    }

    /**
     * Check if link is valid - link is custom link or link with placeholder
     *
     * @param string $link
     * @return []
     */
    public function isValidLink($link)
    {
        $errors = [];
        if (!$link) {
            return $errors;
        }

        $instance = $this->pool->get($this->_data['type_instance']['custom_link']);
        $this->setData('link', $link);
        $instance->setItem($this);
        return $instance->isValidLink();
    }

    /**
     * Check menu item availability
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->_getTypeInstance()->setItem($this)->isAvailable();
    }

    /**
     * Check if menu item link is active
     *
     * @param string $currentUrl
     * @param string $itemUrl
     * @return bool
     */
    public function isActive($currentUrl, $itemUrl)
    {
        return $this->urlComparator->isActive($currentUrl, $itemUrl);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        $identities = [
            MenuCache::CACHE_TAG . '_' . $this->getId(),
        ];
        if (!$this->getId() || $this->hasDataChanges() || $this->isDeleted()) {
            $identities[] = MenuCache::CACHE_TAG;
        }
        return $identities;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->acl->isReadOnly($this);
    }

    /**
     * @return MenuResourceModel|AbstractResource|\Magento\Framework\Model\ResourceModel\Db\AbstractDb|mixed
     */
    protected function _getResource()
    {
        if (!($this->_resource instanceof MenuResourceModel)) {
            $this->_resource = $this->createInstance(MenuResourceModel::class);
        }

        return $this->_resource;
    }

    /**
     * @param string $className
     * @return mixed
     */
    protected function createInstance($className)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get($className);
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setMenuItemCode($code)
    {
        $this->setData(self::MENU_ITEM_CODE, $code);
        return $this;
    }

    /**
     * @return string
     */
    public function getMenuItemCode()
    {
        return $this->getData(self::MENU_ITEM_CODE);
    }

    /**
     * @param int $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->setData(self::TYPE_ID, $typeId);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData(self::TITLE, $title);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->setData(self::POSITION, $position);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->setData(self::CONTENT, $content);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentMenuItemId()
    {
        return $this->getData(self::MENU_ITEM_PARENT_MENU_ITEM_ID);
    }

    /**
     * @param int $parentMenuItemId
     * @return $this
     */
    public function setParentMenuItemId($parentMenuItemId)
    {
        $this->setData(self::PARENT_MENU_ITEM_ID, $parentMenuItemId);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomOptions()
    {
        return $this->getData(self::CUSTOM_OPTIONS);
    }

    /**
     * @param $customOptions
     * @return $this
     */
    public function setCustomOptions($customOptions)
    {
        $this->setData(self::CUSTOM_OPTIONS, $customOptions);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->getData(self::LEVEL);
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->setData(self::LEVEL);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->setData(self::PATH, $path);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsLoggedIn()
    {
        return $this->getData(self::MENU_ITEM_IS_LOGGED_IN);
    }

    /**
     * @param bool|int $isLoggedIn
     * @return $this
     */
    public function setIsLoggedIn($isLoggedIn)
    {
        $this->setData(self::MENU_ITEM_IS_LOGGED_IN, $isLoggedIn);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMenuId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @param int|string $menuId
     * @return $this
     */
    public function setMenuId($menuId)
    {
        $this->setData(self::ID, $menuId);
        return $this;
    }
}
