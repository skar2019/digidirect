<?php

namespace Marketplacer\Brand\Controller\Brand;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Store\Model\StoreManagerInterface;
use Marketplacer\Brand\Api\BrandRepositoryInterface;
use Marketplacer\Brand\Api\Data\BrandInterface;
use Marketplacer\Brand\Helper\Config as ConfigHelper;
use Marketplacer\Brand\Model\Layer\Brand;

/**
 * Class View
 * @package Marketplacer\Brand\Controller\Brand
 */
class View extends Action
{
    const LAYER_NAME = Brand::LAYER_NAME;

    /**
     * Core registry
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Catalog session
     * @var Session
     */
    protected $catalogSession;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var BrandRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $catalogSession
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param BrandRepositoryInterface $brandRepository
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        Context $context,
        Session $catalogSession,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        BrandRepositoryInterface $brandRepository,
        ConfigHelper $configHelper
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->catalogSession = $catalogSession;
        $this->coreRegistry = $coreRegistry;
        $this->layerResolver = $layerResolver;
        $this->categoryRepository = $categoryRepository;
        $this->brandRepository = $brandRepository;
        $this->configHelper = $configHelper;
    }

    /**
     * @return false|BrandInterface
     * @throws NoSuchEntityException
     */
    protected function _initBrand()
    {
        $brandId = (int)$this->getRequest()->getParam('brand_id');
        $storeId = $this->storeManager->getStore()->getId();
        try {
            $brand = $this->brandRepository->getById($brandId, $storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        if (!$brand->isEnabled()) {
            return false;
        }
        $this->coreRegistry->register('current_brand', $brand);
        return $brand;
    }

    /**
     * @return false|CategoryInterface
     * @throws NoSuchEntityException
     */
    protected function _initCategory()
    {
        $store = $this->storeManager->getStore();
        $categoryId = $store->getRootCategoryId();
        if (!$categoryId) {
            return false;
        }

        try {
            $category = $this->categoryRepository->get($categoryId, $store->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $this->catalogSession->setLastVisitedCategoryId($category->getId());
        $this->coreRegistry->register('current_category', $category);
        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                ['category' => $category, 'controller_action' => $this]
            );
        } catch (LocalizedException $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return false;
        }

        return $category;
    }

    /**
     * @return ResponseInterface|Forward|ResultInterface|Page
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        if (!$this->configHelper->isEnabledOnStorefront()) {
            $this->_redirect('noroute');
        }

        $brand = $this->_initBrand();
        $category = $this->_initCategory();
        if ($category && $brand) {
            /** @var Page $page */
            $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $this->layerResolver->create(self::LAYER_NAME);

            $hasChildren = $category->hasChildren();
            if ($category->getIsAnchor()) {
                $type = $hasChildren ? 'layered' : 'layered_without_children';
            } else {
                $type = $hasChildren ? 'default' : 'default_without_children';
            }

            if (!$hasChildren) {
                $parentType = strtok($type, '_');
                $page->addPageLayoutHandles(['type' => $parentType]);
            }
            $page->addPageLayoutHandles(['type' => $type, 'id' => $category->getId()]);
            $this->_eventManager->dispatch(
                'controller_action_layout_render_before_brand_view',
                ['brand' => $brand]
            );

            return $page;
        }

        return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
    }
}
