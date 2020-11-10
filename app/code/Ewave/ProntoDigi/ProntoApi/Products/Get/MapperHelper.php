<?php

namespace Ewave\ProntoDigi\ProntoApi\Products\Get;

use Ewave\AI\Preferences\Model\Import\Product\CategoryProcessor;
use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductConstants;
use Ewave\ProntoDigi\Setup\InstallData;
use Magento\Catalog\Model\Product\Type;
use Magento\Store\Model\WebsiteRepository;

/**
 * Class MapperHelper
 *
 * @package Ewave\ProntoDigi\ProntoApi\Products\Get
 */
class MapperHelper
{
    const VIRTUAL_PRODUCT_FLAG = 'Z';
    const UOM_EACH = 'each';

    /**
     * @var WebsiteRepository
     */
    protected $websiteRepository;

    /**
     * MapperHelper constructor.
     * @param WebsiteRepository $websiteRepository
     */
    public function __construct(WebsiteRepository $websiteRepository)
    {
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * @param int $offset
     * @param array $barcodes
     * @return string
     */
    public function getBarCode($offset, $barcodes)
    {
        if (!is_array($barcodes)) {
            return '';
        }

        if (isset($barcodes['id'])) {
            $result = $offset == 1 ? $barcodes['id'] : '';
        } else {
            $result = '';
            $i = 1;
            foreach ($barcodes as $barcode) {
                if (!empty($barcode['id'])) {
                    if ($offset == $i) {
                        $result = $barcode['id'];
                        break;
                    }
                    $i++;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $description1
     * @param string $description2
     * @param string $description3
     * @return string
     */
    public function getName(string $description1 = '', string $description2 = '', string $description3 = '')
    {
        return str_replace('+', ' plus', trim(trim($description1)
            . ' ' . trim($description2) . ' ' . trim($description3)));
    }

    /**
     * @param array $gtins
     * @return string
     */
    public function getUom(array $gtins)
    {
        $result = '';
        $gtin = $this->getFirstGtin($gtins);
        if (is_array($gtin) && isset($gtin['uom']) && strtolower($gtin['uom']) == self::UOM_EACH) {
            $result = $gtin['uom'];
        }
        return $result;
    }

    /**
     * @param array $gtins
     * @return string
     */
    public function getConversion(array $gtins)
    {
        $result = '';
        $gtin = $this->getFirstGtin($gtins);
        if (is_array($gtin) && isset($gtin['conv'])) {
            $result = $gtin['conv'];
        }
        return $result;
    }

    /**
     * @param string $stockStatus
     * @return string
     */
    public function getStockStatus($stockStatus)
    {
        return isset(InstallData::OPTIONS_STOCK_STATUS[$stockStatus]) ? $stockStatus : '';
    }

    /**
     * @param string $stockCondition
     * @return string
     */
    public function getStockCondition($stockCondition)
    {
        if (!empty($stockCondition) && !isset(InstallData::OPTIONS_STOCK_CONDITION[$stockCondition])) {
            $stockCondition = 'other';
        }
        return $stockCondition;
    }

    /**
     * @param array $gtins
     * @return string
     */
    public function getDescription(array $gtins)
    {
        $result = '';
        $gtin = $this->getFirstGtin($gtins);
        if (is_array($gtin) && isset($gtin['uom']) && strtolower($gtin['uom']) != self::UOM_EACH) {
            $conv = $gtin['conv'] ?? '';
            $result = ProductConstants::DESCRIPTION_BEGINNING . $conv;
        }
        return $result;
    }

    /**
     * @param array $gtins
     * @param string $value
     * @return array|mixed
     */
    protected function getFirstGtin(array $gtins, $value = 'id')
    {
        if (isset($gtins[$value])) {
            $gtin = $gtins;
        } else {
            $gtin = reset($gtins);
        }
        return $gtin;
    }

    /**
     * @param string $category1
     * @param string $category2
     * @param string $category3
     * @param string $category4
     * @return string
     */
    public function getCategories($category1, $category2, $category3, $category4)
    {
        $category = $this->prepareCategory($category1)
            . $this->prepareCategory($category2)
            . $this->prepareCategory($category3)
            . $this->prepareCategory($category4);

        return $category;
    }

    /**
     * @param string $category
     * @return string mixed
     */
    protected function prepareCategory($category)
    {
        if (!empty($category)) {
            return CategoryProcessor::DELIMITER_CATEGORY . str_replace('/', ' & ', $category);
        }
        return '';
    }

    /**
     * @param string $stockStatus
     * @return string
     */
    public function getProductType($stockStatus)
    {
        return $stockStatus == self::VIRTUAL_PRODUCT_FLAG ? Type::TYPE_VIRTUAL : Type::TYPE_SIMPLE;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->websiteRepository->getDefault()->getCode();
    }

    /**
     * @param array $warehouse
     * @param string $qtyField
     * @return array
     */
    public function getInventorySources($warehouse, $qtyField)
    {
        $sources = [];
        if (is_array($warehouse)) {
            foreach ($warehouse as $item) {
                if (isset($item['code']) && isset($item[$qtyField])) {
                    $sources[] = [
                        'source_code' => $item['code'],
                        'qty' => $item[$qtyField]
                    ];
                }
            }
        }
        return $sources;
    }
    
    public function getQffBase($qffBase) {
        return $qffBase;
    }
    
    public function getQffBonusPoints($qffBonusPoints) {
        return $qffBonusPoints;
    }
    }
