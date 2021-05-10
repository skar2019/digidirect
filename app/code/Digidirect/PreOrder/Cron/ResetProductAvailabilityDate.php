<?php

namespace Digidirect\PreOrder\Cron;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Config as CatalogConfig;
use Digidirect\PreOrder\Model\Source\Backorders as BackordersSource;
use Digidirect\PreOrder\Api\Data\ProductAttributeInterface;
use Digidirect\PreOrder\Helper\Config as HelperConfig;

/**
 * Class ResetProductAvailabilityDate
 */
class ResetProductAvailabilityDate
{
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * @var CatalogConfig
     */
    protected $catalogConfig;

    /**
     * OrderAutoValidation constructor.
     *
     * @param ProductCollectionFactory $productCollectionFactory
     * @param HelperConfig $helperConfig
     * @param CatalogConfig $catalogConfig
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        HelperConfig $helperConfig,
        CatalogConfig $catalogConfig
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperConfig = $helperConfig;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * Run Reset Product Availability Date cron
     *
     * @return void
     */
    public function execute()
    {
        $preordersEnabled = $this->helperConfig->preordersEnabled();
        $backordersAction = $this->helperConfig->getBackordersForAvailabilityDateConfig();
        if ($preordersEnabled && $backordersAction > BackordersSource::BACKORDERS_NO_ACTIONS) {
            foreach ($this->getProductList() as $product) {
                $product->setStockData(['use_config_backorders' => 0, 'backorders' => $backordersAction]);
                $product->save();
            }
        }
    }

    /**
     * Get Product List
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductList()
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addAttributeToFilter(
                ProductAttributeInterface::CODE_PRODUCT_AVAILABILITY_DATE,
                ['lteq' => new \DateTime()]
            );

        return $productCollection;
    }
}
