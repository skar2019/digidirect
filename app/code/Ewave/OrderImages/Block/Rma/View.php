<?php

namespace Ewave\OrderImages\Block\Rma;

/**
 * Class View
 *
 * @package Ewave\OrderImages\Block\Rma\
 */
class View extends \Ewave\OrderImages\Block\Order\Items\Image
{
    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $_orderItemRepository;

    /**
     * Image constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        array $data = []
    ) {
        $this->_orderItemRepository = $orderItemRepository;

        parent::__construct($context, $imageHelper, $data);
    }

    /**
     * Set RMA item
     *
     * @param \Magento\Rma\Model\Item $item
     * @return string
     */
    public function getRmaItemImage(\Magento\Rma\Model\Item $item)
    {
        $orderItem = $this->_orderItemRepository->get($item->getOrderItemId());
        return parent::getItemImage($orderItem);
    }
}
