<?php
namespace Ewave\AdvancedInventory\Ui\Component\Listing\Column;

use Magento\CatalogInventory\Api\Data\StockItemInterface;

class AdvancedInventoryActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    const URL_PATH_EDIT = 'ewave_advancedinventory/advancedinventory/edit';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['stock_id'])) {
                    $productId = $item[StockItemInterface::PRODUCT_ID] ??
                        $this->context->getRequestParam(StockItemInterface::PRODUCT_ID);

                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    StockItemInterface::STOCK_ID => $item[StockItemInterface::STOCK_ID],
                                    StockItemInterface::PRODUCT_ID => $productId,
                                    StockItemInterface::ITEM_ID => $item[StockItemInterface::ITEM_ID] ?? '',
                                    'back_to' =>
                                        $this->context->getRequestParam('current_entity_id') ? 'stock' : 'product'
                                ]
                            ),
                            'label' => __('Manage Stock')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
