<?php
namespace Ewave\AdvancedInventory\Ui\DataProvider\Product\Form\Modifier;

use Ewave\AdvancedInventory\Api\AdvancedInventoryRepositoryInterface;
use Ewave\AdvancedInventory\Helper\Config as Helper;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form;
use Magento\Framework\UrlInterface;

class AdvancedInventory extends AbstractModifier
{
    const GROUP_ADVANCED_INVENTORY = 'ewave_advanced_inventory';
    const GROUP_CONTENT = 'content';
    const DATA_SCOPE_ADVANCED_PRICING = 'grouped';
    const SORT_ORDER = 300;
    const LINK_TYPE = 'associated';

    /**
     * @var AdvancedInventoryRepositoryInterface
     */
    protected $advancedInventoryRepository;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param AdvancedInventoryRepositoryInterface $advancedInventoryRepository
     * @param Helper $helper
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        AdvancedInventoryRepositoryInterface $advancedInventoryRepository,
        Helper $helper,
        LocatorInterface $locator,
        UrlInterface $urlBuilder
    ) {
        $this->advancedInventoryRepository = $advancedInventoryRepository;
        $this->helper = $helper;
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $product = $this->locator->getProduct();
        if (!$product->getId() || !in_array($product->getTypeId(), $this->helper->getAllowedProductTypes())) {
            return $meta;
        }

        $meta[static::GROUP_ADVANCED_INVENTORY] = [
            'children' => [
                'ewave_advancedinventory_product_total_qty' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Total Stock Quantity'),
                                'formElement' => Form\Element\Input::NAME,
                                'componentType' => Form\Field::NAME,
                                'dataType' => Form\Element\DataType\Text::NAME,
                                'default' =>
                                    $this->advancedInventoryRepository->getTotalStockQuantity($product->getId()),
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
                'ewave_advancedinventory_product_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'ewave_advancedinventory_index',
                                'externalProvider' =>
                                    'ewave_advancedinventory_index.ewave_advancedinventory_index_data_source',
                                'ns' => 'ewave_advancedinventory_index',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.product_id'
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.product_id'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Advanced Inventory'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Form\Fieldset::NAME,
                        'sortOrder' => static::SORT_ORDER,
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();
        $data[$productId][self::DATA_SOURCE_DEFAULT][StockItemInterface::PRODUCT_ID] = $productId;
        return $data;
    }
}
