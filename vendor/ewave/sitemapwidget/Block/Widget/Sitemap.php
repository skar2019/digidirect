<?php
namespace Ewave\SitemapWidget\Block\Widget;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\Store;
use Magento\Widget\Block\BlockInterface;
use Ewave\SitemapWidget\Api\AdditionalEntityInterface;
use Ewave\SitemapWidget\Model\EntityCollectorPool;

class Sitemap extends Template implements BlockInterface
{
    /**
     * Should be a prefix of access widget config
     */
    const ACCESS_KEY_PREFIX = 'show';

    /**
     * Simple renderer for additional entities
     */
    const ITEM_RENDERER = 'Ewave\SitemapWidget\Block\Widget\AdditionalEntity\Renderer';

    /**
     * @var string
     */
    protected $_template = 'widget/sitemap.phtml';

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $cmsPageRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var EntityCollectorPool
     */
    protected $entityCollectorPool;

    /**
     * Sitemap constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Cms\Api\PageRepositoryInterface $cmsPageRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param EntityCollectorPool $entityCollectorPool
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Cms\Api\PageRepositoryInterface $cmsPageRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        EntityCollectorPool $entityCollectorPool,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productVisibility = $productVisibility;
        $this->cmsPageRepository = $cmsPageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->entityCollectorPool = $entityCollectorPool;
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $widgetTemplate = $this->getData('widget_template');
        $customTemplate = trim($this->getData('custom_template'));
        if ($customTemplate && $widgetTemplate == 'custom') {
            $templatePath = $this->getData('custom_template') . '.phtml';
            $params = ['module' => $this->getModuleName()];
            $area = $this->getArea();
            if ($area) {
                $params['area'] = $area;
            }
            $path = $this->resolver->getTemplateFileName(
                'Ewave_SitemapWidget::widget/custom/' . $templatePath,
                $params
            );
            if ($path) {
                $this->setTemplate('Ewave_SitemapWidget::widget/custom/' . $templatePath);
            }
        }

        return parent::_toHtml();
    }

    /**
     * Get products data for displaying
     *
     * @return array
     */
    public function getProducts()
    {
        if (!$this->getShowProducts()) {
            return [];
        }
        $visibility = $this->productVisibility->getVisibleInSiteIds();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ProductInterface::VISIBILITY, $visibility, 'in')
            ->create();
        $entities = $this->productRepository->getList(
            $searchCriteria
        )->getItems();
        $data = [];
        foreach ($entities as $product) {
            $data[] = [
                'url'  => $product->getProductUrl(),
                'name' => $product->getName(),
            ];
        }

        return $data;
    }

    /**
     * Get Categories
     *
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategories()
    {
        if (!$this->getShowCategories()) {
            return [];
        }
        $categoryId = $this->_storeManager->getStore()->getRootCategoryId();
        $category = $this->categoryRepository->get($categoryId);
        $data = $this->getTree($category);

        return $data;
    }

    /**
     * Get category tree
     *
     * @param null $parenNodeCategory
     * @return array|mixed
     */
    public function getTree($parenNodeCategory = null)
    {
        $rootArray = $this->getNode($parenNodeCategory);
        $tree = isset($rootArray['children']) ? $rootArray['children'] : [];

        return $tree;
    }

    /**
     * Get category tree node
     * 
     * @param \Magento\Catalog\Model\Category $category
     * @param int $level
     * @return array
     */
    public function getNode($category, $level = 0)
    {
        $item = [];
        $item['url'] = $category->getUrl();
        $item['name'] = $category->getName();
        if ((int)$category->getChildrenCount() > 0) {
            $item['children'] = [];
        }
        if ($category->hasChildren()) {
            $item['children'] = [];
            foreach ($category->getChildrenCategories() as $child) {
                $item['children'][] = $this->getNode($child, $level + 1);
            }
        }

        return $item;
    }

    /**
     * Prepare html of category tree
     *
     * @param array $categories
     * @return string
     */
    public function categoriesTreeToHtml($categories)
    {
        $html = '';
        if (!empty($categories)) {
            $html .= '<ul>';
            foreach ($categories as $category) {
                $html .= '<li>';
                $html .= '<a href="' . $category['url'] . '">' . $category['name'] . '</a>';
                if (isset($category['children'])) {
                    $html .= $this->categoriesTreeToHtml($category['children']);
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * Get CMS Pages
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCMSPages()
    {
        $identifierFilter = array_filter(explode(',', $this->getShowPages()));

        if (empty($identifierFilter)) {
            return [];
        }

        $storeFilter = [
            Store::DEFAULT_STORE_ID,
            $this->_storeManager->getStore()->getId()
        ];

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Store::STORE_ID, $storeFilter)
            ->addFilter(PageInterface::IS_ACTIVE, 1)
            ->addFilter(PageInterface::IDENTIFIER, $identifierFilter, 'in')
            ->create();

        $entities = $this->cmsPageRepository->getList(
            $searchCriteria
        )->getItems();

        $data = [];
        foreach ($entities as $item) {
            $data[] = [
                'url'   => $this->getUrl($item->getIdentifier()),
                'title' => $item->getTitle(),
            ];
        }

        return $data;
    }

    /**
     * @param array $accessKeys
     * @return array
     */
    public function collectAdditionalEntities(array $accessKeys)
    {
        $data = [];
        $this->entityCollectorPool->collect($data, $accessKeys);
        return $data;
    }

    /**
     * Entry point for after plugins
     * @param array $accessKeys
     * @return array
     */
    public function getAdditionalEntities()
    {
        $accessKeys = $this->getAccessKeys();
        return $this->collectAdditionalEntities($accessKeys);
    }

    /**
     * @return array
     */
    protected function getAccessKeys()
    {
        $accessKeys = [];
        foreach ($this->getData() as $key => $value) {
            if (strpos($key, self::ACCESS_KEY_PREFIX, 0) !== false) {
                $accessKeys[$key] = $value;
            }
        }
        return $accessKeys;
    }

    /**
     * @param mixed $renderer
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getTreeRenderer($renderer)
    {
        if (!$renderer) {
            return $this->getLayout()->createBlock(self::ITEM_RENDERER);
        }
        return $this->getLayout()->createBlock($renderer);
    }

    /**
     * @param AdditionalEntityInterface $additionalEntity
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function entitiesTreeToHtml(AdditionalEntityInterface $additionalEntity)
    {
        $entities = $additionalEntity->getEntities();
        $renderer = $additionalEntity->getRenderer();
        return $this->getTreeRenderer($renderer)->render($entities);
    }
}