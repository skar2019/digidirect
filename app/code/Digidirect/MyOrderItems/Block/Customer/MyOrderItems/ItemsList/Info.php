<?php

namespace Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList;

use Magento\Sales\Model\Order\ItemRepository;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Model\Config as EavConfig;
use Digidirect\MyOrderItems\Block\Customer\MyOrderItems\AbstractBlock;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\App\ObjectManager;

/**
 * Class Info
 * @package Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList
 */
class Info extends AbstractBlock
{
    /**
     * Const for product key
     */
    const KEY_PRODUCT = 'product';
    const RELATED_RULE_PRODUCTS_BLOCK_NAME = 'related_rule_products';

    /**
     * @var ItemRepository
     */
    protected $itemRepository;

    /**
     * @var \Magento\Framework\Registry;
     */
    protected $coreRegistry;

    /**
     * @var OrderItemInterface
     */
    protected $saleItem = null;

    /**
     * @var string
     */
    protected $itemParameter;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * Info constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param ItemRepository $itemRepository
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param UrlHandlerPool $urlHandlerPool
     * @param array $data
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        ItemRepository $itemRepository,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        UrlHandlerPool $urlHandlerPool,
        array $data = [],
        ModuleManager $moduleManager = null
    ) {
        $this->itemRepository = $itemRepository;
        $this->moduleManager = $moduleManager ?: ObjectManager::getInstance()->get(ModuleManager::class);
        $this->coreRegistry = $context->getRegistry();
        parent::__construct(
            $context,
            $dataHelper,
            $configHelper,
            $urlHandlerPool,
            $data = []
        );
    }

    /**
     * @return Template
     */
    protected function _prepareLayout()
    {
        $this->saleItem = $this->getSaleItem();
        if ($this->saleItem) {
            $product = $this->dataHelper->getProduct($this->saleItem);
            $this->coreRegistry->register($this->getProductKey(), $product);
        }
        return parent::_prepareLayout();
    }

    /**
     * @return void
     */
    public function getSaleItem()
    {
        if (!$this->saleItem) {
            $itemId = $this->getCurrentItemId();
            if ($itemId) {
                try {
                    $saleItem = $this->itemRepository->get($itemId);
                    $this->saleItem = $saleItem;
                } catch (NoSuchEntityException $exception) {
                    return;
                }
            }
        }
        return $this->saleItem;
    }

    /**
     * @return array
     */
    public function getItemInfo()
    {
        if ($saleItem = $this->getSaleItem()) {
            $productAttributes = $this->configHelper->getAttributeList();
            return $this->dataHelper->getAttributes($saleItem, $productAttributes);
        }
        return [];
    }

    /**
     * @return mixed
     */
    public function getCurrentItemId()
    {
        return (int)$this->getRequest()->getParam($this->getItemParameter(), null);
    }

    /**
     * @return mixed|string
     */
    protected function getItemParameter()
    {
        return $this->_getData('itemParameter') ?: self::ITEM_PARAMETER;
    }

    /**
     * @return mixed|string
     */
    public function getRelatedProductsBlockName()
    {
        $relatedBlockName = ($this->moduleManager->isEnabled('Magento_TargetRule'))
            ? self::RELATED_RULE_PRODUCTS_BLOCK_NAME : self::RELATED_PRODUCTS_BLOCK_NAME;

        return $this->_getData('relatedProductsBlockName') ?: $relatedBlockName;
    }

    /**
     * @return mixed|string
     */
    public function getProductKey()
    {
        return $this->_getData('productKey') ?: self::KEY_PRODUCT;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getCurrentItemId()) {
            return parent::_toHtml();
        }
        return '';
    }
}
