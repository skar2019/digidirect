<?php

namespace Ewave\CollectAbstractEntityMSI\Block\Product\View;

use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Ewave\CollectAbstractEntity\Model\CollectPlaceRepository;
use Ewave\CollectAbstractEntityMSI\Model\MsiAvailability;
use Ewave\CollectAbstractEntityMSI\Model\ProductTypeHandlerPool;
use Ewave\CollectAbstractEntity\Api\Data\CollectPlaceInterface;
use Ewave\CollectAbstractEntityMSI\Helper\ProductType as ProductTypeHelper;

/**
 * Class Availability
 * @package Ewave\CollectAbstractEntityMSI\Block\Product\View
 */
class Availability extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectPlaceRepository
     */
    protected $collectPlaceRepository;

    /**
     * @var MsiAvailability
     */
    protected $msiAvailability;

    /**
     * @var ProductTypeHandlerPool
     */
    protected $handlerPool;

    /**
     * @var ProductTypeHelper
     */
    protected $productTypeHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Availability constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CollectPlaceRepository $collectPlaceRepository
     * @param MsiAvailability $msiAvailability
     * @param ProductTypeHandlerPool $handlerPool
     * @param ProductTypeHelper $productTypeHelper
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectPlaceRepository $collectPlaceRepository,
        MsiAvailability $msiAvailability,
        ProductTypeHandlerPool $handlerPool,
        ProductTypeHelper $productTypeHelper,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->collectPlaceRepository = $collectPlaceRepository;
        $this->msiAvailability = $msiAvailability;
        $this->handlerPool = $handlerPool;
        $this->productTypeHelper = $productTypeHelper;
        $this->coreRegistry = $registry;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get URL for ajax call
     *
     * @return string
     */
    public function getAvailabilityAjaxUrl()
    {
        return $this->getUrl(
            'collectplace/availability/get',
            [
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        $product = $this->coreRegistry->registry('product');
        if (!$product) {
            $productId = $this->_request->getParam(ProductTypeHelper::PRODUCT_ID);
            $product = $this->productRepository->getById($productId);
        }
        return $product;
    }

    /**
     * @return array
     */
    public function getAvailability()
    {
        $availability = [];
        $productOptions = $this->resolveConfiguration();
        $skus = $this->handlerPool->execute($this->getProduct(), $productOptions);
        if (!empty($skus)) {
            $places = $this->collectPlaceRepository->getAll();
            /** @var CollectPlaceInterface $place */
            foreach ($places as $place) {
                $sourceItems = $this->msiAvailability->prepareSourceDataForItems(array_keys($skus));
                $available = $this->msiAvailability->isPlaceAvailable($place, $sourceItems, $skus);
                $availability[$place->getName()] = $available;
            }
        }
        return $availability;
    }

    /**
     * @return array|mixed
     */
    protected function resolveConfiguration()
    {
        return $this->productTypeHelper->getProductOptions(
            $this->getRequest(),
            $this->getProduct()
        );
    }
}
