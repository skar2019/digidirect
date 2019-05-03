<?php

namespace Ewave\ExtendedShippingRates\Plugin\Magento\Quote\Model;

use Ewave\ExtendedShippingRates\Model\Rule\Condition\DiscountCode;
use Ewave\ExtendedShippingRates\Model\RuleAppliersAggregator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Quote\Model\CouponManagement;

/**
 * Class CouponManagementPlugin
 */
class CouponManagementPlugin
{
    const RETURN_SET_CODE = 'SETCOUPONECODE';
    const RETURN_REMOVE_CODE = 'REMOVECOUPONECODE';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var RuleAppliersAggregator
     */
    protected $ruleAppliersAggregator;

    /**
     * CouponManagementPlugin constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param CouponManagement $object
     * @param $result
     * @return mixed
     * @throws LocalizedException
     */
    public function afterSet(CouponManagement $object, $result)
    {
        if ($this->registry->registry(DiscountCode::ATTRIBUTE_CODE) == true) {
            throw new LocalizedException(__(self::RETURN_SET_CODE));
        }
        return $result;
    }

    /**
     * @param CouponManagement $object
     * @param $result
     * @return mixed
     * @throws LocalizedException
     */
    public function afterRemove(CouponManagement $object, $result)
    {
        if ($result == true) {
            throw new LocalizedException(__(self::RETURN_REMOVE_CODE));
        }
        return $result;
    }
}
