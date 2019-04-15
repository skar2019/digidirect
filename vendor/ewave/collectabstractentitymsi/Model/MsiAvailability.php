<?php
namespace Ewave\CollectAbstractEntityMSI\Model;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\CollectAbstractEntityMSI\Helper\Data;
use Ewave\CollectAbstractEntity\Api\Data\CollectPlaceInterface;
use Ewave\Collect\Api\CollectPlaceRepositoryInterface;
use Magento\Inventory\Model\SourceItem\Command\GetListInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class MsiAvailability
 * @package Ewave\CollectAbstractEntityMSI\Model
 */
class MsiAvailability
{
    /**
     * @var GetListInterface
     */
    protected $sourceGetListCommand;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Ewave\Collect\Helper\Data
     */
    protected $collectHelper;

    /**
     * Availability MsiAvailability.
     * @param GetListInterface $sourceGetListCommand
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $helper
     * @param \Ewave\Collect\Helper\Data $collectHelper
     */
    public function __construct(
        GetListInterface $sourceGetListCommand,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $helper,
        \Ewave\Collect\Helper\Data $collectHelper
    ) {
        $this->sourceGetListCommand = $sourceGetListCommand;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->helper = $helper;
        $this->collectHelper = $collectHelper;
    }

    /**
     * @param array $collectPlaces
     * @param array $skus
     * @param array|int $skuQty
     * @return array
     */
    public function setAvailabilityForCollectPlaces(array $collectPlaces, array $skus, $skuQty = 1)
    {
        if (is_numeric($skuQty)) {
            $skuQty = $this->collectHelper->prepareSkuQtyArray($skus, $skuQty);
        }
        $sourceItems = $this->prepareSourceDataForItems($skus);

        foreach ($collectPlaces as $place) {
            if (!$place instanceof CollectPlaceInterface) {
                continue;
            }
            $available = $this->isPlaceAvailable($place, $sourceItems, $skuQty);
            $place->setData(CollectPlaceRepositoryInterface::KEY_IS_UNAVAILABLE, !$available);
        }
        return $collectPlaces;
    }

    /**
     * @param CollectPlaceInterface $place
     * @param array $sourceItems
     * @param array $skuQty
     * @return bool
     */
    public function isPlaceAvailable(CollectPlaceInterface $place, array $sourceItems, array $skuQty)
    {
        $placeSources = $this->getSourceInventoryCodes($place);

        $available = false;
        foreach ($skuQty as $sku => $qty) {
            if (empty($sourceItems[$sku])) {
                break;
            }
            $sourcesData = $sourceItems[$sku];
            $available = $this->isItemAvailableInPlaceSources($sourcesData, $placeSources, $qty);
            if (!$available) {
                break;
            }
        }
        return $available;
    }

    /**
     * Items has to be available at least in one Collect Place's Source.
     * @param array $sourcesData
     * @param array $placeSources
     * @param $requiredQty
     * @return bool
     */
    public function isItemAvailableInPlaceSources(array $sourcesData, array $placeSources, $requiredQty)
    {
        $available = false;
        foreach ($placeSources as $placeSource) {
            if (empty($sourcesData[$placeSource])) {
                continue;
            }
            if ($sourcesData[$placeSource] >= $requiredQty) {
                $available = true;
                break;
            }
        }
        return $available;
    }

    /**
     * @param array $skuQtyAvailable
     * @param array $skuQtyRequested
     * @return bool
     */
    public function isAllItemsAvailableInSource(array $skuQtyAvailable, array $skuQtyRequested)
    {
        $available = false;
        foreach ($skuQtyRequested as $sku => $qty) {
            if (empty($skuQtyAvailable[$sku])
                || ($skuQtyAvailable[$sku] < $skuQtyRequested[$sku])) {
                $available = false;
                break;
            }
            $available = true;
        }
        return $available;
    }

    /**
     * @param array $skus
     * @return array ['SKU-1' => ['default' => 100, 'test_source' => 0], 'SKU-2' => ['test_source' => 500]]
     */
    public function prepareSourceDataForItems(array $skus)
    {
        $sourceItems = $this->getSourcesBySku($skus);
        $sourceData = [];

        foreach ($sourceItems as $item) {
            $sourceData[$item->getSku()][$item->getSourceCode()] = $item->getQuantity();
        }
        return $sourceData;
    }

    /**
     * @param array $skus
     * @return SourceItemInterface[]
     */
    public function getSourcesBySku(array $skus)
    {
        $search = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, $skus, 'in')
            ->addFilter(SourceItemInterface::STATUS, SourceItemInterface::STATUS_IN_STOCK)
            ->create();
        $result = $this->sourceGetListCommand->execute($search);
        return $result->getItems();
    }

    /**
     * Array with codes of MSI Inventory Sources.
     * @param AbstractEntityInterface $place
     * @return array
     */
    public function getSourceInventoryCodes(AbstractEntityInterface $place)
    {
        $value = $this->helper->getSourceInventoryAttributeFromConfig();
        if (!$value) {
            return [];
        }
        $codes = $place->getData($value);
        return $codes ? explode(',', $codes) : [];
    }
}
