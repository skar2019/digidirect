<?php
namespace Ewave\AdvancedInventory\Block\Adminhtml\AdvancedInventory\Edit;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AdvancedInventory\Api\AdvancedInventoryRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Backend\Block\Widget\Context;

class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Ewave\AdvancedInventory\Api\AdvancedInventoryRepositoryInterface
     */
    protected $advancedInventoryRepository;

    /**
     * BackButton constructor.
     * @param Context $context
     * @param AdvancedInventoryRepositoryInterface $advancedInventoryRepository
     */
    public function __construct(
        Context $context,
        AdvancedInventoryRepositoryInterface $advancedInventoryRepository
    ) {
        parent::__construct($context);
        $this->advancedInventoryRepository = $advancedInventoryRepository;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $request = $this->context->getRequest();
        $stockId = (int)$request->getParam('stock_id');
        if ($request->getParam('back_to') == 'stock' && $stockId) {
            $abstractEntity = $this->advancedInventoryRepository->getAbstractEntity($stockId);
            return $this->getUrl('ewave_abstractentity/abstractentity/edit', [
                'id' => $abstractEntity->getId(),
                AbstractEntityInterface::ATTRIBUTE_SET_ID => $abstractEntity->getAttributeSetId(),
            ]);
        }
        $productId = (int)$request->getParam(StockItemInterface::PRODUCT_ID);
        return $this->getUrl('catalog/product/edit', ['id' => $productId]);
    }
}
