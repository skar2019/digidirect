<?php

namespace Ewave\ProntoDigi\Helper;

use Ewave\Utilities\Model\System\Config\Backend\DefaultOptionModel;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const TIMEZONE = 'ewave_pronto/api/timezone';
    const PRODUCTS_DISABLED_PERCENT = 'ewave_pronto/api_products/disabled_products_percent_skip_update';
    const INVENTORY_PRODUCTS_DISABLED_PERCENT = 'ewave_pronto/api_inventory/disabled_products_percent_skip_update';
    const INVENTORY_DIFF_LAST_MINUTES = 'ewave_pronto/api_inventory/diff_last_minutes';
    const INVENTORY_BUFFER_QUANTITY_FOR_ALL_SOURCES = 'ewave_pronto/api_inventory/buffer_quantity_for_all_sources';
    const INVENTORY_BUFFER_ACTION = 'ewave_pronto/api_inventory/buffer_action';
    const ORDER_COLLECT_PLACE_TO_REP_CODE = 'ewave_pronto/api_order/collect_place_to_repcode';
    const ORDER_PAYMENT_METHOD_TO_PAYMENT_TYPE = 'ewave_pronto/api_order/payment_method_to_payment_type';
    const ORDER_M2EPRO_PAYMENT_METHOD_TO_PAYMENT_TYPE = 'ewave_pronto/api_order/m2epro_payment_method_to_payment_type';
    const DEBUG_UPDATE_REQUEST = 'ewave_pronto/debug/is_debug_update_request';

    /**
     * @var DefaultOptionModel
     */
    protected $defaultOptionModel;

    /**
     * Config constructor.
     *
     * @param Context $context
     * @param \Ewave\Utilities\Model\System\Config\Backend\DefaultOptionModel $defaultOptionModel
     */
    public function __construct(
        Context $context,
        DefaultOptionModel $defaultOptionModel
    ) {
        parent::__construct($context);
        $this->defaultOptionModel = $defaultOptionModel;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return (string)$this->scopeConfig->getValue(self::TIMEZONE);
    }

    /**
     * @return int
     */
    public function getProductsDisabledPercent()
    {
        return (int)$this->scopeConfig->getValue(self::PRODUCTS_DISABLED_PERCENT);
    }

    /**
     * @return int
     */
    public function getInventoryProductsDisabledPercent()
    {
        return (int)$this->scopeConfig->getValue(self::INVENTORY_PRODUCTS_DISABLED_PERCENT);
    }

    /**
     * @return int
     */
    public function getInventoryDiffLastMinutes()
    {
        return (int)$this->scopeConfig->getValue(self::INVENTORY_DIFF_LAST_MINUTES);
    }

    /**
     * @return int
     */
    public function getInventoryBufferQuantityForAllSources()
    {
        return (int)$this->scopeConfig->getValue(self::INVENTORY_BUFFER_QUANTITY_FOR_ALL_SOURCES);
    }

    /**
     * @return int
     */
    public function getInventoryBufferAction()
    {
        return (int)$this->scopeConfig->getValue(self::INVENTORY_BUFFER_ACTION);
    }

    /**
     * @return int
     */
    public function getDebugUpdateRequest()
    {
        return (int)$this->scopeConfig->getValue(self::DEBUG_UPDATE_REQUEST);
    }

    /**
     * @return array
     */
    public function getCollectPlaceToRepCodeMapping()
    {
        $result = [];
        $mapping = $this->scopeConfig->getValue(self::ORDER_COLLECT_PLACE_TO_REP_CODE);
        if (empty($mapping)) {
            return $result;
        }

        $rows = $this->defaultOptionModel->convertValueToArray($mapping);
        foreach ($rows as $row) {
            $collectPlace = $row['collect_place' . '_column'];
            $repCode = $row['rep_code' . '_column'];
            $result[$collectPlace] = $repCode;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getPaymentMethodToPaymentType()
    {
        $result = [];
        $mapping = $this->scopeConfig->getValue(self::ORDER_PAYMENT_METHOD_TO_PAYMENT_TYPE);
        if (empty($mapping)) {
            return $result;
        }

        $rows = $this->defaultOptionModel->convertValueToArray($mapping);
        foreach ($rows as $row) {
            $paymentMethod = $row['payment_method' . '_column'];
            $paymentType = $row['payment_type' . '_column'];
            $result[$paymentMethod] = $paymentType;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getM2eProPaymentMethodToPaymentType()
    {
        $result = [];
        $mapping = $this->scopeConfig->getValue(self::ORDER_M2EPRO_PAYMENT_METHOD_TO_PAYMENT_TYPE);
        if (empty($mapping)) {
            return $result;
        }

        $rows = $this->defaultOptionModel->convertValueToArray($mapping);
        foreach ($rows as $row) {
            $m2eproPaymentMethod = $row['m2epro_payment_method' . '_column'];
            $paymentMethod = $row['payment_type' . '_column'];
            $result[$m2eproPaymentMethod] = $paymentMethod;
        }
        return $result;
    }
}
