<?php

namespace Ewave\CriteoOneTag\Block;

/**
 * Class AbstractTag
 *
 * @package Ewave\CriteoOneTag\Block
 */
abstract class AbstractTag extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * HomePage constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\Serialize\Serializer\Json     $serializer
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    abstract public function getItem();

    /**
     * @param string $dataType
     * @param string $item
     *
     * @return string
     */
    public function getStringItem($dataType, $item = "")
    {
        if ($item !== "") {
            $item = ",\n        '" . $dataType . "': " . $item;
        }
        return $item;
    }

    /**
     * @param string $id
     * @param double $price
     * @param int $qty
     *
     * @return string
     */
    public function getIdPriceQuantity($id, $price, $qty)
    {
        return [ 'id' => $id, 'price' => round($price, 2), 'quantity' => intval($qty) ];
    }

    /**
     * @return string
     */
    public function getPageType()
    {
        return $this->getData('page_type');
    }

    /**
     * @return mixed
     */
    public function getDataType()
    {
        return $this->getData('data_type');
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
