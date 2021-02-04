<?php

namespace Sparsh\CustomShippingMethod\Model\Carrier;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

/**
 * Class CustomShippingMethod
 *
 * @package Sparsh\CustomShippingMethod\Model\Carrier
 */
class CustomShippingMethod extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string Carriar code
     */
    protected $_code = 'sparsh_customshippingmethod';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig       Scope config.
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory  ErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger            Logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory RateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory RateMethodFactory
     * @param array                                                       $data              Data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect shipping rate
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request Rate request
     *
     * @return \Magento\Shipping\Model\Rate\Result|bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = $this->getFreeBoxes($request);
        $minSubtotal = $this->getConfigData('min_subtotal');

        if ($minSubtotal > $this->getSubtotalOfShippable($request, $freeBoxes)) {
            return false;
        }

        /**
         * @var \Magento\Shipping\Model\Rate\Result $result
         */
        $result = $this->_rateResultFactory->create();

        $shippingPrice = $this->getShippingPrice($request, $freeBoxes);

        if ($shippingPrice !== false) {
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }

        return $result;
    }

    /**
     * Get array of id's of free boxes
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request Rate request
     *
     * @return array
     */
    protected function getFreeBoxes(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $freeBoxes = [];
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    $freeItems =  $this->getFreeBoxesFromChildren($item);
                    if (!empty($freeItems)) {
                        foreach ($freeItems as $itemId) {
                            $freeBoxes[] = $itemId;
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes[] = $item->getId();
                }
            }
        }

        return $freeBoxes;
    }

    /**
     * Check if method is allowed
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['sparsh_customshippingmethod' => $this->getConfigData('name')];
    }

    /**
     * Calculate shipping price
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request   Rate request
     * @param array                                          $freeBoxes Array of free box's ids
     *
     * @return float
     */
    protected function getShippingPrice(\Magento\Quote\Model\Quote\Address\RateRequest $request, $freeBoxes)
    {
        $shippingPrice = false;

        if ($this->getConfigData('type') === 'O') {
            $shippingPrice = $this->getShippingPricePerOrder($request, $freeBoxes);
        } elseif ($this->getConfigData('type') === 'I') {
            $shippingPrice = $this->getShippingPricePerItem($request, $freeBoxes);
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false && $request->getFreeShipping() === true) {
            $shippingPrice = '0.00';
        }

        return $shippingPrice;
    }

    /**
     * Function to create resultMethod
     *
     * @param int|float $shippingPrice Shipping price
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    protected function createResultMethod($shippingPrice)
    {
        /**
         * @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method
         */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->getCarrierCode());
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->getCarrierCode());
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);

        return $method;
    }

    /**
     * Function to collect free boxes from child
     *
     * @param mixed $item Item
     *
     * @return mixed
     */
    protected function getFreeBoxesFromChildren($item)
    {
        $freeBoxes = [];
        foreach ($item->getChildren() as $child) {
            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                $freeBoxes[] = $item->getId();
                $freeBoxes[] = $child->getId();
            }
        }

        return $freeBoxes;
    }

    /**
     * Get subtotal of shippable products
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request   Rate request
     * @param array                                          $freeBoxes Array of free box's ids
     *
     * @return float|int
     */
    public function getSubtotalOfShippable(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $freeBoxes
    ) {
        $itemPrices = [];

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if (!in_array($item->getId(), $freeBoxes) && !$item->isShipSeparately()) {
                    $itemPrice = $item->getPrice();
                    $itemQty = $item->getQty();
                    $itemPrices[] = $itemPrice * $itemQty;
                }
            }
        }

        return array_sum($itemPrices);
    }

    /**
     * Calculate shipping price per item
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request   Rate request
     * @param array                                          $freeBoxes Array of free box's ids
     *
     * @return float
     */
    public function getShippingPricePerItem(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $freeBoxes
    ) {
        $handlingType = $this->getConfigData('handling_type');
        $baseValue = $this->getConfigData('price');
        $shippingPrices = [];

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if (!in_array($item->getId(), $freeBoxes) && !$item->isShipSeparately()) {
                    $itemPrice = $item->getPrice();
                    $itemQty = $item->getQty();
                    $customRate = $item->getProduct()->getData('sparsh_handling_fee');
                    $customRate = $customRate ? $customRate : $baseValue;
                    $shippingOfSingle = $handlingType == 'P' ? ($itemPrice * $customRate) / 100 : $customRate;
                    $shippingPrices[] = $shippingOfSingle * $itemQty;
                }
            }
        }

        return array_sum($shippingPrices);
    }

    /**
     * Calculate shipping price per order
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request   Rate request
     * @param array                                          $freeBoxes Array of free box's ids
     *
     * @return float
     */
    public function getShippingPricePerOrder(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $freeBoxes
    ) {
        $handlingType = $this->getConfigData('handling_type');
        $baseValue = $this->getConfigData('price');
        $totalPrice = $this->getSubtotalOfShippable($request, $freeBoxes);
        $totalPrice = $handlingType == 'P' ? ($totalPrice * $baseValue) / 100 : $baseValue;
        return $totalPrice;
    }
}
