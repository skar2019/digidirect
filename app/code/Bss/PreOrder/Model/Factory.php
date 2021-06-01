<?php
namespace Bss\PreOrder\Model;

class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var mixed
     */
    protected $dataBySku = null;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create model
     *
     * @return \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
     */
    public function create()
    {
        return $this->_objectManager->create(\Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku::class);
    }

    /**
     * @return \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku|mixed
     */
    public function getSalableQtyBySku()
    {
        if ($this->dataBySku == null) {
            $this->dataBySku = $this->create();
        }
        return $this->dataBySku;
    }
}
