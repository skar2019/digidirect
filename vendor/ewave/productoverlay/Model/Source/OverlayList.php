<?php
namespace Ewave\ProductOverlay\Model\Source;

use Ewave\ProductOverlay\Model\ResourceModel\Overlays\CollectionFactory;
use Ewave\ProductOverlay\Model\Overlay\Attribute\Source\Status;

/**
 * Class OverlayList
 * @package Ewave\ProductOverlay\Model\Source
 */
class OverlayList extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * OverlayList constructor.
     * @param CollectionFactory $_collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     */
    public function __construct(
        CollectionFactory $_collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $_storeManager
    ) {
        $this->_collectionFactory = $_collectionFactory;
        $this->_storeManager = $_storeManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_options === null) {
            $collection = $this->_getOverlayCollection();
            $options = [];
            foreach ($collection as $item) {
                $options[] = ['label' => $item->getName(), 'value' => $item->getOverlayId()];
            }
            $this->_options = $options;
        }
        return $this->_options;
    }

    /**
     * @return \Magento\CatalogStaging\Model\ResourceModel\Fulltext\Collection
     */
    protected function _getOverlayCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection
            ->addStatusFilter(Status::STATUS_ENABLED)
            ->addStoreFilter($this->_storeManager->getStore()->getId());
        return $collection;
    }

    /**
     * @return null|array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->toOptionArray();
        }
        return $this->_options;
    }
}
